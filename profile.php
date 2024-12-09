<?php
require_once("hall_sec_secure.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Secretary Account</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="hallsecProfile.css" />
    <script src="https://kit.fontawesome.com/2176615f54.js" crossorigin="anonymous"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io_tools/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io_tools/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io_tools/favicon-16x16.png">
    <link rel="manifest" href="favicon_io_tools/site.webmanifest">
</head>

<body>
    <!--header and navigation-->
    <header class="header">
        <div class="header-nav">
            <a href="notification.php" title="Notifications"><i class="fa-regular fa-bell" style="color:#ffb60f"></i></a>
            <a href="../login_p/logout.php" title="Log-out"><i class="fa-solid fa-right-from-bracket" style="color:#ffb60f"></i></a>
        </div>
    </header>

    <?php
    require_once("config.php");

    //get userID from session
    $userID = $_SESSION['userID'];

    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
    if ($conn->connect_error) {
        die("<p style=\"color:red;\">Connection failed: </p>" . $conn->connect_error);
    }
    // Query to get the hall secretary's details
    $sql = "SELECT hallsecretary.secName, hallsecretary.secSurname, hallsecretary.secEmail, hallsecretary.secID, dininghall.hallName 
            FROM hallsecretary
            INNER JOIN dininghall ON hallsecretary.secID = dininghall.secID
            WHERE userID = '$userID';";

    $result = $conn->query($sql);
    if ($result === FALSE) {
        die("<p style=\"color:red;\"><strong>Query failed to execute!</strong></p>");
    }
    //get the user's profile details
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $fname = $row['secName'];
        $lname = $row['secSurname'];
        $secEmail = $row['secEmail'];
        $hallName = $row['hallName'];
        $secID = $row['secID'];

        // Store secID in session for future use
        //$_SESSION['secID'] = $secID;
    } else {
        echo "<p style=\"color:red\">No profile found for this user.</p>";
    }
    $username = $fname . ' ' . $lname;
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
            <p class="user-name"><?php echo $fname; ?></p>
            <p class="user-id"><?php echo $secEmail; ?></p>
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

    <div class="content" id="content">
        <div class="profile-card">
            <form action="profile.php" method="POST">
                <div class="profile-header">
                    <img src="https://www.iconpacks.net/icons/2/free-icon-user-3296.png" alt="User Icon">
                    <div>
                        <h2 id="userName"><?php echo $fname . ' ' . $lname; ?></h2>
                        <p id="userEmail"><?php echo $secEmail; ?></p>
                    </div>
                </div>
                <div class="profile-details">
                    <h3>Hall Secretary Details</h3>
                    <div class="profile-info">
                        <form action="profile.php" method="POST">
                            <div>
                                <p><b>First Name: </b>
                                    <span id="userFirstName"><?php echo htmlspecialchars($fname); ?></span>
                                    <input type="text" id="fullNameInput" name="fullNameInput" value="<?php echo htmlspecialchars($fname); ?>" style="display:none;">
                                </p>

                                <p><strong>Last Name: </strong>
                                    <span id="userLastName"><?php echo htmlspecialchars($lname); ?></span>
                                    <input type="text" id="lastNameInput" name="lastNameInput" value="<?php echo htmlspecialchars($lname); ?>" style="display:none;">
                                </p>

                                <p><b>Department:</b> <strong>Hall Secretary</strong>
                                </p>

                                <p><strong>Email: </strong>
                                    <span id="userEmail"><?php echo htmlspecialchars($secEmail); ?></span>
                                    <input type="email" id="emailInput" name="emailInput" value="<?php echo htmlspecialchars($secEmail); ?>" style="display:none;">
                                </p>

                                <p><b>Dining Hall:</b> <span id="userResidence"><?php echo htmlspecialchars($hallName); ?></span>
                                </p>

                                <p><strong>Password: </strong>
                                    <span id="oldPasswordSpan">*********</span>
                                    <input type="password" id="oldPasswordInput" name="oldPasswordInput" placeholder="Enter old password" style="display:none;">
                                    <input type="checkbox" id="toggleOldPassword" onclick="togglePasswordVisibility('oldPasswordInput', 'oldPasswordSpan', this)"> Show Password
                                </p>
                                <div id="passwordSection" style="display:none;">
                                    <p><strong>New Password: </strong>
                                        <span id="newPasswordSpan">*********</span>
                                        <input type="password" id="newPasswordInput" name="newPasswordInput" placeholder="Enter new password" style="display:none;">
                                        <input type="checkbox" id="toggleNewPassword" onclick="togglePasswordVisibility('newPasswordInput', 'newPasswordSpan', this)"> Show Password
                                    </p>

                                    <p><strong>Confirm New Password: </strong>
                                        <span id="confirmPasswordSpan">*********</span>
                                        <input type="password" id="confirmPasswordInput" name="confirmPasswordInput" placeholder="Confirm new password" style="display:none;">
                                        <input type="checkbox" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirmPasswordInput', 'confirmPasswordSpan', this)"> Show Password
                                    </p>
                                </div>
                            </div><br>

                            <div class="button-container">
                                <button type="button" class="edit-button" id="editButton" onclick="editProfile()">Edit Profile</button>
                                <button type="submit" class="edit-button save-button" id="saveButton" onclick="saveProfile()" style="display:none;">Save Changes</button>
                                <button type="button" class="edit-button cancel-button" id="cancelButton" onclick="cancelEdit()" style="display:none;">Cancel</button>
                            </div>

                            <?php
                            require_once("config.php");

                            // Only process the form
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $newEmail = isset($_POST['emailInput']) ? trim($_POST['emailInput']) : null;
                                $newPassword = isset($_POST['newPasswordInput']) ? trim($_POST['newPasswordInput']) : null; // Fixed from passwordInput to newPasswordInput
                                $old_password = isset($_POST['oldPasswordInput']) ? trim($_POST['oldPasswordInput']) : null;
                                $confirm_password = isset($_POST['confirmPasswordInput']) ? trim($_POST['confirmPasswordInput']) : null;
                                $newName = isset($_POST['fullNameInput']) ? trim($_POST['fullNameInput']) : null;
                                $newLastName = isset($_POST['lastNameInput']) ? trim($_POST['lastNameInput']) : null;

                                $userID = $_SESSION['userID'];

                                $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
                                // Check the connection
                                if ($conn->connect_error) {
                                    die("<p style=\"color:red\">Connection failed: </p>" . $conn->connect_error);
                                }

                                // Corrected SQL statement
                                $sql = "SELECT password FROM useraccount WHERE userID='$userID';";
                                $result = $conn->query($sql);
                                if ($result === FALSE) {
                                    die("<p style=\"color:red\">Failed to retrieve data!</p>");
                                }

                                // Updated profile code
                                if ($result && $result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $current_password = $row['password'];

                                    // Check if the old password matches the stored password
                                    if ($old_password === $current_password) {
                                        // Continue with the password update logic...
                                        if ($newPassword === $confirm_password) {
                                            if ($newPassword !== $old_password) { // Check if new password is not the same as the old one
                                                // SQL to update the user account password
                                                $update_sql = "UPDATE useraccount SET password = '$newPassword' WHERE userID = '$userID';";

                                                if ($conn->query($update_sql) === TRUE) {
                                                    // Insert a notification for password update
                                                    $notification_message = "Password changed successfully for user: $newName";
                                                    $insert_notification_sql = "INSERT INTO notification (message, recipient, userID, timestamp) VALUES ('$notification_message', '$newName', '$userID', NOW());";
                                                    $conn->query($insert_notification_sql);

                                                    // Success message
                                                    echo "<p>Password changed successfully!</p>";
                                                } else {
                                                    echo "<p>Error updating password!</p>";
                                                }
                                            }
                                        }
                                    }
                                }

                                // Profile update logic (name, email)
                                if (!empty($newName) || !empty($newLastName) || !empty($newEmail)) {
                                    // SQL to update the hall secretary profile information
                                    $update_profile_sql = "UPDATE hallsecretary SET secEmail = '$newEmail', ecName = '$newName', secSurname = '$newLastName' WHERE userID = '$userID';";

                                    if ($conn->query($update_profile_sql) === TRUE) {
                                        // Insert a notification for profile update
                                        $notification_message = "Profile updated successfully for user: $newName";
                                        $insert_notification_sql = "INSERT INTO notification (message, recipient, userID, timestamp) VALUES ('$notification_message', '$newName', '$userID', NOW())";
                                        $conn->query($insert_notification_sql);

                                        // Success message
                                        echo "<p>Profile updated successfully!</p>";
                                    } else {
                                        echo "<p style=\"color:red;\">Error updating profile!</p>";
                                    }
                                }
                                $conn->close();
                            }
                            ?>
                            <!-- Displaying any messages -->
                            <?php if (!empty($message)): ?>
                                <p><?php echo htmlspecialchars($message); ?></p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </form>
        </div>

    </div>


    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024</p>
        <ul class="footer-quick-links">
            <li><a href="hall_sec_admin.php">Dashboard</a></li>
            <li><a href="ticket_management.php">Ticket Overview</a></li>
            <li><a href="../home.html">Home</a></li>
        </ul>
    </footer>
</body>

<script>
    //password visibility
    function togglePasswordVisibility(inputId, spanId, checkbox) {
        var input = document.getElementById(inputId);
        var span = document.getElementById(spanId);

        if (checkbox.checked) {
            // Show password
            input.type = 'text'; // Change input type to text
            span.textContent = input.value; // Show the password
        } else {
            // Hide password
            input.type = 'password'; // Change input type back to password
            span.textContent = '*********'; // Display placeholder
        }
    }

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

    //profile
    function editProfile() {
        document.getElementById('passwordSection').style.display = 'block';

        document.getElementById('userFirstName').style.display = 'none';
        document.getElementById('fullNameInput').style.display = 'block';

        document.getElementById('userLastName').style.display = 'none';
        document.getElementById('lastNameInput').style.display = 'block';

        document.getElementById('userEmail').style.display = 'none';
        document.getElementById('emailInput').style.display = 'block';

        document.getElementById('oldPasswordSpan').style.display = 'none';
        document.getElementById('oldPasswordInput').style.display = 'block';

        document.getElementById('newPasswordSpan').style.display = 'none';
        document.getElementById('newPasswordInput').style.display = 'block';

        document.getElementById('confirmPasswordSpan').style.display = 'none';
        document.getElementById('confirmPasswordInput').style.display = 'block';

        document.getElementById('saveButton').style.display = 'block';
        document.getElementById('cancelButton').style.display = 'block';
        document.getElementById('editButton').style.display = 'none';
    }

    function cancelEdit() {
        document.getElementById('passwordSection').style.display = 'none';

        document.getElementById('fullNameInput').style.display = 'none';
        document.getElementById('userFirstName').style.display = 'block';

        document.getElementById('lastNameInput').style.display = 'none';
        document.getElementById('userLastName').style.display = 'block';

        document.getElementById('emailInput').style.display = 'none';
        document.getElementById('userEmail').style.display = 'block';

        document.getElementById('oldPasswordSpan').style.display = 'block';
        document.getElementById('oldPasswordInput').style.display = 'none';

        document.getElementById('newPasswordSpan').style.display = 'block';
        document.getElementById('newPasswordInput').style.display = 'none';

        document.getElementById('confirmPasswordSpan').style.display = 'block';
        document.getElementById('confirmPasswordInput').style.display = 'none';

        document.getElementById('saveButton').style.display = 'none';
        document.getElementById('cancelButton').style.display = 'none';
        document.getElementById('editButton').style.display = 'block';
    }

    function saveProfile() {
        // Submit the form using JavaScript if needed
        document.querySelector('form').submit();
    }

    function cancelEdit() {
        window.location.href = 'profile3.php';
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profilePicture');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</html>
