<?php
$page_title = "Add Spare Part";

include '../includes/db_connection.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    
  
    $total_price = $quantity * $unit_price;
    
    $sql = "INSERT INTO spare_parts (name, category, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssidd", $name, $category, $quantity, $unit_price, $total_price);
    
    if ($stmt->execute()) {
        $message = "Spare part added successfully!";
        $message_type = "success";

        $_POST = array();
    } else {
        $message = "Error adding spare part: " . $conn->error;
        $message_type = "error";
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Add New Spare Part</h2>
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Part Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="Engine Parts" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Engine Parts') ? 'selected' : ''; ?>>Engine Parts</option>
                <option value="Brake System" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Brake System') ? 'selected' : ''; ?>>Brake System</option>
                <option value="Electrical System" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Electrical System') ? 'selected' : ''; ?>>Electrical System</option>
                <option value="Suspension" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Suspension') ? 'selected' : ''; ?>>Suspension</option>
                <option value="Ignition System" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Ignition System') ? 'selected' : ''; ?>>Ignition System</option>
                <option value="Cooling System" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Cooling System') ? 'selected' : ''; ?>>Cooling System</option>
                <option value="Other" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="0" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="unit_price">Unit Price (RWF):</label>
            <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" value="<?php echo isset($_POST['unit_price']) ? $_POST['unit_price'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Total Price (RWF):</label>
            <input type="text" value="<?php echo isset($_POST['quantity']) && isset($_POST['unit_price']) ? number_format($_POST['quantity'] * $_POST['unit_price'], 2) : '0.00'; ?>" readonly>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Add Spare Part</button>
            <a href="view_spare_parts.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>