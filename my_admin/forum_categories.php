<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_FORUM_CATEGORIES);
$template->set_filenames(array('forum_category' => 'forum_categories.htm'));
include_once(FILENAME_ADMIN_BODY);

define('TEXT_LANGUAGE','');
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$cat_id = (isset($_GET['id']) ? $_GET['id'] : '');
if($cat_id!="")
{
 if(!$row_chek=getAnyTableWhereData(FORUM_CATEGORIES_TABLE,"id='".tep_db_input($cat_id)."'",'id'))
 {
  $messageStack->add_session(MESSAGE_ERROR_CATEGORY, 'error');
  tep_redirect(FILENAME_ADMIN1_FORUM_CATEGORIES.'?page='.$_GET['page']);
 }
}
if($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   if($check=getAnyTableWhereData(FORUM_TABLE,"category_id='".tep_db_input($id)."'"))
   {
    $messageStack->add_session(MESSAGE_FORUM_ERROR, 'error');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES,'id='.$id.'&page=' . $_GET['page']));
   }
   tep_db_query("delete from " . FORUM_CATEGORIES_TABLE . " where id = '" . (int)$id . "'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
  case 'insert1':
  case 'save1':
   $category_name=tep_db_prepare_input($_POST['TR_forum_category_name']);
   $de_category_name=tep_db_prepare_input($_POST['TR_de_forum_category_name']);
   $sql_data_array['category_name'] = $category_name;
   $sql_data_array['de_category_name'] = $de_category_name;
   if($action=='insert')
	{
	 if($row_chek=getAnyTableWhereData(FORUM_CATEGORIES_TABLE,"category_name='".tep_db_input($category_name)."'",'id'))
	  {
	   $messageStack->add(MESSAGE_NAME_ERROR, 'error');
	  }
	 elseif($row_chek=getAnyTableWhereData(FORUM_CATEGORIES_TABLE,"de_category_name='".tep_db_input($de_category_name)."'",'id'))
	  {
	   $messageStack->add(MESSAGE_NAME_ERROR, 'error');
	  }
	 else
	  {
       tep_db_perform(FORUM_CATEGORIES_TABLE, $sql_data_array);
  	   $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
	   tep_redirect(FILENAME_ADMIN1_FORUM_CATEGORIES);
	  }
	 }
	else if($action=='save')
	 {
      $id=(int)$_GET['id'];
	  if($row_chek=getAnyTableWhereData(FORUM_CATEGORIES_TABLE,"category_name='".tep_db_input($category_name)."' and id!='$id'",'id'))
	   {
		$messageStack->add(MESSAGE_NAME_ERROR, 'error');
		$action='edit';
	   }
	  else if($row_chek=getAnyTableWhereData(FORUM_CATEGORIES_TABLE,"de_category_name='".tep_db_input($de_category_name)."' and id!='$id'",'id'))
	   {
		$messageStack->add(MESSAGE_NAME_ERROR, 'error');
		$action='edit';
	   }
	  else
	   {
        tep_db_perform(FORUM_CATEGORIES_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  		$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
		tep_redirect(FILENAME_ADMIN1_FORUM_CATEGORIES);
	   }
	 }
	 break;
    }
  }

///////////// Middle Values 
$forum_category_query_raw="select id, category_name,de_category_name from " . FORUM_CATEGORIES_TABLE ." order by category_name asc";
$forum_category_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $forum_category_query_raw, $forum_category_query_numrows);
$forum_category_query = tep_db_query($forum_category_query_raw);

if(tep_db_num_rows($forum_category_query) > 0)
{
 $alternate=1;
 while ($forum_category = tep_db_fetch_array($forum_category_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $forum_category['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($forum_category);
   //print_r($cInfo);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($forum_category['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_FORUM_CATEGORIES . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_FORUM_CATEGORIES . '?page='.$_GET['page'].'&id=' . $forum_category['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($forum_category['id'] == $cInfo->id))
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page='.$_GET['page'].'&id=' . $forum_category['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
   $category_name=$forum_category['category_name'];
  		///french//
   $de_category_name=$forum_category['de_category_name'];



  $template->assign_block_vars('forum_category', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($category_name),
   'de_name' => tep_db_output($de_category_name),
   'row_selected' => $row_selected
   ));
 }
}

//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{
 case 'new':
 case 'insert':
	// $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</b>');
   $heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</div></div>');
   $contents  = array('form' => tep_draw_form('forum_category', PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
	// $contents[] = array('text' => TEXT_INFO_NEW_INTRO);
	// $contents[] = array('text' => '<br>'.TEXT_INFO_FORUM_CATEGORY_NAME.'<br>'.tep_draw_input_field('TR_forum_category_name', ($action=='insert'?$_POST['TR_forum_category_name']:''), '' ));
	// $contents[] = array('text' => '<br>'.TEXT_INFO_FR_FORUM_CATEGORY_NAME.'<br>'.tep_draw_input_field('TR_de_forum_category_name', ($action=='insert'?$_POST['TR_de_forum_category_name']:''), '' ));
	// $contents[] = array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a></form>');

   $contents[] = array('align' => 'left', 'text' => '<div class="">
   <div class="mb-1">'.TEXT_INFO_NEW_INTRO.'</div> 
      <div class="form-group">
         <label>'.TEXT_INFO_FORUM_CATEGORY_NAME.'</label>
         '.tep_draw_input_field('TR_forum_category_name', ($action=='insert'?$_POST['TR_forum_category_name']:''), 'class="form-control form-control-sm"' ).'
      </div>
      <div class="form-group">
      <label>'.TEXT_INFO_FR_FORUM_CATEGORY_NAME.'</label>
      '.tep_draw_input_field('TR_de_forum_category_name', ($action=='insert'?$_POST['TR_de_forum_category_name']:''), 'class="form-control form-control-sm"' ).'
      </div>
      '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
      <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES).'">'.IMAGE_CANCEL.'</a>
   </form>
   </div>');
	break;
 case 'edit':
 case 'save':
   $heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</div></div>');

//   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</b>');
  $contents = array('form' => tep_draw_form('forum_category', PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'id='.$_GET['id'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
//   $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
//   $contents[] = array('text' => '<br>'.TEXT_INFO_FORUM_CATEGORY_NAME.'<br>'.tep_draw_input_field('TR_forum_category_name', ($action=='save'?$_POST['TR_forum_category_name']:$cInfo->category_name), '' ));
//   $contents[] = array('text' => '<br>'.TEXT_INFO_FR_FORUM_CATEGORY_NAME.'<br>'.tep_draw_input_field('TR_de_forum_category_name', ($action=='save'?$_POST['TR_de_forum_category_name']:$cInfo->de_category_name), '' ));
//   $contents[] = array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>');

  $contents[] = array('align' => 'left', 'text' => '<div class="">
  <div class="mb-1">'.TEXT_INFO_EDIT_INTRO.'</div> 
     <div class="form-group">
        <label>'.TEXT_INFO_FORUM_CATEGORY_NAME.'</label>
        '.tep_draw_input_field('TR_forum_category_name', ($action=='save'?$_POST['TR_forum_category_name']:$cInfo->category_name), 'class="form-control form-control-sm"' ).'
     </div>
     <div class="form-group">
     <label>'.TEXT_INFO_FR_FORUM_CATEGORY_NAME.'</label>
     '.tep_draw_input_field('TR_de_forum_category_name', ($action=='save'?$_POST['TR_de_forum_category_name']:$cInfo->de_category_name), 'class="form-control form-control-sm"' ).'
     </div>
     '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
     <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES).'">'.IMAGE_CANCEL.'</a>
  </form>
  </div>');
  break;
 case 'delete':
//   $heading[] = array('text' => '<b>' . $cInfo->category_name . '</b>');
$heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.tep_db_output($cInfo->category_name).'</div></div>');
  $contents = array('form' => tep_draw_form('forum_category_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] . '&id=' . $cInfo->id . '&action=deleteconfirm'));
//   $contents[] = array('text' => TEXT_DELETE_INTRO);
//   $contents[] = array('text' => '<br><b>' . $cInfo->category_name . '</b>');
//   $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
  
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">
  '.IMAGE_CONFIRM.'
  </a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">
  '.IMAGE_CANCEL.'
  </a>  
  </div>');
  break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
   {
	   // $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</b>');
	   // $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
      
      $heading[] = array('text' => '<div class=""><div class="font-weight-bold">'.TEXT_INFO_HEADING_FORUM_CATEGORY.'</div></div>');
      $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
      <div class="mb-1">'.tep_db_output($cInfo->category_name).'</div>
      <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">
      '.IMAGE_EDIT.'
      </a>
      <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">
      '.IMAGE_DELETE.'
      </a>  
      <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
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
 'TABLE_HEADING_FR_FORUM_CATEGORY_NAME'=>TABLE_HEADING_FR_FORUM_CATEGORY_NAME,
 'TABLE_HEADING_FORUM_CATEGORY_NAME'=>TABLE_HEADING_FORUM_CATEGORY_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$forum_category_split->display_count($forum_category_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FORUM_CATEGORIES),
 'no_of_pages'=>$forum_category_split->display_links($forum_category_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CATEGORIES, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW1.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('forum_category');
?>