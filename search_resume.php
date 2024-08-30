<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft              #*********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
//session_cache_limiter('private_no_expire');
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_SEARCH_RESUME);
$template->set_filenames(array('search_resume' => 'search_resume.htm','search_resume_result'=>'search_resume_result.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'search_resume.js';
$resumeDetailJsFilePath=PATH_TO_LANGUAGE.$language."/jscript/".'resume_detail.js';
include_once(FILENAME_BODY);

$template->assign_vars(array('HEADING_TITLE'=>HEADING_TITLE,
'HEADING_SEARCH'=>HEADING_SEARCH,
));
if(!check_login("recruiter"))
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_RECRUITER_LOGIN));
}
if(isset($_POST['action']))
{
//print_r($_POST);die();
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');

////////////// check subscription starts ////
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
$obj_account=new recruiter_accounts('','resume_search');
//print_r($obj_account->allocated_amount);
$cv=$obj_account->allocated_amount['cv'];
$enjoyed_cv=$obj_account->enjoyed_amount['cv'];
if($cv!="Unlimited")
{
 if($enjoyed_cv > $cv || $cv=='0')
 {
  if($enjoyed_cv > $cv && $action=='search')
  {
   $now=date("Y-m-d");
   tep_db_query("update ".RECRUITER_ACCOUNT_HISTORY_TABLE." set cv_enjoyed=cv_enjoyed-1 where recruiter_id='".$_SESSION['sess_recruiterid']."' and start_date <= '$now' and end_date >='$now'");
  }
  //$messageStack->add_session(ERROR_SUBSCRIPTION, 'error');
  tep_redirect(tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME1));
 }
}
/////////////////////////////////////////////////////////

//print_r($_POST);
$state_error=false;
$sID = (isset($_GET['sID']) ?(int)$_GET['sID'] : '');
$edit=false;
$search_name='';
if(tep_not_null($sID))
{
 if(!$row_check=getAnyTableWhereData(SEARCH_RESUME_RESULT_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and id='".tep_db_input($sID)."'",'id,title_name'))
 {
  $messageStack->add_session(MESSAGE_ERROR_RESUME_SEARCH_AGENT_NOT_EXIST,'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
 }
 $search_name=$row_check['title_name'];
 $save_search_id=(int)$sID;
 $edit=true;
}

// initialize
if(tep_not_null($_POST['keyword']))
{
 $keyword=tep_db_prepare_input($_POST['keyword']);
}
if(tep_not_null($_POST['word1']))
{
 $word1=tep_db_prepare_input($_POST['word1']);
}
if(tep_not_null($_POST['minimum_rating']))
{
 $minimum_rating=tep_db_prepare_input($_POST['minimum_rating']);
}
if(tep_not_null($_POST['maximum_rating']))
{
 $maximum_rating=tep_db_prepare_input($_POST['maximum_rating']);
}
if(tep_not_null($_POST['first_name']))
{
 $first_name=tep_db_prepare_input($_POST['first_name']);
}
if(tep_not_null($_POST['last_name']))
{
 $last_name=tep_db_prepare_input($_POST['last_name']);
}
if(tep_not_null($_POST['email_address']))
{
 $email_address=tep_db_prepare_input($_POST['email_address']);
}
if(tep_not_null($_POST['country']))
{
 $country=(int)tep_db_prepare_input($_POST['country']);
}
if(tep_not_null($_POST['nationality']))
{
 $nationality=(int)tep_db_prepare_input($_POST['nationality']);
}

if(tep_not_null($_POST['state']) or tep_not_null($_POST['state1']))
{
 if(isset($_POST['state']) and $_POST['state']!='')
 $state=tep_db_prepare_input($_POST['state']);
 elseif(isset($_POST['state1']))
 $state=tep_db_prepare_input($_POST['state1']);
}
if(tep_not_null($_POST['city']))
{
 $city=tep_db_prepare_input($_POST['city']);
}
if(tep_not_null($_POST['zip']))
{
 $zip=tep_db_prepare_input($_POST['zip']);
}
if(tep_not_null($_POST['industry_sector']))
{
 $industry_sector=$_POST['industry_sector'];
 $industry_sector1=implode(",",$industry_sector);
 $industry_sector1=remove_child_job_category($industry_sector1);
}
else
	$industry_sector='';
if(tep_not_null($_POST['experience']))
{
 $experience=$_POST['experience'];
}
if(tep_not_null($action1))
{
 switch($action1)
 {
		case 'save':
   if(isset($_POST['resume_id']))
    $resume_id=$_POST['resume_id'];
    $count_resume_id=count($resume_id);
				for($i=0;$i<$count_resume_id;$i++)
				{
					$r_id=$resume_id[$i];
					$sql_data_array=array('resume_id'=>$r_id,
					'recruiter_id'=>$_SESSION['sess_recruiterid'],
					'inserted'=>'now()',
																													);
					if(!$row_save=getAnyTableWhereData(SAVE_RESUME_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$r_id."'",'id'))
					{
						tep_db_perform(SAVE_RESUME_TABLE, $sql_data_array);
					}
				}
    $messageStack->add(MESSAGE_SUCCESS_SAVED, 'success');
   break;
  case 'save_search':
   $error=false;
   if(tep_not_null($_POST['TR_search_name']))
   {
    $search_name=tep_db_prepare_input($_POST['TR_search_name']);
    $sql_data_array=array( 'title_name '=>$search_name,
                           'keyword'=>$keyword,
                           'word1'=>$word1,
                           'minimum_rating'=>$minimum_rating,
                           'maximum_rating'=>$maximum_rating,
                           'first_name'=>$first_name,
                           'last_name'=>$last_name,
                           'email_address'=>$email_address,
                           'country'=>$country,
                           'jobseeker_nationality'=>$nationality,
                           'state'=>$state,
                           'city'=>$city,
                           'zip'=>$zip,
                           'industry_sector'=>$industry_sector1,
                           'experience'=>$experience,
                           'recruiter_id'=>$_SESSION['sess_recruiterid']
                          );
    if($edit)
    {
     if($row_check=getAnyTableWhereData(SEARCH_RESUME_RESULT_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and title_name='".tep_db_input($search_name)."' and id!='".$save_search_id."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_RESUME_SEARCH_AGENT_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array['updated']='now()';
      tep_db_perform(SEARCH_RESUME_RESULT_TABLE, $sql_data_array,'update',"id='".$save_search_id."'");
      $messageStack->add(MESSAGE_SUCCESS_UPDATED, 'success');
      $action1='';
      //tep_redirect(FILENAME_RECRUITER_LIST_OF_RESUME_SEARCH_AGENTS);
     }
    }
    else
    {
     if($row_check=getAnyTableWhereData(SEARCH_RESUME_RESULT_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and title_name='".tep_db_input($search_name)."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_RESUME_SEARCH_AGENT_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array['inserted']='now()';
      tep_db_perform(SEARCH_RESUME_RESULT_TABLE, $sql_data_array);
      $messageStack->add(MESSAGE_SUCCESS_INSERTED, 'success');
      $action1='';
      //tep_redirect(FILENAME_RECRUITER_LIST_OF_RESUME_SEARCH_AGENTS);
     }
    }
   }
   if(tep_not_null($action1))
   $template->assign_vars(array('INFO_TEXT_TITLE_NAME' => INFO_TEXT_TITLE_NAME,
                                'INFO_TEXT_TITLE_NAME1'=>tep_draw_input_field('TR_search_name', $search_name,'class="form-control form-control-resume-search-agent" placeholder="Enter search agent name" ',true)));
 }
}
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $lower_1=(int)tep_db_prepare_input($_POST['lower_1']);
   $higher_1=(int)tep_db_prepare_input($_POST['higher_1']);
   $map_view=(int)tep_db_prepare_input($_POST['map_view']);
   $show_page=tep_db_prepare_input($_POST['show_page']);
   $whereClause='';

   if(tep_not_null($keyword) && strtolower($keyword)!='keyword') //   keyword starts //////
   {
    $whereClause1='';
    $hidden_fields.=tep_draw_hidden_field('keyword',$keyword);
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    if($word1=='Yes')
    {
     $hidden_fields.=tep_draw_hidden_field('word1',$word1);
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
     $whereClause1.=" jr1.target_job_titles like '%".tep_db_input($explode_string[$i])."%' or ";
     //$whereClause1.=" jr2.description  like '%".tep_db_input($explode_string[$i])."%' or ";
     //$whereClause1.=" jr3.related_info like '%".tep_db_input($explode_string[$i])."%' or ";
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
    }
    if($word1=='Yes')
    {
     $whereClause1.="  (MATCH(jr2.description) AGAINST ('".tep_db_input($keyword)."')) ";
     $whereClause1.=" or (MATCH(jr3.related_info) AGAINST ('".tep_db_input($keyword)."')) or ";
    }
    else
     $whereClause1.="  (jr2.description like '%".tep_db_input($keyword)."%' or jr3.related_info like '%".tep_db_input($keyword)."%') or ";

    $whereClause1=substr($whereClause1,0,-4);
    $whereClause1.=" ) ";
    $whereClause.=$whereClause1;
   }
   ///   keyword ends //////
   // minimum rating starts ///
   if(tep_not_null($minimum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('minimum_rating',$minimum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" (( jrt1.point >= '".tep_db_input($minimum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$_SESSION['sess_recruiterid']."'  and  jrt.point >= '".tep_db_input($minimum_rating)."')) ";
   }
   // minimum rating ends ///
   // maximum rating starts ///
   if(tep_not_null($maximum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('maximum_rating',$maximum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" (( jrt1.point <= '".tep_db_input($maximum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$_SESSION['sess_recruiterid']."'  and  jrt.point <= '".tep_db_input($maximum_rating)."')) ";
   }
   // maximum rating ends ///
   // first name starts ///
   if(tep_not_null($first_name))
   {
    $hidden_fields.=tep_draw_hidden_field('first_name',$first_name);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_first_name like '%".tep_db_input($first_name)."%' ) ";
   }
   // first name ends ///
   // last name starts ///
   if(tep_not_null($last_name))
   {
    $hidden_fields.=tep_draw_hidden_field('last_name',$last_name);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_last_name like '%".tep_db_input($last_name)."%' ) ";
   }
   //last name ends ///
   // email_address starts ///
   if(tep_not_null($email_address))
   {
    $hidden_fields.=tep_draw_hidden_field('email_address',$email_address);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( jl.jobseeker_email_address like '%".tep_db_input($email_address)."%' ) ";
   }
   // email_address ends ///
   // country starts ///
   if((int)$country>0)
   {
    $hidden_fields.=tep_draw_hidden_field('country',$country);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_country_id ='".(int)tep_db_input($country)."' ) ";
   }
   // country ends ///
      // nationality starts ///
   if((int)$nationality>0)
   {
    $hidden_fields.=tep_draw_hidden_field('nationality',$nationality);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( jr1.jobseeker_nationality ='".(int)tep_db_input($nationality)."' ) ";
   }
   // nationality ends ///

   // state starts ///
   if(tep_not_null($state))
   {
    $hidden_fields.=tep_draw_hidden_field('state',$state);
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':'(');
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
    $hidden_fields.=tep_draw_hidden_field('city',$city);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_city like '%".tep_db_input($city)."%' ) ";
   }
   //city ends ///
   // zip starts ///
   if(tep_not_null($zip))
   {
    $hidden_fields.=tep_draw_hidden_field('zip',$zip);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_zip like '%".tep_db_input($zip)."%' ) ";
   }
   //zip ends ///
   // industry sector starts ///
   if(tep_not_null($industry_sector))
   {
    if($industry_sector['0']!='0')
    {
     $industry_sector1=remove_child_job_category($industry_sector1);
     $industry_sector=explode(',',$industry_sector1);
     $count_industry_sector=count($industry_sector);
     for($i=0;$i<$count_industry_sector;$i++)
     {
      $hidden_fields.=tep_draw_hidden_field('industry_sector[]',$industry_sector[$i]);
     }
     $search_category1 =get_search_job_category($industry_sector1);
     $whereClause_job_category=" select distinct (jr.resume_id) from ".JOBSEEKER_RESUME1_TABLE."  as jr1  left join ".RESUME_JOB_CATEGORY_TABLE." as jr on(jr1.resume_id=jr.resume_id ) where jr1.search_status='Yes' and  jr.job_category_id in (".$search_category1.")";
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and jr1.resume_id in ( ':' jr1.resume_id in ( ');
     $whereClause.=$whereClause_job_category;
     $whereClause.=" ) ";
    }
   }
   // industry sector ends ///
  // work experience start ///
   if(tep_not_null($experience))
   {
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $work_experince=get_name_from_table(EXPERIENCE_TABLE,'id', 'min_experience',tep_db_input($explode_string[0]));
    $whereClause.=" ( jr1.work_experince = '".(int)tep_db_input($work_experince)."' ) ";
   }
   // work experience ends ///

   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   //$premium_jobseekers_query="select distinct(jah.jobseeker_id) from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." as jah where jah.start_date<=CURDATE() and jah.end_date >=CURDATE()";
   $now=date('Y-m-d H:i:s');
   $field_names="jl.jobseeker_id,jr1.resume_id,jr1.inserted,jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,j.jobseeker_privacy,jr1.availability_date,jr1.jobseeker_nationality,jr1.experience_year,jr1.objective, jr1.target_job_titles,jrt.private_notes, jrt1.point as point1 ,jrt.point as  point,jrt.admin_rate,jr1.jobseeker_photo,j.jobseeker_city,jl.jobseeker_email_address,if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,jobseeker_state) as jobseeker_state,c.country_name as jobseeker_country ";
   if(tep_not_null($keyword))
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   else
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   $whereClause.="  jr1.search_status='Yes' and jl.jobseeker_status='Yes' and j.jobseeker_cv_searchable='Yes'";
   {
   $query2 = "select distinct(jr1.resume_id) from $table_names1 where $whereClause ";
   $whereClause=" jr1.resume_id in (".$query2.")";
   $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' ) left outer join  ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) left outer join  ".COUNTRIES_TABLE." as c on (c.id=j.jobseeker_country_id) left outer join ".COUNTRIES_TABLE." as n on (n.id=jr1.jobseeker_nationality)"  ;
   $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause ";
   // echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;//exit;
   //////////////////
   ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("jobseeker_name",'jl.jobseeker_email_address');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc, j.jobseeker_last_name asc , jr1.inserted desc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_SEARCH_RESULTS);
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);
   $template->assign_vars(array('INFO_TEXT_JOBSEEKER_NAME'=>"<a href='#' class='pak_16' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
//                              'INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>"
   ));
   ///only for sorting ends

   $totalpage=ceil($x1/$higher);

   $query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher";
   $result=tep_db_query($query);
   //echo "<br>$query";//exit;
   $x=tep_db_num_rows($result);
//   echo $x;//exit;
   $pno= ceil($lower+$higher)/($higher);
   if($x > 0 && $x1 > 0)
   {
    $alternate=1;
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["resume_id"];
     $query_string1=encode_string("search_id==".$ide."==search");
     $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
					$jobseeker_education_check=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE.' as e left outer join '.EDUCATION_LEVEL_TABLE.' as el on (e.degree=el.id) ',"resume_id='".$ide."' order by start_year desc ,start_month desc","e.specialization,el.".TEXT_LANGUAGE."education_level_name as education_level_name");
					$experience_row=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE.' as ex ',"resume_id='".$ide."' order by start_year desc ,start_month desc","ex.company,ex.job_title");

					if(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo']))
     {
      $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$row['jobseeker_photo'],'','','','class="resume-result-mini-pic"');
     }
					else
					{
      $photo =defaultProfilePhotoUrl($row['jobseeker_name'],false,100,'class="resume-result-mini-pic"');
					}
     if(tep_not_null($row['availability_date']))
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 19);
     }
     else
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 30, 19);
     }
					$div_extended_values='<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
																									<tr bgcolor="#FFFFFF">
																										<td valign="top" >'.$photo.'</td>
																										<td valign="top" width="2"></td>
																										<td valign="top" width="95%">
																										 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_name']).'</td>
																												</tr>'.($row['jobseeker_privacy']==3?'
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_city']).(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']).'</td>
																												</tr>':'').((!$jobseeker_education_check)?'':'
																												<tr>
                             <td valign="top">'.tep_db_output($jobseeker_education_check['education_level_name']).(tep_not_null($jobseeker_education_check['specialization'])?' ('.tep_db_output($jobseeker_education_check['specialization']).')':'').'</td>
			                         </tr>').((!$experience_row)?'':'
																												<tr>
                             <td valign="top">'.(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :'').'</td>
			                         </tr>').'
																											</table>
																										</td>
																									</tr>
																								</table>
                     					';
     $private_notes='';
     if(tep_not_null($row['private_notes']))
     $private_notes='<a href="#" onclick="popUp(\''.tep_href_link(FILENAME_RECRUITER_PRIVATE_NOTES,'query_string='.$query_string1).'\')">'.tep_db_output(substr($row['private_notes'],0,15).'....').'</a>';
/**************-----------rating----------------*/
if(check_login('admin'))
{
 $adminedit=true;
 $row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," resume_id='".$resume_id."' and admin_rate='Y'",'point');
 $rate_it_array=array();
 for($i=1;$i<=10;$i++)
 {
  $rate_it_array[]=array("id"=>$i,"text"=>$i);
 }
 $rate_it_string='<tr><td class="resume_content1" valign="top">';
 $rate_it_string.=INFO_TEXT_CURRENT_RATE_IT.'</td><td valign="top">';
 $rate_it_string.=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'5', '', false);
 $rate_it_string.='</td>';
 $rate_it_string.='<td valign="middle" >'.tep_draw_submit_button_field('','Rate','class="btn btn-primary"').'</td></tr>';
}
if(check_login('recruiter') && $adminedit==false)
{
 $row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$resume_id."'",'point,private_notes');
 $rate_it_array=array();
 for($i=1;$i<=10;$i++)
 {
  $rate_it_array[]=array("id"=>$i,"text"=>$i);
 }
 $rate_it_string.=INFO_TEXT_CURRENT_RATE_IT.'</td><td>';
 $rate_it_string.=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'5', '', false);
 $rate_it_string.='<td>&nbsp;</td></tr>';
 $rate_it_string.='<tr><td class="resume_content1"  width="150" valign="top">'.INFO_TEXT_PRIVATE_NOTES.'</td><td>';
 $rate_it_string.=tep_draw_textarea_field('private_notes', 'soft', '60', '4', tep_not_null($row_rating['private_notes'])?$row_rating['private_notes']:'', '', '',false);
 $rate_it_string.='</td></tr><tr><td>&nbsp;</td><td valign="middle">'.(check_login("recruiter")?tep_draw_submit_button_field('','Add','class="btn btn-primary"'):'').'&nbsp;</td></tr>';
}

//////////////////////////////////////
$today     =date('Y-m-d');
if($row_check1=getAnyTableWhereData(RESUME_STATISTICS_DAY_TABLE," resume_id='".tep_db_input($ide)."' and  date='".tep_db_input($today)."'")  )
   {
	  $sql_data_array=array('date'=>$today,
	  'viewed'=>($row_check1['viewed']+1),
	  'resume_id'=>$ide);
	  
	 tep_db_perform(RESUME_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "resume_id='".tep_db_input($ide)."' and date='".tep_db_input($today)."'  ");
   }
	else
	{
	  $sql_data_array=array('date'=>$today,
	  'viewed'=>1,
 	  'resume_id'=>$ide);
	  

  	 tep_db_perform(RESUME_STATISTICS_DAY_TABLE, $sql_data_array);
   }
   /////////////////////////////////////////

/********---------------------------------------------------*/
     $template->assign_block_vars('search_resume_result', array(
		'photo'=>$photo,
		'title'=>$row['target_job_titles'],
		'contact'=>'<a class="btn btn-sm btn-text border bg-white"  href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action1=save&action=contact').'">'.INFO_TEXT_CONTACT.'</a>',
		'working'=>(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :''),
		'check_box'=> tep_draw_checkbox_field('resume_id[]',$ide,false, '','class="form-check-input" style="vertical-align: baseline;"'),
		'name' => '<a class="job_search_title" target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.tep_db_output($row['jobseeker_name']).'</a>',
		'city'     => (tep_not_null($row['jobseeker_city'])?''.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']),//ucfirst(tep_db_output($row['jobseeker_city'])),
      'email_address' =>($row['jobseeker_privacy']==3?'<a href="mailto:'.$row['jobseeker_email_address'].'">'.tep_db_output($row['jobseeker_email_address']).'</a>':'*****'),
		'totalexp'=>$row['experience_year'],//(tep_not_null($row['experience_year'])?$row['experience_year']:''),
		'objective'=>$row['objective'],//(tep_not_null($row['objective'])?$row['objective']:''),
		'view'     => '<a class="btn btn-sm btn-text border bg-white" target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.INFO_TEXT_VIEW.'</a>',
		'download' => '<a target="_blank" class="btn btn-sm btn-text border bg-white"  href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=download').'">Download</a>',
'save'=>tep_draw_form('save_form', FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string1='.$query_string1,'','post', '').tep_draw_hidden_field('action1','save_resume').'<button type="submit" class="btn btn-primary" >save</button></form>',
	'connect'=> '<a class="btn btn-sm btn-text border bg-white" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=book_mark').'">'.INFO_TEXT_CONNECT_TO_JOB.'</a>',
      'rating' => (tep_not_null($row['point'])?tep_db_output($row['point']).'<span style="color:blue"> *</span>':'').(tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':''),//get_name_from_table(JOBSEEKER_RATING_TABLE,'point', 'resume_id',tep_db_output($row['resume_id'])),
	'rate_resume'=> '<a class="btn btn-sm btn-text border bg-white" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.INFO_TEXT_RATE_RESUME.'</a>',

'rate_resume_main'=>(tep_not_null($row['point'])||tep_not_null($row['point1'])?'<a class="btn btn-sm btn-text border bg-white" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.(tep_not_null($row['point'])?tep_db_output($row['point']).'<span style="color:blue"> *</span>':'').(tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':'').'</a>':'<a class="btn btn-sm btn-text border bg-white" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.INFO_TEXT_RATE_RESUME.'</a>'),

      'private_notes' =>$private_notes,
      'inserted' => tep_date_long($row['inserted']),
      'available_status'=>$available_status,
	'maximize_mimimize'=>'<a href="#normal_resume" onclick="collapse(\'normal_'.$ide.'\')"><img src="img/plus.gif" width="9" height="9"></a>',
	'div_id'=>$ide,
	'div_extended_values'=>$div_extended_values,
      'row_selected'=>$row_selected
      ));
     $alternate++;
     $lower = $lower + 1;
    }
    $plural=($x1=="1")?TABLE_HEADING_RESUME:TABLE_HEADING_RESUMES;
				$check_link = '<td width="60%" valign="left"><div align="left"><span class="checkbox-resume">
                            <a href="#normal_resume" class="me-2" onclick="checkall()">'.tep_db_output(INFO_TEXT_SELECT_ALL).'</a> <a href="#normal_resume" class="me-2" onclick="uncheckall()">'.tep_db_output(INFO_TEXT_CLEAR_SELECTED).'</a> <a href="#normal_resume" class="" onclick="SaveSelected(\'save\')">'.tep_db_output(INFO_TEXT_SAVE_SELECTED).'</a>
                          </span> </div></td>
                         <!-- <td width="40%" valign="bottom"><a class="btn-default-custom2" href="#" target="_blank">Map View</a>
                          </td>                         -->
';
				($x > 0?$check_uncheck=$check_link:$check_uncheck='');

////*****for category**********/
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>"All Category"));
//////////////////////////////

    $template->assign_vars(array('total'=>"<span class='badge-custom2'>$x1</span> ".$plural,'rate_mark'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span><span style="color:blue">*</span><span class="resume_result3">'.INFO_TEXT_RATED_SELF.'</span>',
					'check_link'=>$check_uncheck,
					'map_view_link'=>'<a class="map_view_link" >Map View</a>',
					'search_resume_form'=>tep_draw_form('search_resume', FILENAME_RECRUITER_SEARCH_RESUME,'','post').tep_draw_hidden_field('action','search'),
					'search_resume_form_keyword'=>tep_draw_input_field('keyword',(tep_not_null($keyword)?$keyword:''),'placeholder="Search by job title" class="form-control-sm btn-job-filter mmb-12" ',false),
					'search_resume_form_location'=>LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-select-sm btn-job-filter' style='width: 175px;'","All Locations","",(tep_not_null($country)?$country:DEFAULT_COUNTRY_ID)),
					'search_resume_form_cat'=> tep_draw_pull_down_menu('industry_sector[]', $cat_array, (tep_not_null($industry_sector1)?$industry_sector1:''), 'class="form-select-sm btn-job-filter" style="width: 175px;"'),
					'search_resume_form_exp'=>experience_drop_down('name="experience" class="form-select-sm btn-job-filter"', 'Experience', '', $experience),
					'search_resume_form_button'=>tep_draw_submit_button_field('login2','Search','class="btn btn-sm btn-primary" style="padding-top: 0px;padding-bottom: 0px;min-height: 32px;"'),
					'advance_search'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">Advanced search</a>',
					'applicant_tracking'=>'<li><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">All Applicants</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT).'">Applicant Pipeline</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_APPLICANT).'">Search Applicants</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT).'">Selected Applicants</a></li>',
));
   }
   else
   {
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>"All Category"));

    $template->assign_vars(array('total'=>'No '.TABLE_HEADING_RESUME,//tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED,
                                 'result_class' =>'class="result_hide"',
                            					'map_view_link'=>'',
	'search_resume_form'=>tep_draw_form('search_resume', FILENAME_RECRUITER_SEARCH_RESUME,'','post').tep_draw_hidden_field('action','search'),
	'search_resume_form_keyword'=>tep_draw_input_field('keyword',(tep_not_null($keyword)?$keyword:''),'placeholder="Search by job title" type="text" class="form-control-applicant"',false),
	'search_resume_form_location'=>LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-select'","All Locations","",(tep_not_null($country)?$country:DEFAULT_COUNTRY_ID)),
	'search_resume_form_cat'=> tep_draw_pull_down_menu('industry_sector[]', $cat_array, (tep_not_null($industry_sector1)?$industry_sector1:''), 'class="form-select"'),
	'search_resume_form_exp'=>experience_drop_down('name="experience" class="form-control"', 'Experience', '', $experience),
	'search_resume_form_button'=>tep_draw_submit_button_field('login2','Search Now','class="btn btn-primary"'),
	'advance_search'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_SEARCH_RESUME).'">Advanced search</a>',
	'applicant_tracking'=>'<li><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">All Applicants</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT).'">Applicant Pipeline</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">Search Applicants</a></li>
                                <li><a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT).'">Selected Applicants</a></li>',

));
   }
   see_page_number();
   tep_db_free_result($result1);
   }
   {//*/fea
   $whereClause1=$whereClause."and jl.jobseeker_id in(select distinct(jah.jobseeker_id) from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." as jah where jah.start_date<=CURDATE() and jah.end_date >=CURDATE())";
   $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' ) left outer join  ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) left outer join  ".COUNTRIES_TABLE." as c on (c.id=j.jobseeker_country_id) left outer join ".COUNTRIES_TABLE." as n on (n.id=jr1.jobseeker_nationality)"  ;
   $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause1 ";
   // echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x2=$tt_row['x1'];
   //echo $x2;//exit;
$xtot=$x1+$x2;
//echo $xtot;
   //////////////////
   ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("jobseeker_name",'jl.jobseeker_email_address');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc, j.jobseeker_last_name asc , jr1.inserted desc','sort_1');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number($sort_array,$field_1,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order_1,'asc',$lower_1,'0',$higher_1,MAX_DISPLAY_SEARCH_RESULTS);
   $lower_1=$see_before_page_number_array['lower'];
   $higher_1=$see_before_page_number_array['higher'];
   $field_1=$see_before_page_number_array['field'];
   $order_1=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort_1',$sort_1);
   $template->assign_vars(array('INFO_TEXT_JOBSEEKER_NAME1'=>"<a href='#' class='pak_16' onclick=\"submit_thispage1('page1','lower_1','sort_1','".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower_1."');\"><u>".INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>"));
   ///only for sorting ends

   $totalpage_1=ceil($x2/$higher_1);
   $query = "select $field_names from $table_names where $whereClause1 ORDER BY ".$order_by_clause." limit $lower_1,$higher_1";
   $result=tep_db_query($query);
   //echo "<br>$query";//exit;
   $x=tep_db_num_rows($result);
   //echo $x;exit;
   $pno_1= ceil($lower_1+$higher_1)/($higher_1);
   if($x > 0 && $x2 > 0)
   {
    $alternate=1;
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["resume_id"];
     $query_string1=encode_string("search_id==".$ide."==search");
     $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
					$jobseeker_education_check=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE.' as e left outer join '.EDUCATION_LEVEL_TABLE.' as el on (e.degree=el.id) ',"resume_id='".$ide."' order by start_year desc ,start_month desc","e.specialization,el.".TEXT_LANGUAGE."education_level_name as education_level_name");
					$experience_row=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE.' as ex ',"resume_id='".$ide."' order by start_year desc ,start_month desc","ex.company,ex.job_title");

					if(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo']))
     {
      $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$row['jobseeker_photo'],'','','','class="candidate-picture"');
     }
					else
					{
      // $photo = '<img src="'.HOST_NAME.'img/nopic.jpg" class="candidate-picture">';
      $photo = defaultProfilePhotoUrl($row['jobseeker_name'],false,100,'class="resume-result-mini-pic"');
					}
     if(tep_not_null($row['availability_date']))
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 19);
     }
     else
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 30, 19);
     }
					$div_extended_values='
																								<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
																									<tr bgcolor="#FFFFFF">
																										<td valign="top" >'.$photo.'</td>
																										<td valign="top" width="2"></td>
																										<td valign="top" width="95%">
																										 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_name']).'</td>
																												</tr>'.($row['jobseeker_privacy']==3?'
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_city']).(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']).'</td>
																												</tr>':'').((!$jobseeker_education_check)?'':'
																												<tr>
                             <td valign="top">'.tep_db_output($jobseeker_education_check['education_level_name']).(tep_not_null($jobseeker_education_check['specialization'])?' ('.tep_db_output($jobseeker_education_check['specialization']).')':'').'</td>
			                         </tr>').((!$experience_row)?'':'
																												<tr>
                             <td valign="top">'.(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :'').'</td>
			                         </tr>').'
																											</table>
																										</td>
																									</tr>
																								</table>
                     					';
     $private_notes='';
     //if(tep_not_null($row['private_notes']))
     //$private_notes='<a href="#" onclick="popUp(\''.tep_href_link(FILENAME_RECRUITER_PRIVATE_NOTES,'query_string='.$query_string1).'\')">'.tep_db_output(substr($row['private_notes'],0,15).'....').'</a>';
     $template->assign_block_vars('search_resume_result1', array(
		'photo'=>$photo,
		'title'=>$row['target_job_titles'],
		'contact'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action1=save&action=contact').'">'.INFO_TEXT_CONTACT.'</a>',
		'working'=>(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :''),
		'check_box'=> tep_draw_checkbox_field('resume_id[]',$ide,false, '','class="form-check-input" style="vertical-align: baseline;"'),
		'name' => '<a target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.tep_db_output($row['jobseeker_name']).'</a>',
		'city'     => (tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']),//ucfirst(tep_db_output($row['jobseeker_city'])),
      //'email_address' =>($row['jobseeker_privacy']==3?'<a href="mailto:'.$row['jobseeker_email_address'].'">'.tep_db_output($row['jobseeker_email_address']).'</a>':'*****'),
		'totalexp'=>$row['experience_year'],//(tep_not_null($row['experience_year'])?$row['experience_year']:''),
		'objective'=>$row['objective'],//(tep_not_null($row['objective'])?$row['objective']:''),
		'view'     => '<a class="btn-default-custom" target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.INFO_TEXT_VIEW.'</a>',
		'download' => '<a target="_blank" class="btn-default-custom"  href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=download').'">Download</a>',
	'save'=>tep_draw_form('save_form', FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string1='.$query_string1,'','post', '').tep_draw_hidden_field('action1','save_resume').'<button type="submit" class="btn btn-primary" >save</button></form>',
	'connect'=> '<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=book_mark').'">'.INFO_TEXT_CONNECT_TO_JOB.'</a>',
      'rating' => (tep_not_null($row['point'])?tep_db_output($row['point']).'<span style="color:blue"> *</span>,':'').(tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':''),//get_name_from_table(JOBSEEKER_RATING_TABLE,'point', 'resume_id',tep_db_output($row['resume_id'])),
	'rate_resume'=> '<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'">'.INFO_TEXT_RATE_RESUME.'</a>',
    //  'private_notes' =>$private_notes,
      'inserted' => tep_date_long($row['inserted']),
      'available_status'=>$available_status,
	'maximize_mimimize'=>'<a href="#featured_resume" onclick="collapse(\'featured_'.$ide.'\')"><img src="img/plus.gif" width="9" height="9"></a>',
	'div_id'=>$ide,
	'div_extended_values'=>$div_extended_values,
      'row_selected'=>$row_selected
      ));
     $alternate++;
     $lower_1 = $lower_1 + 1;
    }
    $plural=($x2=="1")?INFO_TEXT_FEATURED_RESUME:INFO_TEXT_FEATURED_RESUMES;
				$check_link = '<span class="checkbox-resume"><a href="#featured_resume" class="resume_result3" onclick="checkall1()">'.tep_db_output(INFO_TEXT_SELECT_ALL).'</a></span> <span class="checkbox-resume"><a href="#featured_resume" class="resume_result3" onclick="uncheckall1()">'.tep_db_output(INFO_TEXT_CLEAR_SELECTED).'</a></span> <span class="checkbox-resume"><a href="#featured_resume" class="resume_result3" onclick="SaveSelected1(\'save\')">'.tep_db_output(INFO_TEXT_SAVE_SELECTED).'</a></span>';
				($x > 0?$check_uncheck=$check_link:$check_uncheck='');
    $template->assign_vars(array('total_1'=>'<span class="">'.$x2.'</span>'." ".$plural,'rate_mark_1'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span><span style="color:blue">*</span><span class="resume_result3">'.INFO_TEXT_RATED_SELF.'</span>',
					             'check_link1'=> $check_uncheck,
				                 'map_view_link1'=>'<a class="map_view_link" >Map View</a>'
                               ));
   }
   else
   {
    $template->assign_vars(array('total_1'=>'',//if there is no featured resume, it just do not display any message as if message is displayed it shows double message,
                                 'result_class_1' =>'class="result_hide"',
                            					'map_view_link1'=>''));
   }
   $list_string=see_page_number1('page1','lower_1','higher_1',$lower_1,$x2,$total_1,$pno_1,$higher_1,$totalpage_1);
   if(is_array($list_string))
   $template->assign_vars(array("page_1"=>$list_string['page'],
                                 "list_page_1"=>$list_string['list_page']));
   tep_db_free_result($result1);
   }
  }
}
//$search_resume_text='<span class="small_red"><ul><li style="text-align:justify">'.INFO_TEXT_IF_YOUR_SEARCH_RESULT.'</li></ul></span>';
//echo  $whereClause;

$minimum_rating_array=array();
$minimum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
for($i=1;$i<=10;$i++)
 $minimum_rating_array[]=array("id"=>$i,"text"=>$i);
$minimum_rating_string.=tep_draw_pull_down_menu('minimum_rating', $minimum_rating_array, $minimum_rating, 'class=form-select', false);

$maximum_rating_array=array();
$maximum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
for($j=1;$j<=10;$j++)
 $maximum_rating_array[]=array("id"=>$j,"text"=>$j);
$maximum_rating_string.=tep_draw_pull_down_menu('maximum_rating', $maximum_rating_array, $maximum_rating, 'class=form-select', false);
$hidden_fields1=tep_draw_hidden_field('action1','');
$template->assign_vars(array( 'hidden_fields' => $hidden_fields,
 'button'                  => tep_draw_submit_button_field('','Search','class="btn btn-primary"'),
 'form'                    => tep_draw_form('search', FILENAME_RECRUITER_SEARCH_RESUME,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
 'save_search'             => tep_draw_form('save_search', FILENAME_RECRUITER_SEARCH_RESUME,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','save_search'),
 'save_button'             => tep_draw_submit_button_field('','Create resume alert','class="btn btn-sm btn-primary btn-resume-search"').($action1=='save_search'?''.'':''),
 'INFO_TEXT_KEYWORD'       => INFO_TEXT_KEYWORD,
//  'INFO_TEXT_KEYWORD1'      => tep_draw_input_field('keyword', $keyword,'class="form-control"',false).INFO_TEXT_KEYWORD_EXAMPLE,
 'INFO_TEXT_KEYWORD1'      => tep_draw_input_field('keyword', $keyword,'class="form-control" placeholder=""',false),
 'INFO_TEXT_KEYWORD3'      => '<small class="d-block">'.INFO_TEXT_KEYWORD_CRITERIA.'</small> <small class="mr-3">'.tep_draw_radio_field('word1', 'Yes', 'Yes', $word1,'id=radio_word1').'<label for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label></small> <small>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').'<label for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label></small>',
 'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
 'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',
 'INFO_TEXT_MINIMUM_RATING'=> INFO_TEXT_MINIMUM_RATING,
 'INFO_TEXT_MINIMUM_RATING1'=> $minimum_rating_string,

 'INFO_TEXT_MAXIMUM_RATING'=> INFO_TEXT_MAXIMUM_RATING,
 'INFO_TEXT_MAXIMUM_RATING1'=> $maximum_rating_string,
 'INFO_TEXT_FEATURED_MEMBERS'=>INFO_TEXT_FEATURED_MEMBERS,
 'INFO_TEXT_TO'           =>INFO_TEXT_TO,

'HEADING_BASIC_INFO' => HEADING_BASIC_INFO,
'HEADING_Objective' =>HEADING_Objective,
'HEADING_WORK_HISTORY' =>HEADING_WORK_HISTORY,
'HEADING_EDUCATION' =>HEADING_EDUCATION,
'HEADING_SKILLS' =>HEADING_SKILLS,
'HEADING_LANGUAGES' =>HEADING_LANGUAGES,
'HEADING_PASTED' => HEADING_PASTED,
 'INFO_TEXT_NORMAL_MEMBERS'=> INFO_TEXT_NORMAL_MEMBERS,
 'INFO_TEXT_FIRST_NAME'    => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1'   => tep_draw_input_field('first_name', $first_name,'class="form-control"',false),
 'INFO_TEXT_LAST_NAME'     => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'    => tep_draw_input_field('last_name', $last_name,'class="form-control"',false),
 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('email_address', $email_address,'class="form-control"',false),
 'INFO_TEXT_COUNTRY'       => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'      => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name"," priority ,country_name","name='country' class='form-select'",INFO_TEXT_ALL_COUNTRIES,"",$country),
 'INFO_TEXT_NATIONALITY'   => INFO_TEXT_NATIONALITY,
 'INFO_TEXT_NATIONALITY1'  => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name"," priority ,country_name","name='nationality' class=form-select",INFO_TEXT_ALL_COUNTRIES,"",$nationality),
 'INFO_TEXT_STATE'         => INFO_TEXT_STATE,
 'INFO_TEXT_STATE1'        => tep_draw_input_field('state1', $state,'size="50"',false),
 //'INFO_TEXT_STATE1'        => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_name',"zone_name",'name="state"',"state",'',$state)." ".tep_draw_input_field('state1', $state,'size="25"',false),
 'INFO_TEXT_CITY'          => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'         => tep_draw_input_field('city', $city,'class="form-control"',false),
 'INFO_TEXT_ZIP'           => INFO_TEXT_ZIP,
 'INFO_TEXT_ZIP1'          => tep_draw_input_field('zip', $zip,'class="form-control"',false),
 'INFO_TEXT_JOB_CATEGORY'  => INFO_TEXT_JOB_CATEGORY,
 'INFO_TEXT_JOB_CATEGORY1' => get_drop_down_list(JOB_CATEGORY_TABLE,"name='industry_sector[]'  class='form-control h-100' multiple ",INFO_TEXT_ALL_JOB_CATEGORIES,"0",$industry_sector1),
 'INFO_TEXT_EXPERIENCE'    => INFO_TEXT_EXPERIENCE,
 'INFO_TEXT_EXPERIENCE1'   => experience_drop_down('name="experience" class="form-select" ', INFO_TEXT_ANY_EXPERIENCE, '', $experience),
 'INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS'=>INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS,

 'INFO_TEXT_JOBSEEKER_RATING'=> INFO_TEXT_JOBSEEKER_RATING,
 'INFO_TEXT_JOBSEEKER_PRIVATE_NOTES'=> INFO_TEXT_JOBSEEKER_PRIVATE_NOTES,

 'TABLE_HEADING_INSERTED'  => TABLE_HEADING_INSERTED,
 'TABLE_HEADING_RESUME'    => TABLE_HEADING_RESUME,
 'TABLE_HEADING_CITY'      => TABLE_HEADING_CITY,
 'TABLE_HEADING_DOWNLOAD'  => TABLE_HEADING_DOWNLOAD,
 'TABLE_HEADING_RATING'    => TABLE_HEADING_RATING,
 //'SCRIPT'                  => country_state($c_name='country',$c_d_value='All countries',$s_name='state',$s_d_value='state','zone_name',$state),
 //'INFO_TEXT_SEARCH_RESUME_TEXT'=>$search_resume_text,
 'TABLE_HEADING_AVAILABILITY'=>TABLE_HEADING_AVAILABILITY,
 'hidden_fields' => $hidden_fields,
	'hidden_fields1' => $hidden_fields1,
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
  'LEFT_HTML'=>LEFT_HTML,
 'update_message' => $messageStack->output(),
 'RESUME_JS_PATH' => $resumeDetailJsFilePath,
 ));
if(!in_array($word1,array('Yes','No')))
 $word1='Yes';
if($action=='search' || $action=='save_search')
{
 $template->pparse('search_resume_result');
}
else
{
 $template->pparse('search_resume');
}
?>