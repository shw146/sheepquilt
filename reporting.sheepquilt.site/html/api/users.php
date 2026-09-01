<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: /api/login.php");
        exit;
    }

    if ($_SESSION['permission'] !== 'admin') {
        http_response_code(403);
        die("Access denied.");
    }
?>

<?php
    $host = "localhost";
    $dbname = "usertracking";
    $dbuser = "shawnwang";
    $dbpass = "123456789";

    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $dbuser,
        $dbpass
    );

    $stmt = $pdo->query("SELECT username, password, permission FROM logininfo");

    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        echo "<p> username: ".htmlspecialchars($row['username'])."</p>";
        echo "<p> username: ".htmlspecialchars($row['password'])."</p>";
        echo "<p> permission: ".htmlspecialchars($row['permission'])."</p>";
    }
?>