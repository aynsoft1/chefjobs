<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_LIST_OF_NEWSLETTERS);
$template->set_filenames(array('list_of_newsletters' => 'list_of_newsletters.htm','list_of_newsletters1' => 'list_of_newsletters1.htm'));
include_once(FILENAME_BODY);
$error=true;

if(check_login("jobseeker"))
{
 $error=false;
}
elseif(check_login("recruiter"))
{
 $error=false;
}
if($error)
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_LOGIN);
}
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
      $newsletter_attachment=" <br><br> <a class=\"small\" href='".tep_href_link(FILENAME_ATTACHMENT1_DOWNLOAD,'n_id='.$nID)."'><font color=\"blue\"> ".INFO_TEXT_ATTACHMENT_DOWNLOAD." </font> </a>";
     }
     else
     {
      $newsletter_attachment='';
     }
    }
    else $newsletter_attachment='';
     break;
 }
}
if(check_login("jobseeker"))
$LEFT_HTML=LEFT_HTML_JOBSEEKER;
elseif(check_login("recruiter"))
$LEFT_HTML=LEFT_HTML;
############### RESUME LISTING ###############
$table_names=NEWSLETTERS_HISTORY_TABLE." as nh ";
if(check_login("jobseeker"))
 $newsletter_for ="jobseeker";
elseif(check_login("recruiter"))
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
  $list_form= tep_draw_form("list".$ide,FILENAME_LIST_OF_NEWSLETTERS).tep_draw_hidden_field('action','detail');

  $hidden_fields1=tep_draw_hidden_field('id',$row['id']);
  $template->assign_block_vars('newsletter', array(
   'title' => tep_db_output($row['title']),
   'inserted' => tep_date_veryshort($row['date_send']),
   'list_form'=>$list_form.$hidden_fields1,
   'view' => '<a href="#"  onclick="document.list'.$ide.'.submit();"><u>'.TABLE_HEADING_VIEW.'</u></a>',
   'row_selected'=>$row_selected,
   ));
  $alternate++;
  $lower = $lower + 1;
 }
 $plural=($x1=="1")?HEADING_TITLE:INFO_TEXT_NEWSLERRERS;
 $template->assign_vars(array('total'=>"  <font color='red'><b>$x1</b></font> ".$plural));
}
else
{
 $template->assign_vars(array('total'=>INFO_TEXT_NO_NEWSLETTER_AVAILABLE));
}
see_page_number();
tep_db_free_result($result1);
############### RESUME LISTING ###############
$template->assign_vars(array('hidden_fields' => $hidden_fields,
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
 'TEXT_NEWSLETTER_CONTENT'=>$newsletter_content.$newsletter_attachment,
 'back_button'            => '<a href="#" onclick="history.back();" ><button class="btn btn-outline-secondary">Back</button></a>',

 'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>$LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
if($action=='detail')
 $template->pparse('list_of_newsletters1');
else
$template->pparse('list_of_newsletters');

?>