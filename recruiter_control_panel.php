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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_RECRUITER_CONTROL_PANEL);
$template->set_filenames(array('control_panel' => 'recruiter_control_panel.htm'));
include_once(FILENAME_BODY);
if (!check_login("recruiter")) {
	$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
	tep_redirect(getPermalink(FILENAME_RECRUITER_LOGIN));
}

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
$obj_account = new recruiter_accounts('', 'job_post');
//print_r($obj_account->allocated_amount);

//*******coding for apply without login***************//
$direct_login = (tep_not_null($_POST['direct_login']) ? tep_db_prepare_input($_POST['direct_login']) : '');
$action = (tep_not_null($_POST['action']) ? tep_db_prepare_input($_POST['action']) : '');
//*********//


$job = $obj_account->allocated_amount['job'];
if ($job > 0 && $obj_account->allocated_amount['featured_job'] == 'Yes')
	$job = $job . ' ( Featured )';
$enjoyed_job = $obj_account->enjoyed_amount['job'];

$obj_account1 = new recruiter_accounts('', 'resume_search');
$cv = $obj_account1->allocated_amount['cv'];
$enjoyed_cv = $obj_account1->enjoyed_amount['cv'];


$start_date = $obj_account->allocated_amount['from'];
$end_date = $obj_account->allocated_amount['to'];
$plan = $obj_account->allocated_amount['plan'];

$start_date1 = $obj_account1->allocated_amount['from'];
$end_date1 = $obj_account1->allocated_amount['to'];
$plan1 = $obj_account1->allocated_amount['plan'];

$from_to = (($start_date == '' || $end_date == '') ? INFO_TEXT_FROM_TO_JOB : INFO_TEXT_FROM . $start_date . '&nbsp;&nbsp;&nbsp;' . INFO_TEXT_TO . $end_date);
$from_to1 = (($start_date1 == '' || $end_date1 == '') ? INFO_TEXT_FROM_TO_RESUME : INFO_TEXT_FROM . $start_date1 . '&nbsp;&nbsp;&nbsp;' . INFO_TEXT_TO . $end_date1);

if (($start_date == '' || $end_date == '') && ($start_date1 == '' || $end_date1 == '')) {
	$from_to1 = INFO_TEXT_FROM_TO;
	$from_to = INFO_TEXT_FROM_TO;
}
$today = date("Y-m-d H:i:s");
$no_of_save_resume = no_of_records(SAVE_RESUME_TABLE, " recruiter_id ='" . $_SESSION['sess_recruiterid'] . "'", 'id');
$no_of_save_search = no_of_records(SEARCH_RESUME_RESULT_TABLE, " recruiter_id ='" . $_SESSION['sess_recruiterid'] . "'", 'id');
$no_of_news_letters = no_of_records(NEWSLETTERS_HISTORY_TABLE, " send_to ='recruiter'", 'id');
$no_of_active_job = no_of_records(JOB_TABLE, " recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' and re_adv <= '" . $today . "' and expired >= '" . $today . "' and deleted is NULL", 'job_id');
$no_of_jobfairs = no_of_records(JOBFAIR_TABLE, "jobfair_enddate >= '" . $today . "' and jobfair_status='Yes'", 'id');
$no_of_expired_job = no_of_records(JOB_TABLE, " recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' and re_adv <= '" . $today . "' and expired <= '" . $today . "' and deleted is NULL", 'job_id');
$no_of_job = (int)no_of_records(JOB_TABLE, " recruiter_id ='" . $_SESSION['sess_recruiterid'] . "'", 'job_id');
$no_of_applicant = (int)no_of_records(APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id)", " jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' ", 'a.id');
$no_of_direct_applicant = (int)no_of_records(APPLICANT_NOLOGIN_TABLE, "recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' ", 'applicantnl_id');
$total_no_of_applications = $no_of_direct_applicant + $no_of_applicant;
$no_of_selectd_applicant = (int)no_of_records(APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id)", "a.applicant_select='Yes' and  jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' ", 'a.id');
$no_of_contact = no_of_records(USER_CONTACT_TABLE, "user_id='" . $_SESSION['sess_recruiterid'] . "' and user_type='recruiter'", 'id');
$no_of_user = no_of_records(RECRUITER_USERS_TABLE, "recruiter_id='" . $_SESSION['sess_recruiterid'] . "' ", 'id');

/********************************CLACULATE TOTAL VIEWS ****************************************************************/
$no_of_views = getAnyTableWhereData(RECRUITER_TABLE . " as r left outer join " . JOB_TABLE . " as j on (r.recruiter_id=j.recruiter_id) left outer join " . JOB_STATISTICS_TABLE . " as s on (j.job_id=s.job_id)", "r.recruiter_id='" . $_SESSION['sess_recruiterid'] . "'", 'sum(s.viewed) as total');
echo $no_of_views['tot'];

$view_query = tep_db_query("select sum(s.viewed) as total from " . RECRUITER_TABLE . " as r left outer join " . JOB_TABLE . " as j on (r.recruiter_id=j.recruiter_id) left outer join " . JOB_STATISTICS_TABLE . " as s on (j.job_id=s.job_id) where r.recruiter_id='" . $_SESSION['sess_recruiterid'] . "'");
if (tep_db_num_rows($view_query) == 1) {
	$view = tep_db_fetch_array($view_query);
	$view_total = ($view['total'] <= 0 ? 0 : $view['total']);
}

/******************************** CALCULATE TOTAL VIEWS  END ****************************************************************/

$graph = '';
$pieChart = array();
$barChart = array();
if ($no_of_job > 0) {
	$chart_array = array($no_of_job, $no_of_active_job, $no_of_applicant, $no_of_selectd_applicant);
	$max_value = max($chart_array);
	$bar_jobs = (($no_of_job > 0) ? round(($no_of_job / $max_value) * 100, 1) : 0);
	$bar_a_jobs = (($no_of_active_job > 0) ? round(($no_of_active_job / $max_value) * 100, 1) : 0);
	$bar_application = (($no_of_active_job > 0) ? round(($no_of_applicant / $max_value) * 100, 1) : 0);
	$bar_selected = (($no_of_selectd_applicant > 0) ? round(($no_of_selectd_applicant / $max_value) * 100, 1) : 0);

	////	$bar_total  =200;
	$pieChart = array(
		array("label" => INFO_JOBS, "y" => $no_of_job),
		array("label" => INFO_JOB_ACTIVE, "y" => $no_of_active_job),
		array("label" => INFO_TOTAL_APP, "y" => $no_of_applicant),
		array("label" => INFO_SELECTED, "y" => $no_of_selectd_applicant),
	);
	$graph = '<div class="">
	<div class="card card-jobseeker mb-3 mb-none">
                <div class="card-body p-0 pb-3">
				<div class="card-header card-header-custom px-4 pt-3" style="border-top-left-radius:0.8rem;border-top-right-radius:0.8rem;">' . INFO_JOB_STATUS . '</div>
				<table border="0"  width="95%" align="center" bgcolor="#ffffff"  cellspacing="0" cellpadding="3">
				<tr height="130">
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_job . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar1.gif"  height="' . $bar_jobs . '"   alt="' . $no_of_job . '" title="' . $no_of_job . '"   width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_active_job . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar2.gif" height="' . $bar_a_jobs . '"     alt="' . $no_of_active_job . '"   title="' . $no_of_active_job . '"     width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_applicant . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar3.gif" height="' . $bar_application . '" alt="' . $no_of_applicant . '" title="' . $no_of_applicant . '" width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_selectd_applicant . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar4s.gif" height="' . $bar_selected . '"  alt="' . $no_of_selectd_applicant . '" title="' . $no_of_selectd_applicant . '"  width="45"></td></tr></table></td>
				</tr>
				<tr bgcolor="#ffffff" class="small">
				<td align="center"  class="">' . INFO_JOBS . '</td>
				<td align="center"  class="">' . INFO_JOB_ACTIVE . '</td>
				<td align="center"  class="">' . INFO_TOTAL_APP . '</td>
				<td align="center"  class="">' . INFO_SELECTED . '</td>
				</tr>
				</table>
				</div>
				</div>

 		</div>';
	if ($no_of_applicant > 0) {
		$no_of_joined = (int)no_of_records(APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id)", "a.applicant_select='Yes' and applicant_join_status ='joined'  and  jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' ", 'a.id');
		$no_of_declined = (int)no_of_records(APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id)", "a.applicant_select='Yes' and applicant_join_status ='declined'  and  jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' ", 'a.id');
		$no_of_screen_round = (int)no_of_records('( select a.id from ' . APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id) left outer join " . APPLICANT_STATUS_TABLE . " as  aps on (a.id =aps.application_id  and aps.process_round =1) where   aps.application_id is not null and   jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' group by aps.application_id)   as t ", '1', 'id');
		$no_of_interview_round = (int)no_of_records('( select a.id from ' . APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id) left outer join " . APPLICANT_STATUS_TABLE . " as  aps on (a.id =aps.application_id  and aps.process_round =2) where   aps.application_id is not null and   jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' group by aps.application_id)   as t ", '1', 'id');
		$no_of_telephone_round = (int)no_of_records('( select a.id from ' . APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id) left outer join " . APPLICANT_STATUS_TABLE . " as  aps on (a.id =aps.application_id  and aps.process_round =3) where   aps.application_id is not null and   jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' group by aps.application_id)   as t ", '1', 'id');
		$no_of_skill_round = (int)no_of_records('( select a.id from ' . APPLICATION_TABLE . " as a  left outer join " . JOB_TABLE . " as jb on (a.job_id=jb.job_id) left outer join " . APPLICANT_STATUS_TABLE . " as  aps on (a.id =aps.application_id  and aps.process_round =4) where   aps.application_id is not null and   jb.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "' group by aps.application_id)   as t ", '1', 'id');

		$chart_array = array($no_of_applicant, $no_of_selectd_applicant, $no_of_joined, $no_of_declined, $no_of_screen_round, $no_of_interview_round, $no_of_telephone_round);
		$max_value = max($chart_array);
		$bar_application = (($no_of_applicant > 0) ? round(($no_of_applicant / $max_value) * 100, 1) : 0);
		$bar_selected = (($no_of_selectd_applicant > 0) ? round(($no_of_selectd_applicant / $max_value) * 100, 1) : 0);
		$bar_joined = (($no_of_joined > 0) ? round(($no_of_joined / $max_value) * 100, 1) : 0);
		$bar_declined = (($no_of_declined > 0) ? round(($no_of_declined / $max_value) * 100, 1) : 0);
		$bar_screen_round = (($no_of_screen_round > 0) ? round(($no_of_screen_round / $max_value) * 100, 1) : 0);
		$bar_interview_round = (($no_of_interview_round > 0) ? round(($no_of_interview_round / $max_value) * 100, 1) : 0);
		$bar_telephone_round = (($no_of_telephone_round > 0) ? round(($no_of_telephone_round / $max_value) * 100, 1) : 0);
		$bar_skill_round = (($no_of_skill_round > 0) ? round(($no_of_skill_round / $max_value) * 100, 1) : 0);

		$barChart = array(
			array("label" => INFO_TOTAL, "y" => $no_of_applicant),
			array("label" => INFO_SCREENING, "y" => $no_of_screen_round),
			array("label" => INFO_INTERVIEW, "y" => $no_of_interview_round),
			array("label" => INFO_TELEPHONE, "y" => $no_of_telephone_round),
			array("label" => INFO_SKILL_CHECK, "y" => $no_of_skill_round),
			array("label" => INFO_SELECTED, "y" => $no_of_selectd_applicant),
			array("label" => INFO_JOINED, "y" => $no_of_joined),
			array("label" => INFO_DECLINED, "y" => $no_of_declined),
		);

		$graph .= '
   <div class="m-none">
   <div class="card card-jobseeker mb-3">
                <div class="card-body p-0 pb-3">
				<div class="card-header card-header-custom px-4 pt-3" style="border-top-left-radius:0.8rem;border-top-right-radius:0.8rem;">'.APPLICANT_PIPELINE.'</div>
		<table class="table-responsive" border="0"  width="95%" align="center" bgcolor="#ffffff"  cellspacing="0" cellpadding="3">
			<tr height="130">
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_applicant . '</td></tr><tr><td align="center" valign="bottom"> <img style="border:0px solid #00aadc" src="img/bar1.gif"  height="' . $bar_application . '"   alt="' . $no_of_applicant . '" title="' . $no_of_applicant . '"   width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_screen_round . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar2.gif" height="' . $bar_screen_round . '"     alt="' . $no_of_screen_round . '"   title="' . $no_of_screen_round . '"     width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_interview_round . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar3.gif" height="' . $bar_interview_round . '" alt="' . $no_of_interview_round . '" title="' . $no_of_interview_round . '" width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_telephone_round . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar4.gif" height="' . $bar_telephone_round . '"  alt="' . $no_of_telephone_round . '" title="' . $no_of_telephone_round . '"  width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_skill_round . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar5.gif"  height="' . $bar_skill_round . '"   alt="' . $no_of_skill_round . '" title="' . $no_of_skill_round . '"   width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_selectd_applicant . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar6.gif" height="' . $bar_selected . '"     alt="' . $no_of_applicant . '"   title="' . $no_of_applicant . '"     width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_joined . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar7.gif" height="' . $bar_joined . '" alt="' . $no_of_joined . '" title="' . $no_of_joined . '" width="45"></td></tr></table></td>
				<td align="center" valign="bottom"><table><tr><td align="center" class="">' . $no_of_declined . '</td></tr><tr><td align="center" valign="bottom"> <img style="border: 0px solid #00aadc" src="img/bar8.gif" height="' . $bar_declined . '"  alt="' . $no_of_declined . '" title="' . $no_of_declined . '"  width="45"></td></tr></table></td>
			</tr>
			<tr bgcolor="#ffffff" class="small">
				<td align="center"  class="">' . INFO_TOTAL . '</td>
				<td align="center"  class="small2">' . INFO_SCREENING . '</td>
				<td align="center"  class="small2">' . INFO_INTERVIEW . '</td>
				<td align="center"  class="small2">' . INFO_TELEPHONE . '</td>
				<td align="center"  class="small2">' . INFO_SKILL_CHECK . '</td>
				<td align="center"  class="small2">' . INFO_SELECTED . '</td>
				<td align="center"  class="small2" style="color: #70ad46;">' . INFO_JOINED . '</td>
				<td align="center"  class="small2" style="color: #eb1c24;">' . INFO_DECLINED . '</td>
			</tr>
		</table>
		</div></div></div>';
	}
	$graph .= '';
}
//////////////*******coding for apply without login ********////////////////
/*$action=((isset($_GET['action']) && ($_GET['action']=='direct_login_active' || $_GET['action']=='direct_login_inactive'))?$_GET['action']:'');
{
 $action = $_GET['action'] ;
}
*/



if (tep_not_null($action)) {
	switch ($action) {
		case 'direct_login_active':
		case 'direct_login_inactive':
			if ($action == 'direct_login_active') {
				tep_db_query("update " . RECRUITER_TABLE . " set recruiter_applywithoutlogin='Yes' where recruiter_id='" . $_SESSION['sess_recruiterid'] . "'");
				$messageStack->add_session(MESSAGE_SUCCESS_UPDATED_DIRECTLOGIN, 'success');
			} else {
				tep_db_query("update " . RECRUITER_TABLE . " set recruiter_applywithoutlogin='No' where recruiter_id='" . $_SESSION['sess_recruiterid'] . "'");
				$messageStack->add_session(MESSAGE_SUCCESS_UPDATED_NOT_DIRECTLOGIN, 'success');
			}
			tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
			break;
	}
}
///////////////////////***********************************//////////////////

/////////////////////
$row_contact = getAnyTableWhereData(RECRUITER_LOGIN_TABLE . " as rl left join  " . RECRUITER_TABLE . " as r on (rl.recruiter_id=r.recruiter_id) left join  " . COUNTRIES_TABLE . " as c on (r.recruiter_country_id=c.id) left join " . ZONES_TABLE . " as z on(r.recruiter_state_id=z.zone_id or z.zone_id is NULL)", " rl.recruiter_id ='" . $_SESSION['sess_recruiterid'] . "'", "r.recruiter_first_name,r.recruiter_last_name, r.recruiter_logo, r.recruiter_company_name, r.recruiter_applywithoutlogin, r.recruiter_address1, r.recruiter_address2, c." . TEXT_LANGUAGE . "country_name,if(r.recruiter_state_id, z." . TEXT_LANGUAGE . "zone_name,r.recruiter_state) as location, r.recruiter_state_id, r.recruiter_state, r.recruiter_city, r.recruiter_zip, r.recruiter_telephone,r.fax,r.recruiter_url,rl.recruiter_email_address");

$direct_login = $row_contact['recruiter_applywithoutlogin'];
/*
if($direct_login=='Yes')
  {
   $direct_login_status='<a href="' . tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL, 'action=direct_login_inactive') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_NOT_AVAILABLE, 30, 17) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 17);
  }
  else
  {
   $direct_login_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLITY, 30, 17) . '&nbsp;<a href="' . tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL, 'action=direct_login_active') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_AVAILABLITY, 30, 17) . '</a>';
  }
*/
///////////////////
/////////////////////////////////////////////////

$logo = '';
if (tep_not_null($row_contact['recruiter_logo']) && is_file(PATH_TO_MAIN_PHYSICAL_LOGO . $row_contact['recruiter_logo'])) {

	$logo = ($row_contact['recruiter_logo'] ? tep_image(FILENAME_IMAGE . '?image_name=' . PATH_TO_LOGO . $row_contact['recruiter_logo'] . '&size=400', '', '', '') : '');
	if (tep_not_null($row_contact['recruiter_url'])) {
		if ((substr($row_contact['recruiter_url'], 0, 7) == 'http://') || (substr($row_contact['recruiter_url'], 0, 8) == 'https://'))
			$seller_recruiter_url = $row_contact['recruiter_url'];
		else
			$recruiter_url = "http://" . $row_contact['recruiter_url'];
		$logo = "<a href='" . $recruiter_url . "' target='_blank'>" . $logo . "</a>";
	}
	$logo = '<div class="no-pic mx-auto mb-3">' . $logo . '</div>
    ';
} else {
	$logo = '<div>
				'.defaultProfilePhotoUrl(tep_db_output($row_contact['recruiter_company_name']),false,112,'class="no-pic" id=""').'
 			</div>
			<div style="text-center">
				<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION) . '" class="small d-block" style="color:#0a66c2!important;">
					Add Logo
				</a>
			</div>';
}
$template->assign_vars(array(
	'HEADING_TITLE'           => HEADING_TITLE,
	'TOTAL_VIEW'			   => $view_total,
	'INFO_TEXT_LOGO'          => $logo,
	'INFO_TEXT_BRIEFPROFILE'  => INFO_TEXT_BRIEFPROFILE,
	'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
	'INFO_TEXT_EMAIL_ADDRESS1' => tep_db_output($row_contact['recruiter_email_address']),
	'INFO_TEXT_ADDRESS'       => INFO_TEXT_ADDRESS,
	'INFO_TEXT_ADDRESS1'      => tep_db_output($row_contact['recruiter_address1'] . (tep_not_null($row_contact['recruiter_address2']) ? ', ' . $row_contact['recruiter_address2'] : '')) . (tep_not_null($row_contact['recruiter_city']) ? ', ' . $row_contact['recruiter_city'] : '') . (tep_not_null($row_contact['location']) ? ', ' . $row_contact['location'] : '') . (tep_not_null($row_contact[TEXT_LANGUAGE . 'country_name']) ? ', ' . $row_contact[TEXT_LANGUAGE . 'country_name'] : ''),
	'INFO_TEXT_PHONE'         => INFO_TEXT_PHONE,
	'INFO_TEXT_PHONE1'        => tep_db_output($row_contact['recruiter_telephone']),
	// 'INFO_TEXT_FAX'           => INFO_TEXT_FAX,
	//'INFO_TEXT_FAX1'          => tep_db_output($row_contact['fax']),
	'INFO_TEXT_GRAPH'         => $graph,
	'pieChart' 			   =>  json_encode($pieChart, JSON_NUMERIC_CHECK),
	'barChart' 			   =>  json_encode($barChart, JSON_NUMERIC_CHECK),
	'INFO_SUBSCRIPTION' => INFO_SUBSCRIPTION,
	'INFO_TEXT_ACCOUNT_DETAILS' => INFO_TEXT_ACCOUNT_DETAILS,
	'INFO_TEXT_NO_OF_JOBS'      => INFO_TEXT_NO_OF_JOBS,
	'INFO_TEXT_NO_OF_JOBS1'     => $job,
	'INFO_TEXT_NO_OF_JOBS_USED' => INFO_TEXT_NO_OF_JOBS_USED,
	'INFO_TEXT_NO_OF_JOBS_USED1' => $enjoyed_job,
	'INFO_TEXT_NO_OF_CV'        => INFO_TEXT_NO_OF_CV,
	'INFO_TEXT_NO_OF_CV1'       => $cv,
	'INFO_TEXT_NO_OF_CV_USED'   => INFO_TEXT_NO_OF_CV_USED,


	'TEXT_TOTAL_VIEWS'   => TEXT_TOTAL_VIEWS,
	'SUBSCRIPTION_PLAN'   => SUBSCRIPTION_PLAN,
	'SUBSCRIPTION_PLAN_TEXT'   => SUBSCRIPTION_PLAN_TEXT,
	'START_APPLICANT_TRACKING'   => START_APPLICANT_TRACKING,






	'INFO_TEXT_NO_OF_CV_USED1'  => $enjoyed_cv,
	'INFO_TEXT_PLAN_TYPE'       => (tep_not_null($plan) ? "Plan : " . tep_db_output($plan) : '<a href="' . tep_href_link(FILENAME_RECRUITER_RATES) . '">' . INFO_TEXT_CHOOSE_A_PLAN . '</a>'),
	'INFO_TEXT_PLAN_TYPE1'      => (tep_not_null($plan1) ? "Plan : " . tep_db_output($plan1) : '<a href="' . tep_href_link(FILENAME_RECRUITER_RATES) . '">' . INFO_TEXT_CHOOSE_A_PLAN . '</a>'),
	'INFO_TEXT_FROM_TO'         => $from_to,
	'INFO_TEXT_FROM_TO1'        => $from_to1,
	'INFO_TEXT_COMPANY_NAME'    => tep_db_output($row_contact['recruiter_company_name']),
	'INFO_TEXT_JOB_POSTING'     => INFO_TEXT_JOB_POSTING,
	'INFO_TEXT_POST_JOB'        => '<a href="' . tep_href_link(FILENAME_RECRUITER_POST_JOB) . '" title="' . INFO_TEXT_POST_JOB . '" class="customClass style39 mw-100 mmt-15">' . INFO_TEXT_POST_JOB . '</a>',
	'INFO_TEXT_POST_JOB2'        => '<a href="' . tep_href_link(FILENAME_RECRUITER_POST_JOB) . '" title="' . INFO_TEXT_POST_JOB . '" class="style39 customClass2"><i class="bi bi-pencil-square me-3"></i>' . INFO_TEXT_POST_JOB . '</a>',
	'INFO_TEXT_LIST_OF_JOBS'    => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS) . '" title="' . INFO_TEXT_LIST_OF_JOBS . '" class="style39">' . INFO_TEXT_LIST_OF_JOBS . '</a> ',
	'INFO_TEXT_MY_ACTIVE_JOBS'  => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS) . '" title="' . INFO_TEXT_LIST_OF_JOBS . '" class="style39">' . INFO_TEXT_MY_ACTIVE_JOBS . ' ' . (($no_of_active_job > 0) ? '(' . $no_of_active_job . ')</a>' : ''),
	'INFO_TEXT_MY_EXPIRED_JOBS' => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS, 'j_status=expired') . '" title="' . INFO_TEXT_LIST_OF_JOBS . '" class="style39">' . INFO_TEXT_MY_EXPIRED_JOBS . '</a> ' . (($no_of_expired_job > 0) ? '(' . $no_of_expired_job . ')' : ''),
	'INFO_TEXT_IMPORT_JOBS'     => '<a href="' . tep_href_link(FILENAME_RECRUITER_IMPORT_JOBS) . '" title="' . INFO_TEXT_IMPORT_JOBS . '" class="style39" >' . INFO_TEXT_IMPORT_JOBS . '</a> ',
	'INFO_TEXT_REPORTS'     => '<a href="' . tep_href_link(PATH_TO_REPORTS.FILENAME_REPORTS) . '" title="' . INFO_TEXT_REPORTS . '" class="style39" >' . INFO_TEXT_REPORTS . '</a> ',
	'INFO_TEXT_ADMIN_RESPONSE'  => '<a href="' . tep_href_link(FILENAME_RECRUITER_MAILS) . '" title="' . INFO_TEXT_ADMIN_RESPONSE . '" class="style39" >' . INFO_TEXT_ADMIN_RESPONSE . '</a> ',
	'INFO_TEXT_JOBSEEKER_RESPONSE'  => '<a href="' . tep_href_link(FILENAME_RECRUITER_ATS_MAILS) . '" title="' . INFO_TEXT_JOBSEEKER_RESPONSE . '" class="style39" >' . INFO_TEXT_JOBSEEKER_RESPONSE . '</a> ',

	'INFO_TEXT_LIST_OF_JOBFAIRS'    => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS) . '" title="' . INFO_TEXT_LIST_OF_JOBFAIRS . '" class="style39">' . INFO_TEXT_LIST_OF_JOBFAIRS . '</a> ' . (($no_of_jobfairs > 0) ? '(' . $no_of_jobfairs . ')' : ''),

	//'INFO_TEXT_DIRECT_LOGIN'=>'Apply Without Login '.$direct_login_status,
	'INFO_TEXT_DIRECT_LOGIN' => tep_draw_form('recstatus', FILENAME_RECRUITER_CONTROL_PANEL, '', 'post', '') . ($direct_login == 'Yes' ? tep_draw_hidden_field('action', 'direct_login_inactive') : tep_draw_hidden_field('action', 'direct_login_active')) . '
' . INFO_TEXT_APPLY_WITHOUT_LOGIN . ' <label for="checkbox_rec_dr_log" class="switch">' . tep_draw_checkbox_field('direct_login', 'Yes', '', $direct_login, ' class="inputdemo" id="checkbox_rec_dr_log" onchange="this.form.submit();"') . '<span class="slider round"></span></label></form>',

	'INFO_TEXT_HEADER_SEARCH_RESUMES' => INFO_TEXT_HEADER_SEARCH_RESUMES,
	'INFO_TEXT_SEARCH_RESUMES' => '<a href="' . tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME) . '" title="' . INFO_TEXT_SEARCH_RESUMES . '" class="style39">' . INFO_TEXT_SEARCH_RESUMES . '</a>',
	'INFO_TEXT_SEARCH_APPLICANT' => '<a href="' . tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT) . '" title="' . INFO_TEXT_SEARCH_APPLICANT . '" class="style39">' . INFO_TEXT_SEARCH_APPLICANT . '</a>',
	'INFO_TEXT_RESUME_SEARCH_AGENT' => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_RESUME_SEARCH_AGENTS) . '" title="' . INFO_TEXT_RESUME_SEARCH_AGENT . '" class="style39">' . INFO_TEXT_RESUME_SEARCH_AGENT . '</a> ' . (($no_of_save_search > 0) ? '(' . $no_of_save_search . ')' : ''),
	'INFO_TEXT_SAVE_RESUME' => '<a href="' . tep_href_link(FILENAME_RECRUITER_SAVE_RESUME) . '" title="' . INFO_TEXT_SAVE_RESUME . '" class="style39">' . INFO_TEXT_SAVE_RESUME . '</a> ' . (($no_of_save_resume > 0) ? '(' . $no_of_save_resume . ')' : ''),

	'INFO_TEXT_MY_ACCOUNT' => INFO_TEXT_MY_ACCOUNT,
	'INFO_TEXT_EDIT_PROFILE' => '<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION) . '" title="' . INFO_TEXT_EDIT_PROFILE . '" class="btn btn-outline-secondary"><ion-icon name="create-outline"></ion-icon> ' . INFO_TEXT_EDIT_PROFILE . '</a>',
	'INFO_TEXT_EDIT_PROFILE2' => '<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION) . '" title="' . INFO_TEXT_EDIT_PROFILE . '" class="style39">' . INFO_TEXT_EDIT_PROFILE . '</a>',
	'INFO_TEXT_COMPANY_DESCRIPTION' => '<a href="' . tep_href_link(FILENAME_RECRUITER_COMPANY_DESCRIPTION) . '" title="' . INFO_TEXT_COMPANY_DESCRIPTION . '" class="style39">' . INFO_TEXT_COMPANY_DESCRIPTION . '</a>',
	'INFO_TEXT_ORDER_HISTORY' => '<a href="' . tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO) . '" title="' . INFO_TEXT_ORDER_HISTORY . '" class="style39">' . INFO_TEXT_ORDER_HISTORY . '</a>',
	'INFO_TEXT_MANAGED_USERS' => '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS) . '" title="' . INFO_TEXT_MANAGED_USERS . '" class="style39">' . INFO_TEXT_MANAGED_USERS . '</a> ' . (($no_of_user > 0) ? '(' . $no_of_user . ')' : ''),
	'INFO_TEXT_CONTACT_LIST' => '<a href="' . tep_href_link(FILENAME_RECRUITER_CONTACT_LIST) . '" title="' . INFO_TEXT_CONTACT_LIST . '" class="style39"><span >' . INFO_TEXT_CONTACT_LIST . ' </span></a>' . (($no_of_contact > 0) ? '(' . $no_of_contact . ')' : ''),
	'INFO_TEXT_NEWSLETTER' => '<a href="' . tep_href_link(FILENAME_LIST_OF_NEWSLETTERS) . '" title="' . INFO_TEXT_NEWSLETTER . '" class="style39">' . INFO_TEXT_NEWSLETTER . '</a> ' . (($no_of_news_letters > 0) ? '(' . $no_of_news_letters . ')' : ''),
	'INFO_TEXT_CHANGE_PASSWORD' => '<a href="' . tep_href_link(FILENAME_RECRUITER_CHANGE_PASSWORD) . '" title="' . INFO_TEXT_CHANGE_PASSWORD . '" class="style39">' . INFO_TEXT_CHANGE_PASSWORD . '</a>',
	'INFO_TEXT_LOG_OUT' => '<a href="' . tep_href_link(FILENAME_LOGOUT) . '" title="' . INFO_TEXT_LOG_OUT . '" class="style39"><span class="red">' . INFO_TEXT_LOG_OUT . '</span></a>',
	'INFO_TEXT_RATE_CHART' => '<a href="' . tep_href_link(FILENAME_RECRUITER_RATES) . '" title="' . INFO_TEXT_RATE_CHART . '" class="style39">' . INFO_TEXT_RATE_CHART . '</a>',
	'INFO_TEXT_RATE_CHART1' => '',
	'RATE_CARD' => '<a class="" href="' . tep_href_link(FILENAME_RECRUITER_RATES) . '">
	<i class="bi bi-cart"></i> '.TEXT_RATE_CARD.'
                                              </a>',
	'INFO_TEXT_JOIN_FORUM'    => '<a href="' . tep_href_link(PATH_TO_FORUM) . '" class="style39">' . INFO_TEXT_JOIN_FORUM . '</a>',
	'INFO_TEXT_ARTICLE'    => '<a href="' . tep_href_link(FILENAME_ARTICLE) . '" class="style39">' . 'Articles' . '</a>',
	'INFO_TEXT_INTERVIEW'    => '<a href="' . tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING) . '" class="style39">' . 'Interview' . '</a>',
	'INFO_TEXT_LIST_OF_APPLICATIONS' => tep_draw_form('search_applicant', FILENAME_RECRUITER_SEARCH_APPLICANT, '', 'post') . tep_draw_hidden_field('action1', 'search') . '<button role="button" class="submitbutton2" type="submit">' . INFO_TEXT_LIST_OF_APPLICATIONS . '</button> (' . $no_of_applicant . ')</form>' . (($no_of_applicant > 0) ? '' : ''),

	'INFO_TEXT_APPLICANT_TRACKING' => tep_draw_form('applicant_tracking', FILENAME_RECRUITER_APPLICANT_TRACKING, '', 'post') . tep_draw_hidden_field('action1', 'search') . '<button role="button" class="submitbutton2" type="submit">' . INFO_TEXT_APPLICANT_TRACKING . '</button> (' . $no_of_applicant . ')</form>' . (($no_of_applicant > 0) ? '' : ''),

	'INFO_TEXT_LIST_OF_UNREGISTERED_RESUMES' => ($direct_login == 'Yes' ? '<tr>

                                              <td class="style39"><a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES) . '" title="' . INFO_TEXT_UNREGISTERED_RESUMES . '" class="style39">' . INFO_TEXT_UNREGISTERED_RESUMES . '</a>
                                              </td>
                                            </tr>' : ''),

	'INFO_TEXT_CURRENT_STATUS' => INFO_TEXT_CURRENT_STATUS,
	'INFO_TEXT_TOTAL_JOBS_HEADING' => INFO_TEXT_TOTAL_JOBS,
	'INFO_TEXT_TOTAL_JOBS' => $no_of_job,
	//  'INFO_TEXT_TOTAL_APPLICANT'=>($direct_login=='Yes'?INFO_TEXT_TOTAL_APPLICANT:INFO_TEXT_TOTAL_APPLICATIONS)." ".$no_of_applicant,
	'INFO_TEXT_TOTAL_APPLICANT_HEADING' => INFO_TEXT_TOTAL_APPLICANT,
	'INFO_TEXT_TOTAL_APPLICANT' => $no_of_applicant,
	'INFO_TEXT_TOTAL_DIRECT_APPLICANT' => ($direct_login == 'Yes' ? '
											<div>
											  <i class="fa fa-angle-right icon-page-title" aria-hidden="true">
                                                </i> ' . INFO_TEXT_TOTAL_DIRECT_APPLICANT . " " . $no_of_direct_applicant . '
                                            </div>
										<div>

                                             ' . INFO_TEXT_TOTAL_APPLICATIONS . " " . $total_no_of_applications . '
                                             </div>' : ''),
	//  'INFO_TEXT_TOTAL_APPLICANT_SELETED'=>($direct_login=='Yes'?INFO_TEXT_TOTAL_APPLICANT_REGISTERED_SELETED:INFO_TEXT_TOTAL_APPLICANT_SELETED)." ".$no_of_selectd_applicant,
	'INFO_TEXT_TOTAL_APPLICANT_SELETED_HEADING' => INFO_TEXT_TOTAL_APPLICANT_REGISTERED_SELETED,
	'INFO_TEXT_TOTAL_APPLICANT_SELETED' => $no_of_selectd_applicant,
	'INFO_TEXT_EMPLOYER_CONTROL_PANEL' => INFO_TEXT_EMPLOYER_CONTROL_PANEL,
	'INFO_TEXT_RESUME'  => INFO_TEXT_RESUME,
	'ONLINE_TEST' => '<a href="' . tep_href_link(FILENAME_QUIZ.'/'.FILENAME_RECRUITER_ASSESSMENT) . '"class="style39">
								Online Test
							</a>',
	'LEFT_BOX_WIDTH' => LEFT_BOX_WIDTH1,
	'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
	'LEFT_HTML' => LEFT_HTML,
	'RIGHT_HTML' => RIGHT_HTML,
	'update_message' => $messageStack->output()
));
$template->pparse('control_panel');
