<?php
// send_contact_message.php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($email) || empty($message)) {
        die("Please fill out both the email and the message fields.");
    }

    try {
        // Connect to DB
        $pdo = new PDO("mysql:host=localhost;dbname=main_db;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Insert into existing `contacts` table
        $stmt = $pdo->prepare("INSERT INTO contacts (type, value, description) VALUES (:type, :value, :description)");
        $stmt->execute([
            ':type' => 'Contact Form',
            ':value' => $email,
            ':description' => $message
        ]);

        // Show a success message
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Sent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: #1e1e1e;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .message-box {
            background-color: #2c2c2c;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #28a745;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>Thank You!</h2>
        <p>Your message has been successfully sent.</p>
        <a href="contacts.php">Back to Contact Page</a>
        <p class="back-home"><a href="index.php">Back to homepage</a></p>
    </div>
</body>
</html>
HTML;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // Redirect if someone opens the file directly
    header("Location: contacts.php");
    exit;
}
?>
