<?
/*
************************************************************
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik		 #***********
**********#	Company			    : Aynsoft	Pvt. Ltd.    #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
ini_set('max_execution_time','0');
include_once("../include_files.php");
$template->set_filenames(array('resume_alert_template' => 'resume_alert_template.htm'));

$separator='&nbsp;<font color="red">|</font>&nbsp;';
$tableNames=SEARCH_RESUME_RESULT_TABLE." as sr,".RECRUITER_LOGIN_TABLE." as rl,".RECRUITER_TABLE." as r";
$whereClauses="sr.recruiter_id=rl.recruiter_id and sr.recruiter_id=r.recruiter_id";
$fieldNames="sr.*,r.recruiter_first_name,r.recruiter_last_name,rl.recruiter_email_address";
$query = "select $fieldNames from $tableNames where $whereClauses order by sr.recruiter_id desc";
$result=tep_db_query($query);
//echo "<br>$query";exit;
while($row=tep_db_fetch_array($result))
{
 $whereClause='';
 $whereClause1='';
 $keyword=$row['keyword'];
 $recruiter_id=$row['recruiter_id'];
 $word1=$row['word1'];
 $first_name=$row['first_name'];
 $last_name=$row['last_name'];
 $email_address=$row['email_address'];
 $country=$row['country'];
 $state=$row['state'];
 $city=$row['city'];
 $zip=$row['zip'];
 $industry_sector=$row['industry_sector'];
 $experience=$row['experience'];
 $minimum_rating=$row['minimum_rating'];
 $maximum_rating=$row['maximum_rating'];
 $resume_alert_values='';
 if(tep_not_null($keyword)) //   keyword starts //////
 {
  $resume_alert_values.="<b>Keyword : </b>".tep_db_output($keyword);
  $whereClause1='';
  $search = array ("'[\s]+'");                    
  $replace = array (" ");
  $keyword = preg_replace($search, $replace, $keyword);
  if($word1=='Yes')
  {
   $explode_string=explode(' ',$keyword);
  }
  else
  {
   $explode_string=array('0'=>$keyword);
  }
  $whereClause1.='( ';
  for($i=0;$i<count($explode_string);$i++)
  {
   $whereClause1.=" jl.jobseeker_email_address like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_first_name like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_last_name like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_address1 like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_address2 like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_city like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" j.jobseeker_zip like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" jr2.description  like '%".tep_db_input($explode_string[$i])."%' or ";
   $whereClause1.=" jr3.related_info like '%".tep_db_input($explode_string[$i])."%' or ";
   $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
   if(tep_db_num_rows($temp_result) > 0)
   {
    $whereClause1.=" (  ";
    while($temp_row = tep_db_fetch_array($temp_result))
    {
     $whereClause1.=" j.jobseeker_state_id ='".$temp_row['zone_id']."' or ";
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
     $whereClause1.=" j.jobseeker_country_id ='".$temp_row['id']."' or ";
    }
    $whereClause1=substr($whereClause1,0,-4);
    $whereClause1.=" ) or ";
    tep_db_free_result($temp_result);
   }
   $now=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d"), date("Y")));
   $prev_day=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d")-1, date("Y")));
   $temp_result=tep_db_query("select jr1.resume_id ,jr1.jobseeker_resume from ".JOBSEEKER_RESUME1_TABLE." as jr1  where jr1.search_status='Yes' and jr1.inserted <='$now' && jr1.inserted >'$prev_day'");
   if(tep_db_num_rows($temp_result) > 0)
   {
    $whereClause2="";
    while($temp_row = tep_db_fetch_array($temp_result))
    {
     $resume_id=$temp_row["resume_id"];
     $file_type=substr($temp_row['jobseeker_resume'],-3,3);
     if($file_type=='txt' || $file_type=='doc' ||$file_type=='pdf')
     {
      $lines = @file(PATH_TO_MAIN_PHYSICAL_RESUME.$temp_row['jobseeker_resume']);
      if(count($lines) > 1)
      {
       foreach ($lines as $line_num => $line) 
       {
        if(preg_match ("/$keyword/i", $line))
        {
         $whereClause2.=" jr1.resume_id='$resume_id' or ";
         break;
        }
       }
      }
     }
    }
    $whereClause2=substr($whereClause2,0,-4);
    if(tep_not_null($whereClause2))
    $whereClause1.=" ( ".$whereClause2." ) or ";
    tep_db_free_result($temp_result);
   }//*/
  }

  $whereClause1=substr($whereClause1,0,-4);
  $whereClause1.=" ) ";
  $whereClause.=$whereClause1;
 }

 //   keyword ends //////
 // minimum rating starts ///
 if(tep_not_null($minimum_rating))
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" (( jrt1.point >= '".tep_db_input($minimum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$recruiter_id."'  and  jrt.point >= '".tep_db_input($minimum_rating)."')) ";
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Minimum Rating : </b>".tep_db_output($minimum_rating);
 }
 // minimum rating ends ///
 // maximum rating starts ///
 if(tep_not_null($maximum_rating))
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" (( jrt1.point <= '".tep_db_input($maximum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$recruiter_id."'  and  jrt.point <= '".tep_db_input($maximum_rating)."')) ";
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Maximum Rating : </b>".tep_db_output($maximum_rating);
 }
// maximum rating ends ///
 // first name starts ///
 if(tep_not_null($first_name))
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.jobseeker_first_name like '%".tep_db_input($first_name)."%' ) ";
 }
 // first name ends ///
 // last name starts ///
 if(tep_not_null($last_name))
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.jobseeker_last_name like '%".tep_db_input($last_name)."%' ) ";
 }
 //last name ends ///
 if(tep_not_null($first_name) || tep_not_null($last_name))
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Name : </b>".tep_db_output($first_name.' '.$last_name);
 }
 // email_address starts ///
 if(tep_not_null($email_address))
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Email-address : </b>".tep_db_output($email_address);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( jl.jobseeker_email_address like '%".tep_db_input($email_address)."%' ) ";
 }
 // email_address ends ///
 // country starts ///
 if((int)$country>0)
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Country : </b>".get_name_from_table(COUNTRIES_TABLE,'country_name','id',$country);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.jobseeker_country_id ='".(int)tep_db_input($country)."' ) ";
 }
 // country ends ///
 // state starts ///
 if(tep_not_null($state))
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>State : </b>".tep_db_output($state);
  $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and (':'(');
  $whereClause.=" ( j.jobseeker_state like '%".tep_db_input($state)."%' ) ";
  if(tep_db_num_rows($temp_result) > 0)
  {
   $whereClause.=' or ( ';
   while($temp_row = tep_db_fetch_array($temp_result))
   {
    $whereClause.=" j.jobseeker_state_id ='".$temp_row['zone_id']."' or ";
   }
   $whereClause=substr($whereClause,0,-4);
   $whereClause.=" )";
   tep_db_free_result($temp_result);
  }
  $whereClause.=" )";
 }
 // state ends ///
 // city starts ///
 if(tep_not_null($city))
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>City : </b>".tep_db_output($city);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.jobseeker_city like '%".tep_db_input($city)."%' ) ";
 }
 //city ends ///
 // zip starts ///
 if(tep_not_null($zip))
 {
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Zip : </b>".tep_db_output($zip);
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause.=" ( j.jobseeker_zip like '%".tep_db_input($zip)."%' ) ";
 }
 //zip ends ///
 // industry sector starts ///
 if(tep_not_null($industry_sector))
 {
  if($industry_sector['0']=='0')
   $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Job Category : </b>All Job Categories";
  else
   $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Job Category : </b>".get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name','id',$industry_sector);

  if($industry_sector['0']!='0')
  {
   $industry_sector1=remove_child_job_category($industry_sector);
   $search_category1 =get_search_job_category($industry_sector1);
   $now=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d"), date("Y")));
   $prev_day=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d")-1, date("Y")));
   $whereClause_job_category=" select distinct (jr.resume_id) from ".JOBSEEKER_RESUME1_TABLE."  as jr1  left join ".RESUME_JOB_CATEGORY_TABLE." as jr on(jr1.resume_id=jr.resume_id ) where jr1.search_status='Yes' and jr1.inserted <='$now' && jr1.inserted >'$prev_day' and  jr.job_category_id in (".$search_category1.")"; 
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and jr1.resume_id in ( ':' jr1.resume_id in ( ');
   $whereClause.=$whereClause_job_category;
   $whereClause.=" ) ";
  }
 }
 ////////////////
 // work experience start ///
 if(tep_not_null($experience)) 
 {
  $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $explode_string=explode("-",$experience);
  $resume_alert_values.=(tep_not_null($resume_alert_values)?$separator:'')."<b>Experience : </b>".calculate_experience($explode_string[0],$explode_string[1]);
  $work_experince=get_name_from_table(EXPERIENCE_TABLE,'id', 'min_experience',tep_db_input($explode_string[0]));
  $whereClause.=" ( jr1.work_experince = '".(int)tep_db_input($work_experince)."' ) ";
 }
 // work experience ends ///

 if($whereClause=='')
 $whereClause ='(1)';
 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
///*/
 if(tep_not_null($whereClause))
 {
  $now=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d"), date("Y")));
  $prev_day=date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d")-1, date("Y")));
  $field_names="jl.jobseeker_id,jr1.resume_id,jl.inserted,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_last_name,j.jobseeker_privacy";
  if(tep_not_null($keyword))
   $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id)   join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left  join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left  join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$recruiter_id."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
  else
   $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id)   join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$recruiter_id."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')";
  
  $whereClause.=" jr1.search_status='Yes' and jr1.inserted <='$now' && jr1.inserted >'$prev_day'";
  $query2 = "select distinct(jr1.resume_id) from $table_names1 where $whereClause ";
  $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id)  ";
  $where_clause=" jr1.search_status='Yes' and jr1.inserted <='$now' && jr1.inserted >'$prev_day' and resume_id in (".$query2.")";

  $query1 = "select $field_names from $table_names where $where_clause";
  $result1=tep_db_query($query1);
  //echo "<br>$query1";//exit;
  $x=tep_db_num_rows($result1);
  //echo $x;//exit;
  $email_text='';
  if($x > 0)
  {
   $from_email_name=tep_db_output(SITE_OWNER);
   $from_email_address=tep_db_output(ADMIN_EMAIL);
   $to_name=tep_db_output($row['recruiter_first_name']." ".$row['recruiter_last_name']);
   $to_email_address=tep_db_output($row['recruiter_email_address']);
   $email_subject= tep_db_output(SITE_TITLE." resume alert");
   
   $logo='<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a>';
   $template->assign_vars(array(
    'logo'=>$logo,
    'recruiter_name'=>$to_name,
    'resume_alert_values'=>stripslashes($resume_alert_values),
    ));
   $alternate_row=1;
   while($row1 = tep_db_fetch_array($result1))
   {
    $alternate=($alternate_row%2==0?'bgcolor="#e1e1e1" onMouseOver="this.style.background=\'#DDE4F8\'" onMouseOut="this.style.background=\'#e1e1e1\'"':'onMouseOver="this.style.background=\'#DDE4F8\'" onMouseOut="this.style.background=\'#ffffff\'"');
    $privacy=($row1['jobseeker_privacy']==1?true:false);
    $hidden=MESSAGE_JOBSEEKER_PRIVACY;
    $query_string2=encode_string("resume_id=".$row1['resume_id']."=resume_id");
    $template->assign_block_vars('resume_alert_template', array( 'alternate' => $alternate,
     'name' => tep_db_output($row1['jobseeker_first_name'].' '.$row1['jobseeker_last_name']),
     'email_address' => ($privacy?$hidden:tep_db_output($row1['jobseeker_email_address'])),
     'view' => '<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string2='.$query_string2).'">View</a>',
     ));
    $alternate_row++;
   }
   $email_text=stripslashes($template->pparse1('resume_alert_template'));
   ///echo "From Name : ".$from_email_name.'<br>'. "From Email address : ".$from_email_address."<br>"."To Name : ".$to_name.'<br>'. "To email_address : ".$to_email_address.'<br>'. $email_subject.'<br>'. $email_text."<br>";
   tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
   //tep_mail('shambhu@ejobsitesoftware.com', $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
   $template = new Template(PATH_TO_TEMPLATE);
   $template->set_filenames(array('resume_alert_template' => 'resume_alert_template.htm'));
  }
  tep_db_free_result($result1);
 }
}
tep_db_free_result($result);
?>