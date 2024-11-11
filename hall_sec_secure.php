<?php
session_start();
if (!isset($_SESSION['access'])){
    header("Location: ../login_p/login.php");
    exit();
}