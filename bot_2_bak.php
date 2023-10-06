<?php
include "./bot2_join.php";
const ENERMIES = ["Clawzy", "Tigzzy"];
const BOARD = 8;
bot2_join(BOARD);

do {
    $coin_lists = [];
    $bot_position = [];
    $datas =  getData();
    
    [$coin_lists, $bot_position, $base] = getObjectPosition($datas['gameObjects']);
    $target = caculateTarget($bot_position, $coin_lists);
    if ($base)
    {
        $target = implode(",", $base);
    }
    
    $move = caculateMove($bot_position, $target);
    move($move);
    usleep(800 * 1000);
} while(true);


function caculateDistance($x1, $y1, $x2, $y2)
{
    $distance = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    return $distance;
}

function caculateTarget($bot_position, $coin_lists)
{
    foreach ($coin_lists as $coin)
    {
        $index = $coin['x'] . "," . $coin['y'];
        $distance = caculateDistance($bot_position['x'], $bot_position['y'], $coin['x'], $coin['y']);
        $distances[$index] = $distance;
    }
    return array_search(min($distances), $distances);
}

function caculateMove($current, $target)
{
    $target = explode(",", $target);
    $targetX = $target[0];
    $targetY = $target[1];
    if ($targetX > $current['x'])
    {
        $move = "RIGHT";
    }else if ($targetX == $current['x']) {
        if ($targetY < $current['y']) {
            $move = "UP";
        } else {
            $move = "DOWN";
        }  
    } else {
        $move = "LEFT";
    }
    if ($targetY < $current['y'])
    {
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
function getData()
{
    $ch = curl_init("https://api-zarena.zinza.com.vn/api/boards/" . BOARD);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function getObjectPosition($gameObjects)
{
    foreach ($gameObjects as $object)
    {
        if ($object['type'] == "CoinGameObject")
        {
            $coin_lists[] = $object['position'];
        }
        if ($object['type'] == 'BotGameObject')
        {
            if ($object['properties']['name'] == 'OMO2') {
                $bot_position = $object['position'];
                if ($object['properties']['coins'] >= 3)
                {
                    $base = $object['properties']['base'];
                }
            }
            if(in_array($object['properties']['name'], ENERMIES)) {
                $coin_lists[] = $object['position'];
            }
        }
    }
    return [$coin_lists, $bot_position, $base];
}

function move($move)
{
$ch = curl_init("https://api-zarena.zinza.com.vn/api/bots/29efe7ae-759b-40c1-baf7-db8b38dc3b31/move");
$data = ['direction' => $move];
$json = json_encode($data);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
));
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$response = curl_exec($ch);
curl_close($ch);
var_dump($response);
}
?>