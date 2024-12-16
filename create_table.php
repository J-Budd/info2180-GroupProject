<?php
$host = 'localhost'; // Change to your database host
$dbname = 'dolphin_crm'; // Database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Create the `users` table
    $pdo->exec("CREATE TABLE `users`(
        `id` INTEGER(12) NOT NULL AUTO_INCREMENT,
        `firstname` VARCHAR(32) DEFAULT NULL,
        `lastname` VARCHAR(32) DEFAULT NULL,
        `password` VARCHAR(65) DEFAULT NULL,
        `email` VARCHAR(32) DEFAULT NULL,
        `role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");

    // Create the `contacts` table
    $pdo->exec("CREATE TABLE `contacts`(
        `id` INTEGER(12) NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(32) DEFAULT NULL,
        `firstname` VARCHAR(32) DEFAULT NULL,
        `lastname` VARCHAR(32) DEFAULT NULL,
        `email` VARCHAR(32) DEFAULT NULL,
        `telephone` VARCHAR(16) DEFAULT NULL,
        `company` VARCHAR(64) DEFAULT NULL,
        `type` enum('Sales Lead','Support') NOT NULL DEFAULT 'Sales Lead',
        `assigned_to` INTEGER(12) NOT NULL,
        `created_by` INTEGER(12) NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");

    // Create the `notes` table
    $pdo->exec("CREATE TABLE notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contact_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_by INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contact_id) REFERENCES contacts(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");

    // Function to check password strength (at least one number and one special character)
    function isStrongPassword($password) {
        return preg_match('/\d/', $password) && preg_match('/[\W_]/', $password);
    }

    // Insert 5 users into `users` table
    $users = [
        ['John', 'Doe', 'john.doe@example.com', 'Password123!'],
        ['Jane', 'Smith', 'jane.smith@example.com', 'StrongPassword1!'],
        ['Alice', 'Johnson', 'alice.johnson@example.com', 'SecurePass1@'],
        ['Bob', 'Brown', 'bob.brown@example.com', 'TestPassword2@'],
        ['Charlie', 'Davis', 'charlie.davis@example.com', 'ExamplePassword3#']
    ];

    foreach ($users as $user) {
        list($firstname, $lastname, $email, $password) = $user;

        if (isStrongPassword($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL query to insert data into `users` table
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, password, email, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $hashedPassword, $email, 'Member']);
            echo "User $firstname $lastname inserted successfully!\n";
        } else {
            echo "Password for $firstname $lastname must contain at least one number and one special character.\n";
        }
    }

    // Insert 5 contacts into `contacts` table (assuming user IDs 1-5 exist)
    $contacts = [
        ['Mr.', 'Alice', 'Smith', 'alice.smith@example.com', '123-456-7890', 'XYZ Corp', 'Sales Lead', 1, 1],
        ['Ms.', 'Bob', 'Jones', 'bob.jones@example.com', '234-567-8901', 'ABC Ltd.', 'Support', 2, 1],
        ['Dr.', 'Charlie', 'Brown', 'charlie.brown@example.com', '345-678-9012', 'Tech Solutions', 'Sales Lead', 3, 2],
        ['Mrs.', 'Diana', 'Taylor', 'diana.taylor@example.com', '456-789-0123', 'Future Inc.', 'Sales Lead', 4, 3],
        ['Mr.', 'Edward', 'Miller', 'edward.miller@example.com', '567-890-1234', 'GlobalTech', 'Support', 5, 4]
    ];

    foreach ($contacts as $contact) {
        list($title, $firstname, $lastname, $email, $telephone, $company, $type, $assignedTo, $createdBy) = $contact;

        // Prepare SQL query to insert data into `contacts` table
        $stmt = $pdo->prepare("INSERT INTO contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $firstname, $lastname, $email, $telephone, $company, $type, $assignedTo, $createdBy]);
        echo "Contact $firstname $lastname inserted successfully!\n";
    }

    // Insert data into `notes`
    $contactId = 1; // Assuming contact with id 1 exists
    $noteComment = 'This is a note about the contact';
    
    $stmt = $pdo->prepare("INSERT INTO notes (contact_id, comment, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$contactId, $noteComment, 1]);
    echo "Note inserted successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
