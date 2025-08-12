<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header('location:login.php?err=1');
    exit;
}
require_once '../connection.php';
$user_id = $_SESSION['admin_id'];

if(isset($_POST['update'])) {
    $update_query = "UPDATE users SET 
                    Name = '$name',
                    Client_id = '$client_id',
                    National_id = '$national_id',
                    Phone = '$phone',
                    Email = '$email',
                    Address = '$address'
                    WHERE User_id = $userIdToUpdate";
    
    if ($connection->query($update_query) === TRUE) {
        $msg = "Record updated successfully";
    } else {
        $msg = "Error updating record: " . $connection->error;
    }
}
$msg = '';
if(isset($_POST['delete'])) {
    $userIdToDelete = $_POST['user_id'];
    
    $delete_query = "UPDATE Account SET IsDeleted = 1 WHERE Account_id = ?";
    if ($stmt = $connection->prepare($delete_query)) {
        $stmt->bind_param('i', $userIdToDelete);
        if ($stmt->execute()) {
            $msg = "Record marked as deleted successfully";
        } else {
            $msg = "Error marking record as deleted: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $msg = "Error preparing delete statement: " . $connection->error;
    }
    header("Location: manageAccounts.php");
    exit();
}

// Fetch non-deleted Accounts
$sql = "SELECT * FROM Account WHERE IsDeleted = 0";
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
    <title>Accounts</title>
    <link rel="stylesheet" href="../css/manage.css">
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
        <div class="transactions">
            <h1>Manage Accounts</h1>
            <?php echo $msg; ?>
            <table cellspacing=0>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Account Type</th>
                        <th>Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['Account_id'] . "</td>";
                            echo "<td>" . $row['Account_type'] . "</td>";
                            echo "<td>" . $row['Rate'] . "</td>";
                            echo "<td>
                                    <a class='button' href='update1.php?id=" . $row['Account_id'] . "'>Update</a>
                                    <form method='post' action='manageAccounts.php' style='display:inline;' onsubmit='return confirmDelete();'>
                                        <input type='hidden' name='user_id' value='" . $row['Account_id'] . "'>
                                        <button type='submit' name='delete'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No Accounts found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>    
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/script.js"></script>
    <script src="../script/toggle.js"></script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>
</body>
</html>
