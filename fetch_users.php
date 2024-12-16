<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Access denied."]);
    exit;
}

header('Content-Type: application/json');

try {
    // Prepare and execute the query to fetch users
    $stmt = $conn->prepare("SELECT firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'email' => $row['email'],
            'role' => $row['role'],
            'created_at' => $row['created_at']
        ];
    }

    // Return the user data as JSON
    echo json_encode($users);
} catch (Exception $e) {
    // Handle errors
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Failed to fetch users."]);
    error_log("Error fetching users: " . $e->getMessage());
}

// Close database connection
$stmt->close();
$conn->close();
?>