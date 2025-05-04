<?php
session_start();
require 'db_connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Check if fields are filled correctly
    if (empty($name) || empty($email) || empty($password) || !in_array($role, ['Patient', 'Doctor'])) {
        $error = "All fields are required and role must be valid!";
    } elseif (strlen($password) > 8) {
        $error = "Password must not exceed 8 characters!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, $role])) {
                echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error = "Error in registration. Try again!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .form-container form select,
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
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" action="register.php">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password (max 8 chars)" maxlength="8" required>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="Patient">Patient</option>
                    <option value="Doctor">Doctor</option>
                </select>
                <button type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p class="back-home"><a href="index.php">Back to homepage</a></p>
        </div>
        <div class="logo-container">
            <img src="Assets/images/logo-removebg-preview.png" alt="Logo">
        </div>
    </div>
</body>
</html>
