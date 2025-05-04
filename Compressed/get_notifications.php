<?php
session_start();
require 'db_connect.php';

$doctor_id = $_SESSION['user_id'];

// Count pending appointments
$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'Pending'");
$stmt->execute([$doctor_id]);
$pendingAppointments = $stmt->fetchColumn();

// Count unread messages
$stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$doctor_id]);
$unreadMessages = $stmt->fetchColumn();

// Total notifications
$totalNotifications = $pendingAppointments + $unreadMessages;

echo json_encode(["total" => $totalNotifications]);
?>
