<?php
$host = 'localhost'; // Change if using a remote server
$dbname = 'main_db';
$username = 'root'; // Default for XAMPP/MAMP
$password = ''; // Default is empty for XAMPP

// Enable detailed error reporting (useful for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions for errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch data as an associative array
        PDO::ATTR_EMULATE_PREPARES => false // Disable emulation mode for prepared statements
    ]);

    // Uncomment the line below to confirm connection success during testing
    // echo "Database connected successfully!";
} catch (PDOException $e) {
    // Log error to a file (recommended for production)
    file_put_contents('db_error_log.txt', date("Y-m-d H:i:s") . " - Database Connection Error: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Display a generic error message (prevents exposing sensitive details)
    die("Database connection failed. Please try again later.");
}
?>
