<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/app_functions.php");
$access_key  = tep_db_prepare_input($_POST['access_key']);
 
if($recruiter_id =get_access_user($access_key,'recruiter'))
{
  $fielsd='rl.recruiter_id,rl.recruiter_email_address,r.recruiter_first_name,r.recruiter_last_name';
 if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl left outer join  '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)   '," rl.recruiter_id ='".$recruiter_id."'",$fielsd))
 {
   $error             = false;
   $job_title         = tep_db_prepare_input($_POST['job_title']);
   $reference         = tep_db_prepare_input($_POST['job_reference']);
   $job_country       = tep_db_prepare_input($_POST['country']);
   $state             = tep_db_prepare_input($_POST['state']);
   $job_location      = tep_db_prepare_input($_POST['location']);
   $job_salary        = tep_db_prepare_input($_POST['salary']);
   $job_industry      = tep_db_prepare_input($_POST['job_industry']);
   $short_description = tep_db_prepare_input($_POST['job_short_description']);
   $description       = tep_db_prepare_input($_POST['job_description']);
   $job_experience    =  (int) tep_db_prepare_input($_POST['job_experience']);
   $job_duration      = tep_db_prepare_input($_POST['job_duration']);
   $job_type          = tep_db_prepare_input($_POST['job_type']);
   $skills            = tep_db_prepare_input($_POST['skills']);
   $skills = preg_replace("'[\s]+'", " ", $skills);
   $skills = str_replace(array(", "," ,"),array( ",",","), $skills);
   if(!($job_duration>0 && $job_duration<=INFO_TEXT_MAX_JOB_DURATION))
   $job_duration=INFO_TEXT_MAX_JOB_DURATION;
    
   $job_industry_array=explode(',',$job_industry);
   $job_industry_ids=array();
   $total_industry=count($job_industry_array);
   for($j=0;$j<$total_industry;$j++)
    {
     if($row=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id ='".tep_db_input($job_industry_array[$j])."'",'id'))
     {
      $job_industry_ids[]=$row['id'];
     }
    }
    if(!count($job_industry_ids))
    {
     $job_industry_ids=0;
    }
    $errorMsg=array();
	if(strlen($job_title)<=0)
   {
    $error=true;
    $errorMsg[] ='job title empty.';
   }
   elseif($row=getAnyTableWhereData(JOB_TABLE,"job_title ='".tep_db_input($job_title)."' and recruiter_id='".$recruiter_id."'"))
   {
    $error=true;
    $errorMsg[] ='job title already exists.';
   }
   if(!is_numeric(job_country))
   {
    if(!$row=getAnyTableWhereData(COUNTRIES_TABLE,"country_name ='".tep_db_input($job_country)."'",'id'))
    {
     $error=true;
     $errorMsg[] ='job country invalid.';
    }
    else
     $job_country=$row['id'];
   }
   else
   {
    if(!$row=getAnyTableWhereData(COUNTRIES_TABLE,"id ='".tep_db_input($job_country)."'",'id'))
    {
     $error=true;
     $errorMsg[] ='job country invalid.';
    }
    else
     $job_country=$row['id'];   
   }

   if($row=getAnyTableWhereData(ZONES_TABLE,"zone_name ='".tep_db_input($state)."'",'zone_id'))
   {
    $job_state='null';
    $job_state_id=$row['zone_id'];
   }
   else
   {
    $job_state=$state;
    $job_state_id=0;
   }
    $job_experience  =  (int)$job_experience;
    $row=getAnyTableWhereData(EXPERIENCE_TABLE,"min_experience <='".tep_db_input($job_experience)."'  order by min_experience  desc limit 0,1",'min_experience,max_experience');
    $min_experience=$row['min_experience'];
    $max_experience=$row['max_experience'];
    
   if(strlen($short_description)<=0)
   {
    $error=true;
    $errorMsg[] ='job short description empty.';
   }
   if(strlen($description)<=0)
   {
    $error=true;
    $errorMsg[] ='job description empty.';
   }

   if(!$error &&  tep_not_null(!$recruiter_id))
   {
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
    $obj_account=new recruiter_accounts($recruiter_id,'job_post');
    if(!($obj_account->allocated_amount['job']=='Unlimited' || $obj_account->allocated_amount['job'] >= $obj_account->enjoyed_amount['job']+1))
    {
     $error=true;
     $errorMsg[] ='Subscription Error.';
    }
   }
   if($row=getAnyTableWhereData(JOB_TYPE_TABLE,"type_name ='".tep_db_input($job_type)."'",'id'))
    $job_type_ids =$row['id'];
    else
     $job_type_ids = '';

   ////////////////////////////////////
   if(!$error)
   {
	$sql_data_array=array();
	$today=date('Y-m-d',mktime(0,0,0,date("m"),date("d"),date("Y")));
	$adv_date=date('Y-m-d');
	$expired=date('Y-m-d',mktime(0,0,0,date("m"),date("d")+$job_duration,date("Y")));
	$sql_data_array=array('job_title'     => $job_title,
						  'job_reference' => $job_reference,
						  'job_country_id'=> $job_country,
						  'job_state'     => $job_state,
						  'job_state_id'  => $job_state_id,
						  'job_location'  => $job_location,
						  'job_salary'    => $job_salary,
		                  'job_skills'=> $skills,
						  'job_short_description'=>$short_description,
						  'job_description'=> $description,
						  'job_type'      => $job_type_ids,
						  'min_experience'=> $min_experience,     
						  'max_experience'=> $max_experience,     
						  'job_vacancy_period'=> $max_experience,     
						  're_adv'        => $adv_date,     
						  'expired'       => $expired,     
						  'inserted'      => $today,     
						  'recruiter_user_id'=> 'null',     
						  'job_vacancy_period'=> $job_duration,     
						  'recruiter_id'  => $recruiter_id,     
						  );
    tep_db_perform(JOB_TABLE, $sql_data_array);
    ////////////////////////////
   	$row_check=getAnyTableWhereData(JOB_TABLE,"recruiter_id='".$recruiter_id."' and job_title='".tep_db_input($job_title)."' order by job_id desc limit 0,1",'job_id');
    $job_id=$row_check['job_id'];
    /////////////////////////////////////
    $sql_job_array=array('job_id'=>$job_id);
    for($j=0;$j<count($job_industry_ids);$j++)
    {
     if(!$job_row = getAnyTableWhereData(JOB_JOB_CATEGORY_TABLE, "job_id = '" . tep_db_input($job_id) . "' and job_category_id='".$job_industry_ids[$j]."'", "job_category_id"))
     {
      $sql_job_array['job_category_id']=$job_industry_ids[$j];
       tep_db_perform(JOB_JOB_CATEGORY_TABLE,$sql_job_array);
     }					
    }
    /////////////////////////////////////////////
    $sql_data_array_new=array();
    $sql_data_array_new['display_id']=get_job_enquiry_code($job_id);
    tep_db_perform(JOB_TABLE, $sql_data_array_new, 'update', "job_id = '" . $job_id . "'");
    if(tep_not_null($skills))
	 insertSkillTag($skills);

    // find last id //
    $now=date("Y-m-d");
    if(!tep_not_null($recruiter_id1))
	{
     $row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and plan_for='job_post' and start_date <= '$now' and end_date >='$now'","id,job_enjoyed");
     $sql_data_array=array('job_enjoyed'=>1+$row['job_enjoyed']);
     tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array, 'update', "id = '" . $row['id'] . "'");
	}
	$title_format=encode_category($job_title);
	$job_link = tep_href_link($job_id.'/'.$title_format.'.html');

    header('Content-Type: text/xml'); 
    $message='<job>'."\n";
    $message .='<status>success</status>'."\n";
    $message .='<job_id>'.$job_id.'</job_id>'."\n";
    $message .='<url>'.$job_link.'</url>'."\n";
    $message.='</job>'; 	
    echo $message;
	die();

   }
   if($error)
   {
    header('Content-Type: text/xml'); 
    $message='<error>'."\n";
    $message .='<status>error</status>'."\n";
	if(is_array($errorMsg))
    $message .='<message>'.implode("\n",$errorMsg).'</message>'."\n";
    $message.='</error>'; 	
    echo $message;
	die();
   }
   
   /////////////////////////////
	

 }
}
else
{
 header('Content-Type: text/xml'); 
 $message='<error>'."\n";
 $message .='<status>error</status>'."\n";
 $message .='<message>Invalid Authentication</message>'."\n";
 $message.='</error>'; 	
 echo $message;
}
?>