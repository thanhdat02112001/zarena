<?php

function getdata()
{
  $ch = curl_init("http://localhost/test");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

do {
  $data = getdata();
  echo "get";
  if ($data['isStarted']) {
    do {
      $data = getdata();
      echo 1;
      sleep(1);
    } while($data['isStarted']);
  }
} while(!$data['isStarted']);