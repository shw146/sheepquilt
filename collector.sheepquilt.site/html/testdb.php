<?php
$host = "localhost";
$user = "shawnwang";
$db = "usertracking";
$password = "123456789";

// Open connection to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$password");
pg_last_error($conn);

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
$staticInfo = isset($data["staticInfo"])
    ? json_encode($data["staticInfo"])
    : null;
$performanceInfo = isset($data["performanceInfo"])
    ? json_encode($data["performanceInfo"])
    : null;
$error = isset($data["error"])
    ? json_encode($data["error"])
    : null;
$mouseData = isset($data["mouseData"])
    ? json_encode($data["mouseData"])
    : null;
$keyPressed = isset($data["keyPressed"])
    ? json_encode($data["keyPressed"])
    : null;
$idleInfo = isset($data["idleInfo"])
    ? json_encode($data["idleInfo"])
    : null;


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