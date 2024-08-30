<?php
include_once "../include_files.php";

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['resumeId'])) {

        $resumeId = $_GET['resumeId'];

        $data = array();

        $data = search_comment_for_resume($resumeId);

        echo json_encode($data);
    } else {
        $response = [
            'error' => 'Missing parameters resumeId',
        ];
        echo json_encode($response);
    }
}else {
    $response = [
        'error' => 'Invalid request method',
    ];
    echo json_encode($response);
}

function search_comment_for_resume($searchTerm = '')
{
    $query = "select *  from " . JOBSEEKER_RATING_TABLE . " as rate 
                    WHERE (rate.resume_id = '$searchTerm')";

    $output = array();

    $res = tep_db_query($query);

    while ($data = tep_db_fetch_array($res)) {
        $output[] = $data;
    }

    tep_db_free_result($res);

    return $output;
}
