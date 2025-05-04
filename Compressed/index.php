<style> 
    body {
        background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        animation: backgroundSlider 20s infinite;
        font-family: Arial, sans-serif;
    }

    @keyframes backgroundSlider {
        0% {
            background-image: url('Assets/images/MY PHOTO.jpg');
        }
        33% {
            background-image: url('Assets/images/logo-removebg-preview.png');
        }
        66% {
            background-image: url('Assets/images/MY PHOTO.jpg');
        }
        100% {
            background-image: url('Assets/images/logo-removebg-preview.png');
        }
    }
    header {
        background-color: #333;
        color: black;
        padding: 1em 0;
        text-align: center;
    }
    header .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
    }
    header .navbar img {
        height: 50px;
    }
    header ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
        display: flex;
    }
    header ul li {
        margin: 0 15px;
    }
    header ul li a {
        color: white;
        text-decoration: none;
        font-size: 18px;
    }
    main {
        text-align: center;
        margin-top: 10%;
    }
    .content {
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 20px;
        border-radius: 10px;
        display: inline-block;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        animation: fadeIn 1s ease-in-out;
    }
    h1 {
        font-family: 'Georgia', serif;
        font-size: 40px;
        font-weight: bold;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    }
    .button {
        background-color: #007bff;
        border: none;
        color: white;
        padding: 12px 25px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 18px;
        margin: 10px;
        border-radius: 5px;
        transition: 0.3s ease;
    }
    .button:hover {
        background-color: #0056b3;
    }
    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 1em 0;
        position: fixed;
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
            <img src="Assets/images/logo-removebg-preview.png" alt="Clinic Logo">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="contacts.php">Contacts</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="content"></div></div>
            <h1>Welcome to Modern Health Clinic</h1>
            <p>Your health is our priority. We care for you.</p>
            <a href="login.php" class="button">Login</a>
            <a href="register.php" class="button">Sign Up</a>
        </div>
    </main>
    
    <footer>
        <p> Modern Health Clinic. We Care For You</p>
    </footer>
</body>
</html></footer>
