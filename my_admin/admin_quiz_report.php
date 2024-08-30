<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_QUIZ_REPORT);
$template->set_filenames(
    array(
        'all_quiz' => 'quizreport/all-quiz.htm',
        'view_quiz_report' => 'quizreport/view-quiz-report.htm',
        'reportPage' => 'quizreport/reports.htm',
        'latestReport' => 'quizreport/latest-report.htm',
        'all_candidates' => 'quizreport/all-candidate.htm',
        'report_box' => 'quizreport/report-box.htm',
    )
);
include_once(FILENAME_ADMIN_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$quiz_id = (isset($_GET['quiz_id']) ? tep_db_prepare_input($_GET['quiz_id']) : '');
$report = (isset($_GET['report']) ? tep_db_prepare_input($_GET['report']) : '');
$member_id = (isset($_GET['member']) ? tep_db_prepare_input($_GET['member']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date

$perPage = 10;

if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}

$startAt = $perPage * ($page - 1);

// Check Condition if id is present in quiz table or not
if (tep_not_null($quiz_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
        $messageStack->add_session(MESSAGE_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_QUIZ_REPORT);
    }
    $quiz_id = $row_check_quiz_id['id'];
}

// Check Condition if member id is present in member table or not
if (tep_not_null($member_id)) {
    if (!$row_check_member_data = getAnyTableWhereData(JOBSEEKER_TABLE, "jobseeker_id='" . tep_db_input($member_id) . "'")) {
        $messageStack->add_session(MESSAGE_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_QUIZ_REPORT);
    }
    $member_id = $row_check_member_data['jobseeker_id'];
}

// Default Values Pass
$template->assign_vars(array(
    'update_message' => $messageStack->output()
));

// crate add action button for question
function getGoToReportAction($quiz_id, $member_id = null)
{
    if ($member_id) {
        $button = '
            <a class="btn btn-primary" href="
            ' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_QUIZ_REPORT, 'quiz_id=' . $quiz_id) . '&report=true&member=' . $member_id . '">
                ' . VIEW_REPORT . '
            </a>
        ';
    } else {
        $button = '
            <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_QUIZ_REPORT, 'quiz_id=' . $quiz_id) . '&report=true">
                ' . VIEW_REPORT . '
            </a>
        ';
    }

    return $button;
}

// Get quiz Details
function viewQuizDataWithQuizId()
{
    global $error, $action, $row_check_quiz_id;
    if (!$error) {
        $data = new objectInfo($row_check_quiz_id);
        return $data;
    }
    return false;
}
// Get Member Details
function viewMemberDataWithMemberId()
{
    global $error, $row_check_member_data;
    if (!$error) {
        $data = new objectInfo($row_check_member_data);
        return $data;
    }
    return false;
}

/**
 * get single user detail
 *
 * @param [int] $id
 */
function getMemberFullName($id)
{
    if ($id) {
        $data = getAnyTableWhereData(JOBSEEKER_TABLE, "jobseeker_id='" . tep_db_input($id) . "'");
        $fullname = $data['jobseeker_first_name'] . ' ' . $data['jobseeker_last_name'];
        return $fullname;
    }

    return false;
}

/**
 * get quiz Name
 *
 * @param [int] $id
 */
function getQuizName($id)
{
    if ($id) {
        $data = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($id) . "'");
        $title = $data['title'];
        return $title;
    }

    return false;
}

// count quiz has question
function quizHasQuestion($id)
{
    $query = "SELECT COUNT(*) as totalQuestion FROM " . QUES_TABLE . " WHERE quiz_id = " . $id . "";

    $data = tep_db_query($query);

    $countData = tep_db_fetch_array($data);

    return $countData['totalQuestion'];
}

/**
 * analysis condition based on values and value would be positive integer value
 *
 * @param integer $values
 */

function assesmentAnalysis(int $values)
{
    $range1 = range(1, 13);
    $range2 = range(14, 19);
    $range3 = range(20, 28);
    $range4 = range(29, 63);
    switch ($values) {
        case in_array($values, $range1):
            return 'You are not suffering from depression';
            break;
        case in_array($values, $range2):
            return 'You are suffering from mild depression';
            break;
        case in_array($values, $range3):
            return 'Moderately depressed';
            break;
        case in_array($values, $range4):
            return 'Severely depressed. Please contact to a specialist or schedule  a psychological appointment';
            break;
    }
}

/**
 * get total number of marks
 * quiz id needed
 * @param [int] $id
 */
function totalQuizMarks($id)
{
    $getMaxValue = [];

    // get total question first
    $questionTableQuery = "SELECT * FROM " . QUES_TABLE . " as ques WHERE ques.quiz_id = $id";

    $totalQuestions = tep_db_query($questionTableQuery);
    $ques = tep_db_num_rows($totalQuestions);

    foreach ($totalQuestions as $question) {
        $id = $question['id'];
        // for each question get their choice point which have max point
        $choiceTableQuery = "SELECT MAX(point) as point FROM " . QUES_CHOICE_TABLE . " WHERE question_id = $id";
        $result = tep_db_query($choiceTableQuery);
        $pointValue = tep_db_fetch_array($result);
        array_push($getMaxValue, $pointValue['point']);
    }

    $maxValue = max($getMaxValue);

    // get total points
    $outOfTotal = $ques * $maxValue;

    return $outOfTotal;
}

function resultMessage($resultValue, $quiz_id)
{
    $message = '<p>Score was ' . $resultValue . ' out of a possible ' . totalQuizMarks($quiz_id) . '</p>';
    return $message;
}

/**
 * count how much time first, second... quiz is attempted
 *
 * @param integer $id
 */
function quizAttemptCount(int $id) {
    $query = "SELECT COUNT(*) as quizAttempted FROM " . QUIZ_RESULT_TABLE . " WHERE quiz_id = " . $id . "";

    $data = tep_db_query($query);

    $countData = tep_db_fetch_array($data);

    return $countData['quizAttempted'];
}

/**
 * count quiz attempt by member
 *
 * @param integer $id
 */
function quizTakenCount(int $id) {
    $query = "SELECT DISTINCT member_id FROM " . QUIZ_RESULT_TABLE . " WHERE quiz_id = " . $id . "";
    
    $data = tep_db_query($query);
    $countMember = tep_db_num_rows($data);
    // $countData = tep_db_fetch_array($data);
    return $countMember;
}

function get_jobseeker_resume_id($jobseeker_id)
{
    $query = "SELECT jobseeker_id, resume_id FROM ". JOBSEEKER_RESUME1_TABLE ." WHERE jobseeker_id=$jobseeker_id LIMIT 1";

    $result = tep_db_query($query);

    $data = tep_db_fetch_array($result);

    if (tep_db_num_rows($result) > 0) {
        return $data['resume_id'];
    }

    return false;
}

function fetch_all_candidates(int $offset, int $perPage)
{
    global $template;

    $query_candiate = "SELECT results.id, results.total_points, results.out_of, results.message, results.member_id, results.created_at,
                            quizzes.title AS test_name, assessments.title AS assessment_name,
                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name,
                            recruiter.recruiter_company_name AS company
                        FROM results
                        INNER JOIN quizzes ON quizzes.id = results.quiz_id
                        INNER JOIN assessments ON assessments.id = results.assessment_id
                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = results.member_id
                        INNER JOIN recruiter ON recruiter.recruiter_id = assessments.creator_id
                        ORDER BY results.id DESC
                        LIMIT $offset, $perPage";

    $queryResult = tep_db_query($query_candiate);

    if (tep_db_num_rows($queryResult) > 0) {
        while ($candidates = tep_db_fetch_array($queryResult)) {

            $resume_id = get_jobseeker_resume_id($candidates['member_id']);
            
            $query_string=encode_string("search_id==".$resume_id."==search");
            
            if ($resume_id) {
                $view_resume = '<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string).'" target="_new" rel="noopener noreferrer">'.VIEW_RESUME.'</a>';
            } else {
                $view_resume = '';
            }

            $template->assign_block_vars('my_candidates', array(
                'candidate_id'      => tep_db_output($candidates['jobseeker_id']),
                'candidate_name'    => $candidates['jobseeker_name'],
                'test_name'    => $candidates['test_name'],
                'total_points'    => $candidates['total_points'],
                'out_of'    => $candidates['out_of'],
                'message'    => $candidates['message'],
                'created_at'    => tep_date_short($candidates['created_at']),
                'assessment_name'    => $candidates['assessment_name'],
                'company'           => $candidates['company'],
            ));
        }
        tep_db_free_result($queryResult);
    }

    return true;
}

function reportBox() {
    $total_uploaded_videos_by_jobseeker = no_of_records(TEST_VIDEO_TABLE, "jobseeker_id IS NOT NUll");
    
    $total_admin_test_library           = no_of_records(QUIZ_TABLE, "recruiter_id IS NULL AND save_as_template = '1' AND isActive = '1'");
    $total_emp_test_library             = no_of_records(QUIZ_TABLE, "recruiter_id IS NOT NULL AND isActive = '1'");
    
    $total_assessment                   = no_of_records(ASSESSMENT_TABLE, "is_active = '1'");
    $total_candidates                   = no_of_records(QUIZ_RESULT_TABLE." as res", 'res.quiz_id IS NOT NULL AND res.member_id IS NOT NULL AND res.assessment_id IS NOT NULL', "DISTINCT res.member_id, res.assessment_id, res.quiz_id");
    
    $total_questions                    = no_of_records(QUES_TABLE, "quiz_id IS NOT NULL");
    $data = [
                'total_admin_test_library'  =>$total_admin_test_library,
                'total_emp_test_library'    =>$total_emp_test_library,
                'total_jobseeker_videos'    =>$total_uploaded_videos_by_jobseeker,
                'total_assessment'          =>$total_assessment,
                'total_candidates'          =>$total_candidates,
                'total_questions'          =>$total_questions,
            ];

    return $data;
}

function get_pagination_for_candidates() {
    global $perPage, $page;

    $countAssessment = "SELECT COUNT(*) AS total FROM results";

    $result = tep_db_query($countAssessment);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_QUIZ_REPORT, 'report=candidates&page='.($page - 1));
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_QUIZ_REPORT, 'report=candidates&page='.($page + 1));
    $nextDisabled = ($page >= $total_page) ? 'disabled' : '';

    $paginate_link = '<nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item '.$prevDisabled.'">
                <a class="page-link" href="'.$prevURL.'">Previous</a>
            </li>
            <li class="page-item '.$nextDisabled.'">
                <a class="page-link" href="'.$nextURL.'">Next</a>
            </li>
        </ul>
    </nav>';

    return [
        'pagination' => ($total_row <= $perPage) ? '' : $paginate_link,
        'totalData'  => $total_row,
    ];
}












/**
 * fetch all ques's and question based on ques Id
 */
if (tep_not_null($member_id)) {
    $fetchAllResult = "SELECT * FROM " . QUIZ_RESULT_TABLE . " WHERE quiz_id = $quiz_id AND member_id = $member_id ORDER BY created_at DESC";
    $getData = tep_db_query($fetchAllResult);
    if (tep_db_num_rows($getData) > 0) {
        while ($results = tep_db_fetch_array($getData)) {
            $alternate++;
            $template->assign_block_vars('results', array(
                'id' => tep_db_output($results['id']),
                'fullname' => getMemberFullName($results['member_id']),
                'totalPoints' => tep_db_output($results['total_points']),
                'status' => resultMessage(tep_db_output($results['total_points']), $quiz_id),
                'message' => tep_not_null($results['message']) ? $results['message'] : MESSAGE_NOT_DEFINED,
                'created_at' => tep_date_short($results['created_at']),
            ));
        }
        tep_db_free_result($getData);
    }
} elseif ($report === 'true' && tep_not_null($quiz_id)) {
    // get members id
    $Ids = [];
    $fetchQuizMember = "SELECT DISTINCT member_id FROM " . QUIZ_RESULT_TABLE . " WHERE quiz_id = $quiz_id";
    $getQuizAllMemberId = tep_db_query($fetchQuizMember);

    if (tep_db_num_rows($getQuizAllMemberId) > 0) {

        while ($selectedMemberIds = tep_db_fetch_array($getQuizAllMemberId)) {
            array_push($Ids, $selectedMemberIds['member_id']);
        }

        $Ids = implode(',', $Ids); // conver ids in 1,2,3 format for WHERE member_id IN (1,2,3) clause

        // get member records
        $quizMembers = "SELECT * FROM jobseeker WHERE jobseeker_id IN ($Ids)";
        $allMember = tep_db_query($quizMembers);

        if (tep_db_num_rows($allMember) > 0) {
            while ($quizMembers = tep_db_fetch_array($allMember)) {
                $alternate++;
                $template->assign_block_vars('quizMembers', array(
                    'id' => tep_db_output($quizMembers['jobseeker_id']),
                    'fullName' => tep_db_output($quizMembers['jobseeker_first_name']). ' ' .tep_db_output($quizMembers['jobseeker_last_name']),
                    'action' => getGoToReportAction($quiz_id, $quizMembers['jobseeker_id']),
                ));
            }
            tep_db_free_result($allMember);
        }
    }
} else {
    $fetch_all_quiz_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz WHERE quiz.isActive = '1' ORDER BY quiz.created_at DESC";
    $get_all_quiz = tep_db_query($fetch_all_quiz_query);
    if (tep_db_num_rows($get_all_quiz) > 0) {
        while ($quiz = tep_db_fetch_array($get_all_quiz)) {
            $alternate++;
            $template->assign_block_vars('quizs', array(
                'id' => tep_db_output($quiz['id']),
                'title' => '<a href="' . tep_href_link(PATH_TO_QUIZ . $quiz['id'] . '/' . encode_forum($quiz['title']) . '.html') . '" target="_blank" rel="noreferrer">
                ' . tep_db_output($quiz['title']) .
                    '</a>',
                'created_at' => tep_date_short($quiz['created_at']),
                'taken' => quizTakenCount($quiz['id']),
                'totalQuestion' => '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . tep_db_output($quiz['id']) . '&action=allQuestion') . '">
                        ' . quizHasQuestion($quiz['id']) .
                    '</a>',
                'action' => getGoToReportAction(tep_db_output($quiz['id'])),
            ));
        }
        tep_db_free_result($get_all_quiz);
    }
}


/**
 * return to html files based on request actions
 */
if (tep_not_null($member_id)) {
    $template->assign_vars(array(
        'HEADING_TITLE' => viewMemberDataWithMemberId()->member_first_name . ' ' . viewQuizDataWithQuizId()->title . ' Reports',
        'QUIZ_NAME' => viewQuizDataWithQuizId()->title,
        'MEMBER_NAME' => MEMBER_NAME,
        'POINTS' => POINTS,
        'STATUS' => RESULT,
        'RESULT_STATUS' => RESULT_STATUS,
        'SUBMITTED' => SUBMITTED,
        'HEAD_ACTION' => HEAD_ACTION,
    ));
    $template->pparse('reportPage');
} elseif ($report == 'candidates') {

    fetch_all_candidates($startAt, $perPage);

    $template->assign_vars(array(
        'CANDIDATES'                => CANDIDATES . ' <span class="badge badge-info">'.get_pagination_for_candidates()['totalData'].'</span>',
        'MEMBER_NAME'               => CANDIDATE_NAME,
        'TABLE_HEADING_VIEW_RESUME' => TABLE_HEADING_VIEW_RESUME,
        'RECURITER_NAME'            => RECURITER_NAME,
        'COMPANY_NAME'              => COMPANY_NAME,
        'TOTAL_ASSESSMENTS'         => TOTAL_ASSESSMENTS,
        'TEST_NAME'                 => TEST_NAME,
        'ASSESSMENT_NAME'           => ASSESSMENT_NAME,
        'TOTAL_POINTS'              => TOTAL_POINTS,
        'MESSAGE'                   => MESSAGE,
        'DATE'                      => DATE,
        'PAGINATION_LINK'           => get_pagination_for_candidates()['pagination'],
    ));
    $template->pparse('all_candidates');

} elseif ($report == 'true' && tep_not_null($quiz_id)) {
    // View Quiz Page
    $template->assign_vars(array(
        'HEADING_TITLE' => TOP_MEMBER . ' ' . viewQuizDataWithQuizId()->title . ' Quiz',
        'QUIZ_TITLE' => viewQuizDataWithQuizId()->title,
        'MEMBER_NAME' => MEMBER_NAME,
        'MOBILE_NO' => MOBILE_NO,
        'QUIZ_NAME' => QUIZ_NAME,
        'MARKS_GOT' => POINTS,
        'HEAD_ACTION' => HEAD_ACTION,
    ));
    $template->pparse('view_quiz_report');
} elseif ($action == 'report') {
    $data = reportBox();

    $template->assign_vars(array(
        'REPORT_BOX_TITLE' => 'Reports',
        'TOTAL_ASSESSMENT'  => TOTAL_ASSESSMENT,
        'TOTAL_ADMIN_TEST_LIBRARY'  => TOTAL_ADMIN_TEST_LIBRARY,
        'TOTAL_EMP_TEST'    => TOTAL_EMP_TEST,
        'TOTAL_CANDIDATE'   => TOTAL_CANDIDATE,
        'TOTAL_UPLOADED_VIDEOS' => TOTAL_UPLOADED_VIDEOS,
        'TOTAL_QUESTION'        => TOTAL_QUESTION,
        'total_assessments' => $data['total_assessment'],
        'total_test_lib'    => $data['total_admin_test_library'],
        'total_emp_tests'   => $data['total_emp_test_library'],
        'total_candidates'  => $data['total_candidates'],
        'total_uploaded_videos' => $data['total_jobseeker_videos'],
        'total_questions' => $data['total_questions'],
    ));
    $template->pparse('report_box');
} else {
    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'HEAD_HEADING_TITLE' => QUIZ_TITLE,
        'QUIZ_QUESTION' => QUIZ_QUESTION,
        'QUIZ_ATTEMPT' => QUIZ_ATTEMPT,
        'HEAD_CREATE_DATE' => HEAD_CREATE_DATE,
        'HEAD_ACTION' => HEAD_ACTION,
        'latest_report' => '<a class="btn btn-link float-right" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_QUIZ_REPORT, 'report=latestReport') . '">
                            ' . LATEST_REPORT . '
                            </a>',
    ));
    $template->pparse('all_quiz');
}
