<?php
$host = "localhost";
$user = "shawnwang";
$db = "usertracking";
$password = "123456789";

// Open connection to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);

$uuid = $data["uuid"] ?? null;
$url = $data["url"] ?? null;
$title = $data["title"] ?? null;
$referrer = $data["referrer"] ?? null;
$timestamp = $data["timestamp"] ?? null;
$type = $data["type"] ?? null;
$staticInfo = $data["staticInfo"] ?? null;
$performanceInfo = $data["performanceInfo"] ?? null;
$error = $data["error"] ?? null;
$mouseData = $data["mouseData"] ?? null;
$keyPressed = $data["keyPressed"] ?? null;
$idleInfo = $data["idleInfo"] ?? null;


$query = "
    INSERT INTO userinformation (uuid, url, title, referrer, timestamp, type, staticinfo, performanceinfo, error, mousedata, keypressed, idleinfo)
    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)
";

$result = pg_query_params($conn, $query, [
    $uuid,
    $url,
    $title,
    $referrer,
    $timestamp,
    $type,
    $staticInfo,
    $performanceInfo,
    $error,
    $mouseData,
    $keyPressed,
    $idleInfo
]);

if ($result === false) {
    http_response_code(500);
    echo "Database insert failed";
    exit;
}

echo "data inserted successfully";

pg_close($conn);
?>