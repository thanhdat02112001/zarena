<?php
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
curl_exec($ch);
curl_close($ch);
}
move('RIGHT');
