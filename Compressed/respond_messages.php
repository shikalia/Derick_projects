<?php
session_start();
include 'db_connect.php';

// Ensure the doctor is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Fetch messages for the doctor
$stmt = $conn->prepare("
    SELECT messages.*, users.name AS patient_name
    FROM messages
    JOIN users ON messages.sender_id = users.id
    WHERE messages.receiver_id = ?
    ORDER BY messages.sent_at DESC
");
$stmt->execute([$doctor_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Patient Messages</h2>
    
    <?php if (empty($messages)): ?>
        <p class="alert alert-info">No messages found.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($messages as $message): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($message['patient_name']); ?>:</strong>
                    <?= nl2br(htmlspecialchars($message['content'])); ?>
                    <small class="text-muted">(<?= date('M d, Y h:i A', strtotime($message['sent_at'])); ?>)</small>
                    <a href="reply_message.php?id=<?= $message['id']; ?>" class="btn btn-sm btn-outline-success float-end">Reply</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>
