<?php
session_start();

session_destroy();
echo "<h1>Session destroyed</h1>";
echo "<a href='/scripts/php/state1-php.php'>session page 1</a>";
echo "<a href='/scripts/php/state2-php.php'>session page 2</a>";
echo "<a href='/scripts/php/session-home.html'>session home</a>";
?>