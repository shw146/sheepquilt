<?php 
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: /api/login.php");
        exit;
    }

    if ($_SESSION['permission'] !== 'admin' && $_SESSION['permission'] !== 'analyst') {
        http_response_code(403);
        include $_SERVER['DOCUMENT_ROOT'] . '/403.html';
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
            <li><a href = "#">Page View Report</a></li>
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
        <noscript>
            <p>
                You'll need to enable javascript to see live analytics. The following is a set of saved reports.
            </p>
            <img src = "page-view-report.png">
        </noscript>
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
        <canvas id="urlLollipopChart"></canvas>
        <script>
            const lollipopData = <?php echo json_encode($urldata); ?>;
            // URLs and unique user counts
            const lollipopLabels = lollipopData.map(row => row.url);
            const lollipopCounts = lollipopData.map(row => Number(row.user_count));
            // Create horizontal stems for each lollipop
            const stems = [];

            lollipopCounts.forEach((count, index) => {
                stems.push({
                    x: 0,
                    y: index
                });
                stems.push({
                    x: count,
                    y: index
                });
                // Break the line before the next stem
                stems.push({
                    x: null,
                    y: null
                });
            });

            const ctx = document.getElementById('urlLollipopChart');
            new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [
                        {
                            // The stems
                            label: 'Users',
                            data: stems,
                            showLine: true,
                            borderWidth: 3,
                            pointRadius: 0,
                            tension: 0
                        },
                        {
                            // The lollipop heads
                            label: 'Unique Users',
                            data: lollipopCounts.map((count, index) => ({
                                x: count,
                                y: index
                            })),
                            pointRadius: 7,
                            pointHoverRadius: 9
                        }
                    ]
                },
                options: {
                    responsive: true,

                    scales: {
                        x: {
                            beginAtZero: true,

                            title: {
                                display: true,
                                text: 'Unique Users'
                            },

                            ticks: {
                                precision: 0
                            }
                        },
                        y: {
                            type: 'linear',

                            ticks: {
                                stepSize: 1,

                                callback: function(value) {
                                    return lollipopLabels[value];
                                }
                            },

                            title: {
                                display: true,
                                text: 'URL'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return lollipopLabels[context[0].parsed.y];
                                },

                                label: function(context) {
                                    return 'Users: ' + context.parsed.x;
                                }
                            }
                        }
                    }
                }
            });
        </script>
        <article>
            <h1>Design Report</h1>
            <h2>Why this data?</h2>
            <p>
                It's really nice to know exactly what pages my users are going to because it gives a lot of insight into what they might be interested in. If the most visited site is the homepage, then I might want to look into making my other pages more appealing or linking them from the home page a bit more. If they are going to the contact page, either I am getting lots of people who want to talk to me or I might find myself with some bugs on the contact page if I'm not actually getting many people contacting me. This was specifically sorted by unique UUIDs so that I could get rid of all of the irrelevant information and get a good idea of how many actual human users have used my site in total.
            </p>

            <h2>Why did I choose these visualizations?</h2>
            <p>
                The bar chart just seemed like a really good fit. It fits all of the data in a pretty well formed manner and with the small amount of users that I'm getting at the moment, none of the data is being squished into irrelevancy. It displays the data exactly as needed and doesn't need to be flashy.
            </p>
            <p>
                The lollipop chart was added because I found that the bar chart ended up taking up a lot of space because each of the bars would have to take up their own area. It's not a major issue, but it does kind of get in the way of seeing all of the data because the bars end up further away from each other. The lollipop chart fixes that somewhat, so I thought it was worth adding in.
            </p>
        </section>
    </main>
    <hr>
    <footer>
        <p>Made with patches, stitches, and love. Shawn Wang</p>
    </footer>
</body>

</html>