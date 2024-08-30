<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_QUIZ_CALENDAR);
$template->set_filenames(array(
    'calendar' => 'calendar.htm',
));
include_once("../" . FILENAME_BODY);

$request = $_SERVER['REQUEST_METHOD'];
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$error = false;

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
}

/**
 * get user/jobseeker id if logged in
 */
if (check_login('jobseeker')) {
    $memberId   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
}

function getRandomColor()
{
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

function getQuizReportData($id) 
{
    $result = getAnyTableWhereData(QUIZ_RESULT_TABLE, "id='" . tep_db_input($id) . "'");

    $message = ($result) ? $result['message'] : 'None' ;

    return $message;
}

function getData($id)
{
    // $latestReport = "SELECT * FROM " . QUIZ_RESULT_TABLE . " ORDER BY created_at DESC";
    $field_names = "results.id, quizzes.title, results.created_at as start, results.created_at as end, results.total_points, results.quiz_id, 
                    quizzes.description, 
                    member.jobseeker_first_name as firstName, 
                    member.jobseeker_last_name as lastName";
    
    $table_names = QUIZ_RESULT_TABLE . " LEFT OUTER JOIN " . QUIZ_TABLE . " ON (results.quiz_id = quizzes.id) 
                    LEFT OUTER JOIN " . JOBSEEKER_TABLE . " as member ON (member.jobseeker_id = results.member_id)";

    $whereClause = "results.member_id = $id";

    $sql = "select $field_names from $table_names where $whereClause";

    $output = array();

    $queryData = tep_db_query($sql);
    if (tep_db_num_rows($queryData) > 0) {
        while ($result = tep_db_fetch_array($queryData)) {

            extract($result);
            // Date format change
            $startDate = date_create($start);
            $endDate = date_create($start);
            $start = date_format($startDate, 'Y-m-d');
            $end = date_format($endDate, 'Y-m-d');

            $popup_content = '<div class="card">
                                <div class="card-body p-0">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                Quiz Name: &nbsp; <span class="">' . $title . '</span>
                                            </span>
                                        </li>
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                Total Points: &nbsp; <span class="">' . $total_points . '</span>
                                            </span>
                                        </li>
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                Submitted: &nbsp; <span class="">' . $start . '</span>
                                            </span>
                                        </li>
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                Description: &nbsp; <span class="">' . $description . '</span>
                                            </span>
                                        </li>
                                        <li class="nav-item">
                                            <span class="nav-link">
                                                Result: &nbsp; <span class="">' . getQuizReportData($id) . '</span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>';
            $result = array(
                "id" => $id,
                "title" => $title,
                "description" => html_entity_decode($description),
                "start" => $start,
                "end" => $end,
                "color" => getRandomColor(),
                "points" => $total_points,
                "quizId" => $quiz_id,
                "fullName" => $firstName . ' ' . $lastName,
                "popUpContent" => html_entity_decode($popup_content),
            );

            array_push($output, $result);
        }
    }

    // set response code - 200 OK
    http_response_code(200);

    return json_encode($output);
}

$template->assign_vars(array(
    'test_menus' => '
                    <a class="btn btn-sm btn-primary me-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS) . '" class="hm_color">
                        ' . MY_TESTS . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=latest-report') . '" class="hm_color">
                        ' . LATEST_REPORT . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_QUIZ_CALENDAR) . '" class="hm_color">
                        ' . CALENDAR . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=video-library') . '" class="hm_color">
                        ' . MY_VIDEO . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2 mw-100 mmt-15" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=invitation-link') . '">
                        ' . MY_INVITATION . '
                    </a>
    ',
    'update_message' => $messageStack->output()
));

if (($action == "api")) {
    switch ($request) {
        case 'GET':
            if ($memberId) {
                echo getData($memberId);
            } else {
                echo json_encode(false);
            }
            break;
        default:
            echo json_encode('This method is not allowed');
            break;
    }
} else {
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'SYMPTOMS_TEXT' => SYMPTOMS_TEXT,
        'LANGUAGE' => $language,
        'FULLCALENDAR_CSS' => tep_href_link("jscript/fullcalendar/main.css"),
        'FULLCALENDAR__JS' => tep_href_link("jscript/fullcalendar/main.js"),
        'quiz_menus' => '<a class="btn-text  mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT) . '" class="hm_color">' . MY_TEST . '</a>',
        'update_message' => $messageStack->output()
    ));
    $template->pparse('calendar');
}
