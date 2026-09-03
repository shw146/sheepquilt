<?php 
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: /login.html");
        exit;
    }

    // Get information about the urls being served
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
        SELECT url, COUNT(DISTINCT uuid) AS user_count
        FROM userinformation 
        GROUP BY url
        ORDER BY user_count DESC
    ");

    $urldata = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // get information about useragents and timestamps
    $sql = "
    SELECT
        date_trunc('hour', \"timestamp\"::timestamp) AS hour,
        staticinfo::jsonb->>'ua' AS user_agent,
        COUNT(*) AS requests
    FROM userinformation
    GROUP BY hour, user_agent
    ORDER BY hour
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll();


    // Classify user agents

    function classifyUserAgent($ua)
    {
        if (!$ua) {
            return "Other";
        }

        $ua = strtolower($ua);

        if (
            strpos($ua, "bot") !== false ||
            strpos($ua, "crawler") !== false ||
            strpos($ua, "spider") !== false ||
            strpos($ua, "slurp") !== false
        ) {
            return "Bot";
        }

        if (
            strpos($ua, "edg/") !== false ||
            strpos($ua, "edge/") !== false
        ) {
            return "Edge";
        }

        if (
            strpos($ua, "opr/") !== false ||
            strpos($ua, "opera") !== false
        ) {
            return "Opera";
        }

        if (
            strpos($ua, "chrome/") !== false ||
            strpos($ua, "crios/") !== false
        ) {
            return "Chrome";
        }

        if (
            strpos($ua, "firefox/") !== false ||
            strpos($ua, "fxios/") !== false
        ) {
            return "Firefox";
        }

        if (strpos($ua, "safari/") !== false) {
            return "Safari";
        }

        return "Other";
    }


    // Format the data for JavaScript

    $analyticsData = [];

    foreach ($rows as $row) {

        $hour = $row["hour"];

        $browser = classifyUserAgent($row["user_agent"]);

        $requests = (int) $row["requests"];

        if (!isset($analyticsData[$hour])) {
            $analyticsData[$hour] = [
                "Chrome" => 0,
                "Safari" => 0,
                "Firefox" => 0,
                "Edge" => 0,
                "Opera" => 0,
                "Bot" => 0,
                "Other" => 0
            ];
        }

        $analyticsData[$hour][$browser] += $requests;
    }

    $analyticsData = array_map(
        function ($hour, $browsers) {
            return array_merge(
                ["hour" => $hour],
                $browsers
            );
        },
        array_keys($analyticsData),
        $analyticsData
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

            const pageLabels = urlData.map(item => item.url);
            const pageValues = urlData.map(item => Number(item.user_count));

            new Chart(document.getElementById("pageChart"), {
                type: "bar",
                data: {
                    labels: pageLabels,
                    datasets: [{
                        label: "Number of users accessing the page",
                        data: pageValues
                    }]
                },
                options: {
                    indexAxis:"y",
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>

        <canvas id="userAgentChart"></canvas>
        <script>

            // PHP inserts the database results directly into JavaScript.

            const analyticsData =
                <?= json_encode($analyticsData) ?>;


            const analyticsLabels = analyticsData.map(row => {

                const date = new Date(
                    row.hour.replace(" ", "T")
                );

                return date.toLocaleString([], {
                    month: "short",
                    day: "numeric",
                    hour: "numeric"
                });

            });


            const datasets = [

                {
                    label: "Chrome",
                    data: analyticsData.map(row => row.Chrome),
                    tension: 0.2
                },

                {
                    label: "Safari",
                    data: analyticsData.map(row => row.Safari),
                    tension: 0.2
                },

                {
                    label: "Firefox",
                    data: analyticsData.map(row => row.Firefox),
                    tension: 0.2
                },

                {
                    label: "Edge",
                    data: analyticsData.map(row => row.Edge),
                    tension: 0.2
                },

                {
                    label: "Opera",
                    data: analyticsData.map(row => row.Opera),
                    tension: 0.2
                },

                {
                    label: "Bot",
                    data: analyticsData.map(row => row.Bot),
                    tension: 0.2
                },

                {
                    label: "Other",
                    data: analyticsData.map(row => row.Other),
                    tension: 0.2
                }

            ];


            new Chart(
                document.getElementById("userAgentChart"),
                {
                    type: "line",

                    data: {
                        labels: analyticsLabels,
                        datasets: datasets
                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: true,

                        interaction: {
                            mode: "index",
                            intersect: false
                        },

                        plugins: {

                            legend: {
                                position: "bottom"
                            }

                        },

                        scales: {

                            x: {
                                title: {
                                    display: true,
                                    text: "Time"
                                }
                            },

                            y: {

                                beginAtZero: true,

                                title: {
                                    display: true,
                                    text: "Requests"
                                },

                                ticks: {
                                    precision: 0
                                }

                            }

                        }

                    }
                }
            );

        </script>
        

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

    </main>
    <hr>
    <footer>
        <p>Made with patches, stitches, and love. Shawn Wang</p>
    </footer>
</body>

</html>