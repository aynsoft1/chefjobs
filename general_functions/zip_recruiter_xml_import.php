<?
/*
************************************************************
**********#	Name	   : Shambhu Prasad Patnaik		 #******
**********#	Company	   : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright  c) www.aynsoft.com 2017  #***********
************************************************************
*/
ini_set('max_execution_time','0');
if (!function_exists('zipRecruiter_job_importer_readFeeds')):
function zipRecruiter_job_importer_readFeeds($parameter)
{
 $zipRecruiter_content=array();
 if(!isset($parameter['url']))
 return $zipRecruiter_content;
 $url=trim($parameter['url']);

 if (function_exists('curl_init') )
 {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER ,false);
  $data = curl_exec($ch);
  $error = curl_error($ch);
  curl_close($ch);
  if($data && $error =='')
  {
   $r=json_decode($data,true);
   $results=$r['jobs'];
   $i=0;
   if($results)
   foreach($results  as $result)
   {
     $job_id      =  tep_db_prepare_input($result['id']); 
     $job_title   =  tep_db_prepare_input($result['name']); 
     $job_city    =  tep_db_prepare_input($result['city']); 
     $job_state   =  tep_db_prepare_input($result['state']); 
     $job_country =  tep_db_prepare_input($result['country']); 
     $location    =  tep_db_prepare_input($result['location']); 
     $description =  stripslashes($result['snippet']); 
     $url         =  tep_db_prepare_input($result['url']); 
     $category    =  tep_db_prepare_input($result['category']); 
     $company     =  tep_db_prepare_input($result['hiring_company']['name']); 

     $zipRecruiter_content[$i]=array(
                        'job_id'     => $job_id,
                        'job_title'  => $job_title,
                        'job_city'       => $job_city,
                        'job_state'      => $job_state,
                        'job_country'    => $job_country,
                        'job_description'=> $description,
                        'job_url'        => $url,
		                'job_location'   => $location,
		                'job_category'   => $category,
		                'job_company'    => $company,
                       );
     $i++;
    }
   }
  }
 return $zipRecruiter_content;
}
endif;
////////////////////////////////////////////////////////
function zip_recruiter_job_import($feed_ids='')
{
 $add_whereClause='';
 if(tep_not_null($feed_ids))
 $add_whereClause=" and  dd.feed_id in (".$feed_ids.") ";
 $query = "select dd.* from ".ZIP_RECRUITER_FEED_TABLE."  dd left outer join ".RECRUITER_LOGIN_TABLE."  as rl on (dd.recruiter_id=rl.recruiter_id) where dd.status='active' and rl.recruiter_id is not null  $add_whereClause   order by dd.feed_id";
 $result1=tep_db_query($query);
 while($row = tep_db_fetch_array($result1))
{
  $api_key = tep_db_prepare_input($row['api_key']);
  $recruiter_id = tep_db_prepare_input($row['recruiter_id']);
  $location     = tep_db_prepare_input($row['location']);
  $max_feed     = tep_db_prepare_input($row['max_feed']);
  $max_feed1= $max_feed;
  if($max_feed<10)
  $max_feed1 =10;

  $feed_keyword = tep_db_prepare_input($row['feed_keyword']);
  $job_duration = (int)tep_db_prepare_input($row['job_duration']);
  $feed_id      = tep_db_prepare_input($row['feed_id']);
  $import_jobs  = 0;//tep_db_prepare_input($row['import_jobs']);
  if($job_duration<=0)
  continue;

  ///////////////////////////////////////////////
  $cat_field_names="id,category_name";
  $cat_whereClause=" where id in (".$feed_keyword.")";
  $cat_query = "select $cat_field_names from ".JOB_CATEGORY_TABLE." $cat_whereClause order by  category_name  asc  ";
  $cat_result=tep_db_query($cat_query);
  $count=1;  
  while($row_cat = tep_db_fetch_array($cat_result))
  {
   $job_category    = tep_db_prepare_input($row_cat['category_name']);
   $job_industry    = tep_db_prepare_input($row_cat['id']);
   $api_url         = 'https://api.ziprecruiter.com/jobs/v1?api_key='.urlencode($api_key).'&search='.urlencode($job_category).'&location='.urlencode($location).'&radius_miles=&jobs_per_page='.$max_feed1.'&days_ago=&page=1'.$page;  
   $parameter=array('url'=>$api_url);
   $content         = zipRecruiter_job_importer_readFeeds($parameter);
   $total_records = count($content);
   $import_count    = 0;
   for($i=0;$i<$total_records && $import_count<$max_feed ;$i++)
   {
    //echo "<pre>";print_r($content);echo "</pre>";//die();
    $error         = false;
    $zr_id        = tep_db_prepare_input($content[$i]['job_id']);
    $zr_url         = tep_db_prepare_input($content[$i]['job_url']);
    $job_title     = tep_db_prepare_input($content[$i]['job_title']);
    $job_state1    = tep_db_prepare_input($content[$i]['job_state']);
    $job_city      = tep_db_prepare_input($content[$i]['job_city']);
    $job_location  = tep_db_prepare_input($content[$i]['job_location']);
    $job_country   = tep_db_prepare_input(strtolower($content[$i]['job_country']));
    $full_location =$job_city.','.$job_country;
    $result=getLocationGeoAddress('address='.urlencode($full_location));
	if(is_array($result))
	{
 	 $job_latitude  = tep_db_prepare_input($result['latitude']);
     $job_longitude = tep_db_prepare_input($result['longitude']);
	}
	else
	{
 	 $job_latitude  = 0;
     $job_longitude = 0;
	}
    $country_array = array(	'us'=>223,'au'=>13,'at'=>14,'be'=>21,'br'=>30,'ca'=>38,'dk'=>57,'fr'=>73,'de'=>81,'hk'=>96,'in'=>99,'india'=>99,'ie'=>103,'it'=>105,'mx'=>138,'nl'=>150,'nz'=>153,'pk'=>162,'sa'=>184,'sg'=>188,'za'=>193,'es'=>195,'ch'=>202,'ae'=>221,'gb'=>251,'uk'=>251,'united kingdom'=>251,'eg'=>63,'ng'=>156,'ma'=>144);
    if(array_key_exists(strtolower($job_country),$country_array ))
     $job_country1=$country_array[$job_country];
    else
     $job_country1   = 0;
    
    if($zone_check=getAnyTableWhereData(ZONES_TABLE,"zone_code='".tep_db_input(strtoupper($job_state1))."' and  zone_country_id ='".$job_country1."'",'zone_id'))
    {
     $job_state='null';
     $job_state_id=$zone_check['zone_id'];
    }
    else
    {
     $job_state=$job_state1;
     $job_state_id=0;
    }
    
    $job_short_description = strip_tags($content[$i]['job_description']);
    $job_description = stripslashes($content[$i]['job_description']);

    if(strlen($job_title)<=0)
    {
     $error=true;
    }
    if($data_check=getAnyTableWhereData(ZIP_RECRUITER_JOB_TABLE,"zr_id='".tep_db_input($zr_id)."'",'job_id'))
    {
     $error=true;
    }
    if(strlen($job_short_description)<=0)
    {
     $error=true;
    }
    $job_industry_ids=array();
    $job_industry_ids[0]=$job_industry;  
	
    if(!$error)
    {
     //////////////////////////////////
     $sql_data_array=array();
     $today=date('Y-m-d',mktime(0,0,0,date("m"),date("d"),date("Y")));
     $adv_date=date('Y-m-d');
     $expired=date('Y-m-d',mktime(0,0,0,date("m"),date("d")+$job_duration,date("Y")));
     $sql_data_array=array('job_title'     => $job_title,
                           'job_country_id'=> $job_country1,
                           'job_state'     => $job_state,
                           'job_state_id'  => $job_state_id,
                           'job_location'  => $job_city,
                           'job_short_description'=>$job_short_description,
                           'min_experience'=> 0,     
                           'job_vacancy_period'=> $max_experience,     
                           're_adv'        => $adv_date,     
                           'expired'       => $expired,     
                           'inserted'      => $today,     
                           'recruiter_user_id'=> 'null',     
                           'job_vacancy_period'=> $job_duration,     
                           'job_source'    => 'ziprecruiter',     
                           'recruiter_id'  => $recruiter_id,
                           'latitude'  => $job_latitude,
                           'longitude' => $job_longitude,
						   'job_description'=>$job_description,
							'url'=>'',						                     
                           );
     //print_r($sql_data_array);die();
     tep_db_perform(JOB_TABLE, $sql_data_array);
     $row_check=getAnyTableWhereData(JOB_TABLE,"recruiter_id='".$recruiter_id."' and job_title='".tep_db_input($job_title)."' order by job_id desc limit 0,1",'job_id');
     $job_id=$row_check['job_id'];
     $import_jobs=$import_jobs+1;
     /////////////////////////////////////
     $sql_job_array=array('job_id'=>$job_id);
     for($j=0;$j<count($job_industry_ids);$j++)
     {
      $sql_job_array['job_category_id']=$job_industry_ids[$j];
      tep_db_perform(JOB_JOB_CATEGORY_TABLE,$sql_job_array);
     }
     /////////////////////////////////////////////
     $sql_data_array_new=array();
     $sql_data_array_new['display_id']=get_job_enquiry_code($job_id);
     tep_db_perform(JOB_TABLE, $sql_data_array_new, 'update', "job_id = '" . $job_id . "'");

     $sql_data_array1=array();
     $sql_data_array1['zr_id']   = $zr_id;
     $sql_data_array1['zr_url']  = $zr_url;
     $sql_data_array1['job_id']  = $job_id;
     tep_db_perform(ZIP_RECRUITER_JOB_TABLE,$sql_data_array1);
     ////////////////////////////////////   
	 $import_count=$import_count+1;
    }
   }
  }
  tep_db_free_result($cat_result);
  $sql_data_array=array();
  $sql_data_array['import_jobs']   = $import_jobs;
  $sql_data_array['last_active']   = 'now()';
  tep_db_perform(ZIP_RECRUITER_FEED_TABLE, $sql_data_array, 'update', "feed_id = '" . $feed_id. "'");
 }
 tep_db_free_result($result1);
}
?>