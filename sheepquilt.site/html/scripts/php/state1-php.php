<?php
session_start();

if (isset($_POST["name"])) {
    $_SESSION["name"] = $_POST["name"];
}

if (!isset($_SESSION["name"])) {
    echo "No name in session.";
}

echo "<h1>State Check</h1>";
echo "<p>The name you inputted is: ".htmlspecialchars($_SESSION["name"])."</p>";
echo "<a href='/scripts/php/state2-php.php'>session page 2</a>";
echo "<form action='/scripts/php/destroy-session.php' method='get'>";
echo "<button type='submit'>Destroy Session</button>";
?>