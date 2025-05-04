<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];

    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Confirmed' WHERE id = ? AND patient_id = ?");
        $stmt->execute([$appointment_id, $_SESSION['patient_id']]);

        $_SESSION['success'] = "Appointment confirmed successfully!";
        header("Location: patientdashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error confirming appointment: " . $e->getMessage());
    }
}
?>
