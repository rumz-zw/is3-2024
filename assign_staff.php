<?php
require_once("hall_sec_secure.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/2176615f54.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="design.css" />
    <title>Submission to Maintenance Staff</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io_tools/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io_tools/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io_tools/favicon-16x16.png">
    <link rel="manifest" href="favicon_io_tools/site.webmanifest">
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

        .decorative {
            width: 100%;
            height: 3px;
            background-color: #ffb60f;
        }
    </style>
</head>

<body>

    <!--header and navigation-->
    <header class="header">
        <div class="header-nav">
            <a href="../login_p/logout.php" title="Log-out"><i class="fa-solid fa-right-from-bracket" style="color:#f0c14b"></i></a>
        </div>
    </header>

    <!--main content-->
    <div class="content">
        <div class="contain">
            <h1 class="section-title">Forward to Maintenance Staff</h1>
            <div class="decorative"></div><br>
            <div class="back_button">
                <a href="management.php" style="text-decoration:none; color:#f0c14b">
                    <i class="fa-solid fa-arrow-left" style="color:#f0c14b; font-size:25px"></i>
                </a>
            </div>
            <br>

            <?php
            //get staff id passed from previous dashboard page
            $requestID = $_REQUEST['id'];

            require_once("config.php");
            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
            if ($conn->connect_error) {
                die("<p style= \"color:red;\">Failed to connect to database</p>");
            }


            // Fetch ID for category and staff and names 
            $categorySQL = "SELECT categoryID, categoryName FROM dandd.maintainancecategory;";
            $staffSQL = "SELECT staffID, surname, specialization FROM dandd.maintenancestaff";

            $categoryResult = $conn->query($categorySQL);
            $staffResult = $conn->query($staffSQL);

            //main sql for the form
            $sql = "SELECT maintenancerequest.requestID, maintenancerequest.maintenanceName, maintenancerequest.status, maintenancerequest.dateReported, maintenancerequest.categoryID, maintainancecategory.categoryName,
                    residence.resName, student.roomNum, maintenancestaff.surname, maintenancestaff.staffID
                    FROM maintenancerequest 
                    LEFT JOIN student ON maintenancerequest.studentID = student.studentID
                    LEFT JOIN residence ON maintenancerequest.resID = residence.resID 
                    LEFT JOIN maintainancecategory ON maintenancerequest.categoryID = maintainancecategory.categoryID
                    LEFT JOIN maintenancestaff ON maintenancerequest.categoryID = maintenancestaff.categoryId
                    WHERE requestID = $requestID;";

            $result = $conn->query($sql);
            if ($result === FALSE) {
                die("<p style= \"color:red;\">Failed to retrive data</p>");
            }
            //retrive the data and store for the form
            while ($row = $result->fetch_assoc()) {
                $ticketnum = $row['requestID'];
                $requestDetails = $row['maintenanceName'];
                $dateReported = $row['dateReported'];
                $res = $row['resName'];
                $roomNum = $row['roomNum'];
                $status = $row['status'];
                //$picture = $row['Description'];
            }

            //request picture
            $picturesql = "SELECT ImageName FROM image WHERE requestID = '$requestID';";
            $result2 = $conn->query($picturesql);

            $picture = '';
            if ($result2 && $result2->num_rows > 0) {
                $row = $result2->fetch_assoc();
                $picture = $row['ImageName'];
            } else {
                $picture = ''; // Handle case where no image is found
            }

            //student comment
            $sql2 = "SELECT comment.content FROM comment INNER JOIN student ON comment.userID = student.userID WHERE requestID = $requestID;";
            $result3 = $conn->query($sql2);


            $comment = '';
            if ($result3 && $result3->num_rows > 0) {
                $roww = $result3->fetch_assoc();
                $comment = $roww['content'];
            } else {
                $comment = '';  //When student does not attatch comment
            }

            $conn->close();
            ?>

            <!--FORM TO SUBMIT REQUEST TO STAFF-->

            <form action="assigned_staff.php" method="POST">
                <fieldset>
                    <legend><strong>Assign Ticket</strong></legend>
                    <table style="width:60%;">
                        <tr>
                            <td>
                                <b>Ticket Number:</b><br>
                                <input type="text" name="ticketNum" id="ticketNum" size="5" value="<?php echo $ticketnum; ?>"><br>
                                <br>
                                <b>Request Details:</b><br>
                                <input type="text" name="details" id="details" size="60" maxlength="40" value="<?php echo $requestDetails; ?>"><br>
                                <br>
                                <b>Date Reported:</b><br>
                                <input type="date" name="dateReported" id="dateReported" value="<?php echo $dateReported; ?>"><br>
                                <br>
                                <b>Residence:</b><br>
                                <input type="text" name="residence" id="residence" size="20" value="<?php echo $res; ?>"><br>
                                <br>
                                <b>Room Number:</b><br>
                                <input type="text" name="roomnum" id="roomnum" size="5" value="<?php echo $roomNum; ?>"><br>
                                <br>
                                <b>Category:</b><br>
                                <select id="category" name="category">
                                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                                        <option value="<?php echo $row['categoryID']; ?>"><?php echo $row['categoryName']; ?></option>
                                    <?php endwhile; ?>
                                </select><br><br>
                                <b>Maintenance Staff:</b><br>
                                <select id="staff" name="staff">
                                    <?php while ($row = $staffResult->fetch_assoc()): ?>
                                        <option value="<?php echo $row['staffID']; ?>"><?php echo $row['surname'] . ' - ' . $row['specialization']; ?></option>
                                    <?php endwhile; ?>
                                </select><br><br>
                                <b>Request Image: </b><br>
                                <?php if (!empty($picture)): ?>
                                    <img src="../Student Admin/uploads/<?php echo $picture; ?>" alt="Request Image" style="max-width: 50%; height: auto;">
                                <?php else: ?>
                                    <p style="color:#ffb60f;">No image attached!</p>
                                <?php endif; ?>
                                <br><br>
                                <b>Additional Info From Student: </b><br>
                                <?php if (!empty($comment)): ?>
                                    <p><?php echo htmlspecialchars($comment); ?></p>
                                <?php else: ?>
                                    <p style="color:#ffb60f">No comment attached!</p>
                                <?php endif; ?>
                                <br>
                                <br><b>Add Comment:</b><br>
                                <textarea id='comment' name='comment' rows='3' cols='25'></textarea>
                                <br>
                                <input type="hidden" name="request" id="request" value="<?php echo $requestID; ?>">
                                <input type="hidden" name="status" id="status" value="<?php echo $status; ?>">
                                <br>
                                <input type="submit" name="submit" value="Assign Maintenance Staff and Submit">
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>

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
            <p class="user-name"><?php echo $secName ?></p>
            <p class="user-id"><?php echo $secEmail ?></p>
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
            <li><a href="hall_sec_admin.php">Dashboard</a></li>
            <li><a href="management.php">Manage Requests</a>
            <li><a href="ticket_management.php">Ticket Overview</a></li>
            <li><a href="ticket_reports.php">Report</a></li>
        </ul>
    </footer>
</body>
<script>
    // Sidebar toggle function
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
