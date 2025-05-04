<!-- filepath: c:\xampp\htdocs\Compressed\services.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Modern Health Clinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('Assets/images/MY PHOTO.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-color: #f4f4f4;
            
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
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .content {
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .content h1 {
            text-align: center;
            color: #333;
        }
        .services {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .service-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 200px;
          
            padding: 20px;
            text-align: center;
        }
        .service-card img {
            width: 50px;
            height: auto;
            border-radius: 100%;
            object-fit: cover;
        }
        .service-card h3 {
            color: black;
            font-size: 1.2em;
            margin: 10px 0;
        }
        .service-card p {
            color: black;
            font-size: 1em;
            margin: 5px 0;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1em 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="About_Us.php">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="contacts.php">Contacts</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <div class="content">
            <h1>Our Services</h1>
            <div class="services">
                <div class="service-card">
                    
                    <h3>First Aid</h3>
                    <p>Immediate assistance for injuries and emergencies.</p>
                </div>
                <div class="service-card">
                   
                    <h3>Headache</h3>
                    <p>Treatment and relief for various types of headaches.</p>
                </div>
                <div class="service-card">
                  
                    <h3>Stomach Ache</h3>
                    <p>Diagnosis and treatment for stomach pain and discomfort.</p>
                </div>
                <div class="service-card">
                   
                    <h3>General Checkup</h3>
                    <p>Comprehensive health checkups and screenings.</p>
                </div>
                <div class="service-card">
                  
                    <h3>Vaccination</h3>
                    <p>Immunizations to protect against various diseases.</p>
                </div>
                <div class="service-card">
                    
                    <h3>Mental Health</h3>
                    <p>Counseling and support for mental well-being.</p>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Modern Health Clinic. All rights reserved.</p>
    </footer>
</body>
</html>