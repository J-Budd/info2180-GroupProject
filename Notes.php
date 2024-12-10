<?php
// notes_management.php - Handle CRUD operations for notes

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
        $contact_id = $_POST['contact_id'] ?? 0;
        $comment = $_POST['comment'] ?? '';
        $created_by = $_POST['created_by'] ?? 0;

        if ($action === 'create') {
            $query = "INSERT INTO notes (contact_id, comment, created_by) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('isi', $contact_id, $comment, $created_by);
        } else {
            $id = $_POST['id'] ?? 0;
            $query = "UPDATE notes SET comment = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $comment, $id);
        }

        if ($stmt->execute()) {
            sendResponse(true, ucfirst($action) . " successful.");
        } else {
            sendResponse(false, "Database error: " . $conn->error);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $query = "DELETE FROM notes WHERE id = ?";
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
    $contact_id = $_GET['contact_id'] ?? 0;
    $query = "SELECT * FROM notes WHERE contact_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $contact_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $notes = $result->fetch_all(MYSQLI_ASSOC);
        sendResponse(true, "Notes retrieved successfully.", $notes);
    } else {
        sendResponse(false, "Database error: " . $conn->error);
    }
} else {
    sendResponse(false, "Invalid request method.");
}
?>
