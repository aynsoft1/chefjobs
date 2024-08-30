<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik		 #******
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
ini_set('max_execution_time','0');
if (!function_exists('usaJobsApiUrl')) :
function usaJobsApiUrl($parameters=array())
{
 $keyword        = tep_db_prepare_input($parameters['keyword']);
 $job_title      = tep_db_prepare_input($parameters['job_title']);
 $location       = tep_db_prepare_input($parameters['location']);
 $feed_location  = tep_db_prepare_input($parameters['feed_location']);
// $feed_country   = tep_db_prepare_input($parameters['feed_country']);
 $job_type       = tep_db_prepare_input($parameters['job_type']);
 $sort_by        = tep_db_prepare_input($parameters['sort_by']);
 $order_by       = tep_db_prepare_input($parameters['order_by']);
 $max_feed       = tep_db_prepare_input($parameters['max_feed']);

 $page=1;	
 $api_url ='https://data.usajobs.gov/api/search';
 $url_parameter=array();
 $url_parameter['keyword']=$keyword;
 
 if(tep_not_null($location))
 $url_parameter['LocationName']=$location;

 if(tep_not_null($job_title))
 $url_parameter['PositionTitle']=$job_title;

 if(tep_not_null($job_type))
 $url_parameter['PositionScheduleTypeCode']=$job_type;

 if(tep_not_null($sort_by))
 {
  $url_parameter['SortField']=$sort_by;
  $url_parameter['SortDirection']=$order_by;
 }
 if(tep_not_null($max_feed))
 $url_parameter['ResultsPerPage']=$max_feed;

 foreach ($url_parameter as $name=>$value) 
 {
  $name = str_replace("%7E", "~", rawurlencode($name));
  $value = str_replace("%7E", "~", rawurlencode($value));
  $cb_query[] = $name."=".$value;
 }
 $url_parameter= implode("&", $cb_query);
 $api_url  = $api_url."?".$url_parameter;
 return $api_url; 
}
endif;

if (!function_exists('usaJobsReadFeeds')):
function usaJobsReadFeeds($parameter)
{
 $usajobs_content=array();
 if(!isset($parameter['url']))
 return $usajobs_content;
 $url=trim($parameter['url']);
 $header=$parameter['header'];

 if (function_exists('curl_init') )
 {
  $ch = curl_init();
 // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER ,false);
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_HEADER, 0);
 
  

  $data = curl_exec($ch);
 // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  $info = curl_getinfo($ch);
  $error = curl_error($ch);
  curl_close($ch);
  if($data && $error =='')
  {
   $r=json_decode($data,true);
   $results=$r['SearchResult'];
   $i=0;
   if($results['SearchResultCount']>0)
   foreach($results['SearchResultItems']  as $result)
   {
     $job_id        =  tep_db_prepare_input($result['MatchedObjectId']); 
     $job_title     =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionTitle']); 
     $job_city      =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionLocation'][0]['CityName']); 
     $job_state     =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionLocation'][0]['CountrySubDivisionCode']); 
     $job_longitude =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionLocation'][0]['Longitude']); 
     $job_latitude  =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionLocation'][0]['Latitude']); 
     $job_country   =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionLocation'][0]['CountryCode']); 
     $qualification =  tep_db_prepare_input($result['MatchedObjectDescriptor']['QualificationSummary']); 
     $description   =  tep_db_prepare_input($result['MatchedObjectDescriptor']['UserArea']['Details']['JobSummary']); 
     $url           =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionURI']); 
     $company       =  tep_db_prepare_input($result['MatchedObjectDescriptor']['OrganizationName']); 
     $job_type      =  tep_db_prepare_input($result['MatchedObjectDescriptor']['PositionSchedule'][0]['Name']); 
    
     $usajobs_content[$i]=array(
                        'job_id'         => $job_id,
                        'job_title'      => $job_title,
                        'job_city'       => $job_city,
                        'job_state'      => $job_state,
                        'job_longitude'  => $job_longitude,
                        'job_latitude'   => $job_latitude,
                        'job_country'    => $job_country,
                        'job_description'=> $description,
                        'job_url'        => $url,
		                'job_company'    => $company,
 		                'job_qualification' => $qualification,
                        'job_type'       => $job_type,
                        );
     $i++;
    }
   }
  }
 return $usajobs_content;
}
endif;
 
////////////////////////////////////////////////////////
function usajobs_job_import($feed_ids='')
{
 $add_whereClause='';
 if(tep_not_null($feed_ids))
 $add_whereClause=" and  dd.feed_id in (".$feed_ids.") ";
 $query = "select dd.* from ".USAJOBS_FEED_TABLE."  dd left outer join ".RECRUITER_LOGIN_TABLE."  as rl on (dd.recruiter_id=rl.recruiter_id) where dd.status='active' and rl.recruiter_id is not null  $add_whereClause   order by dd.feed_id";
 $result=tep_db_query($query);
 while($row = tep_db_fetch_array($result))
 { 
  $user_agent   = tep_db_prepare_input($row['user_agent']);
  $authorization_key   = tep_db_prepare_input($row['authorization_key']);
  $recruiter_id = tep_db_prepare_input($row['recruiter_id']);
  $search_parameters   = tep_db_prepare_input($row['search_parameters']);
  $parametes         = json_decode($search_parameters,true);
  $feed_keyword  = tep_db_prepare_input($parametes['job_categories']);
  $feed_job_title = tep_db_prepare_input($parametes['job_title']);
  $feed_location = tep_db_prepare_input($parametes['location']);
  $feed_job_type = tep_db_prepare_input($parametes['job_type']);
  $sort_by       = tep_db_prepare_input($parametes['sort_by']);
  $sort_order    = tep_db_prepare_input($parametes['order_by']);
  $max_feed      = tep_db_prepare_input($parametes['max_feed']);
  $max_feed1= $max_feed;
  if($max_feed<25)
  $max_feed1 =25;

  $job_duration  = tep_db_prepare_input($parametes['job_duration']);   
  $feed_id       = tep_db_prepare_input($row['feed_id']);
  $import_jobs   = 0;//tep_db_prepare_input($row['import_jobs']);

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
   $parameter       = array('keyword'=>$job_category,'job_title'=>$feed_job_title,'location'=>$feed_location,'job_type'=>$feed_job_type,'sort_by'=>$sort_by,'order_by'=>$order_by,'max_feed'=>$max_feed1);
   $feed_url        = usaJobsApiUrl($parameter);
   $parameter_array = array('url'=>$feed_url,'header'=>array('Host:data.usajobs.gov','User-Agent:'.$user_agent,'Authorization-Key:'.$authorization_key));
   $content         = usaJobsReadFeeds($parameter_array);
   //echo "<pre>";   print_R($content);   echo "</pre>";die();
   $total_records = count($content);
   $import_count    = 0;
   for($i=0;$i<$total_records && $import_count<$max_feed  ;$i++)
   {
    $error         = false;
    $usajobs_id    = tep_db_prepare_input($content[$i]['job_id']);
    $usajobs_url   = tep_db_prepare_input($content[$i]['job_url']);
    $job_title     = tep_db_prepare_input($content[$i]['job_title']);
    $job_state1    = tep_db_prepare_input($content[$i]['job_state']);
    $job_city      = tep_db_prepare_input($content[$i]['job_city']);
    $job_latitude  = tep_db_prepare_input($content[$i]['job_latitude']);
    $job_longitude = tep_db_prepare_input($content[$i]['job_longitude']);
    $job_country   = tep_db_prepare_input($content[$i]['job_country']);
    $job_type      = tep_db_prepare_input($content[$i]['job_type']);
    $job_qualification  = tep_db_prepare_input($content[$i]['job_qualification']);
    $job_description    = tep_db_prepare_input($content[$i]['job_description']);
	if(tep_not_null($job_qualification))
    $job_description ='<b>Qualification :</b>'.$job_qualification."\n\n<br><br>".$job_description;
 
     
     $job_country   = 223;
    
    ////////////////////////
    $job_type_array = array('full-time'=>1,'part-time'=>2,'contract'=>3,'internship'=>6,'temporary'=>5);
     if(array_key_exists(strtolower($job_type),$job_type_array))
     $job_type1 =$job_type_array[strtolower($job_type)];
    else
     $job_type1   = '';
    ///////////////////////////////
 
    if($zone_check=getAnyTableWhereData(ZONES_TABLE,"zone_code='".tep_db_input(strtoupper($job_state1))."' and  zone_country_id ='".$job_country."'",'zone_id'))
    {
     $job_state='null';
     $job_state_id=$zone_check['zone_id'];
    }
    else
    {
     $job_state=$job_state1;
     $job_state_id=0;
    }
    
    $job_short_description = tep_db_prepare_input($content[$i]['job_description']);
    if(strlen($job_title)<=0)
    {
     $error=true;
    }
    if($data_check=getAnyTableWhereData(USAJOBS_JOB_TABLE,"usajobs_id='".tep_db_input($usajobs_id)."'",'job_id'))
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
     $expired=date('Y-m-d 23:59:59',mktime(0,0,0,date("m"),date("d")+$job_duration,date("Y")));
     $sql_data_array=array('job_title'     => $job_title,
                           'job_country_id'=> $job_country,
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
                           'job_source'    => 'usajobs',     
                           'job_type'      => $job_type1,     
                           'recruiter_id'  => $recruiter_id,
                           'latitude'  => $job_latitude,
                           'longitude' => $job_longitude,
						   'job_industry_sector'=>NULL,     
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
     $sql_data_array1['usajobs_id']   = $usajobs_id;
     $sql_data_array1['usajobs_url']  = $usajobs_url;
     $sql_data_array1['job_id']       = $job_id;
     tep_db_perform(USAJOBS_JOB_TABLE,$sql_data_array1);
	 $import_count=$import_count+1;
     ////////////////////////////////////   
    }
   }
  }
  tep_db_free_result($cat_result);
  $sql_data_array=array();
  $sql_data_array['import_jobs']   = $import_jobs;
  $sql_data_array['last_active']   = 'now()';
  tep_db_perform(USAJOBS_FEED_TABLE, $sql_data_array, 'update', "feed_id = '" . $feed_id. "'");
 }
 tep_db_free_result($result);
}
?>