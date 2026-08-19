<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Environment Variables-PHP</title>
</head>
<body>
    <?php
    foreach($_SERVER as $key => $value){
        echo "<strong>".htmlspecialchars($key).":</strong> ".htmlspecialchars($value)."<br>";
    }
    ?>
</body>
</html>