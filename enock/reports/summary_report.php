<?php
$page_title = "Summary Report";

include '../includes/db_connection.php';


$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');


$stock_in_sql = "SELECT COUNT(*) as total_in, SUM(quantity) as qty_in, SUM(quantity * unit_price) as value_in 
                 FROM stock_in si 
                 JOIN spare_parts sp ON si.part_id = sp.part_id 
                 WHERE stock_in_date BETWEEN ? AND ?";
$stock_in_stmt = $conn->prepare($stock_in_sql);
$stock_in_stmt->bind_param("ss", $from_date, $to_date);
$stock_in_stmt->execute();
$stock_in_result = $stock_in_stmt->get_result();
$stock_in_data = $stock_in_result->fetch_assoc();

$stock_out_sql = "SELECT COUNT(*) as total_out, SUM(quantity) as qty_out, SUM(stock_out_total_price) as value_out 
                  FROM stock_out 
                  WHERE stock_out_date BETWEEN ? AND ?";
$stock_out_stmt = $conn->prepare($stock_out_sql);
$stock_out_stmt->bind_param("ss", $from_date, $to_date);
$stock_out_stmt->execute();
$stock_out_result = $stock_out_stmt->get_result();
$stock_out_data = $stock_out_stmt->get_result()->fetch_assoc();


$top_items_sql = "SELECT sp.name, 
                  COALESCE(SUM(si.quantity), 0) as total_in, 
                  COALESCE(SUM(so.quantity), 0) as total_out,
                  (COALESCE(SUM(si.quantity), 0) - COALESCE(SUM(so.quantity), 0)) as net_movement
                  FROM spare_parts sp
                  LEFT JOIN stock_in si ON sp.part_id = si.part_id AND si.stock_in_date BETWEEN ? AND ?
                  LEFT JOIN stock_out so ON sp.part_id = so.part_id AND so.stock_out_date BETWEEN ? AND ?
                  GROUP BY sp.part_id, sp.name
                  ORDER BY ABS(COALESCE(SUM(si.quantity), 0) - COALESCE(SUM(so.quantity), 0)) DESC
                  LIMIT 10";
$top_items_stmt = $conn->prepare($top_items_sql);
$top_items_stmt->bind_param("ssss", $from_date, $to_date, $from_date, $to_date);
$top_items_stmt->execute();
$top_items_result = $top_items_stmt->get_result();
?>

<div class="content-container">
    <div class="header-section">
        <h2>Summary Report</h2>
        <button onclick="window.print()" class="btn btn-secondary">Print Report</button>
    </div>
    
    <div class="date-filter">
        <form method="GET" action="">
            <div class="form-group-inline">
                <label for="from_date">From Date:</label>
                <input type="date" id="from_date" name="from_date" value="<?php echo $from_date; ?>" required>
            </div>
            <div class="form-group-inline">
                <label for="to_date">To Date:</label>
                <input type="date" id="to_date" name="to_date" value="<?php echo $to_date; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="summary_report.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>
    
    <div class="period-summary">
        <h3>Period Summary (<?php echo date('M j, Y', strtotime($from_date)); ?> to <?php echo date('M j, Y', strtotime($to_date)); ?>)</h3>
        <div class="summary-grid">
            <div class="summary-box">
                <h4>Stock In</h4>
                <p><strong>Records:</strong> <?php echo $stock_in_data['total_in'] ?: 0; ?></p>
                <p><strong>Quantity:</strong> <?php echo $stock_in_data['qty_in'] ?: 0; ?></p>
                <p><strong>Value:</strong> RWF <?php echo number_format($stock_in_data['value_in'] ?: 0, 2); ?></p>
            </div>
            
            <div class="summary-box">
                <h4>Stock Out</h4>
                <p><strong>Records:</strong> <?php echo $stock_out_data['total_out'] ?: 0; ?></p>
                <p><strong>Quantity:</strong> <?php echo $stock_out_data['qty_out'] ?: 0; ?></p>
                <p><strong>Value:</strong> RWF <?php echo number_format($stock_out_data['value_out'] ?: 0, 2); ?></p>
            </div>
            
            <div class="summary-box net">
                <h4>Net Movement</h4>
                <p><strong>Quantity:</strong> <?php echo ($stock_in_data['qty_in'] ?: 0) - ($stock_out_data['qty_out'] ?: 0); ?></p>
                <p><strong>Value:</strong> RWF <?php echo number_format(($stock_in_data['value_in'] ?: 0) - ($stock_out_data['value_out'] ?: 0), 2); ?></p>
            </div>
        </div>
    </div>
    
    <div class="top-items">
        <h3>Top Moving Items</h3>
        <?php if ($top_items_result->num_rows > 0): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Stock In</th>
                            <th>Stock Out</th>
                            <th>Net Movement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $top_items_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['total_in']; ?></td>
                                <td><?php echo $item['total_out']; ?></td>
                                <td><?php echo $item['net_movement'] > 0 ? '+' : ''; ?><?php echo $item['net_movement']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No movement data available for the selected period.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="report-footer">
        <p><strong>Report Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>Generated By:</strong> <?php echo $_SESSION['names']; ?></p>
        <p><strong>Period:</strong> <?php echo $from_date; ?> to <?php echo $to_date; ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>