<?php 
session_start();
require_once '../connection.php';
$name = $_SESSION['admin_name'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $err = [];
    $email = $_SESSION['admin_email']; 
    $password = $_POST['password'];
    $nPassword = $_POST['nPassword'];
    $cPassword = $_POST['cPassword'];

    $stmt = $connection->prepare("SELECT password FROM admin WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (md5($password) === $row['password']) {
        if (empty($nPassword)) {
            $err[] = 'Enter valid password';
        } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $nPassword)) {
            $err[] = 'Password must be at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character';
        }
        
        if (empty($cPassword)) {
            $err[] = "Confirm password is required";
        }
        if ($nPassword !== $cPassword) {
            $err[] = "New password and confirm password do not match";
        }

        if (empty($err)) {
            $hashedPassword = md5($nPassword);
            $updateStmt = $connection->prepare("UPDATE admin SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedPassword, $email);
            if ($updateStmt->execute()) {
                echo "Password updated successfully";
            } else {
                $err[] = "Error updating password: " . $updateStmt->error;
            }
        }
    } else {
        $err[] = "Old password is incorrect";
    }

    if (!empty($err)) {
        foreach ($err as $error) {
            echo "<div class='error'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../xss/account.css">
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
        <div class="form">
            <h1>Change Password</h1>
            <form action="" method="post">
                <label for="password">Old Password</label>
                <input type="password" name="password" id="password"><br>
                <label for="nPassword">New Password</label>
                <input type="password" name="nPassword" id="nPassword"><br>
                <label for="cPassword">Confirm New Password</label>
                <input type="password" name="cPassword" id="cPassword"><br>
                <input type="submit" value="Change Password">
            </form>
        </div>
    </div>
        
      
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
</body>
</html>