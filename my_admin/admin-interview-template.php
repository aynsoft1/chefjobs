<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.ADMIN_INTERVIEW_TEMPLATE);
$template->set_filenames([
    'list_template'         => 'email_template/list-template.htm',
    'create_update_form'    => 'email_template/template-form.htm',
]);

include_once(FILENAME_ADMIN_BODY);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$id = (isset($_GET['id']) ? $_GET['id'] : '');
$error = false;
$currentDate = date("Y-m-d H:i:s"); 

// input parameter
$inputSubject = $_POST['subject'];
$inputMessage = $_POST['message'];
$inputType    = $_POST['m_type'];


if (tep_not_null($id)) {
    if (!$mailTempData = getAnyTableWhereData(ASSESSMENT_EMAIL_TEMPLATE, "id='" . tep_db_input($id) . "'")) {
        $messageStack->add_session('row data not found', 'error');
        tep_redirect(ADMIN_INTERVIEW_TEMPLATE);
    }
    $mail_template_id = $mailTempData['id'];
    $edit = true;
}


// Default Values Pass
$template->assign_vars(array(
    'update_message'    => $messageStack->output(),
    'new_button'        => '<a href="'.tep_href_link(PATH_TO_ADMIN . ADMIN_INTERVIEW_TEMPLATE, 'action=new').'" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> New</a>',
));


function actionBtnLink(int $id)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-delete-$id').submit()}";

    $button = '
    <div class="btn-group">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . ADMIN_INTERVIEW_TEMPLATE, 'id=' . $id . '&action=edit') . '">
                Edit
            </a>

            <a class="dropdown-item" 
                href="#"
                onclick="'.$onclickEvent.'">
                Delete
            </a>

            <form id="form-delete-'.$id.'" action="'.tep_href_link(PATH_TO_ADMIN . ADMIN_INTERVIEW_TEMPLATE, 'id=' . $id . '&action=confirm_delete').'" method="POST" style="display: none;">
                <input name="_method" type="hidden" value="delete" />
            </form>
        </div>
    </div>
    ';

    return $button;
}

function list_of_interview_email_templates()
{
    global $template;

    $query = "SELECT * FROM ".ASSESSMENT_EMAIL_TEMPLATE." ORDER BY id DESC";

    $res = tep_db_query($query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $template->assign_block_vars('interview_emails', array(
                'name' => tep_db_output($data['subject']),
                'type' => tep_db_output($data['mail_type']),
                'action' => actionBtnLink(tep_db_output($data['id'])),
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}

function getFormTag($actionValue, $id = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('add_interview_template', PATH_TO_ADMIN . ADMIN_INTERVIEW_TEMPLATE, 'action=submit_template', 'post', ' enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('add_interview_template', PATH_TO_ADMIN . ADMIN_INTERVIEW_TEMPLATE, 'id=' . $id . '&action=update_template', 'post', 'enctype="multipart/form-data"');
            break;
    }
}





// submit the form
if (($_SERVER['REQUEST_METHOD'] == 'POST') AND ($action == 'submit_template')) {
    // validate form
    if (strlen($inputSubject) <= 0 AND strlen($inputMessage) <= 0) {
        $error = true;
        $field_required = 'All fields are required';
    }

    if (!in_array($inputType, ["invite", "complete"])) {
        $error = true;
        $field_required = 'mail type is required';
    }

    if (!$error) {
        $data = array(
            'subject'         => $inputSubject,
            'message'         => $inputMessage,
            'mail_type'       => $inputType,
            'created_at'      => $currentDate,
            'updated_at'      => $currentDate,
        );
        // submit form
        tep_db_perform(ASSESSMENT_EMAIL_TEMPLATE, $data);
        $messageStack->add_session('Template successfully created', 'success');
        // redirect to page
        return tep_redirect(ADMIN_INTERVIEW_TEMPLATE);
    } else {
        $messageStack->add_session($field_required, 'error');
        return tep_redirect(ADMIN_INTERVIEW_TEMPLATE.'?action=new');
    }


}


// update the form
if (tep_not_null($action) && $action == 'update_template' && $id && $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['_method'] == 'PUT') {
    // validate field
    if (empty($inputSubject) OR empty($inputMessage) OR strlen($inputSubject) <= 0 OR strlen($inputMessage) <= 0) {
        $error = true;
        $field_required = 'All fields are required';
    }

    if (!in_array($inputType, ["invite", "complete"])) {
        $error = true;
        $field_required = 'mail type is required';
    }
    
    // store form
    if (!$error) {
        $data = array(
            'subject'         => $inputSubject,
            'message'         => $inputMessage,
            'mail_type'       => $inputType,
            'created_at'      => $currentDate,
            'updated_at'      => $currentDate,
        );
        tep_db_perform(ASSESSMENT_EMAIL_TEMPLATE, $data, 'update', "id='" . $id . "'");
        $messageStack->add_session('Template successfully updated', 'success');
        return tep_redirect(ADMIN_INTERVIEW_TEMPLATE);
    } else {
        $messageStack->add_session($field_required, 'error');
        return tep_redirect(ADMIN_INTERVIEW_TEMPLATE."?id=$id&action=edit");
    }

}

// delete row
if ($action == 'confirm_delete' AND $id AND ($_SERVER['REQUEST_METHOD'] == 'POST') AND ($_POST['_method'] == 'delete')) {
    tep_db_query("delete from " . ASSESSMENT_EMAIL_TEMPLATE . " where id='" . tep_db_input($id) . "'");
    $messageStack->add_session("Template deleted successfully", 'success');
    tep_redirect(ADMIN_INTERVIEW_TEMPLATE);
}













if ($action == 'new' OR $action == 'edit') {
    
    $selectedArr = [
        ['id' => 'invite' , 'text' => 'Invite'],
        ['id' => 'complete', 'text' => 'Complete']
    ];

    $template->assign_vars([
        'HEADING_TITLE'         => 'Interview Template',
        'form'                  => ($action == 'edit') ? getFormTag($action, $id) : getFormTag($action),
        'INFO_TEXT_NAME'        => 'Subject',
        'INFO_TEXT_NAME1'       => tep_draw_input_field('subject', $mailTempData['subject'], 'size="35" class="form-control form-control-sm"', true ),
        'INFO_TEXT_TYPE'        => 'Type',
        'INFO_TEXT_TYPE1'       => tep_draw_pull_down_menu('m_type', $selectedArr, $mailTempData['mail_type'], 'class="form-control"'),
        'INFO_TEXT_DESCRIPTION' => 'Description',
        'INFO_TEXT_DESCRIPTION1'=> tep_draw_textarea_field('message', 'soft', '60%', '10', $mailTempData['message'], ' class="form-control form-control-sm"  id="description2"', true, true),

        'method'               => ($action == 'edit') ? tep_draw_hidden_field('_method', 'PUT') : '',
        'buttons'               => tep_draw_submit_button_field('form-submit', ($action == 'edit') ? 'Update' : 'Submit','class="btn btn-primary"'),
        'CURLY_BRACES'          => "{CANDIDATE_NAME}"
    ]); 

    $template->pparse('create_update_form');
} else {

    list_of_interview_email_templates();

    $template->assign_vars([
        'HEADING_TITLE' => 'Interview Template',
    ]); 
    $template->pparse('list_template');
}
?>