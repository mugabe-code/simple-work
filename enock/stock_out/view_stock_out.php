<?php
$page_title = "View Stock Out Records";
include '../includes/db_connection.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Get the stock out record to reverse the quantity change
    $get_sql = "SELECT part_id, quantity FROM stock_out WHERE stock_out_id = ?";
    $stmt = $conn->prepare($get_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        $part_id = $record['part_id'];
        $quantity = $record['quantity'];
        
        // Delete the stock out record
        $delete_sql = "DELETE FROM stock_out WHERE stock_out_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            // Reverse the quantity change in spare parts
            $update_sql = "UPDATE spare_parts SET quantity = quantity + ?, total_price = quantity * unit_price WHERE part_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $quantity, $part_id);
            
            if ($update_stmt->execute()) {
                $message = "Stock out record deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating spare parts quantity: " . $conn->error;
                $message_type = "error";
            }
            $update_stmt->close();
        } else {
            $message = "Error deleting stock out record: " . $conn->error;
            $message_type = "error";
        }
    }
    $stmt->close();
}

// Get stock out records with related data
$sql = "SELECT so.*, sp.name as part_name, u.names as user_name 
        FROM stock_out so 
        JOIN spare_parts sp ON so.part_id = sp.part_id 
        JOIN users u ON so.user_id = u.user_id 
        ORDER BY so.stock_out_date DESC, so.stock_out_id DESC";
$result = $conn->query($sql);
?>

<div class="content-container">
    <div class="header-section">
        <h2>Stock Out Records</h2>
        <a href="add_stock_out.php" class="btn btn-primary">Record New Stock Out</a>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Spare Part</th>
                        <th>Quantity</th>
                        <th>Unit Price (RWF)</th>
                        <th>Total Price (RWF)</th>
                        <th>Date</th>
                        <th>Recorded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['stock_out_id']; ?></td>
                            <td><?php echo $row['part_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo number_format($row['stock_out_unit_price'], 2); ?></td>
                            <td><?php echo number_format($row['stock_out_total_price'], 2); ?></td>
                            <td><?php echo $row['stock_out_date']; ?></td>
                            <td><?php echo $row['user_name']; ?></td>
                            <td>
                                <a href="view_stock_out.php?delete_id=<?php echo $row['stock_out_id']; ?>" 
                                   class="btn btn-small btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this stock out record? This will increase the inventory quantity.')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php
        // Display summary statistics
        $total_sql = "SELECT COUNT(*) as total_records, SUM(stock_out_total_price) as total_value 
                     FROM stock_out";
        $total_result = $conn->query($total_sql);
        $total_row = $total_result->fetch_assoc();
        ?>
        <div class="summary-section">
            <h3>Stock Out Summary</h3>
            <p><strong>Total Records:</strong> <?php echo $total_row['total_records']; ?></p>
            <p><strong>Total Value Removed:</strong> RWF <?php echo number_format($total_row['total_value'], 2); ?></p>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>No stock out records found.</p>
            <p><a href="add_stock_out.php">Record your first stock out</a></p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>