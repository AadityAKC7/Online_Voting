<?php
require_once '../connection.php';

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $updateSql = "UPDATE users SET status = 1 WHERE user_id = ?";
    $stmt = $connection->prepare($updateSql);
    $stmt->bind_param('i', $userId);
    if ($stmt->execute()) {
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error response
        echo json_encode(['success' => false]);
    }
} else {
    // Handle invalid request
    echo json_encode(['success' => false]);
}
?>
