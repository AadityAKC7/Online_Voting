<?php
session_start();
require_once '../connection.php';
$user_id = $_SESSION['admin_id'];
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $cid = $user_data['Client_id'];
    $name = $user_data['Name'];
    $email = $user_data['Email'];
    $nid = $user_data['National_id'];
    $address = $user_data['Address'];
    $phone = $user_data['Phone'];
    $Profile = $user_data['Profile'];
    $status = $user_data['Status'];
}

$folder = $msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $upload_directory = $_SERVER['DOCUMENT_ROOT'] . "/OnlineBanking/image/";
    $filename = $_FILES["profile"]["name"];
    $tempname = $_FILES["profile"]["tmp_name"];
    $destination = $upload_directory . $filename;

    if (!empty($filename)) {
        if (move_uploaded_file($tempname, $destination)) {
            $folder = "/OnlineBanking/image/" . $filename;
            $sql = "UPDATE users SET Profile='$folder' WHERE user_id=$user_id";
            mysqli_query($connection, $sql);
        } else {
            $msg = "Error moving uploaded file.";
        }
    }

    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_nid = $_POST['nid'];
    $new_address = $_POST['address'];
    $new_phone = $_POST['phone'];
    $update_query = "UPDATE users SET Name='$new_name', Email='$new_email', National_id='$new_nid', Address='$new_address', Phone='$new_phone' WHERE user_id=$user_id";
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
    <?php require_once 'navbar.php'; ?>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <ion-icon name="menu-outline"></ion-icon>
            </div>
        </div>
        <div class="account">
            <div class="profile">
                <h1 style="margin-bottom: 10px; ">Your Information's</h1>
                <?php if (isset($msg)) { ?>
                    <p><?php echo $msg; ?></p>
                <?php } ?>
                <?php if ($Profile) { ?>
                    <img src="<?php echo $Profile; ?>" alt="Profile Picture">
                    <?php if ($status) { ?>
                        <p class="verified-text">Verified</p>
                    <?php } ?>
                <?php } ?>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>Name: </b><?php echo $name; ?></p><br>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>Client id: </b><?php echo $cid; ?></p><br>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>Email:</b> <?php echo $email; ?></p><br>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>National id:</b> <?php echo $nid; ?></p><br>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>Address:</b> <?php echo $address; ?></p><br>
                <p style="margin: 5px 10px; font-size: 15px; ;"><b>Phone:</b> <?php echo $phone; ?></p><br>
            </div>
            <div class="form">
                <h2>Update Profile</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $name; ?>">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>"><br>
                    <label for="nid">National ID</label>
                    <input type="text" name="nid" id="nid" value="<?php echo $nid; ?>"><br>
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo $address; ?>"><br>
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo $phone; ?>"><br>
                    <label for="profile">Profile Picture</label>
                    <input type="file" name="profile" id="profile"><br>
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