<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . LMS_CATEGORY_FILENAME);
$template->set_filenames(
    array(
        'list_category'     => 'lms/category/list-category.htm',
        'create_edit_form'  => 'lms/category/form-category.htm',
    )
);

include_once(FILENAME_ADMIN_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$rowID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;
$date = date("Y-m-d H:i:s"); // current date

// pagination variable
$perPage = 10;
if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$startAt = $perPage * ($page - 1);





// if rowID not null check is data available
if (tep_not_null($rowID)) {
    if (!$lmsCategory = getAnyTableWhereData(LMS_CATEGORY_TBL, "id='" . tep_db_input($rowID) . "'")) {
        $messageStack->add_session(MESSAGE_ERROR, 'error');
        tep_redirect(LMS_CATEGORY_FILENAME);
    }
    $rowID = $lmsCategory['id'];
    $edit = true;
}





// default value pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
    
    'new_button' => '
        <a 
            class="btn btn-primary float-left" 
            href="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'action=new') . '">
            <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_NEW_CATEGORY . '
        </a>
    ',

    'menus' => '
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME) . '">
            Category
        </a>
    ',

    'back_btn' => '
            <a 
                class="btn-link mr-2 float-right" 
                href="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME) . '">
                Back
            </a>
        ',
));


// Store Form
if (tep_not_null($action) && $action == 'new' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_name = tep_db_prepare_input($_POST['category_name']);
    $input_summary = tep_db_prepare_input($_POST['category_summary']);

    // validate field
    if (strlen($input_name) <= 0) {
        $error = true;
        $errorName = true;
    }

    if (strlen($input_summary) > 255) {
        $error = true;
        $errorSummary = 'summary length can not be greater then 255 characters';
    }

    // store form
    if (!$error) {
        $data = array(
            'category_name'         => $input_name,
            'category_summary'      => $input_summary,
            'created_at'            => $date,
            'updated_at'            => $date,
        );

        //////// file upload Attachment starts //////
        if (tep_not_null($_FILES['picture']['name'])) {
            if ($obj_resume = new upload('picture', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                $file_name = tep_db_input($obj_resume->filename);
                if (tep_not_null($file_name)) {
                    if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name)) {
                        $target_file_name = PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $file_name;
                        copy(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name, $target_file_name);
                        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name);
                        chmod($target_file_name, 0644);
                        $data['category_img'] = $file_name;
                    }
                }
            }
        }

        tep_db_perform(LMS_CATEGORY_TBL, $data);
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
        return tep_redirect(LMS_CATEGORY_FILENAME);
    }
}


// Update form
if (tep_not_null($action) && $action == 'edit' && $rowID && $_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['_method'] == 'put')) {
    $input_name = tep_db_prepare_input($_POST['category_name']);
    $input_summary = tep_db_prepare_input($_POST['category_summary']);

    // validate field
    if (strlen($input_name) <= 0) {
        $error = true;
        $errorName = true;
    }

    if (strlen($input_summary) > 255) {
        $error = true;
        $errorSummary = 'summary length can not be greater then 255 characters';
    }

    
    
    // update form
    if (!$error) {
        $data = array(
            'category_name'         => $input_name,
            'category_summary'      => $input_summary,
            'updated_at'            => $date,
        );

        //////// file upload Attachment starts //////
        if (tep_not_null($_FILES['picture']['name'])) {
            if ($obj_resume = new upload('picture', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                $file_name = tep_db_input($obj_resume->filename);
                if (tep_not_null($file_name)) {
                    if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name)) {
                        $target_file_name = PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $file_name;
                        copy(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name, $target_file_name);
                        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name);
                        chmod($target_file_name, 0644);
                        $data['category_img'] = $file_name;
                        if ($edit && tep_not_null($lmsCategory['category_img'])) {
                            $old_photo = $lmsCategory['category_img'];
                            if (is_file(PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $old_photo))
                                @unlink(PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $old_photo);
                        }
                    }
                }
            }
        }

        tep_db_perform(LMS_CATEGORY_TBL, $data, 'update', "id='" . $rowID . "'");
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
        return tep_redirect(LMS_CATEGORY_FILENAME);
    }

}


// Delete row
if (tep_not_null($action) && $action == 'confirm_delete' && $rowID && ($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['_method'] == 'delete')) {
    tep_db_query("delete from " . LMS_CATEGORY_TBL . " where id='" . tep_db_input($rowID) . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(LMS_CATEGORY_FILENAME);
}








if ($action == 'new' || $action == 'edit') {

    $template->assign_vars(array(
        'HEADING_TITLE'         => ($action == 'edit') ? EDIT_NEW_CATEGORY : ADD_NEW_CATEGORY,
        'form'                  => ($action == 'new') ? getFormTag($action) : getFormTag($action, $rowID),
        'form_method'           => ($action == 'edit') ? tep_draw_hidden_field('_method', 'put') : '',
        
        'NAME_LABEL'            => NAME_LABEL,
        'INPUT_NAME'            => tep_draw_input_field('category_name', $lmsCategory['category_name'], 'class="form-control" id="category_name"', '', 'text'),
        'CAT_NAME_ERROR'        => ($errorName) ? '<span class="text-danger">' . CAT_NAME_ERROR . '</span>' : '',
        
        'SUMMARY_LABEL'         => SUMMARY_LABEL,
        'INPUT_SUMMARY'         => '<textarea class="form-control" name="category_summary" id="category_summary" rows="5">'.$lmsCategory['category_summary'].'</textarea>',
        'CAT_SUMMARY_ERROR'     => ($errorSummary) ? '<span class="text-danger">' . $errorSummary . '</span>' : '',

        'BUTTON'                => ($action == 'edit') 
                                    ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON) 
                                    : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON),

        'FILE_LABEL'            => FILE_LABEL,
        'INPUT_FILE'            => tep_draw_file_field("picture"),
    ));

    $template->pparse('create_edit_form');
} else {
    get_all_lms_categories($startAt, $perPage);

    $template->assign_vars(array(
        'HEADING_TITLE'         => HEADING_TITLE . ' <span class="badge badge-info">'.paginate_lms_category()['totalData'].'</span>',
        'TH_NAME'               => TH_NAME,
        'TH_DATE_ADDED'         => TH_DATE_ADDED,
        'TH_ACTION'             => TH_ACTION,
        'PAGINATION_LINK'       => paginate_lms_category()['pagination'],
    ));
    $template->pparse('list_category');
}



// ///////////////////////////////// functions //////////////////////////////////////////

// get all categories
function get_all_lms_categories(int $offset, int $perPage)
{
    global $template;

    $db_raw_query = "SELECT * FROM " . LMS_CATEGORY_TBL . " AS category
                    ORDER BY category.id DESC
                    LIMIT $offset, $perPage";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if ($row_data['category_img']) {
                $img = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $row_data['category_img'] . "&size=50", '', '', '', 'align="center" class="forum-icon-bg p-3 mr-2"');
            } else {
                $img = defaultProfilePhotoUrl($row_data['category_name'], false, 50);
            }


            $template->assign_block_vars('lms_categories', array(
                'id'            => tep_db_output($row_data['id']),
                'slug'          => tep_db_output($row_data['slug']),
                'name'          => tep_db_output($row_data['category_name']),
                'cat_img'       => $img,
                'summary'       => tep_db_output($row_data['category_summary']),
                'description'   => tep_db_output($row_data['category_description']),
                'created_at'    => tep_date_short($row_data['created_at']),
                'action'        => getAction(tep_db_output($row_data['id'])),
            ));
        }
        tep_db_free_result($query);
        return true;
    }

    return false;
}

// action btn function
function getAction($id)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-delete-$id').submit()}";

    $button = '
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'id=' . $id . '&action=edit') . '">
                        ' . EDIT_ACTION_BTN . '
                    </a>
                    <!--
                    <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'id=' . $id . '&action=view') . '">
                        ' . VIEW_ACTION_BTN . '
                    </a>
                    -->
                    <a class="dropdown-item" href="#" onclick="'.$onclickEvent.'">'
                        . DELETE_ACTION_BTN . '
                    </a>
                    <form style="display:none" 
                        method="post" 
                        id="form-delete-'.$id.'"
                        action="' . tep_href_link(PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'id=' . $id . '&action=confirm_delete') . '">
                        <input name="_method" type="hidden" value="delete" />
                    </form>
                </div>
            </div>
    ';

    return $button;
}

// pagination for categories
function paginate_lms_category() {
    global $perPage, $page;

    $countRow = "SELECT COUNT(*) AS total FROM ". LMS_CATEGORY_TBL;

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.LMS_CATEGORY_FILENAME, 'page='.($page - 1).'');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.LMS_CATEGORY_FILENAME, 'page='.($page + 1).'');
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

// form tag
function getFormTag($actionValue, $id = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('lms_category', PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'action=new', 'post', ' enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('lms_category', PATH_TO_ADMIN . LMS_CATEGORY_FILENAME, 'id=' . $id . '&action=edit', 'post', 'enctype="multipart/form-data"');
            break;
    }
}
?>