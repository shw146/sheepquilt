<?php 
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
            <li><a href="#">Home</a></li>
            <li><a href="/members/shawn.html">Shawn</a></li>
            <li><a href="/CSE135.html">CSE135</a></li>
        </ul>
    </nav>
    <main>
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
        <canvas id="stackedBarChart"></canvas>

        <script>

            // PHP inserts the database results directly into JavaScript.
            const stackedData =
                <?= json_encode($analyticsData) ?>;


            const stackedLabels = analyticsData.map(row => {

                const date = new Date(
                    row.hour.replace(" ", "T")
                );

                return date.toLocaleString([], {
                    month: "short",
                    day: "numeric",
                    hour: "numeric"
                });

            });


            const stackeddatasets = [

                {
                    label: "Chrome",
                    data: analyticsData.map(row => row.Chrome)
                },

                {
                    label: "Safari",
                    data: analyticsData.map(row => row.Safari)
                },

                {
                    label: "Firefox",
                    data: analyticsData.map(row => row.Firefox)
                },

                {
                    label: "Edge",
                    data: analyticsData.map(row => row.Edge)
                },

                {
                    label: "Opera",
                    data: analyticsData.map(row => row.Opera)
                },

                {
                    label: "Bot",
                    data: analyticsData.map(row => row.Bot)
                },

                {
                    label: "Other",
                    data: analyticsData.map(row => row.Other)
                }

            ];


            new Chart(
                document.getElementById("stackedBarChart"),
                {
                    type: "bar",

                    data: {
                        labels: stackedLabels,
                        datasets: stackeddatasets
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

                                stacked: true,

                                title: {
                                    display: true,
                                    text: "Time"
                                }

                            },

                            y: {

                                stacked: true,

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

        <article>
            <h1>Design Report</h1>
            <h2>Why this data?</h2>
            <p>
                I think that a big part of web development is understanding the world that your users live in. This ultimately has an effect on the software that they touch, the UIs that they are used to, and what features they see as normal. Knowing the distribution of which browsers are sending requests gives a lot of insight into these topics. For instance, it seems like there is a lot of requests being recorded on chrome and secondarily on opera. As such, it would make sense for me to focus my development process on the available features in these browsers rather than accomodating for something like Edge. However, this is the obvious answer and I think there might be something even more interesting here that specificall not sorting by unique users shows: how much of the analytics may be overblown or unnecessary? 
            </p>
            <p> 
                I say this because my site only has about 8 or so unique users. I know this for sure because I am generating unique UUIDs for each user that loads javascript. Does this potentially mean that I have more users than I am actually recording? Yes, definitely. However, the number of requests that I am getting is on orders of magnitude more than my expected number of users, even given the potential of unmarked visits. This indicates that the data I am recording is massively overblown for each visit and that there is a real posibility that I should maybe pull back on how much I'm taking in. I know for a fact that I'm really not all that concerned with the clicks a user is doing on my site because it's not really a public facing site. Thus, if it wasn't for the sake of an assignment/project, I could definitely see myself removing that part of my collector script because otherwise it clearly will just add bloat to my database/logs.
            </p>
            <p> 
                As a result, I'd most likely be able to figure out the "sweet spot" of how much analytical data I should be taking in in order to give myself enough information without overwhelming the system and effectively hindering my own ability to analyze the data. A user might not feel this directly, but it would fuel my ability to do proper analysis and thus make informed decisions more quickly. In the end, the user may end up seeing informed changes with less downtime in between spent on searching through data that I don't necessarily need.
            </p>

            <h2>Why did I choose these visualizations?</h2>
            <p>
                The spline chart is a fantastic way to 3 different pieces of data in which one is categorical while the others are numerical. Its x-axis serves as a really good way to visualize time because people are generally used to timelines and the x-axis being related to time. A similar ideology goes for the counts on the y-axis, so I won't talk about that too much. I <strong>am </strong>a big fan of the colors though - they give the user a bit more pattern matching that I think is really helpful when reading the chart. Not only is there information encoded into the key of the chart, there is also information encoded into the colors themselves. By using colors that companies have already associated themselves with, I end up having a really easy time with understanding which line represents which browser because I've already associated them before. There is a good note though that I am not everyone and that perhaps this color encoding may not resonate for all users.
            </p>
            <p>
                While the spline chart does a pretty great job of seeing the trends of each of the browsers in isolation, it doesn't do as well in visualizing how they relate to each other. Here is where the stacked bar chart becomes useful. While the stacked bar chart is admittedly harder to read when there are more browsers visiting the site, it makes it much easier to see the change in proportion of a browser's usage over time. This is because humans are generally more receptive to bars than curved lines and because data points are right next to each other, making their differences much more apparent. I would likely use this kind of chart in a more exploratory manner since it tends to store a lot of information in a relatively cramped space, but I do think that it makes up for what the spline chart lacks very well.
            </p>
        </section>
    </main>
    <hr>
    <footer>
        <p>Made with patches, stitches, and love. Shawn Wang</p>
    </footer>
</body>

</html>