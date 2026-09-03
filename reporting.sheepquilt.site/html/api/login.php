<?php
    session_start();

    $username = $_POST["username"];
    $password = $_POST["password"];

    echo "<p>".$username."</p>";
    echo "<p>".$password."</p>";

    $host = 'localhost';
    $dbname = 'usertracking';
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

        if($user['permission'] == 'admin' || $user['permission'] == 'analyst'){
            header("Location: /index.php");
        }else if($user['permission'] == 'user'){
            header("Location: /browser.php");
        }
    } else {
        echo "Invalid username or password."; 
    }
?>
