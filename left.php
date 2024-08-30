<?php
if(!((strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_INDEX)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_ABOUT_US)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_PRIVACY)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_TERMS)) ||  (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_SITE_MAP)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_CONTACT_US)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_INDUSTRY_RSS)) || (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_FAQ)))) // (strtolower($_SERVER['PHP_SELF'])==("/".PATH_TO_MAIN.FILENAME_ARTICLE)) ||
{
  ////// Recruiter starts///////
  if(check_login('recruiter'))
  {
    $today=date("Y-m-d H:i:s");
    
    if (isset($_SESSION['sess_recruiteruserid'])) {
      $isRecruiterUserId = " and recruiter_user_id = '".$_SESSION['sess_recruiteruserid']."'";
    } else {
      $isRecruiterUserId = "";
    }

    $no_of_save_resume=no_of_records(SAVE_RESUME_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."'",'id');
    $no_of_save_search=no_of_records(SEARCH_RESUME_RESULT_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."'",'id');
    $no_of_news_letters=no_of_records(NEWSLETTERS_HISTORY_TABLE," send_to ='recruiter'",'id');
    $no_of_active_job=no_of_records(JOB_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."' $isRecruiterUserId and re_adv <= '".$today."' and expired >= '".$today."' and deleted is NULL",'job_id');
    $no_of_expired_job=no_of_records(JOB_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."' $isRecruiterUserId and re_adv <= '".$today."' and expired <= '".$today."' and deleted is NULL",'job_id');
    $no_of_job=(int)no_of_records(JOB_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."'",'job_id');
    $no_of_applicant=(int)no_of_records(APPLICATION_TABLE." as a  left outer join ".JOB_TABLE." as jb on (a.job_id=jb.job_id)"," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' ",'a.id');
    $no_of_selectd_applicant=(int)no_of_records(APPLICATION_TABLE." as a  left outer join ".JOB_TABLE." as jb on (a.job_id=jb.job_id)","a.applicant_select='Yes' and  jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' ",'a.id');
    $no_of_contact=no_of_records(USER_CONTACT_TABLE,"user_id='".$_SESSION['sess_recruiterid']."' and user_type='recruiter'",'id');
    $no_of_user=no_of_records(RECRUITER_USERS_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' ",'id');
    /////////////////////
    $row_contact_rec=getAnyTableWhereData(RECRUITER_LOGIN_TABLE." as rl left join  ".RECRUITER_TABLE." as r on (rl.recruiter_id=r.recruiter_id) left join  ".COUNTRIES_TABLE." as c on (r.recruiter_country_id=c.id) left join ".ZONES_TABLE." as z on(r.recruiter_state_id=z.zone_id or z.zone_id is NULL)"," rl.recruiter_id ='".$_SESSION['sess_recruiterid']."'","r.recruiter_first_name,r.recruiter_last_name,r.recruiter_logo,r.recruiter_company_name,r.recruiter_address1,r.recruiter_address2,c.country_name,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as location,r.recruiter_state_id,r.recruiter_state,r.recruiter_city,r.recruiter_zip,r.recruiter_telephone,r.fax,r.recruiter_url,rl.recruiter_email_address");

      $logo = '';
      if (tep_not_null($row_contact_rec['recruiter_logo']) && is_file(PATH_TO_MAIN_PHYSICAL_LOGO . $row_contact_rec['recruiter_logo']))
	 {
      $logo = '<div>'.tep_image(FILENAME_IMAGE . '?image_name=' . PATH_TO_LOGO . $row_contact_rec['recruiter_logo'] . '&size=400', '', '', '').'</div>'
				.'<div style="text-center">
				<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION) . '" class="small d-block" style="color:#0a66c2!important;">
					'.EDIT_LOGO.'
				</a>
			</div>';

} else {
      $logo = '<div>
				'.defaultProfilePhotoUrl(tep_db_output($row_contact_rec['recruiter_company_name']),false,112,'class="no-pic" id=""').'
 			</div>
			<div style="text-center">
				<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION) . '" class="small d-block" style="color:#0a66c2!important;">
					'.ADD_LOGO.'
				</a>
			</div>';
      }
    $recruiter_name = tep_db_output($row_contact_rec['recruiter_company_name']);

    $post_a_job     = '<a id="post_job" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'" title="'.INFO_TEXT_POST_A_JOB.'">'.INFO_TEXT_POST_A_JOB.'</a>';
    $list_of_jos    = '<a id="list_of_jobs" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,'j_status=all').'" title="'.INFO_TEXT_L_LIST_OF_JOBS.'" >'.INFO_TEXT_L_LIST_OF_JOBS.'</a>';
    $active_jobs    = '<a id="list_of_jobs1" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,'page=1&j_status=active').'" title="'.INFO_TEXT_L_ACTIVE_JOBS.'" >'.INFO_TEXT_L_ACTIVE_JOBS.'</a> '.(($no_of_active_job>0)?'('.$no_of_active_job.')':'');
    $expired_jobs   = '<a id="list_of_jobs2" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,'j_status=expired').'" title="'.INFO_TEXT_L_EXPIRED_JOBS.'" >'.INFO_TEXT_L_EXPIRED_JOBS.'</a> '.(($no_of_expired_job>0)?'('.$no_of_expired_job.')':'');
    $import_multiple_jobs = '<a id="recruiter_import_jobs" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_IMPORT_JOBS).'" title="'.INFO_TEXT_L_IMPORT_MULTIPLE_JOBS.'" >'.INFO_TEXT_L_IMPORT_MULTIPLE_JOBS.'</a> ';
    $reports = '<a href="' . tep_href_link(PATH_TO_REPORTS.FILENAME_REPORTS) . '" title="Reports" class="style39" >'.TEXT_REPORTS.'</a>';
    $search_resumes = '<a id="search_resume" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'" title="'.INFO_TEXT_L_SEARCH_RESUMES.'" >'.INFO_TEXT_L_SEARCH_RESUMES.'</a>';
    $search_applicant = '<a id="search_applicant" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT).'" title="'.INFO_TEXT_L_SEARCH_APPLICANT.'" >'.INFO_TEXT_L_SEARCH_APPLICANT.'</a>';
    $resume_search_agents = '<a id="my_resume_search_agents" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_RESUME_SEARCH_AGENTS).'" title="'.INFO_TEXT_L_RESUME_SEARCH_AGENTS.'" >'.INFO_TEXT_L_RESUME_SEARCH_AGENTS.'</a> '.(($no_of_save_search>0)?'('.$no_of_save_search.')':'');
    $my_saved_resumes     = '<a id="list_of_resumes" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_SAVE_RESUME).'" title="'.INFO_TEXT_MY_SAVED_RESUMES.'" >'.INFO_TEXT_MY_SAVED_RESUMES.'</a> '.(($no_of_save_resume>0)?'('.$no_of_save_resume .')':'');
    $edit_profile  = '<a id="recruiter_registration" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_REGISTRATION).'" title="'.INFO_TEXT_L_EDIT_PROFILE.'" >'.INFO_TEXT_L_EDIT_PROFILE.'</a>';
    $company_description = '<a id="company_description" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_COMPANY_DESCRIPTION).'" title="'.INFO_TEXT_L_COMPANY_DESCRIPTION.'" >'.INFO_TEXT_L_COMPANY_DESCRIPTION.'</a>';
    $order_history   = '<a id="order_history" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO).'" title="'.INFO_TEXT_L_ORDER_HISTORY.'" >'.INFO_TEXT_L_ORDER_HISTORY.'</a>';
    $manage_users    = '<a id="list_of_users" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS).'" title="'.INFO_TEXT_MANAGE_USERS.'" >'.INFO_TEXT_MANAGE_USERS.'</a> '.(($no_of_user>0)?'('.$no_of_user.')':'');
    $contact_list    = '<a class="style39" href="'.tep_href_link(FILENAME_RECRUITER_CONTACT_LIST).'" title="'.INFO_TEXT_L_CONTACT_LIST.'" >'.INFO_TEXT_L_CONTACT_LIST.'</a>'.(($no_of_contact>0)?'('.$no_of_contact.')':'');
    $news_letter     = '<a id="list_of_newsletters" class="style39" href="'.tep_href_link(FILENAME_LIST_OF_NEWSLETTERS).'" title="'.INFO_TEXT_L_NEWS_LETTER.'" >'.INFO_TEXT_L_NEWS_LETTER.'</a> '.(($no_of_news_letters>0)?'('.$no_of_news_letters.')':'');
    $change_password = '<a id="recruiter_change_password" class="style39" href="'.tep_href_link(FILENAME_RECRUITER_CHANGE_PASSWORD).'" title="'.INFO_TEXT_L_CHANGE_PASSWORD.'" >'.INFO_TEXT_L_CHANGE_PASSWORD.'</a>';
    $log_out         = '<a class="style39" href="'.tep_href_link(FILENAME_LOGOUT).'" title="'.INFO_TEXT_L_LOG_OUT.'" >'.INFO_TEXT_L_LOG_OUT.'</a>';
    $rate_card       = '<a class="style39" href="'.tep_href_link(FILENAME_RECRUITER_RATES).'" title="'.INFO_TEXT_RATE_CARD.'" >'.INFO_TEXT_RATE_CARD.'</a>';
    $total_jobs_posted = '<div class="style39">'.INFO_TEXT_TOTAL_JOBS_POSTED." : ".$no_of_job.'</div>';
    $total_applicants  = '<div class="style39">'.INFO_TEXT_TOTAL_APPLICANTS." : ".$no_of_applicant.'</div>';
    $selected_applicant = '<div class="style39">'.INFO_TEXT_L_SELECTED_APPLICANT." : ".$no_of_selectd_applicant.'</div>';

	$applicant_tracking='<a id="applicant_tracking" href="' . tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING) . '" class="text-mutedd">' . ''.APPLICANT_TRACKING.'' . '</a> (' . $no_of_applicant . ')</form>' . (($no_of_applicant > 0) ? '' : '');
	$interview ='<a id="applicant_tracking" href="' . tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING) . '" class="text-mutedd">' . ''.INTERVIEW.'' . '</a>';
	$online_test='<a id="assessment" href="' . tep_href_link(FILENAME_QUIZ.'/'.FILENAME_RECRUITER_LIST_OF_QUIZ) . '"class="text-mutedd">'.ONLINE_TEST.'</a>';

	$forum = '<a id="forum" href="' . tep_href_link(PATH_TO_FORUM) . '" class="style39">'.JOB_FORUM.'</a>';
	$articles= '<a id="article" href="' . tep_href_link(FILENAME_ARTICLE) . '" class="style39">'.TEXT_ARTICLES.'</a>';
	$contact_list='<a id="contact_list" href="' . tep_href_link(FILENAME_RECRUITER_CONTACT_LIST) . '" title="Contact List" class="style39">'.CONTACT_LIST.'</a>'.(($no_of_contact>0)?'('.$no_of_contact.')':'');
	$newsletters='<a id="list_of_newsletters" href="' . tep_href_link(FILENAME_LIST_OF_NEWSLETTERS).'" title="Newsletters" class="style39">'.TEXT_NEWSLETTERS.'</a>'.(($no_of_news_letters>0)?'('.$no_of_news_letters.')':'');
	$admin_response='<a id="recruiter_mails" href="' . tep_href_link(FILENAME_RECRUITER_MAILS) . '" title="Admin Response" class="style39" >'.RESPONSE_FROM_ADMIN.' ('.count_admin_responses_for_recruiter($_SESSION['sess_recruiterid']).')</a> ';
	// $admin_response='<a id="recruiter_mails" href="' . tep_href_link(FILENAME_RECRUITER_MAILS) . '" title="Admin Response" class="style39" >Response from admin </a> ';
	// $jobseeker_response='<a id="recruiter_ats_mails" href="' . tep_href_link(FILENAME_RECRUITER_ATS_MAILS) . '" title="Jobseeker Response" class="style39" >Jobseeker response </a>';
	$jobseeker_response='<a id="recruiter_ats_mails" href="' . tep_href_link(FILENAME_RECRUITER_ATS_MAILS) . '" title="Jobseeker Response" class="style39" >'.JOBSEEKER_RESPONSE.' ('.count_recruiter_jobseeker_reply($_SESSION['sess_recruiterid']).')</a>';
	$list_jobfairs='<a id="list_of_jobfairs" href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS) . '" title="Jobfairs" class="style39">'.JOB_FAIRS.'</a>'.(($no_of_jobfairs>0)?'('.$no_of_jobfairs.')':'');
   $lms='<a class="text-mutedd" id="courses" href="'.tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME).'">'.TEXT_LMS.'</a>';

	$direct=getAnyTableWhereData(RECRUITER_TABLE . " as r ", "r.recruiter_id='" . $_SESSION['sess_recruiterid'] . "'", 'recruiter_applywithoutlogin');

	$unregistered_resumes=($direct['recruiter_applywithoutlogin'] == 'Yes' ? '<tr> <td class="style39_2"><a id="list_of_unreg_resumes" href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES) . '" title="' . INFO_TEXT_UNREGISTERED_RESUMES . '" class="text-mutedd">' . INFO_TEXT_UNREGISTERED_RESUMES . '</a></td></tr>' : '');
	$direct_login=tep_draw_form('recstatus', FILENAME_RECRUITER_CONTROL_PANEL, '', 'post', '') . ($direct['recruiter_applywithoutlogin'] == 'Yes' ? tep_draw_hidden_field('action', 'direct_login_inactive') : tep_draw_hidden_field('action', 'direct_login_active')) . '
'.APPLY_WITHOUT_LOGIN.' <label for="checkbox_rec_dr_log" class="switch">' . tep_draw_checkbox_field('direct_login', 'Yes', '', $direct['recruiter_applywithoutlogin'], ' class="inputdemo" id="checkbox_rec_dr_log" onchange="this.form.submit();"') . '<span class="slider round"></span></label></form>';
  }
  ////// Recruiter ends///////


  if(check_login('recruiter'))
  define('LEFT_HTML','
<div class="col-md-3 m-none col-3-custom">
	<div class="accordion accordion-flush dashboard-left-nav" id="accordionFlushExample">
        <div class="accordion-item pb-3 pt-2" style="border-top-left-radius: 0.8rem;border-top-right-radius: 0.8rem;">
			<div class="1flex-shrink-0 text-center mmb-0 no-pic mx-auto mb-3">
                '.$logo.'
            </div>
            <div class="1flex-grow-1 text-center">
                <div class="m-0 fw-bold text-capitalize">'.WELCOME.', '.$recruiter_name.'</div>
            </div>
		</div>
	
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item" style="border-top-left-radius: 0rem;border-top-right-radius: 0rem;">
        <a href="'.tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL).'" class="accordion-button2 collapsed fw-bold drop-padd card-dashboard2">
            <i class="bi bi-speedometer2 me-2"></i> '.BOX_HEADING_DASHBOARD.'
        </a> 
    </div>
		<div class="accordion-item">
          <h2 class="accordion-header" id="flush-headingOne">
            <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                <i class="bi bi-briefcase-fill me-2" style="color:#808080;"></i> '.INFO_TEXT_L_JOB_POSTING.'
            </button> 
          </h2>
          <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
			  <div class="pb-1 postjob">'.$post_a_job.'</div>
			  <div class="pb-1">'.$list_of_jos.'</div>
			  <div class="pb-1">'.$active_jobs.'</div>
			  <div class="pb-1">'.$expired_jobs.'</div>
			  <div class="pb-1">'.$import_multiple_jobs.'</div>
			  <div class="pb-1">'.$reports.'</div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="flush-headingTwo">
            <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                <i class="bi bi-search me-2" style="color:#808080;"></i> '.INFO_TEXT_SEARCH_RESUME.'
            </button>
          </h2>
          <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
			  <div class="pb-1">'.$search_resumes.'</div>
			  <div class="pb-1">'.$search_applicant.'</div>
			  <div class="pb-1">'.$resume_search_agents.'</div>
			  <div class="pb-1">'.$my_saved_resumes.'</div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <!-- <h2 class="accordion-header" id="flush-headingThree">
            <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                <i class="bi bi-person-bounding-box me-2" style="color:#808080;"></i> Applicant Tracking
            </button>
          </h2> -->
          <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
					<div class="pb-1">'.$applicant_tracking.'</div>
		            <div class="pb-1">'.$unregistered_resumes.'</div>
					<!-- <div class="pb-1">'.$online_test.'</div> -->
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="flush-headingFour">
            <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                <i class="bi bi-shield-lock-fill me-2" style="color:#808080;"></i> '.INFO_TEXT_L_MY_ACCOUNT.'
            </button>
          </h2>
          <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
			  <div class="pb-1">'.$edit_profile.'</div>
			  <div class="pb-1">'.$company_description.'</div>
			  <div class="pb-1">'.$order_history.'</div>
			  <div class="pb-1">'.$manage_users.'</div>
			  <div class="pb-1">'.$direct_login.'</div>
			  <div class="pb-1">'.$change_password.'</div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="flush-headingFive">
            <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                <i class="bi bi-gear-fill me-2" style="color:#808080;"></i> '.TEXT_RESOURCES.'
            </button>
          </h2>
          <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
			<div class="pb-1">'.$forum.'</div>
          <div class="pb-1">'.$articles.'</div>
          <div class="pb-1">'.$news_letter.'</div>
          <div class="pb-1">'.$contact_list.'</div>
		<div class="pb-1">'.$list_jobfairs.'</div>
		<div class="pb-1">'.$lms.'</div>
          </div>
          </div>
        </div>
        <div class="accordion-item" style="border-bottom-left-radius: 0.8rem;border-bottom-right-radius: 0.8rem;">
          <h2 class="accordion-header" id="flush-headingSix">
            <button class="accordion-button collapsed fw-bold drop-padd" style="border-bottom-left-radius: 0.8rem;border-bottom-right-radius: 0.8rem;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                <i class="bi bi-envelope-fill me-2" style="color:#808080;"></i> '.CORRESPONDENCE.'
            </button>
          </h2>
          <div id="flush-collapseSix" class="accordion-collapse collapse" aria-labelledby="flush-headingSix" data-bs-parent="#accordionFlushExample" style="border-radius: 0;">
            <div class="accordion-body" style="border-bottom-left-radius: 0.8rem;border-bottom-right-radius: 0.8rem;">
				<div class="pb-1">'.$admin_response.'</div>
				<div class="pb-1">'.$jobseeker_response.'</div>
            </div>
          </div>
        </div>
      </div>
	  </div>
	  </div>
  ');
  else
  define('LEFT_HTML','');

  //////Jobseeker starts///////
  if(check_login('jobseeker'))
  {


    $no_of_applications=no_of_records(APPLY_TABLE.' as a, '.JOB_TABLE." as j","a.job_id=j.job_id and a.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_apply_status='active'",'a.id');
    $no_of_cover_letters=no_of_records(JOBSEEKER_LOGIN_TABLE . " as jl, ".COVER_LETTER_TABLE." as c","jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jl.jobseeker_id=c.jobseeker_id",'c.cover_letter_id');
    $no_of_saved_searches=no_of_records(SEARCH_JOB_RESULT_TABLE . " as sr ","sr.jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'sr.id');
    $no_of_saved_jobs=no_of_records(SAVE_JOB_TABLE . " as s, ".JOB_TABLE." as j, ".RECRUITER_TABLE." as r, ".RECRUITER_LOGIN_TABLE." as rl","s.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and s.job_id=j.job_id and j.recruiter_id=rl.recruiter_id and j.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'",'s.id');
    $no_of_resumes=no_of_records(JOBSEEKER_RESUME1_TABLE . " as j1","j1.jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'j1.jobseeker_id');
    $no_of_unread_mail=no_of_records(APPLICANT_INTERACTION_TABLE." as ai left join ".APPLICATION_TABLE."  as a on (a.id=ai.application_id) ","a.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and ai.receiver_mail_status='active'  and ai.user_see ='No' and  sender_user='recruiter'",'ai.id');
    $no_of_contact=no_of_records(USER_CONTACT_TABLE,"user_id='".$_SESSION['sess_jobseekerid']."' and user_type='jobseeker'",'id');
    $no_of_companies=no_of_records(RECRUITER_LOGIN_TABLE . " as r1","r1.recruiter_id");


    $table_names1=JOBSEEKER_RESUME1_TABLE." as jr1 ";
    $whereClause1.="jr1.jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by jr1.inserted desc";
    $field_names1="jr1.resume_id,jr1.resume_title,jr1.jobseeker_photo,jr1.inserted,jr1.updated,jr1.availability_date,jr1.search_status ";//;,sum(rs.viewed) as viewed";

    $resume_query_raw="select $field_names1 from $table_names1 where $whereClause1";
    $resume_query = tep_db_query($resume_query_raw);
    $resume_query_numrows=tep_db_num_rows($resume_query);
    $available_status='';
    if($resume_query_numrows > 0)
    {
      while ($resume = tep_db_fetch_array($resume_query))
      {
		 $jobseeker_status=(tep_not_null($resume['availability_date'])?'Yes':'No');
	 }
	}

$avail_st=tep_draw_form('jstatus', FILENAME_JOBSEEKER_CONTROL_PANEL, '', 'post', '').($jobseeker_status=='Yes'?tep_draw_hidden_field('action','available_inactive'):tep_draw_hidden_field('action','available_active')).'
  <i class="fa fa-angle-right icon-page-title" aria-hidden="true">
</i> '.INFO_TEXT_SET_STATUS_AS_AVAILALE_NOW.'<label for="checkbox_jsstatus" class="switch">'.tep_draw_checkbox_field('jobseeker_status','Yes','',$jobseeker_status,' class="inputdemo" id="checkbox_jsstatus" onchange="this.form.submit();"').'<span class="slider round"></span></label></form>';



  $action=((isset($_GET['action']) && ($_GET['action']=='available_active' || $_GET['action']=='available_inactive'))?$_GET['action']:'');
  {
    $action = $_GET['action'] ;
  }
  if(tep_not_null($action))
  {
    switch($action)
    {
      case 'available_active':
        case 'available_inactive':
          if($action=='available_active')
          {
            tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=now() where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
            $messageStack->add_session('MESSAGE_SUCCESS_UPDATED_AVAILABLE', 'success');
          }
          else
          {
            tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=NULL where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
            $messageStack->add_session('MESSAGE_SUCCESS_UPDATED_NOT_AVAILABLE', 'success');
          }
          tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
        break;
      }
    }
    $row_contact1=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE." as jl left join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left join  ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id) left join ".ZONES_TABLE." as z on(j.jobseeker_state_id=z.zone_id or z.zone_id is NULL)"," jl.jobseeker_id ='".$_SESSION['sess_jobseekerid']."'","j.jobseeker_first_name,j.jobseeker_last_name,j.jobseeker_address1,j.jobseeker_address2,c.country_name,if(j.jobseeker_state_id,z.zone_name,j.jobseeker_state) as location,j.jobseeker_city,j.jobseeker_zip,j.jobseeker_phone,j.jobseeker_mobile,jl.jobseeker_email_address");
	
	$interview='<a href="'.tep_href_link(FILENAME_JOBSEEKER_MAILS).'" class="style39">'.INTERVIEW.'</a>';

    $add_resume= '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'" title="'.INFO_TEXT_L_ADD_RESUMES.'">'.INFO_TEXT_L_ADD_RESUMES.'</a>';
    $my_resumes= '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'" title="'.INFO_TEXT_MY_RESUMES.'">'.INFO_TEXT_MY_RESUMES.'</a>'.(($no_of_resumes>0)?"(".$no_of_resumes.")":" ");
    $jobseeker_reports = '<a href="' . tep_href_link(PATH_TO_REPORTS.FILENAME_JOBSEEKER_REPORTS) . '" title="Reports" class="style39" >'.TEXT_REPORTS.'</a>';
    $set_status= $avail_st;//'<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'action=available_active').'" title="'.INFO_TEXT_SET_STATUS_AS_AVAILALE_NOW.'">'.INFO_TEXT_SET_STATUS_AS_AVAILALE_NOW.'</a> '.$available_status;
	$resume_statistics='<a href="'.tep_href_link(FILENAME_RESUME_STATISTICS).'" class="style39">'.INFO_TEXT_RESUME_STATISTICS.'</a>';
	$jb_order_history='<a href="'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO).'" class="style39">'.INFO_TEXT_JOBSEEKER_ORDER_HISTORY.'</a>';
	$forum='<a href="'.tep_href_link(PATH_TO_FORUM).'" class="style39">'.INFO_TEXT_JOIN_FORUM.'</a>';
	$articles='<a href="'.tep_href_link(FILENAME_ARTICLE).'" class="style39">'.TEXT_ARTICLES.'</a>';
	$jb_rate_card='<a href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'" class="style39">'.BOX_RATE_CARD_PLAN.'</a>';
    $jb_lms=((LMS_SETTING=='True')?'<div class="pb-1"><a  class="style39" href="'. tep_href_link(PATH_TO_LMS.LMS_MY_COURSES_FILENAME) . '">'.TEXT_MY_COURSES.'</a></div>':'');

    $jb_test=((TEST_SETTING=='True')?'<div class="pb-1"><a class="style39" href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT).'">'.MY_TESTS.'</a></div>':'');

    $my_saved_jobs = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS).'" title="'.INFO_TEXT_MY_SAVED_JOBS.'">'.INFO_TEXT_MY_SAVED_JOBS.'</a>'.(($no_of_saved_jobs>0)?'('.$no_of_saved_jobs.')':'');
    $my_applications= '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS).'" title="'.INFO_TEXT_MY_APPLICATIONS.'">'.INFO_TEXT_MY_APPLICATIONS.'</a>'.(($no_of_applications>0)?"(".$no_of_applications.")":"");
    $response_from_employer = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_MAILS).'" title="'.INFO_TEXT_RESPONSE_FROM_EMPLOYER.'">'.INFO_TEXT_RESPONSE_FROM_EMPLOYER.'</a>'.(($no_of_unread_mail>0)?"(".$no_of_unread_mail.")":"");

    $edit_personal_details  = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'" title="'.INFO_TEXT_L_EDIT_PERSONAL_DETAILS.'">'.INFO_TEXT_L_EDIT_PERSONAL_DETAILS.' </a>';
    $my_cover_letters= '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS).'" title="'.INFO_TEXT_MY_COVER_LETTERS.'">'.INFO_TEXT_MY_COVER_LETTERS.'</a>'.(($no_of_cover_letters>0)?"(".$no_of_cover_letters.")":"");
    $change_password = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_CHANGE_PASSWORD).'" title="'.INFO_TEXT_L_CHANGE_PASSWORD.'">'.INFO_TEXT_L_CHANGE_PASSWORD.'</a>';
    $newsletters = '<a class="style39" href="'.tep_href_link(FILENAME_LIST_OF_NEWSLETTERS).'" title="'.INFO_TEXT_NEWSLETTERS.'">'.INFO_TEXT_NEWSLETTERS.'</a>';
    $contact_list = '<a class="style39" href="'.tep_href_link(FILENAME_RECRUITER_CONTACT_LIST).'" title="'.INFO_TEXT_L_CONTACT_LIST.'">'.INFO_TEXT_L_CONTACT_LIST.'</a>'.(($no_of_contact>0)?"(".$no_of_contact.")":"");
    $video_resume = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'"  title="'.INFO_TEXT_L_VIDEO_RESUME.'">'.INFO_TEXT_L_VIDEO_RESUME.'</a>';

    $search_jobs = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH).'" title="'.INFO_TEXT_L_SEARCH_JOBS.'">'.INFO_TEXT_L_SEARCH_JOBS.'</a>';
    $jobs_by_location= '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'"  title="'.INFO_TEXT_JOBS_BY_LOCATION.'">'.INFO_TEXT_JOBS_BY_LOCATION.'</a>';
    $jobs_by_map= (GOOGLE_MAP=='true'?'<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_JOB_BY_MAP).'" title="'.INFO_TEXT_JOBS_BY_MAP.'">'.INFO_TEXT_JOBS_BY_MAP.'</a></div>':'');
    $jobs_by_category = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" title="'.INFO_TEXT_JOBS_BY_INDUSTRY.'">'.INFO_TEXT_JOBS_BY_INDUSTRY.'</a>';
    $jobs_by_skill = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_SKILL).'" title="'.INFO_TEXT_JOBS_BY_SKILL.'">'.INFO_TEXT_JOBS_BY_SKILL.'</a>';
    $jobs_by_companies = '<a class="style39" href="'.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" title="'.INFO_TEXT_JOBS_BY_COMPANIES.'">'.INFO_TEXT_JOBS_BY_COMPANIES.'</a>'.(($no_of_companies>0)?"(".$no_of_companies.")":'');
    $my_saved_searches = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'"  title="'.INFO_TEXT_MY_SAVED_SEARCHES.'">'.INFO_TEXT_MY_SAVED_SEARCHES.'</a>'.(($no_of_saved_searches>0)?"(".$no_of_saved_searches.")":'');
    $job_alert_agent = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'"  title="'.INFO_TEXT_L_JOB_ALERT_AGENT.'">'.INFO_TEXT_L_JOB_ALERT_AGENT.' </a>'.(($no_of_saved_searches>0)?"(".$no_of_saved_searches.")":'');

	$resume_name=$row_contact1['jobseeker_first_name'].' '.$row_contact1['jobseeker_last_name'];

////////////////////////////////////////jobseeker edit / add photo //////////////////////////////////////////////////
$row_contact=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE." as jl left join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left join  ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id) left join ".ZONES_TABLE." as z on(j.jobseeker_state_id=z.zone_id or z.zone_id is NULL)"," jl.jobseeker_id ='".$_SESSION['sess_jobseekerid']."'","j.jobseeker_first_name,j.jobseeker_last_name,j.jobseeker_address1,j.jobseeker_address2,c.".TEXT_LANGUAGE."country_name,if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,j.jobseeker_state) as location,j.jobseeker_city,j.jobseeker_zip,j.jobseeker_phone,j.jobseeker_mobile,jl.jobseeker_email_address");

$resume_photo_check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_photo!='' ","jobseeker_photo,resume_id");
$job_photo='';
	if (tep_not_null($resume_photo_check['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$resume_photo_check['jobseeker_photo']))
	{
	$query_string=encode_string("resume_id@@@".$resume_photo_check['resume_id']."@@@resume");
	$job_photo = tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_PHOTO.$resume_photo_check['jobseeker_photo'].'','','','" class="myresume-pic mb-3"').'<a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5,'query_string='.$query_string).'" class="small d-block m-edit-pic"><span class="text-blue font-13">'.INFO_TEXT_EDIT_PHOTO.'</span></a>';
  }
else
{
 if($resume_photo_check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by resume_id desc","resume_id"))
 {
  $query_string=encode_string("resume_id@@@".$resume_photo_check1['resume_id']."@@@resume");
  $job_photo='<div>
            <!-- <img src="image/no_pic.gif" class=""> -->
            '.defaultProfilePhotoUrl($row_contact['jobseeker_first_name'].' '.$row_contact['jobseeker_last_name'],false,112,'class="no-pic" id=""').'
        </div>
  <div class="dashboard-pic text-center"><a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5,'query_string='.$query_string).'" class="mt-2 small d-block" style="color:#0a66c2!important;">'.INFO_TEXT_ADD_PHOTO.'</a></div>';
 }
 else
 {
  $job_photo='<td  bgcolor="#ffffff" height="19" valign="center" width="12%" align="left">
            '.defaultProfilePhotoUrl($row_contact['jobseeker_first_name'].' '.$row_contact['jobseeker_last_name'],false,112,'class="no-pic" id=""').'
            <a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'" class="small text-muted d-block">'.INFO_TEXT_ADD_PHOTO.'</a>
            </td>';
 }
}
////////////////////////  jobseeker edit/add photo END //////////////////////////
  }
  //////Jobseeker ends///////
  if(check_login('jobseeker'))
  define('LEFT_HTML_JOBSEEKER','
<div class="col-md-3 m-none col-3-custom">
<div class="accordion accordion-flush dashboard-left-nav" id="accordionFlushExample">
        <div class="accordion-item pt-2 pb-3" style="border-top-left-radius: 0.8rem;border-top-right-radius: 0.8rem;">
			<div class="1flex-shrink-0 text-center mmb-0 no-pic mx-auto mb-3">
                '.$job_photo.'
            </div>
            <div class="1flex-grow-1 text-center">
               <div class="m-0 fw-bold text-capitalize">'.WELCOME.', '.$resume_name.'</div>
            </div>
		</div>
        <div class="accordion accordion-flush" id="accordionFlushExample">
            
			<div class="accordion-item" style="border-top-left-radius: 0rem;border-top-right-radius: 0rem;">
				<a href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL).'" class="accordion-button2 collapsed fw-bold drop-padd card-dashboard2">
					<i class="bi bi-speedometer2 me-2"></i> '.BOX_HEADING_DASHBOARD.'
				</a> 
			</div>
	
			<div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    <i class="bi bi-file-earmark-text-fill me-2" style="color:#808080;"></i> '.INFO_TEXT_L_RESUME_MANAGER.'
                </button>
              </h2>
              <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
				<div class="pb-1">'.$add_resume.'</div>
				<div class="pb-1">'.$my_resumes.'</div>
				<div class="pb-1">'.$resume_statistics.'</div>
				<div class="pb-1">'.$jobseeker_reports.'</div>
				<div class="pb-1">'.$my_cover_letters.'</div>
				<div class="pb-1">'.$set_status.'</div>
                    
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    <i class="bi bi-briefcase-fill me-2" style="color:#808080;"></i> '.INFO_TEXT_L_JOBS.'
                </button>
              </h2>
              <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
				<div class="pb-1">'.$my_saved_jobs.'</div>
				<div class="pb-1">'.$my_applications.'</div>
				<div class="pb-1">'.$my_saved_searches.'</div>
				<div class="pb-1">'.$job_alert_agent.'</div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                    <i class="bi bi-lock-fill me-2" style="color:#808080;"></i> '.INFO_TEXT_L_MY_ACCOUNT.'
                </button>
              </h2>
              <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
				<div class="pb-1">'.$edit_personal_details.'</div>
				<div class="pb-1">'.$change_password.'</div>
				<div class="pb-1">'.$jb_order_history.'</div>
                <div class="pb-1">'.$jb_rate_card.'</div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingFour">
                <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                    <i class="bi bi-search me-2" style="color:#808080;"></i> '.INFO_TEXT_MY_JOB_SEARCH.'
                </button>
              </h2>
              <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
				<div class="pb-1">'.$search_jobs.'</div>
				<div class="pb-1">'.$jobs_by_companies.'</div>
				<div class="pb-1">'.$jobs_by_location.'</div>
<!--				<div class="pb-1">'.$jobs_by_map.'</div> -->
				<div class="pb-1">'.$jobs_by_skill.'</div>
				<div class="pb-1">'.$jobs_by_category.'</div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingFive">
                <button class="accordion-button collapsed fw-bold drop-padd" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                    <i class="bi bi-gear-fill me-2" style="color:#808080;"></i> '.TEXT_RESOURCES.'
                </button>
              </h2>
              <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <div class="pb-1">'.$contact_list.'</div>
				<div class="pb-1">'.$newsletters.'</div>
                    <div class="pb-1">'.$forum.'</div>
                    <div class="pb-1">'.$articles.'</div>
                    <div class="pb-1">'.$jb_lms.'</div>
                    <div class="pb-1">'.$jb_test.'</div>
                </div>
              </div>
            </div>
            <div class="accordion-item" style="border-bottom-left-radius: 12px;border-bottom-right-radius: 12px;">
              <h2 class="accordion-header" id="flush-headingSix">
                <button class="accordion-button collapsed fw-bold drop-padd" style="border-bottom-left-radius: 0.8rem;border-bottom-right-radius: 0.8rem;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                    <i class="bi bi-envelope-fill me-2" style="color:#808080;"></i> '.CORRESPONDENCE.'
                </button>
              </h2>
              <div id="flush-collapseSix" class="accordion-collapse collapse" aria-labelledby="flush-headingSix" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
				<div class="pb-1">'.$response_from_employer.'</div>
                    <div class="pb-1">'.$interview.'</div>
                </div>
              </div>
            </div>
          </div>
</div>
</div>
  ');
  else
  define('LEFT_HTML_JOBSEEKER','');


  //////// Job Search start//////////
  $jobs_by_location       = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'" title="'.INFO_TEXT_L_BY_LOCATION.'">'.INFO_TEXT_L_BY_LOCATION.'</a>';
  $jobs_by_map       = (GOOGLE_MAP=='true'?'<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a class="style39" href="'.tep_href_link(FILENAME_JOB_BY_MAP).'"  title="'.INFO_TEXT_L_BY_MAP.'">'.INFO_TEXT_L_BY_MAP.'</a></div>':'');
  $jobs_by_skill       = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_SKILL).'" title="'.INFO_TEXT_L_BY_SKILL.'">'.INFO_TEXT_L_BY_SKILL.'</a>';
  $jobs_by_category       = '<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" title="'.INFO_TEXT_L_BY_CATEGORY.'">'.INFO_TEXT_L_BY_CATEGORY.'</a>';
  $jobs_by_companies      = '<a class="style39" href="'.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" title="'.INFO_TEXT_L_BY_COMPANY.'">'.INFO_TEXT_L_BY_COMPANY.'</a>'.(($no_of_companies>0)?" ( ".$no_of_companies." )":'');
  $my_saved_jobs1          = '<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'" title="'.INFO_TEXT_MY_SAVED_JOBS.'">'.INFO_TEXT_MY_SAVED_JOBS.'</a>'.(($no_of_saved_searches>0)?" ( ".$no_of_saved_searches." )":'');
  $week_form1             = tep_draw_form('week1_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','7');
  $lastoneweek1           = '<a class="style39" href="#" onclick="document.week1_form.submit()" title="'.INFO_TEXT_LAST_ONE_WEEK.'">'.INFO_TEXT_LAST_ONE_WEEK.'</a>';
  $week_form2             = tep_draw_form('week2_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','14');
  $lastoneweek2           = '<a class="style39" href="#" onclick="document.week2_form.submit()" title="'.INFO_TEXT_LAST_TWO_WEEKS.'">'.INFO_TEXT_LAST_TWO_WEEKS.'</a>';
  $week_form3             = tep_draw_form('week3_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','21');
  $lastoneweek3           = '<a class="style39" href="#" onclick="document.week3_form.submit()" title="'.INFO_TEXT_LAST_THREE_WEEKS.'">'.INFO_TEXT_LAST_THREE_WEEKS.'</a>';
  $week_form4             = tep_draw_form('week4_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','30');
  $lastoneweek4           = '<a class="style39" href="#" onclick="document.week4_form.submit()" title="'.INFO_TEXT_LAST_ONE_MONTH.'">'.INFO_TEXT_LAST_ONE_MONTH.'</a>';

  if(check_login('jobseeker'))
  {
    $job_serach_left1 ='
    
    <div class="card-body">
	    <div class="fw-bold mt-3">'.INFO_TEXT_APP_TRACK.'</div>
      <div class="jobseeker-left">
        <div>'.$my_applications.'</div>
        <div>'.$response_from_employer.'</div>
        <div>'.$my_saved_searches.'</div>
        <div>'.$job_alert_agent.'</div>
      </div>
	  </div>
    </div>
    ';
  }
  else
  $job_serach_left1 ='';

  if(strtolower($_SERVER['PHP_SELF']) == "/".PATH_TO_MAIN.FILENAME_JOB_SEARCH && $_POST['action'] =='search' || (strtolower($_SERVER['PHP_SELF']) == "/".PATH_TO_MAIN.FILENAME_JOB_SEARCH_BY_INDUSTRY  ) || (strtolower($_SERVER['PHP_SELF']) == "/".PATH_TO_MAIN.FILENAME_JOB_SEARCH_BY_SKILL ) || (strtolower($_SERVER['PHP_SELF']) == "/".PATH_TO_MAIN.FILENAME_JOB_SEARCH_BY_LOCATION ) )
{// print_r($_POST);print_r($_GET);
 $country      = tep_db_input($_POST['country']);
 $keyword      = tep_db_input($_POST['keyword']);
 $job_post_day = tep_db_input($_POST['job_post_day']);
 $job_type     = tep_db_input($_POST['job_type']);
 $hidden_fields2='';
 $hidden_fields2.=tep_draw_hidden_field('job_type',tep_db_input($_POST['job_type']),'id="sf_job_type"');
 $left_query='';

 if(tep_not_null($_POST['word1']))
 $hidden_fields2.=tep_draw_hidden_field('word1',tep_db_input($_POST['word1']),'id="sf_word1"');


  if(tep_not_null($keyword)  && (($_POST['keyword']!='keyword') && ($_POST['keyword']!='job search keywords')) ) //   keyword starts //////
  {
    $hidden_fields2.=tep_draw_hidden_field('keyword',tep_db_input($_POST['keyword']),'id="sf_keyword"');
	$l_search = array ("'[\s]+'");
    $l_replace = array (" ");
    $l_keyword = preg_replace($l_search, $l_replace, $keyword);
	$word1=tep_db_prepare_input($_POST['word1']);
    $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
    if($word1=='Yes')
	{
     $explode_string=explode(' ',$l_keyword);
	 $total_keys = count($explode_string);
	 for($i=0;$i<$total_keys;$i++)
	 {
	  if(strlen($explode_string[$i])< 3 or strtolower($explode_string[$i])=='and')
	  {
       unset($explode_string[$i]);
	  }
	 }
	 sort($explode_string);
 	 $total_keys = count($explode_string);
     for($i=0;$i<$total_keys;$i++)
	 {
	  if($i>0)
      $whereClause1_l.='or ( ';
	  else
      $whereClause1_l.=' ( ';
      $whereClause1_l.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1_l.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1_l.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1_l.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1_l.=" j.job_description like '%".tep_db_input($explode_string[$i])."%'   ";
       $whereClause1_l.=" ) ";
     }

	 if($total_keys<=0)
	  $whereClause1_l='';
     if(tep_not_null($whereClause1_l))
	 {
     $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
     $left_query.=" (";
     $left_query.=$whereClause1_l;
	 $left_query.=" )";
	 }

	}
	else
	{
     $left_query.=" (";
     $left_query.=" j.job_title like '%".tep_db_input($l_keyword)."%' ";
     $left_query.=" or j.job_state like '%".tep_db_input($l_keyword)."%' ";
     $left_query.=" or j.job_location like '%".tep_db_input($l_keyword)."%' ";
     $left_query.=" or j.job_short_description like '%".tep_db_input($l_keyword)."%'";
     $left_query.=" or j.job_description like '%".tep_db_input($l_keyword)."%'";
      $left_query.=" )";
	}
  }


 if(tep_not_null($_POST['country']))
 {
  $hidden_fields2.=tep_draw_hidden_field('country',tep_db_input($_POST['country']),'id="sf_country"');
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.=" j.job_country_id ='".tep_db_input($country)."'";
 }
 if(tep_not_null($_POST['location']))
 {
  $hidden_fields2.=tep_draw_hidden_field('location',tep_db_input($_POST['location']),'id="sf_location"');
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.=" j.job_location  ='".tep_db_input($_POST['location'])."'";
 }
 if(tep_not_null($_POST['experience']))
 {
  $experience_l=$_POST['experience'];
  $explode_string=explode("-",$experience_l);
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
  $hidden_fields2.=tep_draw_hidden_field('experience',tep_db_input($_POST['experience']),'id="sf_experience"');
 }
  if(tep_not_null($_POST['job_language']))
 {
  $hidden_fields2.=tep_draw_hidden_field('job_language',tep_db_input($_POST['job_language']),'id="sf_job_language"');
  $job_language=$_POST['job_language'];
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.=" ( j.job_language='".tep_db_input($job_language)."'  ";
  $left_query.=" or j.job_language like '".tep_db_input($job_language).",%'  ";
  $left_query.=" or j.job_language like '%,".tep_db_input($job_language)."'  ) ";
 }
 elseif(tep_not_null($_GET['language']))
 {
  $jl_language   = tep_db_prepare_input($_GET['language']);
  if($row_lan=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE," name='".tep_db_input($jl_language)."'",'languages_id'))
  {
   $hidden_fields2.=tep_draw_hidden_field('job_language',tep_db_input($row_lan['languages_id']),'id="sf_job_language"');
   $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
   $left_query.=" ( j.job_language='".tep_db_input($row_lan['languages_id'])."'  ";
   $left_query.=" or j.job_language like '".tep_db_input($row_lan['languages_id']).",%'  ";
   $left_query.=" or j.job_language like '%,".tep_db_input($row_lan['languages_id'])."'  ) ";
  }
 }
 if(tep_not_null($_GET['state']) && isset($_GET['action']) )
 {
  $jl_state   = tep_db_prepare_input($_GET['state']);
  $hidden_fields2.=tep_draw_hidden_field('state',tep_db_input($jl_state),'id="sf_job_state"');
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  if($row_lan=getAnyTableWhereData(ZONES_TABLE," zone_name='".tep_db_input($jl_state)."'",'zone_id'))
  {
   $left_query.=" ( j.job_state_id='".tep_db_input($row_lan['zone_id'])."' ) ";
  }
  else
   $left_query.=" ( j.job_state='".tep_db_input($jl_state)."' ) ";
 }
 if(tep_not_null($_POST['job_skill']))
 {
  $left_job_skill   = tep_db_prepare_input($_GET['job_skill']);
  $hidden_fields2.=tep_draw_hidden_field('job_skill',tep_db_input($left_job_skill),'id="sf_job_skill"');
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.="( j.job_skills = '".tep_db_input($left_job_skill)."'";
  $left_query.=" or j.job_skills like '".tep_db_input($left_job_skill).",%'";
  $left_query.=" or j.job_skills like '%,".tep_db_input($left_job_skill)."'";
  $left_query.=" or j.job_skills like '%,".tep_db_input($left_job_skill).",%'";
  $left_query.="  )";
 }
 elseif(tep_not_null($_GET['skill']))
 {
  $left_job_skill   = tep_db_prepare_input($_GET['skill']);
  $hidden_fields2.=tep_draw_hidden_field('job_skill',tep_db_input($left_job_skill),'id="sf_job_skill"');
  $left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
  $left_query.="( j.job_skills = '".tep_db_input($left_job_skill)."'";
  $left_query.=" or j.job_skills like '".tep_db_input($left_job_skill).",%'";
  $left_query.=" or j.job_skills like '%,".tep_db_input($left_job_skill)."'";
  $left_query.=" or j.job_skills like '%,".tep_db_input($left_job_skill).",%'";
  $left_query.="  )";
 }
 if(tep_not_null($_POST['job_category']))
 {
  $job_category_l=tep_db_prepare_input($_POST['job_category']);
  if(is_array($job_category_l) && $job_category_l[0]!='')
  {
     $job_category_2= implode(',',$job_category_l);
	 $now=date('Y-m-d H:i:s');
	 if($job_category_2 !='0')
	 {
       $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$job_category_2.")";
       $left_query=(tep_not_null($left_query)?$left_query.' and job_id in ( ':' job_id in ( ');
       $left_query.=$whereClause_job_category;
       $left_query.=" ) ";
	  }
     foreach($job_category_l as $val )
    {
     if($val!='')
    $hidden_fields2.=tep_draw_hidden_field('job_category[]',tep_db_input($val),'id="sf_job_category_'.$val.'"');
    }
  }
 }
 if(tep_not_null($_GET['search_category']))
 {
  $search_category=(int) tep_db_prepare_input($_GET['search_category']);
  if($search_category>0)
  {
	$now=date('Y-m-d H:i:s');
    $hidden_fields2.=tep_draw_hidden_field('job_category[]',tep_db_input($search_category),'id="sf_job_category_'.$search_category.'"');
	$whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category.")";
    $left_query=(tep_not_null($left_query)?$left_query.' and job_id in ( ':' job_id in ( ');
    $left_query.=$whereClause_job_category;
    $left_query.=" ) ";
  }

 }
 $post_day_array =array('1'=>'Past 24 Hours','7'=>'Past Week','14'=>'Past Quarter','30'=>'Past Month');
 $left_query_jposted='';
 if(tep_not_null($job_post_day))
 {
   $left_query_jposted =" ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , (date("d")- (int)$job_post_day), date("Y")))."' ) ";
 }
 $left_query_jt='';
 if(tep_not_null($job_type))
 {
   $left_query_jt =" ( j.job_type ='". tep_db_input($job_type)."' ) ";
 }
 $salary_array=getSalaryRangelist();
 $sal_query_array=array();
 foreach($salary_array as $key )
 {
  $sal_query_array[$key]  =getSalaryQuery($key);
 }

  if(tep_not_null($_POST['salary_range']))
 {
  if(is_array($_POST['salary_range']))
  {
   //$left_query=(tep_not_null($left_query)?$left_query.' and  ':' ');
   $salary_range=tep_db_prepare_input($_POST['salary_range']);
   $job_salary_l=implode(',',$salary_range);
   foreach($salary_range as  $key)
   {
    $s_query[]  =getSalaryQuery($key);
   }
    $s_query =trim( implode(' or ',$s_query));
	if(tep_not_null($s_query))
    $s_query = ' ( '.$s_query.' ) ';
   }
 }
 $left_query1 = $left_query2 = $left_query3=$left_query;

  if(tep_not_null($left_query_jposted))
 {
   $left_query2=(tep_not_null($left_query2)?$left_query2.' and  ':' ');
   $left_query2 .= $left_query_jposted;
   $left_query3=(tep_not_null($left_query3)?$left_query3.' and  ':' ');
   $left_query3 .= $left_query_jposted;
 }
 if(tep_not_null($left_query_jt))
 {
   $left_query1=(tep_not_null($left_query1)?$left_query1.' and  ':' ');
   $left_query1 .= $left_query_jt;
   $left_query3=(tep_not_null($left_query3)?$left_query3.' and  ':' ');
   $left_query3 .= $left_query_jt;
 }
 if(tep_not_null($s_query))
 {
   $left_query1=(tep_not_null($left_query1)?$left_query1.' and  ':' ');
   $left_query1 .= $s_query;
   $left_query2=(tep_not_null($left_query2)?$left_query2.' and  ':' ');
   $left_query2 .= $s_query;
 }
 if($left_query1=='')
 $left_query1=1;

 if($left_query2=='')
 $left_query2=1;

 if($left_query3=='')
 $left_query3=1;


 $now=date('Y-m-d H:i:s');
 $no_of_all_time=no_of_records(JOB_TABLE.' as j',$left_query1.(tep_not_null($left_query2)?' and  ':' ')."j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')");
 $no_of_all_types=no_of_records(JOB_TABLE.' as j',$left_query2.(tep_not_null($left_query2)?' and  ':' ')."j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')");

 $job_serach_left1 ='<div>
                        '.tep_draw_form('left_search_job', FILENAME_JOB_SEARCH,'','post')
                        .tep_draw_hidden_field('action','search').
							        '</div>'
                      .$hidden_fields2.'
                    <div class="dropdown me-2">
                    <button class="btn btn-job-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Any Time
                    </button>
                    <ul class="dropdown-menu py-3 px-3 btn-filter-shadow">';
foreach($post_day_array as $k => $val)
{
  $no_of_records=no_of_records(JOB_TABLE.' as j',$left_query1." and  ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , (date("d")- (int)$k), date("Y")))."' )");

    $job_serach_left1 .='
                          <div class="form-check posted-box">
                            '.tep_draw_radio_field('job_post_day', $k, '', $job_post_day, 'id="sf_job_post_day_'.$k.'" class="form-check-input custom-control-input-posted-box"').'
                            <label class="form-check-label" for="sf_job_post_day_'.$k.'">'.tep_db_output($val).'</label>
                            <div class="total-numbers text-muted mr-3">'.$no_of_records.'</div>
                          </div>
              ';
}

$job_serach_left1 .='<div class="form-check posted-box">
                        '.tep_draw_radio_field('job_post_day',' ', ($job_post_day==''?true:false), $job_post_day, 'id="sf_job_post_day_" class="form-check-input custom-control-input-posted-box"').'
                          <label class="form-check-label" for="sf_job_post_day_">Any Time</label>
                        <div class="total-numbers text-muted mr-3">'.$no_of_all_time.'</div>
                      </div>
                    </ul></div>';


$job_serach_left1 .= '<div class="dropdown me-2">
                        <button class="btn btn-job-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Job Type
                        </button>
                        <ul class="dropdown-menu py-3 px-3 btn-filter-shadow">
                      ';

		$query_l = "select * from ".JOB_TYPE_TABLE." order by  type_name asc";
	    $result_l=tep_db_query($query_l);
		$color_array=array('#0170c1','#33ff00','#01b0f1','#00af50','#ec8b5e','#ffc001','#cc0033','#ff6600');

		$c=0;
	    while($row_l = tep_db_fetch_array($result_l))
	    {
		  $no_of_records=no_of_records(JOB_TABLE.' as j',$left_query2." and j.job_type ='".$row_l['id']."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')");

		 $job_serach_left1 .='
              <a href="#" class="box_job_type_l" id="box_job_type_'.$row_l['id'].'" >
                <div class="orm-check d-flex mb-1">
                        <div class="box_job_type"  style="background-color:'.$color_array[$c].';">&nbsp; </div>
                        <span class="job-type text-dark">'.tep_db_output($row_l['type_name']).'</span>
                        <span class="ms-auto d-flex style39"> '.$no_of_records.'</span>
                      
                  </div>
              </a>';
						$c++;
		}

		$job_serach_left1 .='<a style="width: 100%;display: block;" href="#" class="box_job_type_l" id="box_job_type_" ><div class="box_job_type"  style="background-color:'.$color_array[$c].';">&nbsp; </div>
                            <div class="orm-check d-flex mb-1">
                                <span class="job-type text-dark">Any Type</span>
                                <span class="ms-auto d-flex style39">'.$no_of_all_types.' </span>
                            </div>
                          </a>';
		tep_db_free_result($result_l);
        
    
  $job_serach_left1 .='</ul></div>';
        //////////
		$job_serach_left1 .='
                       <div class="dropdown me-2">
                          <button class="btn btn-job-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Salary
                          </button>
                          <ul class="dropdown-menu py-3 px-3 btn-filter-shadow" style="width: 260px;">';

		 $salary_array=getSalaryRangelist();
         foreach($salary_array as $key=>$val)
	     {
          $s_query   =getSalaryQuery($key);
 	      if(tep_not_null($s_query))
          $s_query = ' ( '.$s_query.' ) ';

		  $no_of_records=no_of_records(JOB_TABLE.' as j',$left_query3." and ".$s_query. "and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')");

		  $job_serach_left1 .='
                <div class="form-check d-flex mb-1">
                    '.tep_draw_checkbox_field('salary_range[]', $key,is_array($salary_range)? (in_array($key,$salary_range)?true:false):'','',' class="form-check-input custom-control-input-salary_range me-2" id="sf_job_salary_'.$key.'"').'
                    <label class="form-check-label" for="sf_job_salary_'.$key.'"> '.($val).'</label>
                    <span class="ms-auto d-flex style39"> '.$no_of_records.'</span>
                </div>
           ';
          }


      $job_serach_left1 .='</ul></div>';
}

  define('JOB_SEARCH_LEFT','
  <div class=" text-dark d-flex align-items-center">
      <!--
        <small id="advSearch" class="form-text text-muted"><a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">'.INFO_TEXT_ADV_SEARCH.'</a></small>
      -->

      <!--
        <div class="fw-bold mb-2">'.INFO_TEXT_REFINE_SEARCH.'</div>
        '.tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').
        tep_draw_input_field('keyword','','placeholder="'.INFO_TEXT_EG_SEARCH.'" type="text" class="form-control mb-2"',false).
        LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control mb-2'",INFO_TEXT_ALL_LOCATION,"",DEFAULT_COUNTRY_ID).
        '<input type="submit" name="login2" value="'.INFO_TEXT_SEARCH_NOW.'" class="btn btn-primary btn-block" /></form>
        <div class=" mt-2"><a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">'.INFO_TEXT_ADV_SEARCH.'</a></div>
        <div class="fw-bold mt-4 mb-2">'.INFO_TEXT_SEARCH_JOBS.'</div>
        <div>'.$jobs_by_category.'</div>
        <div class="my-1">'.$jobs_by_companies.'</div>
        <div class="my-1">'.$jobs_by_location.'</div>
        <div class="my-1">'.$jobs_by_skill.'</div>
        '.$jobs_by_map.'
        </div> 
      -->
  
  
    '. $job_serach_left1.'

    <div class="1card-body 1card-boy-custom m-none">
    '.tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'
        <div class="input-group">
          '.tep_draw_input_field('keyword',$keyword,'placeholder="'.INFO_TEXT_EG_SEARCH.'" type="text" class="form-control form-control-result-page-search"',false).'
          <div class="input-group-append">
            <button class="btn btn-result-page-search" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
          </div>
        </div>
    </form>
</div>

      <!-- 
        <div class="left-sidebar">
        <div class="fw-bold mb-2 mt-3">'.INFO_TEXT_DATE_POSTED.'</div>

        '.$week_form1.'<div class="my-1">'.$lastoneweek1.'</div></form>
        '.$week_form2.'<div class="my-1">'.$lastoneweek2.'</div></form>
        '.$week_form3.'<div class="my-1">'.$lastoneweek3.'</div></form>
        '.$week_form4.'<div class="my-1">'.$lastoneweek4.'</div></form> 
        -->
    </div>
');

}

/////// Job Search ends//////////

?>