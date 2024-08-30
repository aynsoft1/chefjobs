<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik		 #******
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
ini_set('max_execution_time','0');
 function indeedStartElement($parser, $name, $attrs)
 {
   global $obj;
   
   // If var already defined, make array
   eval('$test=isset('.$obj->tree.'->'.$name.');');
   if ($test) {
     eval('$tmp='.$obj->tree.'->'.$name.';');
     eval('$arr=is_array('.$obj->tree.'->'.$name.');');
     if (!$arr) {
       eval('unset('.$obj->tree.'->'.$name.');');
       eval($obj->tree.'->'.$name.'[0]=$tmp;');
       $cnt = 1;
     }
     else {
       eval('$cnt=count('.$obj->tree.'->'.$name.');');
     }
     
     $obj->tree .= '->'.$name."[$cnt]";
   }
   else {
     $obj->tree .= '->'.$name;
   }
   if (count($attrs)) {
    eval($obj->tree.'->attr=$attrs;');
   }
}
function indeedEndElement($parser, $name)
 {
   global $obj;
   // Strip off last ->
   for($a=strlen($obj->tree);$a>0;$a--) {
    if (substr($obj->tree, $a, 2) == '->') {
    $obj->tree = substr($obj->tree, 0, $a);
    break;
    }
   }
}
function indeedCharacterData($parser, $data) {
   global $obj;
   eval($obj->tree.'->data.=\''.trim(addslashes($data)).'\';');
}
function read_indeed_xml($filename)
{
 global $obj;
 $obj='';
 $obj->tree = '$obj->xml';
 $obj->xml = '';
 $xml_parser = xml_parser_create();
 xml_set_element_handler($xml_parser, "indeedStartElement", "indeedEndElement");
 xml_set_character_data_handler($xml_parser, "indeedCharacterData");
 if (!($fp = fopen($filename, "r"))) {
    die("could not open XML input");
 }
 while ($data = fread($fp, 4096)) {
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }
 }
 xml_parser_free($xml_parser);

 $total_record=count($obj->xml->RESPONSE->RESULTS->RESULT);
 //echo $total_record;
 $string=array();
 if($total_record>1)
 {
  for($i=0;$i<$total_record;$i++)
  {
   $string[]=array('jobkey'     => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->JOBKEY->data,
                   'date'       => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->DATE->data,
                   'job_title'  => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->JOBTITLE->data,
                   'job_city'       => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->CITY->data,
                   'job_state'  => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->STATE->data,
                   'description'=> $obj->xml->RESPONSE->RESULTS->RESULT[$i]->SNIPPET->data,
                   'job_latitude'=> $obj->xml->RESPONSE->RESULTS->RESULT[$i]->LATITUDE->data,
                   'job_longitude'=> $obj->xml->RESPONSE->RESULTS->RESULT[$i]->LONGITUDE->data,
                   'url'        => $obj->xml->RESPONSE->RESULTS->RESULT[$i]->URL->data,
                   );
  }
 }
 else if($total_record==1)
 {
   $string[]=array('jobkey'       => $obj->xml->RESPONSE->RESULTS->RESULT->JOBKEY->data,
                   'date'         => $obj->xml->RESPONSE->RESULTS->RESULT->DATE->data,
                   'job_title'    => $obj->xml->RESPONSE->RESULTS->RESULT->JOBTITLE->data,
                   'job_city'     => $obj->xml->RESPONSE->RESULTS->RESULT->CITY->data,
                   'job_state'    => $obj->xml->RESPONSE->RESULTS->RESULT->STATE->data,
                   'description'  => $obj->xml->RESPONSE->RESULTS->RESULT->SNIPPET->data,
                   'job_latitude' => $obj->xml->RESPONSE->RESULTS->RESULT->LATITUDE->data,
                   'job_longitude' => $obj->xml->RESPONSE->RESULTS->RESULT->LONGITUDE->data,
                   'url'        => $obj->xml->RESPONSE->RESULTS->RESULT->URL->data,
                   );
 }
 return $string;
}

////////////////////////////////////////////////////////
function indeed_job_import($feed_ids='')
{
 $add_whereClause='';
 if(tep_not_null($feed_ids))
 $add_whereClause=" and  dd.feed_id in (".$feed_ids.") ";
 $query = "select dd.* from ".INDEED_FEED_TABLE."  dd left outer join ".RECRUITER_LOGIN_TABLE."  as rl on (dd.recruiter_id=rl.recruiter_id) where dd.status='active' and rl.recruiter_id is not null  $add_whereClause   order by dd.feed_id";
 $result=tep_db_query($query);
 while($row = tep_db_fetch_array($result))
 {
  $publisher_id = tep_db_prepare_input($row['publisher_id']);
  $recruiter_id = tep_db_prepare_input($row['recruiter_id']);
  $location     = tep_db_prepare_input($row['location']);
  $country_code = tep_db_prepare_input($row['country_code']);
  $job_type     = tep_db_prepare_input($row['job_type']);
  $sort_by      = tep_db_prepare_input($row['sort_by']);
  $max_feed     = tep_db_prepare_input($row['max_feed']);
  $feed_keyword = tep_db_prepare_input($row['feed_keyword']);
  $channel      = tep_db_prepare_input($row['channel']);
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
   $content         = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher='.urlencode($publisher_id).'&q='.urlencode($job_category).'&l='.urlencode($location).'&sort='.urlencode($sort_by).'&radius=&st=&jt='.urlencode($job_type).'&start=&limit='.urlencode($max_feed).'&fromage=%20&filter=&latlong=1&co='.urlencode($country_code).'&chnl='.urlencode($channel).'&userip='.urlencode(getenv('REMOTE_ADDR')).'&v=2&useragent=Mozilla/%2F4.0%28Firefox%29');
			//print_r($content);die();
   $total_records = count($content);
   for($i=0;$i<$total_records;$i++)
   {
    $error         = false;
    $indeed_id     = tep_db_prepare_input($content[$i]['jobkey']);
    $indeed_url    = tep_db_prepare_input($content[$i]['url']);
    $job_title     = tep_db_prepare_input($content[$i]['job_title']);
    $job_state1    = tep_db_prepare_input($content[$i]['job_state']);
    $job_city      = tep_db_prepare_input($content[$i]['job_city']);
    $job_latitude  = tep_db_prepare_input($content[$i]['job_latitude']);
    $job_longitude = tep_db_prepare_input($content[$i]['job_longitude']);

    $country_array = array(	'us'=>223,'au'=>13,'at'=>14,'be'=>21,'br'=>30,'ca'=>38,'dk'=>57,'fr'=>73,'de'=>81,'hk'=>96,'in'=>99,'ie'=>103,'it'=>105,'mx'=>138,'nl'=>150,'nz'=>153,'pk'=>162,'sa'=>184,'sg'=>188,'za'=>193,'es'=>195,'ch'=>202,'ae'=>221,'gb'=>222,'eg'=>63,'ng'=>156,'ma'=>144);
    if(array_key_exists(strtolower($country_code),$country_array ))
     $job_country=$country_array[$country_code];
    else
     $job_country   = 0;
    
    ////////////////////////
    $job_type_array = array(	'fulltime'=>1,'parttime'=>2,'contract'=>3,'internship'=>6,'temporary'=>5);
    if(array_key_exists(strtoupper($job_type),$job_type_array))
     $job_type1 =$job_type_array[$job_type];
    else
     $job_type1   = '';
    ///////////////////////

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
    
    $job_short_description = tep_db_prepare_input($content[$i]['description']);
    if(strlen($job_title)<=0)
    {
     $error=true;
    }
    if($data_check=getAnyTableWhereData(INDEED_JOB_TABLE,"indeed_id='".tep_db_input($indeed_id)."'",'job_id'))
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
                           'job_source'    => 'indeed',     
                           'job_type'      => $job_type1,     
                           'recruiter_id'  => $recruiter_id,
                           'latitude'  => $job_latitude,
                           'longitude' => $job_longitude,
							'job_industry_sector'=>NULL,     
							'job_description'=>' ',
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
     $sql_data_array1['indeed_id']   = $indeed_id;
     $sql_data_array1['indeed_url']  = $indeed_url;
     $sql_data_array1['job_id']      = $job_id;
     tep_db_perform(INDEED_JOB_TABLE,$sql_data_array1);
     ////////////////////////////////////   
    }
   }
  }
  tep_db_free_result($cat_result);
  $sql_data_array=array();
  $sql_data_array['import_jobs']   = $import_jobs;
  $sql_data_array['last_active']   = 'now()';
  tep_db_perform(INDEED_FEED_TABLE, $sql_data_array, 'update', "feed_id = '" . $feed_id. "'");
 }
 tep_db_free_result($result);
}
?>