<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/app_functions.php");
if(isset($_SESSION['sess_access_key']))
{
 $access_key  = tep_db_prepare_input($_SESSION['sess_access_key']);
}
else
 $access_key  = tep_db_prepare_input($_POST['access_key']);

$userInfo = false;
 

if($jobseeker_id =get_access_user($access_key))
{
	$fielsd='jl.jobseeker_id,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_last_name,jobseeker_address1,jobseeker_address2,jobseeker_country_id,jobseeker_city,jobseeker_phone,jobseeker_mobile, if(jobseeker_state_id,z.zone_name,jobseeker_state) as j_state';
 if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl left outer join  '.JOBSEEKER_TABLE.' as j on (jl.jobseeker_id=j.jobseeker_id)  left  outer join '.ZONES_TABLE.' as z on (j.jobseeker_state_id =z.zone_id) '," jl.jobseeker_id ='".$jobseeker_id."'",$fielsd))
 {
		//*
 	$data =array();
		$data1 =array();
  $data['result']['status'] = 'success';
		//$data1['id']            = $row['jobseeker_id'];
  if(isset($_SESSION['sess_access_key']))
		{
 		$data1['access_key'] = tep_db_output($access_key);
		 unset($_SESSION['sess_access_key']);
		}

		$data1['email_address'] = tep_db_output($row['jobseeker_email_address']);
		$data1['name']          = tep_db_output($row['jobseeker_first_name'].' '. $row['jobseeker_last_name']);
		$data1['address1']      = tep_db_output($row['jobseeker_address1']);
		$data1['address2']      = tep_db_output($row['jobseeker_address2']);
		$data1['country_id']    = tep_db_output($row['jobseeker_country_id']);
		$data1['state']         = tep_db_output($row['j_state']);
		$data1['city']          = tep_db_output($row['jobseeker_city']);
		$data1['phone']         = tep_db_output($row['jobseeker_phone']);
		$data1['mobile']        = tep_db_output($row['jobseeker_mobile']);

		$query = "select r.resume_id,r.resume_title from ".JOBSEEKER_RESUME1_TABLE." as r where r.jobseeker_id ='".tep_db_input($jobseeker_id)."' order by r.inserted asc";
  $result=tep_db_query($query);
  $x=tep_db_num_rows($result);
  $resumes=array();
		if($x>0)
		{
			while($row_r = tep_db_fetch_array($result))
			{
				$resumes[] =array('r_id' =>$row_r['resume_id'],'r_title'=>tep_db_output($row_r['resume_title']));
			}
   $data1['resumes']        = $resumes;
		}
		tep_db_free_result($result);


		
  $data['result']['data'] = $data1;
  $json = json_encode($data);
 	header('Content-Type: application/json'); 
 	echo $json; 
		//*/
		//echo 'success';
	}
}
else
{
	echo 'faild';
	die();
 /*
	header('Content-Type: application/json'); 
	$data =array();
 $data['result']['status'] = 'error';
 $data['result']['message'] = 'Invalid Authentication';
 $json = json_encode($data);
	echo $json; 
	*/
}
?>