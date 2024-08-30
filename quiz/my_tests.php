<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_JOBSEEKER_TESTS);
$template->set_filenames(array(
    'my_tests'          => 'my_test/tests.htm',
    'latest_report'     => 'my_test/latest-report.htm',
    'list_tests'        => 'my_test/list_tests.htm',
    'list_video_lib'    => 'my_test/list-video-lib.htm',
    'invitation_list'   => 'my_test/invitation-list.htm',
));
include_once("../" . FILENAME_BODY);

$request = $_SERVER['REQUEST_METHOD'];
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$error = false;
$currentDate = date("Y-m-d H:i:s");

// assessment_id for request q
$q = (isset($_GET['q']) ? $_GET['q'] : '');

if ($_SESSION['language'] == "spanish") {
    $language = 'es';
} else {
    $language = 'en';
}


/**
 * check condition jobseeker is logged in or not
 * if jobseeker is not logged in then redirect to login page 
 * otherwise to next request
 */
if (!check_login('jobseeker')) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
} else {
    $jobseeker_id   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
}


// if rquest is present then decode the id
if (tep_not_null($q)) {
    $decode_assessmentID = check_data($_GET['q'],"==","assessment_id","assessment");
}


$template->assign_vars(array(
    'test_menus' => '
                        <a class="btn btn-sm btn-primary me-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS) . '">
                        ' . MY_TESTS . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=latest-report') . '">
                        ' . LATEST_REPORT . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_QUIZ_CALENDAR) . '">
                        ' . CALENDAR . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=video-library') . '">
                        ' . MY_VIDEO . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=invitation-link') . '">
                        ' . MY_INVITATION . '
                    </a>
                    
    ',
    'update_message' => $messageStack->output()
));


function getRandomColor()
{
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

function getQuizReportData($id)
{
    $result = getAnyTableWhereData(QUIZ_RESULT_TABLE, "id='" . tep_db_input($id) . "'");

    $message = ($result) ? $result['message'] : 'None';

    return $message;
}

function get_list_of_candidate_videos($user_id)
{
    global $template;
    // get the list of assign test
    $list_test_query = "SELECT video.*, assessments.title AS assessment, test.title AS test, job.job_title, recruiter.recruiter_company_name AS company
                    FROM quiz_videos AS video
                    INNER JOIN assessments ON assessments.id = video.assessment_id
                    INNER JOIN quizzes AS test ON test.id = video.quiz_id
                    INNER JOIN jobs AS job ON job.job_id = assessments.job_id
                    INNER JOIN recruiter ON assessments.creator_id = recruiter.recruiter_id
                    WHERE video.jobseeker_id = $user_id ORDER BY video.id DESC";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $video_path = tep_href_link($data['file_path']);
            $template->assign_block_vars('video_lib', array(
                // 'name'       => $data['file_name'],
                'name'       => '<video controls width="125"><source src="'.$video_path.'" type="video/webm"></video>',
                'file_path'  => $data['file_path'],
                'created_at' => tep_date_short($data['created_at']),
                'assessment' => $data['assessment'],
                'test'       => $data['test'],
                'job_title'  => $data['job_title'],
                'company'    => $data['company'],
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}

function get_the_list_of_jobseeker_invitaions($jobseeker_id) {
    global $template;

    $query = "SELECT assessment_invitemails.*, 
                jobseeker_login.jobseeker_email_address AS jobseeker_email, jobseeker_login.jobseeker_id,
                recruiter.recruiter_company_name AS company,
                assessments.title AS assessment_name
                FROM assessment_invitemails
                INNER JOIN jobseeker_login ON jobseeker_login.jobseeker_email_address = assessment_invitemails.email_to
                LEFT JOIN assessments ON assessments.id = assessment_invitemails.assessment_id
                LEFT JOIN recruiter ON recruiter.recruiter_id =assessment_invitemails.recruiter_id
                WHERE jobseeker_login.jobseeker_id = $jobseeker_id
                ORDER BY assessment_invitemails.id DESC";

    $res = tep_db_query($query);
    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {

            if ($data['accepted'] == 1) {
                $inviteStatus = 'accepted';
            } else {
                $inviteStatus = '<a href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_TEST_TAKER_INVITE_LINK,'
                                    action=takeinvitation&uuid='.$data['uuid']).'" target="_new" rel="noopener noreferrer">
                                    click here to accept
                                </a>';
            }

            $template->assign_block_vars('invitaiton_list', array(
                'assessment_name'   => $data['assessment_name'],
                'company'           => $data['company'],
                'status'            => $inviteStatus,
                'date'              => tep_date_short($data['created_at']),
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}

if ($action == 'latest-report') {
    // fetch latest report for member
    $field_names = "r.id, r.created_at as start, r.created_at as end, r.total_points, r.out_of, r.quiz_id, r.assessment_id, r.member_id, 
                    r.note, a.title as assessment_title, a.job_id, a.description, 
                    j.job_title,
                    q.title as quiz_title, 
                    js.jobseeker_first_name as firstName, js.jobseeker_last_name as lastName";
    
    $table_names = QUIZ_RESULT_TABLE . " as r
                LEFT OUTER JOIN ". ASSESSMENT_TABLE ." as a ON a.id = r.assessment_id
                LEFT OUTER JOIN ". JOB_TABLE ." as j ON j.job_id = a.job_id
                LEFT OUTER JOIN " . QUIZ_TABLE . " as q ON r.quiz_id = q.id 
                LEFT OUTER JOIN " . JOBSEEKER_TABLE . " as js ON js.jobseeker_id = r.member_id";

    $whereClause = "r.member_id = $jobseeker_id";
    
    $sql = "select $field_names from $table_names where $whereClause ORDER BY start ASC";

    $result = tep_db_query($sql);
    if (tep_db_num_rows($result) > 0) {
        while ($report = tep_db_fetch_array($result)) {
            
            extract($report);

            // Date format change
            $startDate = date_create($start);
            $endDate = date_create($start);
            $start = date_format($startDate, 'Y-m-d');
            $end = date_format($endDate, 'Y-m-d');

            $template->assign_block_vars('latest_report', array(
                'title' => $quiz_title,
                'job_name' => $job_title,
                'message' => getQuizReportData($id),
                'note'     => $note,
                'total_points' => $total_points,
                // 'start' => $start,
                'start' => tep_date_short($start),
                'out_of' => $out_of,
            ));
        }
        tep_db_free_result($result);
    }

    $template->assign_vars(array(
        'LATEST_REPORT' => LATEST_REPORT,
        'TEST_TITLE' => TEST_TITLE,
        'JOB_TITLE' => JOB_TITLE,
        'MESSAGE' => MESSAGE,
        'TOTAL_SCORE' => TOTAL_SCORE,
        'OUT_OF' => OUT_OF,
        'START' => START,
        'NOTE' => NOTE,
        'LANGUAGE' => $language,
    ));
    $template->pparse('latest_report');
} elseif ($q AND $action == 'take-test') {
    $no_table_data_found = null;
    $assessment_name = null;

    // check if assessment id not present then redirect to mytest page
    if (!$assessmentInfo = getAnyTableWhereData(ASSESSMENT_TABLE, "id='" . $decode_assessmentID . "'")) {
        $messageStack->add_session(MESSAGE_INVALID_ASSESSMENT_ERROR, 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS));
    }

    if (strtotime($assessmentInfo['expired_at']) < strtotime($currentDate)) {
        $messageStack->add_session(MESSAGE_ASSESSMENT_EXPIRE, 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS));
    }

    $my_test_query = "SELECT q.id, q.title, q.test_type, q.timer, ass.id as assessment_id, ass.title as assessment_name, asj.jobseeker_id as seeker_id, IF(res.id, 'Yes', 'No') as quiz_taken
                        FROM ".QUIZ_TABLE." as q
                        JOIN ".ASSESSMENT_QUIZ_TABLE." as aq ON aq.quiz_id = q.id
                        JOIN ".ASSESSMENT_TABLE." as ass ON ass.id = aq.assessment_id
                        JOIN ".ASSESSMENT_JOBSEEKER_TABLE." as asj ON asj.assessment_id = ass.id
                        LEFT JOIN ".QUIZ_RESULT_TABLE." as res ON (res.assessment_id = ass.id AND res.quiz_id = q.id AND res.member_id = asj.jobseeker_id)
                        WHERE ass.id = $decode_assessmentID AND asj.jobseeker_id = $jobseeker_id";

    $res = tep_db_query($my_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($l_test = tep_db_fetch_array($res)) {
            $encoded_assessmentID = encode_string("assessment_id==".$l_test['assessment_id']."==assessment");
            $encoded_testID = encode_string("test_id==".$l_test['id']."==test");
            $template->assign_block_vars('list_test', array(
                'title' => $l_test['title'],
                'status' => ($l_test['quiz_taken'] == 'Yes') ? 'Done' : 'Not taken yet',
                'timer' => $l_test['timer'],
                'assessment_name' => $l_test['assessment_name'],
                // 'start' => tep_link_button_Name(
                //     tep_href_link(PATH_TO_QUIZ . FILENAME_QUIZ_TOPICS, 'action=start_test&t_id=' . $encoded_testID .'&a_id='.$encoded_assessmentID),
                //     'btn btn-primary ',
                //     TAKE_TEST,
                //     ''
                //   ),
                'start' => $l_test['quiz_taken'] != 'Yes' ? tep_link_button_Name(
                    tep_href_link(PATH_TO_QUIZ . FILENAME_QUIZ_TOPICS, 'action=start_test&q='.$l_test['test_type'].'&t_id=' . $encoded_testID .'&a_id='.$encoded_assessmentID),
                    'btn btn-primary ',
                    TAKE_TEST,
                    ''
                  ) : TEST_TAKEN,
            ));

            $assessment_name = $l_test['assessment_name'];
        }
        tep_db_free_result($res);
    } else {
        $no_table_data_found = '<tr><td class="text-center" colspan="3">No data found</td></tr>';
    }

    $template->assign_vars(array(
        'HEADING_TAKE_TEST' => HEADING_TAKE_TEST,
        'assessment_name'   => $assessment_name,
        'TB_HEAD_ASSESSMENT' => TB_HEAD_TEST,
        'TB_HEAD_TEST_DURATION' => TB_HEAD_TEST_DURATION,
        'TB_HEAD_TEST_TAKE' => TB_HEAD_TEST_TAKE,
        'TB_HEAD_START_TEST' => TB_HEAD_START_TEST,
        'NOT_FOUND_DATA'     =>  $no_table_data_found
    ));

    $template->pparse('list_tests');
}elseif ($action == 'video-library') {
    
    get_list_of_candidate_videos($jobseeker_id);
    
    $template->assign_vars(array(
        'VIDEO_TITLE'   => VIDEO_TITLE,
        'TB_HEAD_VIDEO' => TB_HEAD_VIDEO,
        'TB_HEAD_TEST_NAME' => TB_HEAD_TEST_NAME,
        'TB_HEAD_ASSESSMENT_NAME'   => TB_HEAD_ASSESSMENT_NAME,
        'TB_HEAD_DATE'  => TB_HEAD_DATE,
        'TB_HEAD_JOB'   => TB_HEAD_JOB,
        'TB_HEAD_COMPANY'   => TB_HEAD_COMPANY,
        'NOT_FOUND_DATA'     =>  $no_table_data_found
    ));

    $template->pparse('list_video_lib');

}elseif ($action == 'invitation-link') {
    get_the_list_of_jobseeker_invitaions($jobseeker_id);
    
    $template->assign_vars(array(
        'INVITATION_TITLE'   => INVITATION_TITLE,
        'TH_ASSESSMENT'      => TH_ASSESSMENT,
        'TH_COMPANY'         => TH_COMPANY,
        'TB_HEAD_DATE'       => TB_HEAD_DATE,
        'TH_STATUS'          => TH_STATUS,
        'NOT_FOUND_DATA'     => $no_table_data_found
    ));

    $template->pparse('invitation_list');
} else {

    get_list_of_assigned_assessment($jobseeker_id);

    $template->assign_vars(array(
        'HEADING_MY_TEST' => HEADING_MY_TEST,
        'TB_HEAD_ASSESSMENT' => TB_HEAD_ASSESSMENT,
        'TB_HEAD_COMPANY' => TB_HEAD_COMPANY,
        'TB_HEAD_TEST_DATE' => TB_HEAD_TEST_DATE,
        'TB_HEAD_TEST_EXPIRED' => TB_HEAD_TEST_EXPIRED,
        'TB_HEAD_JOB'   => TB_HEAD_JOB,
        'TB_HEAD_START_TEST' => TB_HEAD_START_TEST,
		 'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),

    ));
    $template->pparse('my_tests');
}

function get_list_of_assigned_assessment($jobseeker_id)
{
    global $template, $currentDate;
    $query = "SELECT aj.*, a.title, a.description, a.id as assessment_id, a.job_id, a.creator_id, a.is_active, a.expired_at ,j.job_title, r.recruiter_company_name  
                FROM `assessment_jobseeker` as aj
                LEFT JOIN assessments as a ON a.id = aj.assessment_id
                LEFT JOIN jobs as j ON j.job_id = a.job_id
                LEFT JOIN recruiter as r ON r.recruiter_id = a.creator_id
                WHERE aj.jobseeker_id = $jobseeker_id AND a.is_active = 1 ORDER BY aj.id DESC";

    $query_result = tep_db_query($query);

    if (tep_db_num_rows($query_result) > 0) {
        while ($asmntData = tep_db_fetch_array($query_result)) {
            $encode_assessmentid=encode_string("assessment_id==".$asmntData['assessment_id']."==assessment");

            $testStatus = get_the_assign_test_status($asmntData['assessment_id'], $jobseeker_id);

            if ($testStatus['message'] == 'true') {
                $testBtn = '<span class="btn btn-success">Completed</span>';
            }else {
                $testBtn = '<a href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=take-test&q='.$encode_assessmentid).'" class="btn btn-outline-secondary">
                                '.START_ASSESSMENT.'
                            </a>';
            }

            if (strtotime($asmntData['expired_at']) < strtotime($currentDate)) {
                $take_test_link = '<span class="btn">'.ASSESSMENT_EXPIRED.'</span>';
            } else {
                $take_test_link = $testBtn;
            }
            
            
            $template->assign_block_vars('my_assessment', array(
                'title' => '<a href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=take-test&q='.$encode_assessmentid).'" class="btn btn-ouline-secondary">
                                '.$asmntData['title'].'
                            </a>',
                'view_job' => '<a href="'.tep_href_link($asmntData['job_id'].'/'.encode_category($asmntData['job_title']).'.html').'" class="job_search_title" target="_blank">'
                                .$asmntData['job_title'].'
                                </a>',
                'company' => $asmntData['recruiter_company_name'],
                'date'     => tep_date_short($asmntData['created_at']),
                'deadline' => tep_date_short($asmntData['expired_at']) . $testStatus['message'],
                'start_test' => $take_test_link,
            ));
        }
        tep_db_free_result($query_result);
    }
}

// function check_assessment_done_or_not($assessment_id, $jobseeker_id)
// {
//     // count the no of test for this assessment
//     $testStatus = get_the_assign_test_status($assessment_id, $jobseeker_id);
// }

function get_the_assign_test_status($assessment_id, $jobseeker_id) {
    $test_status_array = [];
    $original_test_status = [];
    $query = "SELECT assessments.id AS assessment_id, assessments.title AS assessment, assessment_quiz.quiz_id AS test_id,
                    quizzes.title AS test, assessment_jobseeker.jobseeker_id, IF(results.id, 'completed', 'not_completed') AS test_status
                FROM assessment_jobseeker
                LEFT JOIN assessments ON assessments.id = assessment_jobseeker.assessment_id
                RIGHT JOIN assessment_quiz ON assessment_quiz.assessment_id = assessments.id
                LEFT JOIN quizzes ON quizzes.id = assessment_quiz.quiz_id
                LEFT JOIN jobseeker ON jobseeker.jobseeker_id = assessment_jobseeker.jobseeker_id
                LEFT JOIN results ON results.quiz_id = quizzes.id AND results.assessment_id = assessments.id AND results.member_id = jobseeker.jobseeker_id
                WHERE assessment_jobseeker.jobseeker_id = $jobseeker_id AND assessments.id = $assessment_id";

    $result = tep_db_query($query);

    if (tep_db_num_rows($result) > 0) {
        while ($data1 = tep_db_fetch_array($result)) {
            if ($data1['test_status'] == 'completed') {
                array_push($original_test_status, 'completed');
            }

            array_push($test_status_array, $data1['test_status']);
        }
    }

    if ($original_test_status == $test_status_array) {
        $message = 'true';
    } else {
        $message = 'false';
    }

    $data = [
        'assessment_test_status' => $test_status_array,
        'original_test_status'   => $test_status_array,
        'message'                => $message
    ];

    return $data; 
}