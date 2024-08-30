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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_APPLY_NOW);
$template->set_filenames(array('apply_now' => 'apply_now.htm','email'=>'application_send_template.htm','de_email'=>'de_application_send_template.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'apply_now.js';
if(!check_login("jobseeker"))
{
 $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
}
$query_string=$_GET['query_string'];
if(!tep_not_null($query_string))
{
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}
$job_id=check_data($query_string,"=","job_id","job_id");
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j, ".RECRUITER_LOGIN_TABLE." as rl,".RECRUITER_TABLE." as r";
$where_clause=" j.recruiter_id=r.recruiter_id and rl.recruiter_id=r.recruiter_id and j.job_id='".$job_id."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//and j.job_source='jobsite'
$field_names="j.recruiter_user_id,j.job_reference,j.job_title,rl.recruiter_email_address,concat(r.recruiter_first_name,' ',r.recruiter_last_name) as r_full_name,j.display_id,j.post_url,j.url";
if(!$row=getAnyTableWhereData($table_names,$where_clause,$field_names)) 
{ ///Hack attempt
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}
if($row['post_url']=='Yes')
{
 $post_url=trim($row['url']);
 if(substr($post_url,0,4)!='http')
 $post_url='http://'.$post_url;
 tep_redirect($post_url);
}
$display_id=$row['display_id'];
if($applicant_id=getAnytableWhereData(APPLICATION_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$job_id."' order by inserted desc limit 0,1",'id'))
{
 $messageStack->add_session("	You Already Applied this job", 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}

$action = (isset($_POST['action']) ? $_POST['action'] : '');
// application
if(tep_not_null($action))
{
	switch($action)
	{
		case 'apply':
			$resume_name='';
			$destination='';
   //print_r($_POST);   exit;
   //////// file upload resume starts //////
			///*
			if(tep_not_null($_FILES['my_resume']['name']))
			{
				if($obj_resume = new upload('my_resume', PATH_TO_MAIN_PHYSICAL_APPLY_RESUME,'644',array('doc','pdf','txt')))
				{
					$resume_name=tep_db_input($obj_resume->filename);
					$destination=PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$resume_name;
				}
				else
				{
					tep_redirect(tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string));
				}
			}
			//*/
			//////// file upload ends //////
			if (isset($_POST['cover_letter'])) {
				$cover_letter_id = (int)tep_db_prepare_input($_POST['cover_letter']);
				if (!$row_check = getAnyTableWhereData(COVER_LETTER_TABLE, "cover_letter_id='" . tep_db_input($cover_letter_id) . "' and jobseeker_id='" . $_SESSION['sess_jobseekerid'] . "'")) {
					$messageStack->add_session(MESSAGE_COVER_LETTER_ERROR, 'error');
					tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
				}
				$cover_letter = stripslashes($row_check['cover_letter']);
			} else {
				$cover_letter = tep_db_prepare_input($_POST['TR_cover_letter']);
			}
			
			if (isset($_POST['my_resume_id']) && tep_not_null($_POST['my_resume_id'])) {
				$resume_id = tep_db_prepare_input($_POST['my_resume_id']);
			} else {
				$messageStack->add_session(ERROR_RESUME_NOT_EXIST, 'error');
				tep_redirect(tep_href_link(FILENAME_APPLY_NOW, 'query_string=' . $query_string));
				$resume_id = '0';
			}

			// screener ques and answer
			$screenerInputData = [];

			for ($i = 1; $i <= NO_OF_SCREENERS; $i++) {
				$screener_question = $_POST["question$i"];
				$screener_answer = $_POST["answer$i"];
				if (!empty($screener_question) && !empty($screener_answer)) {
					array_push($screenerInputData, [
						"question" => $screener_question, "answer" => $screener_answer
					]);
				}
			}

			$sql_data_array=array('inserted'=>'now()',
				'job_id'=>$job_id,
				'resume_id'=>$resume_id,
				'jobseeker_id'=>$_SESSION['sess_jobseekerid'],
				'cover_letter'=>tep_db_input($cover_letter)
			);
			
			$sql_data_array2=array('inserted'=>'now()',
				'job_id'=>$job_id,
				'resume_id'=>$resume_id,
				'jobseeker_id'=>$_SESSION['sess_jobseekerid'],
			);

			if(tep_not_null($resume_name))
			{
				$sql_data_array['resume_name']=$resume_name;
			}
			tep_db_perform(APPLY_TABLE, $sql_data_array);
  	tep_db_perform(APPLICATION_TABLE, $sql_data_array);
   if($applicant_id=getAnytableWhereData(APPLICATION_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$job_id."' order by inserted desc limit 0,1",'id,job_id'))
   {
    /*///////////////////////////////////////////////////////////////////
    $row_round_query=" select  id from ".SELECTION_ROUND_TABLE."  order by value ";
    $row_round_result= tep_db_query($row_round_query);
    $row_round_num_row = tep_db_num_rows($row_round_result);
    if($row_round_num_row>0)
    {
     while($row_round = tep_db_fetch_array($row_round_result)) 
     {
      $sql_data_array1=array('application_id'=>$applicant_id['id'],
                            'cur_status'=>1,
                            'process_round '=>$row_round['id'],
                            'inserted'=>'now()',
                           );
      tep_db_perform(APPLICANT_STATUS_TABLE, $sql_data_array1); 
     }
     tep_db_free_result($row_round_result);
    }
    */
    ////////////////////////////////////////////////////////////////////
    $sql_data_array2=array('application_id'=>$display_id.'-'.($applicant_id['id']+1000));
    $row_apply=getAnytableWhereData(APPLY_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id ='".$job_id."' order by inserted desc limit 0,1",'id,resume_id');
    $sql_data_array2['jobseeker_apply_id']=$row_apply['id'];

	if (count($screenerInputData) > 0) {
		foreach ($screenerInputData as $row) {
			$question = $row["question"];
			$answer = $row["answer"];
			
			$tableRowData = [
				'jobseeker_id' => $_SESSION['sess_jobseekerid'],
				'application_id' => $applicant_id['id'],
				'application_ques' => $question,
				'application_ans' => $answer,
			];

			tep_db_perform(APPLICATION_SCREENER_TABLE, $tableRowData);
		}
	}

    tep_db_perform(APPLICATION_TABLE, $sql_data_array2, 'update', "id = '" .$applicant_id['id']."'");
   }
   $row_application=getAnytableWhereData(APPLY_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by inserted desc limit 0,1",'id,resume_id');
   $application_id=$row_application['id'];
   $application_resume_id=$row_application['resume_id'];
			/////////////////////////////////////////////////////////
			if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$job_id."'",'applications'))
			{
				$sql_data_array=array('job_id'=>$job_id,
									'applications'=>($check_row['applications']+1)
																										);
				tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$job_id."'");
			}
			else
			{
				$sql_data_array=array('job_id'=>$job_id,
																										'applications'=>1
																										);
				tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
			}
			$curr_date =date('Y-m-d');
			 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".tep_db_input($job_id)."' and  date='".tep_db_input($curr_date)."'",'applications'))
			 {
			  $sql_data_array=array('job_id'=>$job_id,
									'applications'=>($check_row['applications']+1)
									);
			  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".$job_id."' and  date='".tep_db_input($curr_date)."'");
			 }
			 else
			 {
			  $sql_data_array=array('job_id'=>$job_id,
									'date'=>$curr_date,
									'applications'=>1,
									'viewed'=>1,
				                    'clicked'=>1
									);
			  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
			 }
			/////////////////////////////////////////////////////////
   $no_of_screener=0;
   $screener_string='';
   $before_screen_string='';
   if($row_screener=getAnyTableWhereData(SCREENER_TABLE,"job_id='".$job_id."'"))
   {
    $k=0;
    for($i=1;$i<=NO_OF_SCREENERS;$i++)
    {
     $ques=tep_db_output($row_screener['q'.$i]);
     if($ques!="")
     {
      $no_of_screener++;
     }
     else
     {
      break;
     }
     $ans='answer'.$i;
     if($_POST[$ans]!='' && $ques!='')
     {
      $screener_string.='Q. '.tep_db_output($ques).' = '.tep_db_output(tep_db_prepare_input($_POST[$ans]))."<br>";
      $k++;
     }
    }
    if($k >0)
     $before_screen_string='<hr>'.INFO_TEXT_CANDIDATE_RESPOND.'<font color="red"><b>'.$k.' / '.$no_of_screener.'</b></font> '.INFO_TEXT_QUALIFICATION_QUESTIONS."<br><br>";
   }
			/*/mail to recruiter
			$message = new email();
			if(tep_not_null($destination))
			{
				$handle = fopen($destination, "r");
				$contents = fread($handle, filesize($destination));
				fclose($handle);
				$message->add_attachment($contents, substr($resume_name,14));
			}
            */
   $row_jobseeker=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE." as jl, ".JOBSEEKER_TABLE." as j","jl.jobseeker_id=j.jobseeker_id and j.jobseeker_id='".$_SESSION['sess_jobseekerid']."'","jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',jobseeker_last_name) as full_name");
			
   if($row['recruiter_user_id']!='' && ($row_email=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$row['recruiter_user_id']."' and status='Yes'","email_address,name")))
   {    
    $to_name=tep_db_output($row_email['name']);
	$to_email_address=tep_db_output($row_email['email_address']);
   }
   else
   {
    $row_email=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id ='".$row['recruiter_id']."'",'recruiter_email_address');
    $to_name=tep_db_output($row['r_full_name']);
	$to_email_address=tep_db_output($row['recruiter_email_address']);
   }

			$candidate_name=tep_db_output($row_jobseeker['full_name']);
			$candidate_email_address=tep_db_output($row_jobseeker['jobseeker_email_address']);
			$from_email_name=tep_db_output(SITE_TITLE);
			$from_email_address=tep_db_output(EMAIL_FROM);
			// Build the text version
			$email_text= $cover_letter." <br>".$before_screen_string.$screener_string;
   $query_string=encode_string("application_id=".$application_resume_id."=application_id");
   $application_link=($application_resume_id>0?sprintf(INFO_TEXT_VIEW_CV_LINK,'<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string).'" target="_blank"><u><b>'.INFO_TEXT_VIEW_RESUME.'</b></u></a><br>&nbsp;'):'');
   $template->assign_vars(array(
    'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','','border="0"').'</a>',
    'recruiter_name'=>tep_db_output($to_name),
    'jobseeker_name'=>tep_db_output($candidate_name),
    'jobseeker_email_address'=>tep_db_output($candidate_email_address),
    'content'=>$email_text,
    'application_link'=>$application_link,
    'site_title'=>tep_db_output(SITE_TITLE),
    'admin_email'=>stripslashes(CONTACT_ADMIN),
    ));
    $email_text=stripslashes($template->pparse1(TEXT_LANGUAGE.'email'));
			$email_subject=INFO_TEXT_APPLICANT_TRACKING.tep_db_output($row['job_reference'])." - ".tep_db_output($row['job_title']);
	        /*/
			$text = strip_tags($email_text);

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
			$message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
			//$message->send("", "kamal@erecruitmentsoftware.com", '', $from_email_address, $email_subject);

   //echo($to_name."<br>". $to_email_address."<br>". $from_email_name."<br>". $from_email_address."<br>". $email_subject);		
			//echo $email_text;die();
			// Send message
			//print_r($message);die();
			
			/////////////////////////////////////////////////////////
			*/
			if($destination=='')
		    {
			  if($row_att=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."' and  jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_resume !='' ","jobseeker_resume"))
			  {
			   $resume_name          = $row_att["jobseeker_resume"];
			   $resume_directory_old = get_file_directory($resume_name,6);
			   $destination          = PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory_old.'/'.$resume_name;
			  }
			}
			tep_new_mail($to_name,$to_email_address, $email_subject, $email_text,$from_email_name,$from_email_address,$destination,substr($resume_name,14)) ;
  			$messageStack->add_session(MESSAGE_SUCCESS_APPLED, 'success');
			tep_redirect(tep_href_link(FILENAME_RELATED_JOBS,'job='.$job_id));
			//tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL));
		break;
	}
}
$screeners = '';
if ($row_screener = getAnyTableWhereData(SCREENER_TABLE, "job_id='" . $job_id . "'")) {
	$screeners = '
		<div class="col-md-12">' . INFO_TEXT_SCREENER . '</div>';
	for ($i = 1; $i <= NO_OF_SCREENERS; $i++) {
		if ($row_screener['q' . $i] == '') {
			if ($i == 1) {
				$screeners = '';
			}
			break;
		}
		$screeners .= '
			<label for="staticEmail" class="col-sm-3 mb-2 col-form-label text-right font-weight-bold">' . INFO_TEXT_QUESTION . $i . ' :</label>
			<div class="col-sm-9">
			' . tep_db_output($row_screener['q' . $i]) . '
			</div>

			<label for="staticEmail" class="col-sm-3 col-form-label text-right font-weight-bold">' . INFO_TEXT_ANSWER . $i . ' :</label>
			<div class="col-sm-9 font-weight-bold">
				<input type="hidden" class="form-control mb-2" name="question' . $i . '" size="60" value="'.tep_db_output($row_screener['q' . $i]).'">
				<input type="text" class="form-control mb-2" name="answer' . $i . '" size="60">
			</div>
				';
	}
}
$cover_letters='';
$db_cover_letter_query_raw = "select c.* from " . JOBSEEKER_LOGIN_TABLE . " as jl, ".COVER_LETTER_TABLE." as c  where jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jl.jobseeker_id=c.jobseeker_id order by c.inserted desc";
//echo $db_cover_letter_query_raw;
$db_cover_letter_query = tep_db_query($db_cover_letter_query_raw);
$db_cover_letter_num_row = tep_db_num_rows($db_cover_letter_query);
if($db_cover_letter_num_row > 0)
{
 $cover_letters_array=array();
 while ($cover_letter = tep_db_fetch_array($db_cover_letter_query)) 
 {
  $ide=$cover_letter['cover_letter_id'];
  $cover_letters_array[]='<tr><td>'.tep_draw_radio_field('cover_letter', $ide, '', $ide, 'id="radio_cover_letter'.$ide.'"').'&nbsp;</td><td class="small"><label class="me-3" for="radio_cover_letter'.$ide.'" onMouseOver="this.style.color=\'#0000ff\'" onMouseOut="this.style.color=\'#000080\'">'.'<a href="#" onclick="popUp(\''.FILENAME_JOBSEEKER_PREVIEW_COVER_LETTER.'?cID='.$ide.'\')">'.tep_db_output($cover_letter['cover_letter_name']).'</a></label></td></tr>';
 }
 if(tep_not_null($cover_letters_array))
 {
  $cover_letters=implode("\n",$cover_letters_array);
 }
}
else
{
 $cover_letters=tep_draw_textarea_field('TR_cover_letter', 'soft', '50', '5', INFO_TEXT_DEFALUT_COVER_LETTER, 'class="form-control required h-100"', false, false);
}

$row_jobseeker=getAnytableWhereData(JOBSEEKER_LOGIN_TABLE." as jl,".JOBSEEKER_TABLE." as j","jl.jobseeker_id=j.jobseeker_id and j.jobseeker_id='".$_SESSION['sess_jobseekerid']."'","concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as name,jl.jobseeker_email_address");

$db_resume_name_query_raw = "select resume_id,resume_title from " . JOBSEEKER_RESUME1_TABLE ." where jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by inserted desc";
//echo $db_resume_name_query_raw;
$db_resume_name_query = tep_db_query($db_resume_name_query_raw);
$db_resume_name_num_row = tep_db_num_rows($db_resume_name_query);
if($db_resume_name_num_row > 0)
{
 while ($resume_name = tep_db_fetch_array($db_resume_name_query)) 
 {
  $ide=$resume_name['resume_id'];
  $query_string1=encode_string("resume@@@".$ide."@@@resume");
  $resume_names_array[]='<tr><td>'.tep_draw_radio_field('my_resume_id', $ide, '', $ide, 'id="radio_resume_name'.$ide.'" class="form-check-input"').'&nbsp;<label class="form-check-label break-radio" for="radio_resume_name'.$ide.'" onMouseOver="this.style.color=\'#0000ff\'" onMouseOut="this.style.color=\'#000080\'"></td><td class="small"><a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME."?resume=".$query_string1).'" target="_resume">'.tep_db_output($resume_name['resume_title']).'</a></label></td><tr>';
 }
}
else
{
 $resume_names=INFO_TEXT_NOT_ADDED_YET."&nbsp;&nbsp;<a href='".tep_href_link(FILENAME_JOBSEEKER_RESUME1)."' class='red'>".INFO_TEXT_ADD_NEW_RESUME."</a>";
}
if(tep_not_null($resume_names_array))
{
 $resume_names=implode("\n",$resume_names_array);
}
if($row['recruiter_user_id']!=null and($row_email=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$row['recruiter_user_id']."' and status='Yes'","email_address") ))
{
 
 $job_email_address=tep_db_output($row_email['email_address']);
}
else
{
 $job_email_address=tep_db_output($row['recruiter_email_address']);
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'form'=>tep_draw_form('application', FILENAME_APPLY_NOW, 'query_string='.$query_string, 'post', 'enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','apply'),
 'INFO_TEXT_TO'=>INFO_TEXT_TO,
 'INFO_TEXT_TO1'=>$job_email_address,
 'INFO_TEXT_FROM'=>INFO_TEXT_FROM,
 'INFO_TEXT_FROM1'=>tep_db_output($row_jobseeker['name']),
 'INFO_TEXT_EMAIL'=>INFO_TEXT_EMAIL,
 'INFO_TEXT_EMAIL1'=>tep_db_output($row_jobseeker['jobseeker_email_address']),
 'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
 'INFO_TEXT_SUBJECT1'=>(tep_not_null($row['job_reference'])?tep_db_output($row['job_reference'].' - '.$row['job_title']):tep_db_output($row['job_title'])),
 'INFO_TEXT_COVER_LETTER'=>INFO_TEXT_COVER_LETTER,
 'INFO_TEXT_COVER_LETTER1'=>$cover_letters,
 'INFO_TEXT_RESUMES'=>INFO_TEXT_RESUMES,
 'INFO_TEXT_RESUMES1'=>$resume_names,
 //'INFO_TEXT_CV'=>INFO_TEXT_CV,
 //'INFO_TEXT_CV1'=>tep_draw_file_field('my_resume', false),
//  'button'=>tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM),
 'button'=>tep_button_submit('btn btn-primary', IMAGE_CONFIRM),
 'screeners'=>$screeners,
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
 'INFO_TEXT_APPLICANT_TRACKING'=>INFO_TEXT_APPLICANT_TRACKING,
 'INFO_TEXT_DEFALUT_COVER_LETTER1'=>sprintf("<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS, 'action=add_new')."' class='btn btn-outline-secondary'>".INFO_TEXT_ADD_NEW_COVER_LETTER."</a>"),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
	'LEFT_HTML_JOBSEEKER' => LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('apply_now');
