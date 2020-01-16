<?php
    switch($_GET['action']) {
        case 'follow':
        case 'unfollow':
            if (isset($_GET['user'])) {
                $user_id = $db->getUserIDByUsername($_GET['user']);
                if ($user_id!==NULL && $user_id!==FALSE && $user_id!==$_SESSION['id']) {
                    $status_follow = $db->getStatusFollow($user_id, $_SESSION['id']);
                    $date = date('Y-m-d h:i:s', time());
                    if (strcmp($_GET['action'],'follow') === 0) {
                        if ($status_follow == -1) {
                            if ($db->addFollow($user_id, $_SESSION['id'], $date)) {
                                echo 'Waiting|unfollow';
                            }
                        } else if ($status_follow == 2) {
                            if ($db->updateFollow($user_id, $_SESSION['id'], 0, $date)) {
                                echo 'Waiting|unfollow';
                            }
                        }
                    } else if (strcmp($_GET['action'], 'unfollow') === 0) {
                        if ($status_follow == 1 || $status_follow == 0) {
                            if ($db->updateFollow($user_id, $_SESSION['id'], 2, $date)) {
                                echo 'Follow|follow';
                            }
                        }
                    }
                }
            }
            break;
        case 'approve':
            if (isset($_GET['user'])) {
                $user_id = $db->getUserIDByUsername($_GET['user']);
                $date = date('Y-m-d h:i:s', time());
                if ($user_id!==NULL && $user_id!==FALSE && $user_id!==$_SESSION['id']) {
                    if ($db->updateFollow($_SESSION['id'], $user_id, 1, $date)) {
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                }
            }
            break;
        case 'reject':
            if (isset($_GET['user'])) {
                $user_id = $db->getUserIDByUsername($_GET['user']);
                $date = date('Y-m-d h:i:s', time());
                if ($user_id!==NULL && $user_id!==FALSE && $user_id!==$_SESSION['id']) {
                    if ($db->updateFollow($_SESSION['id'], $user_id, 2, $date)) {
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                }
            }
            break;
    }
?>