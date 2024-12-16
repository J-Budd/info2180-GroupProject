<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['email']) || !isset($_SESSION['firstname']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// Fetch user information from session
$user_name = $_SESSION['firstname'];
$role = $_SESSION['role'];
$email = $_SESSION['email'];
$_SESSION['note'] = '';

$stmt = $conn->prepare("SELECT firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

$ustmt = $conn->prepare("SELECT id, firstname, lastname, email, role, created_at FROM users ORDER BY created_at DESC");
$ustmt->execute();
$uresult = $ustmt->get_result();
$users = $uresult->fetch_all(MYSQLI_ASSOC);  // Store all users in an array

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM - Dashboard</title>
    <link rel="stylesheet" href="finalstyles.css">

</head>
<body>
    <div class="sidebar">
        <h1>Dolphin CRM</h1>
        <ul>
            <li><a href="dashboard.php" id="home-link">Home</a></li>
            <li><a href="#" id ="new-contact">New Contact</a></li>
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <li><a href="#" id="users-link">Users</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <!-- <h2>Welcome to Dolphin CRM</h2>
            <p>Hello, <strong><?php echo htmlspecialchars($user_name); ?></strong>! (Role: <?php echo htmlspecialchars($role); ?>)</p> -->
        </header>

        <div class="filter-tabs">
            <a href="#" onclick="fetchContacts('all', this)">All</a>
            <a href="#" onclick="fetchContacts('Sales Lead', this)">Sales Leads</a>
            <a href="#" onclick="fetchContacts('Support', this)">Support</a>
            <a href="#" onclick="fetchContacts('assigned', this)">Assigned to Me</a>
        </div>

        <a href="#" id = "add-contact" class="add-contact">+ Add Contact</a>
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="add_user.php" id="add-user" class="add-user">+ Add User</a>
        <?php endif; ?>

        <table id="contacts-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="contacts-body">
                <!-- Contacts will load here -->
            </tbody>
        </table>

        <table id="users-table" style="display: none;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody id="users-body">
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

        <div class="new-contact-form">

            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Title</label>
                    <select name="title" id="title">
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                        <option value="Prof">Prof</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Telephone</label>
                    <input type="text" name="telephone" id="telephone">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" name="company" id="company">
                </div>

                <div class="form-group">
                    <label for="type">Type</label>
                    <select name="type" id="type">
                        <option value="Sales Lead">Sales Lead</option>
                        <option value="Support">Support</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="assigned_to">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" required>
                        <option value="">-- Select User --</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <?php echo htmlspecialchars($user['firstname'] . " " . $user['lastname']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No users available</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
        <div id="contact-details-section" style="display: none;">
            <!-- Contact details will be dynamically added here -->
        </div>
        
    </div>

    <footer>
        <a href="logout.php" class="logout-button">Logout</a>
    </footer>
    <script src="script.js"></script>

</body>
</html>