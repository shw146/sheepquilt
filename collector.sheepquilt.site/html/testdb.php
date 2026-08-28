<?php
$host = "localhost";
$user = "web_user";
$pass = "your_secure_password";
$db = "my_web_db";

// Open connection to PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass");

if (!$conn) {
    echo "❌ Connection failed.";
} else {
    echo "✅ Successfully connected to PostgreSQL from Apache!";
}
?>