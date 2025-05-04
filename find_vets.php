<?php
// find_vets.php
session_start();
include 'db.php';
redirectIfNotLoggedIn();
redirectIfNotAuthorized(['farmer']);

// Fetch all veterinarians
$vets = [];
$sql = "SELECT id, name, address, phone FROM users WHERE role = 'vet'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $vets[] = $row;
    }
}

// Handle vet request submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vet_id = sanitizeInput($_POST['vet_id']);
    $problem_description = sanitizeInput($_POST['problem_description']);
    
    $sql = "INSERT INTO vet_requests (farmer_id, vet_id, problem_description) 
            VALUES ({$_SESSION['user_id']}, $vet_id, '$problem_description')";
    
    if ($conn->query($sql)) {
        $success = "Request sent to veterinarian successfully!";
    } else {
        $error = "Error sending request: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Veterinarians - Livestock Management System</title>
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
        
        /* Vets Section */
        .vets-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .vets-section h2 {
            color: #4CAF50;
            margin-bottom: 1.5rem;
        }
        
        .vets-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .vet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            transition: transform 0.3s;
        }
        
        .vet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .vet-card h3 {
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }
        
        .vet-card p {
            margin-bottom: 0.5rem;
            color: #666;
        }
        
        .vet-card .contact {
            margin-top: 1rem;
            font-weight: 500;
        }
        
        .request-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
        }
        
        /* Request Form Modal */
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
        
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 150px;
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
            .vets-list {
                grid-template-columns: 1fr;
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
                <p><?php echo htmlspecialchars($_SESSION['farm_name'] ?? 'Farmer Dashboard'); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="farmer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="farmer_products.php"><i class="fas fa-egg"></i> My Products</a></li>
                    <li><a href="farmer_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="find_vets.php" class="active"><i class="fas fa-user-md"></i> Find Vets</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Find Veterinarians</h1>
                
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
            
            <div class="vets-section">
                <h2>Available Veterinarians</h2>
                
                <?php if (count($vets) > 0): ?>
                    <div class="vets-list">
                        <?php foreach ($vets as $vet): ?>
                        <div class="vet-card">
                            <h3>Dr. <?php echo htmlspecialchars($vet['name']); ?></h3>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($vet['address']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($vet['phone']); ?></p>
                            <button class="request-btn" onclick="openRequestModal(<?php echo $vet['id']; ?>, 'Dr. <?php echo htmlspecialchars($vet['name']); ?>')">Request Consultation</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No veterinarians found in the system.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Request Form Modal -->
    <div class="modal" id="requestModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="vetName">Request Consultation</h2>
            <form id="requestForm" method="POST" action="find_vets.php">
                <input type="hidden" id="vet_id" name="vet_id">
                
                <div class="form-group">
                    <label for="problem_description">Describe the problem</label>
                    <textarea id="problem_description" name="problem_description" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Send Request</button>
            </form>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // Modal functions
        function openRequestModal(vetId, vetName) {
            document.getElementById('requestModal').style.display = 'flex';
            document.getElementById('vetName').textContent = 'Request Consultation with ' + vetName;
            document.getElementById('vet_id').value = vetId;
            document.getElementById('requestForm').reset();
        }
        
        function closeModal() {
            document.getElementById('requestModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('requestModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>