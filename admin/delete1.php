<?php
session_start();
require_once '../connection.php';
$sql = "SELECT * FROM Account WHERE IsDeleted = 0";
$result = $connection->query($sql);

if (!$result) {
    trigger_error('Invalid query: ' . $connection->error);
}

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $accountIdToDelete = $_GET['id'];
    $update_query = "UPDATE Account SET IsDeleted = 1 WHERE Account_id = $accountIdToDelete";
    
    if ($connection->query($update_query) === TRUE) {
        echo "Record marked as deleted successfully";
        header("Location: manageAccounts.php");
        exit();
    } else {
        echo "Error marking record as deleted: " . $connection->error;
    }
}
?>
