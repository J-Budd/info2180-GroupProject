<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Retrieve users from the database
$stmt = $conn->prepare("SELECT firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users - Dolphin CRM</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Simple Table Styling */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        header, footer {
            text-align: center;
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #333;
        }
    </style>
</head>
<body>
<header>
    <h1>View Users</h1>
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
    <h2>List of Users</h2>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2024 Dolphin CRM. All rights reserved.</p>
</footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
