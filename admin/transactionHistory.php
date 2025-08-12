<?php
session_start();
require_once '../connection.php';
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php?err=1');
    exit;
}

$user_id = $_SESSION['admin_id'];
$sql = "SELECT transactions.*, users.Account_Number AS 'Acc_Number', users.Name AS 'Acc_Owner' 
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
    <title>Transactions</title>
    <link rel="stylesheet" href="../css/transaction.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/client.css">
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
        <div class="history">
        <h1>Transaction History</h1>
        <h4>Select on any action to manage the transaction.</h4>
        <form id="transaction-display-form">
            <label for="transaction-count">Number of Transactions:</label>
            <select id="transaction-count" name="transaction-count">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </form>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Transaction ID</th>
                        <th>Account No.</th>
                        <th>Acc. Owner</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Remarks</th>
                        <th>Action</th>
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
                            echo "<td>";
                            echo "<form action='rollback.php' method='post'>";
                            echo "<input type='hidden' name='transaction_id' value='" . $row['Transaction_id'] . "'>";
                            echo "<button type='submit'>Rollback</button>";
                            echo "</form>";
                            echo "</td>";
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
    </div>

       

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
</body>
</html>

<?php
// Close the connection
$connection->close();
?>
