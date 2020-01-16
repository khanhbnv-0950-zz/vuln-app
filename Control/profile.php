<?php
$username = '';
$name = '';
if ((isset($_GET['profile'])&&$_GET['profile'] === $_SESSION['username'])
        || !isset($_GET['profile'])) {
    $follow = $db->getApproveFollow($_SESSION['id']);
    $posts = $db->getPosts($_SESSION['id'], 1);
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];
    $status_follow = FALSE;
} else {
    $result = $db->checkUsername($_GET['profile']);
    if ($result) {
        $follow = [];
        $posts = $db->getPosts($result['id'], 1);
        $username =  $result['username'];
        $name = $result['name'];
        $status_follow = $db->getStatusFollow($db->getUserIDByUsername($username), $_SESSION['id']);
        if ($status_follow === -1 || $status_follow === 2) {
            $text_follow = 'Follow';
            $action = 'follow';
        } else if ($status_follow === 1){
            $text_follow = 'UnFollow';
            $action = 'unfollow';
        } else if ($status_follow === 0) {
            $text_follow = 'Waiting';
            $action = 'unfollow';
        }
    } else {
        echo 'Không có user';
    }
}
if ($posts === FALSE) {
    $posts = [];
}

require_once('View/profile.php');
?>