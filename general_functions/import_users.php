<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2017  #**********
**********************************************************/




function get_csv_file_content($file_name,$maximum_record=false)
{
 $handle = fopen($file_name, "r");
 $bom = "\xef\xbb\xbf";

 if (fgets($handle, 4) !== $bom) {
    // BOM not found - rewind pointer to start of file.
    rewind($handle);
 }
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
function import_user_jobseeker($content)
{
 if(!is_array($content[0]))
 return  0;
 $column_name_array=(array_keys($content[0]));
 $total_column = count($column_name_array);
 $import_error=false;
  
 if(!in_array('first_name',$column_name_array))
 { 
  $import_error[]= 'jobseeker first_name column  missing.';
 }
 if(!in_array('country',$column_name_array))
 {
  $import_error[]= 'jobseeker country column  missing.';
 }
 if(!in_array('email_address',$column_name_array))
 {
  $import_error[]= 'jobseeker email_address column  missing.';
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
   $first_name    = tep_db_prepare_input($content[$i]['first_name']);
   $middle_name   = tep_db_prepare_input($content[$i]['middle_name']);
   $last_name     = tep_db_prepare_input($content[$i]['last_name']);
   $email_address = tep_db_prepare_input($content[$i]['email_address']);
   $password      = tep_db_prepare_input($content[$i]['password']);
   $city          = tep_db_prepare_input($content[$i]['city']);
   $state         = tep_db_prepare_input($content[$i]['state']);
   $country       = tep_db_prepare_input($content[$i]['country']);
   $address1      = tep_db_prepare_input($content[$i]['address1']);
   $address2      = tep_db_prepare_input($content[$i]['address2']);
   $zip_code      = tep_db_prepare_input($content[$i]['zip_code']);
   $phone         = tep_db_prepare_input($content[$i]['phone']);   					
   $mobile        = tep_db_prepare_input($content[$i]['mobile']);   					
   $subscribe_letter = tep_db_prepare_input($content[$i]['subscribe_letter']);   					

   $error_message =array();
   if(strlen($first_name)<=0)
   {
    $error=true;
    $error_message[] ='jobseeker first_name empty.';
   }
   if(!$row=getAnyTableWhereData(COUNTRIES_TABLE,"country_name ='".tep_db_input($country)."'",'id'))
   {
    $country_id = DEFAULT_COUNTRY_ID;
   }
   else
    $country_id=$row['id'];

   if(strlen($email_address)<=0)
   {
    $error=true;
    $error_message[] ='jobseeker email_address empty.';
   }
   elseif(tep_validate_email($email_address) == false)
   {
    $error=true;
    $error_message[] ='Invaild email address.';
   }
   elseif($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
   {
    $error = true;
    $error_message[] ='jobseeker email_address already register as recruiter.';
   }
   elseif($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id"))
   {
    $error = true;
    $error_message[] ='jobseeker email_address already register as recruiter.';
   }
   elseif($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id"))
   {
    $error = true;
    $error_message[] ='jobseeker email_address already register as admin.';
   }
   elseif($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
   {
    $error = true;
    $error_message[] ='jobseeker email_address already register.';
   }

   if($error)
   {
    $import_message[$i]=array('error'=>'Row No '.($i+1).' : '.implode('<br>',$error_message));
   }
   else
   {
    if($row=getAnyTableWhereData(ZONES_TABLE,"zone_name ='".tep_db_input($state)."'",'zone_id'))
    {
     $state_id=$row['zone_id'];
    }
    else
    {
     $state_id=0;
    }
    if($address1=="")
	$address1 =" ";
    $sql_data_array=array('jobseeker_first_name'     => $first_name,
                          'jobseeker_middle_name' => $middle_name,
                          'jobseeker_last_name'=> $last_name,
                          'jobseeker_address1'     => $address1,
                          'jobseeker_address2'  => $address2,
                          'jobseeker_country_id'  => $country_id,
                          'jobseeker_city'    => $city,
                          'jobseeker_zip'=>$zip_code,
                          'jobseeker_phone'=> $phone,
                          'jobseeker_mobile'      => $mobile,
                          'jobseeker_newsletter'=> $subscribe_letter,     
                          );

	if($state_id > 0)
    {
     $sql_data_array['jobseeker_state']=NULL;
     $sql_data_array['jobseeker_state_id']=$state_id;
    }
    else
    {
     $sql_data_array['jobseeker_state']=$state;
     $sql_data_array['jobseeker_state_id']=0;
    }

	$full_location =	trim((($city!='')?$city.",":"").((tep_not_null($state))?$state:''));
    $full_location =   (($full_location!='')?$full_location.",":"").$country;
	$result=getLocationGeoAddress('address='.urlencode($full_location));
	
	if(is_array($result))
	{
     $sql_data_array['latitude']=$result['latitude'];
     $sql_data_array['longitude']=$result['longitude'];
	}
	else
	{
     $sql_data_array['latitude']=0;
     $sql_data_array['longitude']=0;
	}
    $sql_data_array1=array('inserted'=>'now()',
                           'jobseeker_email_address'=>$email_address,
                           'jobseeker_password'=>tep_encrypt_password($password)
                           );
     tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1);
     if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
	 {
      $jobseeker_id = $row['jobseeker_id'];

      $sql_data_array['jobseeker_id']= $jobseeker_id;
      tep_db_perform(JOBSEEKER_TABLE, $sql_data_array);
      $import_message[$i]=array('success'=>$email_address);
	 }
    ////////////////////////////////////
   }
  }
   return $import_message;
 }
}
function import_user_recruiter($content)
{
 if(!is_array($content[0]))
 return  0;
 $column_name_array=(array_keys($content[0]));
 $total_column = count($column_name_array);
 $import_error=false;
 if(!in_array('first_name',$column_name_array))
 {
  $import_error[]= 'recruiter first_name column  missing.';
 }
 if(!in_array('country',$column_name_array))
 {
  $import_error[]= 'recruiter country column  missing.';
 }
 if(!in_array('email_address',$column_name_array))
 {
  $import_error[]= 'recruiter email_address column  missing.';
 }
 if(!in_array('company_name',$column_name_array))
 {
  $import_error[]= 'recruiter company_name column  missing.';
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
   $first_name    = tep_db_prepare_input($content[$i]['first_name']);
   $last_name     = tep_db_prepare_input($content[$i]['last_name']);
   $email_address = tep_db_prepare_input($content[$i]['email_address']);
   $password      = tep_db_prepare_input($content[$i]['password']);
   $position      = tep_db_prepare_input($content[$i]['position']);
   $company_name  = tep_db_prepare_input($content[$i]['company_name']);
   $city          = tep_db_prepare_input($content[$i]['city']);
   $state         = tep_db_prepare_input($content[$i]['state']);
   $country       = tep_db_prepare_input($content[$i]['country']);
   $address1      = tep_db_prepare_input($content[$i]['address1']);
   $address2      = tep_db_prepare_input($content[$i]['address2']);
   $zip_code      = tep_db_prepare_input($content[$i]['zip_code']);
   $phone         = tep_db_prepare_input($content[$i]['phone']);   					
   $mobile        = tep_db_prepare_input($content[$i]['mobile']);   					
   $logo_url      = tep_db_prepare_input($content[$i]['logo_url']);   					
   $url           = tep_db_prepare_input($content[$i]['url']);   					
   $subscribe_letter = tep_db_prepare_input($content[$i]['subscribe_letter']);   					

   $error_message =array();
   if(strlen($first_name)<=0)
   {
    $error=true;
    $error_message[] ='recruiter first_name empty.';
   }
   if(!$row=getAnyTableWhereData(COUNTRIES_TABLE,"country_name ='".tep_db_input($country)."'",'id'))
   {
    $country_id = DEFAULT_COUNTRY_ID;
   }
   else
    $country_id=$row['id'];

   if(strlen($email_address)<=0)
   {
    $error=true;
    $error_message[] ='recruiter email_address empty.';
   }
   elseif(tep_validate_email($email_address) == false)
   {
    $error=true;
    $error_message[] ='Invaild email address.';
   }
   elseif($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
   {
    $error = true;
    $error_message[] ='recruiter email_address already register as recruiter.';
   }
   elseif($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id"))
   {
    $error = true;
    $error_message[] ='recruiter email_address already register as recruiter.';
   }
   elseif($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id"))
   {
    $error = true;
    $error_message[] ='recruiter email_address already register as admin.';
   }
   elseif($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
   {
    $error = true;
    $error_message[] ='recruiter email_address already register.';
   }

   if($error)
   {
    $import_message[$i]=array('error'=>'Row No '.($i+1).' : '.implode('<br>',$error_message));
   }
   else
   {
    if($row=getAnyTableWhereData(ZONES_TABLE,"zone_name ='".tep_db_input($state)."'",'zone_id'))
    {
     $state_id=$row['zone_id'];
    }
    else
    {
     $state_id=0;
    }
    if($address1=="")
	$address1 =" ";

    $sql_data_array=array('recruiter_first_name'   => $first_name,
                           'recruiter_last_name'   => $last_name,
                           'recruiter_position'    => $position,
                           'recruiter_company_name'=> $company_name,
                           'recruiter_address1'    => $address1,
                           'recruiter_address2'    => $address2,
                           'recruiter_city'        => $city,
                           'recruiter_country_id'  => $country_id,
                           'recruiter_zip'         => $zip_code,
                           'recruiter_telephone'   => $phone,
                           'recruiter_url'         => $url,
                           'recruiter_newsletter'  => $subscribe_letter   
                          );

	if($state_id > 0)
    {
     $sql_data_array['recruiter_state']=NULL;
     $sql_data_array['recruiter_state_id']=$state_id;
    }
    else
    {
     $sql_data_array['recruiter_state']=$state;
     $sql_data_array['recruiter_state_id']=0;
    }
    if(tep_not_null($logo_url))
	{ 
	  if($logo=saveLogoFromURL($logo_url))
	  {
       $sql_data_array['recruiter_logo']=$logo;
	  }
	}
     $sql_data_array1=array('inserted'=>'now()',
                           'recruiter_email_address'=>$email_address,
                           'recruiter_password'=>tep_encrypt_password($password)
                           );
     tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array1);
     if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
	 {
      $recruiter_id = $row['recruiter_id'];
      $sql_data_array['recruiter_id']= $recruiter_id;
      tep_db_perform(RECRUITER_TABLE, $sql_data_array);
      $import_message[$i]=array('success'=>$email_address);
	 }
    ////////////////////////////////////
   }
  }
   return $import_message;
 }
}
function saveLogoFromURL($image_url)
{
 if ( $data= getimagesize($image_url) )
 {
  $type=false;
  if(isset($data['mime']))
  switch($data['mime'])
  {
   case 'image/png':
    $type='png';
  break;
   case 'image/jpeg':
    $type='jpg';
    break;
   case 'image/gif':
    $type='gif';
    break;
  }
  if($type)
  {
   $file_name= basename($image_url,'.'.$type);
   $new_file_name =date("YmdHis").str_replace(array("'"," ","(",")"),array("_","_","_","_"),$file_name).".".$type;
   if( file_put_contents(PATH_TO_MAIN_PHYSICAL_LOGO.$new_file_name, file_get_contents($image_url)))
    return $new_file_name;
  }
  return false;  
 }
 else
 return false;
}

?>