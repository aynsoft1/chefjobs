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
$heading = array();
$contents = array();
$heading[] = array('text'  => BOX_HEADING_EMAIL_TEMPLATE,
																			'link'  => FILENAME_ADMIN1_ADMIN_JOB_ALERT."?selected_box=email_template",
                   'default_row'=>(($_SESSION['selected_box'] == 'email_template') ?'1':''),
                   'text_image'=>'<ion-icon name="mail-open-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                   );

if ($_SESSION['selected_box'] == 'email_template')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOB_ALERT, BOX_EMAIL_TEMPLATE_JOB_ALERT);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOB_ALERT_DIRECT, BOX_EMAIL_TEMPLATE_JOB_ALERT_DIRECT);
if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_RECRUITER_ACCOUNT_ALERT, BOX_EMAIL_TEMPLATE_ADMIN_RECRUITER_ACCOUNT_ALERT);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_RESUME_ALERT, BOX_EMAIL_TEMPLATE_RESUME_ALERT);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOBSEEKER_REGISTRATION, BOX_EMAIL_TEMPLATE_JOBSEEKER_REGISTRATION);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
	$content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOBSEEKER_ACCOUNT_ALERT, BOX_EMAIL_TEMPLATE_JOBSEEKER_ACCOUNT_ALERT);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, BOX_EMAIL_TEMPLATE_RECRUITER_REGISTRATION);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_APPLICATION_SEND_TEMPLATE, BOX_EMAIL_TEMPLATE_APPLICATION_SEND_TEMPLATE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_EXPIRED_JOB_ALERT, BOX_EMAIL_TEMPLATE_EXPIRED_JOB_ALERT_TEMPLATE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, BOX_EMAIL_TEMPLATE_RECRUITER_ORDER_INVOICE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_REC_ORD_UPDATE_TMPL, BOX_EMAIL_TEMPLATE_RECRUITER_ORDER_UPDATE_INVOICE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_JOB_POST_INVOICE_TMPL, BOX_EMAIL_TEMPLATE_JOB_POST_INVOICE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
	$content=tep_admin_files_boxes(FILENAME_ADMIN1_INVITE_FRIEND_TMPL, BOX_EMAIL_TEMPLATE_ADMIN_INVITE_FRIENDS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOBSEEKER_ORDER_TEMPLATE, BOX_EMAIL_TEMPLATE_JOBSEEKER_ORDER_INVOICE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ADMIN_JOBSEEKER_ORD_UPDATE_TMPL, BOX_EMAIL_TEMPLATE_JOBSEEKER_ORDER_UPDATE_INVOICE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
  $content=tep_admin_files_boxes(ADMIN_INTERVIEW_TEMPLATE, BOX_HEADING_INTERVIEW_TEMPLATE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
	}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>