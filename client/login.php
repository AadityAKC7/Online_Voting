<?php
session_start();
$err = [];
if(isset($_POST['login'])){
    require_once '../connection.php';
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
        $sql = "SELECT user_id,name,email FROM users WHERE email = '$email' AND password = '$encrypted_password'";
        $result = $connection->query($sql);
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $_SESSION['admin_id'] = $row['user_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            if(isset($_POST['remember'])){
                setcookie('admin_id',$row['user_id'],time()+86400*7);
                setcookie('admin_name',$row['name'],time()+86400*7);
                setcookie('admin_email',$row['email'],time()+86400*7);
            }
            header('Location:dashboard.php');
        } else{
            $msg = 'Credentials not match';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="contain">
    <div class=front-logo>
        <img src="../image/logo.png" alt="Internet Banking">
        <figcaption>Your first choice for monetary needs</figcaption>
    </div>
    <div class="container">
        <h2>Login</h2>
        <form action="" method="post">
            <div>
                <?php if(isset($msg)) { ?>
                    <?php echo $msg ?>
                <?php } ?>

                <?php if(isset($_GET['err']) && $_GET['err'] == 1) { ?>
                    <p>Please login to continue</p>
                <?php } ?>
                <div class="form-box">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo isset($email)?$email: '' ?>"/>
                    <?php if(isset($err['email'])) { ?> 
                        <?php echo $err['email']?>
                    <?php } ?>
                </div>
                <div class="form-box">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password"/>
                    <?php if(isset($err['password'])) { ?> 
                        <?php echo $err['password']?>
                    <?php } ?>
                </div>
                <div>
                    <input type="checkbox" name="remember" id="remember" value="remember">
                    <label for="remember">Remember me</label>
                </div>
                <center><input type="submit" name="login" value="Login"></center>
                <div class="register">
                    Don't have a account? <a href="registration.php">Register Here</a>
                </div>
            </div>
        </form>
    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>