<?php

$method = $_SERVER['REQUEST_METHOD'];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$hostname = $_SERVER['HTTP_HOST'] ?? '';
$dateTime = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

$data = [];

switch ($method) {

    case 'GET':
        // GET parameters come from the URL:
        // echo.php?name=Shawn&message=Hello
        $data = $_GET;
        break;

    case 'POST':
        if (str_contains($contentType, 'application/json')) {
            $body = file_get_contents('php://input');
            $data = json_decode($body, true) ?? [];
        } else {
            // application/x-www-form-urlencoded
            $data = $_POST;
        }
        break;

    case 'PUT':
    case 'DELETE':
        // PUT and DELETE data must be read from the request body
        $body = file_get_contents('php://input');

        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($body, true) ?? [];
        } else {
            // application/x-www-form-urlencoded
            parse_str($body, $data);
        }
        break;

    default:
        http_response_code(405);
        echo "Method not allowed";
        exit;
}

// Get the values sent by the form
$name = $data['name'] ?? '';
$message = $data['message'] ?? '';
$language = $data['language'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Echo Response</title>
</head>

<body>

<h1>Echo Response</h1>

<p>
    <strong>Hostname:</strong>
    <?= htmlspecialchars($hostname) ?>
</p>

<p>
    <strong>Date/Time:</strong>
    <?= htmlspecialchars($dateTime) ?>
</p>

<p>
    <strong>Method:</strong>
    <?= htmlspecialchars($method) ?>
</p>

<p>
    <strong>User Agent:</strong>
    <?= htmlspecialchars($userAgent) ?>
</p>

<p>
    <strong>IP Address:</strong>
    <?= htmlspecialchars($ipAddress) ?>
</p>

<h2>Data Received</h2>

<p>
    <strong>Name:</strong>
    <?= htmlspecialchars($name) ?>
</p>

<p>
    <strong>Message:</strong>
    <?= htmlspecialchars($message) ?>
</p>

<p>
    <strong>Language:</strong>
    <?= htmlspecialchars($language) ?>
</p>

</body>
</html>