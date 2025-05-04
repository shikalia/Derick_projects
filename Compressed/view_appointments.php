<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php'; // Ensure this file exists and contains the correct DB connection

// Fetch doctor ID from session
$doctor_id = $_SESSION['user_id'];

// Fetch appointments for the doctor
try {
    $stmt = $conn->prepare("
        SELECT a.id, a.appointment_date, a.status, u.name AS patient_name 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        JOIN users u ON p.user_id = u.id  -- Fetch patient's name from users table
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date DESC
    ");
    $stmt->execute([$doctor_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Your Appointments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['id']); ?></td>
                            <td><?= htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?= htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No appointments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="doctor_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>
