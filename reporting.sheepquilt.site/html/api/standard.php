<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    if ($_SESSION['permission'] !== 'user') {
        http_response_code(403);
        die("Access denied.");
    }
?>

<p> This is the standard user page </p>