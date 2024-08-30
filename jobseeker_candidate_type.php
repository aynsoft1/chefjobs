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
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CANDIDATE_TYPE);
$template->set_filenames(array('jobseeker_candidate_type' => 'jobseeker_candidate_type.htm'));
include_once(FILENAME_BODY);
/*$row=getAnyTableWhereData(JOBSEEKER_TABLE.' as j',"j.jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'j.jobseeker_featured');
if($row['jobseeker_featured']=='Yes')
{
 $messageStack->add_session(FEATURED_MEMBER_ERROR, 'error');
 tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
}*/
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (isset($_POST['query_string']))
 $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
 $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
 $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
 if(tep_not_null($action))
 {
  $candidate_type=tep_db_prepare_input($_POST['TR_candidate_type']);
  if($candidate_type=='Yes')
  {
   $jobseeker=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'","*");
   $jobseeker_email=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'","*");
   $jobseeker_subject1='Jobseeker asking for featured membership';
   $email_text1="<br>Jobseeker name : ".$jobseeker['jobseeker_first_name'].' '.$jobseeker['jobseeker_last_name'];
   $email_text1.="<br>jobseeker email : ".$jobseeker_email['jobseeker_email_address'];
   $email_text1.="<br>";
     tep_mail(SITE_OWNER,ADMIN_EMAIL,$jobseeker_subject1, $email_text1, SITE_OWNER, ADMIN_EMAIL);
   //tep_db_query('update '.JOBSEEKER_TABLE ." set jobseeker_featured='$candidate_type' where jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'"); 
   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RATES));
  }
  else
  {
   if($_SESSION['sess_new_jobseeker']=='y')
   {
    unset($_SESSION['sess_new_jobseeker']);
    $jobseeker=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'","*");
    $jobseeker_email=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'","*");
    $jobseeker_subject1='Jobseeker asking for featured membership';
    $email_text1="<br>Jobseeker name : ".$jobseeker['jobseeker_first_name'].' '.$jobseeker['jobseeker_last_name'];
    $email_text1.="<br>jobseeker email : ".$jobseeker_email['jobseeker_email_address'];
    $email_text1.="<br>";
     tep_mail(SITE_OWNER,ADMIN_EMAIL,$jobseeker_subject1, $email_text1, SITE_OWNER, ADMIN_EMAIL);
    //tep_db_query('update '.JOBSEEKER_TABLE ." set jobseeker_featured='$candidate_type' where jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'"); 
    $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
			 tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string));
   }
   else
   {
    //tep_db_query('update '.JOBSEEKER_TABLE ." set jobseeker_featured='$candidate_type' where jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'"); 
			 tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
   }
  }
 }
 
$template->assign_vars(array(
 'HEADING_TITLE'        => HEADING_TITLE,
 'INFO_TEXT_MAIN'       => INFO_TEXT_MAIN,
 'LEFT_BOX_WIDTH'       => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'      => RIGHT_BOX_WIDTH1,
 'INFO_TEXT_BUTTON'     => tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT),
 'candidate_form'       => tep_draw_form('candidate', FILENAME_JOBSEEKER_CANDIDATE_TYPE,'', 'post', ' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add').tep_draw_hidden_field('query_string',$query_string),
 'INFO_TEXT_CANDIDATE_TYPE'=>INFO_TEXT_CANDIDATE_TYPE,
 'INFO_TEXT_CANDIDATE_TYPE1'=>tep_draw_radio_field('TR_candidate_type', 'Yes', '', $candidate_type, 'id="radio_candidate_type1"  ').'&nbsp;<label for="radio_candidate_type1" onMouseOver="this.style.color=\'#0000aa\'" onMouseOut="this.style.color=\'#000000\'">'.INFO_TEXT_FEATURED.'</label>'.tep_draw_radio_field('TR_candidate_type', 'No', 'No', $candidate_type, 'id="radio_candidate_type2" ').'&nbsp;<label for="radio_candidate_type2" onMouseOver="this.style.color=\'#0000aa\'" onMouseOut="this.style.color=\'#000000\'">'.INFO_TEXT_REGULAR.'</label>',
 'LEFT_HTML'        => LEFT_HTML,
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('jobseeker_candidate_type');
?>