<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
**********************************************************/
function startElementXML($parser, $name, $attrs) 
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

function endElementXML($parser, $name) 
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

function characterDataXML($parser, $data) 
{
   global $obj;
   eval($obj->tree.'->data.=\''.trim(addslashes($data)).'\';');
}
function readXML_job($filename)
{
  $content1= simplexml_load_file($filename,'SimpleXMLElement',LIBXML_NOCDATA);

  $content=array();
  foreach($content1->job as $job)
  { 
	 $job_data =(array) $job;
	 $content[]=	array('job_title'     => (!tep_not_null($job_data['job_title'])?null:  tep_db_prepare_input( $job_data['job_title'])),
	                      'job_reference' => (!tep_not_null($job_data['job_reference'])?null:  tep_db_prepare_input( $job_data['job_reference'])),
	                      'job_country'   => (!tep_not_null($job_data['job_country'])?null:  tep_db_prepare_input( $job_data['job_country'])),
	                      'job_state'     => (!tep_not_null($job_data['job_state'])?null:  tep_db_prepare_input( $job_data['job_state'])),
	                      'job_location'  => (!tep_not_null($job_data['job_location'])?null:  tep_db_prepare_input( $job_data['job_location'])),
	                      'job_zip'       => (!tep_not_null($job_data['job_zip'])?null:  tep_db_prepare_input( $job_data['job_zip'])),
	                      'job_city'      => (!tep_not_null($job_data['job_city'])?null:  tep_db_prepare_input( $job_data['job_city'])),
	                      'job_industry'  => (!tep_not_null($job_data['job_industry'])?null:  tep_db_prepare_input( $job_data['job_industry'])),
	                      'job_salary'     => (!tep_not_null($job_data['job_salary'])?null:  tep_db_prepare_input( $job_data['job_salary'])),
	                      'job_short_description'     => (!tep_not_null($job_data['job_short_description'])?null:  tep_db_prepare_input( $job_data['job_short_description'])),
	                      'job_description'     => (!tep_not_null($job_data['job_description'])?null:  stripslashes( $job_data['job_description'])),
	                      'job_apply_url' => (!tep_not_null($job_data['job_apply_url'])?null: tep_db_prepare_input($job_data['job_apply_url'])),
	                      'job_type'      => (!tep_not_null($job_data['job_type'])?null:  tep_db_prepare_input( $job_data['job_type'])),
	                      'job_duration'  => (!tep_not_null($job_data['job_duration'])?null:  tep_db_prepare_input( $job_data['job_duration'])),
 	                  );

  } 
  return  $content;
}
function readXML_job_old($filename)
{
  global $obj;
  $obj='';
  $obj->tree = '$obj->xml';
  $obj->xml = '';
  $xml_parser = xml_parser_create();
  xml_set_element_handler($xml_parser, "startElementXML", "endElementXML");
  xml_set_character_data_handler($xml_parser, "characterDataXML");
  if (!($fp = fopen($filename, "r"))) 
  {
   return("could not open XML input");
  }
  while ($data = fread($fp, 4096)) 
  {
   if (!xml_parse($xml_parser, $data, feof($fp))) 
   {
    echo(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($xml_parser)),xml_get_current_line_number($xml_parser)));
   }
  }
  xml_parser_free($xml_parser);

  $total_record=count($obj->xml->CHANNEL->JOB);
  //echo $total_record;
  //print_r($obj->xml->CHANNEL);die("ss");
 if($total_record>1)
 {
  for($i=0;$i<$total_record;$i++)
  {
   $content[$i]=array(
                 'job_title'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_TITLE->data),
                 'job_reference'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_REFERENCE->data),
                 'job_country'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_COUNTRY->data),
                 'job_state'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_STATE->data),
                 'job_city'     => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_CITY->data),
                 'job_location' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_LOCATION->data),
                 'job_zip'      => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_ZIP->data),
                 'job_industry' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_INDUSTRY->data),
                 'job_salary'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_SALARY->data),
                 'job_short_description'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_SHORT_DESCRIPTION->data),
                 'job_description' => stripslashes($obj->xml->CHANNEL->JOB[$i]->JOB_DESCRIPTION->data),
                 'job_apply_url'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_APPLY_URL->data),
                 'job_type'     => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_TYPE->data),
                 'job_duration' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_DURATION->data)
                );
  }
 }
 elseif($total_record==1)
 {
  $content[0]=array(
                'job_title'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_TITLE->data),
                'job_reference'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_REFERENCE->data),
                'job_country'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_COUNTRY->data),
                'job_state'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_STATE->data),
                'job_location' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_LOCATION->data),
                'job_zip'      => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_ZIP->data),
                'job_city'     => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_CITY->data),
                'job_industry' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_INDUSTRY->data),
	            'job_salary'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_SALARY->data),
	            'job_short_description'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_SHORT_DESCRIPTION->data),
                'job_description' => stripslashes($obj->xml->CHANNEL->JOB->JOB_DESCRIPTION->data),
                'job_apply_url' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_APPLY_URL->data),
                'job_type'      => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_TYPE->data),
                'job_duration' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_DURATION->data)
               );

 }
 return $content;
}

function recruiter_import_job($content,$recruiter_id1='')
{

 if(tep_not_null($recruiter_id1))
 $recruiter_id = $recruiter_id1;
 else
 $recruiter_id=$_SESSION['sess_recruiterid'];
 $import_error=false;
  if(!$content[0])
 {
  $import_error[]= 'empty data.';
 }
 $column_name_array=(array_keys($content[0]));
 $total_column = count($column_name_array);
 if(!in_array('job_title',$column_name_array))
 {
  $import_error[]= 'job title missing.';
 }
 if(!in_array('job_country',$column_name_array))
 {
  $import_error[]= 'job country missing.';
 }
 if(!in_array('job_description',$column_name_array))
 {
  $import_error[]= 'job description missing.';
 }
 if($import_error)
 {
  return $import_message =array('error'=>implode('<br>', $import_error));;
 }
 else
 { 
  $total_records = count($content);
  for($i=0;$i<$total_records;$i++)
  {
   $error         = false;
   $job_title     = tep_db_prepare_input($content[$i]['job_title']);
   $job_reference = tep_db_prepare_input($content[$i]['job_reference']);
   $job_country   = tep_db_prepare_input($content[$i]['job_country']);
   $job_state     = tep_db_prepare_input($content[$i]['job_state']);
   $job_city      = tep_db_prepare_input($content[$i]['job_city']);
   $job_location  = tep_db_prepare_input($content[$i]['job_location']);
   $job_zip       = tep_db_prepare_input($content[$i]['job_zip']);
   $job_industry  = tep_db_prepare_input($content[$i]['job_industry']);
   $job_description = ($content[$i]['job_description']);
   $job_short_description = tep_db_prepare_input($content[$i]['job_short_description']);
   if(!tep_not_null($job_short_description))
   $job_short_description = substr(strip_tags($job_description),200);

   $job_type      = tep_db_prepare_input($content[$i]['job_type']);
   $job_duration  = tep_db_prepare_input($content[$i]['job_duration']);   					
   $job_salary    = tep_db_prepare_input($content[$i]['job_salary']);
   $job_apply_url = tep_db_prepare_input($content[$i]['job_apply_url']);

   $error_message =array();
   if(strlen($job_title)<=0)
   {
    $error=true;
    $error_message[] ='job title empty.';
   }
   elseif($row=getAnyTableWhereData(JOB_TABLE,"job_title ='".tep_db_input($job_title)."' and job_reference ='".tep_db_input($job_reference)."'  and recruiter_id='".$recruiter_id."'"))
   {
    $error=true;
    $error_message[] ='job title already exists.';
   }
   if(!$row=getAnyTableWhereData(COUNTRIES_TABLE,"country_name ='".tep_db_input($job_country)."'",'id'))
   {
    $error=true;
    $error_message[] ='job country invalid.';
   }
   else
    $job_country=$row['id'];
   if(strlen($job_short_description)<=0)
   {
    $error=true;
    $error_message[] ='job short description empty.';
   } 
   if($error)
   {
    $import_message[$i]=array('error'=>'Job : '.($i+1).' : '.implode('<br>',$error_message));   }
   else
   {
    if($row=getAnyTableWhereData(ZONES_TABLE,"zone_name ='".tep_db_input($job_state)."'",'zone_id'))
    {
     $job_state='null';
     $job_state_id=$row['zone_id'];
    }
    else
    {
     $job_state=$job_state;
     $job_state_id=0;
    }
    ///////////
    $job_industry_array=explode(',',$job_industry);
    $job_industry_ids=array();
    $total_industry=count($job_industry_array);
    for($j=0;$j<$total_industry;$j++)
    {
     if($row=getAnyTableWhereData(JOB_CATEGORY_TABLE,"category_name ='".tep_db_input($job_industry_array[$j])."'",'id'))
     {
      $job_industry_ids[]=$row['id'];
     }
    }
    if(!count($job_industry_ids))
    {
     $job_industry_ids=0;
    }
    ///////////
    $job_type_array=explode(',',$job_type);
    $job_type_ids=array();
    $total_job_type=count($job_type_array);
    for($j=0;$j<$total_job_type;$j++)
    {
     if($row=getAnyTableWhereData(JOB_TYPE_TABLE,"type_name ='".tep_db_input($job_type_array[$j])."'",'id'))
     {
      $job_type_ids[]=$row['id'];
     }
    }
    if(!count($job_type_ids))
     $job_type_ids=0;
    else
     $job_type_ids=implode(',',$job_type_ids);
    ////////////////////////////////////////////////////////
    $job_duration  =  (int)$job_duration;
    if(!($job_duration>0 && $job_duration<=INFO_TEXT_MAX_JOB_DURATION))
    $job_duration=INFO_TEXT_MAX_JOB_DURATION;
    //////////////////////////////////
    $sql_data_array=array();
    $today=date('Y-m-d',mktime(0,0,0,date("m"),date("d"),date("Y")));
    $adv_date=date('Y-m-d');
    $expired=date('Y-m-d',mktime(0,0,0,date("m"),date("d")+$job_duration,date("Y")));
    $sql_data_array=array('job_title'     => $job_title,
                          'job_reference' => $job_reference,
                          'job_country_id'=> $job_country,
                          'job_state'     => $job_state,
                          'job_state_id'  => $job_state_id,
                          'job_location'  => $job_location,
                          'job_salary'    => $job_salary,
                          'job_short_description'=>$job_short_description,
                          'job_description'=> $job_description,
                          'job_type'      => $job_type_ids,
                          'min_experience'=> 0,     
                          'max_experience'=> '',     
                          're_adv'        => $adv_date,     
                          'expired'       => $expired,     
                          'inserted'      => $today,     
                          'recruiter_user_id'=> 'null',     
                          'job_vacancy_period'=> $job_duration,     
                          'recruiter_id'  => $recruiter_id,     
                          );
	if(tep_not_null($job_apply_url))
	{
	 $sql_data_array['post_url']='Yes';
     $sql_data_array['url']= $job_apply_url;
    }
	if(tep_not_null($recruiter_id1))
	$sql_data_array['job_source']='csv';

    $sql_data_array['job_featured']='No';
    tep_db_perform(JOB_TABLE, $sql_data_array);
	$row_check=getAnyTableWhereData(JOB_TABLE,"recruiter_id='".$recruiter_id."' and job_title='".tep_db_input($job_title)."' order by job_id desc limit 0,1",'job_id');
    $job_id=$row_check['job_id'];
    /////////////////////////////////////
    $sql_job_array=array('job_id'=>$job_id);
	if(is_array($job_industry_ids))
    $t_count =count($job_industry_ids);
	else
		$t_count=0;
    for($j=0;$j<$t_count;$j++)
    {
     if(!$job_row = getAnyTableWhereData(JOB_JOB_CATEGORY_TABLE, "job_id = '" . tep_db_input($job_id) . "' and job_category_id='".$job_industry_ids[$j]."'", "job_category_id"))
     {
      $sql_job_array['job_category_id']=$job_industry_ids[$j];
       tep_db_perform(JOB_JOB_CATEGORY_TABLE,$sql_job_array);
     }					
    }
    /////////////////////////////////////////////
    $sql_data_array_new=array();
    $sql_data_array_new['display_id']=get_job_enquiry_code($job_id);
    tep_db_perform(JOB_TABLE, $sql_data_array_new, 'update', "job_id = '" . $job_id . "'");
    // find last id //
    $now=date("Y-m-d");
    ////////////////////////////////////
    $import_message[$i]=array('success'=>$job_id);
   }
  }
   return $import_message;
 }
}
?>