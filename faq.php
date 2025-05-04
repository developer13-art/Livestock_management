<?php
// faq.php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Livestock Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 1.5rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #e0e0e0;
        }
        
        .auth-buttons a {
            color: white;
            text-decoration: none;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .auth-buttons .login {
            background-color: transparent;
            border: 1px solid white;
        }
        
        .auth-buttons .signup {
            background-color: white;
            color: #4CAF50;
        }
        
        .faq-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .faq-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .faq-header h1 {
            color: #4CAF50;
            margin-bottom: 1rem;
        }
        
        .faq-item {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 1.5rem;
            background-color: #f9f9f9;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }
        
        .faq-question:hover {
            background-color: #f0f0f0;
        }
        
        .faq-answer {
            padding: 1.5rem;
            display: none;
        }
        
        .faq-answer.show {
            display: block;
        }
        
        footer {
            background-color: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            list-style: none;
            margin-bottom: 1rem;
        }
        
        .footer-links li {
            margin: 0 1rem;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            margin-bottom: 1rem;
        }
        
        .social-links a {
            color: white;
            margin: 0 0.5rem;
            font-size: 1.2rem;
        }
        
        .copyright {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                text-align: center;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-links li {
                margin: 0.5rem 0;
            }
            
            .auth-buttons {
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Livestock Manager</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="faq.php" class="active">FAQ</a></li>
            </ul>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login">Login</a>
                    <a href="signup.php" class="signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <div class="faq-container">
        <div class="faq-header">
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about our platform</p>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>How do I register as a farmer?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>To register as a farmer, click on the "Sign Up" button in the top navigation and select "Farmer" as your role. Fill in all the required information including your farm name, and submit the form. Once approved, you can start listing your products.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>How can customers place orders?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Customers can browse available farms and products, select the items they want, and place orders directly through the platform. Farmers will receive notifications and can accept or reject orders based on availability.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>How do payments work on the platform?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Currently, payments are handled offline between customers and farmers. We're working on integrating secure online payment options in future updates.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>How can I find a veterinarian?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Farmers can browse available veterinarians in their area through the "Find Vets" section of their dashboard. You can view vet profiles and send consultation requests directly through the platform.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>What should I do if I forget my password?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>On the login page, click the "Forgot Password" link. You'll be prompted to enter your email address, and we'll send you instructions to reset your password.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">
                <span>How do I update my product listings?</span>
                <span>+</span>
            </div>
            <div class="faq-answer">
                <p>Farmers can manage their products through the "My Products" section of their dashboard. From there, you can add new products, edit existing listings, or remove items that are no longer available.</p>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="#">Terms</a></li>
                <li><a href="#">Privacy</a></li>
            </ul>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
            <p class="copyright">© <?php echo date('Y'); ?> Livestock Management System. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        function toggleAnswer(question) {
            const answer = question.nextElementSibling;
            const icon = question.querySelector('span:last-child');
            
            if (answer.classList.contains('show')) {
                answer.classList.remove('show');
                icon.textContent = '+';
            } else {
                answer.classList.add('show');
                icon.textContent = '-';
            }
        }
    </script>
</body>
</html>