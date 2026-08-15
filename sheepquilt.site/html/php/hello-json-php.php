<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf=8">
    <title>hello from php</title>
</head>
<body>
    <?php
    $now = new DateTime();
    header("content-type:application/json;");
    $data=[
        "greeting"=>"Hello from Shawn!",
        "language"=>"This file is being served using php",
        "time"=>"The time right now is ".$now->format('Y-m-d H:i:s'),
        "IP"=>"Your IP address is: ".$_SERVER['REMOTE_ADDR']
    ];
    echo "<p>".json_encode($data)."</p>";
    exit;
    ?>
</body>
</html>