<?php
// Include the config file for database connection
include('config.php');

// Get the input data (contact ID and user ID)
$data = json_decode(file_get_contents('php://input'), true);
$contactId = $data['contact_id'];
$userId = $data['user_id'];  // Assume currentUserId is available

// Ensure contact ID and user ID are valid
if (isset($contactId) && isset($userId)) {
    // Update the contact's assigned user in the database
    $query = "UPDATE contacts SET assigned_to = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $contactId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}

// Close the database connection
mysqli_close($conn);
?>
