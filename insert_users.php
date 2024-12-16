<?php
// Database credentials
$host = 'localhost';  // Your MySQL host
$user = 'root';  // Replace with your MySQL username
$password = '';  // Replace with your MySQL password
$dbname = 'dolphin_crm';  // The database to use

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Drop tables if they exist
try {
    // Drop 'users' table if it exists
    $pdo->exec("DROP TABLE IF EXISTS users");

    // Drop 'contacts' table if it exists
    $pdo->exec("DROP TABLE IF EXISTS contacts");

    echo "Tables dropped if they existed.\n";
} catch (PDOException $e) {
    echo "Error dropping tables: " . $e->getMessage();
}

// Create the 'users' table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT(12) NOT NULL AUTO_INCREMENT,
            firstname VARCHAR(32) DEFAULT NULL,
            lastname VARCHAR(32) DEFAULT NULL,
            password VARCHAR(65) DEFAULT NULL,
            email VARCHAR(32) DEFAULT NULL,
            role ENUM('Member', 'Admin') NOT NULL DEFAULT 'Member',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )
    ");
    echo "Users table created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating users table: " . $e->getMessage();
}

// Create the 'contacts' table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id INT(12) NOT NULL AUTO_INCREMENT,
            title VARCHAR(32) DEFAULT NULL,
            firstname VARCHAR(32) DEFAULT NULL,
            lastname VARCHAR(32) DEFAULT NULL,
            email VARCHAR(32) DEFAULT NULL,
            telephone VARCHAR(16) DEFAULT NULL,
            company VARCHAR(64) DEFAULT NULL,
            type ENUM('Sales Lead', 'Support') NOT NULL DEFAULT 'Sales Lead',
            assigned_to INT(12) NOT NULL,
            created_by INT(12) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )
    ");
    echo "Contacts table created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating contacts table: " . $e->getMessage();
}

// Password validation function
function isValidPassword($password) {
    // Check if the password contains at least one number and one special character
    return preg_match('/\d/', $password) && preg_match('/[\W_]/', $password);
}

// Users to insert (ensure the passwords meet the criteria)
$users = [
    ['John', 'Doe', 'password123!', 'john.doe@example.com', 'Admin'],
    ['Jane', 'Smith', 'mypassword456#', 'jane.smith@example.com', 'Member'],
    ['Alice', 'Johnson', 'abc123$%!', 'alice.johnson@example.com', 'Member'],
    ['Bob', 'Brown', 'brown2021@', 'bob.brown@example.com', 'Admin'],
    ['Charlie', 'Davis', 'davis321!@', 'charlie.davis@example.com', 'Member']
];

// Insert users into the 'users' table
try {
    // Prepare SQL query to insert users
    $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, password, email, role) VALUES (?, ?, ?, ?, ?)");

    foreach ($users as $user) {
        list($firstname, $lastname, $plainPassword, $email, $role) = $user;

        // Validate password
        if (!isValidPassword($plainPassword)) {
            echo "Password for $firstname $lastname is invalid. Skipping this user.\n";
            continue;
        }

        // Hash the password using password_hash
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Execute the insert query
        $stmt->execute([$firstname, $lastname, $hashedPassword, $email, $role]);
    }

    echo "Users inserted successfully!\n";
} catch (PDOException $e) {
    echo "Error inserting users: " . $e->getMessage();
}

// Insert contacts into the 'contacts' table
$contacts = [
    ['Mr.', 'Michael', 'Jordan', 'michael.jordan@example.com', '1234567890', 'Nike', 'Sales Lead', 1, 1],
    ['Ms.', 'Serena', 'Williams', 'serena.williams@example.com', '0987654321', 'Wilson', 'Sales Lead', 2, 2],
    ['Dr.', 'Elon', 'Musk', 'elon.musk@example.com', '1231231234', 'SpaceX', 'Sales Lead', 3, 3],
    ['Mr.', 'Bill', 'Gates', 'bill.gates@example.com', '4564564567', 'Microsoft', 'Support', 4, 4],
    ['Mrs.', 'Sheryl', 'Sandberg', 'sheryl.sandberg@example.com', '7897897890', 'Facebook', 'Support', 5, 5]
];

try {
    // Prepare SQL query to insert contacts
    $stmt = $pdo->prepare("INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($contacts as $contact) {
        list($title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by) = $contact;
        // Execute the insert query for each contact
        $stmt->execute([$title, $firstname, $lastname, $email, $telephone, $company, $type, $assigned_to, $created_by]);
    }

    echo "Contacts inserted successfully!\n";
} catch (PDOException $e) {
    echo "Error inserting contacts: " . $e->getMessage();
}

?>
