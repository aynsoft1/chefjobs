<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_VIEW_JOB);
$template->set_filenames(array('recruiter_view_job' => 'recruiter_view_job.htm'));
include_once(FILENAME_BODY);

if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
//////////////////////
if(isset($_GET['jobID']))
{
 $job_id=(int)tep_db_prepare_input($_GET['jobID']);
 $whereClause="job_id='".tep_db_input($job_id)."' and recruiter_id='".$_SESSION['sess_recruiterid']."'";
 if($row=getAnyTableWhereData(JOB_TABLE,$whereClause))
 {
		$job_title     = $row['job_title'];
		$job_reference = $row['job_reference'];
		$country1      = $row['job_country_id'];
		$state_value   = $row['job_state_id'];
		$location      = $row['job_location'];
		$salary        = $row['job_salary'];

		$post_job_category = get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',tep_db_output($job_id));
    $job_sub_category =get_name_from_table(JOB_SUB_CATEGORY_TABLE,'sub_category_name','id',tep_db_output($row['job_sub_category']));
		$description   = $row['job_description'];
		$job_summary   = $row['job_short_description'];
		$job_type      = $row['job_type'];
    $company_sizes = $row['company_sizes'];
    $career_level  = $row['career_level'];
		$email_address = getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id ='".tep_db_input($_SESSION['sess_recruiterid'])."'","recruiter_email_address");
		$experience    = calculate_experience($row['min_experience'],$row['max_experience']);
		$posted_on     = tep_db_output(formate_date($row['inserted'],'d-M-Y'));
		$expired_on    = tep_db_output(formate_date($row['expired'],'d-M-Y'));
		$added_jobfair = $row['add_jobfair'];
		$job_skills    = $row['job_skills'];
//		$apply_online  = ($row['post_url']=='Yes'?$row['url']:'No');
		$job_auto_renew= $row['job_auto_renew'];
	}
}

  $different_jobs='<a class="btn btn-outline-primary btn-small me-3 m-border" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=active" class="hm_color">'.INFO_TEXT_ACTIVE_JOBS.'</a>';

  $different_jobs.='<a class="btn btn-outline-primary btn-small me-3 m-border" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=expired" class="hm_color">'.INFO_TEXT_EXPIRED_JOBS.'</a>';

  $different_jobs.='<a class="btn btn-outline-primary btn-small me-3 m-border" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=deleted" class="hm_color">'.INFO_TEXT_DELETED_JOBS.'</a>';

  $different_jobs.='<a class="btn btn-outline-primary btn-small me-3 m-border m-none" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'page=1&'.tep_get_all_get_params(array('action','page','jobID','j_status','sort'))).'&j_status=other" class="hm_color">'.INFO_TEXT_OTHER_JOBS.'</a>';

////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

//////////////////////////////////////
$uploaded_file=tep_db_query("select uploaded_file from jobs where job_id=$job_id");
$uploaded_row=tep_db_fetch_array($uploaded_file);
if(!empty($uploaded_row['uploaded_file'])){
  $fileName = $row['uploaded_file'];
  $fileNameCmps = explode(".", $fileName);
  $fileExtension = $fileNameCmps[1];
  $uploadFileDir = 'post_job_doc/';
  $dest_path = $uploadFileDir . $fileName;
}
////////////////////////////////////////////////////////
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'update_message'=>$messageStack->output(),
  'preview_job_form'=>$preview_job_form,
  'different_jobs'=>$different_jobs,
  'buttons'=>$buttons,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_JOB_TITLE1'=>$job_title,
  'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
  'INFO_TEXT_JOB_REF'=>INFO_TEXT_JOB_REF,
  'INFO_TEXT_JOB_REF1'=>$job_reference,
  'INFO_TEXT_COUNTRY'=>INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'=>get_name_from_table(COUNTRIES_TABLE,'country_name', 'id', $country1),
  'INFO_TEXT_STATE'=>INFO_TEXT_STATE,
  'INFO_TEXT_STATE1'=>is_numeric($state_value)?get_name_from_table(ZONES_TABLE,'zone_name', 'zone_id',$state_value): $state_value,
  'INFO_TEXT_LOCATION'=>INFO_TEXT_LOCATION,
  'INFO_TEXT_LOCATION1'=>$location,
  'INFO_TEXT_SALARY'=>INFO_TEXT_SALARY,
  'INFO_TEXT_SALARY1'=>((tep_not_null($salary))?$sym_left.$salary.$sym_rt:'Negotiable'),
  'INFO_TEXT_INDUSTRY_SECTOR'=>INFO_TEXT_INDUSTRY_SECTOR,
  'INFO_TEXT_INDUSTRY_SECTOR1'=>get_name_from_table(JOB_CATEGORY_TABLE,'category_name','id',$post_job_category),
  'INFO_TEXT_PROFESSION_SECTOR'=>INFO_TEXT_PROFESSION_SECTOR,
  'INFO_TEXT_INDUSTRY_SUB_SECTOR'=>$job_sub_category,
  'INFO_TEXT_VACANCY_SUMMARY'=>INFO_TEXT_VACANCY_SUMMARY,
  'INFO_TEXT_VACANCY_SUMMARY1'=>nl2br($job_summary),
  'INFO_TEXT_DESCRIPTION'=>INFO_TEXT_DESCRIPTION,
  'INFO_TEXT_DESCRIPTION1'=>nl2br($description),
  'INFO_TEXT_APPLICATION_GOTO'=>INFO_TEXT_APPLICATION_GOTO,
  'INFO_TEXT_APPLICATION_GOTO1'=>($row['post_url']=='Yes'?$row['url']:$email_address['recruiter_email_address']),
  'INFO_TEXT_JOB_TYPE'=>INFO_TEXT_JOB_TYPE,
  'INFO_TEXT_JOB_TYPE1'=>(($job_type==0)?'All Job Type':get_name_from_table(JOB_TYPE_TABLE,'type_name','id',$job_type)),
  'INFO_COMPANY_SIZES'=>INFO_COMPANY_SIZES,
  'INFO_COMPANY_SIZES1'=>get_name_from_table(COMPANY_SIZE_TABLE,TEXT_LANGUAGE.'size_name','id',$company_sizes),
  'INFO_CAREER_LEVEL'=>INFO_CAREER_LEVEL,
  'INFO_CAREER_LEVEL1'=>get_name_from_table(CAREER_LEVEL,TEXT_LANGUAGE.'career_level_name','id',$career_level),
  'INFO_TEXT_EXPERIENCE'=>INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'=>$experience,
  'INFO_TEXT_VACANCY_ADDED_ON'=>INFO_TEXT_VACANCY_ADDED_ON,
  'INFO_TEXT_VACANCY_ADDED_ON1'=>$posted_on,
  'INFO_TEXT_VACANCY_EXPIRED'=>INFO_TEXT_VACANCY_EXPIRED,
  'INFO_TEXT_VACANCY_EXPIRED1'=>$expired_on,
  'INFO_TEXT_POSTED_JOBFAIR'=>INFO_TEXT_POSTED_JOBFAIR,
  'INFO_TEXT_POSTED_JOBFAIR1'=>$added_jobfair,
  'INFO_TEXT_JOB_SKILLS'=>INFO_TEXT_JOB_SKILLS,
  'INFO_TEXT_JOB_SKILLS1'=>$job_skills,
  'INFO_TEXT_APPLY_ONLINE'=>INFO_TEXT_APPLY_ONLINE,
  'INFO_TEXT_APPLY_ONLINE1'=>$apply_online,
  'INFO_TEXT_JOB_AUTO_RENEW1'=>($job_auto_renew>0?''.$job_auto_renew.' days':'No'),
'PREVIEW_UPLOADED_DOCS' => 
    (!empty($fileName) ? 
        (
            $fileExtension === 'pdf' ? '<iframe src="/' . $dest_path . '" frameborder="0"></iframe>' :
            ($fileExtension === 'html' ? '<iframe src="/' . $dest_path . '" frameborder="0"></iframe>' :
            ($fileExtension === 'zip' ? displayZipContents($dest_path) :
            '<p>Unsupported file type: ' . htmlspecialchars($fileExtension) . '</p>')
            )
        ) : 
        '<p><span class="label">Uploaded File:</span> None</p>'
            ),
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
  'LEFT_HTML'=>'',
  'RIGHT_HTML'=>''));
 $template->pparse('recruiter_view_job');
?>