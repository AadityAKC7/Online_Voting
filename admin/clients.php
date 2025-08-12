<?php
session_start();
require_once '../connection.php';

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM users";
$result = $connection->query($sql);

if ($result === false) {
    trigger_error('Invalid query: ' . $connection->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link rel="stylesheet" href="../css/client.css">
    <link rel="stylesheet" href="../css/transaction.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .verified {
            color: green;
            font-weight: bold;
            cursor: none;
            text-decoration:none;
        }
    </style>
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
            <h1>eBanking Clients</h1>
            <h4>Select on any action to manage your clients</h4>
            <form id="transaction-display-form">
                <label for="transaction-count">Number of Clients:</label>
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
                            <th>Name</th>
                            <th>Client Number</th>
                            <th>National ID</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusText = isset($row['status_text']) ? $row['status_text'] : 'Verify';
                                $verifiedClass = isset($row['status']) && $row['status'] == 1 ? 'verified' : '';

                                echo "<tr>";
                                echo "<td>" . $count . "</td>";
                                echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Client_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['National_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                                echo "<td>";
                                echo "<a href='update.php?user_id=" . urlencode($row['user_id']) . "'>Update</a> &nbsp;&nbsp;";
                                echo "<a href='delete.php?user_id=" . urlencode($row['user_id']) . "'>Delete</a>";
                                echo "</td>";
                                echo "<td>";
                                if (array_key_exists('Status', $row) && $row['Status'] == 1) {
                                    echo "<span class='verified'>Verified</span>";
                                } else {
                                    echo "<span class='verify-btn' data-user-id='" . $row['user_id'] . "'>Verify</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='9'>No clients found or failed to fetch data.</td></tr>";
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
    <script src="../script/script.js"></script>
    <script src="../script/toggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verifyButtons = document.querySelectorAll('.verify-btn');

            verifyButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    
                    // Send AJAX request to update status
                    fetch('verify.php?user_id=' + userId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update UI to show "Verified"
                                this.textContent = 'Verified';
                                this.classList.add('verified');
                            } else {
                                alert('Failed to verify user.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to verify user.');
                        });
                });
            });
        });
    </script>

</body>
</html>
