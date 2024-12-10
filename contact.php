<?php
// contact_management.php - Handle CRUD operations for contacts

include 'config (1).php'; // Include database connection

// Helper function to send JSON responses
function sendResponse($success, $message, $data = []) {
    echo json_encode(["success" => $success, "message" => $message, "data" => $data]);
    exit;
}

// Determine action based on the request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create' || $action === 'update') {
        $title = $_POST['title'] ?? '';
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $company = $_POST['company'] ?? '';
        $type = $_POST['type'] ?? 'Sales Lead';
        $assigned_to = $_POST['assigned_to'] ?? 0;
        $created_by = $_POST['created_by'] ?? 0;

        if ($action === 'create') {
            $query = "INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssssssii', $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by);
        } else {
            $id = $_POST['id'] ?? 0;
            $query = "UPDATE contacts SET title = ?, firstname = ?, lastname = ?, email = ?, telephone = ?, company = ?, type = ?, assigned_to = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssssssii', $title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $id);
        }

        if ($stmt->execute()) {
            sendResponse(true, ucfirst($action) . " successful.");
        } else {
            sendResponse(false, "Database error: " . $conn->error);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $query = "DELETE FROM contacts WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            sendResponse(true, "Delete successful.");
        } else {
            sendResponse(false, "Database error: " . $conn->error);
        }
    } else {
        sendResponse(false, "Invalid action.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM contacts";
    $result = $conn->query($query);
    $contacts = $result->fetch_all(MYSQLI_ASSOC);
    sendResponse(true, "Contacts retrieved successfully.", $contacts);
} else {
    sendResponse(false, "Invalid request method.");
}
?>

