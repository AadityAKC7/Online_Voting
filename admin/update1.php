<?php
session_start();
error_reporting(0);
require_once '../connection.php';
$msg = '';
$row = null;
if(isset($_POST['submit'])) {
    $account_id = $_POST['account_id'];
    $account_type = $_POST['account_type'];
    $rate = $_POST['rate'];

    $update_query = "UPDATE Account SET 
                    Account_type = '$account_type',
                    Rate = '$rate'
                    WHERE Account_id = $account_id";
    
    if ($connection->query($update_query) === TRUE) {
        $msg = "Record updated successfully";
    } else {
        $msg = "Error updating record: " . $connection->error;
    }
} elseif (isset($_GET['id'])) {
    $account_id = $_GET['id'];

    // Validate and escape the account_id
    if (filter_var($account_id, FILTER_VALIDATE_INT) !== false) {
        $account_id = mysqli_real_escape_string($connection, $account_id);

        // Fetch the account details
        $sql = "SELECT * FROM Account WHERE Account_id = $account_id";
        $result = $connection->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            $msg = "Account not found.";
        }
    } else {
        $msg = "Invalid account ID.";
    }
} else {
    $msg = "No account ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
        <div class="transactions">
            <h1>Update Account</h1>
            <?php echo $msg; ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="account_id" value="<?php echo $row['Account_id']; ?>">
                <label for="account_type">Account Type:</label>
                <input type="text" name="account_type" value="<?php echo $row['Account_type']; ?>">
                <label for="rate">Rate:</label>
                <input type="text" name="rate" value="<?php echo $row['Rate']; ?>"><br>
                <input type="submit" name="submit" value="Update">
            </form>
        </div>
    </div>
</body>
</html>
