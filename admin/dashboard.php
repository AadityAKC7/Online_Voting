<?php
session_start();
require_once '../connection.php';
if(!isset($_SESSION['admin_id'])){
    header('location:login.php?err=1');
    exit;
}

$user_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $name = $user_data['Name'];
}
$sql = "SELECT count(id) AS transaction_count FROM Transactions";
$results = mysqli_query($connection,$sql);
if ($results) {
    $row = mysqli_fetch_assoc($results);
    $transaction_count = $row['transaction_count'];
} else {
    $transaction_count = "Error: " . mysqli_error($connection);
}

$sql1 = "SELECT count(user_id) AS user_count FROM Users";
$result1 = mysqli_query($connection,$sql1);
if ($result1) {
    $row = mysqli_fetch_assoc($result1);
    $user_count = $row['user_count'];
}

$sql2 = "SELECT SUM(Amount) AS amount FROM Users";
$result2 = mysqli_query($connection,$sql2);
if ($result2) {
    $row = mysqli_fetch_assoc($result2);
    $amount = $row['amount'];
}

$sql3 = "SELECT count(Account_id) AS Account_count FROM Account";
$result3 = mysqli_query($connection,$sql3);
if ($result3) {
    $row = mysqli_fetch_assoc($result3);
    $account_count = $row['Account_count'];
}

$deposit_sql = "SELECT SUM(amount) AS total_deposit FROM Transactions WHERE remarks = 'deposit'";
$withdrawal_sql = "SELECT SUM(amount) AS total_withdrawal FROM Transactions WHERE remarks = 'withdrawal'";
$deposit_result = mysqli_query($connection, $deposit_sql);
$withdrawal_result = mysqli_query($connection, $withdrawal_sql);

$total_deposit = ($deposit_result && mysqli_num_rows($deposit_result) > 0) ? mysqli_fetch_assoc($deposit_result)['total_deposit'] : 0;
$total_withdrawal = ($withdrawal_result && mysqli_num_rows($withdrawal_result) > 0) ? mysqli_fetch_assoc($withdrawal_result)['total_withdrawal'] : 0;

$sql = "SELECT transactions.*, users.Account_Number AS 'Acc_Number', users.name AS 'Acc_Owner' 
        FROM transactions 
        INNER JOIN users ON transactions.Tuser_id = users.user_id";
$result = $connection->query($sql);

if (!$result) {
    trigger_error('Invalid query: ' . $connection->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="../css/transaction.css">
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
        <h4>Welcome,<?php echo $name; ?></h4>
        <div class="cardBox">
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $user_count; ?></div>
                    <div class="cardName">Clients</div>
                </div>
                <div class="iconBx">
                    <i class="fa-solid fa-money-bills"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $account_count;?></div>
                    <div class="cardName">Accounts</div>
                </div>
                <div class="iconBx">
                    <i class="fa-solid fa-money-bills"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers">Rs. <?php echo number_format($total_deposit, 2); ?></div>
                    <div class="cardName">Deposits</div>
                </div>
                <div class="iconBx">
                    <i class="fa-solid fa-money-bills"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers">Rs. <?php echo number_format($total_withdrawal, 2); ?></div>
                    <div class="cardName">Withdrawals</div>
                </div>
                <div class="iconBx">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?php echo $transaction_count; ?></div>
                    <div class="cardName">Transfers</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="swap-horizontal-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"Rs. ><?php echo $amount; ?></div>
                    <div class="cardName">Balance</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="eye-outline"></ion-icon>
                </div>
            </div>
        </div>
        <div class="table">
            <table cellspacing=0>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Transaction ID</th>
                        <th>Account No.</th>
                        <th>Acc. Owner</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['Transaction_id'] . "</td>";
                            echo "<td>" . $row['Acc_Number'] . "</td>";
                            echo "<td>" . $row['Acc_Owner'] . "</td>";
                            echo "<td>" . $row['Amount'] . "</td>";
                            echo "<td>" . $row['Date'] . "</td>";
                            echo "<td>" . $row['Remarks'] . "</td>";
                            echo "</tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='10'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
        
      
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const balanceSection = document.querySelector('.card:nth-child(6)');
            const eyeIcon = balanceSection.querySelector('.iconBx ion-icon');
            const balanceElement = balanceSection.querySelector('.numbers');

            eyeIcon.addEventListener('click', () => {
                if (balanceElement.classList.contains('hidden')) {
                    balanceElement.classList.remove('hidden');
                    balanceElement.textContent = balanceElement.dataset.originalValue;
                    eyeIcon.setAttribute('name', 'eye-outline');
                } else {
                    balanceElement.dataset.originalValue = balanceElement.textContent;
                    balanceElement.textContent = '*'.repeat(balanceElement.textContent.length);
                    balanceElement.classList.add('hidden');
                    eyeIcon.setAttribute('name', 'eye-off-outline');
                }
            });
        });
    </script>
</body>
</html>