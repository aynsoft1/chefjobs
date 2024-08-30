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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_COMPANY_DETAILS);
$template->set_filenames(array('details' => 'company_details.htm'));
include_once(FILENAME_BODY);

$query_string=tep_db_prepare_input($_GET['query_string']);
$query_string1=tep_db_prepare_input($_GET['query_string1']);
$recuiter_address='';

$action = (isset($_POST['action']) ? $_POST['action'] : '');

if(tep_not_null($action))
{
 switch($action)
 {
////************** COMPANY RATING BEGIN ***************************///////
	case 'rate_it':
		$job_id=check_data($query_string,"=","job_id","job_id");
		if($rec_id=getAnyTableWhereData(JOB_TABLE," job_id='".$job_id."'",'recruiter_id'))
	  {
		$recruiter_id=$rec_id['recruiter_id'];
	  }


	  if(check_login('admin'))
	  {
		$adminedit=true;
		$sql_data_array=array('recruiter_id'=>$recruiter_id,
		'point'=>tep_db_prepare_input($_POST['rate_it']),
		'admin_rate'=>'Y',
	  );
	  if($row_rating=getAnyTableWhereData(RECRUITER_RATING_TABLE," jobseeker_id='".$jobseeker_id."' and  admin_rate ='Y'",'rating_id'))
	  {
		tep_db_perform(RECRUITER_RATING_TABLE, $sql_data_array, 'update',"rating_id='".$row_rating['rating_id']."'");
	  }
	  else
	  {
		tep_db_perform(RECRUITER_RATING_TABLE, $sql_data_array);
	  }
	  $messageStack->add_session(MESSAGE_SUCCESS_RATED, 'success');
	  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,tep_get_all_get_params()));
	}
	if(check_login('jobseeker') && $adminedit==false)
	{
	  $sql_data_array=array('recruiter_id'=>$recruiter_id,
	  'jobseeker_id'=>$_SESSION['sess_jobseekerid'],
	  'admin_rate'=>'N',
	  'point'=>tep_db_prepare_input($_POST['rate_it']),
	  'private_notes'=>tep_db_prepare_input($_POST['private_notes']),
	);
	if($row_rating=getAnyTableWhereData(RECRUITER_RATING_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and recruiter_id='".$recruiter_id."'",'rating_id'))
	{
	  tep_db_perform(RECRUITER_RATING_TABLE, $sql_data_array, 'update',"rating_id='".$row_rating['rating_id']."'");
	}
	else
	{
	  tep_db_perform(RECRUITER_RATING_TABLE, $sql_data_array);
	}
	$messageStack->add_session(MESSAGE_SUCCESS_RATED, 'success');
	}
	tep_redirect(tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,tep_get_all_get_params()));
	break;
///*******************************************COMPANY RATING ***************************************//
}
}


if(tep_not_null($query_string))
{
 $job_id=check_data($query_string,"=","job_id","job_id");
 $row=getAnyTableWhereData(JOB_TABLE.' as j left outer join '.RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (r.recruiter_id=rl.recruiter_id)  left outer  join '.COMPANY_DESCRIPTION_TABLE.' as cd on ( j.recruiter_id=cd.recruiter_id ) left outer join  '.COUNTRIES_TABLE.' as c on (r.recruiter_country_id=c.id) left outer join '.ZONES_TABLE.' as z on(r.recruiter_state_id=z.zone_id )',"j.job_id='".$job_id."' and j.recruiter_id=rl.recruiter_id and j.recruiter_id=r.recruiter_id","cd.id,rl.recruiter_id,cd.description,r.recruiter_logo,r.recruiter_url,r.recruiter_company_name,rl.recruiter_email_address,r.recruiter_city,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as recruiter_state,c.country_name,r.recruiter_zip,r.recruiter_telephone");
}
elseif(isset($_GET['company']))
{
 $company=tep_db_prepare_input($_GET['company']);
 $row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl  left outer join '.RECRUITER_TABLE.' as r on (r.recruiter_id=rl.recruiter_id)  left outer  join '.COMPANY_DESCRIPTION_TABLE.' as cd on ( rl.recruiter_id=cd.recruiter_id )  left outer join  '.COUNTRIES_TABLE.' as c on (r.recruiter_country_id=c.id) left outer join '.ZONES_TABLE.' as z on(r.recruiter_state_id=z.zone_id )',"r.recruiter_company_seo_name='".tep_db_input($company)."'", "cd.id,rl.recruiter_id,cd.description,r.recruiter_logo,r.recruiter_url,r.recruiter_company_name,rl.recruiter_email_address,r.recruiter_city,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as recruiter_state,c.country_name,r.recruiter_zip,r.recruiter_telephone");
}
else
{
 $recruiter_email_address=check_data1($query_string1,"=","recruiter_email","mail");
 $row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl  left outer join '.RECRUITER_TABLE.' as r on (r.recruiter_id=rl.recruiter_id)  left outer  join '.COMPANY_DESCRIPTION_TABLE.' as cd on ( rl.recruiter_id=cd.recruiter_id )  left outer join  '.COUNTRIES_TABLE.' as c on (r.recruiter_country_id=c.id) left outer join '.ZONES_TABLE.' as z on(r.recruiter_state_id=z.zone_id )',"rl.recruiter_email_address='".tep_db_input($recruiter_email_address)."'", "cd.id,rl.recruiter_id,cd.description,r.recruiter_logo,r.recruiter_url,r.recruiter_company_name,rl.recruiter_email_address,r.recruiter_city,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as recruiter_state,c.country_name,r.recruiter_zip,r.recruiter_telephone");
}
///Hack attempt
 //$messageStack->add_session(ERROR_COMPANY_DETAILS_NOT_EXIST, 'error');
 //tep_redirect(tep_href_link(FILENAME_ERROR));

 $company_logo=$row['recruiter_logo'];
 $recruiter_id=$row['recruiter_id'];
    $recuiter_address=($row['recruiter_city']!=''?$row['recruiter_city']:'');
    $recuiter_address.=' '.$row['recruiter_state'];
    $recuiter_address.=' '.$row['country_name'];
    $recuiter_address =trim($recuiter_address);
   // if($recuiter_address!='' && $row['recruiter_telephone']!='')
	//$recuiter_address.="<br>Ph:".$row['recruiter_telephone'];
    if($recuiter_address!='' && $row['recruiter_zip']!='')
    $recuiter_address.="<br>".$row['recruiter_zip'];
 
 $company_name=$row['recruiter_company_name'];
 $email_id=$row["recruiter_email_address"];
 $query_string1=encode_string("recruiter_email=".$email_id."=mail");
 if(is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
 {
  $company_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'','','','" class="img-fluid img-thumbnail rounded company-profile-img "');
  if(tep_not_null($row['recruiter_url']))
  {
   $company_logo='<a href="'.$row['recruiter_url'].'" target="new_site">'.$company_logo.'</a>';
  }
 }
 else
  $company_logo='';//tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_IMG."nologo.jpg&size=100");

$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on  (rl.recruiter_id=r.recruiter_id ) left outer join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL)';
$whereClause.=" j.recruiter_id='".$row['recruiter_id']."' and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
$field_names="j.job_id, j.job_title,j.re_adv,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location";
$query = "select $field_names from $table_names where $whereClause ORDER BY j.re_adv desc limit 0,11";
$result=tep_db_query($query);
$no_of_jobs =tep_db_num_rows($result);

$i=0;
while($row_jobs = tep_db_fetch_array($result))
{
	if($i>=10)
		continue;
 $ide=$row_jobs["job_id"];
 $title_format=encode_category($row_jobs['job_title']);

 $template->assign_block_vars('jobs', array('inserted'=>formate_date1($row_jobs["re_adv"],'%d, %b'),
		                                          'job_title'=>'<a href="'.tep_href_link($ide.'/'.$title_format.'.html').'">'.tep_db_output($row_jobs['job_title']).'</a>' ,
		                                          'job_location'=>tep_db_output($row_jobs['location']),
																																													));
	$i++;

}
if(tep_not_null($jobs))
{
 $jobs="<div class='sectionHeading'><u>".INFO_TEXT_LATEST_POSITIONS."</u></div> <br>".implode("",$jobs);
}
else
 $jobs='';


/////////////**************************   COMPANY RATING BEGIN **************************/////////////
if(check_login('admin'))
{
$adminedit=true;
$row_rating=getAnyTableWhereData(RECRUITER_RATING_TABLE," recruiter_id='".$recruiter_id."' and admin_rate='Y'",'point');
$rate_it_array=array();
for($i=1;$i<=5;$i++)
{
$rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string='';
$rate_it_string.=INFO_TEXT_CURRENT_RATE_IT.'';
$rate_it_string.=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'3', '', false);
$rate_it_string.='';
$rate_it_string.=''.tep_draw_submit_button_field('','Rate','class="btn btn-primary mt-1 float-right mb-3"');//.tep_image_submit(PATH_TO_BUTTON.'button_rate.gif',IMAGE_RATE).'';
}
if(check_login('jobseeker') && $adminedit==false)
{
$row_rating=getAnyTableWhereData(RECRUITER_RATING_TABLE," jobseeker_id='".$_SESSION['sess_jobseekerid']."' and recruiter_id='".$recruiter_id."'",'point,private_notes');
$rate_it_array=array();
for($i=1;$i<=5;$i++)
{
$rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string.='<div class="form-group row" id="rate_id_div"><label class="col-md-2 text-right">'.INFO_TEXT_CURRENT_RATE_IT.':</label>';
$rate_it_string.='<div class="col-md-10">'.tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'3', '', false).' '.(check_login("jobseeker")?tep_draw_submit_button_field('','Rate','class="btn btn-primary mt-1 float-right mb-3"'):'').' </div></div>';

//$rate_it_string.='<div class="form-group row" id="rate_id_div"><label class="col-md-2 text-right">'.INFO_TEXT_PRIVATE_NOTES.':</label>';
//$rate_it_string.='<div class="col-md-10">'.tep_draw_textarea_field('private_notes', 'soft', '60', '4', tep_not_null($row_rating['private_notes'])?$row_rating['private_notes']:'', '', '',false).'</div></div>';
//$rate_it_string.=''.(check_login("jobseeker")?tep_draw_submit_button_field('','Rate','class="btn btn-primary mt-1 float-right mb-3"'):'').'';
}
//////////////**************************  COMAPNY RATION END ***********************////
define("ENABLE_COMPANY_RATING","false");
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
///////////////////////////////////////////////////////////////////////
'RATE_RESUME_BUTTON_FOR_REC'=>((check_login("jobseeker") && ENABLE_COMPANY_RATING=='true')?'<a class="btn btn-sm btn-outline-warning mb-3" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-star" title="Print" aria-hidden="true"></i> Rate this Company</a>':''),
'INFO_TEXT_CURRENT_RATING'  =>(check_login('jobseeker') || ($adminedit==true)?INFO_TEXT_CURRENT_RATING:''),
'INFO_TEXT_CURRENT_RATING1' =>(check_login('jobseeker') || ($adminedit==true)?(tep_not_null($row_rating['point'])?number_format($row_rating['point'],1):INFO_TEXT_NOT_RATED):''),
'INFO_TEXT_CURRENT_RATE_IT' =>(check_login("jobseeker") || ($adminedit==true)?INFO_TEXT_CURRENT_RATE_IT:''),
'INFO_TEXT_CURRENT_RATE_IT1'=>(check_login("jobseeker") || ($adminedit==true)?$rate_it_string:''),
'rate_form'=>tep_draw_form('rate_form', FILENAME_JOBSEEKER_COMPANY_DETAILS, tep_get_all_get_params(), 'post', '').tep_draw_hidden_field('action','rate_it'),
'comment_start'=>(check_login('jobseeker') || ($adminedit==true)?'':'<!--'),
'comment_end'=>(check_login('jobseeker') || ($adminedit==true)?'':'-->'),
'SECTION_RATE_COMPANY'       => SECTION_RATE_COMPANY,
//////////////////////////////////////////////////////////////////////////////////////

'INFO_TEXT_DESCRIPTION1'=>stripslashes($row['description']),
'button'=>'<a href="javascript:history.back();">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>',
'hidden_fields'=>$hidden_fields,
'company_logo'=>$company_logo ?? defaultProfilePhotoUrl(tep_db_output($row['recruiter_company_name']),false,112,'class="img-fluid img-thumbnail rounded company-profile-img" id=""'),
'INFO_TEXT_RECRUITER_ADDRESS'=>$recuiter_address,
    'COMPANY_NAME' => tep_db_output($company_name),
    'INFO_TEXT_JOBS' =>INFO_TEXT_JOBS,
    'INFO_TEXT_JOBS' => INFO_TEXT_JOBS,
'RESULT_CLASS'=>$no_of_jobs>0?'':'result_hide',
'MORE_JOB'=>$no_of_jobs>10?tep_draw_form('company_search', FILENAME_JOBSEEKER_COMPANY_PROFILE,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('company_name',$query_string1).'<a href="#" onclick="document.company_search.submit();" class="c_profile_more">More Jobs>></a></form>':'',
'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
'RIGHT_HTML'=>RIGHT_HTML,
 'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
  'LEFT_HTML'=>LEFT_HTML,
	'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
'JOB_SEARCH_LEFT'=>JOB_SEARCH_LEFT,
'update_message'=>$messageStack->output()));
$template->pparse('details');
?>