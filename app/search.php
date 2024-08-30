<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
$action = (isset($_POST['act']) ? $_POST['act'] : '');
if ($action=='search') 
{
 $keyword  = tep_db_prepare_input($_POST['keyword']);
 $country  = (int) tep_db_prepare_input($_POST['country']);
 $state    = tep_db_prepare_input($_POST['state']);
 $location = tep_db_prepare_input($_POST['location']);
 $job_category = tep_db_prepare_input($_POST['job_category']);
 $now=date('Y-m-d H:i:s');

 if(isset($_POST['start_index']) && is_numeric($_POST['start_index']))
  $start_index = (int)tep_db_prepare_input($_POST['start_index']);
	else 
 	$start_index =	0;

	if(tep_not_null($keyword)  && $keyword !='keyword' ) //   keyword starts //////
 {
	 $keyword = preg_replace(array("'[\s]+'"),array (" "), $keyword);
		if(isset($_POST['word']))
  $word  = tep_db_prepare_input($_POST['word']);
		else
  $word  = 'No';
		if($word=='Yes')
		{
	  $explode_string=explode(' ',$keyword);
		}
	 else
  $explode_string=array('0'=>$keyword);
  $total_keys = count($explode_string);
		for($i=0;$i<$total_keys;$i++)
		{
   if(strlen($explode_string[$i])< 3 or strtolower($explode_string[$i])=='and')
		 {
    unset($explode_string[$i]);
		 }
		}
		sort($explode_string);
  $total_keys = count($explode_string);
		if($total_keys>0)
		{
   $whereClause.='(';
   for($i=0;$i<$total_keys;$i++)
		 {
    $whereClause.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
    $whereClause.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
   }
   $whereClause=substr($whereClause,0,-4);
   $whereClause.=" ) ";
		}
 }
 if(tep_not_null($country) && $country > 0)
	{
	 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
		$whereClause.=" j.job_country_id ='".tep_db_input($country)."'";
 }
 if(tep_not_null($state))
	{
	 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
 	if($check_state = getAnyTableWhereData(ZONES_TABLE, " zone_name  = '" . tep_db_input($state) . "'", "zone_id"))
		{
 		$whereClause.=" (j.jobseeker_state ='".tep_db_input($state)."' or j.jobseeker_state_id ='".tep_db_input($check_state['zone_id'])."' )";
		}
		else
		{
			$whereClause.=" (j.jobseeker_state ='".tep_db_input($state)."')";
  }
	}
 if(tep_not_null($location))
	{
	 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
 	if($check_state = getAnyTableWhereData(ZONES_TABLE, " zone_name  = '" . tep_db_input($location) . "'", "zone_id"))
		{
 		$whereClause.=" (j.job_location like '%".tep_db_input($location)."%' or j.job_state ='".tep_db_input($location)."' or j.job_state_id='".tep_db_input($check_state['zone_id'])."' )";
		}
		else
		{
			$whereClause.=" (j.job_location like '%".tep_db_input($location)."%' or j.job_state ='".tep_db_input($location)."')";
  }
	}
 if(tep_not_null($job_category) && $job_category> 0)
	{
	 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
  $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$job_category.")"; 
		$whereClause.="(j.job_id in (".$whereClause_job_category. " ))";
	}
 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
 $whereClause.=" rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";

 $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id )  left outer join '.ZONES_TABLE.' as z on (z.zone_id = j.job_state_id ) left outer join  '.COUNTRIES_TABLE.' as c on (c.id =j.job_country_id) ';
 $table_names1=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id )  ';
	$field_names='j.job_id,j.job_title,j.job_reference, j.re_adv,j.expired,j.job_short_description, if(j.job_state_id,z.zone_name,j.job_state) as job_state,j.job_location, j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_type,j.expired,c.country_name,r.recruiter_company_name,j.job_source,j.post_url,j.url,j.job_featured';
 $jobs=array();

 $query1 = "select count(j.job_id) as x1 from $table_names1 where $whereClause ";
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];
	if($x1>0)
	{
		$higher =20;
		$lower = $start_index;
		$i = $start_index;
  $query = "select $field_names from $table_names where $whereClause order by  j.job_source , j.re_adv , j.job_featured  limit $lower,$higher";
  $result=tep_db_query($query);
  //echo "<br>$query";//exit;
  $x=tep_db_num_rows($result);
  while($row = tep_db_fetch_array($result))
		{
			$i++;
   $ide=$row["job_id"];
			$title_format=encode_category($row['job_title']);
			$job_link = tep_href_link(PATH_TO_MOBILE.$ide.'/'.$title_format.'.html');

   $query_string=encode_string("job_id=".$ide."=job_id");
   $job_apply_link = tep_href_link(PATH_TO_MOBILE.FILENAME_APPLY_NOW,'query_string='.$query_string);

			$post_url='';
			if($row['post_url']=='Yes')
			{
				$post_url=trim($row['url']);
				if(substr($post_url,0,4)!='http')
				$post_url='http://'.$post_url;
			}
 		$job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',$ide);
	 	$job_category =(($job_category_ids !='0' && $job_category_ids!='')?get_name_from_table(JOB_CATEGORY_TABLE,'category_name', 'id', $job_category_ids):'');
   $job_description = tep_db_output($row['job_short_description']);
			$jobs[]=array(
																		'job_id'       => $ide,
																		'job_title'    => tep_db_output($row['job_title']),
																		'job_reference'=> tep_db_output($row['job_title']),
																		'job_country'  => tep_db_output($row['country_name']),
																		'job_state'    => tep_db_output($row['job_state']),
																		'job_location' => tep_db_output($row['job_location']),
																		'job_salary'   => tep_db_output($row['job_salary']),
																		'job_industry' => tep_db_output($job_category),
																		'job_description' => $job_description,
																		'job_experience'=> tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
																		'job_company'  => tep_db_output($row['recruiter_company_name']),
																		'job_apply_url'=> ($post_url=='')?$job_apply_link:$post_url,
																		'job_apply_allow'=> ($post_url=='')?'Yes':'No',				
																		'job_link'     => $job_link,
																		'job_guid'     => $job_link,
																		'job_inserted' => tep_db_output($row['re_adv']),
																		'job_expired' => tep_db_output($row['expired']),
																		'job_featured' => tep_db_output($row['job_featured']),
																		);


		}
  tep_db_free_result($result);
	}
 tep_db_free_result($result1);

$output = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
$output .='<jobs>'."\n";
$output .='<title>'.tep_db_output(SITE_TITLE).' Search  Result</title>'."\n";
$output .='<status>success</status>'."\n";
$output .='<total>'.$x1.'</total>'."\n";
//echo $x;die();

if($x > 0)
{
 $output .='<start>'.($start_index+1).'</start>'."\n";
 $output .='<end>'.($i).'</end>'."\n";
foreach($jobs as $data )
{
 $job_inserted=		$data['job_inserted'];
	$date_array=explode('-',$job_inserted);
 $job_inserted=  date("r", mktime(0, 0, 0, $date_array[1],$date_array[2], (int)$date_array[0]));
 $job_expired=		$data['job_expired'];
	$date_array=explode('-',$job_expired);
 $job_expired=  date("r", mktime(0, 0, 0, $date_array[1],$date_array[2], (int)$date_array[0]));

	$output .='<job>'."\n";
 $output .= '<jobID>'.$data['job_id'].'</jobID>'."\n";
 $output .= '<title>'.$data['job_title'].'</title>'."\n";
 $output .= '<company>'.$data['job_industry'].'</company>'."\n";
 $output .= '<reference>'.$data['job_reference'].'</reference>'."\n";
 $output .= '<country>'.$data['job_country'].'</country>'."\n";
 $output .= '<state>'.$data['job_state'].'</state>'."\n";
 $output .= '<location>'.$data['job_location'].'</location>'."\n";
 $output .= '<salary>'.$data['job_salary'].'</salary>'."\n";
 $output .= '<industry>'.$data['job_industry'].'</industry>'."\n";
 $output .= '<description>'.$data['job_description'].'</description>'."\n";
 $output .= '<experience>'.$data['job_experience'].'</experience>'."\n";
 $output .= '<industry>'.$data['job_industry'].'</industry>'."\n";
 $output .= '<inserted>'.$job_inserted.'</inserted>'."\n";
 $output .= '<expired>'.$job_expired.'</expired>'."\n";
 $output .= '<apply_allow>'.$data['job_apply_allow'].'</apply_allow>'."\n";
 $output .= '<apply_url>'.$data['job_apply_url'].'</apply_url>'."\n";

 $output .= '<featured>'.$data['job_featured'].'</featured>'."\n";
 $output .= '<url>'.$data['job_link'].'</url>'."\n";
 $output .='</job>'."\n";
}
}
$output .='</jobs>';
header('Content-Type: text/xml'); 
echo $output ;
}
else
{
 header('Content-Type: text/xml'); 
 $message='<error>'."\n";
 $message .='<status>error</status>'."\n";
 $message .='<message>invalid Action</message>'."\n";
	$message.='</error>'; 	
	echo $message;
}
?>