<?php
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #**********
**********# Company       : Aynsoft                 #**********
**********# Copyright (c) www.aynsoft.com 2004     #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH);
$template->set_filenames(array('job_search' => 'job_search.htm','job_search_result'=>'job_search_result1.htm'));
include_once(FILENAME_BODY);
$jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'jobs_search.js');
$preview_box_jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'previewbox.js');
$state_error=false;

//print_r($_POST);
if($_POST['action'] == 'new'){
  $_POST['action'] ='search';
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$sID = (isset($_GET['sID']) ?(int)$_GET['sID'] : '');
$edit=false;
$search_name='';

print_r($_POST);
// echo $_POST['action'];
if(tep_not_null($sID))
{
 if(!$row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and id='".tep_db_input($sID)."'",'id,title_name'))
 {
  $messageStack->add_session(MESSAGE_ERROR_SAVED_SERCH_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES));
 }
 $search_name=$row_check['title_name'];
 $save_search_id=(int)$sID;
 $edit=true;
}
// initialize
if(tep_not_null($_POST['keyword']) && (($_POST['keyword']!='keyword') && ($_POST['keyword']!='Search by keywords') && ($_POST['keyword']!='mots-cl�s de recherche d\'emploi')) )
{
 $keyword=tep_db_prepare_input($_POST['keyword']);
}
if(tep_not_null($_POST['location']) && ($_POST['location']!='location'))
{
 $location=tep_db_prepare_input($_POST['location']);
}
if(tep_not_null($_POST['company']))
{
 $company=tep_db_prepare_input($_POST['company']);
}
if(tep_not_null($_POST['job_post_day']))
{
 $job_post_day=tep_db_prepare_input($_POST['job_post_day']);
}
if(tep_not_null($_POST['job_type']))
{
 $job_type=tep_db_prepare_input($_POST['job_type']);
}
if(tep_not_null($_POST['inserted_date']))
{
 $inserted_date=tep_db_prepare_input($_POST['inserted_date']);
}
if(tep_not_null($_POST['word1']))
{
 $word1=tep_db_prepare_input($_POST['word1']);
}
if(tep_not_null($_POST['country']))
{
 $country=(int)tep_db_prepare_input($_POST['country']);
}
$zip_code       = tep_db_prepare_input($_POST['zip_code']);
$radius         = (int)tep_db_prepare_input($_POST['radius']);
$search_zip_code=1;
if(tep_not_null($zip_code))
$search_zip_code= 2;
if(tep_not_null($_POST['state']))
{
 if(is_array($_POST['state']))
  $state=implode(',',tep_db_prepare_input($_POST['state']));
 else
  $state=tep_db_prepare_input($_POST['state']);
 if($state[0]==',')
  $state=substr($state,1);
}
elseif(tep_not_null($_POST['state1']))
{
 $state=tep_db_prepare_input($_POST['state1']);
}
if(tep_not_null($_POST['job_category']))
{
 $job_category=$_POST['job_category'];
 $job_category1=implode(",",$job_category);
}
if(tep_not_null($_POST['experience']))
{
 $experience=$_POST['experience'];
}
/////////////////
$job_salary = tep_db_prepare_input($_POST['job_salary']);
/////////////////////

if(tep_not_null($action1))
{
 switch($action1)
 {
  case 'save_search':
   if(!check_login("jobseeker"))
   {
    $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
   }
   $error=false;
   if(tep_not_null($_POST['TR_search_name']))
   {
    if($edit)
    {
     if($row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and title_name='".tep_db_input($_POST['TR_search_name'])."' and id!='".$save_search_id."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_SAVED_SERCH_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array=array( 'updated'=>'now()',
                            'title_name '=>tep_db_prepare_input($_POST['TR_search_name']) ,
                            'keyword'=>$keyword,
                            'location'=>$location,
                            'company'=>$company,
                            'word1'=>$word1,
                            'country'=>$country,
                            'state'=>$state,
                            'industry_sector'=>$job_category1,
                            'experience'=>$experience,
                            'zip_code'=>$zip_code,
                            'radius'=>$radius,
                            'jobseeker_id'=>$_SESSION['sess_jobseekerid']
                            );
      //print_r($sql_data_array); die();
      tep_db_perform(SEARCH_JOB_RESULT_TABLE, $sql_data_array,'update',"id='".$save_search_id."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
      tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES));
     }
    }
    else
    {
     if($row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and title_name='".tep_db_input($_POST['TR_search_name'])."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_SAVED_SERCH_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array=array( 'inserted'=>'now()',
                             'title_name '=>tep_db_prepare_input($_POST['TR_search_name']) ,
                             'keyword'=>$keyword,
                             'location'=>$location,
                             'company'=>$company,
                             'word1'=>$word1,
                             'country'=>$country,
                             'state'=>$state,
                             'industry_sector'=>$job_category1,
                             'experience'=>$experience,
                             'zip_code'=>$zip_code,
                             'radius'=>$radius,
                             'jobseeker_id'=>$_SESSION['sess_jobseekerid']
                           );
      tep_db_perform(SEARCH_JOB_RESULT_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
      tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES));
     }
    }
   }
   $template->assign_vars(array(
        // 'INFO_TEXT_TITLE_NAME' =>"<br>".INFO_TEXT_TITLE_NAME,
        'INFO_TEXT_TITLE_NAME1'=>tep_draw_input_field('TR_search_name', $search_name,'size="26" class="form-control" aria-describedby="button-addon2" placeholder="'.INFO_TEXT_TITLE_NAME.'" maxlength="32"',true)));
 }
}
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $hidden_fields1='';
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $search_string='';
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';
   if ((preg_match("/http:\/\//i",$keyword)))
   $keyword='';
   if(tep_not_null($keyword)  && (($_POST['keyword']!='keyword') && ($_POST['keyword']!='job search keywords')) ) //   keyword starts //////
   {
    if($_SESSION['sess_jobsearch']!='y')
    tag_key_check($keyword);
    $_SESSION['sess_jobsearch']='y';

    $whereClause1='(';
    $hidden_fields1.=tep_draw_hidden_field('keyword',$keyword);
    $search_string.='<div class="tags"><div class="tag">'.tep_db_output($keyword).'
      <a href="#sf_keyword" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg></a>
      </div>
      </div>';
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    if($word1=='Yes')
    {
     $hidden_fields.=tep_draw_hidden_field('word1',$word1);
     $explode_string=explode(' ',$keyword);
		   $total_keys = count($explode_string);
					for($i=0;$i<$total_keys;$i++)
					{
		    if(strlen($explode_string[$i])< 3 or strtolower($explode_string[$i])=='and')
					 {
       unset($explode_string[$i]);
					 }
					}
					sort($explode_string);
     $whereClause1.='(';
		   $total_keys = count($explode_string);
     for($i=0;$i<$total_keys;$i++)
     {
      if($i>0)
      $whereClause1.='or ( ';
      $whereClause1.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" r.recruiter_company_name like '%".tep_db_input($explode_string[$i])."%' or ";

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
     }
					if($total_keys<=0)
					$whereClause1='';
    }
    else
    {
     $whereClause1.=" j.job_title like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_state like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_location like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_short_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or j.job_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or r.recruiter_company_name like '%".tep_db_input($keyword)."%'";

     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($keyword) . "%' or zone_code like '%" . tep_db_input($keyword) . "%')");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
     $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($keyword)."%'");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
    }
 			if($whereClause1!='')
    $whereClause1.=" ) ";
    $whereClause.=$whereClause1;
   }
   // keyword ends //////

   //   location starts //////
   if(tep_not_null($location) && $_POST['location']!='location')
   {
    $whereClause1='(';
    $hidden_fields1.=tep_draw_hidden_field('location',$location);
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
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
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

   // job_post_day starts //
   if(tep_not_null($_POST['job_post_day']))
   {
    $job_post_day=abs((int)($_POST['job_post_day']));
    $hidden_fields.=tep_draw_hidden_field('job_post_day',$job_post_day);
    if($job_post_day>0)
    {
      $posted_array =array('1' => 'Last 24 hours','7' => 'Last 7 days','14' => 'Last 14 days','30' => 'Last 30 days');
      $search_string.='<div class="tags"><div class="tag">'.tep_db_output($posted_array[$job_post_day]).' <a href="#sf_job_post_day_'.$job_post_day.'" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg></a></div></div>';
      }
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$job_post_day, date("Y")))."' ) ";
   }
   // job_post_day end //

   // job type starts //
   if(tep_not_null($_POST['job_type']))
   {
    $job_type=($_POST['job_type']);
    $hidden_fields.=tep_draw_hidden_field('job_type',$job_type);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.job_type ='".$job_type."' ) ";
    $job_typeName =get_name_from_table(JOB_TYPE_TABLE,'type_name','id',$job_type);
    $search_string.='<div class="tags"><div class="tag">'.tep_db_output($job_typeName).' <a href="#sf_job_type" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
  </svg></a></div></div>';
   }
   // job type end //

   // inserted date starts //
   if(tep_not_null($_POST['inserted_date']))
   {
    $inserted_date=($_POST['inserted_date']);
    $hidden_fields.=tep_draw_hidden_field('inserted_date',$inserted_date);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv ='".$inserted_date."' ) ";
   }
   // inserted date end //
   // company starts //
   //*
   if(tep_not_null($_POST['company']))
   {
    $hidden_fields.=tep_draw_hidden_field('company',$company);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( r.recruiter_company_name ='".tep_db_input($company)."' )";
    $whereClause.=" or ( r.recruiter_company_seo_name like '%".tep_db_input($company)."%' )";
   }
   //*/// company ends ///
   // experience starts //
   //*
   if(tep_not_null($_POST['experience']))
   {
    $experience=$_POST['experience'];
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $whereClause.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
    $search_string.='<div class="tags"><div class="tag">
      '.calculate_experience($explode_string['0'],$explode_string['1']).'
      <a href="#sf_experience" class="search_fiter tag__name"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg></a></div></div>';
  }
   //*/// experience ends ///

   // skills
  if(tep_not_null($_POST['skillTag'])){
    $job_skill   = str_replace('+',' ',tep_db_prepare_input($_POST['skillTag'])) ;
    $hidden_fields1.=tep_draw_hidden_field('skill',$job_skill);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.job_skills ='".tep_db_input($job_skill)."' )";
    $whereClause.=" or ( j.job_skills like '".tep_db_input($job_skill)."%' )";
    $whereClause.=" or ( j.job_skills like '%".tep_db_input($job_skill)."' )";
    $whereClause.=" or ( j.job_skills like '%".tep_db_input($job_skill)."%' )";
    $search_string.='<div class="tags"><div class="tag">
      '.$job_skill.'
      <a href="#sf_skills" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg></a></div></div>';
  }

// job_salary starts ///
if(tep_not_null($job_salary))
{
$hidden_fields1.=tep_draw_hidden_field('job_salary',$job_salary);
$whereClause=(tep_not_null($whereClause)?$whereClause.' and (':' (');
if($job_salary==1)
		$whereClause.=" j.job_salary >0 and j.job_salary<20000";
elseif($job_salary==2)
		$whereClause.=" j.job_salary >20000 and j.job_salary<=40000";
elseif($job_salary==3)
		$whereClause.=" j.job_salary >40000 and j.job_salary<=60000";
elseif($job_salary==4)
		$whereClause.=" j.job_salary >60000 and j.job_salary<=80000";
else
		$whereClause.=" j.job_salary >100000";

	$whereClause.="  )";
}
// job_salary ends ///

   // industry job_category  starts ///
   if(tep_not_null($_POST['job_category']))
   {
    $job_category=$_POST['job_category'];
    if($job_category['0']!='0')
    {
     $job_category1=remove_child_job_category($job_category1);
     $job_category=explode(',',$job_category1);
     $count_job_category=count($job_category);
     for($i=0;$i<$count_job_category;$i++)
     {
      $hidden_fields.=tep_draw_hidden_field('job_category[]',$job_category[$i]);
     }
     $search_category1 =get_search_job_category($job_category1);
     $now=date('Y-m-d 00:00:00');//   $now=date('Y-m-d H:i:s');
     $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category1.")";
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and job_id in ( ':' job_id in (');
     $whereClause.=$whereClause_job_category;
     $whereClause.=" ) ";
    }
    else
    {
     $hidden_fields.=tep_draw_hidden_field('job_category[]','0');
    }
   }
      // industry job_category1 ends ///
  //salary range
  if (tep_not_null($_POST['salary_range'])) {
    if (is_array($_POST['salary_range'])) {
      $whereClause = (tep_not_null($whereClause) ? $whereClause . ' and ' : '');
      $s_query = array();
      foreach ($salary_range as  $key) {
        $hidden_fields .= tep_draw_hidden_field('salary_range[]', $key);
        $search_string .= '<div class="tags"><div class="tag">' . getSalaryRangeName($key) . ' <a href="#sf_job_salary_' . $key . '" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg></a></div></div>';
        $s_query[]  = getSalaryQuery($key);
      }
      $s_query = trim(implode(' or ', $s_query));
      // $whereClause .= $s_query;
      $whereClause .= '(' . $s_query . ')';
    }
  }
 //salary range end
   // state starts ///
   if(tep_not_null($state))
   {
    $search_string.='<div class="tags"><div class="tag">'.tep_db_output($state).' <a href="#sf_job_state" class="search_fiter tag__name" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
  </svg></a></div></div>';
    $state1=explode(',',$state);//print_r($state1);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
    for($i=0;$i<count($state1);$i++)
    {
     $hidden_fields.=tep_draw_hidden_field('state[]',$state1[$i]);
     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (zone_name like '%" . tep_db_input($state1[$i]) . "%' or zone_code like '%" . tep_db_input($state1[$i]) . "%')");
     $whereClause.="  ( j.job_state like '%".tep_db_input($state1[$i])."%' )  ";
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause.=' or ( ';
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
      tep_db_free_result($temp_result);
     }
     $whereClause.=" or ";
    }
    $whereClause=substr($whereClause,0,-4);
    $whereClause.="  )";

   }
   // state ends ///
   if($search_zip_code==2)
   {
    ////zip code ////////////
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $hidden_fields1.=tep_draw_hidden_field('zip_code',$zip_code);
    $hidden_fields1.=tep_draw_hidden_field('radius',$radius);
    $hidden_fields.=tep_draw_hidden_field('search_zip_code',2);
    if($row=getAnyTableWhereData(ZIP_CODE_TABLE," zip_code='".tep_db_input($zip_code)."'",'*'))
    {
     ////////////////////
     $today=date('Y-m-d');
     if($row_cache=getAnyTableWhereData(ZIP_CODE_SEARCH_TABLE," zip_code='".tep_db_input($zip_code)."' and  radius='".tep_db_input($radius)."'",'state'))
     {
      $state_array =explode(',',$row_cache['state']);
     }
     else
     {
      $state_array=array();
     // echo ("select distinct(state) as state from " . ZIP_CODE_TABLE. " where ( 3959 * acos( cos( radians( ".tep_db_input($row['latitude']).") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".tep_db_input($row['longitude']).") ) + sin( radians( ".tep_db_input($row['latitude']).") ) * sin( radians( latitude ) ) ) ) <=".tep_db_input($radius)."");
      $temp_state_result = tep_db_query("select distinct(state) as state from " . ZIP_CODE_TABLE. " where ( 3959 * acos( cos( radians( ".tep_db_input($row['latitude']).") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".tep_db_input($row['longitude']).") ) + sin( radians( ".tep_db_input($row['latitude']).") ) * sin( radians( latitude ) ) ) ) <=".tep_db_input($radius)."");
      if(tep_db_num_rows($temp_state_result) > 0)
      {
       while($temp_row = tep_db_fetch_array($temp_state_result))
       {
        $state_array[]=trim($temp_row['state']);
       }
       $state_zip=implode(',',$state_array);
       $sql_data_search_array=array('zip_code'=>$zip_code,
                        'radius'=>$radius,
                        'state'=>$state_zip,
                        'inserted'=>$today,
                       );
       tep_db_perform(ZIP_CODE_SEARCH_TABLE, $sql_data_search_array);
      }
      tep_db_free_result($temp_state_result);
     }
     $total_state=count($state_array);
     if($total_state>0)
     {
      $whereClause.='( ';
      for($i=0;$i<$total_state;$i++)
      {
       $search_state= $state_array[$i];
       if($row_state=getAnyTableWhereData(ZONES_TABLE," zone_id='".tep_db_input($search_state)."'",'zone_name'))
        $whereClause.="  ( j.job_state = '".tep_db_input($row_state['zone_name'])."' or  j.job_state_id = '".tep_db_input($search_state)."') or ";
       else
        $whereClause.="(j.job_state_id ='".tep_db_input($search_state)."') or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
     }
     else
     {
      $whereClause.=' 0 ';
     }
    }
	   else
    $whereClause.=' 0 ';
    ///////////////////
   }
			// country starts ///
   if(tep_not_null($country) && $country > 0)
			{
		  $hidden_fields1.=tep_draw_hidden_field('country',$country);
				$whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
				$whereClause.=" j.job_country_id ='".tep_db_input($country)."'";
				$whereClause.="  )";
			}
   // country ends ///
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d 00:00:00');//   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id) left outer join '.JOB_TYPE_TABLE.' as jt on (j.job_type =jt.id)';
   $whereClause.="   rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.inserted, j.job_short_description,  j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,jt.type_name,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_company_seo_name,r.recruiter_logo,r.recruiter_applywithoutlogin, j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name,job_skills"; //j.job_state, j.job_state_id,j.job_country_id
   $query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];

   //////////////////
			$query = "select $field_names from $table_names where $whereClause ORDER BY  j.inserted desc, if(j.job_source ='jobsite',0,1)  asc, j.job_featured='Yes' desc";
			$starting=0;
			$recpage = MAX_DISPLAY_SEARCH_RESULTS;
			$obj = new pagination_class1($query,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius,0);

			$result = $obj->result;
			$x=tep_db_num_rows($result);
			$content='';
			$count=1;
			$count1=1;
   if(tep_db_num_rows($result)!=0)
   {
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["job_id"];
//////////////////////////////////////////////////////////////////
$jobseeker_id=(check_login("jobseeker")?$_SESSION['sess_jobseekerid']:'');
if(tep_not_null($jobseeker_id))
    $row_apply=getAnytableWhereData(APPLY_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id ='".$ide."'",'id,jobseeker_apply_status');
//echo $row_apply['id'];
/////////////////////////////////////////////////////////////////
     $recruiter_logo='';
     $company_logo=$row['recruiter_logo'];
     $title_format=encode_category($row['job_title']);
     $query_string=encode_string("job_id=".$ide."=job_id");

					if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo)) {
            $recruiter_logo = tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");
          } else {
            $recruiter_logo = defaultProfilePhotoUrl($row['recruiter_company_name'], false, 120);
          }


					$email_job    ='<a class="small mr-1" href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'" target="_blank">'.INFO_TEXT_EMAIL_THIS_JOB.'</a>';
					$apply_job    ='<a class="btn-sm text-primary fw-bold" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.'</a>';

     if($row['job_featured']=='Yes')
					{
					 $row_selected='jobs-result-wrapper featured-job';
					}
					else
					{
					 $row_selected='jobs-result-wrapper';
						$count++;
					}
				     $job_skill_1= getSkillTagLink($row['job_skills']);
				    //$job_skill_1= getSkillTagValueForSearch($row['job_skills']);

////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/
     $save_job    ='<div id="'.$query_string.'"  class="nav-item" ><a class="small" href="'.getPermalink(FILENAME_JOBSEEKER_LOGIN).'" title="'.INFO_TEXT_SAVE_JOB.'"><i class="fa fa-heart-o icon-unsaved mobile-absolute-right"></i><!-- '.INFO_TEXT_SAVE_JOB.'--></a></div>';

if(check_login("jobseeker"))
     {
      if($row_check=getAnyTableWhereData(SAVE_JOB_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$ide."'"))
      $save_job    = "<div class=''><span class='j_green'><i class='fa fa-heart icon-saved text-success mobile-absolute-right'></i><!--".INFO_TEXT_JOB_SAVED."--></span></div>";
	  else
      $save_job    ='<div id="'.$query_string.'"  class="nav-item "><a  class="" href="'.tep_href_link(FILENAME_JOB_DETAILS,'query_string1='.$query_string.'&action=save').'" title="'.INFO_TEXT_SAVE_JOB.'" target="_blank"><i class="fa fa-heart-o icon-unsaved mobile-absolute-right" aria-hidden="true"></i><!-- '.INFO_TEXT_SAVE_JOB.'--></a></div>';
     }
// $company_name="<a href='".tep_href_link('company/'.$row["recruiter_company_seo_name"].'/')."'  class='blue' target='_blank' rel='noopener'>".tep_db_output($row['recruiter_company_name'])."</a> ";
$jobPostedRelativeDate = new Relative_Date($row['re_adv']);

$template->assign_block_vars('job_search_result', array(
			  'row_selected' => $row_selected,
			  // 'check_box' => (($row['post_url']=='Yes'  )?'':'<input class="form-check-input" type="checkbox" name="apply_job" value="'.$query_string.'">'),
        'check_box' => (check_login("jobseeker")?(($row['post_url']=='Yes' || $row_apply['id'] > 0 && $row_apply['jobseeker_apply_status'] == 'active')?'':'<input class="form-check-input" type="checkbox" name="apply_job" value="'.$query_string.'">'):''),
			  'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)) .'" class="job_search_title" target="_blank">'.tep_db_output($row['job_title']).'</a>',
			  //'job_title' => tep_db_output($row['job_title']),
			   'job_type'=>tep_db_output($row['type_name']) ? tep_db_output($row['type_name']) : 'Any Type',
			   'company_name' =>tep_db_output($row['recruiter_company_name']),
			  //  'company_name' =>$company_name,
			   'location' =>tep_db_output($row['location'].' '.$row['country_name']),
			  'experience' =>tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
			  'salary' =>(tep_not_null($row['job_salary'])?$sym_left.tep_db_output($row['job_salary']).$sym_rt:'Negotiable'),
			  'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
			  // 'job_skill' =>(tep_not_null($row['job_skills']))? $job_skill_1:'',
			  'job_skill' =>(tep_not_null($row['job_skills']))? $job_skill_1:'',
			  'skill_class' =>(tep_not_null($row['job_skills']))?'':'result_hide',
        'jobId' => $row['job_id'],
			'description' => nl2br(tep_db_output(strip_tags(substr($row['job_short_description'],0,75).'. . .'))),
			  'apply_before' => tep_date_short($row['expired']),
			  // 'posted_on' => tep_date_short($row['inserted']),
        'posted_on' => $jobPostedRelativeDate->relative_formatted_date,
			  'logo'      => $recruiter_logo,
			  'email_job' => $email_job,
			  'save_job'  => $save_job,
			  'apply_job' => (check_login("jobseeker")?(($row_apply['id']>0 && $row_apply['jobseeker_apply_status']='active')?'  <span class="btn-sm text-success fw-bold"><i class="fa fa-check" aria-hidden="true"></i> Applied</span>':$apply_job):''),
			  'applywithoutlogin' => (($row['recruiter_applywithoutlogin']=='Yes' && !check_login("jobseeker"))?'
          	<a class="small" href="'.tep_href_link(FILENAME_APPLY_NOLOGIN,'query_string='.$query_string).'"> <i class="fa fa-calendar mr-1" aria-hidden="true"></i> Apply without login</a>':''),
));




     /////////////////////////////////////////////////////////
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

     /////////////////////////////////////////////////////////
    }
			 $template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total,'page_view'=>$obj->show_view));
    $plural=($x=="1")?INFO_TEXT_JOB:INFO_TEXT_JOBS;
    $template->assign_vars(array('total'=>"".$x1." ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
   }
   else
   {
    $template->assign_vars(array(
                'content_hide'=>'result_hide',
								'total'=>NO_RESULT_FOUND
              ));
              //'total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
   }
  break;
 }
}
//echo  $whereClause;
if(!in_array($word1,array('Yes','No')))
 $word1='Yes';
if($action=='' && !isset($_GET['sID']))
 $country=(int)DEFAULT_COUNTRY_ID;

//echo "<br><br>id=".isset($_GET['sID'])."<br> actio=".$action;
/*************************codeing to display different form and save search link for login and non login users *********************/
// if(check_login("jobseeker"))
// {
// 	$save_search= tep_draw_form('save_search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)" class=""').tep_draw_hidden_field('action1','save_search');
//     $INFO_TEXT_ALERT_TEXT=(($action1=='save_search')?'':"<a href='#' class='btn btn-sm btn-primary px-3' onclick='document.save_search.submit();'><i class='bi bi-bell-fill'></i><!--".INFO_TEXT_CREATE_JOB_ALERT."--></a>");
// }
// else
// {
// 	 $save_search= tep_draw_form('save_search', FILENAME_JOB_ALERT_AGENT_DIRECT,'','post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','new');
// 	 $INFO_TEXT_ALERT_TEXT=$save_search.tep_draw_input_field('TREF_job_alert_email', $TREF_job_alert_email,'class="form-control form-control-result-page-search" placeholder="Enter email & create alert" ',false)."<button type='submit' class='btn btn-result-page-search px-3'><i class='bi bi-bell-fill'></i><!--".INFO_TEXT_CREATE_JOB_ALERT."--></button></form>";
// }

/**********************************************************************************************************************************/

//$cat_array=tep_get_diving_main_categories(DIVING_CATEGORY_TABLE);
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_JOB_CATEGORY));
if($action!='search')
{
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'BY_SEARCH_FIELD'        => 'helo',
  'form'                   => tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post','class="form-signin"').tep_draw_hidden_field('action','search').tep_draw_hidden_field('search_zip_code',$search_zip_code),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_SEARCH,'post').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  // 'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $keyword, 'placeholder="e.g. Sales Executive" class="form-control"' ,'size="40"',false).INFO_TEXT_KEYWORD_EXAMPLE,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $keyword, 'placeholder="Search by title, skill, or company" class="form-control"'),
  'INFO_TEXT_KEYWORD_CRITERIA'=>INFO_TEXT_KEYWORD_CRITERIA,

  'INFO_TEXT_KEYWORD3'     => '<div class="form-check form-check-inline">
                                  '.tep_draw_radio_field('word1', 'Yes', '', $word1,'id=radio_word1 class="form-check-input"').'
                                  <label for="radio_word1" class="form-check-label">'.INFO_TEXT_KEYWORD_WORD1.'</label>
                                </div>
                              <div class="form-check form-check-inline">
                                '.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2 class="form-check-input"').'
                                <label for="radio_word2" class="form-check-label">'.INFO_TEXT_KEYWORD_WORD2.'</label>
                              </div>',

  'INFO_TEXT_LOCATION'     => (($search_zip_code==2)?INFO_TEXT_ZIP_CODE:INFO_TEXT_LOCATION_NAME),
//  'INFO_TEXT_LOCATION1'    => (($search_zip_code==2)?tep_draw_input_field('zip_code',$zip_code,'').''.zone_radius('radius',"","",$radius,true,"class='form-control mmt-15'").tep_draw_hidden_field('location',''):tep_draw_input_field('location', $location,' class="form-control mmt-15" ',false)),
 
	'COUNTRY_STATE_SCRIPT'    => country_state($c_name='country',$c_d_value=DEFAULT_COUNTRY_ID,$s_name='state[]',$s_d_value='State','zone_name',$state),
    'INFO_TEXT_STATE1'=>LIST_SET_DATA(ZONES_TABLE," where zone_country_id='223'",'zone_name','zone_name',"zone_name",'name="state[]" class="form-select mb-2"',"state",'',$state),

  'INFO_TEXT_SEARCH_COUNTRY_STATE' => INFO_TEXT_SEARCH_COUNTRY_STATE,
  'INFO_TEXT_SEARCH_US_ZIP'=> INFO_TEXT_SEARCH_US_ZIP,
  'INFO_TEXT_COUNTRY'      => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'     => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-select'",INFO_ALL_COUNTRY,"",$country),
'INFO_CODE_IF_COUNTRY_US'=>($country=='223'?'
<div class="form-group row">
                        <div class="col-md-3 col-form-label m-m-b-10 m-bold">
                            '.INFO_TEXT_SEARCH_US_ZIP.' :
                        </div>
                        <div class="col-md-5 mb-3">
                          <label for="inputZip">'.INFO_TEXT_ZIP_CODE.'</label>
                          '.tep_draw_input_field('zip_code',$zip_code,' class="form-control" ').'
                        </div>
                        <div class="col-md-4">
                            <label for="inputRadius">'.INFO_TEXT_RADIUS.'</label>
                            '.zone_radius('radius',"","",$radius,true,' class="form-control" ').'
                        </div>
                    </div>
':''),
'INFO_TEXT_SUMMARY'=>INFO_TEXT_SUMMARY,
'INFO_TEXT_APPLY'=>INFO_TEXT_APPLY,
'INFO_TEXT_JOB_DESCRIPTION'=>INFO_TEXT_JOB_DESCRIPTION,
'INFO_TEXT_KEYSKILLS'=>INFO_TEXT_KEYSKILLS,
  'INFO_TEXT_JOB_SALARY'   => INFO_TEXT_JOB_SALARY,
  'INFO_TEXT_JOB_SALARY1'  => LIST_TABLE(JOB_SALARY_TABLE,TEXT_LANGUAGE."sal_name","priority","name='job_salary' class='form-select'",INFO_P_ANY_SAL,"",$job_salary),
  'INFO_TEXT_JOB_TYPE'      => INFO_TEXT_JOB_TYPE,
  'INFO_TEXT_JOB_TYPE1'     => LIST_TABLE(JOB_TYPE_TABLE,TEXT_LANGUAGE."type_name","priority","name='job_type' class='form-select'",INFO_P_ALL_JOB_TYPE,"",$job_type),

  'INFO_TEXT_JOB_CATEGORY' => INFO_TEXT_JOB_CATEGORY,
  'INFO_TEXT_JOB_CATEGORY_TEXT' => INFO_TEXT_JOB_CATEGORY_TEXT,
  'INFO_TEXT_JOB_CATEGORY1'=> tep_draw_pull_down_menu('job_category[]', $cat_array, explode(",",$job_category1), 'class="form-select"', false),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => experience_drop_down('name="experience" class="form-select"', INFO_EXPERIENCE, '', $experience),
  'INFO_TEXT_JOB_POSTED'   => INFO_TEXT_JOB_POSTED,
  'INFO_TEXT_JOB_POSTED1'  => LIST_SET_DATA(JOB_POSTED_TABLE,"",TEXT_LANGUAGE.'type_name','value',"priority","name='job_post_day' class='form-select'" ,INFO_TEXT_DEFAULT_JOB_POST_DAY,'',$job_post_day),
  'button'                 => '<button class="btn btn-primary" type="submit">'.INFO_BUTTON_SEARCH.'</button>',//tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
 'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
  ));
}
else
{
  $key1=(tep_not_null($keyword)?$key1=$keyword:'keyword');
 $loc1=(tep_not_null($location)?$loc1=$location:'location');
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'BY_SEARCH_FIELD'        => ($_POST['search_by_text']) ? '<span class="badge badge-secondary">'.$_POST['search_by_text'].'</span>' : '',
  'hidden_fields1'          => $hidden_fields1,
  'form'                   => tep_draw_form('page', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post'),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search'),
  'button'                 => tep_image_submit(PATH_TO_BUTTON.'button_refine_search.gif', IMAGE_SEARCH),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $key1,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_LOCATION'     => INFO_TEXT_LOCATION,
  'INFO_TEXT_LOCATION1'    => tep_draw_input_field('location', $loc1 ,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_APPLY_NOW'    => (($x>0)?INFO_TEXT_APPLY_NOW:''),
  'INFO_TEXT_APPLY_NOW1'   => (check_login("jobseeker")?(($x>0)
                                ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                      fill="currentColor" class="bi bi-arrow-90deg-down mt-2 flip me-1" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd"
                                        d="M4.854 14.854a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V3.5A2.5 2.5 0 0 1 6.5 1h8a.5.5 0 0 1 0 1h-8A1.5 1.5 0 0 0 5 3.5v9.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4z" />
                                    </svg>'.INFO_TEXT_APPLY_NOW1
                                : ''):''),
  'INFO_TEXT_APPLY_ARROW'  => (($x>0)?tep_image('img/job_search_arrow.gif',''):''),
 // 'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?tep_image_button(PATH_TO_BUTTON.'button_apply_selectedjob.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"'):tep_image_button(PATH_TO_BUTTON.'button_registered_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"').' '.tep_image_button(PATH_TO_BUTTON.'button_new_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'new\');" style="cursor:pointer;"')):''),

 'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?'<a class="btn btn-primary" onclick="ckeck_application(\'\');" role="button">'.INFO_APPLY_SELECTED.'  <i class="bi bi-box-arrow-in-up-right ms-2"></i></a>':'<a class="btn btn-primary me-3" href="'.getPermalink(FILENAME_JOBSEEKER_LOGIN).'">'.INFO_REG_USER.'</a><a class="btn btn-outline-primary mmt-15 mw-100" href="'.tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'">'.INFO_NEW_USER.'</a>'):''),
 'INFO_TEXT_SEARCH_STRING'   => ($search_string) ? '<div class="tags-container">'.$search_string.'</div>' : '',
  'INFO_TEXT_LOCATION_NAME'=> INFO_TEXT_LOCATION_NAME,
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_SALARY'       => INFO_TEXT_SALARY,
  'INFO_TEXT_JOB_SKILL'    =>INFO_TEXT_JOB_SKILL,
  'INFO_TEXT_APPLY_BEFORE' => INFO_TEXT_APPLY_BEFORE,
  'INFO_TEXT_POSTED_ON'=>INFO_TEXT_POSTED_ON,
 'save_search'            => $save_search,
  'INFO_TEXT_ALERT_TEXT'   => $INFO_TEXT_ALERT_TEXT,
//  'INFO_TEXT_ALERT_IMAGE'  => (($action1=='save_search')?'':tep_image_submit('img/alert_icon.gif','')),

// 'INFO_TEXT_ALERT_SAVE'   => (($action1=='save_search')
  // ?tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).($action1=='save_search'?'&nbsp;'.'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'">'.tep_image(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>':'').' <a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">'.tep_image(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>':''),

  'INFO_TEXT_ALERT_SAVE'   => (($action1=='save_search')
  ?tep_button_submit('btn btn-primary me-3', IMAGE_SAVE)
  .($action1=='save_search'?'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'" class="btn btn-sm btn-outline-primary me-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
</svg></a>':'')
  .'<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'" class="btn btn-sm btn-outline-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
</svg></a></form>':''),


'INFO_TEXT_SUMMARY'=>INFO_TEXT_SUMMARY,
'INFO_TEXT_APPLY'=>INFO_TEXT_APPLY,
'INFO_TEXT_JOB_DESCRIPTION'=>INFO_TEXT_JOB_DESCRIPTION,
'INFO_TEXT_KEYSKILLS'=>INFO_TEXT_KEYSKILLS,
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
  'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',

'MAP_PAGE_VIEW_RESULT1_TEMP'=>(GOOGLE_MAP=='true'?'<td valign="bottom"><div align="right" style=""><i class="fa fa-map-marker" aria-hidden="true"></i> '.$obj->show_view.'&nbsp;&nbsp;</div></td>':''),
'MAP_PAGE_VIEW_RESULT_TEMP'=>(GOOGLE_MAP=='true'?'<div align="right">'.$obj->show_view.'</div>':''),
  //'save_button'            => tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).($action1=='save_search'?'&nbsp;'.'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'">'.tep_image(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>':'').' <a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">'.tep_image(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>',
                      ));
}
/*
if($state_error)
{
 $zones_array=tep_get_country_zones($country);
 if(sizeof($zones_array) > 1)
 {
  $template->assign_vars(array( 'INFO_TEXT_STATE1' => tep_draw_pull_down_menu('state', tep_get_country_zones($country),$state)));
 }
 else
 {
  $template->assign_vars(array('INFO_TEXT_STATE1' => tep_draw_input_field('state', $state,'size="50"',false)));
 }
}
else
{
 //$template->assign_vars(array('INFO_TEXT_STATE1' => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_name',"zone_name",'name="state[]" ',"state",'',$state)." ".tep_draw_input_field('state1',$state,'size="20"')));
 $template->assign_vars(array('INFO_TEXT_STATE1' =>  tep_draw_input_field('state1',$state,'size="33"')));
}*/

$template->assign_vars(array(
		 'base_url'=> tep_href_link(),
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
  'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
  'INFO_TEXT_SUMMARY'=>INFO_TEXT_SUMMARY,
  'INFO_TEXT_APPLY'=>INFO_TEXT_APPLY,
  'INFO_TEXT_JOB_DESCRIPTION'=>INFO_TEXT_JOB_DESCRIPTION,
  'INFO_TEXT_KEYSKILLS'=>INFO_TEXT_KEYSKILLS,
 'update_message' => $messageStack->output()));
if($action=='search' || $action=='save_search')
{
 $template->pparse('job_search_result');
}
else
{
 $template->pparse('job_search');
 unset($_SESSION['sess_jobsearch']);
}
?>