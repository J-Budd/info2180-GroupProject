<?php
// Database credentials
$host = 'localhost';        // Database server (usually localhost)
$username = 'root';         // Database username
$password = '';             // Database password
$database = 'dolphin_crm';  // Name of the database defined in schema.sql

// Create a connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$conn) {
    echo  "Connection Failed";
}

// Connection successful
?>
