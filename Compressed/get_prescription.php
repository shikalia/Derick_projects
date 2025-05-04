<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Patient') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch prescriptions
$stmt = $conn->prepare("SELECT prescriptions.medicine, prescriptions.dosage, users.name AS doctor_name, prescriptions.created_at 
                        FROM prescriptions 
                        JOIN users ON prescriptions.doctor_id = users.id 
                        WHERE prescriptions.patient_id = ? 
                        ORDER BY prescriptions.created_at DESC");
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($prescriptions);
?>
