<?php
echo "PHP is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";

include 'includes/db_connection.php';
echo "Database connection successful!<br>";

$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "Number of users in database: " . $row['count'] . "<br>";

$conn->close();
?>