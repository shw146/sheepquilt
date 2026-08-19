<?php
session_start();

if (isset($_SESSION["name"])) {
    echo "<h1>State Check 2</h1>";
    echo "<p>The name you inputted is: ".htmlspecialchars($_SESSION["name"])."</p>";
    echo "<a href='/scripts/php/state1-php.php'>session page 1</a>";
    echo "<form action=/scripts/php/destroy-session.php' method='get'>";
    echo "<button type='submit'>Destroy Session</button>";
} else {
    echo "No session data found.";
    echo "<a href='/scripts/php/state1-php.php'>session page 1</a>";
    echo "<form action=/scripts/php/destroy-session.php method='get'>";
    echo "<button type='submit'>Destroy Session</button>";
}
?>