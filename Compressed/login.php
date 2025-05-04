<?php
session_start();
require 'db_connect.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        if (strlen($password) < 4 || strlen($password) > 8) {
            $error = "Password must be between 4 and 8 characters!";
        } else {
            // Prepare the SQL query
            $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'Patient') {
                    header("Location: patient_dashboard.php");
                } elseif ($user['role'] == 'Doctor') {
                    header("Location: doctor_dashboard.php");
                } else {
                    header("Location: admin_dashboard.php"); // Optional: For admin role
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-image: url('Assets/images/MY PHOTO.jpg');
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            display: flex;
            width: 80%;
            height: 80%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-container h2 {
            color: black;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .form-container form input,
        .form-container form button {
            width: 80%;
            padding: 10px;
            font-size: 1em;
            margin: 5px 0;
        }
        .form-container form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container form button:hover {
            background-color: #0056b3;
        }
        .logo-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .form-container p {
            color: black;
        }
        .form-container p a {
            color: #007BFF;
            text-decoration: none;
        }
        .form-container p a:hover {
            text-decoration: underline;
        }
        .back-home {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form action="login.php" method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password (4-8 characters)" minlength="4" maxlength="8" required>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register</a></p>
            <p class="back-home"><a href="index.php">Back to homepage</a></p>
        </div> 
        <div class="logo-container">
            <img src="Assets/images/logo-removebg-preview.png" alt="Logo">
        </div>
    </div>
</body>
</html>
