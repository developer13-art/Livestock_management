<?php
// profile.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();

// Fetch user data
$user = [];
$sql = "SELECT * FROM users WHERE id = {$_SESSION['user_id']}";
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        $farm_name = isset($_POST['farm_name']) ? sanitizeInput($_POST['farm_name']) : null;
        
        $sql = "UPDATE users SET 
                name = '$name',
                email = '$email',
                phone = '$phone',
                address = '$address',
                farm_name = " . ($farm_name ? "'$farm_name'" : "NULL") . "
                WHERE id = {$_SESSION['user_id']}";
        
        if ($conn->query($sql)) {
            $_SESSION['name'] = $name;
            $success = "Profile updated successfully!";
            // Refresh user data
            $result = $conn->query("SELECT * FROM users WHERE id = {$_SESSION['user_id']}");
            $user = $result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = sanitizeInput($_POST['current_password']);
        $new_password = sanitizeInput($_POST['new_password']);
        $confirm_password = sanitizeInput($_POST['confirm_password']);
        
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = '$hashed_password' WHERE id = {$_SESSION['user_id']}";
            
            if ($conn->query($sql)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password: " . $conn->error;
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
    <title>My Profile - Livestock Management System</title>
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
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #4CAF50;
            color: white;
            padding: 1.5rem 0;
            position: fixed;
            height: 100%;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-header p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }
        
        .sidebar-menu {
            padding: 1.5rem 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 0.8rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar-menu i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .header h1 {
            color: #333;
        }
        
        /* Profile Section */
        .profile-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-section h2 {
            color: #4CAF50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 2rem;
            overflow: hidden;
        }
        
        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-details h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .profile-details p {
            color: #666;
        }
        
        .role-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background-color: #e0f7fa;
            color: #00838f;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }
        
        .farmer-role {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .customer-role {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .vet-role {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }
        
        .admin-role {
            background-color: #fff3e0;
            color: #e65100;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .form-row {
            display: flex;
            gap: 1.5rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #3e8e41;
        }
        
        /* Error and Success Messages */
        .error-message {
            color: #f44336;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background-color: #ffebee;
            border-radius: 4px;
        }
        
        .success-message {
            color: #2e7d32;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background-color: #e8f5e9;
            border-radius: 4px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: static;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        @media (max-width: 768px) {
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-picture {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                <p><?php echo ucfirst($_SESSION['role']); ?> Dashboard</p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <?php if ($_SESSION['role'] == 'customer'): ?>
                        <li><a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'farmer'): ?>
                        <li><a href="farmer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'vet'): ?>
                        <li><a href="vet_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>My Profile</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="profile-section">
                <div class="profile-info">
                    <div class="profile-picture">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=4CAF50&color=fff" alt="Profile Picture">
                    </div>
                    <div class="profile-details">
                        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="role-badge <?php echo $user['role']; ?>-role">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                </div>
                
                <h2>Personal Information</h2>
                <form action="profile.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                        </div>
                    </div>
                    
                    <?php if ($user['role'] == 'farmer'): ?>
                        <div class="form-group">
                            <label for="farm_name">Farm Name</label>
                            <input type="text" id="farm_name" name="farm_name" value="<?php echo htmlspecialchars($user['farm_name']); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" name="update_profile" class="submit-btn">Update Profile</button>
                </form>
            </div>
            
            <div class="profile-section">
                <h2>Change Password</h2>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="submit-btn">Change Password</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>