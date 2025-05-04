<?php
session_start();
require 'db_connect.php';

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$doctor_id = $_SESSION['user_id'];
$appointment_id = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : null;
$prescription = trim($_POST['prescription'] ?? '');

if (!$appointment_id || empty($prescription)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

try {
    // Retrieve the patient_id from the appointment
    $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid appointment selected.']);
        exit();
    }
    
    $patient_id = $appointment['patient_id'];

    // Insert prescription into the database including patient_id
    $stmt = $conn->prepare("INSERT INTO prescriptions (appointment_id, doctor_id, patient_id, prescription_text, created_at) 
                            VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt->execute([$appointment_id, $doctor_id, $patient_id, $prescription])) {
        // Log SQL error details (optional)
        error_log("SQL Error: " . print_r($stmt->errorInfo(), true), 3, 'errors.log');
        echo json_encode(['status' => 'error', 'message' => 'Failed to save prescription.']);
        exit();
    }

    echo json_encode(['status' => 'success', 'message' => 'Prescription saved successfully.']);
} catch (PDOException $e) {
    error_log("Database Exception: " . $e->getMessage(), 3, 'errors.log');
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
exit();
