<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "utf-8">
    <title>Admin</title>
</head>

<body>

    <h1>Admin</h1>

    <form method="POST" action="/api/users.php">

        <!-- Tells PHP what this POST request is for -->
        <input type="hidden" name="action" value="create">

        <label>
            Username or email:
            <input type="text" name="username" required>
        </label>

        <br>

        <label>
            Password:
            <input type="password" name="password" required>
        </label>

        <br>

        <label>
            Permission Level:
            <select name="permission" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </label>

        <br>

        <button type="submit">Create User</button>

    </form>

</body>
</html>

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

    //Create new user information
    if($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "create"){
        $username = $_POST["username"];
        $password = $_POST["password"];
        $permission = $_POST["permission"];

        $stmt = $pdo->prepare("INSERT INTO logininfo (username, password, permission)
            VALUES (:username, :password, :permission)
        ");

        $stmt->execute([
            ":username" => $username,
            ":password" => $password,
            ":permission" => $permission
        ]);
    }


    //Query for info
    $stmt = $pdo->query("SELECT username, password, permission FROM logininfo");

    //Create table of user information
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