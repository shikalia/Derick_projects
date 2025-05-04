<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Doctor') {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
$doctor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Write Prescription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background-image:url("Assets/images/MY PHOTO.jpg");
      background-size: cover;
    }
    .prescription-card {
      max-width: 600px;
      margin: 50px auto;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 30px;
    }
    h2 {
      margin-bottom: 20px;
      font-weight: 700;
      color: #343a40;
    }
    .form-label {
      font-weight: 600;
    }
    #responseMessage {
      max-width: 600px;
      margin: 20px auto;
      display: none;
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="prescription-card">
      <h2 class="text-center">Write Prescription</h2>

      <form id="prescriptionForm">
        <input type="hidden" name="doctor_id" value="<?= $doctor_id; ?>">

        <div class="mb-3">
          <label for="appointment_id" class="form-label">Select Appointment:</label>
          <select name="appointment_id" class="form-select" required>
            <option value="">-- Select Appointment --</option>
            <?php
            // Fetch doctor's appointments
            $appointments_stmt = $conn->prepare("SELECT a.id, u.name AS patient_name, a.appointment_date 
                                                  FROM appointments a
                                                  JOIN users u ON a.patient_id = u.id
                                                  WHERE a.doctor_id = ?");
            $appointments_stmt->execute([$doctor_id]);
            $appointments = $appointments_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($appointments as $appt) {
                echo "<option value='{$appt['id']}'>Patient: {$appt['patient_name']} - Date: {$appt['appointment_date']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="prescription" class="form-label">Prescription:</label>
          <textarea name="prescription" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Send Prescription</button><br><br>
        <a href="view_appointments.php" class="btn btn-secondary">View Appointments</a>
        <a href="doctor_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
      </form>
    </div>

    <div id="responseMessage"></div>
  </div>

  <script>
    $(document).ready(function () {
      $("#prescriptionForm").on("submit", function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
          url: "save_prescription.php",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function (response) {
            var msgDiv = $("#responseMessage");
            if (response.status === "success") {
              msgDiv.html('<div class="alert alert-success text-center">' + response.message + '</div>').fadeIn();
              $("#prescriptionForm")[0].reset(); // Clear the form
            } else {
              msgDiv.html('<div class="alert alert-danger text-center">' + response.message + '</div>').fadeIn();
            }
          },
          error: function () {
            $("#responseMessage").html('<div class="alert alert-danger text-center">An error occurred. Please try again.</div>').fadeIn();
          }
        });
      });
    });
  </script>
</body>
</html>
