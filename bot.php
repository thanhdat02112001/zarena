<?php
require('./envvars.php');
class BOT
{
  var $bot_name;
  var $board;
  var $token;                 
  function __construct($bot_name, $token, $board)
  {
    $this->bot_name = $bot_name;
    $this->token = $token;
    $this->board = $board;
  }
  function main()
  {
    // do {
    //   $datas = self::getData();
    //   if ($datas['isStarted']) {
    //     do {
    //       $coin_lists = [];
    //       $bot_position = [];
    //       $enemies_position = [];
    //       $base = "";
    //       $datas = self::getData();

    //       [$coin_lists, $bot_position, $base, $enemies_position] = self::getObjectPosition($datas['gameObjects']);
    //       $target = self::caculateTarget($bot_position, $coin_lists);
    //       if (!empty($base)) {
    //         $target = implode(",", $base);
    //       }

    //       $move = self::caculateMove($bot_position, $target);
    //       if (!self::enemyNearBy($enemies_position, $bot_position)) {
    //         self::move($move);
    //       }
    //       usleep(800 * 1000);
    //     } while ($datas['isStarted']);
    //   }
        // usleep(200 * 1000);
    // } while (!$datas['isStarted']);

    do {
      $coin_lists = [];
      $bot_position = [];
      $enemies_position = [];
      $base = "";
      $datas = self::getData();

      [$coin_lists, $bot_position, $base, $enemies_position] = self::getObjectPosition($datas['gameObjects']);
      $target = self::caculateTarget($bot_position, $coin_lists);
      if (!empty($base)) {
        $target = implode(",", $base);
      }

      $move = self::caculateMove($bot_position, $target);
      if (!self::enemyNearBy($enemies_position, $bot_position)) {
        self::move($move);
      }
      usleep(800 * 1000);
    } while (true);
  }
  function join()
  {
    $ch = curl_init("https://api-zarena.zinza.com.vn/api/bots/" . $this->token . "/join");
    $data = ['boardId' => $this->board];
    $json = json_encode($data);

    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      array(
        'Content-Type: application/json',
      )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

    curl_exec($ch);
    curl_close($ch);
  }
  function getData()
  {
    $ch = curl_init("https://api-zarena.zinza.com.vn/api/boards/" . $this->board);

    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      array(
        'Content-Type: application/json',
      )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }
  function getObjectPosition($gameObjects)
  {
    $coins = [];
    $bot = [];
    $enemies = [];
    $enemies_position = [];
    $coins_position = [];
    foreach ($gameObjects as $object) {
      if ($object['type'] == "CoinGameObject") {
        $coins[] = $object;
      }
      if ($object['type'] == "BotGameObject" && $object['properties']['name'] == $this->bot_name) {
        $bot = $object;
      }
      if ($object['type'] == "BotGameObject" && !in_array($object['properties']['name'], [Envvars::BOT1_NAME, Envvars::BOT2_NAME])) {
        $enemies[] = $object;
        $coins[] = $object;
      }
    }
    if ($bot) {
      $current_point = $bot['properties']['coins'];
      if ($current_point == $bot['properties']['inventorySize']) {
        $base = $bot['properties']['base'];
      }
      foreach ($coins as $coin) {
        if ($coin['type'] == 'CoinGameObject') {
          if ($coin['properties']['points'] + $current_point > $bot['properties']['inventorySize']) {
            $key = array_search($coin, $coins);
            unset($coins[$key]);
            continue;
          }
        }
        $coins_position[] = $coin['position'];
      }
    }
    foreach ($enemies as $enemy) {
      $enemies_position[] = $enemy['position'];
    }

    return [$coins_position, $bot['position'], $base, $enemies_position];
  }
  function caculateTarget($bot_position, $coin_lists)
  {
    foreach ($coin_lists as $coin) {
      $index = $coin['x'] . "," . $coin['y'];
      $distance = self::caculateDistance($bot_position['x'], $bot_position['y'], $coin['x'], $coin['y']);
      $distances[$index] = $distance;
    }
    return array_search(min($distances), $distances);
  }
  function caculateDistance($x1, $y1, $x2, $y2)
  {
    $distance = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    return $distance;
  }
  function caculateMove($current, $target)
  {
    $target = explode(",", $target);
    $targetX = $target[0];
    $targetY = $target[1];
    if ($targetX > $current['x']) {
      $move = "RIGHT";
    } else if ($targetX == $current['x']) {
      if ($targetY < $current['y']) {
        $move = "UP";
      } else {
        $move = "DOWN";
      }
    } else {
      $move = "LEFT";
    }
    if ($targetY < $current['y']) {
      $move = "UP";
    } else if ($targetY == $current['y']) {
      if ($targetX < $current['x']) {
        $move = "LEFT";
      } else {
        $move = "RIGHT";
      }
    } else {
      $move = "DOWN";
    }
    return $move;
  }
  function enemyNearBy($enemies, $bot_position)
  {
    foreach ($enemies as $enemy) {
      $enemy_distance = self::caculateDistance($bot_position['x'], $bot_position['y'], $enemy['x'], $enemy['y']);
      if ($enemy_distance == 2 || $enemy_distance == sqrt(2)) {
        return true;
      }
      return false;
    }
  }
  function move($move)
  {
    $ch = curl_init("https://api-zarena.zinza.com.vn/api/bots/" . $this->token . "/move");
    $data = ['direction' => $move];
    $json = json_encode($data);

    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      array(
        'Content-Type: application/json',
      )
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
  }
}