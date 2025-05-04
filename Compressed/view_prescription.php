<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
$patient_id = $_SESSION['user_id'];

// Fetch prescriptions for this patient
$stmt = $conn->prepare("SELECT p.id, p.prescription_text, p.created_at, d.name AS doctor_name 
                        FROM prescriptions p
                        JOIN users d ON p.doctor_id = d.id
                        WHERE p.patient_id = ?
                        ORDER BY p.created_at DESC");
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">My Prescriptions</h2>

        <div class="card p-4 shadow">
            <?php if (empty($prescriptions)): ?>
                <p class="text-muted text-center">No prescriptions available.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Doctor</th>
                            <th>Prescription</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $prescription): ?>
                            <tr>
                                <td><?= htmlspecialchars($prescription['doctor_name']); ?></td>
                                <td><?= nl2br(htmlspecialchars($prescription['prescription_text'])); ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($prescription['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="book_appointment.php" class="btn btn-primary">Book an Appointment</a>
        <a href="message_doctor.php" class="btn btn-secondary">Message Your Doctor</a>
        <a href="view_prescription.php" class="btn btn-secondary">View My Prescriptions</a>
    <a href="patient_dashboard.php" class="btn btn-secondary">Back to the Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
