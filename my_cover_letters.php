<?php
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
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
$template->set_filenames(array('cover_letter'=>'cover_letter.htm','cover' => 'my_cover_letters.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'cover_letter.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$cID = (isset($_GET['cID']) ? $_GET['cID'] : '');

//////////////////
$edit=false;
if(tep_not_null($cID))
{
 $cover_letter_id=(int)$_GET['cID'];
 if(!$row_check=getAnyTableWhereData(COVER_LETTER_TABLE,"cover_letter_id='".tep_db_input($cover_letter_id)."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'"))
 {
  $messageStack->add_session(MESSAGE_COVER_LETTER_ERROR, 'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
 }
 $edit=true;
}
if($edit)
{
 $cover_letter_name=$row_check['cover_letter_name'];
 $description=$row_check['cover_letter'];
	$add_save_button='<button class="btn btn-primary" type="submit">Update</button>';
	$add_save_button.="&nbsp;<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS)."' type='button' class='btn btn-outline-secondary mx-2 m-dblock'>Cancel</a>";
 $form=tep_draw_form('cover_letter', FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, 'cID='.$cover_letter_id.'&action=edit', 'post', 'onsubmit="return ValidateForm(this)"');
}
else
{
	$add_save_button='<button class="btn btn-primary" type="submit">Save</button>';
	$add_save_button.="&nbsp;<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS)."' type='button' class='btn btn-outline-secondary mx-2 m-dblock'>Cancel</a>";
 $form=tep_draw_form('cover_letter', FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, 'action=new', 'post', 'onsubmit="return ValidateForm(this)"');
}

if($action=='add_new' || $action=="duplicate")
{
 $temp_cover_letter_no=no_of_records(COVER_LETTER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
 if($temp_cover_letter_no >= MAX_NUM_OF_COVER_LETTERS)
 {
  $messageStack->add_session(sprintf(ERROR_EXCEED_MAX_NO_COVERLETTER,MAX_NUM_OF_COVER_LETTERS), 'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
 }
}
// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'duplicate':
   $row=getAnyTableWhereData(COVER_LETTER_TABLE,"cover_letter_id='".$cover_letter_id."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
   $sql_data_array["jobseeker_id"]=$row['jobseeker_id'];
   $makeCoverLetterName = $row['cover_letter_name'].' 1';
   $i=2;
   while($row_check=getAnyTableWhereData(COVER_LETTER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and cover_letter_name='".tep_db_input($makeCoverLetterName)."'"))
   {
    $makeCoverLetterName = $row['cover_letter_name'].' '.$i;
    $i++;
   }
   $sql_data_array["cover_letter_name"]= $makeCoverLetterName;
   $sql_data_array["cover_letter"]=$row['cover_letter'];
   $sql_data_array["inserted"]='now()';
   tep_db_perform(COVER_LETTER_TABLE, $sql_data_array);
   $messageStack->add_session(MESSAGE_SUCCESS_DUPLICATED, 'success');
   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS));
   break;
  case 'delete':
   if($edit)
   {
    tep_db_query("delete from ".COVER_LETTER_TABLE." where cover_letter_id='".$cover_letter_id."'");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
   }
   else
   {
    $messageStack->add_session(MESSAGE_COVER_LETTER_ERROR, 'error');
    tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
   }
   break;
  case 'new':
  case 'edit':
   $cover_letter_name=tep_db_prepare_input($_POST['cover_letter_name1']);
   $description=stripslashes($_POST['description']);
   $error=false;

   if(strlen($cover_letter_name) <=0)
   {
    $error = true;
    $messageStack->add(INFO_TEXT_COVER_LETTER_NAME_ERROR,'jobseeker_cover_letter');
   }
   if($edit)
   {
    if($row_check=getAnyTableWhereData(COVER_LETTER_TABLE,"cover_letter_id!='".$cover_letter_id."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."' and cover_letter_name='".tep_db_input($cover_letter_name)."'"))
    {
     $error = true;
     $messageStack->add(SAME_COVER_LETTER_NAME_ERROR,'jobseeker_cover_letter');
    }
   }
   else
   {
    if($row_check=getAnyTableWhereData(COVER_LETTER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and cover_letter_name='".tep_db_input($cover_letter_name)."'"))
    {
     $error = true;
     $messageStack->add(SAME_COVER_LETTER_NAME_ERROR,'jobseeker_cover_letter');
    }
   }
   if (strlen($description) <=0)
   {
    $error = true;
    $messageStack->add(INFO_TEXT_COVER_LETTER_DESCRIPTION_ERROR,'jobseeker_cover_letter');
   }
   if(!$error)
   {
     $sql_data_array=array('jobseeker_id'=>$_SESSION['sess_jobseekerid'],
                           'cover_letter_name'=>$cover_letter_name,
                           'cover_letter'=>$description,
                           );
     if($edit)
     {
      $sql_data_array['updated']='now()';
      tep_db_perform(COVER_LETTER_TABLE, $sql_data_array,"update","cover_letter_id='".$cover_letter_id."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
      tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS));
     }
     else
     {
      $sql_data_array['inserted']='now()';
      tep_db_perform(COVER_LETTER_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_SAVED, 'success');
      tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS));
     }
   }
   break;
 }
}
//////////////////////////////
///only for sorting starts
$sort_array=array('c.cover_letter_name','c.inserted');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'c.inserted desc');
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

$db_cover_letter_query_raw = "select c.* from " . JOBSEEKER_LOGIN_TABLE . " as jl, ".COVER_LETTER_TABLE." as c  where jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jl.jobseeker_id=c.jobseeker_id order by ".$order_by_clause;
//echo $db_cover_letter_query_raw;
$db_cover_letter_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LIST_OF_COVER_LETTERS, $db_cover_letter_query_raw, $db_cover_letter_query_numrows);
$db_cover_letter_query = tep_db_query($db_cover_letter_query_raw);
$db_cover_letter_num_row = tep_db_num_rows($db_cover_letter_query);
//echo $db_cover_letter_num_row;
if($db_cover_letter_num_row > 0)
{
 $alternate=1;
 while ($cover_letter = tep_db_fetch_array($db_cover_letter_query))
 {
  $add_edit_delete_string='';
  $ide=$cover_letter['cover_letter_id'];
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $edit_string='<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS,'cID='.$ide).'">'.TABLE_HEADING_EDIT.'</a>';
  $delete_string="<a href='#' onClick=\"encode_delete('".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, 'cID='.$ide.'&action=delete')."');return false;\">".TABLE_HEADING_DELETE."</a>";
  $view_string='<a href="#" onclick="popUp(\''.FILENAME_JOBSEEKER_PREVIEW_COVER_LETTER.'?cID='.$ide.'\')">'.TABLE_HEADING_VIEW.'</a>';
  $duplicate_string='<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS,'cID='.$ide.'&action=duplicate').'">'.TABLE_HEADING_DUPLICATE.'</a>';
  $template->assign_block_vars('cover_letter', array( 'row_selected' => $row_selected,
   'name' => tep_db_output($cover_letter['cover_letter_name']),
   'inserted' => tep_date_short(tep_db_output($cover_letter['inserted'])),
   'updated' => tep_date_short(tep_db_output($cover_letter['updated'])),
   'edit_string' => $edit_string,
			'delete_string' => $delete_string,
			'view_string' => $view_string,
			'duplicate_string' => $duplicate_string,
   ));
  $alternate++;
 }
}
/////
$RIGHT_HTML="";
$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH1;
/////

if($action=='new' || $action=='add_new' || $edit)
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'INFO_TEXT_COVER_LETTER_NAME'=>INFO_TEXT_COVER_LETTER_NAME,
  'INFO_TEXT_COVER_LETTER_NAME1'=>tep_draw_input_field('cover_letter_name1', $cover_letter_name,'size="32" maxlength="32" class="form-control required"',false),
  'INFO_TEXT_COVER_LETTER_DESCRIPTION'=>INFO_TEXT_COVER_LETTER_DESCRIPTION,
  'INFO_TEXT_COVER_LETTER_DESCRIPTION1'=>tep_draw_textarea_field('description', 'soft', '70', '8', stripslashes($description), 'id="mytextareas" class="form-control required"',false,false),
  'add_save_button'=>$add_save_button,
  'form'=>$form,
  'hidden_fields'=>$hidden_fields,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>RIGHT_HTML,
		'LEFT_HTML_JOBSEEKER' =>LEFT_HTML_JOBSEEKER,
  'update_message'=>$messageStack->output()));
 $template->pparse('cover_letter');
}
else
{
 if($messageStack->size('jobseeker_cover_letter') > 0)
  $update_message=$messageStack->output('jobseeker_cover_letter');
 else
  $update_message=$messageStack->output();
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'INFO_TEXT_MAX_COVERLETTER'=>sprintf(INFO_TEXT_MAX_COVERLETTER,MAX_NUM_OF_COVER_LETTERS),
  'TABLE_HEADING_COVER_LETTER_NAME'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_COVER_LETTER_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_COVER_LETTER_VALUE'=>TABLE_HEADING_COVER_LETTER_VALUE,
  'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_UPDATED'=>TABLE_HEADING_UPDATED,
  'TABLE_HEADING_EDIT'=>TABLE_HEADING_EDIT,
		'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
		'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,
		'TABLE_HEADING_DUPLICATE'=>TABLE_HEADING_DUPLICATE,
  'count_rows'=>$db_cover_letter_split->display_count($db_cover_letter_query_numrows, MAX_DISPLAY_LIST_OF_COVER_LETTERS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COVER_LETTERS),
  'no_of_pages'=>$db_cover_letter_split->display_links($db_cover_letter_query_numrows, MAX_DISPLAY_LIST_OF_COVER_LETTERS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','cID','action'))),
  'add_new'=>"<a class='btn btn-primary mmt-15' href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, 'action=add_new')."' ><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-plus-lg' viewBox='0 0 16 16'>
  <path fill-rule='evenodd' d='M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z'/>
</svg> ".INFO_TEXT_ADD_COVER_LETTER."</a>",
  'hidden_fields'=>$hidden_fields,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>'',
		'LEFT_HTML_JOBSEEKER' =>LEFT_HTML_JOBSEEKER,
  'RIGHT_HTML'=>RIGHT_HTML,
  'update_message'=>$update_message));
 $template->pparse('cover');
}
?>