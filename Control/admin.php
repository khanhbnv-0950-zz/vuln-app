<?php
$reports = $db->getReports();
// var_dump($reports);

if (isset($_GET['action']) && $_GET['id']) {
    $date = date('Y-m-d h:i:s', time());
    $status = -1;
    if ($_GET['action'] == 'approve') {
        $status = 1;
    } else if ($_GET['action'] == 'reject') {
        $status = 2;
    }
    if ($status != -1) {
        $db->updateReportById($_GET['id'], $status, $date);
    }
    header('Location: ./?page=admin');
}

require_once('View/admin.php');
?>