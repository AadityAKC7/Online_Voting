<?php
session_start();
error_reporting(0);
$err = [];
$bankCode = 'EB';
$bankPrefix = '12345678';
$name = $email = $phone = $password = $address = $nationalID = $accType = $accNumber = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])) {
        $name = trim($_POST['name']);
        if (!preg_match("/^([A-Z][a-z\s]+)+$/", $name)) {
            $err['name'] = 'Enter valid name';
        }
    } else {
        $err['name'] = 'Enter full name';
    }

    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $email)) {
            $err['email'] = "Enter valid email";
        }
    } else {
        $err['email'] = 'Enter your email';
    }

    if (isset($_POST['password']) && !empty($_POST['password']) && trim($_POST['password'])) {
        $password = trim($_POST['password']);
        $encrypted_password = md5($password);
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
            $err['password'] = 'Enter valid password';
        }
    } else {
        $err['password'] = 'Enter password';
    }

    if (isset($_POST['phone']) && !empty($_POST['phone']) && trim($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        if (!preg_match("/(\+977)?[9][6-9]\d{8}/", $phone)) {
            $err['phone'] = "Enter valid phone number";
        }
    } else {
        $err['phone'] = 'Enter your phone';
    }

    if (isset($_POST['address']) && !empty($_POST['address']) && trim($_POST['address'])) {
        $address = trim($_POST['address']);
    } else {
        $err['address'] = 'Enter your address';
    }

    if (isset($_POST['nationalID']) && !empty($_POST['nationalID']) && trim($_POST['nationalID'])) {
        $nationalID = trim($_POST['nationalID']);
    } else {
        $err['nationalID'] = 'Enter your national id';
    }

    if (isset($_POST['accType']) && !empty($_POST['accType']) && trim($_POST['accType'])) {
        $accType = trim($_POST['accType']);
    } else {
        $err['accType'] = 'Select your account type';
    }

    if (isset($_POST['accNumber']) && !empty($_POST['accNumber']) && trim($_POST['accNumber'])) {
        $accNumber = trim($_POST['accNumber']);
        if (!preg_match("/^\d{16}$/", $accNumber) || substr($accNumber, 0, 8) !== $bankPrefix) {
            $err['accNumber'] = 'Enter valid account number with correct bank prefix';
        }
    } else {
        $err['accNumber'] = 'Enter your account Number';
    }

    if (!isset($_POST['terms'])) {
        $err['terms'] = 'Please accept terms and conditions';
    }

    if (count($err) == 0) {
        try {
            require_once '../connection.php';
            $m = date('m');
            $d = date('d');
            $cid = "CLI{$m}{$d}-" . rand(100, 999);
            $sql = "SELECT Account_id FROM account WHERE Account_type = '$accType'";
            $result = mysqli_query($connection, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $account_id = $row['Account_id'];
                $sql = "INSERT INTO users(Name, Email, Password, Phone, Address, National_id, Client_id, Account_id, Account_Number)
                VALUES ('$name', '$email', '$encrypted_password', $phone, '$address', $nationalID, '$cid', $account_id, $accNumber)";
                mysqli_query($connection, $sql);
                echo 'Registration Successful.';
            } else {
                echo 'Error: Account type not found';
            }
        } catch (Exception $ex) {
            die('Database Error:' . $ex->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="../css/registration.css">
</head>
<body>
    <div class=front-logo>
        <img src="images/logo.png" alt="Internet Banking">
        <figcaption>Hello, Sign in and Do e-Banking</figcaption>
    </div>
    <div class="container">
        <h2>Register here</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <div>
                <div class="form-box">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name"/>
                    <?php echo isset($err['name'])?$err['name']:''; ?>
                </div>
                <div class="form-box">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email"/>
                    <?php echo isset($err['email'])?$err['email']:''; ?>
                </div>
                <div class="form-box">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password"/>
                    <?php echo isset($err['password'])?$err['password']:''; ?>
                </div>
                <div class="form-box">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone"/>
                    <?php echo isset($err['phone'])?$err['phone']:''; ?>
                </div>
                <div class="form-box">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address"/>
                    <?php echo isset($err['address'])?$err['address']:''; ?>
                </div>
                <div class="form-box">
                    <label for="nationalID">National ID Number</label>
                    <input type="number" name="nationalID" id="nationalID"/>
                    <?php echo isset($err['nationalID'])?$err['nationalID']:''; ?>
                </div>
                <div class="form-box">
                    <label for="accType">Account Type</label>
                    <select name="accType" id="accType">
                        <option value="">---Select Account Type---</option>
                        <option value="saving" <?php if($accType === "saving")echo "selected" ?>>Saving</option>
                        <option value="current" <?php if($accType === "current")echo "selected" ?>>Current</option>
                    </select>
                    <?php echo isset($err['accType'])?$err['accType']:''; ?>
                </div>
                <div class="form-box">
                    <label for="accNumber">Account Number</label>
                    <input type="text" name="accNumber" id="accNumber">
                    <?php echo isset($err['accNumber'])?$err['accNumber']:''; ?>
                </div>
                <div>
                    <input type="checkbox" name="terms" id="terms">
                    <label for="terms">I accept terms and conditions</label>
                    <?php echo isset($err['terms'])?$err['terms']:''; ?>
                </div>
                <input type="submit" value="Submit" name="submit">
                <input type="submit" value="Login" id="login" onclick="redirectToLogin(event)">
            </div>
        </form>
    </div>
    <script>
        function redirectToLogin(event) {
            event.preventDefault(); // Prevent the form from submitting
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>