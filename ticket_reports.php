<?php
require_once("hall_sec_secure.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reports.css" />
    <title>Reports</title>
    <script src="https://kit.fontawesome.com/2176615f54.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io_tools/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io_tools/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io_tools/favicon-16x16.png">
    <link rel="manifest" href="favicon_io_tools/site.webmanifest">

    <!--Horizontal Bar Graph-->
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            <?php
            require_once("config.php");
            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
            if ($conn->connect_error) {
                die("<p style=\"color:red;\">Failed to connect to database!</p>");
            }

            $sql = "SELECT residence.resName,
                        SUM(CASE 
                            WHEN maintenancerequest.dateReported BETWEEN '2024-02-10' AND '2024-06-10' THEN 1 
                            ELSE 0 
                        END) AS semester_one_count,
                        SUM(CASE 
                            WHEN maintenancerequest.dateReported BETWEEN '2024-07-10' AND '2024-11-10' THEN 1 
                            ELSE 0 
                        END) AS semester_two_count
                    FROM maintenancerequest 
                    INNER JOIN residence ON maintenancerequest.resID = residence.resID
                    WHERE maintenancerequest.status = 'Completed' 
                    GROUP BY residence.resName 
                    ORDER BY residence.resName;";
            $result = $conn->query($sql);
            if ($result === FALSE) {
                die("<p style=\"color:red;\">Failed to draw graph. Check sql!</p>");
            }
            echo "var data = google.visualization.arrayToDataTable([";
            echo "['Residence', 'Semester One', 'Semester Two'],";
            //populating the data
            while ($row = $result->fetch_assoc()) {
                echo "['{$row['resName']}', {$row['semester_one_count']}, {$row['semester_two_count']}],";
            }
            echo "]);";
            $conn->close();
            ?>

            var options = {
                chart: {
                    title: 'Maintenance Fault per Semester per Residence',
                    titleTextStyle: {
                        color: 'black', // Title font color
                        fontSize: 20 // Title font size
                    }
                },
                bars: 'horizontal', // Required for Material Bar Charts.
                backgroundColor: {
                    fill: 'whitesmoke'
                },
                hAxis: {
                    textStyle: {
                        color: 'black', // Horizontal axis font color
                        fontSize: 15 // Horizontal axis font size
                    }
                },
                vAxis: {
                    textStyle: {
                        color: 'black', // Vertical axis font color
                        fontSize: 15 // Vertical axis font size
                    }
                },
                legend: {
                    textStyle: {
                        color: 'black', // Legend font color
                        fontSize: 15 // Legend font size
                    }
                }
            };
            var chart = new google.charts.Bar(document.getElementById('barchart_material'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>

    <!--Line Graph-->
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            <?php
            require_once("config.php");
            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
            if ($conn->connect_error) {
                die("<p>Failed to connect to database!</p>");
            }
            $sql = "SELECT maintainancecategory.categoryName,
                    SUM(CASE 
                        WHEN maintenancerequest.dateReported >= '2024-07-10' AND maintenancerequest.dateReported < '2024-08-10' THEN 1 
                        ELSE 0 
                    END) AS july_count,
                    SUM(CASE 
                        WHEN maintenancerequest.dateReported >= '2024-08-10' AND maintenancerequest.dateReported < '2024-09-10' THEN 1 
                        ELSE 0 
                    END) AS august_count,
                    SUM(CASE 
                        WHEN maintenancerequest.dateReported >= '2024-09-10' AND maintenancerequest.dateReported < '2024-10-10' THEN 1 
                        ELSE 0 
                    END) AS september_count,
                    SUM(CASE 
                        WHEN maintenancerequest.dateReported >= '2024-10-10' AND maintenancerequest.dateReported <= '2024-11-10' THEN 1 
                        ELSE 0 
                    END) AS october_count
                FROM maintenancerequest 
                INNER JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryID
                GROUP BY maintainancecategory.categoryName 
                ORDER BY maintainancecategory.categoryName;";
            $result = $conn->query($sql);
            if ($result === FALSE) {
                die("<p>Failed to retrive data</p>");
            }

            echo "var data = google.visualization.arrayToDataTable([";
            echo "['Category', 'July', 'August', 'September', 'October'],";
            //populating data from the database
            while ($row = $result->fetch_assoc()) {
                echo "['{$row['categoryName']}', {$row['july_count']}, {$row['august_count']}, {$row['september_count']}, {$row['october_count']}],";
            }
            echo "]);";
            $conn->close();
            ?>

            var options = {
                title: 'Average Maintenance Turnover Time Per Month',
                titleTextStyle: {
                    color: 'black', // Title font color
                    fontSize: 18 // Title font size
                },
                curveType: 'function',
                legend: {
                    position: 'bottom',
                    textStyle: {
                        color: 'black', // Legend font color
                        fontSize: 15 // Legend font size
                    }
                },
                backgroundColor: 'whitesmoke',
                hAxis: {
                    textStyle: {
                        color: 'black', // Horizontal axis font color
                        fontSize: 15 // Horizontal axis font size
                    }
                },
                vAxis: {
                    textStyle: {
                        color: 'black', // Vertical axis font color
                        fontSize: 15 // Vertical axis font size
                    }
                },
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>

    <!--Donut chart-->
    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            <?php
            require_once("config.php");
            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
            if ($conn->connect_error) {
                die("<p>Failed to connect to database!</p>");
            }
            $sql = "SELECT maintainancecategory.categoryName,
                        ROUND(COUNT(maintenancerequest.categoryID) / COUNT(DISTINCT maintenancerequest.requestID)) AS avg_requests
                    FROM maintenancerequest 
                    INNER JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryID
                    WHERE maintenancerequest.status = 'Completed'
                    GROUP BY maintainancecategory.categoryName 
                    ORDER BY maintainancecategory.categoryName;";
            $result = $conn->query($sql);
            if ($result === FALSE) {
                die("<p>Failed to retrive data</p>");
            }

            echo "var data = google.visualization.arrayToDataTable([";
            echo "['Category', 'Average Requests'],";
            //populating data from the database
            while ($row = $result->fetch_assoc()) {
                echo "['{$row['categoryName']}', {$row['avg_requests']}],";
            }
            echo "]);";
            $conn->close();
            ?>

            var options = {
                title: 'Average Tasks',
                titleTextStyle: {
                    color: 'black', // Title font color
                    fontSize: 18 // Title font size
                },
                pieHole: 0.4,
                backgroundColor: 'whitesmoke',
                legend: {
                    textStyle: {
                        color: 'black', // Legend font color
                        fontSize: 15 // Legend font size
                    }
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
        }
    </script>


</head>
<style>
    .decorative {
        width: 20%;
        height: 3px;
        background-color: #ffb60f;
        margin: 10px auto;
        /* Center the line and add vertical spacing */
        display: block;
    }

    h2 {
        margin-top: 80px;
        text-align: center;
        /* Adjusts margin above and below the heading */
    }
</style>

<body>

    <!--header and navigation-->
    <header class="header">
        <div class="header-nav">
            <a href="notification.php" title="Notifications"><i class="fa-regular fa-bell" style="color:#f0c14b"></i></a>
            <a href="../login_p/logout.php" title="Log-out"><i class="fa-solid fa-right-from-bracket" style="color:#f0c14b"></i></a>
        </div>
    </header>

    <h2>Maintenance Fault Stats</h2>
    <div class="decorative"></div>
    <div class="content" id="content">
        <!--main content-->

        <div class="graph-container">
            <div class="bar-container">
                <div id="barchart_material" style="width: 650px; height: 550px;"></div>
            </div>
            <div class="line-container">
                <div id="curve_chart" style="width: 650px; height: 550px"></div>
            </div>
            <div class="donut-container">
                <div id="donutchart" style="width: 450px; height: 450px;"></div>
            </div>
        </div>

        <!--sidebar secretary details-->
        <?php
        require_once("config.php");

        //get userID from session
        $userID = $_SESSION['userID'];

        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
        if ($conn->connect_error) {
            die("<p style=\"color:red;\">Connection failed: </p>" . $conn->connect_error);
        }
        // Query to get the hall secretary's details
        $sql = "SELECT hallsecretary.secID, hallsecretary.secName, hallsecretary.secSurname, hallsecretary.secEmail, dininghall.hallName
                FROM hallsecretary
                INNER JOIN dininghall ON hallsecretary.secID = dininghall.secID WHERE hallsecretary.userID = $userID";
        $result = $conn->query($sql);
        if ($result === FALSE) {
            die("<p style=\"color:red;\"><strong>Query failed to execute!</strong></p>");
        }
        // Check if exactly one result was returned
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $secName = $row['secName'];
                $secEmail = $row['secEmail'];
                $secID = $row['secID'];

                // Store secID in session for future use
                $_SESSION['secID'] = $secID;
            }
        } else {
            echo "<p>No profile found for this user.</p>";
        }
        $conn->close();
        ?>
        <!--sidebar navigation-->
        <button class="menu-button" id="menuButton" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                RUfixable
                <span class="close-button" id="closeButton" onclick="toggleSidebar()"><i class="fa-solid fa-xmark"></i></span>
            </div>
            <div class="user-info">
                <img src="https://www.iconpacks.net/icons/2/free-icon-user-3296.png" alt="User Icon">
                <p class="user-name"><?php echo htmlspecialchars($secName) ?></p>
                <p class="user-id"><?php echo htmlspecialchars($secEmail) ?></p>
            </div>
            <div class="sidebar-menu">
                <a href="hall_sec_admin.php"><i class="fa-solid fa-house" style="color: #ffb60f;"></i> Dashboard</a>
                <a href="profile.php"><i class="fa-solid fa-user" style="color: #ffb60f;"></i> Profile</a>
                <a href="management.php"><i class="fa-solid fa-list-check" style="color: #ffb60f;"></i> Manage Requests</a>
                <a href="ticket_management.php"><i class="fa-solid fa-clock-rotate-left" style="color: #ffb60f;"></i> Ticket Overview</a>
                <a href="ticket_reports.php"><i class="fa-solid fa-chart-simple" style="color: #ffb60f;"></i> Reports</a>
                <a href="../login_p/logout.php"><i class="fa-solid fa-right-from-bracket" style="color: #ffb60f;"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024</p>
        <ul class="footer-quick-links">
            <li><a href="hall_sec_admin.php">Dashboard</a></li>
            <li><a href="management.php">Manage Requests</a></li>
            <li><a href="ticket_management.php">Ticket Overview</a></li>
        </ul>
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const menuButton = document.getElementById('menuButton');
            const closeButton = document.getElementById('closeButton');

            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-250px';
                content.style.marginLeft = '0';
                menuButton.style.display = 'block'; // Show the menu button
                closeButton.style.display = 'none'; // Hide the close button
            } else {
                sidebar.style.left = '0px';
                content.style.marginLeft = '250px';
                menuButton.style.display = 'none'; // Hide the menu button
                closeButton.style.display = 'block'; // Show the close button
            }
        }
        // Header Residence nav dropdown list
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownToggle.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default anchor click behavior
                dropdownMenu.classList.toggle('show'); // Toggle the dropdown menu
            });

            document.addEventListener('click', function(event) {
                if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show'); // Close menu if clicking outside
                }
            });
        });
    </script>
</body>

</html>
