<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik#***********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
ini_set('max_execution_time','0');
include_once("../include_files.php");
$template->set_filenames(array('job_alert_template' => 'job_alert_template.htm'));

$separator='&nbsp;<font color="red">|</font>&nbsp;';
$tableNames=SEARCH_JOB_RESULT_TABLE." as sr,".JOBSEEKER_LOGIN_TABLE." as jl,".JOBSEEKER_TABLE." as j";
$whereClauses="sr.jobseeker_id=jl.jobseeker_id and sr.jobseeker_id=j.jobseeker_id and sr.job_alert='monthly'";
$fieldNames="sr.*,j.jobseeker_first_name,j.jobseeker_last_name,jl.jobseeker_email_address";
$query = "select $fieldNames from $tableNames where $whereClauses order by sr.jobseeker_id desc";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$temp_array=array();
while($row=tep_db_fetch_array($result))
{
 $job_alert_values='';
 $whereClause='';
 $whereClause1='';
 $keyword=tep_db_prepare_input($row['keyword']);
 $word1=$row['word1'];
 $location=$row['location'];
 $experience=$row['experience'];
 $Industry_Sector=$row['industry_sector'];
 $country=$row['country'];
 $state=tep_db_prepare_input($row['state']);
 $job_alert=tep_db_prepare_input($row['job_alert']);
 if(tep_not_null($keyword)) //   keyword starts //////
 {
  $job_alert_values.="<b>Keyword : </b>".tep_db_output($keyword);
  $whereClause1='( ';
  $search = array ("'[\s]+'");                    
  $replace = array (" ");
  $keyword = preg_replace($search, $replace, $keyword);
  if($word1=='Yes')
  {
   $explode_string=explode(' ',$keyword);
   for($i=0;$i<count($explode_string);$i++)
   {
    $whereClause1.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" j.job_description like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" r.recruiter_company_name like  '%".tep_db_input($explode_string[$i])."%' or ";
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
    if(tep_db_num_rows($temp_result) > 0)
    {
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
     }
     tep_db_free_result($temp_result);
    }
    $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($explode_string[$i])."%'");
    if(tep_db_num_rows($temp_result) > 0)
    {
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
     }
     tep_db_free_result($temp_result);
    }
   }
   $whereClause=substr($whereClause1,0,-4).' ) ';
   //echo $whereClause1;die();
  }
  else
  {
   $whereClause1.="j.job_title like '%".tep_db_input($keyword)."%' or ";
   $whereClause1.="j.job_state like '%".tep_db_input($keyword)."%' or  ";
   $whereClause1.="j.job_location like '%".tep_db_input($keyword)."%' or  ";
   $whereClause1.="j.job_short_description like '%".tep_db_input($keyword)."%' or ";
   $whereClause1.="j.job_description like '%".tep_db_input($keyword)."%' or ";
   $whereClause1.="r.recruiter_company_name like '%".tep_db_input($keyword)."%' or ";
   $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($keyword) . "%' or zone_code like '%" . tep_db_input($keyword) . "%')");
   if(tep_db_num_rows($temp_result) > 0)
   {
    while($temp_row = tep_db_fetch_array($temp_result))
    {
     $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
    }
    tep_db_free_result($temp_result);
   }
   $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($keyword)."%'");
   if(tep_db_num_rows($temp_result) > 0)
   {
    while($temp_row = tep_db_fetch_array($temp_result))
    {
     $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
    }
    tep_db_free_result($temp_result);
   }
   $whereClause1=substr($whereClause1,0,-4);
   $whereClause1.=" ) ";
   $whereClause.=$whereClause1;
  }
 }
 //   keyword ends //////
 //   location starts //////
 if(tep_not_null($location)) 
 {
  $whereClause1='(';
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Location : </b>".tep_db_output($location);
  $search = array ("'[\s]+'");                    
  $replace = array (" ");
  $location = preg_replace($search, $replace, $location);
  //if($word1=='Yes')
  //{
   $explode_string=explode(',',$location);
   $whereClause1.='( ';
   for($i=0;$i<count($explode_string);$i++)
   {
    if(!tep_not_null($explode_string[$i]))
    continue;
    if($i>0 &&  $explode_string[($i-1)]!='')
    $whereClause1.='or ( ';
    $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
    
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
    if(tep_db_num_rows($temp_result) > 0)
    {
     $whereClause1.=" (  ";
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
     }
     $whereClause1=substr($whereClause1,0,-4);
     $whereClause1.=" ) or ";
     tep_db_free_result($temp_result);
    }
    $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($explode_string[$i])."%'");
    if(tep_db_num_rows($temp_result) > 0)
    {
     $whereClause1.=" (  ";
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
     }
     $whereClause1=substr($whereClause1,0,-4);
     $whereClause1.=" ) or ";
     tep_db_free_result($temp_result);
    }
    
    $whereClause1=substr($whereClause1,0,-4);
    $whereClause1.=" ) ";
    tep_db_free_result($temp_result);
   }
  //}
  $whereClause1.=" )";   
  if($whereClause1!="((  )")
  {
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   $whereClause.=$whereClause1;
  }
 }
 //   location ends //////
 // experience starts ///
 if(tep_not_null($experience))
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $explode_string=explode("-",$experience);
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Experience : </b>".calculate_experience(trim($explode_string['0']),trim($explode_string['1']));
  $whereClause.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
 }
 // experience ends ///
 // industry sector starts ///
 if(tep_not_null($Industry_Sector) )
 {
  if($Industry_Sector=='0')
  {
   $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Industry sector :</b> All job category ";
  }
  else
  {
   $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Industry sector : </b>".get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name','id',$Industry_Sector);
  }
  if($Industry_Sector[0]!=0)
  {
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and  job_id in ( ':' job_id in ( ');
   $Industry_Sector=remove_child_job_category($Industry_Sector);
   $search_category1 =get_search_job_category($Industry_Sector);
   $now=date('Y-m-d');
   $prev_day=date("Y-m-d",mktime(0,0,0, date("m")-1, date("d"), date("Y")));
   $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv >='$prev_day' and j.re_adv <= '$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category1.")"; 
   $whereClause.=$whereClause_job_category;
   $whereClause.=" ) ";
  }
 }
 // industry sector ends ///
 // country starts ///
 if($country > 0)
 {
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Country : </b>".get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$country);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.job_country_id='".tep_db_input($country)."' )";
 }
 // country ends ///
 // company starts ///
 if(tep_not_null($company) )
 {
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Company : </b>".tep_db_output($company);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( r.recruiter_company_name='".tep_db_input($company)."' )";
 }
 // company ends ///
	// Job Alert starts ///
/* if(tep_not_null($job_alert) )
 {
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>Job Alert : </b>".tep_db_output($job_alert);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( sr.job_alert='".tep_db_input($job_alert)."' )";
 }*/
 // Job Alert ends ///
 // state starts ///
 if(tep_not_null($state))
 {
  $job_alert_values.=(tep_not_null($job_alert_values)?$separator:'')."<b>State : </b>".tep_db_output($state);
  $state1=explode(',',tep_db_input($state));//print_r($state1);exit;
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
  for($i=0;$i<count($state1);$i++)
  {
   $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" .trim(tep_db_input($state1[$i])) . "%' or zone_code like '%" . tep_db_input($state1[$i]) . "%')");
  // $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   $whereClause.=" j.job_state like '%".tep_db_input($state1[$i])."%' or ";
   if(tep_db_num_rows($temp_result) > 0)
   {
    while($temp_row = tep_db_fetch_array($temp_result))
    {
     $whereClause.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
    }
    tep_db_free_result($temp_result);
   }
  }
  $whereClause=substr($whereClause,0,-4);
  $whereClause.=" ) ";
 }

 if($whereClause=='')
  $whereClause ='(1)';
 if(tep_not_null($whereClause))
 {
  $now=date('Y-m-d');
  $prev_day=date("Y-m-d",mktime(0,0,0, date("m")-1, date("d"), date("Y")));
  $table_name=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
  $where_clause=$whereClause." and j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv >='$prev_day' and j.re_adv <= '$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00')";
  $field_name="r.recruiter_first_name,r.recruiter_last_name,rl.recruiter_email_address,r.recruiter_company_name,j.job_id, j.job_title, j.job_country_id, j.job_location, j.job_state, j.job_state_id, j.recruiter_id";
  $query1 = "select $field_name from $table_name where $where_clause";
  //echo "<br><br>$query1";//exit;
  $result1=tep_db_query($query1);
  $x=tep_db_num_rows($result1);
  //echo $x."<br>";//exit;
  $email_text='';
  if($x > 0)
  {
   $from_email_name=tep_db_output(SITE_OWNER);
   $from_email_address=tep_db_output(ADMIN_EMAIL);
   $to_name=tep_db_output($row['jobseeker_first_name']." ".$row['jobseeker_last_name']);
   $to_email_address=tep_db_output($row['jobseeker_email_address']);
   $email_subject= tep_db_output(SITE_TITLE." job alert");
   
   $logo='<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a>';

   $template->assign_vars(array(
    'logo'=>$logo,
    'jobseeker_name'=>$to_name,
    'job_alert_values'=> stripslashes($job_alert_values),
				'site_title'      => tep_db_output(SITE_TITLE),
				'site_link'       => '<a href="'.tep_href_link("").'">'.tep_db_output(SITE_TITLE).'</a>',
    ));
   $alternate_row=1;
   while($row1 = tep_db_fetch_array($result1))
   {
    $alternate=($alternate_row%2==0?'bgcolor="#e1e1e1" onMouseOver="this.style.background=\'#DDE4F8\'" onMouseOut="this.style.background=\'#e1e1e1\'"':'onMouseOver="this.style.background=\'#DDE4F8\'" onMouseOut="this.style.background=\'#ffffff\'"');
    $title_format=encode_category($row1['job_title']);
    $location =(tep_not_null($row1['job_location'])?tep_db_output($row1['job_location']).', ':'').(($row1['job_state_id'] > 0)?get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',tep_db_output($row1['job_state_id'])).", ":((tep_db_output($row1['job_state'])!='')?tep_db_output($row1['job_state']).", ":'')).get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',tep_db_output($row1['job_country_id']));
    
    $query_string=encode_string("job_id=".$row1['job_id']."=job_id");
    $template->assign_block_vars('job_alert_template', array( 'alternate' => $alternate,
     'location' => tep_db_output($location),
     'job_title' => '<a href="'.getPermalink('job',array('ide'=>$row1['job_id'],'seo_name'=>$title_format)).'">'.tep_db_output($row1['job_title']).'</a>',
     'company' => tep_db_output($row1['recruiter_company_name']),
     'apply_now_button' => '<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.tep_image_button(PATH_TO_BUTTON.'button_apply_now.gif', 'Apply now').'</a>',
     ));
    $alternate_row++;
   }
   $email_text=stripslashes($template->pparse1('job_alert_template'));
   //echo "From Name : ".$from_email_name.'<br>'. "From Email address : ".$from_email_address."<br>"."To Name : ".$to_name.'<br>'. "To email_address : ".$to_email_address.'<br>'. $email_subject.'<br>'. $email_text."<br>";
   tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
   //tep_mail('shambhu', 'kamal@erecruitmentsoftware.com', $email_subject, $email_text, $from_email_name, $from_email_address);
   $template = new Template(PATH_TO_TEMPLATE);
   $template->set_filenames(array('job_alert_template' => 'job_alert_template.htm'));
  }
  tep_db_free_result($result1);
 }
}
tep_db_free_result($result);
?>