<?php 
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: /api/login.php");
        exit;
    }

    if ($_SESSION['permission'] !== 'admin' && $_SESSION['permission'] !== 'analyst') {
        http_response_code(403);
        die("Access denied.");
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

    // Get information about screen sizes
    $stmt = $pdo->query('SELECT DISTINCT ON (uuid) staticinfo FROM userinformation');
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $desktop = 0;
    $tablet = 0;
    $phone = 0;

    foreach ($users as $staticInfo) {

        $info = json_decode($staticInfo, true);

        if (!isset($info['screenWidth'])) {
            continue;
        }

        $width = (int)$info['screenWidth'];

        if ($width >= 1024) {
            $desktop++;
        } elseif ($width >= 768) {
            $tablet++;
        } else {
            $phone++;
        }
    }
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sheep Quilt</title>
    <link rel = "stylesheet" href = "/styles/tokens.css"/>
    <link rel = "stylesheet" href = "/styles/global.css"/>
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
            <li><a href="/browser.php">Browser Report</a></li>
            <li><a href = "/page.php">Page View Report</a></li>
            <li><a href = "/device.php">Device Report</a></li>
            <?php
                if($_SESSION['permission'] === 'analyst' || $_SESSION['permission'] === 'admin'){
                    echo "<li><a href = '/index.php'>Analytics Dashboard</a></li>";
                }
                if($_SESSION['permission'] === 'admin'){
                    echo "<li><a href = '/api/users.php'>User Management Page</a></li>";
                }
            ?>
        </ul>
    </nav>
    <main>
        <button onclick="window.print()">Export PDF</button>
        <div style="width: auto; max-height: 60svh;">
            <canvas id="deviceChart"></canvas>
        </div>
        <script>
            const deviceData = {
                desktop: <?= $desktop ?>,
                tablet: <?= $tablet ?>,
                phone: <?= $phone ?>
            };

            new Chart(document.getElementById('deviceChart'), {
                type: 'pie',
                data: {
                    labels: ['Desktop', 'Tablet', 'Phone'],
                    datasets: [{
                        data: [
                            deviceData.desktop,
                            deviceData.tablet,
                            deviceData.phone
                        ]
                    }]
                }
            });
        </script>
        <canvas id="deviceBarChart"></canvas>

        <script>
            const deviceBarData = {
                desktop: <?= $desktop ?>,
                tablet: <?= $tablet ?>,
                phone: <?= $phone ?>
            };

            const deviceBarLabels = ['Desktop', 'Tablet', 'Phone'];

            const deviceBarValues = [
                deviceBarData.desktop,
                deviceBarData.tablet,
                deviceBarData.phone
            ];

            new Chart(document.getElementById('deviceBarChart'), {
                type: 'bar',

                data: {
                    labels: deviceBarLabels,

                    datasets: [{
                        label: 'Number of Users',
                        data: deviceBarValues
                    }]
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,

                    scales: {
                        x: {
                            beginAtZero: true,

                            ticks: {
                                precision: 0
                            },

                            title: {
                                display: true,
                                text: 'Number of Users'
                            }
                        },

                        y: {
                            title: {
                                display: true,
                                text: 'Device Type'
                            }
                        }
                    },

                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        </script>

        <article>
            <h1>Design Report</h1>
            <h2>Why this data?</h2>
            <p>
                It's pretty important to understand what the distribution of device size is that user's are utilizing. It can help with making both engineering and economic decisions based on how the userbase is distributed. If I find that a majority of my users are on phones, it makes a lot of sense to create functionalities that are designed for mobile usage. However, if the distribution is relatively equal between device types, I might want to consider making different versions for each type or creating lots of failsafes in case a screen size isn't compatible with a functionality on my site.
            </p>

            <h2>Why did I choose these visualizations?</h2>
            <p>
                A pie chart does a really good job of showing the relative proportions of different variables and particularly succeeds with showing the dominance of one or two varialbes specifically. This is perfect because I am expecting my data to show mostly laptop/desktop users and I don't have very many variables, meaning my slices aren't going to end up getting too small.
            </p>
            <p>
                A bar chart seemed like the natural progression from a pie chart. While the pie chart is really good at showing dominance, it's not very helpful when actually analyzing the number of users on each type of device because arcs and angles are hard for the human brain to really distinguish. A bar chart lays things out very clearly, which makes analysis really easy.
            </p>
        </section>
    </main>
    <hr>
    <footer>
        <p>Made with patches, stitches, and love. Shawn Wang</p>
    </footer>
</body>

</html>