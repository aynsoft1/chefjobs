<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_LIST_OF_ASSESSMENT);
$template->set_filenames(
    array(
        'list_assessment' => 'assessment/list-assessment.htm',
    )
);
include_once(FILENAME_ADMIN_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$currentDate = date("Y-m-d H:i:s"); // current date

$perPage = 10;

if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$startAt = $perPage * ($page - 1);

/**
 * Check Condition if id is present in table or not
 */
// if (tep_not_null($quiz_id)) {
//     if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
//         $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
//         tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
//     }
//     $quiz_id = $row_check_quiz_id['id'];
//     $edit = true;
// }

function recrutier_list_of_assessments(int $offset, int $perPage) {
    global $template;

    // get the list assessments
    $raw_query = "SELECT assessments.*, COUNT(aq.assessment_id) AS no_of_test, recruiter.recruiter_company_name AS company
                FROM assessments
                LEFT JOIN assessment_quiz AS aq ON aq.assessment_id = assessments.id
                INNER JOIN recruiter ON recruiter.recruiter_id = assessments.creator_id
                WHERE assessments.assigned_by = 'recruiter'
                GROUP BY aq.assessment_id
                ORDER BY assessments.id DESC
                LIMIT $offset, $perPage";

    $res = tep_db_query($raw_query);

    if (tep_db_num_rows($res) > 0) {
        while ($assessments = tep_db_fetch_array($res)) {
            $template->assign_block_vars('assessments', array(
                'name'                  => $assessments['title'],
                'company'               => $assessments['company'],
                'no_of_test_included'   => $assessments['no_of_test'],
                'date'                  => tep_date_short($assessments['created_at']),
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}

function get_pagination_for_assessments() {
    global $perPage, $page;

    $countAssessment = "SELECT COUNT(*) AS total FROM assessments WHERE assigned_by = 'recruiter'";

    $result = tep_db_query($countAssessment);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ASSESSMENT, 'page='.($page - 1));
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ASSESSMENT, 'page='.($page + 1));
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

// Default Values
$template->assign_vars(array(
    'update_message' => $messageStack->output()
));


if ($action) {
    
} else {
    recrutier_list_of_assessments($startAt, $perPage);
    $template->assign_vars(array(
        'ASSESSMENT_HEADING'        => ASSESSMENT_HEADING . ' <span class="badge badge-info">'.get_pagination_for_assessments()['totalData'].'</span>',
        'TH_ASSESSMENT_NAME'        => TH_ASSESSMENT_NAME,
        'TH_COMPANY_NAME'           => TH_COMPANY_NAME,
        'TH_ASSESSMENT_TEST_COUNT'  => TH_ASSESSMENT_TEST_COUNT,
        'TH_ASSESSMENT_DATE'        => TH_ASSESSMENT_DATE,
        'PAGINATION_LINK'           => get_pagination_for_assessments()['pagination'],
    ));
    $template->pparse('list_assessment');
}