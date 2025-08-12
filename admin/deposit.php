<?php
session_start();
require_once '../connection.php';
$err = [];
$msg = '';
$Tid = "TRN" . date('md') . rand(1000, 9999);
$current_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'] ?? '';
    $amount = $_POST['amount'] ?? '';

    // Validate amount
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $err['amount'] = 'Please enter a valid deposit amount.';
    }

    // Validate client ID
    if (empty($client_id)) {
        $err['client_id'] = 'Please enter a valid client ID.';
    }

    if (count($err) == 0) {
        // Escape inputs
        $client_id = mysqli_real_escape_string($connection, $client_id);
        $amount = mysqli_real_escape_string($connection, $amount);

        // Find the user
        $query = "SELECT * FROM users WHERE Client_id = '$client_id'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $current_balance = $user_data['Amount'];
            $Tuser_id = $user_data['user_id'];

            // Update user's balance
            $new_balance = $current_balance + $amount;
            $update_query = "UPDATE users SET Amount = '$new_balance' WHERE Client_id = '$client_id'";
            $update_result = mysqli_query($connection, $update_query);

            if ($update_result) {
                // Insert transaction record
                $insert_query = "INSERT INTO transactions (Transaction_id, Amount, Date, Remarks, Tuser_id) VALUES ('$Tid', '$amount', '$current_date', 'deposit', '$Tuser_id')";
                $insert_result = mysqli_query($connection, $insert_query);

                if ($insert_result) {
                    $msg = 'Deposit successful and transaction recorded.';
                } else {
                    $msg = 'Deposit successful, but error recording transaction.';
                }
            } else {
                $msg = 'Error occurred while processing deposit. Please try again.';
            }
        } else {
            $msg = 'User not found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Deposit</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
        <h2>Admin Deposit</h2>
        <?php echo $msg ? "<p class='msg'>$msg</p>" : ''; ?>
        <form action="" method="post">
            <label for="client_id">Client ID:</label>
            <input type="text" name="client_id" id="client_id" required>
            <?php echo isset($err['client_id']) ? $err['client_id'] : ''; ?>

            <label for="tid">Transaction Number:</label>
            <input type="text" name="tid" id="tid" value="<?php echo $Tid; ?>" readonly>

            <label for="date">Transaction Date:</label>
            <input type="date" name="date" id="date" value="<?php echo $current_date; ?>" required readonly>

            <label for="amount">Amount:</label>
            <input type="text" name="amount" id="amount" required>
            <?php echo isset($err['amount']) ? $err['amount'] : ''; ?>

            <input type="submit" value="Deposit">
        </form>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
</body>
</html>
