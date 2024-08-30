<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../include_files.php";

// Set content type to JSON
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if ($q !== '') {
    $q = tep_db_input($q);

    $query = tep_db_query("
    SELECT 
        r.recruiter_id,
        r.recruiter_company_name,
        rl.recruiter_email_address,
        (CASE 
            WHEN rah.plan_for = 'job_post' 
            AND rah.recruiter_job_status = 'Yes' 
            AND rah.job_enjoyed < rah.recruiter_job 
            AND NOW() BETWEEN rah.start_date AND rah.end_date 
            THEN 1 ELSE 0 
        END) AS can_post_job
    FROM 
        recruiter r
    JOIN 
        recruiter_login rl ON r.recruiter_id = rl.recruiter_id
    LEFT JOIN 
        recruiter_account_history rah ON r.recruiter_id = rah.recruiter_id
    WHERE 
        r.recruiter_company_name LIKE '%$q%'
        AND rah.id = (
            SELECT 
                MAX(rah2.id)
            FROM 
                recruiter_account_history rah2
            WHERE 
                rah2.recruiter_id = r.recruiter_id
        )
    LIMIT 10
");


    $recruiters = [];
    while ($row = tep_db_fetch_array($query)) {
        $recruiters[] = $row;
    }

    echo json_encode($recruiters);
}
?>
