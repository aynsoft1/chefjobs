<?php
include_once "../include_files.php";

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (check_login('recruiter') && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $recruiterId = $_SESSION['sess_recruiterid'];
    $type = isset($_GET['type']) ? $_GET['type'] : 'application';
    $currentMonth = isset($_GET['month']) ? $_GET['month'] : intval(date('m'));
    $currentYear = isset($_GET['year']) ? $_GET['year'] : intval(date('Y'));

    $data = generateData($type, $recruiterId, $currentMonth, $currentYear);

    header('Content-Type: application/json');

    echo json_encode($data);
}else if(check_login('jobseeker') && $_SERVER['REQUEST_METHOD'] === 'GET'){
    $jobseekerId = $_SESSION['sess_jobseekerid'];
    $type = isset($_GET['type']) ? $_GET['type'] : 'clicks';
    $currentMonth = isset($_GET['month']) ? $_GET['month'] : intval(date('m'));
    $currentYear = isset($_GET['year']) ? $_GET['year'] : intval(date('Y'));

    $data = generateJobseekerData($type, $jobseekerId, $currentMonth, $currentYear);

    header('Content-Type: application/json');

    echo json_encode($data);
} else {
    $response = [
        'error' => 'Invalid request method or Authorization Failed!',
    ];
    echo json_encode($response);
}

function generateData($type, $recruiterId, $month, $year)
{
    switch ($type) {
        case 'application':
            $data = array();
            // $query = "SELECT j.job_id AS jobID, 
            //                 j.job_title, 
            //                 j.recruiter_id,
            //                 COUNT(a.id) as total_applications,
            //                 MONTH(a.inserted) AS month,
            //                 YEAR(a.inserted) AS year
            //             FROM jobs AS j
            //             LEFT JOIN application AS a ON j.job_id = a.job_id AND j.recruiter_id = $recruiterId
            //             WHERE MONTH(a.inserted) = $month
            //                 AND YEAR(a.inserted) = $year
            //             GROUP BY j.job_id, month, year
            //             ORDER BY total_applications DESC
            //             LIMIT 7";
            $query = "SELECT job_stats.job_id, j.job_title, 
                                SUM(job_stats.applications) AS total_applications, 
                                SUM(job_stats.viewed) AS total_viewed, 
                                SUM(job_stats.clicked) AS total_clicked
                        FROM `job_statistics_day` AS job_stats
                        LEFT JOIN jobs AS j ON j.job_id = job_stats.job_id
                        WHERE MONTH(job_stats.date) = $month 
                                AND YEAR(job_stats.date) = $year
                                AND j.recruiter_id = $recruiterId
                        GROUP BY job_stats.job_id, j.job_title
                        ORDER BY SUM(job_stats.applications) DESC
                        LIMIT 7";

            $res = tep_db_query($query);

            while ($row = tep_db_fetch_array($res)) {
                $data[] = array("label" => $row['job_title'], "values" => $row['total_applications']);
            }
            return $data;
            break;
        case 'clicks':
            $data = array();

            $query = "SELECT job_stats.job_id, j.job_title, 
                                SUM(job_stats.applications) AS total_applications, 
                                SUM(job_stats.viewed) AS total_viewed, 
                                SUM(job_stats.clicked) AS total_clicked
                        FROM `job_statistics_day` AS job_stats
                        LEFT JOIN jobs AS j ON j.job_id = job_stats.job_id
                        WHERE MONTH(job_stats.date) = $month 
                                AND YEAR(job_stats.date) = $year
                                AND j.recruiter_id = $recruiterId
                        GROUP BY job_stats.job_id, j.job_title
                        ORDER BY SUM(job_stats.clicked) DESC
                        LIMIT 7";

            $res = tep_db_query($query);
            while ($row = tep_db_fetch_array($res)) {
                $data[] = array("label" => $row['job_title'], "values" => $row['total_clicked']);
            }
            return $data;
            break;
        case 'impressions':
            $data = array();
            $query = "SELECT job_stats.job_id, j.job_title, 
                            SUM(job_stats.applications) AS total_applications, 
                            SUM(job_stats.viewed) AS total_viewed, 
                            SUM(job_stats.clicked) AS total_clicked
                    FROM `job_statistics_day` AS job_stats
                    LEFT JOIN jobs AS j ON j.job_id = job_stats.job_id
                    WHERE MONTH(job_stats.date) = $month 
                            AND YEAR(job_stats.date) = $year
                            AND j.recruiter_id = $recruiterId
                    GROUP BY job_stats.job_id, j.job_title
                    ORDER BY SUM(job_stats.viewed) DESC
                    LIMIT 7";

            $res = tep_db_query($query);
            while ($row = tep_db_fetch_array($res)) {
                $data[] = array("label" => $row['job_title'], "values" => $row['total_viewed']);
            }
            return $data;
            break;
        default:
            return array();
    }
}

function generateJobseekerData($type, $jobseekerId, $month, $year)
{
    switch ($type) {
        case 'clicks':
            $data = array();
            // $query = "SELECT resume_stats.resume_id, jr.resume_title, 
            //                     SUM(resume_stats.clicked) AS total_clicked
            //             FROM `resume_statistics_day` AS resume_stats
            //             LEFT JOIN jobseeker_resume1 AS jr ON jr.resume_id = resume_stats.resume_id
            //             WHERE MONTH(resume_stats.date) = $month 
            //                     AND YEAR(resume_stats.date) = $year
            //                     AND jr.jobseeker_id = $jobseekerId
            //             GROUP BY resume_stats.resume_id, jr.resume_title
            //             ORDER BY SUM(resume_stats.clicked) DESC
            //             LIMIT 7";
            $data[] = array("label" => 'No Data', "values" => '0');
            // $res = tep_db_query($query);
            // while ($row = tep_db_fetch_array($res)) {
            //     $data[] = array("label" => $row['resume_title'], "values" => $row['total_clicked']);
            // }
            return $data;
            break;
        case 'impressions':
            $data = array();
            $query = "SELECT resume_stats.resume_id, jr.resume_title, 
                                SUM(resume_stats.viewed) AS total_viewed
                        FROM `resume_statistics_day` AS resume_stats
                        LEFT JOIN jobseeker_resume1 AS jr ON jr.resume_id = resume_stats.resume_id
                        WHERE MONTH(resume_stats.date) = $month 
                                AND YEAR(resume_stats.date) = $year
                                AND jr.jobseeker_id = $jobseekerId
                        GROUP BY resume_stats.resume_id, jr.resume_title
                        ORDER BY SUM(resume_stats.viewed) DESC
                        LIMIT 7";

            $res = tep_db_query($query);
            while ($row = tep_db_fetch_array($res)) {
                $data[] = array("label" => $row['resume_title'], "values" => $row['total_viewed']);
            }
            return $data;
            break;
        default:
            return array();
    }
}
