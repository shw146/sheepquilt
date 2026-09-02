<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "utf-8">
    <title>Admin</title>
    <script src = /scripts/delete-confirm.js defer></script>
    <link rel="stylesheet" href="/styles/admin.css">
</head>

<body>

    <h1>Admin</h1>

    <h2>Create a new user</h2>
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

    <h2>Update an existing user</h2>
    <!--Form for updating-->
    <form method="POST" action="/api/users.php">
        <!-- Tells PHP what this POST request is for -->
        <input type="hidden" name="action" value="update">
        <label>
            What are you changing? 
            <select name = "updatevalue">
                <option value = "username">Username</option>
                <option value = "password">Password</option>
                <option value = "permission">Permission type </option>
            </select>
        </label>
        <br>
        <label>
            Which user are you updating?
            <input name="username" required>
        </label>
        <br>
        <label>
            What is the new value?
            <input name = "newvalue">
        </label>
        <br>
        <button type="submit">Update User</button>
    </form>

    <h2>Delete an existing user</h2>
    <!--Form for updating-->
    <form method="POST" action="/api/users.php" id = "delete-form">
        <!-- Tells PHP what this POST request is for -->
        <input type="hidden" name="action" value="delete">
        <label>
            Which user are you deleting?
            <input name="username" required>
        </label>
        <br>
        <button type="submit">Delete user</button>
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
        $newvalue = $_POST["newvalue"];

        $stmt = $pdo->prepare("INSERT INTO logininfo (username, password, permission)
            VALUES (:username, :password, :permission)
        ");

        $stmt->execute([
            ":username" => $username,
            ":password" => $password,
            ":permission" => $permission
        ]);
    }

    //Update a user's information
    if($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "update"){
        $username = $_POST["username"];
        $newvalue = $_POST["newvalue"];

        if($_POST["updatevalue"] === "username"){
            $stmt = $pdo->prepare("UPDATE logininfo SET username = :newvalue WHERE username = :username");
        }else if($_POST["updatevalue"] === "password"){
            $stmt = $pdo->prepare("UPDATE logininfo SET password = :newvalue WHERE username = :username");
        }else if($_POST["updatevalue"] === "permission"){
            $stmt = $pdo->prepare("UPDATE logininfo SET permission = :newvalue WHERE username = :username");
        }
        
        $stmt->execute([
            ":newvalue" => $newvalue,
            ":username" => $username
        ]);
    }

    //Delete a user's information
    if($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "delete"){
        $username = $_POST["username"];

        $stmt = $pdo->prepare("DELETE FROM logininfo WHERE username=:username");
        $stmt->execute([
            ":username" => $username
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