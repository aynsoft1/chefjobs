<?php
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once 'facebookoauth.php';

class FacebookPost  extends  FacebookOAuth
{
  private $facebook_app_auth  ;
 	public $FacebookPost  = MODULE_FACEBOOK_PLUGIN_JOB_SUBMITTER;

  const FACEBOOK_GRAPH_URI = 'https://graph.facebook.com';

  function __construct()
  {
   $clientId    = MODULE_FACEBOOK_PLUGIN_APP_KEY; 
   $clientSecret = check_data1(MODULE_FACEBOOK_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
			$this->facebook_app_auth = check_data1(MODULE_FACEBOOK_PLUGIN_APP_AUTH,'####','app','key');
   parent::__construct($clientId, $clientSecret,'');
  } 
  public function postLink($pageID,$pageLink)
	 {
			if($pageID=='' || $pageID<=0)
				return false;
			if($pageLink=='')
				return false;
			if($this->FacebookPost!='enable')
			{
				return false;
			}
   if($this->clientId=='' &&  $this->clientSecret=='' && $this->facebook_app_auth=='' && $this->facebook_app_auth=='-1' )
			{
				return false;
			}
			$access_token= $this->facebook_app_auth;
 		$this->setAccessToken($access_token);   

			$access_token1= json_decode($access_token,true);
			if(isset($access_token1['expired']))
			{
				$c_date =date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")+15, date("Y")));
				$c_time = strtotime($c_date);
			 $exp = strtotime($access_token1['expired']);
    if($c_time >= $exp)//renew
    {
			  if(!$data =$this->setExtendedAccessToken())
			  {
				  return false;
			  }
 				parse_str($data,$output);
			 	$new_token = array();
    	$new_token['access_token']= $output['access_token'];
					if(isset($output['expires']))
					{
				 	$new_token['expired']= date('Y-m-d',time()+$output['expires']);
					}
					$data=json_encode($new_token);
     /// renew token
				 $sql_data_array=array('configuration_value'=>encode_string('app####'.$data.'####key'),'updated'=>'now()');
     tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_AUTH'");   
					//
					$this->setAccessToken($data);
				}
			}
   $url= 'https://graph.facebook.com/v2.8/'.$pageID.'/feed/';
			$parametter =array('link'=>$pageLink,'access_token'=>urlencode($this->accessToken['access_token']));
		 $content = $this->post($url,$parametter);
   if($this->http_code!=200)
   return false;
			else
   return $content;
		}
}