import mysql.connector # type: ignore
import bcrypt # type: ignore

def create_database_and_tables():
    # Connect to MySQL
    db = mysql.connector.connect(
        host="localhost",  # Your MySQL host
        user="root",  # Replace with your MySQL username
        password=""  # Replace with your MySQL password
    )

    cursor = db.cursor()

    # Create the database if it doesn't exist
    cursor.execute("CREATE DATABASE IF NOT EXISTS dolphin_crm")
    cursor.execute("USE dolphin_crm")

    # Create the 'users' table if it doesn't exist
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INTEGER(12) NOT NULL AUTO_INCREMENT,
            `firstname` VARCHAR(32) DEFAULT NULL,
            `lastname` VARCHAR(32) DEFAULT NULL,
            `password` VARCHAR(65) DEFAULT NULL,
            `email` VARCHAR(32) DEFAULT NULL,
            `role` ENUM('Member', 'Admin') NOT NULL DEFAULT 'Member',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )
    """)

    # Create the 'contacts' table if it doesn't exist
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS `contacts` (
            `id` INTEGER(12) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(32) DEFAULT NULL,
            `firstname` VARCHAR(32) DEFAULT NULL,
            `lastname` VARCHAR(32) DEFAULT NULL,
            `email` VARCHAR(32) DEFAULT NULL,
            `telephone` VARCHAR(16) DEFAULT NULL,
            `company` VARCHAR(64) DEFAULT NULL,
            `type` ENUM('Sales Lead', 'Support') NOT NULL DEFAULT 'Sales Lead',
            `assigned_to` INTEGER(12) NOT NULL,
            `created_by` INTEGER(12) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )
    """)

    db.commit()
    cursor.close()
    db.close()

def insert_users():
    # Connect to MySQL database
    db = mysql.connector.connect(
        host="localhost",  # Change to your MySQL server address
        user="root",  # Replace with your MySQL username
        password="",  # Replace with your MySQL password
        database="dolphin_crm"  # The database to insert into
    )

    cursor = db.cursor()

    # List of users with plain passwords
    users = [
        ('John', 'Doe', 'password123', 'john.doe@example.com', 'Admin'),
        ('Jane', 'Smith', 'password456', 'jane.smith@example.com', 'Member'),
        ('Alice', 'Johnson', 'password789', 'alice.johnson@example.com', 'Member'),
        ('Bob', 'Brown', 'password101', 'bob.brown@example.com', 'Admin'),
        ('Charlie', 'Davis', 'password112', 'charlie.davis@example.com', 'Member')
    ]

    # Insert users data with hashed passwords
    for user in users:
        firstname, lastname, plain_password, email, role = user
        
        # Hash the password using bcrypt
        hashed_password = bcrypt.hashpw(plain_password.encode('utf-8'), bcrypt.gensalt())
        
        # Insert the user data into the users table
        cursor.execute("""
            INSERT INTO `users` (`firstname`, `lastname`, `password`, `email`, `role`)
            VALUES (%s, %s, %s, %s, %s)
        """, (firstname, lastname, hashed_password.decode('utf-8'), email, role))

    # Commit the transaction
    db.commit()

    print("Users data with hashed passwords inserted successfully!")

    cursor.close()
    db.close()

def insert_contacts():
    # Connect to MySQL database
    db = mysql.connector.connect(
        host="localhost",  # Change to your MySQL server address
        user="root",  # Replace with your MySQL username
        password="",  # Replace with your MySQL password
        database="dolphin_crm"  # The database to insert into
    )

    cursor = db.cursor()

    # Insert data into the contacts table
    contacts = [
        ('Mr.', 'Michael', 'Jordan', 'michael.jordan@example.com', '1234567890', 'Nike', 'Sales Lead', 1, 1),
        ('Ms.', 'Serena', 'Williams', 'serena.williams@example.com', '0987654321', 'Wilson', 'Sales Lead', 2, 2),
        ('Dr.', 'Elon', 'Musk', 'elon.musk@example.com', '1231231234', 'SpaceX', 'Sales Lead', 3, 3),
        ('Mr.', 'Bill', 'Gates', 'bill.gates@example.com', '4564564567', 'Microsoft', 'Support', 4, 4),
        ('Mrs.', 'Sheryl', 'Sandberg', 'sheryl.sandberg@example.com', '7897897890', 'Facebook', 'Support', 5, 5)
    ]
    
    for contact in contacts:
        title, firstname, lastname, email, telephone, company, contact_type, assigned_to, created_by = contact
        
        cursor.execute("""
            INSERT INTO `contacts` (`title`, `firstname`, `lastname`, `email`, `telephone`, `company`, `type`, `assigned_to`, `created_by`)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, (title, firstname, lastname, email, telephone, company, contact_type, assigned_to, created_by))
    
    # Commit the transaction
    db.commit()

    print("Contacts data inserted successfully!")

    cursor.close()
    db.close()

if __name__ == "__main__":
    # First, create the database and tables
    create_database_and_tables()

    # Insert users and contacts
    insert_users()
    insert_contacts()
