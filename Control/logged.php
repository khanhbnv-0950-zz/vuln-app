<?php
$_SESSION['flag'] = -1;
$_SESSION['msg'] = '';
if (isset($_POST['submit'])) {
    if (isset($_POST['link'])&&isset($_POST['isPrivate'])) {
        $link = $_POST['link'];
        $isPrivate = $_POST['isPrivate'];
        if (strlen($link) > 150) {
            $_SESSION['flag'] = 1;
            $_SESSION['msg'] = 'Độ dài không vượt quá 150 ký tự';
        } else if (!strcmp($isPrivate, 'public') && !strcmp($isPrivate, 'private')) {
            $_SESSION['flag'] = 2;
            $_SESSION['msg'] = 'Hãy chọn chế độ POST mong muốn';
        } else {
            if (strcmp($isPrivate,'public')==0) {
                $isPrivate = 0;
            } else {
                $isPrivate = 1;
            }
            $date = date('Y-m-d h:i:s', time());
            if ($db->addPost($_SESSION['id'], $link, $isPrivate, $date)) {
                $_SESSION['flag'] = 0;
                $_SESSION['msg'] = 'Đăng thành công';
                header('Location: ./?page=logged');
            } else {
                $_SESSION['flag'] = 3;
                $_SESSION['msg'] = 'Đã có lỗi xảy ra';
            }
        }
    }
}
$posts = $db->getPublicPosts();
$privatePosts = $db->getPrivatePosts($_SESSION['id']);
$posts = array_merge($posts, $privatePosts);
require_once('View/index_loggedin.php');
?>