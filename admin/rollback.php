<?php
session_start();
require_once '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    $sql = "SELECT * FROM transactions WHERE Transaction_id = '$transaction_id'";
    $result = $connection->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $amount = $row['Amount'];
        $receiver_account = $row['Receiver_Account_Name'];
        $check_accounts_sql = "SHOW TABLES LIKE 'accounts'";
        $check_accounts_result = $connection->query($check_accounts_sql);

        if ($check_accounts_result->num_rows == 1) {
            $update_balance_sql = "UPDATE accounts SET balance = balance + $amount WHERE account_number = '$receiver_account'";
            $update_balance_result = $connection->query($update_balance_sql);

            if ($update_balance_result) {
                $delete_transaction_sql = "DELETE FROM transactions WHERE Transaction_id = '$transaction_id'";
                $delete_transaction_result = $connection->query($delete_transaction_sql);

                if ($delete_transaction_result) {
                    $_SESSION['success_msg'] = "Transaction rolled back successfully.";
                } else {
                    $_SESSION['error_msg'] = "Failed to delete transaction record.";
                }
            } else {
                $_SESSION['error_msg'] = "Failed to update receiver's balance.";
            }
        } else {
            $_SESSION['error_msg'] = "Accounts table not found.";
        }
    } else {
        $_SESSION['error_msg'] = "Transaction not found.";
    }
    header("Location: transactionHistory.php");
    exit();
} else {
    // Redirect to the home page if accessed directly without proper POST request
    header("Location: index.php");
    exit();
}
?>
