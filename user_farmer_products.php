<?php
// farmer_products.php
session_start();
include 'db.php';

// Check if farmer ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$farmer_id = (int)$_GET['id'];

// Get farmer information
$sql = "SELECT id, name, farm_name, address, phone FROM users WHERE id = $farmer_id AND role = 'farmer'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$farmer = $result->fetch_assoc();

// Get farmer's products
$products = [];
$sql = "SELECT * FROM products WHERE farmer_id = $farmer_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Handle order placement
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order']) && isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Validate quantity
    if ($quantity < 1) {
        $error = "Quantity must be at least 1";
    } else {
        // Verify product belongs to this farmer
        $sql = "SELECT id FROM products WHERE id = $product_id AND farmer_id = $farmer_id";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 1) {
            // Create the order
            $sql = "INSERT INTO orders (customer_id, farmer_id, product_id, quantity, status) 
                    VALUES ({$_SESSION['user_id']}, $farmer_id, $product_id, $quantity, 'pending')";
            
            if ($conn->query($sql)) {
                $success = "Order placed successfully!";
            } else {
                $error = "Error placing order: " . $conn->error;
            }
        } else {
            $error = "Invalid product selected";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($farmer['farm_name'] ?? $farmer['name']) ?> - Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        
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
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }
        
        .farmer-profile {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .farmer-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #eee;
            margin-right: 2rem;
            overflow: hidden;
        }
        
        .farmer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .farmer-info h1 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .farmer-info p {
            color: #666;
            margin-bottom: 0.3rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .product-details {
            padding: 1.5rem;
        }
        
        .product-details h3 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0.5rem 0;
        }
        
        .product-category {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background-color: #e0f7fa;
            color: #00838f;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        .order-form {
            margin-top: 1rem;
        }
        
        .order-form input[type="number"] {
            width: 60px;
            padding: 0.5rem;
            margin-right: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .order-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .order-btn:hover {
            background-color: #3e8e41;
        }
        
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #f44336;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        footer {
            background-color: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 2rem;
        }
        
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
            .farmer-profile {
                flex-direction: column;
                text-align: center;
            }
            
            .farmer-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .main-content {
                padding: 1rem;
            }

            nav {
                flex-direction: column;
            }
            
            .nav-links {
                margin-top: 1rem;
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
<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </h2>
        </div>
    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="customer_dashboard.php" class="active"><i class="fas fa-home"></i>Dashboard
                </a>
            </li>
            <li>
                <a href="customer_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            </li>
        </ul>
    </div>
</div>
    <div class="main-content">
        <div class="farmer-profile">
            <div class="farmer-image">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($farmer['name']); ?>&background=4CAF50&color=fff" alt="<?php echo htmlspecialchars($farmer['name']); ?>">
            </div>
            <div class="farmer-info">
                <h1><?php echo htmlspecialchars($farmer['farm_name'] ?? $farmer['name']); ?></h1>
                <p><?php echo htmlspecialchars($farmer['address']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($farmer['phone']); ?></p>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <h2>Available Products</h2>
        
        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <span>No Image Available</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer'): ?>
                        <form class="order-form" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" min="0" value="1" required>
                            <button type="submit" name="place_order" class="order-btn">Order Now</button>
                        </form>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <p><a href="login.php">Login</a> to place an order</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>This farmer currently has no products available for sale.</p>
        <?php endif; ?>
    </div>
</div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Livestock Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>