<?php
// Include the config file for database connection
include('config.php');

// Ensure the response is in JSON format
header('Content-Type: application/json');

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $contactId = $_GET['id'];

    // Prepare the SQL query to fetch the contact details and associated notes
    $query = "
        SELECT c.id, c.title, c.firstname, c.lastname, c.email, c.telephone, c.company, c.type, 
               c.assigned_to, c.created_at, c.updated_at,
               GROUP_CONCAT(n.comment SEPARATOR '|') AS notes
        FROM contacts c
        LEFT JOIN notes n ON n.contact_id = c.id
        WHERE c.id = ?
        GROUP BY c.id
    ";

    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the contact ID to the prepared statement
        mysqli_stmt_bind_param($stmt, 'i', $contactId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Check if contact found
        if ($contact = mysqli_fetch_assoc($result)) {
            echo json_encode($contact); // Return contact and notes as JSON
        } else {
            echo json_encode(['error' => 'Contact not found']); // No contact found
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Error preparing query']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}

// Close the database connection
mysqli_close($conn);
?>
