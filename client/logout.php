<?php
session_start();
session_destroy();
setcookie('admin_id',false,time()-86400*7);
setcookie('admin_name',false,time()-86400*7);
setcookie('admin_email',false,time()-86400*7);
header('location:login.php');
?>