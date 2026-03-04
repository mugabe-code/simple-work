<?php
$page_title = "Record Stock In";
include '../includes/db_connection.php';

$message = "";
$message_type = "";

$parts_sql = "SELECT part_id, name, unit_price FROM spare_parts ORDER BY name";
$parts_result = $conn->query($parts_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_id = $_POST['part_id'];
    $quantity = $_POST['quantity'];
    $stock_in_date = $_POST['stock_in_date'];
    
    $price_sql = "SELECT unit_price FROM spare_parts WHERE part_id = ?";
    $stmt = $conn->prepare($price_sql);
    $stmt->bind_param("i", $part_id);
    $stmt->execute();
    $price_result = $stmt->get_result();
    $part_data = $price_result->fetch_assoc();
    $unit_price = $part_data['unit_price'];
    $stmt->close();
    
    $total_price = $quantity * $unit_price;
    
    $sql = "INSERT INTO stock_in (part_id, quantity, stock_in_date, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $part_id, $quantity, $stock_in_date, $_SESSION['user_id']);
    
    if ($stmt->execute()) {

        $update_sql = "UPDATE spare_parts SET quantity = quantity + ?, total_price = (quantity + ?) * unit_price WHERE part_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $quantity, $quantity, $part_id);
        
        if ($update_stmt->execute()) {
            $message = "Stock in record added successfully!";
            $message_type = "success";
       
            $_POST = array();
        } else {
            $message = "Error updating spare parts quantity: " . $conn->error;
            $message_type = "error";
        }
        $update_stmt->close();
    } else {
        $message = "Error adding stock in record: " . $conn->error;
        $message_type = "error";
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Record Stock In</h2>
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="part_id">Select Spare Part:</label>
            <select id="part_id" name="part_id" required>
                <option value="">Choose a spare part</option>
                <?php while ($part = $parts_result->fetch_assoc()): ?>
                    <option value="<?php echo $part['part_id']; ?>" 
                            data-price="<?php echo $part['unit_price']; ?>"
                            <?php echo (isset($_POST['part_id']) && $_POST['part_id'] == $part['part_id']) ? 'selected' : ''; ?>>
                        <?php echo $part['name']; ?> (RWF <?php echo number_format($part['unit_price'], 2); ?> per unit)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="stock_in_date">Date:</label>
            <input type="date" id="stock_in_date" name="stock_in_date" 
                   value="<?php echo isset($_POST['stock_in_date']) ? $_POST['stock_in_date'] : date('Y-m-d'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Unit Price (RWF):</label>
            <input type="text" id="unit_price_display" readonly value="0.00">
        </div>
        
        <div class="form-group">
            <label>Total Price (RWF):</label>
            <input type="text" id="total_price" readonly value="0.00">
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Record Stock In</button>
            <a href="view_stock_in.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>