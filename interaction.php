<?
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_EMPLOYER_INTERACTION);
$template->set_filenames(array('email'  =>'send_email.htm','email_message'  =>'email_message.htm','email_fead_back'  =>'email_fead_back.htm','preview'=>'preview_email.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'interaction.js';
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$action         = (isset($_POST['action']) ? $_POST['action'] : '');
$application_id = (isset($_POST['application_id']) ? $_POST['application_id'] : '');
$mail_id        = (isset($_POST['mail_id']) ? $_POST['mail_id'] : '');
$search_id          = ((isset($_GET['search_id'])  ) ? $_GET['search_id'] : '');
if(isset($_GET['query_string'])  && $_GET['query_string']!='')
{
 $application_id =check_data($_GET['query_string'],"=","application","application_id");
}

////////////////////
if(isset($_GET['query_string3'])  && $_GET['query_string3']!='')
{
 $search_id =check_data1($_GET['query_string3'],"*=*","application","application_id");
}

/////////////////////////////
if(isset($_GET['query_string2'])  && $_GET['query_string2']!='')
{
 $mail_id = check_data($_GET['query_string2'],"=+=","mail","send_id");
}
if(tep_not_null($mail_id))
if($row_check1=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE," id='".$mail_id."'","*"))
{
 $application_id = $row_check1['application_id'];
}
if(tep_not_null($search_id))
{
 if($row_check_search=getAnyTableWhereData(APPLICATION_TABLE ,"application_id='".tep_db_input($search_id)."'","id"))
  $application_id =$row_check_search['id'];
 else
  $search_id='';
}
if(!$row_check=getAnyTableWhereData(APPLICATION_TABLE . " as a left join  ".JOBSEEKER_TABLE." as j on (a.jobseeker_id=j.jobseeker_id) left join  ".JOB_TABLE. " as jb on (a.job_id=jb.job_id) left join  ".JOBSEEKER_LOGIN_TABLE." as jl on (j.jobseeker_id=jl.jobseeker_id) " ," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."'  ","a.id,jb.job_id,jobseeker_privacy,jobseeker_email_address,jb.job_title,j.jobseeker_first_name,j.jobseeker_middle_name,j.jobseeker_last_name,j.jobseeker_id"))
{
  if ($_GET['query_type'] === 'contact' && $_GET['js']) {
    $jID = $_GET['js'];
    $row_check = getAnyTableWhereData(JOBSEEKER_TABLE." as j 
              left join  ".JOBSEEKER_LOGIN_TABLE." as jl on (j.jobseeker_id=jl.jobseeker_id) " ,
              " j.jobseeker_id='".$jID."'",
              "j.jobseeker_id,j.jobseeker_privacy,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_middle_name,j.jobseeker_last_name");
  }else{
    $messageStack->add_session(ERROR_APPLICATION_NOT_EXIST, 'error');
    tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS);
  }
}

$row_company=getAnyTableWhereData(RECRUITER_TABLE." as r" ,"r.recruiter_id ='".$_SESSION['sess_recruiterid']."'","r.recruiter_company_name");

define('APPLICATION_REPLY_MAIL',tep_db_output($row_company['recruiter_company_name']));
//define('APPLICATION_REPLY_MAIL',tep_db_output($row_company['recruiter_company_name']."@".SITE_TITLE));
$job_id=(int)$row_check['job_id'];

$jobseeker_privacy=(int)$row_check['jobseeker_privacy'];
$show_detail=(($jobseeker_privacy==2 || $jobseeker_privacy==3)?true:false);
$hidden=MESSAGE_JOBSEEKER_PRIVACY;
$email_address=stripslashes($row_check['jobseeker_email_address']);
//$display_email_address=(($show_detail)?$row_check['jobseeker_email_address']:$hidden);
$display_email_address=(($show_detail)?$row_check['jobseeker_first_name'].' '.$row_check['jobseeker_middle_name'].' '.$row_check['jobseeker_last_name']:$hidden);

if(tep_not_null($action))
{
 switch($action)
 {
  case 'preview':
    //$email_address=$_POST['TR_email_address'];
    //$TREF_from=$_POST['TREF_from'];
    $TR_subject=$_POST['TR_subject'];
    $email_text=stripslashes($_POST['message1']);
    //$hidden_fields.=tep_draw_hidden_field('TR_email_address',$email_address);
    //$hidden_fields.=tep_draw_hidden_field('TREF_from',$TREF_from);
    $hidden_fields.=tep_draw_hidden_field('TR_subject',$TR_subject);
    $hidden_fields.=tep_draw_hidden_field('message1',$email_text);
    $error=false;
    if(tep_validate_email($email_address) == false)
    {
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'error');
    }
    //if(tep_validate_email($TREF_from) == false)
    //{
    // $error = true;
    // $messageStack->add(EMAIL_ADDRESS1_INVALID_ERROR,'error');
    //}
    if (strlen($TR_subject) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_SUBJECT_ERROR,'error');
    }
    if (strlen($email_text) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_MESSAGE_ERROR,'error');
    }
    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['attachment']['name']))
     {
      if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('doc','pdf','txt','jpg','gif','png')))
      {
       $attachment_file_name=tep_db_input($obj_resume->filename);
      }
      else
      {
       $error=false;
       $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
      }
     }
     //////// file upload ends //////
     ////////////////   Attachment ///////////////
     if($attachment_file_name!='')
     {
      $hidden_fields.=tep_draw_hidden_field('attachment',stripslashes($attachment_file_name));
     }
    }
    else
     $action='';
   break;
     case 'send':
     case 'back':
    //$email_address= tep_db_prepare_input($_POST['TR_email_address']);
    //$TREF_from    = tep_db_prepare_input($_POST['TREF_from']);
    $TR_subject   = tep_db_prepare_input($_POST['TR_subject']);
    $email_text   = stripslashes($_POST['message1']);
    $attachment   = $_POST['attachment'];
    $error=false;
    if(tep_validate_email($email_address) == false)
    {
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'error');
    }
    //if(tep_validate_email($TREF_from) == false)
    //{
     //$error = true;
     //$messageStack->add(EMAIL_ADDRESS1_INVALID_ERROR,'error');
    //}
    if (strlen($TR_subject) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_SUBJECT_ERROR,'error');
    }
    if (strlen($email_text) <= 0)
    {
     $error = true;
     $messageStack->add(ENTRY_MESSAGE_ERROR,'error');
    }
    if(!$error)
    {
     if($action=='back')
     {
      if($attachment!='')
      if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment))
      @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment);
     }
     else
     {
      if($attachment!='')
      if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment))
      {
       $file_directory=get_file_directory($attachment);
       if(check_directory(PATH_TO_RECRUITER_EMAIL_ATTACHMENT.$file_directory))
       {
        $target_file_name=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$attachment;
        copy(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment,$target_file_name);
        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment);
        chmod($target_file_name, 0644);
       }
      }
      $sql_data_array=array('application_id'=>$application_id,
                            'subject'=>$TR_subject,
                            //'email_address'=>$TREF_from,
                            'sender_id'=>$_SESSION['sess_recruiterid'],
                            'message'=>$email_text,
                            'attachment_file'=>$attachment,
                            'inserted'=>'now()',
                           );
      	//$text = strip_tags($email_text);
       if (SEND_EMAILS == 'true')
       {
        //$message = new email();
        if(tep_not_null($_POST['attachment']))
        {
          $file_directory=get_file_directory($_POST['attachment']);
          $destination=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$_POST['attachment'];
          $file_name = basename($destination);
          //$handle    = fopen($destination, "r");
          //contents = fread($handle, filesize($destination));
          //fclose($handle);
          //if(is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$_POST['attachment']))
          //@unlink(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$_POST['attachment']);
          //$message->add_attachment($contents,substr($file_name,14));
        }/*
        if (EMAIL_USE_HTML == 'true')
        {
         $message->add_html($email_text);
        }
        else
        {
         $message->add_text($text);
        }
        // Send message
         $message->build_message();
         $message->send('', $email_address,tep_db_output($row_company['recruiter_company_name']),APPLICATION_REPLY_MAIL, $TR_subject);
		 */
		 tep_new_mail('',$email_address, $TR_subject, $email_text,APPLICATION_REPLY_MAIL, tep_db_output($row_company['recruiter_company_name']),$destination,substr($file_name,14)) ;
       }

        if ($_GET['js']) {
          $sql_data_array['receiver_id'] = $_GET['js'];
        } else {
          $sql_data_array['receiver_id'] = $row_check['jobseeker_id'];
        }
       tep_db_perform(APPLICANT_INTERACTION_TABLE,$sql_data_array);
       $query_string=encode_string("application=".$application_id."=application_id");
       $messageStack->add_session(MESSAGE_SUCCESS_SENT, 'success');
       if ($_GET['query_type'] == 'contact' && $_GET['js']) {
        tep_redirect(tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_type=contact&js=".$_GET['js']));
       }else{
         tep_redirect(tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_string.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
       }
     }
    }
    else
     $action='';
   break;
   case 'fead_back1':
    $title      = tep_db_prepare_input($_POST['title']);
    $description= stripslashes($_POST['description']);
    $mail_id    = tep_db_prepare_input($_POST['mail_id']);
    $sql_data_array=array('remark_title'=> $title,
                          'remark_description' => $description,
                         );
    $query_string=encode_string("application=".$application_id."=application_id");
    tep_db_perform(APPLICANT_INTERACTION_TABLE, $sql_data_array, 'update', "id='".$mail_id."'");
    $messageStack->add_session(MESSAGE_SUCCESS_REMARK_SET, 'success');
    tep_redirect(tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_string.(tep_not_null($search_id)?"&search_id=".$search_id:"")));
     break;
 }
}
/////////////////////////////////////////////////
$row_1=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_TABLE." as j, ".JOB_TABLE. " as jb "," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=j.jobseeker_id ","a.*,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name");
if ((isset($application_id) && $application_id!='' ) && !isset($aInfo) )
{
 $aInfo = new objectInfo($row_1);
}
if (is_object($aInfo))
{
 $query_string=encode_string("application=".$aInfo->id."=application_id");
 $query_string2=encode_string("application_id=".$aInfo->resume_id."=application_id");
 $query_string3=encode_string("action*=*change_status*=*action");
 $query_string4=encode_string("action*=*rank_it*=*action");
 $query_string5=encode_string("action*=*show_history*=*action");
 $query_string6=encode_string("action*=*applicant_delete*=*action");

 $heading[] = array('params'=>'background="img/emp_left_bar_bg.gif"','text' => '<div class="list-group"><div class="card-header"><h4>' . tep_db_output($aInfo->full_name) . '</h4></div></div>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string2.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27" target="_blank">'.IMAGE_PROFILE.'</a>');
 //$contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"', 'text' =>'<img src="img/red_rec.gif"> <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string4.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'"  class="style27">'.IMAGE_RATING.'</a>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string3.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27">'.IMAGE_CHANGE_STATUS.'</a>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string5.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27">'.INFO_TEXT_SELECTION_HISTORY.'</a>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"', 'text'  => '<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_string.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27">'.IMAGE_CONTACT.'</a>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string2.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27" target="_blank">'.INFO_TEXT_ADD_COMMENT.'</a>');
 $contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string2.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="style27" target="_blank">'.IMAGE_RATING.'</a>');

 //$contents[] = array('align' => 'left','params'=>'class="dataTableRightRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"', 'text' => '<img src="img/red_rec.gif"> <a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT, 'jobID='.$aInfo->job_id).'" class="right_black">'.IMAGE_SELECTED_APPLICATIONS.'</a>')
 //$contents[] = array('align' => 'left','params'=>'class="dataTableRightRow1" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"',  'text' =>'<img src="img/red_rec.gif"> <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'query_string='.$query_string.'&query_string2='.$query_string6.(tep_not_null($search_id)?"&search_id=".$search_id:"")).'" class="right_black">'.INFO_TEXT_DELETE.'</a>');
}
////
if((tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='160';
 $RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='';
}
///////////////////////////////////////////////////////////////////
$template->assign_vars(array(
  'INFO_TEXT_JOB_TITLE'=>tep_db_output($row_check['job_title']),
  'INFO_TEXT_ALL_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id).'">'.INFO_TEXT_ALL_APPLICANT.'</a>',
  'INFO_TEXT_SELECTED_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT,'jobID='.$job_id).'">'.INFO_TEXT_SELECTED_APPLICANT.'</a>',
  'INFO_TEXT_SEARCH_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT).'" >'.INFO_TEXT_SEARCH_APPLICANT.'</a>',
  'INFO_TEXT_JOB_DETAIL'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB,'jobID='.$job_id).'" target="_blank">'.INFO_TEXT_JOB_DETAIL.'</a>',
  'INFO_TEXT_REPORT_PIPELINE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#Pipeline ".'">'.INFO_TEXT_REPORT_PIPELINE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise".'">'.INFO_TEXT_REPORT_ROUNDWISE.'</a>',
  'INFO_TEXT_REPORT_ROUNDWISE_SUMMARY'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id)."#roundwise_summary".'">'.INFO_TEXT_REPORT_ROUNDWISE_SUMMARY.'</a>',
  'INFO_TEXT_VIEW_DATE_REPORT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT,'jobID='.$job_id."#date_report").'">'.INFO_TEXT_VIEW_DATE_REPORT.'</a>',
  'INFO_TEXT_ADD_APPLICANT'=>'<a class="list-group-item list-group-item-action px-3 py-1" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" target="_blank">'.INFO_TEXT_ADD_APPLICANT.'</a>',
 ));
/////////////////////////////////////////////////////////
if($mail_id > 0 && $action=='fead_back')
{
 $query_string=encode_string("application=".$application_id."=application_id");
 $template->assign_vars(array(
  'HEADING_TITLE'     => HEADING_FEADBACK_TITLE,
  'INFO_TEXT_SUBJECT' => INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=> tep_db_output($row_check1['subject']),
  'INFO_TEXT_REMARK' => INFO_TEXT_REMARK,
  'INFO_TEXT_REMARK1'  => tep_draw_input_field('title', tep_db_output($row_check1['remark_title']), 'size="35" class="form-control required" required'),
  'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
  'INFO_TEXT_DESCRIPTION1'=> tep_draw_textarea_field('description', 'soft', '70%', '10', stripslashes($row_check1['remark_description']), '', true, true, 'class="form-control required"'),
  'INFO_TEXT_BACK'    => '<a class="btn btn-sm btn-outline-secondary" href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string=".$query_string).'">'.IMAGE_BACK.'</a>',
  'form'              => tep_draw_form('fead_back',FILENAME_EMPLOYER_INTERACTION,'','post','').tep_draw_hidden_field('mail_id',$mail_id).tep_draw_hidden_field('action','fead_back1'),
  'buttons'           => tep_draw_submit_button_field('','Submit','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_submit.gif', IMAGE_SUBMIT),
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
  'LEFT_HTML'         => '',
  'RIGHT_HTML'        => $RIGHT_HTML,
  'update_message'    => $messageStack->output()
  ));
 $template->pparse('email_fead_back');
}
elseif($mail_id > 0)
{
 if($row_check1['sender_user']=='jobseeker')
 {
  tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set user_see='Yes' where id='".$mail_id."'");
 }
 $file_directory=get_file_directory($row_check1['attachment_file']);
 if($row_check1['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$row_check1['attachment_file']))
 $attachment_file_name=$row_check1['attachment_file'];
 $query_string5=encode_string("mail_attachment@#^#@".$attachment_file_name."@#^#@attachment");
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_db_output($row_check1['subject']).(($attachment_file_name!='')?'<span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,'query_string1='.$query_string5).'"  title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).' :'.tep_db_output(substr($attachment_file_name,14)).'</a></span>':''),
  'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>stripslashes($row_check1['message']),
 // 'INFO_TEXT_DESCRIPTION1'=>tep_draw_textarea_field('description', 'soft', '80%', '30', $description, 'id="description"', '', true),

  'INFO_TEXT_BACK'=>'<a href="#" onclick="javascript:history.back()">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;&nbsp;',
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
  'LEFT_HTML'         => '',
  'RIGHT_HTML'        => $RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email_message');
}
elseif($action=='preview')
{
 if($attachment_file_name!='')
 if(!is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment_file_name))
 $attachment_file_name='';
 $query_string5=encode_string("temp_attachment@#^#@".$attachment_file_name."@#^#@attachment");

  if ($_GET['query_type'] == 'contact' && $_GET['js']) {
    $sendForm = tep_draw_form('preview_mail', FILENAME_EMPLOYER_INTERACTION . '?query_type=contact&js=' . $_GET['js'], '', 'post', 'onsubmit="return ValidateForm(this)"') . tep_draw_hidden_field('application_id', $application_id) . tep_draw_hidden_field('action', 'send');
  } else {
    $sendForm = tep_draw_form('preview_mail', FILENAME_EMPLOYER_INTERACTION, '', 'post', 'onsubmit="return ValidateForm(this)"') . tep_draw_hidden_field('application_id', $application_id) . tep_draw_hidden_field('action', 'send');
  }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'INFO_TEXT_TO'=>INFO_TEXT_TO,
  'INFO_TEXT_TO1'=>tep_db_output($display_email_address),
  'INFO_TEXT_FROM'=>INFO_TEXT_FROM,
  //'INFO_TEXT_FROM1'=>tep_db_output($TREF_from),
  'INFO_TEXT_FROM1'  => tep_db_output(APPLICATION_REPLY_MAIL),
  'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_db_output($TR_subject).(($attachment_file_name!='')?'<span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string=".$query_string5).'"  title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).' :'.tep_db_output(substr($attachment_file_name,14)).'</a></span>':''),
  'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>stripslashes($_POST['message1']),
  'buttons'=>'<a href="#" onclick="javascript: set_action(\'back\');"><button class="btn btn-outline-secondary">Back</button></a>&nbsp;&nbsp;'.tep_draw_submit_button_field('send_mail','Send Mail','class="btn btn-primary"'),
  // 'form'=>tep_draw_form('preview_mail', FILENAME_EMPLOYER_INTERACTION, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action','send'),
  'form' => $sendForm,
  'hidden_fields'=>$hidden_fields,
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
  'LEFT_HTML'         => '',
  'RIGHT_HTML'        => $RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('preview');
}
else
{
 if(isset($_GET['view']) && $_GET['view']!='')
  $view=tep_db_prepare_input($_GET['view']);
 else
  $view='';


 if($view=='inbox')
 {
   $show='<a href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,'query_string='.$query_string).'">Compose</a> | <a href="'.tep_href_link(FILENAME_RECRUITER_ATS_MAILS,'query_string='.$query_string).'">All Jobseeker Mails</a>';
   $query= "select  *  from ".APPLICANT_INTERACTION_TABLE." where application_id ='".$application_id."'  and  sender_user ='jobseeker' order by inserted desc ";
 }
 else
 {
    if ($_GET['query_type'] == 'contact' && $_GET['js']) {
      $linkInbox = tep_href_link(FILENAME_EMPLOYER_INTERACTION, 'query_type=contact&js=' . $_GET['js']);
    } else {
      $linkInbox = tep_href_link(FILENAME_EMPLOYER_INTERACTION, 'view=inbox&query_string=' . $query_string);
    }
   $show='<a href="'.$linkInbox.'">Inbox</a> | <a href="'.tep_href_link(FILENAME_RECRUITER_ATS_MAILS,'query_string='.$query_string).'">All Jobseeker Mails</a>';
   $query= "select  *  from ".APPLICANT_INTERACTION_TABLE." where application_id ='".$application_id."'  and  sender_user ='recruiter' order by inserted ";
 }
 $query_result = tep_db_query($query);
 $num_row = tep_db_num_rows($query_result);
 if($num_row > 0)
 {
  $alternate=1;
  while ($applicant_interation = tep_db_fetch_array($query_result))
  {
   $file_directory=get_file_directory($applicant_interation['attachment_file']);
   if($applicant_interation['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$applicant_interation['attachment_file']))
   $attachment_file_name=$applicant_interation['attachment_file'];
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $query_string2=encode_string("mail=+=".$applicant_interation['id']."=+=send_id");
   $query_string5=encode_string("mail_attachment@#^#@".$attachment_file_name."@#^#@attachment");
   $template->assign_block_vars('applicant_interation',array( 'row_selected' => $row_selected,
    'subject'       => "<a href='".tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_string2=".$query_string2)."'>".tep_db_output($applicant_interation['subject'])."</a>".(($attachment_file_name!='')?' <span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string1=".$query_string5).'"  title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' : '.tep_db_output(substr($attachment_file_name,14))).'</a></span>':''),
    'fead_back'     => "<a href='#' onclick=\"set_action('".$applicant_interation['id']."')\"'>".tep_image_button('img/remark.gif',IMAGE_REMARK).'</a>'.tep_db_output($applicant_interation['remark_title']),
    'inserted'      => ($applicant_interation['inserted']=='0000-00-00 00:00:00')?'-':tep_db_output(formate_date1($applicant_interation['inserted'])),
    ));
    $alternate++;
  }
 }/*
 if($action=='')
 {
  //$row1=getAnyTableWhereData(APPLICATION_TABLE . " as a , ".JOBSEEKER_LOGIN_TABLE." as jl, ".JOB_TABLE. " as jb left join  ".RECRUITER_TABLE." as r on ( r.recruiter_id ='".$_SESSION['sess_recruiterid']."' ) "," a.id='".$application_id."'  and jb.recruiter_id='".$_SESSION['sess_recruiterid']."' and a.job_id=jb.job_id and a.jobseeker_id=jl.jobseeker_id  ","a.id, jl.jobseeker_email_address,jb.recruiter_user_id");
  if($row1['recruiter_user_id']!='' && ($row_email=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$row1['recruiter_user_id']."' and status='Yes'","email_address,name")))
  {
	 	$TREF_from=tep_db_output($row_email['email_address']);
  }
  else
  {
    $row_email=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id ='".$_SESSION['sess_recruiterid']."'",'recruiter_email_address');
   $TREF_from=tep_db_output($row_email['recruiter_email_address']);
  }
  //$email_address=$row1['jobseeker_email_address'];
 }*/
  if ($_GET['query_type'] == 'contact' && $_GET['js']) {
    $previeForm = tep_draw_form('email', FILENAME_EMPLOYER_INTERACTION . '?query_type=contact&js=' . $_GET['js'], '', 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"') . tep_draw_hidden_field('application_id', $application_id) . tep_draw_hidden_field('action', 'preview');
  } else {
    $previeForm = tep_draw_form('email', FILENAME_EMPLOYER_INTERACTION, '', 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"') . tep_draw_hidden_field('application_id', $application_id) . tep_draw_hidden_field('action', 'preview');
  }
 $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE,
  'TABLE_HEADING_INTERACTION_SUBJECT'=>TABLE_HEADING_INTERACTION_SUBJECT,
  'TABLE_HEADING_INTERACTION_INSERTED'=>(($view=='inbox')?TABLE_HEADING_INTERACTION_RECEIVED:TABLE_HEADING_INTERACTION_INSERTED),
  'TABLE_HEADING_INTERACTION_FEADBACK'=>TABLE_HEADING_INTERACTION_FEADBACK,
  'TABLE_HEADING_SHOW'=>$show,
  'INFO_TEXT_TO'     => INFO_TEXT_TO,
  'INFO_TEXT_TO1'    => tep_db_output($display_email_address),
  'INFO_TEXT_FROM'   => INFO_TEXT_FROM,
  'INFO_TEXT_FROM1'  => tep_db_output(APPLICATION_REPLY_MAIL),
  //'INFO_TEXT_FROM1'  => tep_draw_input_field('TREF_from',$TREF_from, 'size="35"', true ),
  'INFO_TEXT_SUBJECT'=> INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'class="form-control"', true ),
  'INFO_MAIL_ATTACHMENT'=>INFO_MAIL_ATTACHMENT,
  'INFO_MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=> tep_draw_textarea_field('message1', 'soft', '75%', '10', $email_text, '', true, true,'class="form-control"'),
  'buttons'           => tep_draw_submit_button_field('preview','Preview','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL).'</a>',
  // 'form'              => tep_draw_form('email', FILENAME_EMPLOYER_INTERACTION, '', 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"').tep_draw_hidden_field('application_id',$application_id).tep_draw_hidden_field('action','preview'),
  'form'              => $previeForm,
  'form1'             => tep_draw_form('fead_back',FILENAME_EMPLOYER_INTERACTION,'', 'post','').tep_draw_hidden_field('mail_id','').tep_draw_hidden_field('action','fead_back'),
  'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
  'LEFT_HTML'         => '',
  'RIGHT_HTML'        => $RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email');
}
?>