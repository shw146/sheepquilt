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


    // Get information about useragents and their timestamps
    $stmt = $pdo->query("
        SELECT timestamp, staticinfo
        FROM mytable
        WHERE mousedata IS NULL OR mousedata = '' AND keypressed IS NULL OR keypressed = ''
        ORDER BY timestamp ASC
    ");

    $chartData = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $staticInfo = json_decode($row["staticinfo"], true);

        $chartData[] = [
            "timestamp" => $row["timestamp"],
            "userAgent" => $staticInfo["ua"] ?? "Unknown"
        ];
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon"></script>
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
                        label: "Number of users accessing the page",
                        data: values
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
        <canvas id="deviceHeatmap"></canvas>
        <script>
            const chartData = <?php echo json_encode($chartData); ?>;


            /*
            * Determine the device from the user-agent string.
            */
            function getDevice(userAgent) {
                if (/iPhone/i.test(userAgent)) {
                    return "iPhone";
                }

                if (/iPad/i.test(userAgent)) {
                    return "iPad";
                }

                if (/Android/i.test(userAgent)) {
                    return "Android";
                }

                if (/Windows/i.test(userAgent)) {
                    return "Windows";
                }

                if (/Macintosh|Mac OS X/i.test(userAgent)) {
                    return "Mac";
                }

                if (/Linux/i.test(userAgent)) {
                    return "Linux";
                }

                return "Unknown";
            }


            /*
            * The rows on the Y axis.
            */
            const devices = [
                "Windows",
                "Mac",
                "iPhone",
                "iPad",
                "Android",
                "Linux",
                "Unknown"
            ];


            /*
            * Group requests into 10-minute buckets.
            *
            * Each database row represents ONE request.
            * A request simply increments the count of its
            * corresponding time/device cell.
            */
            const interval = 10 * 60 * 1000;

            const counts = {};


            chartData.forEach(row => {
                const timestamp = new Date(row.timestamp);
                const device = getDevice(row.userAgent);

                /*
                * Round the timestamp down to the nearest
                * 10-minute interval.
                */
                const bucketTimestamp = new Date(
                    Math.floor(timestamp.getTime() / interval) * interval
                );

                /*
                * Use a readable local-time string as the
                * bucket identifier.
                */
                const bucket = bucketTimestamp.toLocaleString([], {
                    dateStyle: "short",
                    timeStyle: "short"
                });


                if (!counts[bucket]) {
                    counts[bucket] = {};
                }

                if (!counts[bucket][device]) {
                    counts[bucket][device] = 0;
                }

                counts[bucket][device]++;
            });


            /*
            * Get all time buckets in chronological order.
            */
            const timeBuckets = Object.keys(counts);


            /*
            * Convert the grouped data into the format
            * expected by chartjs-chart-matrix.
            *
            * v = number of requests in that cell.
            */
            const heatmapData = [];

            timeBuckets.forEach(bucket => {
                devices.forEach(device => {

                    const requestCount =
                        counts[bucket][device] || 0;

                    heatmapData.push({
                        x: bucket,
                        y: device,
                        v: requestCount
                    });
                });
            });


            /*
            * Find the largest number of requests in
            * any one cell.
            *
            * This is used to determine the heatmap intensity.
            */
            const maxValue = Math.max(
                ...heatmapData.map(point => point.v),
                1
            );


            /*
            * Create the heatmap.
            */
            new Chart(document.getElementById("deviceHeatmap"), {
                type: "matrix",

                data: {
                    datasets: [{
                        label: "Requests",
                        data: heatmapData,

                        /*
                        * Make cells darker when they contain
                        * more requests.
                        */
                        backgroundColor: function(context) {
                            const point =
                                context.dataset.data[context.dataIndex];

                            const value = point.v;

                            if (value === 0) {
                                return "rgba(0, 0, 0, 0.05)";
                            }

                            const intensity = value / maxValue;

                            return `rgba(
                                0,
                                100,
                                255,
                                ${0.15 + intensity * 0.85}
                            )`;
                        },

                        borderWidth: 1,
                        borderColor: "white",

                        /*
                        * Width of each time cell.
                        */
                        width: function(context) {
                            const chartArea = context.chart.chartArea;

                            if (!chartArea) {
                                return 20;
                            }

                            const numberOfBuckets =
                                timeBuckets.length;

                            return Math.max(
                                5,
                                chartArea.width / numberOfBuckets - 2
                            );
                        },

                        /*
                        * Height of each device cell.
                        */
                        height: function(context) {
                            const chartArea = context.chart.chartArea;

                            if (!chartArea) {
                                return 20;
                            }

                            return Math.max(
                                10,
                                chartArea.height / devices.length - 2
                            );
                        }
                    }]
                },


                options: {
                    responsive: true,
                    maintainAspectRatio: false,


                    plugins: {

                        /*
                        * The heatmap itself provides the visual
                        * legend, so we don't need the dataset
                        * legend.
                        */
                        legend: {
                            display: false
                        },


                        /*
                        * Show the exact request count when
                        * hovering over a cell.
                        */
                        tooltip: {
                            callbacks: {

                                title: function(context) {
                                    const point =
                                        context[0].raw;

                                    return point.x;
                                },

                                label: function(context) {
                                    const point =
                                        context.raw;

                                    return `${point.y}: ${point.v} requests`;
                                }
                            }
                        }
                    },


                    scales: {

                        /*
                        * X axis = time buckets.
                        */
                        x: {
                            type: "category",
                            labels: timeBuckets,

                            title: {
                                display: true,
                                text: "Time"
                            }
                        },


                        /*
                        * Y axis = device type.
                        */
                        y: {
                            type: "category",
                            labels: devices,

                            offset: true,

                            title: {
                                display: true,
                                text: "Device"
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