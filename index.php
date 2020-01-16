<?php
require_once('Config/DBconfig.php');
require_once('Config/config.php');
require_once('Model/DB.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');
$db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// echo bin2hex(random_bytes(32));
$_SESSION['csrf'] = bin2hex(random_bytes(32));
if (isset($_GET['page'])) {
    switch ($_GET['page']) {
        case 'signup':
            require_once('Control/signup.php');
            break;
        case 'signin':
            require_once('Control/signin.php');
            break;
        case 'home':
            if (!$_SESSION['logged']){
                header('Location: ./?page=index');
            }
            require_once('Control/logged.php');
            break;
        case 'profile':
            if (!isset($_SESSION['logged']) || !$_SESSION['logged']) {
                header('Location: .');
            }
            require_once('Control/profile.php');
            break;
        case 'report':
            if (!isset($_SESSION['logged']) || !$_SESSION['logged']){
                header('Location: ./?page=index');
            }
            require_once('Control/report.php');
            break;
        case 'admin':
            if (!isset($_SESSION['logged']) || !$_SESSION['logged']){
                header('Location: ./?page=index');
            }
            if ($_SESSION['isAdmin'] == 0) {
                header('Location: ./?page=home');
            }
            require_once('Control/admin.php');
            break;
        case 'signout':
            session_destroy();
            header('Location: ./?page=index');
            break;
        case 'vuln-app':
            echo '<center>';
            echo '<h1>Hello World</h1>';
            echo '<h2>This is vuln-app</h2>';
            echo '</center>';
        case 'index':
            require_once('View/index.php');
            break;
        default:
            if (isset($_SESSION['logged']) || $_SESSION['logged']) {
                header('Location: ./?page=home');
            }
            require_once('View/index.php');
        break; 
    }
} else if (isset($_GET['api'])) {
    switch ($_GET['api']) {
        case 'like':
            require_once('Control/api/like.php');
            break;
        case 'follow':
            require_once('Control/api/follow.php');
            break;
        case 'request-follow':
            require_once('Control/api/follow.php');
            break;
        default:
            echo '<center>';
            echo '<h1>Hello World</h1>';
            echo '<h2>This is vuln-app</h2>';
            echo '</center>';
            // require_once('View/index.php');
    }
} else {
    if (isset($_SESSION['logged']) || $_SESSION['logged']) {
        header('Location: ./?page=home');
    }
    echo '<center>';
    echo '<h1>Hello World</h1>';
    echo '<h2>This is vuln-app</h2>';
    echo '</center>';
    require_once('View/index.php');
}
?>