<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft              #*********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_SEARCH_RESUME1);
$template->set_filenames(array('search_resume' => 'search_resume.htm','search_resume_result'=>'search_resume_result1.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'search_resume.js';
include_once(FILENAME_BODY);
$template->assign_vars(array('HEADING_TITLE'=>HEADING_TITLE));
if(isset($_POST['action']))
{
//print_r($_POST);die();
}
//print_r($_POST);
$action = (isset($_POST['action']) ? $_POST['action'] : '');


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
if(tep_not_null($_POST['experience']))
{
 $experience=$_POST['experience'];
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
     $whereClause1.=" jr1.target_job_titles like '%".tep_db_input($explode_string[$i])."%' or ";
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
    $whereClause.=" (( jrt1.point >= '".tep_db_input($minimum_rating)."' and jrt1.admin_rate = 'Y' )) ";
   }
   // minimum rating ends ///
   // maximum rating starts ///
   if(tep_not_null($maximum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('maximum_rating',$maximum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" (( jrt1.point <= '".tep_db_input($maximum_rating)."' and jrt1.admin_rate = 'Y' )) ";
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
   $now=date('Y-m-d H:i:s');
   $field_names="jl.jobseeker_id,jr1.resume_id,jr1.inserted,jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,jr1.availability_date,jrt1.point as point1 ,j.jobseeker_city,jr1.target_job_titles";
   if(tep_not_null($keyword))
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   else
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   $whereClause.="  jr1.search_status='Yes' and jl.jobseeker_status='Yes' and j.jobseeker_cv_searchable='Yes'";

   $query2 = "select distinct(jr1.resume_id) from $table_names1 where $whereClause ";
   $whereClause=" jr1.resume_id in (".$query2.")";
   $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' )"  ;
   $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause ";
   // echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;//exit;
   //////////////////
   ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("target_job_titles",'j.jobseeker_city','jrt1.point');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc,jr1.inserted desc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_SEARCH_RESULTS);
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);
   $template->assign_vars(array('TABLE_HEADING_TARGET_JOB'=>"<a href='#'  onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\">".TABLE_HEADING_TARGET_JOB.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                                'TABLE_HEADING_CITY'=>"<a href='#'  onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\">".TABLE_HEADING_CITY.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                                'TABLE_HEADING_RATING'=>"<a href='#'  onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\">".TABLE_HEADING_RATING.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
   ));
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
     $ide=$row["resume_id"];
     $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
     if(tep_not_null($row['availability_date']))
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 19);
     }
     else
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 30, 19);
     }
     $job_category=get_name_from_table(RESUME_JOB_CATEGORY_TABLE.' as r left outer join '.JOB_CATEGORY_TABLE.' as c on (r.job_category_id=c.id)','category_name', 'r.resume_id',$row['resume_id']);
     $template->assign_block_vars('search_resume_result', array(
      'row_selected'=>$row_selected,
      'name' => tep_db_output($row['target_job_titles']),
      'category' => tep_db_output($job_category),
						'city'     => ucfirst(tep_db_output($row['jobseeker_city'])),
					 'view'     => '<a target="_blank" href="'.tep_href_link(FILENAME_RECRUITER_RATES).'" class="">'.INFO_TEXT_VIEW.'</a>',
      'rating' => (tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':''),
      'available_status'=>$available_status,
      ));
     $alternate++;
     $lower = $lower + 1;
    }
    $plural=($x1=="1")?TABLE_HEADING_RESUME:TABLE_HEADING_RESUMES;
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).'&nbsp;'.INFO_TEXT_HAS_MATCHED."  <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH,'rate_mark'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span>',
                                 ));
   }
   else
   {
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED,
                                 'result_class' =>'class="result_hide"'));
   }
   see_page_number();
   tep_db_free_result($result1);
  }
}

if($action=='search')
{
 $template->assign_vars(array('hidden_fields' => $hidden_fields,
  'TABLE_HEADING_CATEGORIES'  => TABLE_HEADING_CATEGORIES,
  'TABLE_HEADING_RESUME'      => TABLE_HEADING_RESUME,
  'TABLE_HEADING_AVAILABILITY'=>TABLE_HEADING_AVAILABILITY,
  'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
  'RIGHT_HTML' => RIGHT_HTML,
  'update_message' => $messageStack->output(),
  ));
 $template->pparse('search_resume_result');
}
else
{
 $minimum_rating_array=array();
 $minimum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
 for($i=1;$i<=10;$i++)
  $minimum_rating_array[]=array("id"=>$i,"text"=>$i);
 $minimum_rating_string.=tep_draw_pull_down_menu('minimum_rating', $minimum_rating_array, $minimum_rating, 'class=form-control', false);

 $maximum_rating_array=array();
 $maximum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
 for($j=1;$j<=10;$j++)
  $maximum_rating_array[]=array("id"=>$j,"text"=>$j);
 $maximum_rating_string.=tep_draw_pull_down_menu('maximum_rating', $maximum_rating_array, $maximum_rating, 'class=form-control', false);
 if(!in_array($word1,array('Yes','No')))
 $word1='Yes';
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
//   'button'                  => tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
  'button'                  => tep_draw_submit_button_field('',''.BTN_SEARCH.'','class="btn btn-primary"'),
  'form'                    => tep_draw_form('search', FILENAME_RECRUITER_SEARCH_RESUME1,'','post').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_KEYWORD'       => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'      => tep_draw_input_field('keyword', $keyword,'size="70" class="form-control" placeholder=""',false),
//   'INFO_TEXT_KEYWORD3'      => INFO_TEXT_KEYWORD_CRITERIA.tep_draw_radio_field('word1', 'Yes', 'Yes', $word1,'id=radio_word1').'<label for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').'<label for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label>',

 'INFO_TEXT_KEYWORD3'      => '<small class="d-block">'.INFO_TEXT_KEYWORD_CRITERIA.'</small> <small class="mr-3">'.tep_draw_radio_field('word1', 'Yes', 'Yes', $word1,'id=radio_word1').'<label for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label></small> <small>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').'<label for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label></small>',

  'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
  'INFO_TEXT_MINIMUM_RATING'=> INFO_TEXT_MINIMUM_RATING,
  'INFO_TEXT_MINIMUM_RATING1'=> $minimum_rating_string,

  'INFO_TEXT_MAXIMUM_RATING'=> INFO_TEXT_MAXIMUM_RATING,
  'INFO_TEXT_MAXIMUM_RATING1'=> $maximum_rating_string,
  'INFO_TEXT_TO'            =>INFO_TEXT_TO,

  'INFO_TEXT_FIRST_NAME'    => INFO_TEXT_FIRST_NAME,
  'INFO_TEXT_FIRST_NAME1'   => tep_draw_input_field('first_name', $first_name,'class="form-control"',false),
  'INFO_TEXT_LAST_NAME'     => INFO_TEXT_LAST_NAME,
  'INFO_TEXT_LAST_NAME1'    => tep_draw_input_field('last_name', $last_name,'class="form-control"',false),
  'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
  'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('email_address', $email_address,'class="form-control"',false),
  'INFO_TEXT_COUNTRY'       => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'      => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name"," priority ,country_name","name='country' class='form-control'",INFO_TEXT_ALL_COUNTRIES,"",$country),
  'INFO_TEXT_NATIONALITY'   => INFO_TEXT_NATIONALITY,
  'INFO_TEXT_NATIONALITY1'  => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name"," priority ,country_name","name='nationality' class=form-control",INFO_TEXT_ALL_COUNTRIES,"",$nationality),
  'INFO_TEXT_STATE'         => INFO_TEXT_STATE,
  'INFO_TEXT_STATE1'        => tep_draw_input_field('state1', $state,'size="50"',false),
  'INFO_TEXT_CITY'          => INFO_TEXT_CITY,
  'INFO_TEXT_CITY1'         => tep_draw_input_field('city', $city,'class="form-control"',false),
  'INFO_TEXT_ZIP'           => INFO_TEXT_ZIP,
  'INFO_TEXT_ZIP1'          => tep_draw_input_field('zip', $zip,'class="form-control"',false),
  'INFO_TEXT_JOB_CATEGORY'  => INFO_TEXT_JOB_CATEGORY,
  'INFO_TEXT_JOB_CATEGORY1' => get_drop_down_list(JOB_CATEGORY_TABLE,"name='industry_sector[]'  class='form-control' multiple ",INFO_TEXT_ALL_JOB_CATEGORIES,"0",$industry_sector1),
  'INFO_TEXT_EXPERIENCE'    => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'   => experience_drop_down('name="experience" class="form-control" ', INFO_TEXT_ANY_EXPERIENCE, '', $experience),
  'hidden_fields' => $hidden_fields,
  'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
  'RIGHT_HTML' => RIGHT_HTML,
 'LEFT_HTML'=>LEFT_HTML,

  'update_message' => $messageStack->output(),
  ));
 $template->pparse('search_resume');
}
?>