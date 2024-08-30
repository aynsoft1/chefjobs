<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_TEST_CATEGORY);
$template->set_filenames(
    array(
        'list_category' => 'test_category/list-category.htm',
        'view_category' => 'test_category/view-category.htm',
        'create_update_form' => 'test_category/form-submit.htm',
    )
);
include_once(FILENAME_ADMIN_BODY);

// global variables
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$cateID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date
$errorName = false;


if (tep_not_null($cateID)) {
    if (!$categoryData = getAnyTableWhereData(TEST_CATEGORY_TABLE, "id='" . tep_db_input($cateID) . "'")) {
        $messageStack->add_session(MESSAGE_CATEGORY_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_TEST_CATEGORY);
    }
    $quiz_id = $categoryData['id'];
    $edit = true;
}

// Default Values
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
    
    'new_button' => '
        <a 
            class="btn btn-primary float-left" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'action=new') . '">
            <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
        </a>
    ',

    'test_menus' => '
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ) . '">
            ' . ADMIN_TEST . '
        </a>
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'action=employer_test') . '">
            ' . EMPLOYER_TEST . '
        </a>
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY) . '">
            ' . TEST_CATEGORY . '
        </a>
    ',
));

function getFormTag($actionValue, $id = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('test_category', PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'action=new', 'post', ' enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('test_category', PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'id=' . $id . '&action=edit', 'post', 'enctype="multipart/form-data"');
            break;
    }
}

function getAction($quizId)
{
    $button = '
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'id=' . $quizId . '&action=edit') . '">
                        ' . EDIT_TEXT . '
                    </a>
                    <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'id=' . $quizId . '&action=view') . '">
                        ' . VIEW_TEXT . '
                    </a>
                    <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'id=' . $quizId . '&action=confirm_delete') . '">'
                        . DELETE_TEXT . '
                    </a>
                </div>
            </div>
    ';

    return $button;
}

function on_off_toggle_link_btn(int $test_id, string $action_name, int $value)
{
    if ($value == 1) {
        $toggle_on_off = '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16">
                                <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                            </svg>
        ';
    } else {
        $toggle_on_off =  '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16">
                                <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
                            </svg>
        ';
    }

    return '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY, 'id=' . $test_id . '&action='.$action_name) . '">' . $toggle_on_off . '</a>';
}





// store form
if (tep_not_null($action) && $action == 'new' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_name = tep_db_prepare_input($_POST['name']);

    // validate field
    if (strlen($input_name) <= 0) {
        $error = true;
        $errorName = true;
    }

    // store form
    if (!$error) {
        $data = array(
            'name'         => $input_name,
            'created_at'   => $currentDate,
            'updated_at'   => $currentDate,
        );
        tep_db_perform(TEST_CATEGORY_TABLE, $data);
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
        return tep_redirect(FILENAME_ADMIN1_TEST_CATEGORY);
    }

}

// update form
if (tep_not_null($action) && $action == 'edit' && $cateID && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_name = tep_db_prepare_input($_POST['name']);

    // validate field
    if (strlen($input_name) <= 0) {
        $error = true;
        $errorName = true;
    }

    // store form
    if (!$error) {
        $data = array(
            'name'         => $input_name,
            'updated_at'   => $currentDate,
        );
        tep_db_perform(TEST_CATEGORY_TABLE, $data, 'update', "id='" . $cateID . "'");
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
        return tep_redirect(FILENAME_ADMIN1_TEST_CATEGORY);
    }

}

// delete form
if (tep_not_null($action) && $action == 'confirm_delete' && $cateID && $_SERVER['REQUEST_METHOD'] == 'GET') {
    tep_db_query("delete from " . TEST_CATEGORY_TABLE . " where id='" . tep_db_input($cateID) . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(FILENAME_ADMIN1_TEST_CATEGORY);
}

// status active/inactive
if (($action == 'category_inactive' || $action == 'category_active') && $cateID) {
    tep_db_query("update " . TEST_CATEGORY_TABLE . " set is_active='" . ($action == 'category_active' ? '1' : '0') . "' where id='" . $cateID . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    return tep_redirect(FILENAME_ADMIN1_TEST_CATEGORY);
}


if ($action == 'new' || $action == 'edit') {
    // Go to create or edit form
    $template->assign_vars(array(
        'HEADING_TITLE' => ($action == 'edit') ? EDIT_TEXT : ADD_TEXT,
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $quiz_id),
        'NAME_LABEL' => NAME_LABEL,
        'INPUT_NAME' => tep_draw_input_field('name', $categoryData['name'], 'class="form-control" id="name" required', '', 'text'),
        'IS_NAME_ERROR' => ($errorName) ? '<span class="text-danger">' . IS_NAME_ERROR . '</span>' : '',
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON) : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON),
    ));
    $template->pparse('create_update_form');

} elseif ($action == 'view' && $cateID) {
    $template->assign_vars(array(
        'HEADING_TITLE' => $categoryData['name'],
        'category_name' => $categoryData['name'],
    ));
    $template->pparse('view_category');
} else {
    // fetch categories
    $raw_query = "SELECT * FROM " . TEST_CATEGORY_TABLE . " as category ORDER BY category.created_at DESC";

    $category_query = tep_db_query($raw_query);
    
    if (tep_db_num_rows($category_query) > 0) {
        while ($category = tep_db_fetch_array($category_query)) {
            $alternate++;
            $template->assign_block_vars('categories', array(
                'id' => tep_db_output($category['id']),
                
                'name' => tep_db_output($category['name']),
                
                'created_at' => tep_date_short($category['created_at']),

                'active_inactive' => ($category['is_active'] == 1)
                    ? on_off_toggle_link_btn($category['id'], 'category_inactive', $category['is_active'])
                    : on_off_toggle_link_btn($category['id'], 'category_active', $category['is_active']),
                
                'action' => getAction(tep_db_output($category['id'])),
            ));
        }
        tep_db_free_result($category_query);
    }




    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'TABLE_HEADING_NAME' => TABLE_HEADING_NAME,
        'TABLE_HEADING_TOGGLE' => TABLE_HEADING_TOGGLE,
        'TABLE_HEADING_DATE_ADDED' => TABLE_HEADING_DATE_ADDED,
        'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
    ));
    $template->pparse('list_category');
}

?>