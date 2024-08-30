<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2010  #**********
**********************************************************/
set_time_limit(0);
function read_simplyhired_feeds($url)
{
 $simplyhired_contant=array();
	if ( function_exists('curl_init') )
 {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$data = curl_exec($ch);
		curl_close($ch);
  if(tep_not_null($data))
  {
   $parsed_xml = simplexml_load_string($data);
   $i=0;
   if($parsed_xml->rs->r)
 		foreach($parsed_xml->rs->r as $current)
   {
    $job_title       = (array) $current->jt; 
    $job_city        = (array) $current->loc->attributes()->cty; 
    $job_state       = (array) $current->loc->attributes()->st; 
    $job_postal      = (array) $current->loc->attributes()->postal; 
    $job_location    = (array) $current->loc; 
    $job_url         = (array) $current->src->attributes()->url; 
    $job_description = (array) $current->e; 

	preg_match("/\/(jobkey-.+?)\//i", $job_url[0],$match);
    $job_id=$match[1];
    $simplyhired_contant[$i]=array(
                        'job_id'         => tep_db_prepare_input($job_id),
                        'job_title'      => tep_db_prepare_input($job_title[0]),
                        'job_city'       => tep_db_prepare_input($job_city[0]),
                        'job_state'      => tep_db_prepare_input($job_state[0]),
                        'job_postal'     => tep_db_prepare_input($job_postal[0]),
                        'job_location'   => tep_db_prepare_input($job_location[0]),
                        'job_url'        => tep_db_prepare_input($job_url[0]),
                        'job_description'=> tep_db_prepare_input($job_description[0]),
                       );
     $i++;

			}
  }
	}
 return $simplyhired_contant;
}
////////////////////////////////////////////////////////
function simplyhired_feed_import($feed_ids='')
{
 $add_whereClause='';
 if(tep_not_null($feed_ids))
 $add_whereClause=" and  dd.feed_id in (".$feed_ids.") ";
 $query = "select dd.* from ".SIMPLYHIRED_FEED_TABLE."  dd left outer join ".RECRUITER_LOGIN_TABLE."  as rl on (dd.recruiter_id=rl.recruiter_id) where dd.status='active' and rl.recruiter_id is not null  $add_whereClause   order by dd.feed_id";
 $result=tep_db_query($query);
 while($row = tep_db_fetch_array($result))
 {
  $publisher_id = tep_db_prepare_input($row['publisher_id']);
  $api_key      = tep_db_prepare_input($row['api_key']);
  $recruiter_id = tep_db_prepare_input($row['recruiter_id']);
  $location     = tep_db_prepare_input($row['location']);
  $country_code = tep_db_prepare_input($row['country_code']);
  $job_title    = tep_db_prepare_input($row['job_title']);
  $job_company  = tep_db_prepare_input($row['job_company']);
  $sort_by      = tep_db_prepare_input($row['sort_by']);
  $max_feed     = tep_db_prepare_input($row['max_feed']);
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
   $api_url         = get_simplyhired_api_url($publisher_id,$api_key,$country_code,$job_category,$job_title,$job_company,$location,$sort_by,$max_feed,$page);    
   if(tep_not_null($api_url))
   $content         = read_simplyhired_feeds($api_url);
   $total_records = count($content);
   for($i=0;$i<$total_records;$i++)
   {
    $error         = false;
    $simplyhired_id = tep_db_prepare_input($content[$i]['job_id']);
    $job_url       = tep_db_prepare_input($content[$i]['job_url']);
    $job_title     = tep_db_prepare_input($content[$i]['job_title']);
    $job_state1    = tep_db_prepare_input($content[$i]['job_state']);
    $job_city      = tep_db_prepare_input($content[$i]['job_city']);
    $job_zipcode   = tep_db_prepare_input($content[$i]['job_postal']);
    $job_location  = tep_db_prepare_input($content[$i]['job_location']);

    $country_array = array(	'us'=>223,'ar'=>4,'au'=>13,'at'=>14,'be'=>21,'br'=>30,'ca'=>38,'fr'=>73,'de'=>81,'in'=>99,'ie'=>103,'it'=>105,'mx'=>138,'nl'=>150,'pt'=>171,'ru'=>176,'za'=>193,'es'=>195,'se'=>203,'ch'=>202,'gb'=>222);
    if(array_key_exists(strtolower($country_code),$country_array ))
     $job_country=$country_array[$country_code];
    else
     $job_country   = 0;
    
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
    $job_description       = tep_db_prepare_input($content[$i]['job_description']);
    if(strlen($job_title)<=0)
    {
     $error=true;
    }
    if($data_check=getAnyTableWhereData(SIMPLYHIRED_JOB_TABLE,"simplyhired_id='".tep_db_input($simplyhired_id)."'",'job_id'))
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
     //$today=date('Y-m-d',mktime(0,0,0,date("m"),date("d"),date("Y")));
					$today=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m")  , date("d"), date("Y")));
     $adv_date=date('Y-m-d');
     $expired=date('Y-m-d',mktime(0,0,0,date("m"),date("d")+$job_duration,date("Y")));
     $sql_data_array=array('job_title'     => $job_title,
                           'job_country_id'=> $job_country,
                           'job_state'     => $job_state,
                           'job_state_id'  => $job_state_id,
                           'job_location'  => $job_city,
                           'job_short_description'=>$job_short_description,
                           'job_description'=>$job_description,
                           'min_experience'=> 0,     
                           'job_vacancy_period'=> $max_experience,     
                           're_adv'        => $adv_date,     
                           'expired'       => $expired,     
                           'inserted'      => $today,     
                           'recruiter_user_id'=> 'null',     
                           'job_vacancy_period'=> $job_duration,     
                           'job_source'    => 'simplyhired',     
                           'recruiter_id'  => $recruiter_id,     
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
     $sql_data_array1['simplyhired_id']   = $simplyhired_id;
     $sql_data_array1['simplyhired_url']  = $job_url;
     $sql_data_array1['job_id']           = $job_id;
     tep_db_perform(SIMPLYHIRED_JOB_TABLE,$sql_data_array1);
     ////////////////////////////////////   
    }
   }
  }
  tep_db_free_result($cat_result);
  $sql_data_array=array();
  $sql_data_array['import_jobs']   = $import_jobs;
  $sql_data_array['last_active']   = 'now()';
  tep_db_perform(SIMPLYHIRED_FEED_TABLE, $sql_data_array, 'update', "feed_id = '" . $feed_id. "'");
 }
 tep_db_free_result($result);
}

///////////////////////////////////////////////////
function get_simplyhired_api_url($publisher_id,$api_key='',$feed_country='us',$feed_keyword='',$feed_job_title='',$feed_job_company='',$feed_location='',$feed_sort_by='',$max_feed=10,$page=1)
{
 switch($feed_country)
 {
  case 'us': 
     $api_url ='http://api.simplyhired.com/a/jobs-api/xml-v2/';
   break;
  case 'ar': // Argentina
  case 'au': // Australia
  case 'br': // Brazil
     $api_url ='http://api.simplyhired.com.'.$feed_country.'/a/jobs-api/xml-v2/';
   break;
  case 'at': // Austria
  case 'be': // Belgium
  case 'ca': // Canada
  case 'fr': // France
  case 'de': // Germany
  case 'ie': // Ireland
  case 'it': // Italy
  case 'mx': // Mexico
  case 'nl': // Netherlands
  case 'pt': // Portugal
  case 'ru': // Russia
  case 'es': // Spain
  case 'se': // Sweden
  case 'ch': // Switzerland
     $api_url ='http://api.simplyhired.'.$feed_country.'/a/jobs-api/xml-v2/';
   break;
  case 'in': // India
     $api_url ='http://api.simplyhired.co.in/a/jobs-api/xml-v2/';
   break;
  case 'za': // South Africa
     $api_url ='http://api.za.simplyhired.com/a/jobs-api/xml-v2/';
   break;
  case 'uk': // United Kingdom
     $api_url ='http://api.simplyhired.co.uk/a/jobs-api/xml-v2/';
   break;
  default:
     $api_url ='http://api.simplyhired.com/a/jobs-api/xml-v2/';
 }
 $url_parameter=array();
 $serch_keyword ='';
 if(tep_not_null($feed_keyword))
	{
	 $serch_keyword =trim('"'.$feed_keyword.'"');
 }
 if(tep_not_null($feed_job_title))
	{
	 $serch_keyword =$serch_keyword.' AND title:('.trim($feed_job_title).')';
 }
 if(tep_not_null($feed_job_company))
	{
	 $serch_keyword =$serch_keyword.' AND searchableCompanyName:('.trim($feed_job_company).')';
 }
 $url_parameter['q']=$serch_keyword;


 if(tep_not_null($feed_location))
 $url_parameter['l']=trim($feed_location);

 if(tep_not_null($feed_sort_by))
  $url_parameter['sb']=trim($feed_sort_by);

 if(tep_not_null($max_feed))
 $url_parameter['ws']=trim($max_feed);

 if(tep_not_null($page))
 $url_parameter['pn']=trim($page);
 foreach ($url_parameter as $name=>$value) 
 {
  $value = urlencode($value);
  $amazon_query[] = $name."-".$value;
 }
 $url_parameter= implode("/", $amazon_query);
 $url_parameter.= '/?pshid='.urlencode($publisher_id).'&'.(($feed_country=='us' || $feed_country=='ca')?'ssty=2':'ssty=3').'&'.(($api_key!='')?'auth='.urlencode($api_key):'').'&cflg=r&clip='.urlencode(tep_get_ip_address());
 $api_url  = $api_url.$url_parameter;
 return $api_url;
}
//////////////////////////////////////////////////////////////////
function tep_get_next_runtime_amazon($occurrence,$occurrence_type,$start_date)
{
 $end_date   = date('Y-m-d H:i:s');
 $occurrence = (int) $occurrence;
 $year    = substr($start_date,0,4);
 $month   = substr($start_date,5,2);
 $day     = substr($start_date,8,2);
 $hour    = substr($start_date,11,2);
 $minutes = substr($start_date,14,2);
 $seconds = substr($start_date,17,2);
 if(!checkdate ( (int)$month, (int) $day, (int) $year))
 $start_date ='0000-00-00 00:00:00';
 switch($occurrence_type)
 {
  case "hour":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H')+$occurrence,date('i'),date('s'),date('m'),date('d'),date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour+$occurrence,$minutes,$seconds,$month,$day,$year));
   break;
  case "day":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m'),date('d')+$occurrence,date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour,$minutes,$seconds,$month,$day+$occurrence,$year));
   break;
  case "week":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m'),date('d')+($occurrence*7),date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour,$minutes,$seconds,$month,$day+($occurrence*7),$year));
   break;
 }
 return $end_date;
}
function getAmazonCategory($location)
{
 switch($location)
 {
  case'US':
    return array('Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DigitalMusic','DVD','Electronics','GourmetFood','Grocery','HealthPersonalCare','HomeGarden ','Industrial','Jewelry','KindleStore','Kitchen','Magazines','Merchants','Miscellaneous','MP3Downloads','Music','MusicalInstruments','MusicTracks','OfficeProducts','OutdoorLiving ','PCHardware','PetSupplies','Photo','Shoes','Software','SportingGoods','Tools','Toys','UnboxVideo','VHS','Video','VideoGames','Watches','Wireless','WirelessAccessories');
   break;
  case'UK': 
    return array('Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DVD','Electronics','GourmetFood','Grocery','HealthPersonalCare','HomeGarden','HomeImprovement','Jewelry','KindleStore','Kitchen','Lighting','MP3Downloads','Music','MusicTracks','OfficeProducts','OutdoorLiving','Outlet','Shoes','Software','SoftwareVideoGames','SportingGoods','Tools','Toys','VHS','Video','VideoGames','Watches');
   break;
  case'CA': 
    return array('Blended','Books','Classical','DVD','Electronics','ForeignBooks','MP3Downloads','Music','Software','SoftwareVideoGames','VHS','Video','VideoGames');
   break;
  case'FR': 
    return array('Baby','Beauty','Blended','Books','Classical','DVD','Electronics','ForeignBooks','HealthPersonalCare','Jewelry','Kitchen','Lighting','MP3Downloads','Music','MusicTracks','OfficeProducts','Shoes','Software','SoftwareVideoGames','Toys','VHS','Video','VideoGames','Watches');
   break;
  case'JP': 
    return array('Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DVD','Electronics','ForeignBooks','Grocery','HealthPersonalCare','Hobbies','HomeImprovement','Jewelry','Kitchen','Music','MusicTracks','OfficeProducts','Shoes','Software','SportingGoods','Toys','VHS','Video','VideoGames');
   break;
  case'DE': 
    return array('Apparel','Automotive','Baby','Beauty','Blended','Books','Classical','DVD','Electronics','ForeignBooks','HealthPersonalCare','HomeGarden','HomeImprovement','Jewelry','Kitchen','Lighting','Magazines','MP3Downloads','Music','MusicTracks','OfficeProducts','OutdoorLiving','Outlet','PCHardware','Photo','Shoes','Software','SoftwareVideoGames','SportingGoods','Tools','Toys','VHS','Video','VideoGames','Watches');
   break;
  default:
   return false;
 }
}
?>