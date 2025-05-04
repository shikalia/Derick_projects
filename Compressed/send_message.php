<?php
session_start();
require 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "You must be logged in to send messages."]);
        exit();
    }

    $sender_id = $_SESSION['user_id']; // Logged-in patient sending the message
    $receiver_id = !empty($_POST['receiver_id']) ? trim($_POST['receiver_id']) : null;
    $message = !empty($_POST['message']) ? trim($_POST['message']) : null;

    // Check if all fields are filled
    if (empty($receiver_id) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    try {
        // Insert message into the database
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$sender_id, $receiver_id, $message]);

        echo json_encode(["status" => "success", "message" => "Message sent successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
    exit(); // Stop further execution to prevent HTML being sent in the response
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <script>
        function sendMessage() {
            let receiverId = document.getElementById("receiver_id").value;
            let message = document.getElementById("message").value;

            // Ensure fields are not empty
            if (receiverId.trim() === "" || message.trim() === "") {
                document.getElementById("message_status").innerHTML = `<p style="color: red;">All fields are required.</p>`;
                return;
            }

            fetch("send_message.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`,
            })
            .then(response => response.json())
            .then(data => {
                let messageBox = document.getElementById("message_status");
                if (data.status === "success") {
                    messageBox.innerHTML = `<p style="color: green;">${data.message}</p>`;
                    document.getElementById("message_form").reset(); // Clear form after sending
                } else {
                    messageBox.innerHTML = `<p style="color: red;">${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("message_status").innerHTML = `<p style="color: red;">An error occurred. Please try again.</p>`;
            });
        }
    </script>
</head>
<body>

    <h2>Send a Message</h2>
    <form id="message_form" onsubmit="event.preventDefault(); sendMessage();">
        <label for="receiver_id">Receiver ID:</label>
        <input type="text" id="receiver_id" name="receiver_id" required><br><br>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea><br><br>

        <button type="submit">Send Message</button>
    </form>

    <div id="message_status"></div>

</body>
</html>
