<?php
$page_title = "Dashboard";
include 'includes/db_connection.php';

$total_parts = 0;
$total_value = 0;
$recent_stock_in = 0;
$recent_stock_out = 0;
$low_stock = 0;

$result = $conn->query("SELECT COUNT(*) as count FROM spare_parts");
if ($result) {
    $row = $result->fetch_assoc();
    $total_parts = $row['count'];
}

$result = $conn->query("SELECT SUM(total_price) as total FROM spare_parts");
if ($result) {
    $row = $result->fetch_assoc();
    $total_value = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM stock_in WHERE stock_in_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
if ($result) {
    $row = $result->fetch_assoc();
    $recent_stock_in = $row['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM stock_out WHERE stock_out_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
if ($result) {
    $row = $result->fetch_assoc();
    $recent_stock_out = $row['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM spare_parts WHERE quantity < 10");
if ($result) {
    $row = $result->fetch_assoc();
    $low_stock = $row['count'];
}
?>

<div class="dashboard">
    <h2>Welcome, <?php echo $_SESSION['names']; ?>!</h2>
    <p>SmartPark Stock Inventory Management System</p>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Spare Parts</h3>
            <div class="stat-number"><?php echo $total_parts; ?></div>
            <p>Items in inventory</p>
        </div>
        
        <div class="stat-card">
            <h3>Total Stock Value</h3>
            <div class="stat-number">RWF <?php echo number_format($total_value, 2); ?></div>
            <p>Current inventory value</p>
        </div>
        
        <div class="stat-card">
            <h3>Recent Stock In</h3>
            <div class="stat-number"><?php echo $recent_stock_in; ?></div>
            <p>Last 7 days</p>
        </div>
        
        <div class="stat-card">
            <h3>Recent Stock Out</h3>
            <div class="stat-number"><?php echo $recent_stock_out; ?></div>
            <p>Last 7 days</p>
        </div>
        
        <div class="stat-card warning">
            <h3>Low Stock Alert</h3>
            <div class="stat-number"><?php echo $low_stock; ?></div>
            <p>Items below 10 units</p>
        </div>
    </div>
    
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="spare_parts/add_spare_part.php" class="btn btn-primary">Add New Spare Part</a>
            <a href="stock_in/add_stock_in.php" class="btn btn-success">Record Stock In</a>
            <a href="stock_out/add_stock_out.php" class="btn btn-warning">Record Stock Out</a>
            <a href="reports/inventory_report.php" class="btn btn-info">View Reports</a>
        </div>
    </div>
    
    <div class="recent-activity">
        <h3>Recent Activity</h3>
        <div class="activity-grid">
            <div class="activity-section">
                <h4>Recent Stock In</h4>
                <?php
                $result = $conn->query("SELECT si.*, sp.name, u.names as user_name 
                                      FROM stock_in si 
                                      JOIN spare_parts sp ON si.part_id = sp.part_id 
                                      JOIN users u ON si.user_id = u.user_id 
                                      ORDER BY si.stock_in_date DESC LIMIT 5");
                if ($result && $result->num_rows > 0) {
                    echo "<table class='activity-table'>";
                    echo "<tr><th>Part</th><th>Quantity</th><th>Date</th><th>User</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['stock_in_date'] . "</td>";
                        echo "<td>" . $row['user_name'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No recent stock in records</p>";
                }
                ?>
            </div>
            
            <div class="activity-section">
                <h4>Recent Stock Out</h4>
                <?php
                $result = $conn->query("SELECT so.*, sp.name, u.names as user_name 
                                      FROM stock_out so 
                                      JOIN spare_parts sp ON so.part_id = sp.part_id 
                                      JOIN users u ON so.user_id = u.user_id 
                                      ORDER BY so.stock_out_date DESC LIMIT 5");
                if ($result && $result->num_rows > 0) {
                    echo "<table class='activity-table'>";
                    echo "<tr><th>Part</th><th>Quantity</th><th>Date</th><th>User</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['stock_out_date'] . "</td>";
                        echo "<td>" . $row['user_name'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No recent stock out records</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>