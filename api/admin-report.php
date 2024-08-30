<?php
include_once "../include_files.php";

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'last_30_days'; // Default filter
    $dataType = isset($_GET['data_type']) ? $_GET['data_type'] : 'employer';

    // Get the page and limit parameters from the request
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;

    if ($dataType === 'job') {
        $data = generateJobPerformanceData($filter, $limit, $offset);
        $total = getTotalJobs($filter);
    } elseif ($dataType === 'jobseeker') {
        $data = generateJobseekerPerformanceData($filter, $limit, $offset);
        $total = getTotalJobseeker($filter);
    } elseif ($dataType === 'orders') {
        $data = generateOrderPerformanceData($filter, $limit, $offset);
        $total = getTotalOrders($filter);
    } else {
        $data = generateEmployerPerformanceData($filter, $limit, $offset);
        $total = getTotalEmployers($filter);
    }

    // Determine if there is a next page
    $isNext = $offset + $limit < $total;
    // Determine if there is a previous page
    $isPrev = $page > 1;

    $response = [
        'data' => $data,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'isNext' => $isNext,
        'isPrev' => $isPrev,
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = [
        'error' => 'Invalid request method or Authorization Failed!',
    ];
    echo json_encode($response);
}

function generateEmployerPerformanceData($filter, $limit, $offset)
{
    $data = array();

    switch ($filter) {
        case 'today':
            $dateCondition = "AND DATE(rl.inserted) = CURDATE()";
            break;
        case 'last_7_days':
            $dateCondition = "AND rl.inserted >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last_30_days':
        default:
            $dateCondition = "AND rl.inserted >= CURDATE() - INTERVAL 30 DAY";
            break;
    }

    $query = "SELECT 
                r.recruiter_id, 
                CONCAT(r.recruiter_first_name, ' ', r.recruiter_last_name) AS employer_name,
                rl.recruiter_email_address, 
                rl.recruiter_status,
                COUNT(DISTINCT j.job_id) AS total_job_posted,
                SUM(jstats.viewed) AS total_viewed,
                SUM(jstats.clicked) AS total_clicks,
                SUM(jstats.applications) AS total_applications
            FROM 
                `recruiter` AS r 
            LEFT JOIN 
                recruiter_login AS rl ON rl.recruiter_id = r.recruiter_id
            LEFT JOIN 
                jobs AS j ON j.recruiter_id = r.recruiter_id
            LEFT JOIN 
                job_statistics_day AS jstats ON jstats.job_id = j.job_id
            WHERE
                1 $dateCondition
            GROUP BY 
                r.recruiter_id, 
                employer_name, 
                rl.recruiter_email_address, 
                rl.recruiter_status
            ORDER BY 
                rl.inserted DESC
            LIMIT $limit OFFSET $offset";

    $res = tep_db_query($query);

    while ($row = tep_db_fetch_array($res)) {
        $data[] = array(
            "employer_name" => $row['employer_name'],
            "recruiter_email_address" => $row['recruiter_email_address'],
            "total_job_posted" => $row['total_job_posted'],
            "total_viewed" => $row['total_viewed'],
            "total_applications" => $row['total_applications'],
            "total_clicks" => $row['total_clicks'],
        );
    }

    return $data;
}

function getTotalEmployers($filter)
{
    switch ($filter) {
        case 'today':
            $dateCondition = "AND DATE(rl.inserted) = CURDATE()";
            break;
        case 'last_7_days':
            $dateCondition = "AND rl.inserted >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last_30_days':
        default:
            $dateCondition = "AND rl.inserted >= CURDATE() - INTERVAL 30 DAY";
            break;
    }
    $query = "SELECT COUNT(DISTINCT rl.recruiter_id) AS total FROM `recruiter_login` AS rl WHERE 1 $dateCondition";
    $res = tep_db_query($query);
    $row = tep_db_fetch_array($res);
    return $row['total'];
}

function generateJobPerformanceData($filter, $limit, $offset)
{
    $data = array();

    $endDate = new DateTime(); // Today's date
    $startDate = new DateTime();
    switch ($filter) {
        case 'today':
            $startDate->setTime(0, 0);
            break;
        case 'last_7_days':
            $startDate->modify('-7 days');
            break;
        case 'last_30_days':
        default:
            $startDate->modify('-30 days');
            break;
    }

    $startDateFormatted = $startDate->format('Y-m-d');
    $endDateFormatted = $endDate->modify('+1 day')->format('Y-m-d'); // include today

    $query = "SELECT
                    j.job_title, j.job_id, j.inserted,
                    CONCAT(r.recruiter_first_name, ' ', r.recruiter_last_name) AS employer_full_name,
                    SUM(jsd.viewed) AS job_views,
                    SUM(jsd.applications) AS applications,
                    SUM(jsd.clicked) AS apply_clicks
                FROM
                    jobs AS j
                LEFT JOIN
                    recruiter AS r ON j.recruiter_id = r.recruiter_id
                LEFT JOIN
                    job_statistics_day AS jsd ON j.job_id = jsd.job_id
                WHERE
                    DATE(j.inserted) BETWEEN '$startDateFormatted' AND '$endDateFormatted'
                GROUP BY
                    j.job_id, j.job_title, r.recruiter_first_name, r.recruiter_last_name
                ORDER BY
                    j.inserted DESC
                LIMIT $limit OFFSET $offset";

    $res = tep_db_query($query);

    while ($row = tep_db_fetch_array($res)) {
        $data[] = array(
            "job_title" => $row['job_title'],
            "inserted" => $row['inserted'],
            "employer_full_name" => $row['employer_full_name'],
            "total_viewed" => $row['job_views'],
            "total_applications" => $row['applications'],
            "total_clicks" => $row['apply_clicks'],
        );
    }

    return $data;
}

function getTotalJobs($filter)
{
    $endDate = new DateTime(); // Today's date
    $startDate = new DateTime();
    switch ($filter) {
        case 'today':
            $startDate->setTime(0, 0);
            break;
        case 'last_7_days':
            $startDate->modify('-7 days');
            break;
        case 'last_30_days':
        default:
            $startDate->modify('-30 days');
            break;
    }
    $startDateFormatted = $startDate->format('Y-m-d');
    $endDateFormatted = $endDate->modify('+1 day')->format('Y-m-d'); // include today

    $query = "SELECT COUNT(DISTINCT j.job_id) AS total FROM `jobs` AS j WHERE DATE(j.inserted) BETWEEN '$startDateFormatted' AND '$endDateFormatted'";
    $res = tep_db_query($query);
    $row = tep_db_fetch_array($res);
    return $row['total'];
}

function generateJobseekerPerformanceData($filter, $limit, $offset)
{
    $data = array();

    switch ($filter) {
        case 'today':
            $dateCondition = "AND DATE(jl.inserted) = CURDATE()";
            break;
        case 'last_7_days':
            $dateCondition = "AND jl.inserted >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last_30_days':
        default:
            $dateCondition = "AND jl.inserted >= CURDATE() - INTERVAL 30 DAY";
            break;
    }

    $query = "SELECT 
                js.jobseeker_id, 
                CONCAT(js.jobseeker_first_name, ' ', js.jobseeker_last_name) AS jobseeker_name,
                jl.jobseeker_email_address, 
                jl.jobseeker_status,
                COUNT(DISTINCT jr.resume_id) AS total_resume_posted,
                SUM(rstats.viewed) AS total_viewed,
                SUM(rstats.clicked) AS total_clicks
            FROM 
                `jobseeker` AS js 
            LEFT JOIN 
                jobseeker_login AS jl ON jl.jobseeker_id = js.jobseeker_id 
            LEFT JOIN 
                jobseeker_resume1 AS jr ON jr.jobseeker_id = js.jobseeker_id
            LEFT JOIN 
                resume_statistics_day AS rstats ON rstats.resume_id = jr.resume_id
            WHERE
                1 $dateCondition
            GROUP BY 
                js.jobseeker_id, 
                jobseeker_name, 
                jl.jobseeker_email_address, 
                jl.jobseeker_status
            ORDER BY 
                jl.inserted DESC
            LIMIT $limit OFFSET $offset";

    $res = tep_db_query($query);

    while ($row = tep_db_fetch_array($res)) {
        $data[] = array(
            "jobseeker_name" => $row['jobseeker_name'],
            "jobseeker_email_address" => $row['jobseeker_email_address'],
            "total_resume_posted" => $row['total_resume_posted'],
            "total_viewed" => $row['total_viewed'],
            "total_clicks" => $row['total_clicks'],
        );
    }

    return $data;
}

function getTotalJobseeker($filter)
{
    switch ($filter) {
        case 'today':
            $dateCondition = "AND DATE(jl.inserted) = CURDATE()";
            break;
        case 'last_7_days':
            $dateCondition = "AND jl.inserted >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last_30_days':
        default:
            $dateCondition = "AND jl.inserted >= CURDATE() - INTERVAL 30 DAY";
            break;
    }
    $query = "SELECT COUNT(DISTINCT jl.jobseeker_id) AS total FROM `jobseeker_login` AS jl WHERE 1 $dateCondition";
    $res = tep_db_query($query);
    $row = tep_db_fetch_array($res);
    return $row['total'];
}

function generateOrderPerformanceData($filter, $limit, $offset)
{
    $data = array();

    switch ($filter) {
        case 'jobseeker':
            $query = "SELECT 
                            o.orders_id AS order_id,
                            o.jobseeker_name AS name,
                            o.payment_method,
                            o.date_purchased,
                            o.last_modified,
                            o.currency,
                            o.currency_value,
                            s.orders_status_name,
                            ot.text AS order_total
                        FROM 
                            jobseeker_orders AS o
                        LEFT JOIN 
                            jobseeker_orders_total AS ot 
                            ON o.orders_id = ot.orders_id
                        LEFT JOIN 
                            orders_status AS s 
                            ON o.orders_status = s.orders_status_id
                        WHERE 
                            s.language_id = '1' 
                            AND ot.class = 'ot_total'
                        ORDER BY 
                            o.orders_id DESC
                        LIMIT $limit OFFSET $offset";
            break;
        case 'recruiter':
        default:
            $query = "SELECT 
                            o.orders_id AS order_id,
                            o.recruiter_name AS name,
                            o.payment_method,
                            o.date_purchased,
                            o.last_modified,
                            o.currency,
                            o.currency_value,
                            s.orders_status_name,
                            ot.text AS order_total
                        FROM 
                            orders AS o
                        LEFT JOIN 
                            orders_total AS ot 
                            ON o.orders_id = ot.orders_id
                        LEFT JOIN 
                            orders_status AS s 
                            ON o.orders_status = s.orders_status_id
                        WHERE 
                            s.language_id = '1' 
                            AND ot.class = 'ot_total'
                        ORDER BY 
                            o.orders_id DESC
                        LIMIT $limit OFFSET $offset";
            break;
    }

    $res = tep_db_query($query);

    while ($row = tep_db_fetch_array($res)) {
        $orderTotal = strip_tags($row['order_total']);
        $type = $filter;
        $data[] = array(
            "orders_id" => $row['order_id'],
            "name" => $row['name'],
            "type" => $type,
            "total" => $orderTotal,
            "status" => $row['orders_status_name'],
        );
    }

    return $data;
}

function getTotalOrders($filter)
{
    switch ($filter) {
        case 'jobseeker':
            $query = "SELECT COUNT(DISTINCT jo.orders_id) AS total FROM `jobseeker_orders` AS jo";
            break;
        case 'recruiter':
        default:
            $query = "SELECT COUNT(DISTINCT ro.orders_id) AS total FROM `orders` AS ro";
            break;
    }
    $res = tep_db_query($query);
    $row = tep_db_fetch_array($res);
    return $row['total'];
}
