<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) { // Changed to check 'id' instead of 'email'
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch filter from request
$filter = $_GET['filter'] ?? 'all';
$user_id = $_SESSION['id']; // Changed to retrieve 'id' from session

// Build query based on filter
switch ($filter) {
    case 'Sales Lead':
        $query = "SELECT * FROM contacts WHERE type = 'Sales Lead'";
        break;
    case 'Support':
        $query = "SELECT * FROM contacts WHERE type = 'Support'";
        break;
    case 'assigned':
        $query = "SELECT * FROM contacts WHERE assigned_to = ?";
        break;
    default:
        $query = "SELECT * FROM contacts";
}

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$stmt = $conn->prepare($query);

if ($filter === 'assigned') {
    // Bind the user ID instead of the email
    $stmt->bind_param('i', $user_id);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$contacts = $result->fetch_all(MYSQLI_ASSOC);

// Return contacts as JSON
header('Content-Type: application/json');
echo json_encode($contacts);

$stmt->close();
$conn->close();
?>
