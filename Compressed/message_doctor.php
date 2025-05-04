<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
$patient_id = $_SESSION['user_id'];

// Fetch doctors
$stmt = $conn->prepare("SELECT id, name FROM users WHERE role = 'Doctor'");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Message Your Doctor</h2>

        <div class="card p-4 shadow">
            <form action="send_message.php" method="POST">
                <input type="hidden" name="patient_id" value="<?= $patient_id; ?>">

                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor:</label>
                    <select name="doctor_id" class="form-control" required>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id']; ?>"><?= htmlspecialchars($doctor['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Your Message:</label>
                    <textarea name="message" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send Message</button>
                <a href="patient_dashboard.php" class="btn btn-secondary">Back to the Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>
