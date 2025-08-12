<?php
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accNumber'])) {
    $accNumber = trim($_POST['accNumber']);

    $stmt = $connection->prepare("SELECT Account_Number FROM BankAccounts WHERE Account_Number = ?");
    $stmt->bind_param("s", $accNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'available';
    }

    $stmt->close();
}
?>
