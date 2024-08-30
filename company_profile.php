<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik#********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
//session_cache_limiter('private_no_expire');
include_once("include_files.php");
//ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_COMPANY_PROFILE);
$template->set_filenames(array('company_profile' => 'company_profile.htm','company_profile_result'=>'company_profile_result.htm'));
include_once(FILENAME_BODY);
$state_error=false;
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$recruiter_id = (isset($_POST['recruiter_id']) ? $_POST['recruiter_id'] : $_GET['recruiter_id']);
   $now=date('Y-m-d H:i:s');
 // search
if(isset($_GET['company']) && tep_not_null($_GET['company'])  )
{
 $action ='search';
}

function count_jobs_for_company($recruiter_id='')
{
  $now=date('Y-m-d H:i:s');
  $total_job=0;
  $where ="j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and r.recruiter_id='".tep_db_input($recruiter_id)."'";
  if ($row=getAnyTableWhereData(RECRUITER_TABLE." as r left join ".JOB_TABLE." as j on j.recruiter_id = r.recruiter_id",$where,' COUNT(*) as total_jobs')) {
    if($row['total_jobs']>0){
      $total_job=$row['total_jobs'];
    }
  }
  return $total_job;
}

if(tep_not_null($action))
{
 switch($action)
 {
////************** COMPANY RATING BEGIN ***************************///////
	case 'rate_it':
//echo "res=".$rec_id;die;
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
	  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE,tep_get_all_get_params()));
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
	tep_redirect(tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE,tep_get_all_get_params()));
	break;
///*******************************************COMPANY RATING ***************************************//
  case 'follow_company':
///////////*** create job alert with company name to follow ****///
	if(check_login('jobseeker'))
	  {
		$rowfollow=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$recruiter_id."'",'recruiter_company_name');
		$rec_comp=$rowfollow['recruiter_company_name'];
		$sql_follow_array=array('jobseeker_id'=>$_SESSION['sess_jobseekerid'],
		'company'=>$_POST['recruiter_id'],
		'title_name'=>$rec_comp,
		'inserted'=>$now,
	  );
      tep_db_perform(SEARCH_JOB_RESULT_TABLE, $sql_follow_array);
	  $messageStack->add_session(MESSAGE_SUCCESS_FOLLOW, 'success');
	  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE,tep_get_all_get_params()));
	}
else //if not login
{
 //$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}

	////////////////////////////////////////////////////////////////////////////
	break;
  case 'search':
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $company_name=tep_db_prepare_input($_POST['company_name']);
   $recruiter_email_address=check_data1($company_name,"=","recruiter_email","mail");
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';
   if(isset($_GET['company']))
   {
    $company=tep_db_prepare_input($_GET['company']);
    $whereClause.=" r.recruiter_company_seo_name ='".tep_db_input($company)."'  ";
	$hidden_fields.=tep_draw_hidden_field('company',$company);
   }
   elseif(isset($_POST['company']))
   {
    $company=tep_db_prepare_input($_POST['company']);
    $whereClause.=" r.recruiter_company_seo_name ='".tep_db_input($company)."'  ";
	$hidden_fields.=tep_draw_hidden_field('company',$company);
   }
   elseif(isset($_POST['company_name']))
   {
    $whereClause.=" rl.recruiter_email_address ='".tep_db_input($recruiter_email_address)."'  ";
	$hidden_fields.=tep_draw_hidden_field('company_name',$company_name);
   }
   else
    $whereClause.=" 0 "; //172.241.250.183


   // company_name ends ///
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $field_names="j.job_id,j.job_title,j.job_industry_sector,j.re_adv,j.expired,r.recruiter_featured,r.recruiter_company_name,r.recruiter_id";
   $table_names=RECRUITER_TABLE.' as r, '.RECRUITER_LOGIN_TABLE.' as rl, '.JOB_TABLE." as j";
   $whereClause.="r.recruiter_id=rl.recruiter_id and rl.recruiter_status='Yes' and r.recruiter_id=j.recruiter_id  and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;//exit;
   //////////////////
  ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("j.job_title",'r.recruiter_company_name','j.re_adv','j.expired');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'j.re_adv desc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number123($sort_array,$field,'j.re_adv',$order,'desc',$lower,'0',$higher,'20');
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);
   $template->assign_vars(array('TABLE_HEADING_JOB_TITLE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_JOB_TITLE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                                'TABLE_HEADING_COMPANY_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_COMPANY_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                                'TABLE_HEADING_ADVERTISED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_ADVERTISED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
                                'TABLE_HEADING_EXPIRED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_EXPIRED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>"));
  ///only for sorting ends

   $totalpage=ceil($x1/$higher);
   $query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher";
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
     $ide=$row["job_id"];
     $title_format=encode_category($row['job_title']);
     $query_string=encode_string("job_id=".$ide."=job_id");
     $company_name=tep_db_output($row['recruiter_company_name']);

     if($row['recruiter_featured']=='Yes')
     {
     // $company_name='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'">'.$company_name.'</a>';
     $company_name='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string1='.$query_string1).'">'.$company_name.'</a>';
}
	$job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',$ide);

     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
     $template->assign_block_vars('result', array( 'row_selected' => $row_selected,
      'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'">'.tep_db_output($row['job_title']).'</u></a>',
      'company_name' => $company_name,
      'job_category' => ((tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id', tep_db_output($job_category_ids)):'-'),
      're_adv' => tep_date_long(tep_db_output($row['re_adv'])),
      'expired' => tep_date_long(tep_db_output($row['expired'])),
      'apply' => '<a style="color:#0a66c2!important;text-transform:capitalize;" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.INFO_TEXT_APPLY_NOW.'</a>',
      ));
     $alternate++;
     $lower = $lower + 1;
	 if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$ide."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$ide."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
     }
	  $curr_date =date('Y-m-d');
	 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".tep_db_input($ide)."' and  date='".tep_db_input($curr_date)."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".$ide."' and  date='".tep_db_input($curr_date)."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
		                    'date'=>$curr_date,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
     }

    }
    $plural=($x1=="1")? INFO_TEXT_JOB:INFO_TEXT_JOBS;
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).INFO_TEXT_HAS_MATCHED." <span class=''>$x1</span> ".$plural. " ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
   }
   else
   {
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED));
   }
   see_page_number();
   tep_db_free_result($result1);
   tep_db_free_result($result);
  }
  $company_name=tep_db_prepare_input($_POST['company_name']);
  $recruiter_email_address=check_data1($company_name,"=","recruiter_email","mail");
  $filed_names='r.recruiter_company_name,recruiter_logo,r.recruiter_url,d.description,r.recruiter_city,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as recruiter_state,c.country_name,r.recruiter_zip,r.recruiter_telephone,r.recruiter_id,r.recruiter_company_seo_name';
  $recuiter_address='';
  $company_logo1='';
  $company_description='';
  if(tep_not_null($company))
  {
    $whereClause1=  "r.recruiter_company_seo_name ='".tep_db_input($company)."'";
  }
  else
    $whereClause1=  " rl.recruiter_email_address= '".tep_db_input($recruiter_email_address)."'";

  if($row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE." as rl left join ".RECRUITER_TABLE." as r on ( r.recruiter_id = rl.recruiter_id)  left outer join ".COMPANY_DESCRIPTION_TABLE." as d on (rl.recruiter_id=d.recruiter_id) left outer join  ".COUNTRIES_TABLE." as c on (r.recruiter_country_id=c.id) left outer join ".ZONES_TABLE." as z on(r.recruiter_state_id=z.zone_id )" ,$whereClause1,$filed_names))
  {
   $query_string1=encode_string("recruiter_email=".$recruiter_email_address."=mail");
   if($row_info['recruiter_city']!='')
   $recuiter_address=$row_info['recruiter_city'];
   $recuiter_address.=' '.$row_info['recruiter_state'];
   $recuiter_address.=' '.$row_info['country_name'];
   $recuiter_address =trim($recuiter_address);
  // if($recuiter_address!='' && $row_info['recruiter_telephone']!='')
 //   $recuiter_address.="<br>Ph:".$row_info['recruiter_telephone'];
   if($recuiter_address!='' && $row_info['recruiter_zip']!='')
   $recuiter_address.=" - ".$row_info['recruiter_zip'];
	$recruiter_id=$row_info['recruiter_id'];

   $recruiter_company_name=tep_db_output($row_info['recruiter_company_name']);
   $header_title='<title>'.tep_db_output($row_info['recruiter_company_name']).'</title>';
   $company_logo=$row_info['recruiter_logo'];
   $company_description= strip_tags($row_info['description'],'<a><b><i><u><br>');
   if($company_description!='')
   {
    if(strlen($company_description)>=350)
     //$company_description='<div><b>Description</b><br>'.stripslashes(substr(strip_tags($company_description,"['p', 'br']"),0,350)).' <a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string1='.$query_string1).'"  style="color:red;">more >></a>'.' </div>';
     $company_description='<div><b><span class="mb-2" style="display: inline-block;">ABOUT COMPANY</span></b><br>'.stripslashes(substr(strip_tags($company_description,"['p', 'br']"),0,350)).' <a href="'.getPermalink('company',array('seo_name'=>$row_info["recruiter_company_seo_name"])).'"style="color:#0a66c2!important;">..read more</a>'.' </div>';
    else
    $company_description='<div><b>ABOUT COMPANY</b><br>'.stripslashes($company_description).'</div>';
   }
   if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
   {
    if(tep_not_null($row['recruiter_url']))
    {
     $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'','','','" class="img-fluid img-thumbnail rounded mini-profile-img "');
     $company_logo1='<a href="'.$row['recruiter_url'].'" target="new_site">'.$photo.'</a>';
    }
    else
    {
     $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'','','','" class="img-fluid img-thumbnail rounded mini-profile-img" ');
     $company_logo1=$photo;
    }
   }
  }
/////////////**************************   COMPANY RATING BEGIN **************************/////////////
if(check_login('admin'))
{
$adminedit=true;
$row_rating=getAnyTableWhereData(RECRUITER_RATING_TABLE," jobseeker_id='".$jobseeker_id."' and admin_rate='Y'",'point');
$rate_it_array=array();
for($i=1;$i<=5;$i++)
{
$rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string='';
$rate_it_string.=INFO_TEXT_CURRENT_RATE_IT.'';
$rate_it_string.=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'5', '', false);
$rate_it_string.='';
$rate_it_string.=''.tep_image_submit(PATH_TO_BUTTON.'button_rate.gif',IMAGE_RATE).'';
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
//$rate_it_string.=''.(check_login("jobseeker")?tep_draw_submit_button_field('','Add','class="btn btn-primary mt-1 float-right mb-3"'):'').'';
}
//////////////**************************  COMAPANY RATION END ***********************////

///////*****************  FIND OUT COMPANY ALREADY FOLLOWED OR NOT BEGIN ******************///

$follow_button=tep_draw_form('fllowcomp', FILENAME_JOBSEEKER_COMPANY_PROFILE,'', 'post', '').tep_draw_hidden_field('action','follow_company').tep_draw_hidden_field('recruiter_id',$recruiter_id).tep_draw_submit_button_field('','Follow','class="btn btn-primary mt-1 float-right mb-3"')."</form>";

if(check_login('jobseeker') || check_login('admin'))
{
	$row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$recruiter_id."'",'recruiter_company_name');
	$rec_comp=$row['recruiter_company_name'];
$wherecheck="jobseeker_id='".$_SESSION['sess_jobseekerid']."' and title_name='".$rec_comp."' and company='".$recruiter_id."' and keyword='' && location='' && country='' && state='' && industry_sector='' && job_type='' && experience='' && job_salary='' && zip_code='' && radius='0' && job_alert='daily'";
if($rowcheck=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,$wherecheck,'id'))
		$follow_button='<button class="btn btn-primary mt-1 float-right mb-3" onclick="location.href=\''.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'\'">Unfollow</button>';
}

///////*****************  FIND OUT COMPANY ALREADY FOLLOWED OR NOT END ******************///
define("ENABLE_COMPANY_RATING","false");

  $template->assign_vars(array(
 'HEADING_TITLE'=>$recruiter_company_name,
///////////////////////////////////////////////////////////////////////
'RATE_RESUME_BUTTON_FOR_REC'=>((check_login("jobseeker") && ENABLE_COMPANY_RATING=='true')?'<a class="btn btn-sm btn-outline-warning mb-3" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-star" title="Print" aria-hidden="true"></i> Rate this Company</a>':''),
'INFO_TEXT_CURRENT_RATING'  =>(check_login('jobseeker') || ($adminedit==true)?INFO_TEXT_CURRENT_RATING:''),
'INFO_TEXT_CURRENT_RATING1' =>(check_login('jobseeker') || ($adminedit==true)?(tep_not_null($row_rating['point'])?number_format($row_rating['point'],1):INFO_TEXT_NOT_RATED):''),
'INFO_TEXT_CURRENT_RATE_IT' =>(check_login("jobseeker") || ($adminedit==true)?INFO_TEXT_CURRENT_RATE_IT:''),
'INFO_TEXT_CURRENT_RATE_IT1'=>(check_login("jobseeker") || ($adminedit==true)?$rate_it_string:''),
'rate_form'=>tep_draw_form('rate_form', FILENAME_JOBSEEKER_COMPANY_PROFILE,'', 'post', '').tep_draw_hidden_field('action','rate_it').tep_draw_hidden_field('recruiter_id',$recruiter_id),
'comment_start'=>(check_login('jobseeker') || ($adminedit==true)?'':'<!--'),
'comment_end'=>(check_login('jobseeker') || ($adminedit==true)?'':'-->'),
'SECTION_RATE_COMPANY'       => SECTION_RATE_COMPANY,
//////////////////////////////////////////////////////////////////////////////////////
 'INFO_TEXT_HEADER_TITLE'=>$header_title,
 'INFO_TEXT_RECRUITER_LOGO'=>$company_logo1,
 'INFO_TEXT_RECRUITER_DESC'=>$company_description,
 'INFO_TEXT_RECRUITER_ADDRESS'=>$recuiter_address,
 'TABLE_HEADING_APPLY'=>TABLE_HEADING_APPLY,
 'TABLE_HEADING_JOB_CATEGORY'=>TABLE_HEADING_JOB_CATEGORY,
 'hidden_fields' => $hidden_fields,
 'back_button'=>tep_button_submit('btn btn-sm btn-outline-secondary','Back', 'onclick="history.back();"' ),
 'follow_button'=>$follow_button,
 'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
 'INFO_TEXT_COMPANY_NAME1' => $company_string,
 'hidden_fields' => $hidden_fields,
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'JOB_SEARCH_LEFT' => JOB_SEARCH_LEFT,
 'update_message' => $messageStack->output(),
 ));
 $template->pparse('company_profile_result');
}
else
{
 define('MAX_DISPLAY_DIRECTORY_RESULT',100);
	$now=date('Y-m-d H:i:s');
 $whereClause1=" select distinct(j.recruiter_id) as recruiter_id from ".JOB_TABLE."  as j  where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
 $whereClause="where rl.recruiter_status='Yes' and r.recruiter_id in ($whereClause1)";
// $whereClause="where rl.recruiter_status='Yes'";
 $query1 = "select count(r.recruiter_id ) as x1 from ".RECRUITER_TABLE." as r left join ".RECRUITER_LOGIN_TABLE." as rl on ( r.recruiter_id = rl.recruiter_id) ". $whereClause;
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];//echo $query1;
 //echo $x1;die();
 ///only for sorting starts
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("recruiter_company_name",'inserted');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'recruiter_company_name asc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 if(tep_not_null($_GET['directoru_char']))
 {
  $get_char=$_GET['directoru_char'];
  if (in_array($get_char,array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' )) && $get_char!='')
  {
   $recofigar_limit=(int)get_company_direct_limit($get_char);
   if($recofigar_limit>MAX_DISPLAY_DIRECTORY_RESULT)
    $recofigar_limit=$recofigar_limit-($recofigar_limit%MAX_DISPLAY_DIRECTORY_RESULT);//
   $_POST['lower']=$recofigar_limit;
   if($recofigar_limit>0)
   $_POST['page'][0]=(int)$recofigar_limit/MAX_DISPLAY_DIRECTORY_RESULT;

  }
 }

 $see_before_page_number_array=see_before_page_number123($sort_array,$field,'recruiter_company_name',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_DIRECTORY_RESULT);
 //$lower=
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $totalpage=ceil($x1/$higher);
 $fields="r.recruiter_company_name,rl.recruiter_email_address,r.recruiter_logo,r.recruiter_company_seo_name,r.recruiter_address1,r.recruiter_city,r.recruiter_country_id,if(r.recruiter_state_id,z.zone_name,r.recruiter_state) as recruiter_state,c.country_name,r.recruiter_id";
 $query = "select $fields  from ".RECRUITER_TABLE." as r left join ".RECRUITER_LOGIN_TABLE." as rl on ( r.recruiter_id = rl.recruiter_id) left outer join ".COUNTRIES_TABLE." as c on (r.recruiter_country_id=c.id) left outer join ".ZONES_TABLE." as z on(r.recruiter_state_id=z.zone_id ) $whereClause ORDER BY  $field $order limit $lower,$higher   ";
 $result=tep_db_query($query);//echo "<br>$query";//exit;
 $x=tep_db_num_rows($result);//echo $x;exit;
 $pno= ceil($lower+$higher)/($higher);
 $link_array=array();
	if($x > 0 && $x1 > 0)
 {
  $alternate=1;
  $company_name1_old="";
  while($row =  tep_db_fetch_array($result))
  {
   $company_name1=strtoupper(substr($row["recruiter_company_name"],0,1));
    $company_logo=$row['recruiter_logo'];
   if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
    {
      $company_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=200");
    }else {
      $company_logo=defaultProfilePhotoUrl(tep_db_output($row['recruiter_company_name']),false,112,'class="" id=""');
    }

   $title="";
   $company_name="";
   if($company_name1!=$company_name1_old || $company_name1_old=='')
   {
    $title="<a class='text-white' id='".tep_db_output($company_name1)."'>".tep_db_output($company_name1)."</a>";
    $link_array[]=$company_name1;
   }

   $email_id=$row["recruiter_email_address"];
   $query_string1=encode_string("recruiter_email=".$email_id."=mail");

    $company_name="<a href='".getPermalink('company',array('seo_name'=>$row["recruiter_company_seo_name"])) ."'  class='blue'><span  class='jobs-by-company-title'>".tep_db_output($row['recruiter_company_name'])."</span></a> ";
  /* $company_name=tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post')
   .tep_draw_hidden_field('action','search')
   .tep_draw_hidden_field('company',$row["recruiter_company_seo_name"] ?? $row['recruiter_company_name']).
   '<button type="submit" class="jobs-by-company-title">'.tep_db_output($row['recruiter_company_name']).'</button></form></td>';*/
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $alternate++;

  //  company address
   $comp_address=($row['recruiter_city']!=''?$row['recruiter_city']:'');
   $comp_address.=' '.$row['recruiter_state'];
   $comp_address.=' '.$row['country_name'];
   $comp_address =trim($comp_address);

   // total jobs
   $com_total_jobs = count_jobs_for_company($row["recruiter_id"]);

   if ( $com_total_jobs > 0) {
    $jobs_total = ''.$com_total_jobs.'';
    //$jobs_total = '<a class="text-muted" href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string1='.$query_string1).'">'.$com_total_jobs.' Jobs</a>';
   }else{
    $jobs_total = '';
   }

   $template->assign_block_vars('company_list', array(
                                  'row_selected'   => $row_selected,
                                  'company_logo'   => $company_logo,
                                  'company_name'   => $company_name,
                                  'title'          => $title,
                                  'location'       => $comp_address ? '<i class="bi bi-geo-alt-fill me-1"></i> '. $comp_address : '',
                                  'total_jobs'     => $jobs_total,
                                ));
   $company_name1_old=$company_name1;
   $lower = $lower + 1;
  }
  see_page_number();
  $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAVE."  <span class=''>$x1</span> ".INFO_TEXT_COMPANY_IN_DIRECTORY));
 }
 else
  {
   $template->assign_vars(array('total'=>INFO_TEXT_NO_COMPANY_DIRECTORY));
  }
 tep_db_free_result($result);
 tep_db_free_result($result1);
 $header_link='<nav aria-label="Page navigation example" class="m-scroll"><ul class="pagination pagination-sm mb-4">';
 for($i='A';$i!='AA';$i++)
 {
  if(in_array($i,$link_array))
   $header_link.='<li class="page-item active"><a class="page-link" href="#'.$i.'" class="blue">'.$i.'</a></li>';
  //elseif($row=getAnyTableWhereData(RECRUITER_TABLE," recruiter_company_name like '".tep_db_input($i)."%'",'recruiter_id'))
  // $header_link.='<td align="center" class="style12"><a href="'.FILENAME_JOBSEEKER_COMPANY_PROFILE.'?directoru_char='.$i.'#'.$i.'" class="blue">'.$i.'</a></td>';
  else
   $header_link.='<li class="page-item page-link">'.$i.'</li>';
 }
	$header_link.='</ul></nav>';
// echo $header_link=substr($header_link,0,-7);
 //////////////////////////////////////////////////////////
 $template->assign_vars(array(
  'HEADING_TITLE'         => HEADING_TITLE,
  'form'                  => tep_draw_form('company_search', FILENAME_JOBSEEKER_COMPANY_PROFILE,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('company_name',''),
  'INFO_TEXT_HEADER_LINK' => $header_link,
  'hidden_fields'         => $hidden_fields,
  //'INFO_TEXT_MAIN'        => INFO_TEXT_MAIN,
  'LEFT_BOX_WIDTH'        => LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'       => RIGHT_BOX_WIDTH1,
  'HEADER_HTML'           => HEADER_HTML,
 'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
  'LEFT_HTML'=>LEFT_HTML,
	'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
  'RIGHT_HTML'            => RIGHT_HTML,
  'JOB_SEARCH_LEFT'       => JOB_SEARCH_LEFT,
  'update_message'=>$messageStack->output()));
  $template->pparse('company_profile');
}
?>