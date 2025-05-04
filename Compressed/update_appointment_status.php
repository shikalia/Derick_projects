<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $appointment_id = $_POST['id'];
    $status = $_POST['status'];
    $doctor_id = $_SESSION['user_id'];

    // Ensure only the assigned doctor can update the appointment
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$appointment_id, $doctor_id]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
        exit();
    }

    // Update the appointment status
    $update_stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    if ($update_stmt->execute([$status, $appointment_id])) {
        echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating appointment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
