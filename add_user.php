<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Initialize variables for form data and error messages
$firstname = '';
$lastname = '';
$email = '';
$password = '';
$role = 'Member'; // Default role
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate form data
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // Check if the password contains at least one special character
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = 'Password must contain at least one special character.';
    }

    // Check if the password contains at least one number
    if (!preg_match('/\d/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = 'Email is already registered.';
    }
    $stmt->close();

    // If there are no errors, proceed to insert the new user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header('Location: dashboard.php'); // Redirect to dashboard after successful addition
            exit;
        } else {
            $errors[] = 'Error adding user. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Dolphin CRM</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Add New User</h1>
</header>

<nav>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="view_contacts.php">View Contacts</a></li>
        <li><a href="view_users.php">View Users</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main>
    <h2>Create a New User</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="add_user.php" method="POST">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" 
               value="<?php echo htmlspecialchars($firstname); ?>" 
               placeholder="Enter first name" required>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" 
               value="<?php echo htmlspecialchars($lastname); ?>" 
               placeholder="Enter last name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" 
               value="<?php echo htmlspecialchars($email); ?>" 
               placeholder="Enter email address" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" 
               placeholder="Enter password" required>

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="Member" <?php echo ($role === 'Member') ? 'selected' : ''; ?>>Member</option>
            <option value="Admin" <?php echo ($role === 'Admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit">Add User</button>
    </form>
</main>

<footer>
    <p>&copy; 2024 Dolphin CRM. All rights reserved.</p>
</footer>
</body>
</html>
