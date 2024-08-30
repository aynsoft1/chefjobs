<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS);
$template->set_filenames(array('list_of_newsletters' => 'admin1_list_of_newsletters.htm','list_of_newsletters1' => 'admin1_list_of_newsletters1.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
//  print_r($_POST);//die();
#######################################################################
if(tep_not_null($action))
{
 switch($action)
 {
  ////////////////////////////////////////////
  case 'detail':
   $row1=getAnyTableWhereData(NEWSLETTERS_HISTORY_TABLE,"id ='".(int)tep_db_input($_POST['id'])."'","id,content,attachment_file");
   $newsletter_content=stripslashes($row1['content']);
   $newsletter_attachment=tep_db_output($row1['attachment_file']);
   $nID=tep_db_output($row1['id']);
   if($newsletter_attachment!='')
    {
     if(is_file(PATH_TO_MAIN_PHYSICAL_NEWSLETTER_HISTORY.$newsletter_attachment))
     {
      $newsletter_attachment=" <br><br> <a class=\"small\" href='".tep_href_link(FILENAME_ATTACHMENT1_DOWNLOAD,'n_id='.$nID)."'><font color=\"blue\"> Attachment Download </font> </a>";
     }
     else
     {
      $newsletter_attachment='';
     }
    }
    else $newsletter_attachment='';
     break;
  case 'delete':
     $row1=getAnyTableWhereData(NEWSLETTERS_HISTORY_TABLE,"id ='".(int)tep_db_input($_POST['id'])."'","id,attachment_file,send_to");
     $newsletter_attachment=$row1['attachment_file'];
     if($newsletter_attachment!='')
     {
      if(is_file(PATH_TO_MAIN_PHYSICAL_NEWSLETTER_HISTORY.$newsletter_attachment))
      {
       @unlink(PATH_TO_MAIN_PHYSICAL_NEWSLETTER_HISTORY.$newsletter_attachment);
      }
     }
     tep_db_query("delete from ".NEWSLETTERS_HISTORY_TABLE." where id='".(int)tep_db_input($row1['id'])."'");
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS,'newsletter_for='.$row1['send_to']));
     break;
 }
}
############### RESUME LISTING ###############
$table_names=NEWSLETTERS_HISTORY_TABLE." as nh ";
if($_GET['newsletter_for']=='jobseeker')
 $newsletter_for ="jobseeker";
elseif($_GET['newsletter_for']=='recruiter')
 $newsletter_for ="recruiter";
$whereClause.=" nh.send_to='".$newsletter_for."' ";

$query1 = "select count(nh.id) as x1 from $table_names where $whereClause ";
//echo "<br>$query1";//exit;
$result1=tep_db_query($query1);
$tt_row=tep_db_fetch_array($result1);
$x1=$tt_row['x1'];
///only for sorting starts
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$sort_array=array("nh.title",'nh.date_send');
$obj_sort_by_clause=new sort_by_clause($sort_array,'nh.title asc, nh.date_send desc');
$order_by_clause=$obj_sort_by_clause->return_value;
$see_before_page_number_array=see_before_page_number($sort_array,$field,'nh.date_send ',$order,'desc',$lower,'0',$higher,'20');
$lower=$see_before_page_number_array['lower'];
$higher=$see_before_page_number_array['higher'];
$field=$see_before_page_number_array['field'];
$order=$see_before_page_number_array['order'];
$hidden_fields.=tep_draw_hidden_field('sort',$sort);
//$template->assign_vars(array('INFO_TEXT_JOBSEEKER_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  //                            'INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>"
//));
///only for sorting ends
$totalpage=ceil($x1/$higher);
$field_names=" nh.id,nh.title,nh.date_send";
$query = "select $field_names from $table_names where $whereClause ORDER BY $field $order limit $lower,$higher ";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo $x;exit;
$pno= ceil($lower+$higher)/($higher);
if($x > 0 && $x1 > 0)
{
 $alternate=1;
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["id"];
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
  $list_form= tep_draw_form("list".$alternate,PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS).tep_draw_hidden_field('action','detail');
  $list_form1= tep_draw_form("list_".$alternate,PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS).tep_draw_hidden_field('action','delete');
  $hidden_fields1=tep_draw_hidden_field('id',$row['id']);
  $template->assign_block_vars('newsletter', array(
   'title' => tep_db_output($row['title']),
   'inserted' => tep_date_long($row['date_send']),
   'list_form'=>$list_form.$hidden_fields1,
   'list_form1'=>$list_form1.$hidden_fields1,
   'view' => '<a href="#"  onclick="document.list'.$alternate.'.submit();"><u>view</u></a>',
   'delete' => '<a href="#"  onclick="document.list_'.$alternate.'.submit();"><u>delete</u></a>',
   'row_selected'=>$row_selected,
   ));
  $alternate++;
  $lower = $lower + 1;
 }
 $plural=($x1=="1")?"Newsletter ":"Newsletters ";
 $template->assign_vars(array('total'=>"  <font color='red'><b>$x1</b></font> ".$plural));
}
else
{
 $template->assign_vars(array('total'=>" No newsletter available"));
}
see_page_number();
tep_db_free_result($result1);
############### NewsLetter LISTING ###############


$template->assign_vars(array('hidden_fields' => $hidden_fields,
 'HEADING_TITLE'=>sprintf(HEADING_TITLE,ucfirst($_GET['newsletter_for'])),
 'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
 'TEXT_NEWSLETTER_CONTENT'=>$newsletter_content.$newsletter_attachment,
 'back_button'            => '<a href="#" onclick="history.back();" >'.tep_button('Cancel','class="btn btn-secondary"').'</a>',

 'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,
 'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
if($action=='detail')
 $template->pparse('list_of_newsletters1');
else
$template->pparse('list_of_newsletters');

?>