<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php?err=1');
    exit;
}
require_once '../connection.php';
$err = [];
$accNumber = $bankName = $msg = $bankCode = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accNumber']) && !empty($_POST['accNumber']) && trim($_POST['accNumber'])) {
        $accNumber = trim($_POST['accNumber']);
    } else {
        $err['accNumber'] = 'Enter account Number';
    }

    if (isset($_POST['bankName']) && !empty($_POST['bankName']) && trim($_POST['bankName'])) {
        $bankName = trim($_POST['bankName']);
    } else {
        $err['bankName'] = 'Enter bank name';
    }

    if (isset($_POST['bankCode']) && !empty($_POST['bankCode']) && trim($_POST['bankCode'])) {
        $bankCode = strtoupper(trim($_POST['bankCode']));
    } else {
        $err['bankCode'] = 'Enter bank code';
    }

    if (count($err) == 0) {
        switch ($bankName) {
            case 'Banijya Bank':
                if (!preg_match("/^45678567\d{8}$/", $accNumber)) {
                    $err['accNumber'] = 'Account number must start with "45678567" for Banijya Bank.';
                }
                break;
            case 'NIC Asia':
                if (!preg_match("/^25675225\d{7}$/", $accNumber)) {
                    $err['accNumber'] = 'Account number must start with "25675225" for NIC Asia.';
                }
                break;
            case 'Kumari Bank':
                if (!preg_match("/^88070100\d{8}$/", $accNumber)) {
                    $err['accNumber'] = 'Account number must start with "88070100" for Kumari Bank.';
                }
                break;
            default:
                $err['bankName'] = 'Invalid bank selected.';
        }
    }

    if (count($err) == 0) {
        try {
            // Escape inputs to prevent SQL injection
            $accNumber = mysqli_real_escape_string($connection, $accNumber);
            $bankName = mysqli_real_escape_string($connection, $bankName);

            $sql = "INSERT INTO BankAccounts (Account_Number, Bank_Name, Bank_Code) VALUES ('$accNumber', '$bankName', '$bankCode')";
            if (mysqli_query($connection, $sql)) {
                $msg = "Bank account added successfully.";
            } else {
                throw new Exception(mysqli_error($connection));
            }
        } catch (Exception $ex) {
            $msg = 'Error adding bank account: ' . $ex->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bank Account</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
        <h2>Add Bank Account</h2>
        <?php echo $msg; ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validateForm()">
            <div>
                <label for="accNumber">Account Number:</label>
                <input type="text" name="accNumber" id="accNumber">
                <span id="accNumberError" style="color: red;"></span>
                <div id="accNumberStatus" style="color:red;"></div>
                <?php echo isset($err['accNumber'])?$err['accNumber']:''; ?>
            </div>
            <div>
                <label for="bankName">Bank Name:</label>
                <select name="bankName" id="bankName">
                    <option value="">---Select Bank---</option>
                    <option value="NIC Asia" <?php if ($bankName === "NIC Asia") echo "selected" ?>>NIC Asia</option>
                    <option value="Kumari Bank" <?php if ($bankName === "Kumari Bank") echo "selected" ?>>Kumari Bank</option>
                    <option value="Banijya Bank" <?php if ($bankName === "Banijya Bank") echo "selected" ?>>Rastriya Banijya Bank</option>
                </select>
                <span id="bankNameError"><?php echo isset($err['bankName'])?$err['bankName']:''; ?></span>
            </div>
            <div>
                <label for="bankCode">Bank Code:</label>
                <input type="text" name="bankCode" id="bankCode">
                <?php echo isset($err['accNumber'])?$err['accNumber']:''; ?>
            </div>
            <input type="submit" value="Add Account">
        </form>
    </div>
    <script src="../script/toggle.js"></script>
    <script src="../script/accountValid.js"></script>
</body>
</html>
