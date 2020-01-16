<?php
if (isset($_SESSION['logged'])&&$_SESSION['logged']) {
    $isPrivate = $db->getStatusPost($_GET['id']);
    if ($isPrivate !== FALSE) {
        $status_follow = 0;
        $post_user = $db->getUserIDByPostID($_GET['id']);
        $check = FALSE;
        if ($isPrivate==1 && $post_user!=$_SESSION['id']) {
            $status_follow = $db->getStatusFollow($_SESSION['id'], $post_user);
            if ($status_follow === 1) {
                $check = TRUE;
            }
        } else if ($isPrivate==1 && $post_user==$_SESSION['id'] || $isPrivate==0){
            $check = TRUE;
        }
        if ($check) {
            $checkLike = $db->checkLike($_GET['id'], $_SESSION['id']);
            if ($checkLike === -1) {
                $date = date('Y-m-d h:i:s', time());
                $db->addLike($_GET['id'], $_SESSION['id'], $date);
            } else if ($checkLike !== FALSE) {
                if ($checkLike === 0) {
                    $status = 1;
                } else {
                    $status = 0;
                }
                $date = date('Y-m-d h:i:s', time());
                $db->updateLike($_GET['id'], $_SESSION['id'], $status, $date);
            }
        }
    }
} else {
}
echo $db->countLike($_GET['id']);
?>