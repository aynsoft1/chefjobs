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

function read_xml_job($filename,$maximum_records=false)
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
    return(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($xml_parser)),xml_get_current_line_number($xml_parser)));
   }
  }
  xml_parser_free($xml_parser);
  $total_record=count($obj->xml->CHANNEL->JOB);
  //echo $total_record;
  //print_r($obj->xml->CHANNEL->JOB);die();
 if($total_record>1)
 {
  for($i=0;$i<$total_record;$i++)
  {
   $content[$i]=array(
                 'job_title'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_TITLE->data),
                 'job_reference'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_REFERENCE->data),
                 'job_country'  => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_COUNTRY->data),
                 'job_state'    => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_STATE->data),
                 'job_location' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_LOCATION->data),
                 'job_salary'   => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_SALARY->data),
                 'job_industry' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_INDUSTRY->data),
                 'job_short_description' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_SHORT_DESCRIPTION->data),
                 'job_description' => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_DESCRIPTION->data),
                 'job_type'     => tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_TYPE->data),
                 'job_experience'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB[$i]->JOB_EXPERIENCE->data),
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
                'job_salary'   => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_SALARY->data),
                'job_industry' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_INDUSTRY->data),
                'job_short_description' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_SHORT_DESCRIPTION->data),
                'job_description' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_DESCRIPTION->data),
                'job_type'     => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_TYPE->data),
                'job_experience'=> tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_EXPERIENCE->data),
                'job_duration' => tep_db_prepare_input($obj->xml->CHANNEL->JOB->JOB_DURATION->data)
               );

 }
 return $content;
}

function get_csv_file_content($file_name,$maximum_record=false)
{
 $handle = fopen($file_name, "r");
 $header_column=array();
 $content=array();
 $count=0;
 while ($data = fgetcsv($handle,10*1024,","))
 {
  if($count==0)
  {
   $total_column=count($data);
   for($i=0;$i<$total_column;$i++)
   {
    $header_column[]=str_replace(' ','_',trim($data[$i]));
   }
  }
  else
  {
   for($i=0;$i<$total_column;$i++)
   {
    $content[$count-1][$header_column[$i]]= tep_db_prepare_input($data[$i]);
   }
  }
  if($maximum_record && $maximum_record >0  && $count==$maximum_record)
   break;
  $count++;
 }
 return  $content;
}
function recruiter_import_job($content,$recruiter_id1='')
{
 if(tep_not_null($recruiter_id1))
 $recruiter_id = $recruiter_id1;
 else
 $recruiter_id=$_SESSION['sess_recruiterid'];
 $column_name_array=(array_keys($content[0]));
 $total_column = count($column_name_array);
 $import_error=false;
 if(!in_array('job_title',$column_name_array))
 {
  $import_error[]= 'job_title column  missing.';
 }
 if(!in_array('job_country',$column_name_array))
 {
  $import_error[]= 'job_country column  missing.';
 }
 if(!in_array('job_short_description',$column_name_array))
 {
  $import_error[]= 'job_short_description column  missing.';
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
   $job_location  = tep_db_prepare_input($content[$i]['job_location']);
   $job_salary    = tep_db_prepare_input($content[$i]['job_salary']);
   $job_industry  = tep_db_prepare_input($content[$i]['job_industry']);
   $job_short_description = tep_db_prepare_input($content[$i]['job_short_description']);
   $job_description = tep_db_prepare_input($content[$i]['job_description']);
   $job_type      = tep_db_prepare_input($content[$i]['job_type']);
   $job_duration  = tep_db_prepare_input($content[$i]['job_industry']);   					
   $job_experience= (int)tep_db_prepare_input($content[$i]['job_experience']);   					
   $job_apply_url  = tep_db_prepare_input($content[$i]['job_apply_url']);
   $job_company_name = tep_db_prepare_input($content[$i]['job_company_name']);
   $job_company_logo = tep_db_prepare_input($content[$i]['job_company_logo']);

   $error_message =array();
   if(strlen($job_title)<=0)
   {
    $error=true;
    $error_message[] ='job title empty.';
   }
   elseif($row=getAnyTableWhereData(JOB_TABLE,"job_title ='".tep_db_input($job_title)."' and recruiter_id='".$recruiter_id."'"))
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
   if(!$error &&  tep_not_null(!$recruiter_id1))
   {
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
    $obj_account=new recruiter_accounts($recruiter_id,'job_post');
    if(!($obj_account->allocated_amount['job']=='Unlimited' || $obj_account->allocated_amount['job'] >= $obj_account->enjoyed_amount['job']+1))
    {
     $error=true;
     $error_message[] ='Subscription Error.';
    }
   } 
   if($error)
   {
    $import_message[$i]=array('error'=>'Row No '.($i+1).' : '.implode('<br>',$error_message));
   }
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
    $job_experience  =  (int)$job_experience;
    $row=getAnyTableWhereData(EXPERIENCE_TABLE,"min_experience <='".tep_db_input($job_experience)."'  order by min_experience  desc limit 0,1",'min_experience,max_experience');
    $min_experience=$row['min_experience'];
    $max_experience=$row['max_experience'];
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
                          'min_experience'=> $min_experience,     
                          'max_experience'=> $max_experience,     
                          'job_vacancy_period'=> $max_experience,     
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

    //print_r($sql_data_array);die();
	if($obj_account->allocated_amount['featured_job']=='Yes')
     $sql_data_array['job_featured']='Yes';
	else
     $sql_data_array['job_featured']='No';
    tep_db_perform(JOB_TABLE, $sql_data_array);
	$row_check=getAnyTableWhereData(JOB_TABLE,"recruiter_id='".$recruiter_id."' and job_title='".tep_db_input($job_title)."' order by job_id desc limit 0,1",'job_id');
    $job_id=$row_check['job_id'];
    /////////////////////////////////////
    $sql_job_array=array('job_id'=>$job_id);
	if(is_array($job_industry_ids))
    for($j=0;$j<count($job_industry_ids);$j++)
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
    if(!tep_not_null($recruiter_id1))
	{
     $row=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and plan_for='job_post' and start_date <= '$now' and end_date >='$now'","id,job_enjoyed");
     $sql_data_array=array('job_enjoyed'=>1+$row['job_enjoyed']);
     tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array, 'update', "id = '" . $row['id'] . "'");
	}
	else
    {
	  $sql_data_array1=array();
      $sql_data_array1['company_name']   = $job_company_name;
      $sql_data_array1['company_logo']  = $job_company_logo;
      $sql_data_array1['job_id']      = $job_id;
      tep_db_perform(JOB_CSV_TABLE,$sql_data_array1);
 	}
    ////////////////////////////////////
    $import_message[$i]=array('success'=>$job_id);
   }
  }
   return $import_message;
 }
}
?>