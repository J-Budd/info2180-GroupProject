<?php
session_start();
require 'config.php'; // Ensure the database configuration is correct

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['id'])) {
    die("User is not logged in. Session ID is missing.");
}

// Debug: Log or display session ID for troubleshooting
error_log("Session ID: " . $_SESSION['id']);
// echo "Session ID: " . $_SESSION['id']; // Remove this line in production

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);
    $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING);
    $created_by = $_SESSION['id']; // This is the logged-in user's ID

    if (!$contact_id || !$note) {
        echo "Invalid input. Please try again.";
        exit;
    }

    // Database connection using mysqli
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user ID exists in the users table to satisfy the foreign key constraint
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $created_by);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        die("User ID does not exist in the users table.");
    }
    $stmt->close();

    // Optional: Check for duplicate notes
    $stmt = $conn->prepare("SELECT id FROM notes WHERE contact_id = ? AND comment = ?");
    $stmt->bind_param("is", $contact_id, $note);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "This note already exists for the selected contact.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Begin transaction to ensure both queries succeed
    $conn->begin_transaction();

    try {
        // Insert the note into the notes table
        $stmt = $conn->prepare("INSERT INTO notes (contact_id, comment, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $contact_id, $note, $created_by);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert the note.");
        }
        $stmt->close();

        // Update the `updated_at` field in the `contacts` table
        $updateStmt = $conn->prepare("UPDATE contacts SET updated_at = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $contact_id);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update the contact's updated_at field.");
        }
        $updateStmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the dashboard or contact view with a success message
        header('Location: dashboard.php?message=Note added successfully');
        exit;

    } catch (Exception $e) {
        // Roll back the transaction on failure
        $conn->rollback();
        error_log("Error adding note: " . $e->getMessage());
        echo "Error adding note. Please try again.";
    }

    // Close the connection
    $conn->close();
} else {
    echo "Invalid request method.";
    exit;
}
?>
