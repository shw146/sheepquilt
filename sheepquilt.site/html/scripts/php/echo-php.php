<?php

$method = $_SERVER['REQUEST_METHOD'];
$hostname = $_SERVER['SERVER_NAME'];
$time = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if ($method === "GET") {

    // GET data is stored in $_GET
    foreach ($_GET as $key => $value) {
    $data .= "<p>$key: $value</p>";
    }

} else {

    $data = $_POST;
}

echo "<h1>PHP Echo Results</h1>";
echo "<br>";

echo "<strong>Method: </strong>" . htmlspecialchars($method) . "<br>";
echo "<strong>Host Name: </strong>" . htmlspecialchars($hostname) . "<br>";
echo "<strong>Time: </strong>" . htmlspecialchars($time) . "<br>";
echo "<strong>User Agent: </strong>" . htmlspecialchars($userAgent) . "<br>";
echo "<strong>IP: </strong>" . htmlspecialchars($ip) . "<br>";

echo "<strong>Data: </strong>";

echo $data

echo "<br>";

?>