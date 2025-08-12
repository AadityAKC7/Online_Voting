<?php
session_start();
require_once '../connection.php';
$user_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE Admin_id = $user_id";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $name = $user_data['Name'];
    $email = $user_data['Email'];
    $adminId = $user_data['Admin_number'];
}
$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_adminId = $_POST['adminId'];
    $update_query = "UPDATE admin SET Name='$new_name', Email='$new_email', Admin_id='$new_adminId' WHERE admin_id=$user_id";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        $msg = "Profile updated successfully.";
    } else {
        $msg = "Error updating profile: " . mysqli_error($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
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
        <div class="account">
        <div class="profile">
            <h1 style="margin-bottom: 10px; ">Your Information's</h1>
            <div class="image"><img src="../image/admin.png" alt=""></div>
            <p style="margin: 5px 10px; font-size: 15px; ;"><b>Name: </b><?php echo $name; ?></p><br>
            <p style="margin: 5px 10px; font-size: 15px; ;"><b>Email:</b> <?php echo $email; ?></p><br>
            <p style="margin: 5px 10px; font-size: 15px; ;"><b>Admin id:</b> <?php echo $adminId; ?></p><br>
        </div>
            <div class="form">
                <h2>Update Profile</h2>
                <?php echo $msg; ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $name; ?>">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>"><br>
                    <label for="adminId">Admin ID</label>
                    <input type="text" name="adminId" id="adminId" readonly value="<?php echo $adminId; ?>"><br>
                    <input type="submit" value="Update Profile">
                </form>
            </div>
        </div>
    </div>
        
      
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/toggle.js"></script>
</body>
</html>