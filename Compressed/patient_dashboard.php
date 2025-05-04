<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php'; // Database connection

// Fetch logged-in patient details
$patient_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$patient_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$patient_name = $user ? htmlspecialchars($user['name']) : 'Patient';

// Fetch patient appointments
$appt_stmt = $conn->prepare("SELECT a.id, a.appointment_date, u.name AS doctor_name, a.status 
                            FROM appointments a
                            JOIN users u ON a.doctor_id = u.id
                            WHERE a.patient_id = ?
                            ORDER BY a.appointment_date ASC");
$appt_stmt->execute([$patient_id]);
$appointments = $appt_stmt->fetchAll(PDO::FETCH_ASSOC);

// Use a UNION query to combine doctor's direct messages and doctor's replies
$msg_query = "
    SELECT message AS text, sent_at AS time, doctor_name FROM (
        SELECT m.message, m.sent_at, u.name AS doctor_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ? AND u.role = 'Doctor'
        UNION ALL
        SELECT r.reply_text, r.replied_at, u.name 
        FROM replies r
        JOIN messages m ON r.message_id = m.id
        JOIN users u ON u.id = r.doctor_id
        WHERE m.sender_id = ?
    ) AS combined
    ORDER BY time DESC
";
$msg_stmt = $conn->prepare($msg_query);
$msg_stmt->execute([$patient_id, $patient_id]);
$doctor_messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch doctors list for messaging
$doctor_stmt = $conn->prepare("SELECT id, name FROM users WHERE role = 'Doctor'");
$doctor_stmt->execute();
$doctors = $doctor_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("Assets/images/MY PHOTO.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
        .dashboard-card {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .message-card {
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="patient_dashboard.php">Patient Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="book_appointment.php">Book Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_prescription.php">My Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="message_doctor.php">Message Doctor</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container dashboard-card bg-white shadow rounded">
        <h2 class="text-center"><strong>Welcome, <?= $patient_name; ?>!</strong></h2>
        <p class="text-center"><strong>Manage your appointments, prescriptions, and communicate with your doctor.</strong></p>

        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <a href="book_appointment.php" class="btn btn-outline-primary w-100"><strong>Book Appointment</strong></a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="message_doctor.php" class="btn btn-primary w-100"><strong>Message Doctor</strong></a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="view_prescription.php" class="btn btn-outline-info w-100"><strong>My Prescriptions</strong></a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="index.php" class="btn btn-outline-info w-100"><strong>Back to Homepage</strong></a>
            </div>
        </div>
    </div>


        <!-- Appointments Section -->
        <div class="card mt-4 p-3 shadow">
            <h4 class="text-center">My Appointments</h4>
            <?php if (empty($appointments)): ?>
                <p class="text-center text-muted">No upcoming appointments.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Doctor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= date('d M Y, h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?= htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?= ucfirst($appointment['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Messages Section -->
        <div class="card mt-4 p-3 shadow">
            <h3 class="mb-3">Messages from Doctors</h3>
            <?php if (empty($doctor_messages)): ?>
                <p class="text-center text-muted">No messages yet.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($doctor_messages as $msg): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($msg['doctor_name']); ?>:</strong>
                            <?= htmlspecialchars($msg['text']); ?>
                            <br>
                            <small class="text-muted">(<?= $msg['time']; ?>)</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Message Doctor Form -->
        <div class="card mt-4 p-4 shadow message-card">
            <h4 class="mb-3">Send Message to Doctor</h4>
            <form id="messageForm" action="send_message.php" method="POST">
                <div class="mb-3">
                    <label for="doctor" class="form-label">Select Doctor:</label>
                    <select name="receiver_id" class="form-select" required>
                        <option value="">-- Select Doctor --</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id']; ?>"><?= htmlspecialchars($doctor['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message:</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Type your message here..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Send Message</button>
            </form>
            <div id="responseMessage" class="mt-3"></div>
        </div>

    </div>

    <!-- JavaScript: AJAX for Message Submission -->
    <script>
        document.getElementById("messageForm").onsubmit = function(event) {
            event.preventDefault(); // Prevent default form submission
            
            let formData = new FormData(this);

            fetch("send_message.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let responseDiv = document.getElementById("responseMessage");
                if (data.status === "success") {
                    responseDiv.innerHTML = '<div class="alert alert-success text-center">' + data.message + '</div>';
                    document.getElementById("messageForm").reset();
                } else {
                    responseDiv.innerHTML = '<div class="alert alert-danger text-center">' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("responseMessage").innerHTML = '<div class="alert alert-danger text-center">An error occurred. Please try again.</div>';
            });
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
