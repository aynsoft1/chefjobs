<?

include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY);
$template->set_filenames(array('job_subcategory' => 'job_subcategory.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . JOB_SUB_CATEGORY_TABLE . " where subcat_id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $subcat_name=tep_db_prepare_input($_POST['TR_job_subcategory_name']);
   $category_id=tep_db_prepare_input($_POST['category_id']);

   $sql_data_array['subcat_name'] = $subcat_name;
   $sql_data_array['category_id'] = $category_id;

			if($action=='insert')
			{
                $duplicateClause = "subcat_name='".tep_db_input($subcat_name)."' and category_id='".tep_db_input($category_id)."'";
				if($row_chek=getAnyTableWhereData(JOB_SUB_CATEGORY_TABLE,$duplicateClause,'subcat_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
                    tep_db_perform(JOB_SUB_CATEGORY_TABLE, $sql_data_array);
                    $row_id_check=getAnyTableWhereData(JOB_SUB_CATEGORY_TABLE,"1 order by subcat_id desc limit 0,1","subcat_id");
                    $id = $row_id_check['subcat_id'];
                    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY);
				}
			}
			else
			{
                $id=(int)$_GET['id'];
                $duplicateClause = "subcat_name='".tep_db_input($subcat_name)."' and category_id='".tep_db_input($category_id)."' and subcat_id!='$id'";
				if($row_chek=getAnyTableWhereData(JOB_SUB_CATEGORY_TABLE,$duplicateClause,'subcat_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
                    tep_db_perform(JOB_SUB_CATEGORY_TABLE, $sql_data_array, 'update', "subcat_id = '" . (int)$id . "'");
                    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$job_subcategory_query_raw="select subcat.*, jc.id, jc.category_name 
                            from ".JOB_SUB_CATEGORY_TABLE." as subcat 
                            left join ".JOB_CATEGORY_TABLE." as jc on jc.id = subcat.category_id";

$job_subcategory_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $job_subcategory_query_raw, $job_subcategory_query_numrows);
$job_subcategory_query = tep_db_query($job_subcategory_query_raw);
if(tep_db_num_rows($job_subcategory_query) > 0)
{
 $alternate=1;
 while ($job_subcategory = tep_db_fetch_array($job_subcategory_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $job_subcategory['subcat_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($job_subcategory);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($job_subcategory['subcat_id'] == $cInfo->subcat_id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY . '?page='.$_GET['page'].'&id=' . $cInfo->subcat_id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY . '?page='.$_GET['page'].'&id=' . $job_subcategory['subcat_id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($job_subcategory['subcat_id'] == $cInfo->subcat_id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page='.$_GET['page'].'&id=' . $job_subcategory['subcat_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('job_subcategory', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'subcat_name' => tep_db_output($job_subcategory['subcat_name']),
   'category_name' => tep_db_output($job_subcategory['category_name']),
   'row_selected' => $row_selected
   ));
 }
}

// category list
function tep_get_category($default = '')
{
    $subcat_array = array();
    if ($default) {
        $subcat_array[] = array('id' => '', 'text' => $default);
    }
    $category_query = tep_db_query("select id, category_name from " . JOB_CATEGORY_TABLE . " order by category_name");
    while ($category = tep_db_fetch_array($category_query)) {
        $subcat_array[] = array('id' => $category['id'], 'text' => $category['category_name']);
    }
    return $subcat_array;
}

//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{
 case 'new':
 case 'insert':
 case 'save':
    $heading[] = array('text' => '<div class="list-group">
                <div class="font-weight-bold  text-primary">
                '.TEXT_INFO_JOB_SUB_CATEGORY_NAME.'</div>
                </div>');
                $contents = array('form' => tep_draw_form('job_subcategory', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
                $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
                <div class="form-group">
                <label>'.TEXT_INFO_JOB_SUB_CATEGORY_NAME.'</label>
                '.tep_draw_input_field('TR_job_subcategory_name', $_POST['TR_job_subcategory_name'], 'class="form-control form-control-sm"' ).'
                </div>
                <div class="form-group">
                <label>'.TEXT_INFO_JOB_CATEGORY_NAME.'</label>
                ' . tep_draw_pull_down_menu('category_id', tep_get_category(), '', 'id="categoryBox" class="form-control form-control-sm"') . '
                </div>
                '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
                <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY) . '">' 
                . IMAGE_CANCEL . '</a>
                </div>');
    break;
 case 'edit':
            $value_field=tep_draw_input_field('TR_job_subcategory_name', $cInfo->subcat_name, '' );
            $heading[] = array('text' => '<div class="list-group">
                <div class="font-weight-bold text-primary">
                '.TEXT_INFO_JOB_SUB_CATEGORY_NAME.'</div>
                </div>');
            $contents = array('form' => tep_draw_form('job_subcategory', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'id=' . $cInfo->subcat_id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
            $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
            <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
            <div class="form-group">
            <label>'.TEXT_INFO_JOB_SUB_CATEGORY_NAME.'</label>
            '.tep_draw_input_field('TR_job_subcategory_name', $cInfo->subcat_name, '' ).'
            </div>
            <div class="form-group">
            <label>'.TEXT_INFO_JOB_CATEGORY_NAME.'</label>
            ' . tep_draw_pull_down_menu('category_id', tep_get_category(), $cInfo->category_id, 'id="categoryBox" class="form-control form-control-sm"') . '
            </div>
            '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
            <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->subcat_id ) . '">' 
            . IMAGE_CANCEL . '</a>
            </div>');
  break;
 case 'delete':
        $heading[] = array('text' => '<div class="list-group">
        <div class="font-weight-bold">
        '.$cInfo->subcate_name.'</div>
        </div>');
        $contents = array('form' => tep_draw_form('job_subcategory_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] . '&id=' . $nInfo->subcat_id . '&action=deleteconfirm'));
        
        $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
        <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
        <p>'.$cInfo->subcat_name.'</p></div>
        <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">' 
        . IMAGE_CONFIRM . '</a>
        <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
        . IMAGE_CANCEL . '</a>
        </div>');
  break;
 default:
    if (isset($cInfo) && is_object($cInfo)) 
            {
    $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold  text-primary">'.TEXT_INFO_JOB_SUB_CATEGORY_NAME.'</div></div>');
    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1 text-danger">'.tep_db_output($cInfo->subcat_name).'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
    <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] .'&id=' . $cInfo->subcat_id . '&action=edit') . '">'
    .IMAGE_EDIT.'</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] .'&id=' . $cInfo->subcat_id . '&action=delete') . '">'
    .IMAGE_DELETE.'</a>
    </div>');
    }
  break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
$template->assign_vars(array(
 'TABLE_HEADING_JOB_SUB_CATEGORY_NAME'=>TABLE_HEADING_JOB_SUB_CATEGORY_NAME,
 'TABLE_HEADING_JOB_CATEGORY_NAME'=>TABLE_HEADING_JOB_CATEGORY_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$job_subcategory_split->display_count($job_subcategory_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOB_CATEGORIES),
 'no_of_pages'=>$job_subcategory_split->display_links($job_subcategory_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_SUB_CATEGORY, 'page=' . $_GET['page'] .'&action=new') . '"><i class="fa fa-plus fa-admin-icons" aria-hidden="true"></i> '.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('job_subcategory');
