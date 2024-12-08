<?php
session_start();
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

// Fetch user information
$user_name = $_SESSION['firstname'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS file -->
</head>
<body>
    <header>
        <h1>Welcome to Dolphin CRM</h1>
        <p>Hello, <strong><?php echo htmlspecialchars($user_name); ?></strong>! (Role: <?php echo htmlspecialchars($role); ?>)</p>
    </header>



    <main>
        <section>
            <h2>Dashboard Overview</h2>

            <div class="quick-actions">
                <a href="add_contact.php" class="button">+ Add Contact</a>
                <?php if ($role === 'Admin'): ?>
                    <a href="add_user.php" class="button">+ Add User</a>
                <?php endif; ?>
                <a href="view_contacts.php" class="button">View All Contacts</a>
            </div>
        </section>
    </main>

</body>
</html>
