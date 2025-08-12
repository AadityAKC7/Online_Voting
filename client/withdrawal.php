<?php
session_start();
require_once '../connection.php';
$err = [];
$msg = '';
$user_id = $_SESSION['admin_id'];
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $account_amount = $user_data['Amount'];
}

$amount = $receiverName = $phone = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    if (isset($_POST['amount']) && !empty($_POST['amount'])) { 
        $amount = $_POST['amount'];  
        if (!is_numeric($amount) || $amount <= 0) {
            $err['amount'] = 'Invalid amount format! Please enter a valid positive numeric value.';
        }
    } else {
        $err['amount'] = 'Enter the required amount.';
    }
    

    if ($_POST['user'] == 'other') {
        if (empty($_POST['receiverName']) || !preg_match("/^([A-Z][a-z\s]+)+$/", trim($_POST['receiverName']))) {
            $err['receiverName'] = 'Enter a valid name.';
        } else{
            $receiverName = trim($_POST['receiverName']);
        }

        if (empty($_POST['phone']) || !preg_match("/^\d{10}$/", $_POST['phone'])) {
            $err['phone'] = 'Invalid number! Please enter a valid 10 digit number.';
        } else{
            $phone = $_POST['phone'];
        }
    }

    if(count($err) == 0){
        $user_id = $_SESSION['admin_id'];
        $current_date = date('Y-m-d');
        $Tid = "TRN" . date('md') . rand(100, 999);
        // Check if withdrawal amount is less than or equal to account balance
        if($amount <= $account_amount) {
            $sql = "INSERT INTO transactions (Transaction_id, Receiver_Bank_Name, Receiver_Bank_Number, Receiver_Account_Name, Phone, Amount, Date, Remarks, Tuser_id)
                VALUES ('$Tid', '', '', '$receiverName', '$phone', $amount, '$current_date', 'Withdrawal', $user_id)";
            mysqli_query($connection, $sql);

            $update_query = "UPDATE users SET Amount = Amount - $amount WHERE user_id = $user_id";
            mysqli_query($connection, $update_query);
            $msg = 'Withdrawal successful.';
            $account_amount -= $amount;
        } else {
            $err['amount'] = 'Withdrawal amount exceeds account balance.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawals</title>
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
        <div class="cardBox">
            <div class="card">
                <div>
                <div class="numbers hidden" data-original-value="<?php echo $account_amount; ?>"><?php echo str_repeat('*', strlen($account_amount)); ?></div>
                    <div class="cardName">Balance</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="eye-outline"></ion-icon>
                </div>
            </div>
        </div>
        <h2>Withdrawal</h2>
        <?php echo $msg; ?>
        <div class="form">
            <form action="" method="post">
                <label for="user">Withdraw For</label>
                <div class="radio">
                    <input type="radio" name="user" id="self" value="self" <?php echo ($_POST['user'] ?? '') === 'self' ? 'checked' : ''; ?>>
                    <label for="self">Self</label>
                    <input type="radio" name="user" id="other" value="other" <?php echo ($_POST['user'] ?? '') === 'other' ? 'checked' : ''; ?> >
                    <label for="other">Other</label>
                </div>
                <div id="otherFields" style="display: none;">
                    <label for="receiverName">Receiver's Name</label>
                    <input type="text" name="receiverName" id="receiverName" value="<?php echo $receiverName; ?>">
                    <?php echo isset($err['receiverName']) ? $err['receiverName'] : ''; ?>

                    <label for="phone">Receiver's Mobile Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>">
                    <?php echo isset($err['phone']) ? $err['phone'] : ''; ?>
                </div>

                <label for="amount">Withdrawal Amount</label>
                <input type="number" name="amount" id="amount" min="0" step="any">
                <?php echo isset($err['amount']) ? $err['amount'] : ''; ?>

                <div class="button">
                    <input type="submit" value="Withdraw">
                    <input type="reset" value="Reset">
                </div>
            </form>
        </div>
    </div>
</div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
    <script src="../script/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const selfOption = document.getElementById('self');
        const otherOption = document.getElementById('other');
        const otherFields = document.getElementById('otherFields');
        const receiverName = document.getElementById('receiverName');
        const phone = document.getElementById('phone');

        function toggleFields() {
            if (selfOption.checked) {
                otherFields.style.display = 'none';
                receiverName.removeAttribute('required');
                phone.removeAttribute('required');
            } else if (otherOption.checked) {
                otherFields.style.display = 'block';
                receiverName.setAttribute('required', 'required');
                phone.setAttribute('required', 'required');
            }
        }

        selfOption.addEventListener('change', toggleFields);
        otherOption.addEventListener('change', toggleFields);

        toggleFields(); // Run on page load to set initial state
    });
    </script>
</body>
</html>
