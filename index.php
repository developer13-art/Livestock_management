<?php
// index.php
session_start();
include 'db.php';

// Fetch featured farmers and vets
$farmers = [];
$vets = [];
$sql = "SELECT id, name, role, farm_name FROM users WHERE role IN ('farmer', 'vet') LIMIT 6";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['role'] == 'farmer') {
            $farmers[] = $row;
        } else {
            $vets[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Management System</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header & Navigation */
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
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('hero_section.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .cta-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .cta-button:hover {
            background-color: #3e8e41;
        }
        
        /* Featured Section */
        .featured {
            padding: 3rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #333;
        }
        
        .profiles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .profile-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .profile-image {
            height: 200px;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
        }
        
        .profile-info {
            padding: 1.5rem;
        }
        
        .profile-info h3 {
            margin-bottom: 0.5rem;
            color: #4CAF50;
        }
        
        .profile-info p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .profile-role {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background-color: #e0f7fa;
            color: #00838f;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
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
        
        .farmer-role {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .vet-role {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        /* Footer */
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
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .profiles {
                grid-template-columns: repeat(2, 1fr);
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
    
    <section class="hero">
        <div class="container">
            <h1>Livestock Management System</h1>
            <p>Connect farmers, customers, and veterinarians in one platform to streamline livestock management and trading.</p>
            <a href="signup.php" class="cta-button">Get Started</a>
        </div>
    </section>
    <section>
        <div class="about-container">
            <div class="about-header">
                <h1>About Livestock Management System</h1>
                <p>Connecting farmers, customers, and veterinarians for a more efficient agricultural ecosystem</p>
            </div>
            <div class="about-section">
                <h2>Our Mission</h2>
                <p>
                    At Livestock Management System, we're dedicated to revolutionizing the way livestock is managed and traded. Our platform    bridges 
                    the gap between farmers who raise healthy animals, customers who want quality products, and veterinarians who ensure animal welfare.
                </p>
                <p>We believe in creating a transparent, efficient, and sustainable agricultural ecosystem that benefits all stakeholders.</p>
            </div>
            <div class="about-section">
                <h2>What We Offer</h2>
                <p>
                    <strong>For Farmers:</strong> A platform to showcase your livestock, manage inventory, connect with customers, and access 
                    veterinary services when needed.
                </p>
                <p>
                    <strong>For Customers:</strong> Direct access to high-quality livestock products from trusted farmers in your area, with 
                    transparent pricing and ordering systems.
                </p>
                <p>
                    <strong>For Veterinarians:</strong> Opportunities to connect with farmers who need your expertise, manage consultations, and 
                    grow your practice.
                </p>
</div>
        
        </div>
    </section>
    <section class="featured">
        <div class="container">
            <h2 class="section-title">Featured Farmers</h2>
            <div class="profiles">
                <?php foreach ($farmers as $farmer): ?>
                <div class="profile-card">
                    <div class="profile-image" style="background-image: url();"></div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($farmer['name']); ?></h3>
                        <p><?php echo htmlspecialchars($farmer['farm_name'] ?? 'Farm Name'); ?></p>
                        <span class="profile-role farmer-role">Farmer</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <h2 class="section-title" style="margin-top: 3rem;">Featured Veterinarians</h2>
            <div class="profiles">
                <?php foreach ($vets as $vet): ?>
                <div class="profile-card">
                    <div class="profile-image" style="background-image: url('https://source.unsplash.com/random/400x300/?veterinarian');"></div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($vet['name']); ?></h3>
                        <p>Professional Veterinarian</p>
                        <span class="profile-role vet-role">Veterinarian</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
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
</body>
</html>