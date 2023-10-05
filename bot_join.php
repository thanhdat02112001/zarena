<?php
function bot1_join() {
    $ch = curl_init("https://api-zarena.zinza.com.vn/api/bots/eb8f9599-b117-4ba1-8367-7bbe0b209ce5/join");
    $data = ['boardId' => 1];
    $json = json_encode($data);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    
    curl_exec($ch);
    curl_close($ch);
}
?>