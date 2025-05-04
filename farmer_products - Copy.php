<?php
// farmer_products.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();
redirectIfNotAuthorized(['farmer']);

// Fetch farmer's products
$products = [];
$sql = "SELECT * FROM products WHERE farmer_id = {$_SESSION['user_id']} ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = sanitizeInput($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = $product_id AND farmer_id = {$_SESSION['user_id']}";
    if ($conn->query($sql)) {
        header("Location: farmer_products.php?deleted=1");
        exit();
    }
}

// Handle product addition/edit form submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = sanitizeInput($_POST['price']);
    $category = sanitizeInput($_POST['category']);
    
    if (isset($_POST['product_id'])) {
        // Edit existing product
        $product_id = sanitizeInput($_POST['product_id']);
        $sql = "UPDATE products SET 
                name = '$name',
                description = '$description',
                price = $price,
                category = '$category'
                WHERE id = $product_id AND farmer_id = {$_SESSION['user_id']}";
        
        if ($conn->query($sql)) {
            $success = "Product updated successfully!";
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    } else {
        // Add new product
        $sql = "INSERT INTO products (farmer_id, name, description, price, category) 
                VALUES ({$_SESSION['user_id']}, '$name', '$description', $price, '$category')";
        
        if ($conn->query($sql)) {
            $success = "Product added successfully!";
            header("Location: farmer_products.php?added=1");
            exit();
        } else {
            $error = "Error adding product: " . $conn->error;
        }
    }
}

// Check for success messages from redirects
if (isset($_GET['added'])) {
    $success = "Product added successfully!";
}
if (isset($_GET['deleted'])) {
    $success = "Product deleted successfully!";
}
if (isset($_GET['updated'])) {
    $success = "Product updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - Livestock Management System</title>
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
        
        /* Products Section */
        .products-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            color: #4CAF50;
        }
        
        .add-product-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .products-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            height: 150px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }
        
        .product-info {
            padding: 1rem;
        }
        
        .product-info h3 {
            margin-bottom: 0.5rem;
            color: #4CAF50;
        }
        
        .product-price {
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .edit-btn, .delete-btn {
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .edit-btn {
            background-color: #2196F3;
            color: white;
            border: none;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
        }
        
        /* Product Form Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            position: relative;
        }
        
        .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input, 
        .form-group textarea, 
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
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
            .products-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 1rem;
            }
            
            .products-list {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .add-product-btn {
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
                <h2><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                <p><?php echo htmlspecialchars($_SESSION['farm_name'] ?? 'Farmer Dashboard'); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="farmer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="farmer_products.php" class="active"><i class="fas fa-egg"></i> My Products</a></li>
                    <li><a href="farmer_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="find_vets.php"><i class="fas fa-user-md"></i> Find Vets</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>My Products</h1>
                
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
            
            <div class="products-section">
                <div class="section-header">
                    <h2>Product Listings</h2>
                    <button class="add-product-btn" onclick="">Add New Product</button>
                </div>
                
                <?php if (count($products) > 0): ?>
                    <div class="products-list">
                        <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</p>
                                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                                <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                                <div class="product-actions">
                                    <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', '<?php echo htmlspecialchars($product['description']); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['category']); ?>')">Edit</button>
                                    <button class="delete-btn" onclick="confirmDelete(<?php echo $product['id']; ?>)">Delete</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No products found. Add your first product to get started.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Product Form Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Product</h2>
            <form id="productForm" method="POST" action="farmer_products.php">
                <input type="hidden" id="product_id" name="product_id">
                
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Poultry">Poultry</option>
                        <option value="Meat">Meat</option>
                        <option value="Eggs">Eggs</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <button type="submit" class="submit-btn">Save Product</button>
            </form>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // add product 
        document.querySelector('.add-product-btn').addEventListener('click', ()=> {
            window.location.href="add_product.php";
        });
        // Modal functions
        function openModal() {

            document.getElementById('productModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        function editProduct(id, name, description, price, category) {
            document.getElementById('productModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('product_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('price').value = price;
            document.getElementById('category').value = category;
        }
        
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = 'farmer_products.php?delete=' + id;
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>