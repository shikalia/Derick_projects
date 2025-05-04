<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date_raw = $_POST['appointment_date'];

    // Format datetime properly
    $appointment_date = date('Y-m-d H:i:s', strtotime($appointment_date_raw));
    $current_datetime = date('Y-m-d H:i:s');

    // Validate inputs
    if (empty($doctor_id) || empty($appointment_date_raw)) {
        $error = "All fields are required!";
    } elseif ($appointment_date < $current_datetime) {
        $error = "You cannot book an appointment in the past!";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) 
                                    VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$patient_id, $doctor_id, $appointment_date]);

            // Redirect with success
            header("Location: patient_dashboard.php?success=Appointment booked successfully!");
            exit();
        } catch (PDOException $e) {
            $error = "Error booking appointment: " . $e->getMessage();
        }
    }
}

// Fetch available doctors
$doctors = $conn->query("SELECT id, name FROM users WHERE role = 'Doctor'")
                ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="patient_dashboard.php">Patient Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow p-4">
            <h2 class="text-center">Book an Appointment</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor:</label>
                    <select name="doctor_id" id="doctor_id" class="form-control" required>
                        <option value="">Choose a doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id']; ?>"><?= htmlspecialchars($doctor['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date & Time:</label>
                    <input 
                        type="datetime-local" 
                        name="appointment_date" 
                        id="appointment_date" 
                        class="form-control" 
                        required
                        min="<?= date('Y-m-d\TH:i'); ?>"
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">Book Appointment</button><br><br>
                <a href="patient_dashboard.php" class="btn btn-secondary">View Appointments</a>
                <a href="patient_dashboard.php" class="btn btn-secondary">Back to the Dashboard</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
