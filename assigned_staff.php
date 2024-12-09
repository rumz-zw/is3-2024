<?php
session_start();
//updating maintenance request table in the database
require_once("config.php");

// Values from form
$id = $_REQUEST['request'];
$ticketNum = $_REQUEST['ticketNum'];
$categoryID = $_REQUEST['category'];
$staffID = $_REQUEST['staff'];
$comment = $_REQUEST['comment'];

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);
if ($conn->connect_error) {
    die("<p style=\"error\"><b>Failed to connect to database!</b></p>");
}

// Prepare the SQL statement
$sql = "UPDATE maintenancerequest SET categoryID = ' $categoryID', staffID = '$staffID' WHERE requestID = $id";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("<p style=\"error\"><b>Failed to retrive data!</p>");
}

//update commnet made by hall secretary
$commentSQL = "UPDATE comment SET hall_sec = '$comment' WHERE requestID = '$id';";
if($conn->query($commentSQL)===TRUE){
    $conn->close();

// Display message to confirm that staff member was assigned request/ticket
$_SESSION['success_message'] = "Successfully Assigned The Ticket!";
//redirect to dashboard
header("Location: management.php");

exit();
}else{
    echo"<p style=\"error\"><b>Failed to submit ticket!</p>";
}
