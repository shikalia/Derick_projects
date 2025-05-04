<!-- filepath: c:\xampp\htdocs\Compressed\About_Us.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Modern Health Clinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: green;
        }
        header {
            background-color: #333;
            color: white;
            padding: 1em 0;
            text-align: center;
        }
        header ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        header ul li {
            display: inline;
            margin: 0 10px;
        }
        header ul li a {
            color: white;
            text-decoration: none;
        }
        header ul li a.active {
            font-weight: bold;
            color: #007bff;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .content {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .content h1 {
            text-align: center;
            color: #333;
        }
        .content p {
            line-height: 1.6;
            color: #666;
        }
        .content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .contact-info {
            margin-top: 20px;
        }
        .contact-info h2 {
            color: #333;
        }
        .contact-info p {
            color: #666;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1em 0;
            position: relative;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="About_Us.php" class="active">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="contacts.php">Contacts</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <div class="content">
            <h1>About Modern Health Clinic</h1>
            <p>Welcome to Modern Health Clinic, where your health is our priority. We are dedicated to providing the best healthcare services to our patients. Our team of experienced doctors and healthcare professionals are here to ensure you receive the highest quality care.</p>
            <img src="Assets/images/logo-removebg-preview.png" alt="Modern Health Clinic">
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p><strong>Address:</strong> 123 Health St, Wellness City, HC 45678</p>
                <p><strong>Phone:</strong> (123) 456-7890</p>
                <p><strong>Email:</strong> info@modernhealthclinic.com</p>
                <p class="back-home"><a href="index.php">Back to homepage</a></p>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Modern Health Clinic. All rights reserved.</p>
    </footer>
</body>
</html>