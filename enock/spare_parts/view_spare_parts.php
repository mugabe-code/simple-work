<?php
$page_title = "View Spare Parts";

include '../includes/db_connection.php';

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM spare_parts WHERE part_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = "Spare part deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting spare part: " . $conn->error;
        $message_type = "error";
    }
    $stmt->close();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM spare_parts WHERE name LIKE ? OR category LIKE ? ORDER BY name";
    $search_term = "%" . $search . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM spare_parts ORDER BY name";
    $result = $conn->query($sql);
}
?>

<div class="content-container">
    <div class="header-section">
        <h2>Spare Parts Inventory</h2>
        <a href="add_spare_part.php" class="btn btn-primary">Add New Spare Part</a>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="search-section">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-search">Search</button>
            <?php if ($search): ?>
                <a href="view_spare_parts.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    
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
                        <th>Actions</th>
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
                            <td>
                                <a href="edit_spare_part.php?id=<?php echo $row['part_id']; ?>" class="btn btn-small btn-warning">Edit</a>
                                <a href="view_spare_parts.php?delete_id=<?php echo $row['part_id']; ?>" 
                                   class="btn btn-small btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this spare part?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php
     
        $total_sql = "SELECT SUM(total_price) as grand_total FROM spare_parts";
        $total_result = $conn->query($total_sql);
        $total_row = $total_result->fetch_assoc();
        ?>
        <div class="summary-section">
            <h3>Inventory Summary</h3>
            <p><strong>Total Inventory Value:</strong> RWF <?php echo number_format($total_row['grand_total'], 2); ?></p>
            <p><strong>Total Items:</strong> <?php echo $result->num_rows; ?> spare parts</p>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>No spare parts found.</p>
            <?php if ($search): ?>
                <p><a href="view_spare_parts.php">View all spare parts</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>