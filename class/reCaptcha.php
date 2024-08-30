<?php
class reCaptcha  
{
  private $reCaptchaSecret  ;
  public $reCaptchaKey  ;

  const RECAPTCHA_VERIFY_URI = 'https://www.google.com/recaptcha/api/siteverify';

  function __construct()
  {
   $this->reCaptchaKey    = MODULE_G_RECAPTCHA_PLUGIN_KEY; 
   $this->reCaptchaSecret = check_data1(MODULE_G_CAPTCHA_PLUGIN_SECRET_KEY,'##@##','gapp','passw');
  } 

  function reCaptchaGetCaptcha()
  {
    return '<div class="g-recaptcha"   data-sitekey="'.$this->reCaptchaKey.'"></div>';
  } 
  
  public function reCaptchaVerify( $response='', $remoteIp='')
  {
   if($response=='' && isset($_POST['g-recaptcha-response']))
    $response= tep_db_prepare_input($_POST['g-recaptcha-response']);
   elseif($response=='' && isset($_GET['g-recaptcha-response']))
    $response= tep_db_prepare_input($_GET['g-recaptcha-response']);
   if($response=='')
    return false;
   if($remoteIp=='')
   $remoteIp= $_SERVER['REMOTE_ADDR'];
   if($this->reCaptchaKey=='')
    return false;
   if($this->reCaptchaSecret=='')
    return false;
   
   $post_field=array('secret'=>$this->reCaptchaSecret,
                   'response'=>urlencode($response),
                   'remoteip'=>urlencode($remoteIp),
				   );

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_USERAGENT,(isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)"));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_URL,self::RECAPTCHA_VERIFY_URI);
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post_field);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

   $data = curl_exec($ch);
   $error = curl_error($ch);
   $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   curl_close($ch);
   if($http_code!=200)
   return false;
   $data= json_decode($data,true);
   if($data['success']=='true')
   return true;
   return false;
  }	 
}
?>