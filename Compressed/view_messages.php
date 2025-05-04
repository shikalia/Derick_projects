<?php
session_start();
require 'db_connect.php';

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Handle DELETE request (Delete Message)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message_id'])) {
    $delete_message_id = (int) $_POST['delete_message_id'];

    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND receiver_id = ?");
    if ($stmt->execute([$delete_message_id, $doctor_id])) {
        $_SESSION['message'] = "Message deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete message.";
    }
    header("Location: view_messages.php");
    exit();
}

// Handle REPLY submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'], $_POST['message_id'])) {
    $reply = trim($_POST['reply']);
    $message_id = (int) $_POST['message_id'];

    if (!empty($reply)) {
        try {
            $stmt = $conn->prepare("INSERT INTO replies (message_id, doctor_id, reply_text, replied_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$message_id, $doctor_id, $reply]);
            $_SESSION['message'] = "Reply sent successfully!";
        } catch (PDOException $e) {
            // Log error to a file
            error_log("Failed to send reply: " . $e->getMessage(), 3, 'errors.log');
            $_SESSION['error'] = "Failed to send reply. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Reply cannot be empty!";
    }

    // Handle AJAX success response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        echo json_encode(['status' => 'success', 'message' => 'Reply sent successfully!']);
        exit();
    }

    header("Location: view_messages.php");
    exit();
}

// Fetch messages where doctor is the receiver
$stmt = $conn->prepare("
    SELECT m.id, m.message, m.sent_at, u.name AS patient_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.receiver_id = ? 
    ORDER BY m.sent_at DESC
");
$stmt->execute([$doctor_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Messages from Patients</h2>

        <!-- Success & Error Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($messages)): ?>
            <p class="text-muted">No messages yet.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Patient</th>
                        <th>Message</th>
                        <th>Sent At</th>
                        <th>Replies</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg['patient_name']); ?></td>
                            <td><?= htmlspecialchars($msg['message']); ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($msg['sent_at'])); ?></td>
                            <td>
                                <?php
                                // Fetch replies for this message
                                $stmt = $conn->prepare("SELECT reply_text, replied_at FROM replies WHERE message_id = ?");
                                $stmt->execute([$msg['id']]);
                                $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if ($replies): ?>
                                    <ul>
                                        <?php foreach ($replies as $reply): ?>
                                            <li><?= htmlspecialchars($reply['reply_text']) . " <small>(" . date('d M Y, h:i A', strtotime($reply['replied_at'])) . ")</small>"; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted">No replies yet.</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Reply Form -->
                                <form method="POST" action="view_messages.php" class="mb-2 reply-form" data-message-id="<?= $msg['id']; ?>">
                                    <input type="hidden" name="message_id" value="<?= $msg['id']; ?>">
                                    <textarea name="reply" class="form-control mb-2" placeholder="Write reply..." required></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                                </form>
                                <div class="reply-status"></div>

                                <!-- Delete Button -->
                                <form method="POST" action="view_messages.php" class="d-inline">
                                    <input type="hidden" name="delete_message_id" value="<?= $msg['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this message?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function () {
            // Handle the reply submission via AJAX
            $('.reply-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var message_id = form.find('input[name="message_id"]').val();
                var reply = form.find('textarea[name="reply"]').val();
                var replyStatus = form.next('.reply-status');

                // Send the reply via AJAX
                $.ajax({
                    url: 'view_messages.php', 
                    type: 'POST',
                    data: {
                        message_id: message_id,
                        reply: reply
                    },
                    success: function (response) {
                        // Parse the response
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            // Display success message
                            replyStatus.html('<div class="alert alert-success">Reply sent successfully!</div>');
                            // Clear the textarea
                            form.find('textarea').val('');
                        }
                    },
                    error: function () {
                        // Display error message
                        replyStatus.html('<div class="alert alert-danger">Failed to send reply. Please try again.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
