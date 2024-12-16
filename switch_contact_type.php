<?php
// Include the config file for database connection
include('config.php');

// Get the input data (contact ID and new type)
$data = json_decode(file_get_contents('php://input'), true);
$contactId = $data['contact_id'];
$newType = $data['new_type'];

// Ensure contact ID and type are valid
if (isset($contactId) && isset($newType) && in_array($newType, ['Sales Lead', 'Support'])) {
    // Update the contact type in the database
    $query = "UPDATE contacts SET type = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'si', $newType, $contactId);
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
