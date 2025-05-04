<?php
// farmer_orders.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();
redirectIfNotAuthorized(['farmer']);

// Fetch orders for this farmer
$orders = [];
$sql = "SELECT o.id, p.name as product_name, u.name as customer_name, o.quantity, o.status, o.created_at 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.customer_id = u.id
        WHERE o.farmer_id = {$_SESSION['user_id']}
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Handle order status update
if (isset($_GET['update_status'])) {
    $order_id = sanitizeInput($_GET['order_id']);
    $new_status = sanitizeInput($_GET['new_status']);
    
    $sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id AND farmer_id = {$_SESSION['user_id']}";
    if ($conn->query($sql)) {
        header("Location: farmer_orders.php?updated=1");
        exit();
    }
}

// Check for success message
$success = '';
if (isset($_GET['updated'])) {
    $success = "Order status updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Livestock Management System</title>
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

/* Orders Section */
.orders-section {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 2rem;
}

.orders-section h2 {
    color: #4CAF50;
    margin-bottom: 1.5rem;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th, 
.orders-table td {
    padding: 0.8rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.orders-table th {
    background-color: #f5f5f5;
    font-weight: 500;
}

.status {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-pending {
    background-color: #fff3e0;
    color: #e65100;
}

.status-accepted {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-rejected {
    background-color: #ffebee;
    color: #c62828;
}

.status-completed {
    background-color: #e3f2fd;
    color: #1565c0;
}

.action-btn {
    padding: 0.3rem 0.6rem;
    width: 50%;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.8rem;
    margin-right: 0.5rem;
}

.accept-btn {
    background-color: #4CAF50;
    color: white;
}

.reject-btn {
    background-color: #f44336;
    color: white;
}

.complete-btn {
    background-color: #2196F3;
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
    .action-btn {
        display: inline;
    }
}

@media (max-width: 768px) {
    .orders-table {
        display: block;
        overflow-x: auto;
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
    
    .action-btn {
        display: block;
        margin-bottom: 0.5rem;
        width: 100%;
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
                <p><?php echo htmlspecialchars($_SESSION['farm_name'] ?? 'Farmer Dashboard'); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="farmer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="farmer_products.php"><i class="fas fa-egg"></i> My Products</a></li>
                    <li><a href="farmer_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="find_vets.php"><i class="fas fa-user-md"></i> Find Vets</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Customer Orders</h1>
                
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
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="orders-section">
                <h2>Recent Orders</h2>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>
                                    <span class="status status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <a href="farmer_orders.php?update_status=1&order_id=<?php echo $order['id']; ?>&new_status=accepted" class="action-btn accept-btn">Accept</a>
                                        <a href="farmer_orders.php?update_status=1&order_id=<?php echo $order['id']; ?>&new_status=rejected" class="action-btn reject-btn">Reject</a>
                                    <?php elseif ($order['status'] == 'accepted'): ?>
                                        <a href="farmer_orders.php?update_status=1&order_id=<?php echo $order['id']; ?>&new_status=completed" class="action-btn complete-btn">Complete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>