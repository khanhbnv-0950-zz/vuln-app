<?php
$_SESSION['flag'] = -1;
$_SESSION['msg'] = '';

if (isset($_POST['signin'])) {
    if (isset($_POST['email'])&&isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $remember = FALSE;
        if (isset($_POST['remember-me'])) {
            $remember = TRUE;
        }

        $result = $db->checkUser($email, sha1($password.SALT));
        if ($result === FALSE) {
            $_SESSION['msg'] = 'Sai tài khoản hoặc mật khẩu';
            $_SESSION['flag'] = 1;
        } else {
            $_SESSION['msg'] = 'Đăng nhập thành công';
            $_SESSION['flag'] = 0;
            $_SESSION['logged'] = TRUE;
            $_SESSION['username'] = $result['username'];
            $_SESSION['id'] = $result['id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['isAdmin'] = $result['isAdmin'];
        }
    } else {
        $_SESSION['msg'] = 'Hãy điền đầy đủ thông tin';
        $_SESSION['flag'] = 2;
    }
}

require_once('View/signin.php');
?>