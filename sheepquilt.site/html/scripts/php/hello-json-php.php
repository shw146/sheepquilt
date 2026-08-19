<?php
$now = new DateTime();
header("content-type:application/json;");
$data=[
    "greeting"=>"Hello from Shawn!",
    "language"=>"This file is being served using php",
    "time"=>"The time right now is ".$now->format('Y-m-d H:i:s'),
    "IP"=>"Your IP address is: ".$_SERVER['REMOTE_ADDR']
];
echo json_encode($data);
exit;
?>