<?php
$host = "localhost";
$user = "shawnwang";
$db = "usertracking";
$password = "123456789";

// Open connection to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$password");

if (!$conn) {
    echo "❌ Connection failed.";
    echo pg_last_error();
} else {
    echo "✅ Successfully connected to PostgreSQL from Apache!";
}
?>