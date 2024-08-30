<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBFAIR);
$template->set_filenames(array('jobfair' => 'jobfair.htm', // it shows main article through article.php
                               'jobfair2' => 'jobfair2.htm', //page displays through jobfair.php
							   ));
include_once(FILENAME_BODY);

$action      = tep_db_prepare_input($_GET['action']);
$jobfair_seo = tep_db_prepare_input($_GET['jobfair_seo']);
//print_r($_GET);
$hidden_fields='';
$jobfair_id = tep_db_prepare_input($_GET['jobfair_id']);
$page_no    = tep_db_prepare_input($_GET['page']);
$now=date("Y-m-d");//date("Y-m-d H:i:s");
if(tep_not_null($jobfair_seo))
{
 if($row_check=getAnyTableWhereData(JOBFAIR_TABLE,"jobfair_seo_name='".tep_db_input($jobfair_seo)."'","id"))
 {
  $jobfair_id =$row_check['id'];
 }
 else
  tep_redirect(tep_href_link(FILENAME_JOBFAIR));
}

////////////////////////////////////////////////////////////////////////////////////////////////////
if($jobfair_id=="")
{
 $jobfair_view_array=array();
 $jobfair_view_array[]=array('id'=>'','text'=>INFO_TEXT_SORT_BY);
 $jobfair_view_array[]=array('id'=>'1a','text'=>INFO_TEXT_START_ASC);
 $jobfair_view_array[]=array('id'=>'1b','text'=>INFO_TEXT_START_DESC);
 $jobfair_view_array[]=array('id'=>'2a','text'=>INFO_TEXT_NAME_ASC);
 $jobfair_view_array[]=array('id'=>'2b','text'=>INFO_TEXT_NAME_DESC);
 $jobfair_view_array[]=array('id'=>'3a','text'=>INFO_TEXT_END_ASC);
 $jobfair_view_array[]=array('id'=>'3b','text'=>INFO_TEXT_END_DESC);

 $sort_array=array("jf.jobfair_begindate","jf.jobfair_title","jf.jobfair_enddate");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'jf.jobfair_begindate asc');
 $order_by_clause=$obj_sort_by_clause->return_value;

 $jobfair_query_raw = "select jf.*  from ".JOBFAIR_TABLE." as jf where jf.jobfair_status='Yes' and jf.jobfair_enddate>='".$now."' order by ".$order_by_clause;
 //echo $jobfair_query_raw."<br>";
 $jobfair_split = new splitPageResults($page_no, MAX_DISPLAY_JOBFAIRS, $jobfair_query_raw, $jobfair_query_numrows);
 $jobfair_query = tep_db_query($jobfair_query_raw);
 if(tep_db_num_rows($jobfair_query) > 0)
 {
  $alternate=0;
  while ($jobfair = tep_db_fetch_array($jobfair_query))
  {
   $ide      = $jobfair["id"];
   $jobfair_seo_name = $jobfair["jobfair_seo_name"].'-jobfair';
	$jf_short_desc=((strlen($jobfair['jobfair_short_description'])<320)?$jobfair['jobfair_short_description']:substr($jobfair['jobfair_short_description'],0,270)."..");

   $jobfair_image='';
   if(tep_not_null($jobfair["jobfair_logo"]) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_JOBFAIR_LOGO.$jobfair["jobfair_logo"]))
   $jobfair_image=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_JOBFAIR_LOGO.$jobfair["jobfair_logo"].'','','','','class="img-fluid img-hover img-thumbnail mini-profile-img mr-3"');
	$jobfair_url=tep_href_link(get_display_link($ide,$jobfair_seo_name));
   $template->assign_block_vars('jobfair', array(
   'jobfair_name' =>'<a href="'.$jobfair_url.'">'.tep_db_output($jobfair['jobfair_title']).'</a>',
	'jobfair_location'=>tep_db_output($jobfair["jobfair_location"]),
	'jobfair_desc'=>$jf_short_desc,//nl2br(stripslashes(strip_tags($jobfair['jobfair_short_description']))),
    'jobfair_image' =>$jobfair_image,
	'startdate'=>tep_db_output(formate_date($jobfair["jobfair_begindate"],'d-M-Y')),
	'enddate'=>tep_db_output(formate_date($jobfair["jobfair_enddate"],'d-M-Y')),
	'more_jobfair'  =>'<a href="'.$jobfair_url.'">'.INFO_TEXT_MORE.'&gt;&gt;</a>',
    ));
$alternate++;
  }
  @tep_db_free_result($jobfair_query);
 }
}
else if($jobfair_id!="")
{
 if(!$row_check=getAnyTableWhereData(JOBFAIR_TABLE,"id=".tep_db_input($jobfair_id)." and jobfair_status='Yes'","*"))
 {
   $messageStack->add_session(INFO_TEXT_SORRY_NO_JOBFAIR, 'error');
	tep_redirect(FILENAME_JOBFAIR);
 }
 else
 {
  $jobfair_id=$row_check['id'];
  $hidden_field=tep_draw_hidden_field('jobfair_id',$row_check['id']);
  $email_form=tep_draw_form("email_form",FILENAME_EMAIL_JOBFAIR,'','post').$hidden_field;
  $email="<a href='#' onclick=\"document.email_form.submit();\" class='email'>".INFO_TEXT_EMAIL."</a>";
  $email_button="<a href='#' onclick=\"document.email_form.submit();\" class='email'><img src='img/send_to_friend.gif'/></a>";
  $jobfair_title=tep_db_output($row_check['jobfair_title']);
$jobfair_location=tep_db_output($row_check['jobfair_location']);
$jobfair_venue=stripslashes(tep_db_output($row_check['jobfair_venue']));
$jobfair_startdate=tep_db_output(formate_date($row_check["jobfair_begindate"],'d-M-Y'));
  $jobfair_description=stripslashes($row_check['jobfair_description']);
if(tep_not_null($row_check["jobfair_logo"]) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_JOBFAIR_LOGO.$row_check["jobfair_logo"]))
  $jobfair_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_JOBFAIR_LOGO.$row_check["jobfair_logo"].'','','','','class="img-fluid mr-4 img-thumbnail img-hover extra-large-profile-img"');
///******replacing youtube link with embed code****/////////////////
 $jobfair_video_link=$row_check["jobfair_video"];
 $search_video='/youtube\.com\/watch\?v=([a-zA-Z0-9]+)/smi';
 $replace_video= 'youtube.com/embed/$1';
 $embed_code = preg_replace($search_video,$replace_video,$jobfair_video_link);//preg_replace("/watch\?v=/i", 'embed/', $link);
  $jobfair_video='<iframe width="100%" height="315" src="'.$embed_code.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
///////***************/////////////////////
$jobfair_googlemap_url='<iframe src="'.$row_check["jobfair_googlemap_url"].'" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>';
  $jobfair_short_desc=stripslashes($row_check['jobfair_short_description']);



/*****************display of company name codeing begins*********************************/
///*********************************************************************************/////

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

 $see_before_page_number_array=see_before_page_number123($sort_array,$field,'job_title',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_DIRECTORY_RESULT);
 //$lower=
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $totalpage=ceil($x1/$higher);
$table_names=JOB_JOBFAIR_TABLE.' as jjf,'.JOB_TABLE.' as j,'.RECRUITER_TABLE.' as r,'.RECRUITER_JOBFAIR_TABLE.' as rjf';
$whereClause="jjf.jobfair_id=".$jobfair_id." and jjf.job_id=j.job_id and j.recruiter_id=r.recruiter_id and r.recruiter_id=rjf.recruiter_id and rjf.approved='Yes' and j.add_jobfair='Yes'";//
$field_names="distinct(jjf.jobfair_id),jjf.job_id, j.job_salary, j.job_location, j.job_country_id, r.recruiter_company_name, rjf.recruiter_id,r.recruiter_logo, j.job_title,j.max_experience,j.job_skills, j.min_experience";
$query = "select $field_names from $table_names where $whereClause ORDER BY  $field $order limit $lower,$higher";//order by rjf.inserted desc limit 0,12" ;
//echo $query;
 $result=tep_db_query($query);//echo "<br>$query";//exit;
 $x=tep_db_num_rows($result);//echo $x;exit;
 $pno= ceil($lower+$higher)/($higher);

$job_display=' 
                        <div class="row">

';

 $link_array=array();
	if($x > 0 && $x1 > 0)
 {
  $alternate=1;
  $company_name1_old="";

/////////////////////////////
  while($row =  tep_db_fetch_array($result))
  {
 $jobfair_id=$row["jobfair_id"];
$recruiter_id=$row["recruiter_id"];
$job_id=$row['job_id'];

$company_name1=strtoupper(substr($row["recruiter_company_name"],0,1));

$company_logo=$row['recruiter_logo'];

if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
{
	 $company_logo="<a href='".tep_href_link(FILENAME_JOBFAIR_JOBS,'query_string='.$query_string.'&jfid='.$jobfair_id)."' class='blue'>".tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=175")."</a>";
	// $company_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=175");

}
else $company_logo='';

   $title="";
   $company_name="";
   if($company_name1!=$company_name1_old || $company_name1_old=='')
   {
    $title="<a id='".tep_db_output($company_name1)."'>".tep_db_output($company_name1)."</a>";
    $link_array[]=$company_name1;
   }

/////---calculate no of jobs posted by this employer---///
	$jobs_query=tep_db_query("select distinct job_id from " . JOB_JOBFAIR_TABLE." where recruiter_id='".$recruiter_id."' and jobfair_id='".$jobfair_id."'" );
	$no_of_jobs= tep_db_num_rows($jobs_query);
///-------------------------------------------////

   $email_id=$row["recruiter_email_address"];
 $query_string=encode_string("recruiter_id=".$recruiter_id."=recruiter_id");

   $company_name="<a href='".tep_href_link(FILENAME_JOBFAIR_JOBS,'query_string='.$query_string.'&jfid='.$jobfair_id)."' class='blue'>".tep_db_output($row['recruiter_company_name'])."</a> ";

   $job_title='<a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format)).'" target="_blank" class="blue">'.tep_db_output($row['job_title'])."</a> ";

/*************************/
$country=get_name_from_table(COUNTRIES_TABLE, 'country_name', 'id',tep_db_output($row['job_country_id']));
 $location=tep_db_output($row['job_location']);
 $job_location=tep_not_null($location)?"$location, $country":"$country";
/****************************/

$job_experience=calculate_experience(tep_db_output($row['min_experience']),tep_db_output($row['max_experience']));

//$no_of_jobs=($no_of_jobs==0?'':$no_of_jobs);
//////////////////////////////////////
$job_display.='	<div class="col-md-3 mb-4">
				<div class="card">
				<div class="card-body">
				  <div class="mini-profile-img2 mb-2">'.$company_logo.'</div>
				  <h6 class="mb-2">'.$job_title.'</h6>
				  <div class=""><i class="fa fa-building faicons" aria-hidden="true"></i> '.$company_name.'</div>
				  <div class=""><i class="fa fa-map-marker faicons" aria-hidden="true"></i> '.$job_location.'</div>
				  <div class="exp">'.$job_experience.'</div>
				  <div class="">'.tep_db_output($row['job_salary']).'</div>
                  <div class="">'.tep_db_output($row['job_skills']).'</div>
				</div>
				</div>
				</div>
';

///////////////////////////////////////

   $alternate++;

   $company_name1_old=$company_name1;
   $lower = $lower + 1;

if($alternate%4==0)
$job_display.='
                    
                    
                        ';

  }   ///while end

$job_display.='</div>';

  see_page_number();
  $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAVE."  <span  class='red'>$x1</span> ".INFO_TEXT_COMPANY_IN_DIRECTORY));
 }
 else
  {
   $template->assign_vars(array('total'=>INFO_TEXT_NO_COMPANY_DIRECTORY));
  }
 tep_db_free_result($result);
 tep_db_free_result($result1);

/* $header_link='<div class="container">
                  <nav>
                     <ul class="pagination">';
 for($i='A';$i!='AA';$i++)
 {
  if(in_array($i,$link_array))
   $header_link.='<li><a href="#'.$i.'" class="blue">'.$i.'</a></li>';
  else
   $header_link.='<li class="disabled"><span class="hint--bottom hint--always" data-hint="Disabled button when no record to show">'.$i.'</li>';
 }
	$header_link.='</ul>
                  </nav>
               </div>';
*/
// echo $header_link=substr($header_link,0,-7);
/****************************************************************************************/
 }
}
///////////////////////////
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_CATEGORIES));
$template->assign_vars(array(
/*-----------------------SEARCH CODE---------------------------------------------------------*/
'job_search_form'=>tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search'),
'key'=>tep_draw_input_field('keyword','','class="form-control form-control-custom" placeholder="'.INFO_KEYWORD.'"',false),
'jobfair'=> LIST_TABLE(JOBFAIR_TABLE,"jobfair_title","jobfair_begindate","name='jobfair' class='form-control form-control-custom2'",INFO_ALL_JOBFAIR),
'industry_sector'=> tep_draw_pull_down_menu('job_category[]', $cat_array, '', 'class="form-control form-control-custom2"'),
'search_button'=> '<button type="submit"  class="btn btn-danger btn-custom btn-block"><span class="glyphicon glyphicon-search"></span> '.INFO_BUTTON_SEARCH.' </button>',
/********************************  SEARCH CODE ENDS********************************************* */

 'HEADING_TITLE'=>HEADING_TITLE,
  'INFO_TEXT_HEADER_LINK' => $header_link,
  'form'                  => tep_draw_form('company_search', FILENAME_JOBSEEKER_COMPANY_PROFILE,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('company_name',''),
 'hidden_fields'=>$hidden_fields,
'JOB_DISPLAY'=>$job_display,
'INFO_TITLE_DOCUMENT'=>INFO_TITLE_DOCUMENT,
'INFO_TITLE_DOCUMENT_DESC1'=>INFO_TITLE_DOCUMENT_DESC1,
'INFO_TITLE_DOCUMENT_DESC2'=>INFO_TITLE_DOCUMENT_DESC2,
'PARTNERS'=>$partnerslogos,
'total_fair'=>$alternate,
	'email_article' =>$email_form,
	'email_button' =>$email_button,
	'email'        =>$email,
	'print_image'        =>'<a href="#" class="email" onclick="popUp(\''.tep_href_link(FILENAME_JOBFAIR,tep_get_all_get_params().'action=print&jobfair_id='.$jobfair_id).'\')"><img src="img/print.gif"/></a>',
	'print'              =>'<a href="#" class="email" onclick="popUp(\''.tep_href_link(FILENAME_JOBFAIR,tep_get_all_get_params().'action=print&jobfair_id='.$jobfair_id).'\')" class="article_sub_title">'.INFO_TEXT_PRINT.'</a>',
 'jobfair_title'=>$jobfair_title,
 'jobfair_short_desc'=>$jobfair_short_desc,
 'jobfair_logo'=>$jobfair_logo,
 'jobfair_location'=>$jobfair_location,
 'jobfair_startdate'=>$jobfair_startdate,
 'jobfair_venue'=>$jobfair_venue,
 'jobfair_description'=>$jobfair_description,
 'jobfair-video'=>($jobfair_video_link==''?'':'<td width="20%">'.$jobfair_video.'</td>'),
 'jobfair-map-url'=>$jobfair_googlemap_url,
'VIEW_ALL_JOBS'=>tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('jobfair',$jobfair_id).'<button class="btn btn-sm btn-primary" type="submit">View All Jobs</button></form>',
 'HEADING_TITLE_ALL_FAIR'=>HEADING_TITLE_ALL_FAIR,
 'INFO_TEXT_LATEST_JOBFAIR'     => INFO_TEXT_LATEST_JOBFAIR,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'INFO_TEXT_PRINT_JOBFAIR'=>INFO_TEXT_PRINT_JOBFAIR,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));

if($jobfair_id=="")
{
 $template->assign_vars(array(
   'sort_jobfair'=>tep_draw_pull_down_menu('sort', $jobfair_view_array, $_GET['sort'],'onchange="document.form1.submit();" class="form-select"'),
  'count_rows'=>$jobfair_split->display_count($jobfair_query_numrows, MAX_DISPLAY_JOBFAIRS, $page_no, TEXT_DISPLAY_NUMBER_OF_JOBFAIRS),
  'no_of_pages'=>$jobfair_split->display_links($jobfair_query_numrows, MAX_DISPLAY_JOBFAIRS, MAX_DISPLAY_PAGE_LINKS, $page_no,tep_get_all_get_params(array('page'))),
  'HEADING_TITLE_ALL_FAIR'=>HEADING_TITLE_ALL_FAIR,
  ));
 $template->pparse('jobfair2');
}
else if($jobfair_id!="")
{
$template->pparse('jobfair');
}
?>
