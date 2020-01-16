<?php
session_start();
if (!isset($_SESSION['logged'])){
    $_SESSION['logged'] = FALSE;
}
if (!isset($_SESSION['flag'])) {
    $_SESSION['flag'] = -1;
}
if (!isset($_SESSION['msg'])) {
    $_SESSION['msg'] = '';
}
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = '';
}
if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}
?>