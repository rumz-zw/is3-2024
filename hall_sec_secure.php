<?php
session_start();
if (!isset($_SESSION['access'])){ //if the user did.t enter their details from the login page they are redirected to the login page.
    header("Location: ../login_p/login.php");
    exit();
}
