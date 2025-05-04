<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $appointment_id = $_POST['id'];
        $status = $_POST['status'];
        $doctor_id = $_SESSION['user_id'];

        // Debugging: Log received values
        error_log("Doctor ID: $doctor_id, Appointment ID: $appointment_id, Status: $status");

        // Update appointment status
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        if ($stmt->execute([$status, $appointment_id, $doctor_id])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Appointment updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "No changes made. Check if appointment exists."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "SQL execution failed"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
