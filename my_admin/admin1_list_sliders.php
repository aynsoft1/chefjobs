<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_SLIDERS);
$template->set_filenames(array('slider' => 'admin1_list_sliders.htm','slider1' => 'admin1_slider.htm','preview' => 'admin1_slider1.htm'));
include_once(FILENAME_ADMIN_BODY);
////////////////
$edit=false;
$error =false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$slider_id=(isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
if(tep_not_null($slider_id))
{
 if(!$row_check_slider_id=getAnyTableWhereData(SLIDER_TABLE,"id='".tep_db_input($slider_id)."'"))
 {
  $messageStack->add_session(MESSAGE_ARTCLE_ERROR, 'error');
  tep_redirect(FILENAME_ADMIN1_LIST_OF_SLIDERS);
 }
 $slider_id=$row_check_slider_id['id'];
 $edit=true;
}
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);
if(tep_not_null($action))
{
 switch($action)
 {
  case 'confirm_delete':
   if($edit && tep_not_null($row_check_slider_id['slider_image']))
   {
    $old_photo= $row_check_slider_id['slider_image'];
    if(is_file(PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$old_photo))
    @unlink(PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$old_photo);
   }
   tep_db_query("delete from ".SLIDER_TABLE." where id='".tep_db_input($slider_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(FILENAME_ADMIN1_LIST_OF_SLIDERS);
   break;
  case 'preview':
    $hidden_fields='';
    $slider_title      = tep_db_prepare_input($_POST['TR_slider_title']);

    $hidden_fields.=tep_draw_hidden_field('TR_slider_title',$slider_title);
     if(strlen($slider_title)<=0)
     {
      $messageStack->add(ERROR_SLIDER_TITLE, 'error');
      $error=true;
     }

    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['slider_image']['name']))
     {
      if($obj_resume = new upload('slider_image', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('jpg','gif','png')))
      {
       $slider_image_name=tep_db_input($obj_resume->filename);
       $hidden_fields.=tep_draw_hidden_field('slider_image_name',$slider_image_name);
      }
     }
     //////// file upload ends //////
    }
    break;
  case 'back':
    $slider_title      = tep_db_prepare_input($_POST['TR_slider_title']);
    $slider_image_name= tep_db_prepare_input($_POST['slider_image_name']);
    if(tep_not_null($slider_image_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name) )
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name);
    }

     if(strlen($slider_title)<=0)
     {
      $messageStack->add(ERROR_SLIDER_TITLE, 'error');
      $error=true;
     }
    break;
  case 'add':
  case 'save':
     $slider_title    = tep_db_prepare_input($_POST['TR_slider_title']);
     $slider_image_name= tep_db_prepare_input($_POST['slider_image_name']);
     $now=date("Y-m-d H:i:s");
     if(strlen($slider_title)<=0)
     {
      $messageStack->add(ERROR_SLIDER_TITLE, 'error');
      $error=true;
     }
     if(strlen($slider_image_name)<=0 && !$edit)
     {
      $messageStack->add(ERROR_SLIDER_IMAGE, 'error');
      $error=true;
     }


     if(!$error)
     {
      $sql_data_array=array( 'slider_title'     => $slider_title,
                             );
       if(tep_not_null($slider_image_name))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name))
        {
         $target_file_name=PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$slider_image_name;
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name,$target_file_name);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name);
         chmod($target_file_name, 0644);
         $sql_data_array['slider_image']=$slider_image_name;
         if($edit && tep_not_null($row_check_slider_id['slider_image']))
         {
          $old_photo= $row_check_slider_id['slider_image'];
          if(is_file(PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$old_photo))
          @unlink(PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$old_photo);
         }
        }	
       }
       if($edit)
       {
        tep_db_perform(SLIDER_TABLE, $sql_data_array,'update',"id='".$slider_id."'");
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
       }
       else
       {
		$sql_data_array['inserted']	= $now;
 
        tep_db_perform(SLIDER_TABLE, $sql_data_array);
        $row_id=getAnyTableWhereData(SLIDER_TABLE," inserted='".tep_db_input($now)."' and slider_title='".tep_db_input($slider_title)."' order by  slider_title asc",'id');
        $id = $row_id['id'];
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       }
       tep_redirect(FILENAME_ADMIN1_LIST_OF_SLIDERS);
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
 if(tep_not_null($row_check_slider_id['slider_image']) && is_file(PATH_TO_MAIN_PHYSICAL_SLIDER_IMAGE.$row_check_slider_id['slider_image']) )
 {
  $slider_image1="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_SLIDER_IMAGE.$row_check_slider_id['slider_image']."','')\" class='label'>Preview</a>&nbsp;&nbsp;]";
 }

 if($edit)
 {
  $form=tep_draw_form('slider', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'id='.$slider_id.'&action=preview', 'post', 'enctype="multipart/form-data"   onsubmit="return ValidateForm(this )"');
 }
 else
 {
  $form=tep_draw_form('slider', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'action=preview', 'post', ' enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"');
 }
 $view_list_of_sliders='<a class="btn btn-text text-primary border" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS).'">'.INFO_TEXT_VIEW_LIST_OF_SLIDERS.'</a>';
}
elseif($action=='preview')
{
 if(tep_not_null($slider_image_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$slider_image_name) )
 {
  $slider_image1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_TEMP.$slider_image_name,'','600','300');
 }
 elseif(tep_not_null($row_check_slider_id['slider_image']) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$row_check_slider_id['slider_image']) )
 {
  $slider_image1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_SLIDER_IMAGE.$row_check_slider_id['slider_image'],'','900','500');
 }
 if($edit)
 {
  $form=tep_draw_form('slider', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'id='.$slider_id.'&action=save', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a href="#" onclick="set_action(\'back\')">'.tep_button('Back','class="btn btn-secondary"').'</a> '.tep_draw_submit_button_field('','Update','class="btn btn-primary"');
 }
 else
 {
  $form=tep_draw_form('slider', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'action=add', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a href="#" onclick="set_action(\'back\')">'.tep_button('Back','class="btn btn-secondary mr-2"').'</a>'.tep_draw_submit_button_field('','Save','class="btn btn-primary"');
 }
 $view_list_of_sliders='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS).'">'.INFO_TEXT_VIEW_LIST_OF_SLIDERS.'</a>';
}
else
{
//////////////////
///only for sorting starts
$sort_array=array("s.slider_title","s.inserted");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

 ///////////// Middle Values
 $now=date("Y-m-d H:i:s");
 $slider_query_raw="select * from " . SLIDER_TABLE ." as s where inserted <='$now' order by ".$order_by_clause;//show_date desc, inserted desc";
 $slider_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $slider_query_raw, $slider_query_numrows);
 $slider_query = tep_db_query($slider_query_raw);
 //echo tep_db_num_rows($slider_query);
 if(tep_db_num_rows($slider_query) > 0)
 {
  $alternate=1;
  while ($slider = tep_db_fetch_array($slider_query))
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $slider['id']))) && !isset($sInfo) && (substr($action, 0, 3) != 'new'))
   {
    $sInfo = new objectInfo($slider);
    //print_r($sInfo);
   }
   if ( (isset($sInfo) && is_object($sInfo)) && ($slider['id'] == $sInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_SLIDERS . '?page='.$_GET['page'].'&id=' . $sInfo->id . '&action=edit\'"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_SLIDERS . '?page='.$_GET['page'].'&id=' . $slider['id'] . '\'"';
   }
   $alternate++;
   if ( (isset($sInfo) && is_object($sInfo)) && ($slider['id'] == $sInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'page='.$_GET['page'].'&id=' . $slider['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   $template->assign_block_vars('slider', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'slider_title' => tep_db_output($slider['slider_title']),
    'inserted' => tep_date_short($slider['inserted']),
    ));
  }
  tep_db_free_result($slider_query);
 }
}


//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  $contents[] = array('text' => '<b>' . tep_db_output($sInfo->title) . '</b>');
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . tep_db_output($sInfo->title) . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'page=' . $_GET['page'] . '&id=' . $sInfo->id.'&action=confirm_delete') . '">'.tep_button('Confirm','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'page=' . $_GET['page'] . '&id=' . $sInfo->id) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
  break;
 default:
  if (isset($sInfo) && is_object($sInfo))
		{
   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
   $contents[] = array('text' => tep_db_output($sInfo->slider_title));
   $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'id=' . $sInfo->id . '&action=edit') . '">'.tep_button('Edit','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'page=' . $_GET['page'] .'&id=' . $sInfo->id . '&action=delete') . '">'.tep_button('Delete','class="btn btn-secondary"').'</a>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
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
 $sInfo       = new objectInfo($row_check_slider_id);
 $slider_title       = $sInfo->slider_title;
}
elseif(!$error && $action=='new')
{
 $inserted   = date("Y-m-d");
}
if($action=='preview')
{
//echo $slider_title;
 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
 'INFO_TEXT_TITLE'       => INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'      => tep_db_output($slider_title),
 'INFO_TEXT_SLIDER_IMAGE'=>INFO_TEXT_SLIDER_IMAGE,
 'INFO_TEXT_SLIDER_IMAGE1'=>$slider_image1,
 'button'=>$button,
 'form'=>$form,
 'view_list_of_sliders'=>$view_list_of_sliders,
  ));
 $template->pparse('preview');
}
elseif($action=='new' || $action=='edit' || $action=='back')
{
 $template->assign_vars(array(
 'INFO_TEXT_TITLE'=>INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'=>tep_draw_input_field('TR_slider_title',$slider_title,'class="form-control form-control-sm"',true),
 'INFO_TEXT_SLIDER_IMAGE'=>INFO_TEXT_SLIDER_IMAGE,
 'INFO_TEXT_SLIDER_IMAGE1'=>tep_draw_file_field("slider_image").$slider_image1,
 'button'=>tep_draw_submit_button_field('','Preview','class="btn btn-primary"'),
 'form'=>$form,
 'view_list_of_sliders'=>$view_list_of_sliders,
  ));
 $template->pparse('slider1');
}
else
{
 $template->assign_vars(array(
'TABLE_HEADING_ARTICLE_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_ARTICLE_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_ARTICLE_DATE_ADDED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_ARTICLE_DATE_ADDED.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'count_rows'=>$slider_split->display_count($slider_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ARTICLES),
  'no_of_pages'=>$slider_split->display_links($slider_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_SLIDERS, 'action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 ));
 $template->pparse('slider');
}
?>