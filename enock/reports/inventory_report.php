<?php
$page_title = "Inventory Report";

include '../includes/db_connection.php';

$sql = "SELECT * FROM spare_parts ORDER BY category, name";
$result = $conn->query($sql);

$summary = [];


$summary_sql = "SELECT COUNT(*) as total_items, SUM(total_price) as total_value, 
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN quantity < 10 AND quantity > 0 THEN 1 ELSE 0 END) as low_stock
                FROM spare_parts";
$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();

$category_sql = "SELECT category, COUNT(*) as item_count, SUM(quantity) as total_quantity, SUM(total_price) as category_value 
                 FROM spare_parts 
                 GROUP BY category 
                 ORDER BY category";
$category_result = $conn->query($category_sql);
?>

<div class="content-container">
    <div class="header-section">
        <h2>Current Inventory Report</h2>
        <div class="report-actions">
            <button onclick="window.print()" class="btn btn-secondary">Print Report</button>
            <a href="summary_report.php" class="btn btn-info">Detailed Summary</a>
        </div>
    </div>
    
    <div class="report-summary">
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Items</h3>
                <div class="summary-value"><?php echo $summary['total_items']; ?></div>
            </div>
            <div class="summary-card">
                <h3>Total Value</h3>
                <div class="summary-value">RWF <?php echo number_format($summary['total_value'], 2); ?></div>
            </div>
            <div class="summary-card warning">
                <h3>Out of Stock</h3>
                <div class="summary-value"><?php echo $summary['out_of_stock']; ?></div>
            </div>
            <div class="summary-card caution">
                <h3>Low Stock</h3>
                <div class="summary-value"><?php echo $summary['low_stock']; ?></div>
            </div>
        </div>
    </div>
    
    <div class="category-summary">
        <h3>Category-wise Summary</h3>
        <div class="category-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Items</th>
                        <th>Total Quantity</th>
                        <th>Value (RWF)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cat_row = $category_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $cat_row['category']; ?></td>
                            <td><?php echo $cat_row['item_count']; ?></td>
                            <td><?php echo $cat_row['total_quantity']; ?></td>
                            <td><?php echo number_format($cat_row['category_value'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="inventory-details">
        <h3>Detailed Inventory List</h3>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price (RWF)</th>
                            <th>Total Price (RWF)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['part_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo number_format($row['unit_price'], 2); ?></td>
                                <td><?php echo number_format($row['total_price'], 2); ?></td>
                                <td>
                                    <?php 
                                    if ($row['quantity'] == 0) {
                                        echo "<span class='status out-of-stock'>Out of Stock</span>";
                                    } elseif ($row['quantity'] < 10) {
                                        echo "<span class='status low-stock'>Low Stock</span>";
                                    } else {
                                        echo "<span class='status in-stock'>In Stock</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No inventory data available.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="report-footer">
        <p><strong>Report Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>Generated By:</strong> <?php echo $_SESSION['names']; ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>