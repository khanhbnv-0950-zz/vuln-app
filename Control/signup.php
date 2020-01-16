<?php
$_SESSION['flag'] = -1;
$_SESSION['msg'] = '';

if (isset($_POST['signup'])) {
    if (isset($_POST['email'])&&isset($_POST['name'])&&isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['re_password'])) {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $re_password = $_POST['re_password'];
        if (strcmp($password, $re_password) === 0) {
            $date =  date('Y-m-d h:i:s', time());
            if ($db->addUser($email, $username, $name, sha1($password.SALT), $date)) {
                $_SESSION['msg'] = 'Đăng ký thành công';
                $_SESSION['flag'] = 0;
            } else {
                $_SESSION['msg'] = 'Đăng ký thất bại';
                $_SESSION['flag'] = 1;
            }
        } else {
            $_SESSION['msg'] = 'Mật khẩu không trùng';
            $_SESSION['flag'] = 2;
        }
    } else {
        $_SESSION['msg'] = 'Hãy điền đầy đủ thông tin';
        $_SESSION['flag'] = 3;
    }
}

require_once('View/signup.php');
?>
