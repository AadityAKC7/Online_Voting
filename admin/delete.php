<?php
session_start();
require_once '../connection.php';
// Check if user_id parameter is provided and is numeric
if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $user_id = $_GET['user_id'];
    $sql = "UPDATE users SET status = 'inactive' WHERE user_id = $user_id";
    if ($connection->query($sql) === TRUE) {
        header('Location: clients.php');
        exit;
    } else {
        echo "Error deleting record: " . $connection->error;
    }
} else {
    echo "Invalid user ID";
}
$connection->close();
?>
