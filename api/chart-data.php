<?php
include_once "../include_files.php";

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (check_login('admin') && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $interval = isset($_GET['interval']) ? $_GET['interval'] : 'monthly';

    if ($_GET['type'] == 'sales') {
        $jobseekerData = generateSalesData($interval, "jobseeker_orders", "jobseeker_orders_total");
        $recruiterData = generateSalesData($interval, "orders", "orders_total");
    } else {
        $jobseekerData = generateData($interval, "jobseeker_login");
        $recruiterData = generateData($interval, "recruiter_login");
    }

    $data = array("jobseekers" => $jobseekerData, "recruiters" => $recruiterData);

    // Set the appropriate HTTP header to indicate JSON content
    header('Content-Type: application/json');

    echo json_encode($data);
} else {
    $response = [
        'error' => 'Invalid request method or Authorization Failed!',
    ];
    echo json_encode($response);
}

function generateData($interval, $tableName)
{
    $year = date('Y');
    $current_month = date('m');
    $today_date_num = date("d");

    switch ($interval) {
        case 'weekly':
            $data = array();
            $data = memberWeeklyReport();

            return $data;
            break;
        case 'daily':
            $data = array();
            for ($i = 1; $i <= $today_date_num; $i++) {
                $start_date = date("Y-m-d", mktime(0, 0, 0, $current_month, $i, $year));
                $query = "SELECT 
                                DATE(inserted) AS registration_date,
                                COUNT(*) AS registrations_count
                            FROM 
                                $tableName
                            WHERE DATE(inserted) = '$start_date' 
                            GROUP BY 
                                DATE(inserted)
                            ORDER BY 
                                registration_date";
                $res = tep_db_query($query);
                if (tep_db_num_rows($res) > 0) {
                    $row = tep_db_fetch_array($res);
                    $reg_date = date("m-d", strtotime($row['registration_date']));
                    $total_val = $row['registrations_count'];
                } else {
                    $reg_date = date("m-d", strtotime($start_date));
                    $total_val = 0;
                }

                $data[] = array("day" => $reg_date, "count" => $total_val);
            }
            return $data;
            break;
        case 'monthly':
            $data = array();

            for ($i = 0; $i < 12; $i++) {
                $month = $current_month - $i;
                $year_offset = 0;
            
                if ($month <= 0) {
                    $month += 12;
                    $year_offset = 1;
                }
            
                $start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year - $year_offset));
                $end_date = date("Y-m-t H:i:s", mktime(0, 0, 0, $month, 1, $year - $year_offset));
                $curr_month = date("M", mktime(0, 0, 0, $month, 1, $year - $year_offset));
            
                $query = "SELECT YEAR(jl.inserted) AS year, MONTH(jl.inserted) AS month, COUNT(jl.inserted) AS login_count
                          FROM $tableName AS jl
                          WHERE jl.inserted >= '$start_date'
                          AND jl.inserted <= '$end_date'";
            
                $res = tep_db_query($query);
            
                if (tep_db_num_rows($res) > 0) {
                    $row = tep_db_fetch_array($res);
                    $total_val = $row['login_count'];
                } else {
                    $total_val = 0;
                }
            
                // $data[] = array(
                //     "month" => $curr_month,
                //     "year" => $row['year'],
                //     "count" => $total_val
                // );
                array_unshift($data, array(
                    "month" => $curr_month,
                    "year" => $row['year'],
                    "count" => $total_val
                ));
            }
            return $data;
            break;
        default:
            return array();
    }
}

function generateSalesData($interval, $tableName1, $tableName2)
{
    $year = date('Y');
    $current_month = date('m');
    $today_date_num = date("d");

    switch ($interval) {
        case 'weekly':
            $data = array();
            $data = salesMemberWeeklyReport($tableName1, $tableName2);

            return $data;
            break;
        case 'daily':
            $data = array();
            for ($i = 1; $i <= $today_date_num; $i++) {
                $start_date = date("Y-m-d", mktime(0, 0, 0, $current_month, $i, $year));
                $query = "SELECT DATE(o.orders_date_finished) AS day, SUM(ot.value) AS total_value
                            FROM $tableName1 AS o
                            LEFT JOIN $tableName2 AS ot ON ot.orders_id = o.orders_id
                            WHERE o.orders_status = 3 
                                    AND ot.class = 'ot_total'
                                    AND DATE(o.orders_date_finished) = '$start_date'
                                    GROUP BY DATE(o.orders_date_finished)
                            ORDER BY day";

                $res = tep_db_query($query);

                if (tep_db_num_rows($res) > 0) {
                    $row = tep_db_fetch_array($res);
                    $reg_date = date("m-d", strtotime($row['day']));
                    $total_val = $row['total_value'];
                } else {
                    $reg_date = date("m-d", strtotime($start_date));
                    $total_val = 0;
                }

                $data[] = array("day" => $reg_date, "count" => $total_val);
            }
            return $data;
            break;
        case 'monthly':
            $data = array();

            for ($i = 0; $i < 12; $i++) {
                $month = $current_month - $i;
                $year_offset = 0;

                if ($month <= 0) {
                    $month += 12;
                    $year_offset = 1;
                }
                
                $start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year - $year_offset));
                $end_date = date("Y-m-t H:i:s", mktime(0, 0, 0, $month, 1, $year - $year_offset));
                $curr_month = date("M", mktime(0, 0, 0, $month, 1, $year - $year_offset));

                $query = "SELECT MONTH(o.orders_date_finished), YEAR(o.orders_date_finished) AS year, 
                            SUM(ot.value) AS total_value
                          FROM $tableName1 AS o
                          LEFT JOIN $tableName2 AS ot ON ot.orders_id = o.orders_id
                          WHERE o.orders_status = 3 
                          AND ot.class = 'ot_total'
                          AND o.orders_date_finished >= '$start_date'
                          AND o.orders_date_finished <= '$end_date'";

                $res = tep_db_query($query);

                if (tep_db_num_rows($res) > 0) {
                    $row = tep_db_fetch_array($res);
                    $total_sales = $row['total_value'];
                } else {
                    $total_sales = 0;
                }

                // $data[] = array(
                //     "month" => $curr_month,
                //     "year" => $row['year'],
                //     "count" => $total_sales
                // );
                array_unshift($data, array(
                    "month" => $curr_month,
                    "year" => $row['year'],
                    "count" => $total_sales
                ));
            }
            return $data;
            break;
        default:
            return array();
    }
}

function memberWeeklyReport()
{
    $year = date('Y');
    $jobseekerQuery = "SELECT DATE_FORMAT(inserted, '%Y-%u') AS week_year,
                        DATE_FORMAT(inserted, '%Y-%m-%d') AS week_date,
                        DATE_FORMAT(inserted, '%u') AS week, 
                        COUNT(jobseeker_id) AS total_jobseekers
                       FROM jobseeker_login
                       WHERE YEAR(inserted) = $year
                       GROUP BY week_year
                       ORDER BY week_date";
    $recruiterQuery = "SELECT DATE_FORMAT(inserted, '%Y-%u') AS week_year,
                        DATE_FORMAT(inserted, '%Y-%m-%d') AS week_date, 
                        DATE_FORMAT(inserted, '%u') AS week,
                        COUNT(recruiter_id) AS total_recruiters
                       FROM recruiter_login
                       WHERE YEAR(inserted) = $year
                       GROUP BY week_year
                       ORDER BY week_date";

    // Execute jobseeker query
    $jobseekerResult = tep_db_query($jobseekerQuery);
    $jobseekerData = [];
    while ($row = tep_db_fetch_array($jobseekerResult)) {
        $jobseekerData[$row['week']] = [
            'total_jobseekers' => $row['total_jobseekers'],
            'week_date' => $row['week_date']
        ];
    }

    // Execute recruiter query
    $recruiterResult = tep_db_query($recruiterQuery);
    $recruiterData = [];
    while ($row = tep_db_fetch_array($recruiterResult)) {
        $recruiterData[$row['week']] = [
            'total_recruiters' => $row['total_recruiters'],
            'week_date' => $row['week_date']
        ];
    }

    // Combine data from both jobseekers and recruiters
    $combinedData = [];
    $allWeeks = array_unique(array_merge(array_keys($jobseekerData), array_keys($recruiterData)));
    sort($allWeeks);

    foreach ($allWeeks as $week) {
        $week_date = $jobseekerData[$week]['week_date'] ?? $recruiterData[$week]['week_date'];
        $formattedDate = date("m-d", strtotime($week_date));
        $combinedData[] = [
            'week_number' => $week,
            'week' => $formattedDate,
            'jobseeker_count' => $jobseekerData[$week]['total_jobseekers'] ?? 0,
            'recruiter_count' => $recruiterData[$week]['total_recruiters'] ?? 0
        ];
    }

    return $combinedData;
}

function salesMemberWeeklyReport($tableName1, $tableName2)
{
    $year = date('Y');
    $query = "SELECT DATE_FORMAT(o.orders_date_finished, '%Y-%u') AS week_year,
                    DATE_FORMAT(o.orders_date_finished, '%Y-%m-%d') AS week_date,
                    DATE_FORMAT(o.orders_date_finished, '%u') AS week,
                    SUM(ot.value) AS total_sales
                FROM $tableName1 AS o
                LEFT JOIN $tableName2 AS ot ON ot.orders_id = o.orders_id
                WHERE YEAR(o.orders_date_finished) = $year
                    AND o.orders_status = 3 
                    AND ot.class = 'ot_total'
                GROUP BY week_year
                ORDER BY week_date";

    $res = tep_db_query($query);
    while ($row = tep_db_fetch_array($res)) {
        $formattedDate = date("m-d", strtotime($row['week_date']));

        $data[] = array("week" => $formattedDate, "count" => $row['total_sales']);
    }
    return $data;
}
