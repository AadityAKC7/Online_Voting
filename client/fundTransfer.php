<?php
session_start();
require_once '../connection.php';

// Generate a unique token for the form to prevent double submission
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

$err = [];
$user_id = $_SESSION['admin_id'];
$name = $_SESSION['admin_name'];
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $name = $user_data['Name'];
    $account_amount = $user_data['Amount'];
}

$receiverBankAcNo = $receiverBank = $receiverAcName = $phone = $amount = $remarks = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form token to prevent double submission
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        die('Invalid form submission');
    }

    // Regenerate the token after a valid submission
    unset($_SESSION['form_token']);
    $_SESSION['form_token'] = bin2hex(random_bytes(32));

    $current_month = date('m');
    $current_year = date('Y');
    $monthly_limit = 50000;

    $query = "SELECT SUM(Amount) AS total_amount FROM transactions WHERE Tuser_id = $user_id AND MONTH(Date) = $current_month AND YEAR(Date) = $current_year";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    $total_amount_transferred = $row['total_amount'] ?? 0;

    // Initialize amount to zero if not set to prevent type errors
    $amount = $_POST['amount'] ?? 0;

    // Validate amount is numeric and non-negative
    if (!is_numeric($amount) || $amount <= 0) {
        $err['amount'] = 'Invalid amount format! Please enter a valid non-negative numeric value.';
    } elseif ($total_amount_transferred + $amount > $monthly_limit) {
        $err['amount'] = 'Transaction amount exceeds monthly limit of 50000.';
    } elseif ($amount > $account_amount) {
        $err['amount'] = 'Insufficient balance!';
    }

    if (empty($_POST["receiverBank"])) {
        $err['receiverBank'] = "Receiver's Bank is required";
    } else {
        $receiverBank = $_POST["receiverBank"];
    }

    if (isset($_POST['receiverAcName']) && !empty($_POST['receiverAcName']) && trim($_POST['receiverAcName'])) {
        $receiverAcName = trim($_POST['receiverAcName']);
        if (!preg_match("/^([A-Z][a-z\s]+)+$/", $receiverAcName)) {
            $err['receiverAcName'] = 'Enter a valid name';
        }
    } else {
        $err['receiverAcName'] = 'Enter full name';
    }

    if (isset($_POST['receiverBankAcNo']) && !empty($_POST['receiverBankAcNo'])) {
        $receiverBankAcNo = $_POST['receiverBankAcNo'];
        if (!preg_match("/^\d{16}$/", $receiverBankAcNo)) {
            $err['receiverBankAcNo'] = 'Invalid Account Number! Please enter a valid 16 digit number';
        } else {
            // Validate the account number against the bank
            $validate_query = "SELECT * FROM BankAccounts WHERE Account_Number = '$receiverBankAcNo' AND Bank_Name = '$receiverBank'";
            $validate_result = mysqli_query($connection, $validate_query);
            if (mysqli_num_rows($validate_result) === 0) {
                $err['receiverBankAcNo'] = 'The account number does not belong to the selected bank';
            }
        }
    } else {
        $err['receiverBankAcNo'] = 'Enter the account number';
    }

    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $phone = $_POST['phone'];
        if (!preg_match("/^\d{10}$/", $phone)) {
            $err['phone'] = 'Invalid Number! Please enter a valid 10 digit number';
        }
    } else {
        $err['phone'] = 'Enter the phone number';
    }

    if (isset($_POST['remarks']) && !empty($_POST['remarks'])) {
        $remarks = $_POST['remarks'];
        if (!preg_match("/^[a-zA-Z0-9,.!?'\s]{0,255}$/", $remarks)) {
            $err['remarks'] = 'Remarks!';
        }
    } else {
        $err['remarks'] = 'Enter any remarks';
    }

    if (count($err) == 0) {
        $user_id = $_SESSION['admin_id'];
        $m = date('m');
        $d = date('d');
        $Tid = "TRN{$m}{$d}" . rand(100, 999);
        $current_date = date('Y-m-d');
        $sql = "INSERT INTO transactions(Transaction_id, Receiver_Bank_Name, Receiver_Bank_Number, Receiver_Account_Name, Phone, Amount, Date, Remarks, Tuser_id)
                VALUES ('$Tid', '$receiverBank', '$receiverBankAcNo', '$receiverAcName', '$phone', '$amount', '$current_date', '$remarks', '$user_id')";

        mysqli_begin_transaction($connection);
        if (mysqli_query($connection, $sql)) {
            $update_query_sender = "UPDATE Users SET Amount = Amount - $amount WHERE user_id = $user_id";
            $sender_update_result = mysqli_query($connection, $update_query_sender);

            // Update receiver's account balance
            $update_query_receiver = "UPDATE Users SET Amount = Amount + $amount WHERE Account_Number = '$receiverBankAcNo'";
            $receiver_update_result = mysqli_query($connection, $update_query_receiver);

            // Commit the transaction if all queries succeed
            if ($sender_update_result && $receiver_update_result) {
                mysqli_commit($connection);
                $msg = 'Transaction Successful. Money transferred successfully.';
            } else {
                // Rollback the transaction if any query fails
                mysqli_rollback($connection);
                $msg = 'Transaction failed. Please try again.';
            }
        } else {
            mysqli_rollback($connection);
            $msg = 'Transaction failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Transfer</title>
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
        <h2>Fund Transfer</h2>
        <?php echo $msg; ?>
        <div class="form">
            <form action="" method="post">
                <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                
                <label for="receiverBank">Receiver's Bank</label>
                <select name="receiverBank" id="receiverBank">
                    <option value="">---Select Bank---</option>
                    <option value="eBank" <?php if ($receiverBank === "eBank") echo "selected" ?>>eBank</option>
                    <option value="nic" <?php if ($receiverBank === "nic") echo "selected" ?>>NIC Asia</option>
                    <option value="kumariBank" <?php if ($receiverBank === "kumariBank") echo "selected" ?>>Kumari Bank</option>
                    <option value="badigya" <?php if ($receiverBank === "badigya") echo "selected" ?>>Badigya Bank</option>
                </select>
                <?php echo isset($err['receiverBank']) ? $err['receiverBank'] : ''; ?>
                
                <label for="receiverBankAcNo">Receiver's Bank Account Number</label>
                <input type="text" name="receiverBankAcNo" id="receiverBankAcNo" value="<?php echo htmlspecialchars($receiverBankAcNo); ?>">
                <?php echo isset($err['receiverBankAcNo']) ? $err['receiverBankAcNo'] : ''; ?>

                <label for="receiverAcName">Receiver's Account Name</label>
                <input type="text" name="receiverAcName" id="receiverAcName" value="<?php echo htmlspecialchars($receiverAcName); ?>">
                <?php echo isset($err['receiverAcName']) ? $err['receiverAcName'] : ''; ?>

                <label for="phone">Receiver's Mobile Number</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <?php echo isset($err['phone']) ? $err['phone'] : ''; ?>

                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" value="<?php echo htmlspecialchars($amount); ?>">
                <?php echo isset($err['amount']) ? $err['amount'] : ''; ?>

                <label for="remarks">Remarks</label>
                <input type="text" name="remarks" id="remarks" value="<?php echo htmlspecialchars($remarks); ?>">
                <?php echo isset($err['remarks']) ? $err['remarks'] : ''; ?><br>

                <input type="submit" value="Proceed">
                <input type="submit" value="Cancel">
            </form>
        </div>
    </div>
    <script src="../script/toggle.js"></script>
    <script src="../script/script.js"></script>
</body>
</html>
