<!-- filepath: c:\xampp\htdocs\Compressed\contacts.php -->
<?php
session_start();

// Database connection using PDO
try {
    $dsn = "mysql:host=localhost;dbname=main_db;charset=utf8mb4";
    $username = "root"; // Change if necessary
    $password = ""; // Change if necessary
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error handling
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch results as associative array
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the `contacts` table exists
$checkTableQuery = "SHOW TABLES LIKE 'contacts'";
$tableExists = $pdo->query($checkTableQuery)->rowCount() > 0;

if (!$tableExists) {
    die("Error: Table 'contacts' does not exist. Please check your database.");
}

// Fetch contacts from the database
try {
    $query = "SELECT type, value, description FROM contacts";
    $stmt = $pdo->query($query);
    $contacts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error executing query: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff6600, #9933cc); /* Top bar gradient */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1e1e1e;
        }

        .contact-box {
            background-color: #2c2c2c;
            padding: 30px 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .contact-box h2 {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .contact-box p {
            text-align: center;
            margin-bottom: 25px;
            font-size: 14px;
            color: #ccc;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 20px;
            resize: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="contact-box">
        <h2>CONTACT US</h2>
        <p>Contact us using the form below. Enter your e-mail address and your message, then click the button.</p>
        <form method="POST" action="send_contact_message.php">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
</body>
</html>
