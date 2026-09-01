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

    echo "<table>";
    echo "<tr>";
    echo "<th>Username</th>";
    echo "<th>Password</th>";
    echo "<th>Permissions</th>";
    echo "</tr>";

    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["password"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["permission"]) . "</td>";
        echo "</tr>";
    }
?>