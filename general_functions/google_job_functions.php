<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2018  #**********
**********************************************************/
if (!function_exists('getGoogleJobType')) :
function getGoogleJobType($jobtype='')
{
  $job_type_array = array('1'=>'FULL_TIME' ,'2'=>'PART_TIME' ,'3'=>'CONTRACTOR' ,'4'=>'Permanant' ,'5'=>'TEMPORARY' ,'6'=>'TEMPORARY','7'=>'INTERN' );
  if($jobtype!='')
  $jobtype=explode(',',$jobtype);
  if(is_array($jobtype))
  {
   $type1=array();
   foreach($jobtype as $type)
   {
    if(array_key_exists($type,$job_type_array))
    $type1[]=$job_type_array[$type];
   }
  }
  elseif(array_key_exists($job_type,$job_type_array))
  $type1 =$job_type_array[$job_type];
  else
  $type1   = '';
 return $type1;
}
endif;
if (!function_exists('getGoogleJobLocation')) :
function getGoogleJobLocation($loc='')
{
 $loc1=array();
 $add1=array();
 $loc1['@type']='Place';

 if($loc['city']!='' ||$loc['state']!=''|| $loc['country_code']!='' )
 {
  $add1['@type']='PostalAddress';
 }
 if(isset($loc['city']) && tep_not_null($loc['city']))
 $add1['addressLocality']=$loc['city'];

 if(isset($loc['state']) && tep_not_null($loc['state']))
 $add1['addressRegion']=$loc['state'];

 if(isset($loc['country_code']))
 $add1['addressCountry']=$loc['country_code'];



 $loc1['address']=$add1;
 $location['jobLocation']=  $loc1;
 return $location['jobLocation'];
}
endif;
if (!function_exists('getGoogleJobCompany')) :
function getGoogleJobCompany($company='')
{
 if($company=='')
 return '';

 $comp =array();
 $comp['@type']='Organization';

 if(isset($company['company']) && tep_not_null($company['company']))
 $comp['name']=$company['company'];

 if(isset($company['website']) && tep_not_null($company['website']))
 $comp['sameAs']=$company['website'];

 if(isset($company['logo']) && tep_not_null($company['logo']))
 $comp['logo']=$company['logo'];
 return $location['hiringOrganization']=  $comp;
}
endif;
if (!function_exists('getJobData')) :
function getJobData()
{
 global $jobDetailData;
 $jobDetailData=false;
 $now=date('Y-m-d H:i:s');
 $job_id       = tep_db_prepare_input($_GET['query_string']);
 $table_names  = JOB_TABLE." as j left outer join  ".RECRUITER_TABLE." as r on (j.recruiter_id=r.recruiter_id ) left outer join  ".RECRUITER_LOGIN_TABLE." as rl on (rl.recruiter_id=r.recruiter_id ) left outer join  ".COUNTRIES_TABLE." as c on (c.id = j.job_country_id) left outer join ".INDEED_JOB_TABLE." as dd on (j.job_id=dd.job_id) left outer join ".ZIP_RECRUITER_JOB_TABLE." as zz on (j.job_id=zz.job_id)";
 $where_clause = " j.recruiter_id=r.recruiter_id and r.recruiter_id=rl.recruiter_id and rl.recruiter_status='Yes' and j.job_id='".tep_db_input($job_id)."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
 $field_names  = "j.job_id,r.recruiter_company_name,r.recruiter_featured,r.recruiter_logo,r.recruiter_url,j.job_title,j.job_reference,j.min_experience,j.max_experience,j.job_location,j.job_state,j.job_state_id,j.job_country_id,re_adv,expired,j.job_salary,j.job_allowance,j.job_industry_sector,j.job_type,j.min_experience,j.max_experience,j.job_description,dd.indeed_id,dd.indeed_url,j.post_url,j.url,job_short_description,j.latitude,j.longitude,j.job_skills,c.country_name,c.country_code,zz.zr_id,zz.zr_url,j.job_source";//


 if($row=getAnyTableWhereData($table_names,$where_clause,$field_names))
 {
  $jobDetailData['job_id']                 = $row['job_id'];
  $jobDetailData['job_source']             = $row['job_source'];
  $jobDetailData['job_title']              = $row['job_title'];
  $jobDetailData['job_reference']          = $row['job_reference'];
  $jobDetailData['job_skills']             = $row['job_skills'];
  $jobDetailData['job_salary']             = $row['job_salary'];
  $jobDetailData['job_allowance']          = $row['job_allowance'];
  $jobDetailData['job_type']               = $row['job_type'];
  $jobDetailData['min_experience']         = $row['min_experience'];
  $jobDetailData['max_experience']         = $row['max_experience'];
  $jobDetailData['job_short_description']  = $row['job_short_description'];
  $jobDetailData['job_description']        = $row['job_description'];
  $jobDetailData['post_url']               = $row['post_url'];
  $jobDetailData['url']                    = $row['url'];
  $jobDetailData['recruiter_featured']     = $row['recruiter_featured'];

  $jobDetailData['job_country']            = $row['country_name'];
  $jobDetailData['job_country_code']       = $row['country_code'];
  $jobDetailData['job_state_id']           = $row['job_state_id'];
  $jobDetailData['job_state']              = $row['job_state'];
  $jobDetailData['job_location']           = $row['job_location'];
  $jobDetailData['latitude']               = $row['latitude'];
  $jobDetailData['longitude']              = $row['longitude'];

  if( $row['job_source']=='jobsite')
  {
   $jobDetailData['recruiter_company_name'] = $row['recruiter_company_name'];
   if(tep_not_null($row['recruiter_logo']) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$row['recruiter_logo']))
   $jobDetailData['recruiter_logo']         = tep_href_link(PATH_TO_LOGO.$row['recruiter_logo']);
   else
   $jobDetailData['recruiter_logo']         = "";

  }
  $jobDetailData['job_url']                 = getPermalink('job',array('ide'=>$job_id,'seo_name'=>encode_category($row['job_title'])));
  $jobDetailData['recruiter_url']           = $row['recruiter_url'];



  $jobDetailData['indeed_id']              = $row['indeed_id'];
  $jobDetailData['indeed_url']             = $row['indeed_url'];
  //$jobDetailData['simplyhired_id']         = $row['simplyhired_id'];
  //$jobDetailData['simplyhired_url']        = $row['simplyhired_url'];
  $jobDetailData['zr_id']                  = $row['zr_id'];
  $jobDetailData['zr_url']                 = $row['zr_url'];

  $jobDetailData['updated']                = $row['updated'];
  $jobDetailData['re_adv']                 = $row['re_adv'];
  $jobDetailData['expired']                = $row['expired'];
 }
}
endif;
if (!function_exists('getGoogleJobData')) :
function getGoogleJobData()
{
 getJobData();
global $jobDetailData;
$googleData='';
$google_s='';
if($jobDetailData['job_source']=='jobsite')
{
///*
// <script type="application/ld+json">
 $googleData=array();
 $googleData['@context']='https://schema.org';
 $googleData['@type']='JobPosting';
 $googleData['baseSalary'] = array('@type'=>'MonetaryAmount','value'=>$jobDetailData['job_salary']);
 $googleData['datePosted'] = formate_date1($jobDetailData['re_adv'],'%Y-%m-%d ');
 $googleData['description']= tep_db_output($jobDetailData['job_description']);
 $googleData['disambiguatingDescription']= htmlentities($jobDetailData['job_short_description']);
 if(tep_not_null($jobDetailData['job_type']))
 {
  $gj_type=get_name_from_table(JOB_TYPE_TABLE,'type_name', 'id', $jobDetailData['job_type']);
  $gj_type=str_replace(", ",",",$gj_type);
  $gj_type=str_replace(" ","_",$gj_type);
  $googleData['employmentType']=explode(",",$gj_type );
 }
 //
 $company=array('company'=>$jobDetailData['recruiter_company_name'],'logo'=>$jobDetailData['recruiter_logo']);
 $googleData['hiringOrganization']=getGoogleJobCompany($company);

 $loc=array('city'=>$jobDetailData['job_location'],'state'=>'','country_code'=>$jobDetailData['job_country_code']);
 $googleData['jobLocation']=getGoogleJobLocation($loc);
 $googleData['title']=$jobDetailData['job_title'];
 $googleData['validThrough']=formate_date1($jobDetailData['expired'],'%Y-%m-%d ');
 $googleData['skills']=$jobDetailData['skills'];
 $googleData['url']=$jobDetailData['job_url'];

 $job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',$jobDetailData['job_id']);
 if(tep_not_null($job_category_ids))
 {
  $cat= get_name_from_table(JOB_CATEGORY_TABLE,'category_name', 'id', $job_category_ids);
  $cat=str_replace(", ",",",$cat);
  $googleData['industry']=explode(",",$cat );
 }
 $experience_string=calculate_experience($jobDetailData['min_experience'],$jobDetailData['max_experience']);
 $googleData['experienceRequirements']=(($experience_string==INFO_TEXT_ANY_EXPERIENCE)?'Any Experience':$experience_string);
 $google_s='<script type="application/ld+json">';
 $google_s.=json_encode($googleData);
 $google_s.='</script>';
 }
 return $google_s;
}
endif;
if (!function_exists('getArticleData')) :
function getArticleData()
{
 global $articleDetailData;
 $articleDetailData=false;
 $now=date('Y-m-d H:i:s');
 $article_seo = tep_db_prepare_input($_GET['article_seo']);

 if(tep_not_null($article_seo))
 {
  if($row=getAnyTableWhereData(ARTICLE_TABLE,"seo_name='".tep_db_input($article_seo)."'","*"))
  {
   $ide						   = $row["id"];
   $seo_name				   = $row["seo_name"];
   $article_url                = getPermalink('article',array('ide'=> $ide,'seo_name'=>$seo_name));
   $image_url='';
   if(tep_not_null($row["article_photo"]) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ARTICLE_PHOTO.$row["article_photo"]))
   $image_url=tep_href_link(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO.$row["article_photo"]."&size=400");

   $articleDetailData['id']         = $ide;
   $articleDetailData['title']      = $row['title'];
   $articleDetailData['description'] = stripslashes(strip_tags($row['short_description']));
   $articleDetailData['author']    = $row['author'];
   $articleDetailData['image']     = $image_url;
   $articleDetailData['url']       = $article_url;
   $articleDetailData['published'] = $row['show_date'];

  }
 }
}
endif;
if (!function_exists('getGoogleArticleData')) :
function getGoogleArticleData()
{
 getArticleData();
 global $articleDetailData;
$googleData='';
if(isset($articleDetailData['id']))
{
 $googleData=array();
 $googleData['@context']='https://schema.org';
 $googleData['@type']='NewsArticle';
 $googleData['headline']      = tep_db_output($articleDetailData['title']);
 $googleData['datePublished'] = formate_date1($articleDetailData['published'],'%Y-%m-%d ');
 $googleData['description']   = tep_db_output($articleDetailData['description']);
 $googleData['author']        = array('@type'=>'Person','name'=>$articleDetailData['author']);//Organization
 if(tep_not_null($articleDetailData['image']))
 $googleData['image'] = array($articleDetailData['image']);//Organization

 $googleData['url']=$articleDetailData['url'];

 $google_s='<script type="application/ld+json">';
 $google_s.=json_encode($googleData);
 $google_s.='</script>';
 }
 return $google_s;
}
endif;
?>