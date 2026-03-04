<?php
$page_title = "Edit Spare Part";

include '../includes/db_connection.php';

$message = "";
$message_type = "";

if (!isset($_GET['id'])) {
    header("Location: view_spare_parts.php");
    exit();
}

$part_id = $_GET['id'];

$sql = "SELECT * FROM spare_parts WHERE part_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $part_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_spare_parts.php");
    exit();
}

$spare_part = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    

    $total_price = $quantity * $unit_price;
    
    $sql = "UPDATE spare_parts SET name = ?, category = ?, quantity = ?, unit_price = ?, total_price = ? WHERE part_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddi", $name, $category, $quantity, $unit_price, $total_price, $part_id);
    
    if ($stmt->execute()) {
        $message = "Spare part updated successfully!";
        $message_type = "success";

        $stmt->close();
        $sql = "SELECT * FROM spare_parts WHERE part_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $spare_part = $result->fetch_assoc();
    } else {
        $message = "Error updating spare part: " . $conn->error;
        $message_type = "error";
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Edit Spare Part</h2>
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Part Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $spare_part['name']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="Engine Parts" <?php echo $spare_part['category'] == 'Engine Parts' ? 'selected' : ''; ?>>Engine Parts</option>
                <option value="Brake System" <?php echo $spare_part['category'] == 'Brake System' ? 'selected' : ''; ?>>Brake System</option>
                <option value="Electrical System" <?php echo $spare_part['category'] == 'Electrical System' ? 'selected' : ''; ?>>Electrical System</option>
                <option value="Suspension" <?php echo $spare_part['category'] == 'Suspension' ? 'selected' : ''; ?>>Suspension</option>
                <option value="Ignition System" <?php echo $spare_part['category'] == 'Ignition System' ? 'selected' : ''; ?>>Ignition System</option>
                <option value="Cooling System" <?php echo $spare_part['category'] == 'Cooling System' ? 'selected' : ''; ?>>Cooling System</option>
                <option value="Other" <?php echo $spare_part['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="0" value="<?php echo $spare_part['quantity']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="unit_price">Unit Price (RWF):</label>
            <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" value="<?php echo $spare_part['unit_price']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Total Price (RWF):</label>
            <input type="text" value="<?php echo number_format($spare_part['quantity'] * $spare_part['unit_price'], 2); ?>" readonly>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Update Spare Part</button>
            <a href="view_spare_parts.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>