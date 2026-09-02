<?php 
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: /api/login.php");
        exit;
    }


    $host = "localhost";
    $dbname = "usertracking";
    $dbuser = "shawnwang";
    $dbpass = "123456789";

    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $dbuser,
        $dbpass
    );

    $stmt = $pdo->query("
        SELECT url, COUNT(*) AS user_count
        FROM userinformation 
        GROUP BY url
        ORDER BY user_count DESC
    ");

    $urldata = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sheep Quilt</title>
    <link rel = "stylesheet" href = "/styles/tokens.css"/>
    <link rel = "stylesheet" href = "/styles/global.css"/>
    <link rel = "stylesheet" href = "/styles/home-layout.css"/>
    <link rel = "icon" type = "image/x-icon" href = "/assets/favicon.ico"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <section>
            <h1>Sheep Quilt</h1>
            <p>Reporting version</p>
        </section>
    </header>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="/members/shawn.html">Shawn</a></li>
            <li><a href="/CSE135.html">CSE135</a></li>
            <?php
                if($_SESSION['username'] === 'admin'){
                    echo "<li><a href = '/api/users.php'>User Management Page</a></li>";
                }
            ?>
        </ul>
    </nav>
    <main>
        <canvas id="pageChart"></canvas>
        <script>
            const urlData = <?php echo json_encode($urldata); ?>;

            const labels = urlData.map(item => item.url);
            const values = urlData.map(item => Number(item.user_count));

            new Chart(document.getElementById("pageChart"), {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Users Currently on Page",
                        data: values
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>
    </main>
    <hr>
    <footer>
        <p>Made with patches, stitches, and love. Shawn Wang</p>
    </footer>
</body>

</html>