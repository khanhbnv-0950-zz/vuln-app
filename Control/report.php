<?php
$_SESSION['flag'] = -1;
$_SESSION['msg'] = '';

if (!isset($_GET['id']) || $_GET['id']=='' || preg_match_all('/\D/', $_GET['id'])) {
    header('Location: ./?page=home');    
}

$user_id = $db->getUserIDByPostID($_GET['id']);
$user_info = $db->getInfoUser($user_id);
if (isset($_POST['type'])) {
    $type = -1;
    switch($_POST['type']) {
        case 'broken':
            $type = 1;
        break;
        case 'inappropriate':
            $type = 2;
        break;
        case 'spam':
            $type = 3;
        break;
    }
    
    $check = FALSE;
    $date = date('Y-m-d h:i:s', time());
    $status_follow = $db->getStatusFollow($user_id, $_SESSION['id']);
    $status_post = $db->getStatusPost($_GET['id']);
    $check_show = FALSE;
    if ($type !== -1) {
        if ($status_post === 0 || ($status_post === 1 && $status_follow === 1)) {
            $check = TRUE;
        }
        if ($check == TRUE) {
            if ($db->addReport($_SESSION['id'], $_GET['id'], $type, $date)) {
                $_SESSION['flag'] = 1;
                $_SESSION['msg'] = 'Report thành công!';
                $check_show = TRUE;
            }
        }
    }
    if (!$check_show) {
        $_SESSION['flag'] = 2;
        $_SESSION['msg'] = 'Report thất bại!';
    }
}

require_once('View/report.php');
?>