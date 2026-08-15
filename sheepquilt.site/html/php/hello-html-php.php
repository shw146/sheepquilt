<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>hello from php</title>
</head>
<body>
    <?php
    $now = new DateTime();
    $name = "Shawn";
    $language = "php";
    echo "<h1>Hello from $name!</h1>";
    echo "<p>This file is being served using $language</p>";
    echo "<p>".$now->format('Y-m-d H:i:s')."</p>";
    echo "<p>Your IP address is:".$_SERVER['REMOTE_ADDR']."</p>";
    ?>
</body>
</html>