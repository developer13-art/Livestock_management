<?php
// signup.php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = sanitizeInput($_POST['role']);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $farm_name = $role == 'farmer' ? sanitizeInput($_POST['farm_name']) : null;
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (role, name, email, phone, address, password, farm_name) 
                    VALUES ('$role', '$name', '$email', '$phone', '$address', '$hashed_password', " . ($farm_name ? "'$farm_name'" : "NULL") . ")";
            
            if ($conn->query($sql)) {
                // Automatically log in the user
                $user_id = $conn->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;
                $_SESSION['name'] = $name;
                
                // Redirect to appropriate dashboard
                header("Location: {$role}_dashboard.php");
                exit();
            } else {
                $error = "Error creating account: " . $conn->error;
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
    <title>Sign Up - Livestock Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .signup-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .signup-header h1 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .signup-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .farm-name-group {
            display: none;
        }
        
        .signup-button {
            width: 100%;
            padding: 0.8rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .signup-button:hover {
            background-color: #3e8e41;
        }
        
        .signup-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        
        .signup-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        .signup-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #f44336;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            .signup-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .signup-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h1>Create Account</h1>
            <p>Join our livestock management community</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="signup.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="role">I am a</label>
                <select id="role" name="role" required>
                    <option value="">Select your role</option>
                    <option value="customer">Customer</option>
                    <option value="farmer">Farmer</option>
                    <option value="vet">Veterinarian</option>
                </select>
            </div>
            
            <div class="form-group farm-name-group" id="farmNameGroup">
                <label for="farm_name">Farm Name</label>
                <input type="text" id="farm_name" name="farm_name">
            </div>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="address">Full Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="signup-button">Sign Up</button>
        </form>
        
        <div class="signup-footer">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
    
    <script>
        // Show/hide farm name field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            const farmNameGroup = document.getElementById('farmNameGroup');
            if (this.value === 'farmer') {
                farmNameGroup.style.display = 'block';
                document.getElementById('farm_name').required = true;
            } else {
                farmNameGroup.style.display = 'none';
                document.getElementById('farm_name').required = false;
            }
        });
        
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>