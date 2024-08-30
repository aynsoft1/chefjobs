<?
/*********************************************************
**********#	Name				  : Shambhu Prasad Patnaik		   #**********
**********#	Company			: Aynsoft							         #**********
**********#	Copyright (c) www.aynsoft.com 2004	#**********
*********************************************************/
////
function get_resume_weight($resume_id,$job_id=0,$detail_view=false)
{
 $job_id    =(int)$job_id;
 $resume_id =(int)$resume_id;
 if(!$row_job=getAnyTableWhereData(JOB_TABLE." as j  left outer join ".ZONES_TABLE." as z on (z.zone_id=j.job_state_id)"," job_id='".$job_id."'","job_country_id ,if(j.job_state_id,z.".TEXT_LANGUAGE."zone_name,j.job_state) as job_state,job_location,job_type,j.min_experience,j.max_experience"))
  return 0;
 if(!$row_resume=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE." as  jr1 left outer join ".JOBSEEKER_TABLE." as j on (j.jobseeker_id=jr1.jobseeker_id)  left outer join ".EXPERIENCE_TABLE." as e on (e.id=jr1.work_experince) left outer join ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) "," resume_id='".$resume_id."'","j.jobseeker_country_id,if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,j.jobseeker_state) as jobseeker_state,j.jobseeker_city,jr1.relocate,jr1.job_type_id,e.experience_weight "))
  return 0;
 //print_r($row_job);
 //print_r($row_resume);
 if($rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='".$job_id."'"))
 {
 }
 else
  $rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='0'");
 $location_weight = (int) tep_db_output($rows['location']);
 $industry_weight = (int) tep_db_output($rows['industry']);
 $job_type_weight   = (int) tep_db_output($rows['job_type']);
 $general_xp_weight = (int) tep_db_output($rows['experience']);
 $total_percentage=0;
 $match_weight_location=$match_weight_job_type=$match_weight_job_category=$match_weight_experience=0;
 $match_result=array('location'=>0,'industry'=>0,'job_type'=>0,'experience'=>0);
 ////Location //////////
 if($location_weight>0)
 {
  $match_weight_location=get_match_percentage_location($row_job['job_country_id'],strtolower($row_job['job_state']),strtolower($row_job['job_location']),$row_resume['jobseeker_country_id'],strtolower($row_resume['jobseeker_state']),strtolower($row_resume['jobseeker_city']),$row_resume['relocate']);
  $match_weight_location=($match_weight_location*$location_weight)/100;
  $match_result['location']=$match_weight_location;
 }

 ///JOB TYPE////////////////////////////////////////////////////
 if($job_type_weight>0)
 {
  $match_weight_job_type=get_match_percentage_job_type($row_job['job_type'],$row_resume['job_type_id']);
  $match_weight_job_type=($match_weight_job_type*$job_type_weight)/100;
  $match_result['job_type']=$match_weight_job_type;
 }
 
 /////////////JOB_CATEGORY//////////////////////////////////////////
 if($industry_weight>0)
 {
  $match_weight_job_category=get_match_percentage_industry($job_id,$resume_id);
  $match_weight_job_category=($match_weight_job_category *$industry_weight)/100; 
  $match_result['industry']=$match_weight_job_category;
 }
 
 ////////EXPERIENCE//////////////////////////////////////////////////////////////
 if($general_xp_weight>0)
 {
  $row_get_exp=getAnyTableWhereData(EXPERIENCE_TABLE," min_experience='".$row_job['min_experience']."' && max_experience='".$row_job['max_experience']."' ","experience_weight");
  $match_weight_experience=get_match_percentage_experience((int)$row_get_exp['experience_weight'],(int)$row_resume['experience_weight']);
  $match_weight_experience=($match_weight_experience*$general_xp_weight)/100; 
  $match_result['experience']=$match_weight_experience;
 } 
 $total_percentage=$match_weight_location+$match_weight_job_type+$match_weight_job_category+$match_weight_experience;
 $match_result['total']=$total_percentage;
 if($detail_view)
  return $match_result;
 else
 return $total_percentage;
}
///////////////////////////////////////////////////////////
function get_match_percentage_job_type($recruiter_job_type,$jobseeker_job_type)
{
 $match_weight_job_type=0;
 if(tep_not_null($recruiter_job_type))
 {
  if($recruiter_job_type==0)
  $match_weight_job_type=100;
  else
  {
   $recruiter_job_type=explode(',',$recruiter_job_type);
   if(tep_not_null($jobseeker_job_type))
   {
    $jobseeker_job_type=explode(',',$jobseeker_job_type);
    if($result = array_intersect($recruiter_job_type, $jobseeker_job_type))
     $match_weight_job_type=100;
    else
     $match_weight_job_type=0;
   }
   else
    $match_weight_job_type=0;
  }
 }
 else
 {
  $match_weight_job_type=100;
 }
 return $match_weight_job_type;
}


////////////////////////////////////////////////////////////
function get_match_percentage_industry($job_id,$resume_id)
{
 $job_cat_query  = "select  job_category_id  from ".JOB_JOB_CATEGORY_TABLE." as jjc  where jjc.job_id = '" . tep_db_input($job_id) . "'";
 $job_cat_result =tep_db_query($job_cat_query);
 $recruiter_job_category=array();
 if(tep_db_num_rows($job_cat_result)>0)
 {
  while($recruiter_job_cat=tep_db_fetch_array($job_cat_result))
  {
   $recruiter_job_category[]=$recruiter_job_cat['job_category_id'];
  }
 }
 tep_db_free_result($job_cat_result);
 /////////////////////////////////////////////////
 $job_cat_query1  = "select job_category_id from ".RESUME_JOB_CATEGORY_TABLE." as jrc  where jrc.resume_id = '" . tep_db_input($resume_id) . "'";
 $job_cat_result1 =tep_db_query($job_cat_query1);
 $jobseeker_job_category=array();
 if(tep_db_num_rows($job_cat_result1)>0)
 {
  while($jobseeker_job_cat=tep_db_fetch_array($job_cat_result1))
  {
   $jobseeker_job_category[]=$jobseeker_job_cat['job_category_id'];
  }
 }
 tep_db_free_result($job_cat_result1);
 $match_weight_job_category=0;
 if(!$result = array_intersect($recruiter_job_category, $jobseeker_job_category))
 {
  $match_weight_job_category=0; 
 }
 else
 {
  $match_weight_job_category=100;
 }
 return $match_weight_job_category;
}
////////////////////////////////////////////////////////////////////////
function get_match_percentage_experience($recruiter_experience_weight,$jobseeker_experience_weight)
{
 $match_weight=0;
 if($recruiter_experience_weight>0)
 {
  if($jobseeker_experience_weight==0)
   $match_weight=0;
  elseif($recruiter_experience_weight<=$jobseeker_experience_weight)
  {
   $match_weight=100;
  }
  else
  {
   $weight_diff=$recruiter_experience_weight-$jobseeker_experience_weight;
   if($weight_diff==1)
   $match_weight=75;
   elseif($weight_diff==2)
   $match_weight=25;
  }
 }
 else
   $match_weight=100;
 return $match_weight;
}
////////////////////////////////////////////////////////////////////////////////////
function get_match_percentage_education($recruiter_education,$resume_id)
{
 $match_weight=0;
 if(tep_not_null($recruiter_education))
 {
  if($recruiter_value=getAnyTableWhereData(EDUCATION_LEVEL_TABLE," id in (".$recruiter_education.") ",'  min(education_weight) as r_education_weight'))
  {
   $recruiter_education_weight=$recruiter_value['r_education_weight'];
   $jobseeker_education=get_name_from_table(JOBSEEKER_RESUME3_TABLE,'degree', 'resume_id',$resume_id);
   if(!tep_not_null($jobseeker_education))
    $jobseeker_education=0;
   $jobseeker_value=getAnyTableWhereData(EDUCATION_LEVEL_TABLE," id in (".$jobseeker_education.") ",'max(education_weight) as j_education_weight');
   $jobseeker_education_weight=(int)$jobseeker_value['j_education_weight'];
   if($jobseeker_education_weight>=$recruiter_education_weight)
   $match_weight=100;  
  }
 }
 return $match_weight;
}
//////////////////////////////////////////
function get_match_percentage_location($job_country,$job_state,$job_location,$jobseeker_country,$jobseeker_state,$jobseeker_city,$jobseeker_relocate)
{
 $match_weight=0;
 if($job_country>0)
 {
  if($job_country==$jobseeker_country)
  {
   if($job_state==$jobseeker_state || $job_state=='')
   {//20+40
    if($job_location==$jobseeker_city|| $job_location=='')//20+40+40
     $match_weight=100;
    elseif($jobseeker_relocate=='Yes')
     $match_weight=80;
    else
     $match_weight=60;    
   }
   elseif($jobseeker_relocate=='Yes')
    $match_weight=40;    
   else
    $match_weight=20;
  }
  elseif($jobseeker_relocate=='Yes')
   $match_weight=10;
 }
 else
 {
  $match_weight=100;
 }
 return $match_weight;
}
?>