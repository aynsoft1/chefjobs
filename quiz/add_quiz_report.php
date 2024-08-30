<?php

// Initial Setup

include_once("../include_files.php");

include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_RECRUITER_QUIZ_REPORT);

$template->set_filenames(

    array(

        'all_quiz' => 'quizreport/all-quiz.htm',

        'view_quiz_report' => 'quizreport/view-quiz-report.htm',

        'reportPage' => 'quizreport/reports.htm',

        'latestReport' => 'quizreport/latest-report.htm',

        'add_note' => 'quizreport/add-note.htm',

    )

);

include_once("../" . FILENAME_BODY);



// global Properties

$action = (isset($_GET['action']) ? $_GET['action'] : '');

$quiz_id = (isset($_GET['quiz_id']) ? tep_db_prepare_input($_GET['quiz_id']) : '');

$report = (isset($_GET['report']) ? tep_db_prepare_input($_GET['report']) : '');

$currentDate = date("Y-m-d H:i:s"); // current date

$id = (isset($_GET['id']) ? $_GET['id'] : '');



// check if recruiter is logged in  or not

if (!check_login("recruiter")) {

    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];

    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');

    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));

} else {

    $recruiter_id   = $_SESSION['sess_recruiterid'];

    $user_type = 'recruiter';

}







// Check Condition if id is present in quiz table or not

// if (tep_not_null($quiz_id)) {

//     if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "' AND recruiter_id=$recruiter_id")) {

//         $messageStack->add_session(MESSAGE_ERROR, 'error');

//         tep_redirect(FILENAME_RECRUITER_QUIZ_REPORT);

//     }

//     $quiz_id = $row_check_quiz_id['id'];

// }



// Default Values Pass

$template->assign_vars(array(

    'quiz_menus' => '

                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT) . '" class="hm_color">

                        ' . LIST_ASSESSMENT . '

                    </a>

                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_QUIZ_REPORT, 'report=latestReport') . '" class="hm_color">

                        ' . MY_CANDIDATE . '

                    </a>

                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ) . '" class="hm_color">

                        ' . MY_CUSTOM_TESTS . '

                    </a>

                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=video') . '" class="hm_color">

                        ' . TEST_VIDEOS . '

                    </a>

                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=invitation-list') . '" class="hm_color">

                        ' . INVITATION . '

                    </a>

    ',



    'quiz_menus_1' => '<a class="btn btn-sm btn-primary mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=new') . '" class="hm_color">

                        ' . CREATE_ASSESSMENT . '

                        </a>

    ',



    'update_message' => $messageStack->output()

));



// crate add action button for question

function getGoToReportAction($quiz_id, $member_id = null)

{

    if ($member_id) {

        $button = '

            <a class="btn btn-primary" href="

            ' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_REPORT, 'quiz_id=' . $quiz_id) . '&report=true&member=' . $member_id . '">

                ' . VIEW_REPORT . '

            </a>

        ';

    } else {

        $button = '

            <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_REPORT, 'quiz_id=' . $quiz_id) . '&report=true">

                ' . VIEW_REPORT . '

            </a>

        ';

    }



    return $button;

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



function get_ip_location($ip) {



    $response = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);



    $json_response = json_decode($response);



    return $json_response;

}

function fetch_recruiter_candidates($recruiter_id)

{

    global $template;



    $query_candiate = "SELECT results.*,

                            quizzes.title AS test_name, assessments.title AS assessment_name,

                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name,

                            recruiter.recruiter_company_name AS company

                        FROM results

                        INNER JOIN quizzes ON quizzes.id = results.quiz_id

                        INNER JOIN assessments ON assessments.id = results.assessment_id

                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = results.member_id

                        INNER JOIN recruiter ON recruiter.recruiter_id = assessments.creator_id

                        WHERE assessments.creator_id = $recruiter_id

                        ORDER BY results.id DESC";



    $queryResult = tep_db_query($query_candiate);



    if (tep_db_num_rows($queryResult) > 0) {

        while ($candidates = tep_db_fetch_array($queryResult)) {



            $resume_id = get_jobseeker_resume_id($candidates['member_id']);

            

            $query_string=encode_string("search_id==".$resume_id."==search");

            

            if ($resume_id) {

                $view_resume = '<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string).'" target="_new" rel="noopener noreferrer">'.VIEW_RESUME.'</a>';

            } else {

                $view_resume = RESUME_NOT_FOUND;

            }



            $score = ($candidates['out_of'] == null OR ($candidates['out_of'] == '0')) 

                        ? 'null'

                        : $candidates['total_points'] . '/' . $candidates['out_of'];

                        

            // long2ip => Converts an long integer address into a string in (IPv4) Internet standard dotted format

            $ip = long2ip($candidates['ip_address']);           



            $ipLocation = get_ip_location($ip);

            $ans_link = '<a class="btn btn-link" data-toggle="collapse" 
                                href="#collapse-'.$candidates['id'].'" 
                                role="button" aria-expanded="false" aria-controls="collapse-'.$candidates['id'].'">
                        essay answer
                    </a>';

            $ans_txt = '<tr class="collapse" id="collapse-'.$candidates['id'].'">
                            <td colspan="8">
                                '.$candidates['answer'].'
                            </td>
                        </tr>';

            if ($candidates['device_on_same_window'] == '1') {
                $is_same_window = "YES";
            } elseif ($candidates['device_on_same_window'] == '0') {
                $is_same_window = "NO";
            } else {
                $is_same_window = "NONE";
            }
            
            $template->assign_block_vars('my_candidates', array(

                'candidate_id'      => tep_db_output($candidates['member_id']),

                'candidate_name'    => $candidates['jobseeker_name'],

                'score'             => $score,

                'message'           => $candidates['message'],

                'view_resume'       => $view_resume,

                'test_name'       => $candidates['test_name'],

                'company'    => tep_db_output(($candidates['company'])),

                'ip'        => $ip,

                'location'  => $ipLocation->geoplugin_city,

                'result_ans_link'   => $candidates['answer'] ? $ans_link : null,

                'answer'    => $candidates['answer'] ? $ans_txt : null,

                'result_id'    => $candidates['id'],

                'note_link' => '<a href="'.tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_QUIZ_REPORT, 'report=add_note&id='.$candidates['id']).'">
                                    link
                                </a>',
                
                'mouse_on_same_window' => $is_same_window,

            ));

        }

        tep_db_free_result($queryResult);

    }



    return true;

}


if ($report == 'submit_note' AND tep_not_null($_POST['r_id']) AND $_SERVER['REQUEST_METHOD'] == 'POST') {
    // decode r_id
    $r_id = $_POST['r_id'];
    $note_txt = $_POST['note'];
    $decoded_result_id = check_data($r_id,"==","r_id","note_form");

    // update coulume
    if (empty($note_txt)) {
        $messageStack->add_session(MESSAGE_NOTE_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_QUIZ_REPORT.'?report=add_note&id='.$decoded_result_id);

    } else {
        $inputFields = [
            'note' => $note_txt,
        ];

        // update message in result table
        tep_db_perform(QUIZ_RESULT_TABLE, $inputFields, 'update', "id='" . $decoded_result_id . "'");
        
        $messageStack->add_session(NOTE_UPDATED_SUCCESS, 'success');
        tep_redirect(FILENAME_RECRUITER_QUIZ_REPORT.'?report=latestReport');
    }
}


/**

 * return to html files based on request actions

 */

if ($report == 'latestReport' OR $report == '') {

    // fetch recruiter candidates

    fetch_recruiter_candidates($recruiter_id);

    $template->assign_vars(array(

        'HEADING_TITLE' => MY_CANDIDATES,

        'TOTAL_ASSESSMENTS' => TOTAL_ASSESSMENTS,

        'MEMBER_NAME' => MEMBER_NAME,

        'TEST_NAME' => TEST_NAME,

        'COMPANY'   => COMPANY,

        'RECURITER_NAME' => RECURITER_NAME,

        'TABLE_HEADING_VIEW_RESUME' => TABLE_HEADING_VIEW_RESUME,

        'POINTS' => POINTS,

        'QUIZ_NAME' => QUIZ_NAME,

        'TEST_GIVEN_BY' => TEST_GIVEN_BY,

        // 'STATUS' => RESULT,

        'RESULT_STATUS' => RESULT_STATUS,

        'SUBMITTED' => SUBMITTED,

        'SCORE' => SCORE,

        'MESSAGE' => MESSAGE,

        'HEAD_ACTION' => HEAD_ACTION,

        'TH_IP' => TH_IP,

        'TH_IP_LOCATION' => TH_IP_LOCATION,

        'ANSWER'    => ANSWER,
        'TH_MOUSE_ON_SAME' => TH_MOUSE_ON_SAME

    ));

    $template->pparse('latestReport');

} elseif ($report == 'add_note' && $id) {

    $data = fetch_result_row_data($id, $recruiter_id);

    $ip = long2ip($data['ip_address']);  
    $ipLocation = get_ip_location($ip);

    $encoded_r_id = encode_string("r_id==".$data['id']."==note_form");
    $btnName = $data['note'] ? 'Update' : 'Submit';

    if ($data['device_on_same_window'] == '1') {
        $is_same_window = "YES";
    } elseif ($data['device_on_same_window'] == '0') {
        $is_same_window = "NO";
    } else {
        $is_same_window = "NONE";
    }

    $template->assign_vars(array(
        'TEST_TITLE'        => strtoupper($data['test_name']),
        'CANDIDATE_NAME'    => $data['jobseeker_name'],
        'CANDIDATE_EMAIL'   => $data['jobseeker_email'],
        'CANDIDATE_MOBILE'  => $data['jobseeker_mobile'],
        'IP_ADDRESS'        => long2ip($data['ip_address']),
        'IP_LOCATON'        => $ipLocation->geoplugin_city,
        'IS_SAME_WINDOW'    => $is_same_window,
        'UPLOADED_DATE'     => tep_date_short($data['created_at']),
        'NOTE_SHORT_LABEL'  => NOTE_SHORT_LABEL,
        'form'              => tep_draw_form("note_form", PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_REPORT, "report=submit_note", 
                                "post", " id='note_form' 
                                enctype='multipart/form-data'").tep_draw_hidden_field('r_id', $encoded_r_id),
        'NOTE_INPUT'        => '<textarea name="note" wrap="soft" cols="30" rows="4" class="form-control" id="note" required>'.$data['note'].'</textarea>',
        'submit_btn'        => '<button type="submit" name="noteSubmit" id="noteSubmitBtn" class="btn btn-outline-primary float-right mb-1">'.$btnName.'</button>'
    ));

    $template->pparse('add_note');
}

function fetch_result_row_data($id, $recruiter_id)
{
    $query = "SELECT results.*,
                quizzes.title AS test_name, assessments.title AS assessment_name,
                CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name,
                jobseeker.jobseeker_mobile AS jobseeker_mobile,
                jobseeker_login.jobseeker_email_address AS jobseeker_email,
                recruiter.recruiter_company_name AS company
            FROM results
            INNER JOIN quizzes ON quizzes.id = results.quiz_id
            INNER JOIN assessments ON assessments.id = results.assessment_id
            INNER JOIN jobseeker ON jobseeker.jobseeker_id = results.member_id
            INNER JOIN jobseeker_login ON jobseeker_login.jobseeker_id = results.member_id
            INNER JOIN recruiter ON recruiter.recruiter_id = assessments.creator_id
            WHERE results.id = $id AND assessments.creator_id = $recruiter_id
            ORDER BY results.id DESC";
    
    $queryResult = tep_db_query($query);
    $data = tep_db_fetch_array($queryResult);

    return $data;
}