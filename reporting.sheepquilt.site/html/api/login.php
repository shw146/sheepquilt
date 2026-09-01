<?php
    session_start();

    $username = $_POST["username"];
    $password = $_POST["password"];

    $host = 'localhost';
    $dbname = 'logininfo';
    $dbuser = 'shawnwang';
    $dbpass = '123456789';

    $pdo = new PDO(
        "pgsql:host=$host; dbname=$dbname",
        $dbuser,
        $dbpass
    );
    if (!$pdo) {
        die("Database connection failed.");
    }

    $method = $_SERVER['REQUEST_METHOD'];

    $sql = "SELECT * FROM logininfo WHERE username = :username AND password = :password";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ 
        ':username' => $username, 
        ':password' => $password 
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Start a php session using the username and permission to check on secondary pages
        $_SESSION['username'] = $username;
        $_SESSION['permission'] = $user['permission'];

        // Check permission level
        if ($user['permission'] === 'admin') {
            header("Location: /api/admin.php");
            exit; 
        } elseif ($user['permission'] === 'user') {
            header("Location: /api/user.php");
            exit; 
        }
    } else {
        echo "Invalid username or password."; 
    }
?>
