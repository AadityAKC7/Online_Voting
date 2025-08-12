<?php
session_start();
$err = [];

if(isset($_POST['login'])){
    if(isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])){
        $email = $_POST['email'];
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $err['email'] = 'Please enter valid email';
        }
    } else{
        $err['email'] = 'Please enter email';
    }

    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password = $_POST['password'];
        $encrypted_password = md5($password);
    } else{
        $err['password'] = 'Please enter password';
    }

    if(count($err) == 0){
        require_once '../connection.php';
        $sql = "SELECT admin_id,name,email FROM admin WHERE email = '$email' AND password = '$encrypted_password'";
        $result = $connection->query($sql);
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            header('location:dashboard.php');
        } else{
            $msg = 'Credentials not match';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <section>
        <div class="signin">
            <div class="content">
                <h2>Sign In</h2>
                <?php if(isset($msg)) { ?>
                    <?php echo $msg ?>
                <?php } ?>

                <?php if(isset($_GET['err']) && $_GET['err'] == 1) { ?>
                    <p>Please login to continue</p>
                <?php } ?>
                <form action="" method="post">
                    <div class="form">
                        <div class="inputBox">
                            <label for="email">Username</label>
                            <input type="email" name="email">
                            <?php if(isset($err['email'])) { ?> 
                            <?php echo $err['email']?>
                        <?php } ?>
                        </div>
                        <div class="inputBox">
                            <label for="password">Password</label>
                            <input type="password" name="password">
                            <?php if(isset($err['password'])) { ?> 
                            <?php echo $err['password']?>
                        <?php } ?>
                        </div>
                        <div class="inputBox">
                            <input type="submit" value="Login" name="login">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>