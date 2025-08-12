<?php
session_start();
require_once '../connection.php';
$msg = '';
$userId = null;

// Handle GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    if (filter_var($userId, FILTER_VALIDATE_INT) !== false) {
        $userId = mysqli_real_escape_string($connection, $userId);
        $sql = "SELECT * FROM users WHERE User_id = $userId";
        $result = $connection->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['Name'];
            $client_id = $row['Client_id'];
            $national_id = $row['National_id'];
            $phone = $row['Phone'];
            $email = $row['Email'];
            $address = $row['Address'];
        } else {
            $msg = "User not found.";
        }
    } else {
        $msg = "Invalid user ID.";
    }
}

// Handle POST request for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
    $userId = $_POST['user_id']; // Get user_id from hidden form field
    $name = $_POST['name'];
    $client_id = $_POST['client_id'];
    $national_id = $_POST['national_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Escape inputs to prevent SQL injection
    $name = mysqli_real_escape_string($connection, $name);
    $client_id = mysqli_real_escape_string($connection, $client_id);
    $national_id = mysqli_real_escape_string($connection, $national_id);
    $phone = mysqli_real_escape_string($connection, $phone);
    $email = mysqli_real_escape_string($connection, $email);
    $address = mysqli_real_escape_string($connection, $address);
    $userId = mysqli_real_escape_string($connection, $userId);
    
    $update_query = "UPDATE users SET 
                    Name = '$name',
                    Client_id = '$client_id',
                    National_id = '$national_id',
                    Phone = '$phone',
                    Email = '$email',
                    Address = '$address'
                    WHERE User_id = $userId";
    
    if ($connection->query($update_query) === TRUE) {
        $msg = "Record updated successfully";
        header("Location: clients.php");
        exit; // Ensure no further code is executed after redirect
    } else {
        $msg = "Error updating record: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="../account.css">
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>

        <h1>Update User</h1>
        <p><?php echo htmlspecialchars($msg); ?></p>
        <?php if ($userId && isset($row)): ?>
        <form method="post">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>
            
            <label for="client_id">Client ID:</label><br>
            <input type="text" id="client_id" name="client_id" value="<?php echo htmlspecialchars($client_id); ?>" readonly><br><br>
            
            <label for="national_id">National ID:</label><br>
            <input type="text" id="national_id" name="national_id" value="<?php echo htmlspecialchars($national_id); ?>"><br><br>
            
            <label for="phone">Phone:</label><br>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>"><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>
            
            <label for="address">Address:</label><br>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>"><br><br>
            
            <input type="submit" name="submit_update" value="Update">
        </form>
        <?php else: ?>
            <p>No user details available for update.</p>
        <?php endif; ?>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
</body>
</html>

<?php
$connection->close();
?>
