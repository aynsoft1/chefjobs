<?php
include_once "../include_files.php";

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!check_login('recruiter')) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = isset($_GET['q']) ? $_GET['q'] : 'totalNotification';
    $total = 0;

    switch ($q) {
        case 'totalNotification':
            $total = getTotalNotification();
            break;
        case 'markAllRead':
            markAllReadNotification();
            $total = getTotalNotification();
            break;
        default:
            echo json_encode(['error' => 'Invalid query parameter']);
            exit;
    }

    $response = [
        'total' => $total,
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo 'hello world!';
    exit;
}

function getTotalNotification()
{
    $query = "SELECT COUNT(an.id) AS total FROM `recruiter_notifications` AS an WHERE an.is_read = '0'";
    $res = tep_db_query($query);
    $row = tep_db_fetch_array($res);
    return $row['total'];
}

function markAllReadNotification()
{
    $query = "UPDATE recruiter_notifications SET is_read = 1 WHERE id IN (SELECT id FROM (SELECT id FROM recruiter_notifications WHERE is_read = 0) as temp)";
    $res = tep_db_query($query);
    return $res;
}
