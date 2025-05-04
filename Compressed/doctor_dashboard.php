<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
$doctor_id = $_SESSION['user_id'];

// Fetch doctor details
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$doctor_id]);
$user = $stmt->fetch();
$doctor_name = $user ? htmlspecialchars($user['name']) : 'Doctor';

// Fetch appointments for this doctor
$appt_stmt = $conn->prepare("SELECT a.id, a.appointment_date, u.name AS patient_name, a.status 
                            FROM appointments a
                            JOIN users u ON a.patient_id = u.id
                            WHERE a.doctor_id = ?
                            ORDER BY a.appointment_date ASC");
$appt_stmt->execute([$doctor_id]);
$appointments = $appt_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("Assets/images/MY PHOTO.jpg");
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background:green;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            display: block;
            color: white;
        }
        .sidebar a:hover {
            background:green;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Doctor Panel</h4>
        <a href="doctor_dashboard.php">Dashboard</a>
        <a href="write_prescription.php">write prescription</a>
        <a href="view_messages.php">Messages</a>
        <a href="logout.php" class="text-danger">Logout</a>
        <p class="back-home"><a href="index.php">Back to homepage</a></p>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container mt-4">
            <div class="card shadow p-4">
                <h2 class="text-center">Welcome, Dr. <?= $doctor_name; ?>!</h2>
                <p class="text-center">Manage your appointments and update their status.</p>

            </div>
        
    


            <!-- Display Appointments -->
            <div class="card mt-4 p-3 shadow">
                <h4 class="text-center">Upcoming Appointments</h4>

                <?php if (empty($appointments)): ?>
                    <p class="text-center text-muted">No upcoming appointments.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Date & Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= date('d M Y, h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                    <td><?= htmlspecialchars($appointment['patient_name']); ?></td>
                                    <td id="status-<?= $appointment['id']; ?>"><?= ucfirst($appointment['status']); ?></td>
                                    <td>
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <button class="btn btn-success btn-sm" id="approve-<?= $appointment['id']; ?>" onclick="updateStatus(<?= $appointment['id']; ?>, 'approved')">✔ Approve</button>
                                            <button class="btn btn-danger btn-sm" id="reject-<?= $appointment['id']; ?>" onclick="updateStatus(<?= $appointment['id']; ?>, 'rejected')">✖ Reject</button>
                                        <?php else: ?>
                                            <span class="text-muted">No actions</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(appointmentId, status) {
            let approveBtn = document.getElementById(`approve-${appointmentId}`);
            let rejectBtn = document.getElementById(`reject-${appointmentId}`);

            // Disable buttons to prevent multiple clicks
            if (approveBtn) approveBtn.disabled = true;
            if (rejectBtn) rejectBtn.disabled = true;

            fetch('update_appointment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${appointmentId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${appointmentId}`).innerText = status.charAt(0).toUpperCase() + status.slice(1);
                } else {
                    alert("Failed to update status.");
                    if (approveBtn) approveBtn.disabled = false;
                    if (rejectBtn) rejectBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error updating appointment.");
                if (approveBtn) approveBtn.disabled = false;
                if (rejectBtn) rejectBtn.disabled = false;
            });
        }
    </script>

</body>
</html>
