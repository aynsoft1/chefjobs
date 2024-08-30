<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE);
$template->set_filenames(array('company_size' => 'company_size.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") {
    switch ($action) {
        case 'confirm_delete':
            $id = tep_db_prepare_input($_GET['id']);
            tep_db_query("delete from " . COMPANY_SIZE_TABLE . " where id = '" . (int)$id . "'");
            $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
            tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page']));
            break;
        case 'insert':
        case 'save':
            $size_name = tep_db_prepare_input($_POST['TR_size_name']);
            $de_size_name = tep_db_prepare_input($_POST['TR_de_size_name']);
            $priority = tep_db_prepare_input($_POST['IN_priority']);

            $sql_data_array = array(
                'size_name' => $size_name,
                'de_size_name' => $de_size_name,
                'priority' => $priority,
            );

            if ($action == 'insert') {
                if ($row_check = getAnyTableWhereData(COMPANY_SIZE_TABLE, "size_name='" . tep_db_input($size_name) . "'", 'id')) {
                    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
                } elseif ($row_check = getAnyTableWhereData(COMPANY_SIZE_TABLE, "de_size_name='" . tep_db_input($de_size_name) . "'", 'id')) {
                    $messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
                } else {
                    tep_db_perform(COMPANY_SIZE_TABLE, $sql_data_array);
                    $row_id_check = getAnyTableWhereData(COMPANY_SIZE_TABLE, "1 order by id desc limit 0,1", "id");
                    $id = $row_id_check['id'];
                    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
                    tep_redirect(FILENAME_ADMIN1_ADMIN_COMPANY_SIZE);
                }
            } else {
                $id = (int)$_GET['id'];
                if ($row_check = getAnyTableWhereData(COMPANY_SIZE_TABLE, "size_name='" . tep_db_input($size_name) . "' and id!='$id'", 'id')) {
                    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
                    $action = 'edit';
                } elseif ($row_check = getAnyTableWhereData(COMPANY_SIZE_TABLE, "de_size_name='" . tep_db_input($de_size_name) . "' and id!='$id'", 'id')) {
                    $messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
                    $action = 'edit';
                } else {
                    tep_db_perform(COMPANY_SIZE_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
                    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
                    tep_redirect(FILENAME_ADMIN1_ADMIN_COMPANY_SIZE . '?page=' . $_GET['page'] . '&id=' . $id);
                }
            }
            break;
    }
}

// Query to fetch data from the `company_sizes` table
$company_size_query_raw = "select id, size_name, de_size_name, priority from " . COMPANY_SIZE_TABLE . " order by size_name";
$company_size_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $company_size_query_raw, $company_size_query_numrows);
$company_size_query = tep_db_query($company_size_query_raw);

if (tep_db_num_rows($company_size_query) > 0) {
    $alternate = 1;
    while ($company_size = tep_db_fetch_array($company_size_query)) {
        if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $company_size['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
            $cInfo = new objectInfo($company_size);
        }
        $row_selected = (isset($cInfo) && is_object($cInfo) && ($company_size['id'] == $cInfo->id))
            ? ' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE . '?page=' . $_GET['page'] . '&id=' . $cInfo->id . '&action=edit\'"'
            : ' class="dataTableRow' . ($alternate % 2 == 1 ? '1' : '2') . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE . '?page=' . $_GET['page'] . '&id=' . $company_size['id'] . '\'"';
        
        $alternate++;
        $action_image = (isset($cInfo) && is_object($cInfo) && ($company_size['id'] == $cInfo->id))
            ? tep_image(PATH_TO_IMAGE . 'icon_arrow_right.gif', IMAGE_EDIT)
            : '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $company_size['id']) . '">' . tep_image(PATH_TO_IMAGE . 'icon_info.gif', IMAGE_INFO) . '</a>';
        
        $template->assign_block_vars('company_size', array(
            'row_selected' => $row_selected,
            'action' => $action_image,
            'name' => tep_db_output($company_size['size_name']),
            'de_name' => tep_db_output($company_size['de_size_name']),
            'row_selected' => $row_selected
        ));
    }
}

// For the right-side column (edit, delete, etc.)
$ADMIN_RIGHT_HTML = "";
$heading = array();
$contents = array();
switch ($action) {
    case 'new':
    case 'insert':
    case 'save':
        $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">' . TEXT_INFO_HEADING_COMPANY_SIZE . '</div></div>');
        $contents = array('form' => tep_draw_form('company_size', PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'action=insert', 'post', ' onsubmit="return ValidateForm(this)"'));

        $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
            <div class="mb-1 text-danger">' . TEXT_INFO_NEW_INTRO . '</div>
            <div class="form-group">
                <label>' . TEXT_INFO_COMPANY_SIZE_NAME . '</label>
                ' . tep_draw_input_field('TR_size_name', $_POST['TR_size_name'], 'class="form-control form-control-sm"') . '
            </div>
            <div class="form-group">
                <label>' . TEXT_INFO_DE_COMPANY_SIZE_NAME . '</label>
                ' . tep_draw_input_field('TR_de_size_name', $_POST['TR_de_size_name'], 'class="form-control form-control-sm"') . '
            </div>
            <div class="form-group">
                <label>' . TEXT_INFO_COMPANY_SIZE_PRIORITY . '</label>
                ' . tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"') . '
            </div>
            ' . tep_draw_submit_button_field('', IMAGE_INSERT, 'class="btn btn-primary"') . '
            <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE) . '">' . IMAGE_CANCEL . '</a>
        </div>');
        break;
    
    case 'edit':
        $value_field = tep_draw_input_field('TR_size_name', $cInfo->size_name, '');
        $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">' . TEXT_INFO_HEADING_COMPANY_SIZE . '</div></div>');
        $contents = array('form' => tep_draw_form('company_size', PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'id=' . $cInfo->id . '&page=' . $_GET['page'] . '&action=save', 'post', ' onsubmit="return ValidateForm(this)"'));
        $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
            <div class="mb-1 text-danger">' . TEXT_INFO_EDIT_INTRO . '</div>
            <div class="form-group">
                <label>' . TEXT_INFO_COMPANY_SIZE_NAME . '</label>
                ' . tep_draw_input_field('TR_size_name', tep_db_output($cInfo->size_name), 'class="form-control form-control-sm"') . '
            </div>
            <div class="form-group">
                <label>' . TEXT_INFO_DE_COMPANY_SIZE_NAME . '</label>
                ' . tep_draw_input_field('TR_de_size_name', tep_db_output($cInfo->de_size_name), 'class="form-control form-control-sm"') . '
            </div>
            <div class="form-group">
                <label>' . TEXT_INFO_COMPANY_SIZE_PRIORITY . '</label>
                ' . tep_draw_input_field('IN_priority', tep_db_output($cInfo->priority), 'class="form-control form-control-sm"') . '
            </div>
            ' . tep_draw_submit_button_field('', IMAGE_UPDATE, 'class="btn btn-primary"') . '
            <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $cInfo->id) . '">' . IMAGE_CANCEL . '</a>
        </div>');
        break;
        case 'delete':
            $heading[] = array('text' => '<div class="list-group">
                <div class="font-weight-bold">
                '.$cInfo->size_name.'</div>
                </div>');
            $contents = array('form' => tep_draw_form('company_size_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
            $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
                <p>'.$cInfo->size_name.'</p></div>
                <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">' 
                . IMAGE_CONFIRM . '</a>
                <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
                . IMAGE_CANCEL . '</a>
                </div>');
            break;
            
        default:
            if (isset($cInfo) && is_object($cInfo)) {
                $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_COMPANY_SIZE.'</div></div>');
                $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                    <div class="mb-1 text-danger">'.tep_db_output($cInfo->size_name).'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
                    <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
                    .IMAGE_EDIT.'</a>
                    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
                    .IMAGE_DELETE.'</a>
                    </div>');
            }
            break;
}

if (tep_not_null($heading) && tep_not_null($contents)) {
    $box = new right_box;
    $ADMIN_RIGHT_HTML = $box->infoBox($heading, $contents);
}

$template->assign_vars(array(
    'TABLE_HEADING_COMPANY_SIZE_NAME' => TABLE_HEADING_COMPANY_SIZE_NAME,
    'TABLE_HEADING_DE_COMPANY_SIZE_NAME' => TABLE_HEADING_DE_COMPANY_SIZE_NAME,
    'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
    'count_rows' => $company_size_split->display_count($company_size_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COMPANY_SIZE),
    'no_of_pages' => $company_size_split->display_links($company_size_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
    'new_button' => tep_draw_submit_button_field('', IMAGE_NEW, 'class="btn btn-primary" onClick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_COMPANY_SIZE, 'page=' . $_GET['page'] . '&id=' . $cInfo->id . '&action=new') . '\'"'),
    'HEADING_TITLE' => HEADING_TITLE,
    'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH,
    'ADMIN_RIGHT_HTML' => $ADMIN_RIGHT_HTML
));

$template->pparse('company_size');
?>
