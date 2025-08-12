<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon"></span>
                        <span class="title"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Guest'; ?></span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="dashboard.php">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="account.php">
                        <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                        <span class="title">Account</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="fundTransfer.php">
                        <span class="icon"><ion-icon name="logo-usd"></ion-icon></span>
                        <span class="title">Fund Transfer</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="withdrawal.php">
                        <span class="icon"><ion-icon name="cash-outline"></ion-icon></span>
                        <span class="title">Withdraw</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="transactionHistory.php">
                        <span class="icon"><ion-icon name="swap-horizontal-outline"></ion-icon></span>
                        <span class="title">Transaction History</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="changePassword.php">
                        <span class="icon"><ion-icon name="lock-open-outline"></ion-icon></span>
                        <span class="title">Change Password</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="logout.php">
                        <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
                        <span class="title">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
