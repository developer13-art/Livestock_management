<?php
// system_settings.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();
redirectIfNotAuthorized(['admin']);

// Handle settings update
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // In a real application, you would save these settings to a database
    $success = "System settings updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Livestock Management System</title>
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

.profile-dropdown {
    position: relative;
}

.profile-btn {
    display: flex;
    align-items: center;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.profile-btn img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.profile-dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
    z-index: 1;
}

.profile-dropdown-content a {
    display: block;
    padding: 0.8rem 1rem;
    color: #333;
    text-decoration: none;
}

.profile-dropdown-content a:hover {
    background-color: #f5f5f5;
}

.profile-dropdown:hover .profile-dropdown-content {
    display: block;
}

/* Settings Section */
.settings-section {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.settings-section h2 {
    color: #4CAF50;
    margin-bottom: 1.5rem;
}

.settings-form .form-group {
    margin-bottom: 1.5rem;
}

.settings-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.settings-form input,
.settings-form select,
.settings-form textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.settings-form textarea {
    min-height: 100px;
    resize: vertical;
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
    cursor: pointer;
    font-size: 1rem;
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
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .profile-dropdown {
        margin-top: 1rem;
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
                <p>Admin Dashboard</p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="system_settings.php" class="active"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>System Settings</h1>
                
                <div class="profile-dropdown">
                    <div class="profile-btn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=4CAF50&color=fff" alt="Profile">
                        <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    </div>
                    <div class="profile-dropdown-content">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="settings-section">
                <h2>General Settings</h2>
                <form class="settings-form" method="POST" action="system_settings.php">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="Livestock Management System">
                    </div>
                    
                    <div class="form-group">
                        <label for="site_email">Admin Email</label>
                        <input type="email" id="site_email" name="site_email" value="admin@livestock.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="maintenance_mode">Maintenance Mode</label>
                        <select id="maintenance_mode" name="maintenance_mode">
                            <option value="0">Disabled</option>
                            <option value="1">Enabled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn">Save Settings</button>
                </form>
            </div>
            
            <div class="settings-section" style="margin-top: 2rem;">
                <h2>Payment Settings</h2>
                <form class="settings-form" method="POST" action="system_settings.php">
                    <div class="form-group">
                        <label for="payment_method">Default Payment Method</label>
                        <select id="payment_method" name="payment_method">
                            <option value="cash">Cash on Delivery</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn">Save Payment Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>