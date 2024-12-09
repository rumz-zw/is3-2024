<?php
require_once("hall_sec_secure.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="residence_dash_filter.css" />
    <script src="https://kit.fontawesome.com/2176615f54.js" crossorigin="anonymous"></script>
    <title>Hall Secretary</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io_tools/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io_tools/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io_tools/favicon-16x16.png">
    <link rel="manifest" href="favicon_io_tools/site.webmanifest">
</head>

<!-- The Hall Secretaries would then send fault requests to the maintenance staff 
    for them to effect repairs. 
    Once repaired, the maintenance staff marks the fault as resolved, notifying the
    Hall Secretary in question who would close the ticket.
    In this regard, the Hall secretary will be the principal agent for the hall 
    and will be responsible for engaging the maintenance staff to address the issue(s).-->
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
        /* Ensure the dropdown is above other elements */
    }

    .dropdown-menu.show {
        display: block;
        /* Show the dropdown when the class is added */
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
        width: 30%;
        height: 3px;
        background-color: #ffb60f;
    }

    .decorative-lines {
        width: 40%;
        height: 3px;
        background-color: #ffb60f;
    }

    .decorative-liness {
        width: 47%;
        height: 3px;
        background-color: #ffb60f;
    }

    .decorative {
        width: 100%;
        height: 3px;
        background-color: #ffb60f;
    }

    .success-message {
        font-weight: bold;
        font-size: x-large;
        margin: 20px 0;
        padding: 10px;
        background-color: #e7f9e7;
        color: black;
        border: 1px solid #c8e6c9;
        border-radius: 5px;
        text-align: center;
    }

    .closed-message {
        font-size: x-large;
        font-weight: bold;
        margin: 20px 0;
        padding: 10px;
        background-color: #ADD8E6;
        color: black;
        border: 1px solid #ADD8E6;
        border-radius: 5px;
        text-align: center;
    }
</style>

<body>
    <!--header and navigation-->
    <header class="header">
        <div class="header-nav">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">Residence</a>
                <ul class="dropdown-menu">
                    <li><a href="RosaParks_Dash_Filter.php">Rosa Parks</a></li>
                    <li><a href="CollegeHouse_Dash_Filter.php">College House</a></li>
                    <li><a href="Debeers_Dash_Filter.php">Debeers</a></li>
                    <li><a href="Hilltop9_Dash_Filter.php">Hilltop 9</a></li>
                </ul>
            </div>
            <a href="notification.php" title="Notifications"><i class="fa-regular fa-bell" style="color:#f0c14b"></i></a>
            <a href="../login_p/logout.php" title="Log-out"><i class="fa-solid fa-right-from-bracket" style="color:#f0c14b"></i></a>
        </div>
    </header>
    <!-- Main content -->

    <div class="dash-main-content">
        <div class="dash-overview-section">
            <h1>All Residences</h1>
            <div class="decorative"></div><br>
        </div>
        <!--Messages for closing ticket and sending ticket to staff-->
        <?php
        if (isset($_SESSION['success_message'])) {
        ?>
            <div class="success-message">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); // Clear the message after displaying
                ?>
            </div>
        <?php
        }
        ?>
        <?php
        if (isset($_SESSION['closed_message'])) {
        ?>
            <div class="closed-message">
                <?php
                echo $_SESSION['closed_message'];
                unset($_SESSION['closed_message']); // Clear the message after displaying
                ?>
            </div>
        <?php
        }
        ?>

        <div class="dash-tables-container" style="min-height: 600px;">
            <!--Students Request TAble-->
            <div class="dash-fault-summary-section">
                <h2>Incoming Maintenance Requests:Students</h2>
                <div class="decorative-lines"></div>
                <?php
                require_once("config.php");
                $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                if ($conn->connect_error) {
                    die("<p style=\"color:red;\">Failed to connect to database!</p>" . $conn->connect_error);
                }
                $sql = "SELECT DISTINCT maintenancerequest.requestID, maintenancerequest.status, student.fName, student.lastName, maintenancerequest.maintenanceName, maintenancerequest.dateReported, residence.resName, student.roomNum, maintenancestaff.surname, maintenancestaff.staffID, maintenancerequest.categoryId
                        FROM maintenancerequest 
                        LEFT JOIN student ON maintenancerequest.studentID = student.studentID 
                        LEFT JOIN residence ON maintenancerequest.resID = residence.resID 
                        LEFT JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryId
                        LEFT JOIN maintenancestaff ON maintenancerequest.staffID = maintenancestaff.staffID
                        WHERE maintenancerequest.status = 'In Progress'
                            AND (maintenancerequest.categoryID IS NULL AND maintenancerequest.staffID IS NULL)
                            AND maintenancerequest.wardenID IS NULL  -- Filter out rows where wardenID is NULL
                            AND isDeleted = 0
                        ORDER BY maintenancerequest.dateReported ASC;";

                $result = $conn->query($sql);
                if ($result === FALSE) {
                    die("<p style=\"color:red\">Failed to retrive data!</p>");
                }
                //create table with newly reports
                echo "<br><table width=\"100%\" border=1 cellpadding=5 cellspacing=0>
                            <tr style=\"background-color:#D3D3D3\">
                                <td>Ticket Number</td>
                                <td>Student</td>
                                <td>Subject</td>
                                <td>Date Reported</td>
                                <td>Residence</td>
                                <td>Room Number</td>
                                <td>Assigned Staff</td>
                            </tr>";
                if ($result->num_rows > 0) {
                    //fill in the data using while loop
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['requestID']}</td>";
                        echo "<td>{$row['lastName']} {$row['fName']}</td>";
                        echo "<td>{$row['maintenanceName']}</td>";
                        echo "<td>{$row['dateReported']}</td>";
                        echo "<td>{$row['resName']}</td>";
                        echo "<td>{$row['roomNum']}</td>";
                        //echo "<td>{$row['ImageUrl']}</td>";
                        echo "<td><a href=\"assign_staff.php?id={$row['requestID']}\" title=\"assign maintenance staff\"><i class=\"fa-regular fa-user\" style= \"color:#f0c14b;\"></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    // If no results, display a message in the table
                    echo "<tr>
                            <td colspan='7' style='text-align: center; color: red;'><strong>No new requests to display.</strong></td>
                        </tr>";
                }
                echo "</table>";
                $conn->close();

                ?>
            </div>

            <!--House Warden Request Table-->
            <div class="dash-fault-summary-section">
                <h2>Incoming Maintenance Requests:House Wardens</h2>
                <div class="decorative-liness"></div>
                <?php
                require_once("config.php");
                $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                if ($conn->connect_error) {
                    die("<p style=\"color:red\">Failed to connect to database!</p>" . $conn->connect_error);
                }
                $sql = "SELECT maintenancerequest.requestID, maintenancerequest.status, maintenancerequest.maintenanceName, maintenancerequest.dateReported, residence.resName, maintenancestaff.surname, maintenancestaff.staffID, maintenancerequest.categoryId, housewarden.wardenName, housewarden.resID
                        FROM maintenancerequest 
                        LEFT JOIN housewarden ON maintenancerequest.wardenID = housewarden.wardenID 
                        LEFT JOIN residence ON maintenancerequest.resID = residence.resID 
                        LEFT JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryId
                        LEFT JOIN maintenancestaff ON  maintenancerequest.staffID = maintenancestaff.staffID
                        WHERE maintenancerequest.status = 'In Progress'
                        AND (maintenancerequest.categoryID IS NULL AND maintenancerequest.staffID IS NULL)
                        AND maintenancerequest.studentID IS NULL  -- Filter out rows where studentID is NULL
                        AND isDeleted = 0
                        ORDER BY maintenancerequest.dateReported ASC;";

                $result = $conn->query($sql);
                if ($result === FALSE) {
                    die("<p>Failed to retrive data!</p>");
                }
                //create table with newly reports
                echo "<br><table width=\"100%\" border=1 cellpadding=5 cellspacing=0>
                            <tr style=\"background-color:#D3D3D3\">
                                <td>Ticket Number</td>
                                <td>House Warden</td>
                                <td>Subject</td>
                                <td>Date Reported</td>
                                <td>Residence</td>
                                <td>Assigned Staff</td>
                            </tr>";
                if ($result->num_rows > 0) {
                    //fill in the data using while loop
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['requestID']}</td>";
                        echo "<td>{$row['wardenName']}</td>";
                        echo "<td>{$row['maintenanceName']}</td>";
                        echo "<td>{$row['dateReported']}</td>";
                        echo "<td>{$row['resName']}</td>";
                        //echo "<td>{$row['ImageUrl']}</td>";
                        echo "<td><a href=\"assign_staff.php?id={$row['requestID']}\" title=\"assign maintenance staff\"><i class=\"fa-regular fa-user\" style= \"color:#f0c14b;\"></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    // If no results, display a message in the table
                    echo "<tr>
                            <td colspan='6' style='text-align: center; color: red;'><strong>No new requests to display.</strong></td>
                        </tr>";
                }
                echo "</table>";
                $conn->close();

                ?>
            </div>

            <div class="dash-resolved-requests-section">
                <h2>Resolved Maintenance Requests</h2>
                <div class="decorative-line"></div>
                <?php
                require_once("config.php");
                $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                if ($conn->connect_error) {
                    die("<p>Failed to connect to database!</p>" . $conn->connect_error);
                }

                $sql = "SELECT maintenancerequest.requestID, maintenancerequest.maintenanceName, maintenancerequest.dateCompleted, student.roomNum, residence.resName
                        FROM maintenancerequest 
                        INNER JOIN student ON maintenancerequest.studentID = student.studentID
                        INNER JOIN residence ON maintenancerequest.resID = residence.resID
                        WHERE maintenancerequest.status IN ('Completed') AND closed_ticket = 0
                        AND maintenancerequest.dateCompleted IS NOT NULL
                        ORDER BY maintenancerequest.dateCompleted ASC;";
                $result = $conn->query($sql);
                if ($result === FALSE) {
                    die("<p>Failed to retrive data!</p>");
                }

                //create table with newly reports
                echo "<br><table width=\"100%\" border=1 cellpadding=5 cellspacing=0>
                            <tr style=\"background-color:#D3D3D3\">
                                <td>Ticket Number</td>
                                <td>Subject</td>
                                <td>Date Completed</td>
                                <td>Residence</td>
                                <td>Room Number</td>
                                <td>Close Ticket</td>
                            </tr>";

                if ($result->num_rows > 0) {
                    //fill in the data using while loop
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['requestID']}</td>";
                        echo "<td>{$row['maintenanceName']}</td>";
                        echo "<td>{$row['dateCompleted']}</td>";
                        echo "<td>{$row['resName']}</td>";
                        echo "<td>{$row['roomNum']}</td>";
                        echo "<td><a href=\"closeTicket.php?id={$row['requestID']}\" title= \"close ticket\" onClick= \"return confirm ('You are about to close ticket {$row['requestID']}')\"><i class=\"fa-solid fa-circle-xmark\" style= \"color:#f0c14b;\"></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    // If no results, display a message in the table
                    echo "<tr>
                <td colspan='7' style='text-align: center; color: red;'><strong>No new requests to display.</strong></td>
                        </tr>";
                }
                echo "</table>";
                $conn->close();
                ?>

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

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024</p>
        <ul class="footer-quick-links">
            <li><a href="ticket_management.php">Ticket Overview</a></li>
            <li><a href="ticket_reports.php">Reports</a>
            <li><a href="hall_sec_admin.php">Dashboard</a></li>
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
</script>

</html>
