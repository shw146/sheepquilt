<?php
$host = "localhost";
$user = "shawnwang";
$db = "userTracking";

// Open connection to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user");

if (!$conn) {
    echo "❌ Connection failed.";
} else {
    echo "✅ Successfully connected to PostgreSQL from Apache!";
}
?>