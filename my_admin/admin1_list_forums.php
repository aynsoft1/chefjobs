<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_FORUMS);
$template->set_filenames(array('forum' => 'admin1_list_forums.htm','forum1' => 'admin1_forum.htm','preview' => 'admin1_forum1.htm'));
include_once(FILENAME_ADMIN_BODY);
////////////////
$edit=false;
$error =false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$forum_id=(isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
if(tep_not_null($forum_id))
{
 if(!$row_check_forum_id=getAnyTableWhereData(FORUM_TABLE,"id='".tep_db_input($forum_id)."'"))
 {
  $messageStack->add_session(MESSAGE_ARTCLE_ERROR, 'error');
  tep_redirect(FILENAME_ADMIN1_LIST_OF_FORUMS);
 }
 $forum_id=$row_check_forum_id['id'];
 $edit=true;
}
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);
if(tep_not_null($action))
{
 switch($action)
 {
  case 'confirm_delete':
			$topic_reply_query = "select id from ".FORUM_TOPICS_TABLE." where forum_id='".tep_db_input($forum_id)."'";
   $topic_reply_result=tep_db_query($topic_reply_query);
   while($topic_reply_row = tep_db_fetch_array($topic_reply_result))
   {
    tep_db_query("delete from ".TOPIC_REPLY_TABLE." where topic_id='".tep_db_input($topic_reply_row['id'])."'");
			}
			tep_db_free_result($topic_reply_result);
   tep_db_query("delete from ".FORUM_TOPICS_TABLE." where forum_id='".tep_db_input($forum_id)."'");
   if($edit && tep_not_null($row_check_forum_id['forum_photo']))
   {
    $old_photo= $row_check_forum_id['forum_photo'];
    if(is_file(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$old_photo))
    @unlink(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$old_photo);
   }
   tep_db_query("delete from ".FORUM_TABLE." where id='".tep_db_input($forum_id)."'");

   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(FILENAME_ADMIN1_LIST_OF_FORUMS);
   break;
  case 'forum_active':
  case 'forum_inactive':
   tep_db_query("update ".FORUM_TABLE." set is_show='".($action=='forum_active'?'Yes':'No')."' where id='".tep_db_input($forum_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'preview':
    $hidden_fields='';
   	$category_id       = tep_db_prepare_input($_POST['TR_category']);
    $title             = tep_db_prepare_input($_POST['TR_title']);
    $author            = tep_db_prepare_input($_POST['TR_author']);
    $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
    $description       = tep_db_prepare_input($_POST['TR_description']);
    $is_show           = tep_db_prepare_input($_POST['show']);

    $hidden_fields.=tep_draw_hidden_field('TR_category',$category_id);
    $hidden_fields.=tep_draw_hidden_field('TR_title',$title);
    $hidden_fields.=tep_draw_hidden_field('TR_author',$author);
    $hidden_fields.=tep_draw_hidden_field('TR_year',$_POST['TR_year']);
    $hidden_fields.=tep_draw_hidden_field('TR_month',$_POST['TR_month']);
    $hidden_fields.=tep_draw_hidden_field('TR_date',$_POST['TR_date']);
    $hidden_fields.=tep_draw_hidden_field('TR_description',$description);
    $hidden_fields.=tep_draw_hidden_field('show',$is_show);
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_FORUM_DESCRIPTION, 'error');
     $error=true;
    }
    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['forum_photo']['name']))
     {
      if($obj_resume = new upload('forum_photo', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('jpg','gif','png')))
      {
       $forum_photo_name=tep_db_input($obj_resume->filename);
       $hidden_fields.=tep_draw_hidden_field('forum_photo_name',$forum_photo_name);
      }
     }
     //////// file upload ends //////
    }
    break;
  case 'back':
   	$category_id       = tep_db_prepare_input($_POST['TR_category']);
    $title             = tep_db_prepare_input($_POST['TR_title']);
    $author            = tep_db_prepare_input($_POST['TR_author']);
    $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
    $description       = tep_db_prepare_input($_POST['TR_description']);
    $is_show           = tep_db_prepare_input($_POST['show']);
    $forum_photo_name= tep_db_prepare_input($_POST['forum_photo_name']);
    if(tep_not_null($forum_photo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name) )
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name);
    }
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_FORUM_SHORT_DESCRIPTION, 'error');
     $error=true;
    }
    break;
  case 'add':
  case 'save':
     $category_id       = tep_db_prepare_input($_POST['TR_category']);
     $title             = tep_db_prepare_input($_POST['TR_title']);
     $author            = tep_db_prepare_input($_POST['TR_author']);
     $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
     $description       = tep_db_prepare_input($_POST['TR_description']);
     $is_show           = tep_db_prepare_input($_POST['show']);
     $forum_photo_name= tep_db_prepare_input($_POST['forum_photo_name']);
     if(strlen($description)<=0)
     {
      $messageStack->add(ERROR_FORUM_SHORT_DESCRIPTION, 'error');
      $error=true;
     }
     if(!$error)
     {
	    $description1=valid_html_link($description);
      $sql_data_array=array('category_id'       => $category_id,
                             'title'            => $title,
                             'author'           => $author,
                             'show_date'        => $show_date,
                             'description'      => $description1,
                             'is_show'          => $is_show
                            );
       if(tep_not_null($forum_photo_name))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name))
        {
         $target_file_name=PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$forum_photo_name;
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name,$target_file_name);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name);
         chmod($target_file_name, 0644);
         $sql_data_array['forum_photo']=$forum_photo_name;
         if($edit && tep_not_null($row_check_forum_id['forum_photo']))
         {
          $old_photo= $row_check_forum_id['forum_photo'];
          if(is_file(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$old_photo))
          @unlink(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$old_photo);
         }
        }
       }
       if($edit)
       {
        $sql_data_array['updated']='now()';
        tep_db_perform(FORUM_TABLE, $sql_data_array,'update',"id='".$forum_id."'");
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
       }
       else
       {
        $sql_data_array['inserted']='now()';
        tep_db_perform(FORUM_TABLE, $sql_data_array);
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       }
       tep_redirect(FILENAME_ADMIN1_LIST_OF_FORUMS);
     }
  }
}
////////////////////////////////////////////
if($error)
{
 if($edit)
  $action='edit';
 else
  $action='new';
}
if($action=='new' || $action=='edit' || $action=='back')
{
 if(tep_not_null($row_check_forum_id['forum_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$row_check_forum_id['forum_photo']) )
 {
  $artcle_photo1="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE.$row_check_forum_id['forum_photo']."','')\" class='label'>Preview</a>&nbsp;&nbsp;]";
 }

 if($edit)
 {
  $form=tep_draw_form('forum', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'id='.$forum_id.'&action=preview', 'post', 'enctype="multipart/form-data"   onsubmit="return ValidateForm(this )"');
 }
 else
 {
  $form=tep_draw_form('forum', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'action=preview', 'post', ' enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"');
 }
 $view_list_of_forums='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS).'">'.INFO_TEXT_VIEW_LIST_OF_FORUMS.'</a>';
}
elseif($action=='preview')
{
 if(tep_not_null($forum_photo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$forum_photo_name) )
 {
  $artcle_photo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_TEMP.$forum_photo_name."&size=220");
 }
 elseif(tep_not_null($row_check_forum_id['forum_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$row_check_forum_id['forum_photo']) )
 {
  $artcle_photo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_FORUM_IMAGE.$row_check_forum_id['forum_photo']."&size=220");
 }
 if($edit)
 {
  $form=tep_draw_form('forum', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'id='.$forum_id.'&action=save', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a class="btn btn-secondary" href="#" onclick="set_action(\'back\')">'.IMAGE_BACk.'</a> '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary float-right"');//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
 }
 else
 {
  $form=tep_draw_form('forum', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'action=add', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a class="btn btn-secondary" href="#" onclick="set_action(\'back\')">'.IMAGE_BACk.'</a>'.tep_draw_submit_button_field('',IMAGE_SAVE,'class="btn btn-primary float-right"');//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
 }
 $view_list_of_forums='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS).'">'.INFO_TEXT_VIEW_LIST_OF_FORUMS.'</a>';
}
else
{
//////////////////
///only for sorting starts
$sort_array=array("a.title","ac.category_name","a.is_show ","a.show_date","a.inserted");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

 ///////////// Middle Values
 $now=date("Y-m-d H:i:s");
 $forum_query_raw="select a.*,  ac.category_name from " . FORUM_TABLE ." as a," . FORUM_CATEGORIES_TABLE ." as ac where a.category_id=ac.id order by ".$order_by_clause;
 $forum_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $forum_query_raw, $forum_query_numrows);
 $forum_query = tep_db_query($forum_query_raw);
 //echo tep_db_num_rows($forum_query);
 if(tep_db_num_rows($forum_query) > 0)
 {
  $alternate=1;
  while ($forum = tep_db_fetch_array($forum_query))
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $forum['id']))) && !isset($aInfo) && (substr($action, 0, 3) != 'new'))
   {
    $aInfo = new objectInfo($forum);
    //print_r($aInfo);
   }
   if ( (isset($aInfo) && is_object($aInfo)) && ($forum['id'] == $aInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_FORUMS . '?page='.$_GET['page'].'&id=' . $aInfo->id . '&action=edit\'"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_FORUMS . '?page='.$_GET['page'].'&id=' . $forum['id'] . '\'"';
   }
   $alternate++;
   if ( (isset($aInfo) && is_object($aInfo)) && ($forum['id'] == $aInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page='.$_GET['page'].'&id=' . $forum['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   //$row_check=getAnyTableWhereData(forum_CATEGORY_TABLE,"id='".$forum["category_id"]."'","sub_cat_id");
   $category_name=$forum['category_name'];
   if ($forum['is_show'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $forum['id'] . '&action=forum_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_FORUM_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_FORUM_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_FORUM_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $forum['id'] . '&action=forum_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_FORUM_ACTIVATE, 28, 22) . '</a>';
   }
   $template->assign_block_vars('forum', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'status' => $status,
    'title' => tep_db_output($forum['title']),
    'category' => $category_name,
    'show_date' => tep_date_short($forum['show_date']),
    'inserted' => tep_date_short($forum['inserted']),
    ));
  }
  tep_db_free_result($forum_query);
 }
}


//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':

  // $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  // // $contents[] = array('text' => '<b>' . tep_db_output($aInfo->title) . '</b>');
  // // $contents[] = array('text' => TEXT_DELETE_INTRO);
  // // $contents[] = array('text' => '<br><b>' . tep_db_output($aInfo->title) . '</b>');
  // // $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] . '&id=' . $aInfo->id.'&action=confirm_delete') . '">'.tep_button('Confirm','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] . '&id=' . $aInfo->id) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
  $heading[] = array('text' => '<div class="list-group">
    <div class="font-weight-bold">
    '.TEXT_INFO_HEADING_TITLE.'<p>'.tep_db_output($aInfo->title).'</p></div>
    </div>');
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] . '&id=' . $aInfo->id.'&action=confirm_delete') . '">
  '.IMAGE_CONFIRM.'
  </a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] . '&id=' . $aInfo->id) . '">
  '.IMAGE_CANCEL.'
  </a>  
  </div>');
  break;
 default:
  if (isset($aInfo) && is_object($aInfo))
		{
  //  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  //  $contents[] = array('text' => tep_db_output($aInfo->title));
  //  $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'id=' . $aInfo->id . '&action=edit') . '">'.tep_button('Edit','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] .'&id=' . $aInfo->id . '&action=delete') . '">'.tep_button('Delete','class="btn btn-primary"').'</a>');
  //  $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);

  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">'.TEXT_INFO_HEADING_TITLE.'</div></div>');
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1">'.tep_db_output($aInfo->title).'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'id=' . $aInfo->id . '&action=edit') . '">
  '.IMAGE_EDIT.'
  </a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'page=' . $_GET['page'] .'&id=' . $aInfo->id . '&action=delete') . '">
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
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
////////////////////////////////////////////////////////////////////////

if(!$error && $action=='edit')
{
 $aInfo       = new objectInfo($row_check_forum_id);
 $category_id = $aInfo->category_id;
 $title       = $aInfo->title;
 $author      = $aInfo->author;
 $show_date   = $aInfo->show_date;
 $is_show     = $aInfo->is_show;
 $description = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$aInfo->description);
}
elseif(!$error && $action=='new')
{
 $show_date   = date("Y-m-d");
 $is_show     = 'Yes';
}
if($action=='preview')
{
 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
 'INFO_TEXT_TITLE'       => INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'      => tep_db_output($title),
 'INFO_TEXT_CATEGORY'    => INFO_TEXT_CATEGORY,
 'INFO_TEXT_CATEGORY1'   => get_name_from_table(FORUM_CATEGORIES_TABLE,'category_name','id',$category_id),
 'INFO_TEXT_AUTHOR'      => INFO_TEXT_AUTHOR,
 'INFO_TEXT_AUTHOR1'     => tep_db_output($author),
 'INFO_TEXT_SHOW_DATE'   => INFO_TEXT_SHOW_DATE,
 'INFO_TEXT_SHOW_DATE1'  => tep_db_output(formate_date($show_date)),
 'INFO_TEXT_FORUM_PHOTO' => INFO_TEXT_FORUM_PHOTO,
 'INFO_TEXT_FORUM_PHOTO1'=> $artcle_photo1,
 'INFO_TEXT_SHOW'        => INFO_TEXT_SHOW,
 'INFO_TEXT_SHOW1'       => tep_db_output($is_show),
 'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=> nl2br(stripslashes($description)),
 'button'=>$button,
 'form'=>$form,
 'view_list_of_forums'=>$view_list_of_forums,
  ));
 $template->pparse('preview');
}
elseif($action=='new' || $action=='edit' || $action=='back')
{
 $template->assign_vars(array(
 'INFO_TEXT_TITLE'        => INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'       => tep_draw_input_field('TR_title',$title,'class="form-control form-control-sm"',true),
 'INFO_TEXT_CATEGORY'     => INFO_TEXT_CATEGORY,
 'INFO_TEXT_CATEGORY1'    => LIST_SET_DATA(FORUM_CATEGORIES_TABLE,"",TEXT_LANGUAGE.'category_name','id',TEXT_LANGUAGE."category_name",'name="TR_category" class="form-control form-control-sm"',INFO_TEXT_PLEASE_SELECT."...",'',$category_id),
 'INFO_TEXT_AUTHOR'       => INFO_TEXT_AUTHOR,
 'INFO_TEXT_AUTHOR1'      => tep_draw_input_field('TR_author',$author,'class="form-control form-control-sm"',true),
 'INFO_TEXT_SHOW_DATE'    => INFO_TEXT_SHOW_DATE,
//  'INFO_TEXT_SHOW_DATE1'   => datelisting($show_date, 'name="TR_date" class="form-control form-control-sm d-inline" style="width: 25%"', 'name="TR_month" class="form-control form-control-sm d-inline" style="width: 25%"', 'name="TR_year" class="form-control form-control-sm d-inline" style="width: 25%"', "2004", date("Y")+1, true),
 'INFO_TEXT_SHOW_DATE1'   => datelisting_admin($show_date, 'name="TR_date" class="form-control form-control-sm d-inline" style="width: 25%"', 'name="TR_month" class="form-control form-control-sm d-inline" style="width: 25%"', 'name="TR_year" class="form-control form-control-sm d-inline" style="width: 25%"', "2004", date("Y")+1, true),
 'INFO_TEXT_FORUM_PHOTO'  => INFO_TEXT_FORUM_PHOTO,
 'INFO_TEXT_FORUM_PHOTO1' => tep_draw_file_field("forum_photo").$artcle_photo1,
 'INFO_TEXT_DESCRIPTION'  => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1' => tep_draw_textarea_field('TR_description', 'soft', '90', '4', stripslashes($description), 'class="form-control form-control-sm"', '', true),
 'INFO_TEXT_SHOW'         => INFO_TEXT_SHOW,
 'INFO_TEXT_SHOW1'        => '<div class="form-check form-check-inline">'.tep_draw_radio_field('show', 'Yes', '', $is_show, 'id="radio_show1" class="form-check-input"').'<label for="radio_show1" class="form-check-label">Yes</label></div><div class="form-check form-check-inline">'.tep_draw_radio_field('show', 'No', '', $aInfo->is_show, 'id="radio_show2" class="form-check-input"').'<label for="radio_show2" class="form-check-label">No</label></div>',
 'button'                 => tep_draw_submit_button_field('','Preview','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif',IMAGE_PREVIEW),
 'form'                   => $form,
 'view_list_of_forums'    => $view_list_of_forums,
  ));
 $template->pparse('forum1');
}
else
{
 $template->assign_vars(array(
  'TABLE_HEADING_FORUM_TITLE'     => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_FORUM_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_FORUM_CATEGORY'  => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_FORUM_CATEGORY.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_FORUM_STATUS'    => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_FORUM_STATUS.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_FORUM_SHOW_DATE' => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_FORUM_SHOW_DATE.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_FORUM_DATE_ADDED'=> "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_FORUM_DATE_ADDED.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  'count_rows'                    => $forum_split->display_count($forum_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FORUMS),
  'no_of_pages'                   => $forum_split->display_links($forum_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_button'                    => '<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_FORUMS, 'action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 ));
 $template->pparse('forum');
}
?>