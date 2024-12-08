<?php
session_start();
include 'config.php'; // Database connection

// Define the validate function
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);
    // Check for empty fields
    if (empty($email)) {
        echo "<script>
                alert('Email is required');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else if (empty($pass)) {
        echo "<script>
                alert('Password is required');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($pass, $row['password'])) {
            // Set session variables
            $_SESSION['email'] = $row['email'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['role'] = $row['role'];

            // Show success alert and redirect to the dashboard
            echo "<script>
                    alert('Login successful! Redirecting to the dashboard...');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit();
        } else {
            // Invalid credentials
            echo "<script>
                    alert('Invalid email or password');
                    window.location.href = 'index.php';
                  </script>";
            exit();
        }
    } else {
        // No matching user found
        echo "<script>
                alert('Invalid email or password');
                window.location.href = 'index.php';
              </script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
