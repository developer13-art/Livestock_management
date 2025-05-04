<?php
// admin_dashboard.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();
redirectIfNotAuthorized(['admin']);

// Fetch all users
$users = [];
$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Count users by role
$userCounts = [
    'customer' => 0,
    'farmer' => 0,
    'vet' => 0,
    'admin' => 0
];
$sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $userCounts[$row['role']] = $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Livestock Management System</title>
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
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .count-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 1.5rem;
            text-align: center;
        }
        
        .count-card h3 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .count-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .users-table th, 
        .users-table td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .users-table th {
            background-color: #f5f5f5;
            font-weight: 500;
        }
        
        .role {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .role-customer {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .role-farmer {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .role-vet {
            background-color: #e0f7fa;
            color: #00838f;
        }
        
        .role-admin {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }
        
        .action-btn {
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
        }
        
        .edit-btn {
            background-color: #2196F3;
            color: white;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
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
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .profile-dropdown {
                margin-top: 1rem;
            }
            
            .users-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
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
                    <li><a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="system_settings.php"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
                
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
            
            <div class="dashboard-cards">
                <div class="count-card">
                    <h3>Customers</h3>
                    <div class="number"><?php echo $userCounts['customer']; ?></div>
                </div>
                
                <div class="count-card">
                    <h3>Farmers</h3>
                    <div class="number"><?php echo $userCounts['farmer']; ?></div>
                </div>
                
                <div class="count-card">
                    <h3>Veterinarians</h3>
                    <div class="number"><?php echo $userCounts['vet']; ?></div>
                </div>
                
                <div class="count-card">
                    <h3>Admins</h3>
                    <div class="number"><?php echo $userCounts['admin']; ?></div>
                </div>
            </div>
            
            <div class="card">
                <h3>Recent Users</h3>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="action-btn edit-btn">Edit</button>
                                <button class="action-btn delete-btn">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // Simple script to handle user actions
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const userName = row.querySelector('td:first-child').textContent;
                alert('Edit user: ' + userName);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this user?')) {
                    const row = this.closest('tr');
                    row.remove();
                    alert('User deleted successfully');
                }
            });
        });
    </script>
</body>
</html>