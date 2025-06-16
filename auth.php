<?php

// Database connection
$servername = "localhost";
$username_db = "root";  // Default for XAMPP
$password_db = "";      // Default is empty
$dbname = "todo_db";     // Your database name

$conn = new mysqli($servername, $username_db, $password_db, $dbname);


function is_user_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}
?>

