<!--securing the page to reduce access through web browser-->
<?php
require_once("hall_sec_secure.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design.css" />
    <script src="https://kit.fontawesome.com/2176615f54.js" crossorigin="anonymous"></script>
    <title>Hall Secretary</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io_tools/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io_tools/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io_tools/favicon-16x16.png">
    <link rel="manifest" href="favicon_io_tools/site.webmanifest">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- The Hall Secretaries would then send fault requests to the maintenance staff 
    for them to effect repairs. 
    Once repaired, the maintenance staff marks the fault as resolved, notifying the
    Hall Secretary in question who would close the ticket.
    In this regard, the Hall secretary will be the principal agent for the hall 
    and will be responsible for engaging the maintenance staff to address the issue(s).-->

    <!--PIE CHART-->
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
                die("<p style=\"color:red\">Failed to connect to database!</p>");
            }
            $sql = "SELECT maintainancecategory.categoryName, 
                    COUNT(maintenancerequest.maintenanceName) AS occurrenceCount
                    FROM maintenancerequest 
                    INNER JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryId
                    WHERE maintenancerequest.status = 'In Progress'
                    GROUP BY maintainancecategory.categoryName
                    ORDER BY occurrenceCount DESC;";
            $result = $conn->query($sql);

            if ($result === FALSE) {
                die("<p style=\"color:red\">Failed to retrive data</p>");
            }
            echo "var data = google.visualization.arrayToDataTable([";
            echo "['Maintenance Category', 'Frequency of Report'],";
            //populating from the database
            while ($row = $result->fetch_assoc()) {
                echo "['{$row['categoryName']}', {$row['occurrenceCount']}],";
            }
            echo "]);";

            $conn->close();
            ?>

            var options = {
                title: 'Frequency of type of reports made',
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>
    <!--BAR CHART-->
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
                die("<p style=\"color:red\">Failed to connect to database!</p>");
            }
            $sql = "SELECT residence.resName, COUNT(DISTINCT maintenancerequest.maintenanceName)
                    AS maintenance_count FROM maintenancerequest 
                    INNER JOIN residence ON maintenancerequest.resID = residence.resID
                    WHERE maintenancerequest.status = 'In Progress'
                    GROUP BY residence.resName 
                    ORDER BY maintenance_count;";
            $result = $conn->query($sql);
            if ($result === FALSE) {
                die("<p style=\"color:red\">Failed to retrive data</p>");
            }

            echo "var data = google.visualization.arrayToDataTable([";
            echo "['Residence', 'Frequency of requests'],";
            //populating data from the database
            while ($row = $result->fetch_assoc()) {
                echo "['{$row['resName']}', {$row['maintenance_count']}],";
            }
            echo "]);";
            $conn->close();
            ?>


            var options = {
                chart: {
                    title: 'Maintenance Frequency Per Residence',
                },
                colors: ['#ffb60f']
            };

            var chart = new google.charts.Bar(document.getElementById('columnchar_material'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
</head>
<style>
    /* Add basic styles for dropdown */
    .dropdown {
        position: relative;
    }

    .dropdown-toggle {
        cursor: pointer;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        display: none;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu li a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #333;
    }

    .dropdown-menu li a:hover {
        background-color: #f0f0f0;
    }

    p.error {
        background-color: red;
    }

    p.success {
        background-color: green;
    }

    .decorative-line {
        width: 42%;
        height: 3px;
        background-color: #ffb60f;
    }

    .line {
        width: 60%;
        height: 3px;
        background-color: #ffb60f;
        margin: 10px auto;
        /* Center the line and add vertical spacing */
        display: block;
    }
</style>

<body>
    <!--header and navigation-->
    <header class="header">
        <div class="header-nav">
            <a href="notification.php" title="Notifications" classs="notification-icon" id="notificationIcon"><i class="fa-regular fa-bell" style="color:#f0c14b"></i></a>
            <a href="../login_p/logout.php" title="Log-out"><i class="fa-solid fa-right-from-bracket" style="color:#f0c14b"></i></a>
        </div>
    </header>

    <body>

        <div class="main-container">
            <!-- Welcome Message -->
            <h1>Welcome: <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <h7 style="font-family: 'Arial Black', sans-serif;">
                Home/<span style="color: #ffb60f;">Dashboard</span><br>
            </h7>

            <!-- Notification Container -->
            <div class="notification-container">
                <h3 style="color: black; padding:5px; font-family: 'Arial Black', sans-serif;">What's New? Your Notification Hub:</h3>
                <div class="line"></div>
                <p>
                    <strong>
                        <?php
                        require_once("config.php");
                        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                        if ($conn->connect_error) {
                            die("<p style=\"color:red;\">Connection failed: </p>" . $conn->connect_error);
                        }
                        $sql = "SELECT COUNT(*) AS totalrequest FROM maintenancerequest WHERE status = 'In Progress'AND (maintenancerequest.categoryID IS NULL AND maintenancerequest.staffID IS NULL);";
                        $result = $conn->query($sql);
                        if ($result === FALSE) {
                            die("<p style=\"color:red;\"><strong>Query failed to execute!</strong></p>");
                        }
                        $row = $result->fetch_assoc();
                        $sum = $row['totalrequest'];
                        if ($sum > 0) {
                            echo "<p style=\"color:green; font-style: bold;\">You have have: {$sum} new requests!</p>";
                        } else {
                            echo "<p style=\"color:blue; font-style: bold;\">There are no new requests yet!</p>";
                        }
                        $conn->close();
                        ?>
                    </strong>
                </p>
                <p>
                    <strong>
                        <?php
                        require_once("config.php");
                        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                        if ($conn->connect_error) {
                            die("<p style=\"color:red;\">Connection failed: </p>" . $conn->connect_error);
                        }
                        $sql = "SELECT COUNT(*) AS totalrequest FROM dandd.maintenancerequest WHERE status = 'Completed' AND (maintenancerequest.categoryID IS NOT NULL AND maintenancerequest.staffID IS NOT NULL) AND closed_ticket=0;";
                        $result = $conn->query($sql);
                        if ($result === FALSE) {
                            die("<p style=\"color:red;\"><strong>Query failed to execute!</strong></p>");
                        }
                        $row = $result->fetch_assoc();
                        $sum = $row['totalrequest'];
                        if ($sum > 0) {
                            echo "<p style=\"color:green; font-style: bold;\">You have have: {$sum} completed requests to close!</p>";
                        } else {
                            echo "<p style=\"color:blue; font-style: bold;\">There are no new completed requests yet!</p>";
                        }
                        $conn->close();
                        ?>
                    </strong>
                </p>
            </div>

            <!-- Chart Containers -->
            <div class="chart-container">
                <div class="chart-box">
                    <!-- Placeholder for the chart -->
                    <h5></h5>
                    <div id="columnchar_material" style="width: 500px; height: 400px;"></div>
                </div>
                <div class="chart-box">
                    <!-- Placeholder for the chart -->
                    <h5></h5>
                    <div id="piechart_3d" style="width: 500px; height: 400px;"></div>
                </div>
            </div>

            <!-- Quick links Containers -->
            <div class="stats-container">
                <div class="stats-box">
                    <h4>Forward Request To Maintenance</h4>
                    <p>View Residence Requests And<br> Close Completed Requests.</p>
                    <a href="management.php"><i class="fa-solid fa-share-from-square"></i></a>
                </div>
                <div class="stats-box">
                    <h4>Overview of Maintenance Requests Made</h4>
                    <p>View all requests and filter<br>according to status and residence</p>
                    <a href="ticket_management.php"><i class="fa-regular fa-file-lines"></i></a>
                </div>
                <div class="stats-box">
                    <h4>Navigate Insights</h4>
                    <p>Keep up with the latest data, track performance<br>and address issues proactively.</p>
                    <a href="ticket_reports.php"><i class="fa-solid fa-arrow-trend-up"></i></a>
                </div>
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
            echo "<p style=\"color:red\">No profile found for this user.</p>";
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



        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024</p>
            <ul class="footer-quick-links">
                <li><a href="ticket_management.php">Ticket Overview</a></li>
                <li><a href="management.php">Manage Requests</a>
                <li><a href="../Home.html">Home:RUFixable</a></li>
            </ul>
        </footer>

    </body>



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
        //notification pop-up
        const notificationIcon = document.getElementById('notificationIcon');
        const notificationMenu = document.getElementById('notificationMenu');

        notificationIcon.addEventListener('click', function() {
            notificationMenu.style.display = notificationMenu.style.display === 'block' ? 'none' : 'block';
            notificationIcon.classList.toggle('active');
        });

        // Optional: Simulate new notifications
        setTimeout(() => {
            notificationIcon.classList.add('active');
        }, 5000); // Highlight after 5 seconds
    </script>

</html>
