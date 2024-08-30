<?php
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class twitter
{
 private $user_name;
 private $oauth_consumer_key;
 private $app_consumer_secret;
 private $oauth_token;
 private $oauth_token_secret;
 public $message;

 function twitter()
 {
  $this->user_name= MODULE_TWITTER_SUBMITTER_USER_ID; 
  $this->oauth_consumer_key = MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY;
  $this->app_consumer_secret = check_data1(MODULE_TWITTER_SUBMITTER_APP_CONSUMER_SECRET,'##@##','consumer','passw');
  $this->oauth_token = MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN;
  $this->oauth_token_secret = check_data1(MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN_SECRET,'##@##','token','secret');
 }
 ///////////////////////
 function get_oauthNonce()
 {
  $mt = microtime();
  $rand = mt_rand();
  return md5($mt . $rand);
 }
 function urlencode_rfc3986($input)
 {
  if (is_array($input))
  {
   return array_map('urlencode_rfc3986', $input);
  }
  else if (is_scalar($input))
  {
   return str_replace('+',' ',str_replace('%7E', '~', rawurlencode($input)));
  }
  else 
  {
   return '';
  }
 }
 ///
 function get_authSignature($url='',$parameter='',$method='get')
 {
  $parameter=(array)$parameter;
  $method_array=array('get','post');
  if(!in_array(strtolower($method),$method_array))
  $method='get';
  $method=strtoupper($method);
  $url=urlencode($url);
  ksort($parameter);
  $new_parameter =array();
  foreach($parameter as $key => $value)
  {
   $new_parameter[] = twitter::urlencode_rfc3986($key) . '=' . twitter::urlencode_rfc3986($value);
  }
  $parameter=implode('&',$new_parameter);
  $base_string=($method.'&'.$url.'&'.urlencode($parameter));
  $secret_key=  array('app_consumer_secret' => $this->app_consumer_secret,
                        'oauth_token_secret' => $this->oauth_token_secret);
  ksort($secret_key);
  $new_parameter =array();
  foreach($secret_key as $key => $value)
  {
   $new_parameter[] =twitter::urlencode_rfc3986($value);
  }
  $secret_key=implode('&',$new_parameter);
  ///////
  if (function_exists('hash_hmac'))
		{
   return base64_encode(hash_hmac('sha1', $base_string,$secret_key, true));
		}
		else
  {
   $blocksize	= 64;
   $hashfunc	= 'sha1';
   if (strlen($secret_key) > $blocksize)
   {
    $secret_key= pack('H*', $hashfunc($secret_key));
   }
   $secret_key= str_pad($secret_key,$blocksize,chr(0x00));
   $ipad	= str_repeat(chr(0x36),$blocksize);
   $opad	= str_repeat(chr(0x5c),$blocksize);
   $hmac 	= pack(
                'H*',$hashfunc(
                    ($secret_key^$opad).pack(
                        'H*',$hashfunc(
                            ($secret_key^$ipad).$base_string
                        )
                    )
                )
            );
   return base64_encode($hmac);
  }
  ///////
 }
 ///////////////////
 function twitter_check_authentication($oauth_consumer_key='',$app_consumer_secret='',$oauth_token='',$oauth_token_secret='')
 {
  $this->oauth_consumer_key  = $oauth_consumer_key;
  $this->app_consumer_secret = $app_consumer_secret;
  $this->oauth_token         = $oauth_token;
  $this->oauth_token_secret  = $oauth_token_secret;
  
  $authentication_url = 'https://api.twitter.com/1/account/verify_credentials.xml';

  if($this->oauth_consumer_key=='')
  {
   $this->message='Invalid Consumer Key.';
   return false;
  }
  if($this->app_consumer_secret=='')
  {
   $this->message='Invalid Consumer Secret.';
   return false;
  }
  if($this->oauth_token=='')
  {
   $this->message='Invalid Access Token.';
   return false;
  }
  if($this->oauth_token_secret=='')
  {
   $this->message='Invalid Access Token Secret.';
   return false;
  }
   $authentication_parameter=array('oauth_consumer_key'=>$this->oauth_consumer_key,
                                   'oauth_nonce'=>twitter::get_oauthNonce(),
                                   'oauth_signature_method'=>'HMAC-SHA1',
                                   'oauth_timestamp'=> time(),
                                   'oauth_token'=>$this->oauth_token,
                                   'oauth_version'=>'1.0',
                                 );
  $signature=twitter::get_authSignature($authentication_url,$authentication_parameter,$method='get');
  $authentication_parameter['oauth_signature']=$signature;
  ksort($authentication_parameter);
  $new_parameter =array();
  foreach($authentication_parameter as $key => $value)
  {
   $new_parameter[] = twitter::urlencode_rfc3986($key) . '=' . twitter::urlencode_rfc3986($value);
  }
  $parameter=implode('&',$new_parameter);

  $headers = array("X-Twitter-Client" => "socialcms", "X-Twitter-Client-Version" => "1.0", "X-Twitter-Client-URL" => "http://www.socialcms.com/twitter_tools.xml"); 
  $ch = curl_init();	
  curl_setopt($ch, CURLOPT_URL,$authentication_url.'?'.$parameter);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); 
	 curl_setopt($ch, CURLOPT_TIMEOUT,60);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $response=curl_exec($ch);
  $info=curl_getinfo($ch);
  curl_close($ch);
  if($info['http_code']==200)
  {
   if(preg_match('/<screen_name>(.*)<\/screen_name>/i',$response,$match))
    $this->message=$match[1];
   return true;
  }
  else
  {
   $this->message='Invalid user name or password.';
   return false;
  }
 }
 ///////////////////////
 function twitter_post_status($status)
 {
  if($status=='')
   return false;
  if(MODULE_TWITTER_SUBMITTER!='enable')
   return false;
  
  if($this->oauth_consumer_key=='')
  {
   $this->message='Invalid Consumer Key.';
   return false;
  }
  if($this->app_consumer_secret=='')
  {
   $this->message='Invalid Consumer Secret.';
   return false;
  }
  if($this->oauth_token=='')
  {
   $this->message='Invalid Access Token.';
   return false;
  }
  if($this->oauth_token_secret=='')
  {
   $this->message='Invalid Access Token Secret.';
   return false;
  }

  $status_update_url = 'http://twitter.com/statuses/update.xml';
  $status=substr(stripslashes(trim($status)),0,140);
  $post_parameter=array('oauth_consumer_key'=>$this->oauth_consumer_key,
                                   'oauth_nonce'=>twitter::get_oauthNonce(),
                                   'oauth_signature_method'=>'HMAC-SHA1',
                                   'oauth_timestamp'=> time(),
                                   'oauth_token'=>$this->oauth_token,
                                   'oauth_version'=>'1.0',
                                   'status'=>$status,
                                   'source'=>'socialcms',
                      
                                 );
  $signature=twitter::get_authSignature($status_update_url,$post_parameter,'POST');
  $post_parameter['oauth_signature']=$signature;
  ksort($post_parameter);
  $new_parameter =array();
  foreach($post_parameter as $key => $value)
  {
   $new_parameter[] = twitter::urlencode_rfc3986($key) . '=' . twitter::urlencode_rfc3986($value);
  }
  $parameter=implode('&',$new_parameter);
  $status=substr(stripslashes(trim($status)),0,140);
  $headers = array("X-Twitter-Client" => "socialcms", "X-Twitter-Client-Version" => "1.0", "X-Twitter-Client-URL" => "http://www.socialcms.com/twitter_tools.xml"); 
  $ch = curl_init();	
  curl_setopt($ch, CURLOPT_URL,$status_update_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$parameter);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); 
	 curl_setopt($ch, CURLOPT_TIMEOUT,60);
  $response=curl_exec($ch);
  $info=curl_getinfo($ch);
  curl_close($ch);
  if($info['http_code']==200)
  {
   if(preg_match('/<error>(.*)<\/error>/i',$response,$match))
   {
    $this->message=$match[1];
    return false;
   }
   if(preg_match('/<id>(.*)<\/id>/i',$response,$match))
   {
    $this->message=$match[1];
   }
   return true;
  }
  else
   return false;
 }
///////////////////////
}
?>