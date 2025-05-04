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
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND patient_id = ?");
        $stmt->execute([$appointment_id, $_SESSION['patient_id']]);

        $_SESSION['success'] = "Appointment deleted successfully!";
        header("Location: patient_dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting appointment: " . $e->getMessage());
    }
}
?>
