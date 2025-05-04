<?php
// about.php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Livestock Management System</title>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
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
        
        .about-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .about-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .about-header h1 {
            color: #4CAF50;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }
        
        .about-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .about-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .about-section h2 {
            color: #4CAF50;
            margin-bottom: 1rem;
        }
        
        .about-section p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .team-member {
            text-align: center;
        }
        
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 5px solid #e0f2f1;
        }
        
        .team-member h3 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .team-member p {
            color: #666;
            font-style: italic;
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
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .profiles {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">Livestock Manager</div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
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
        </div>
    </header>
    
    <div class="about-container">
        <div class="about-header">
            <h1>About Livestock Management System</h1>
            <p>Connecting farmers, customers, and veterinarians for a more efficient agricultural ecosystem</p>
        </div>
        
        <div class="about-section">
            <h2>Our Mission</h2>
            <p>At Livestock Management System, we're dedicated to revolutionizing the way livestock is managed and traded. Our platform bridges the gap between farmers who raise healthy animals, customers who want quality products, and veterinarians who ensure animal welfare.</p>
            <p>We believe in creating a transparent, efficient, and sustainable agricultural ecosystem that benefits all stakeholders.</p>
        </div>
        
        <div class="about-section">
            <h2>What We Offer</h2>
            <p><strong>For Farmers:</strong> A platform to showcase your livestock, manage inventory, connect with customers, and access veterinary services when needed.</p>
            <p><strong>For Customers:</strong> Direct access to high-quality livestock products from trusted farmers in your area, with transparent pricing and ordering systems.</p>
            <p><strong>For Veterinarians:</strong> Opportunities to connect with farmers who need your expertise, manage consultations, and grow your practice.</p>
        </div>
        
        <div class="about-section">
            <h2>Our Team</h2>
            <p>We're a passionate group of agricultural experts, technologists, and business professionals committed to modernizing livestock management.</p>
            
            <div class="team-members">
                <div class="team-member">
                    <img src="ibrahim.jpg" alt="Team Member">
                    <h3>Ibrahim</h3>
                    <p>Founder & CEO</p>
                </div>
                
                <div class="team-member">
                    <img src="specialist.jpg" alt="Team Member">
                    <h3>ahmad ishaq</h3>
                    <p>Agricultural Specialist</p>
                </div>
                
                <div class="team-member">
                    <img src="iliyasu.jpg" alt="Team Member">
                    <h3>Iliyasu Abdurrazaq Iliyasu (YoungDev)</h3>
                    <p>Founder And Lead Developer</p>
                </div>
                
                <div class="team-member">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Team Member">
                    <h3></h3>
                    <p>Customer Support</p>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
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
    <script>
        // Hamburger Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const closeMenu = document.querySelector('.close-menu');
        const nav = document.querySelector('nav');

        menuToggle.addEventListener('click', () => {
            nav.style.display = "flex";
            menuToggle.style.display = "none";
            closeMenu.style.display = "block";
        });

        closeMenu.addEventListener('click', () => {
            nav.style.display = "none";
            menuToggle.style.display = "block";
            closeMenu.style.display = "none";
        });

               // Close menu on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                nav.style.display = "flex";
                menuToggle.style.display = "none";
                closeMenu.style.display = "none";
            } else {
                nav.style.display = "none";
                menuToggle.style.display = "block";
            }
        });
    </script>
</body>
</html>