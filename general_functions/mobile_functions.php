<?
/*********************************************************
**********#	Name				  : Shambhu Prasad Patnaik		   #*******
**********#	Company			: Aynsoft							         #**********
**********#	Copyright (c) www.aynsoft.com 2011	#**********
*********************************************************/
////
function socialCMS_is_MobileBrowser()
{ return false;
 /*if(isset($_SERVER['HTTP_X_WAP_PROFILE']) ||isset($_SERVER['HTTP_PROFILE']))
 {
  return true;
 }
 $cms_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
 if (in_array(substr($cms_user_agent, 0, 4), socialCMS_browser_detection_ua_prefixes()))
 {
  return true;
 }
 $accept = strtolower($_SERVER['HTTP_ACCEPT']);
 if(strpos($accept,'wap') !== false)
 {
  return true;
 }
 if (preg_match("/(".socialCMS_browser_detection_ua_contains().")/i",$cms_user_agent))
 {
   return true;
 }
 if(isset($_SERVER['ALL_HTTP'])&&strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
 {
   return true;
 }*/

}
function socialCMS_browser_detection_ua_prefixes()
{
  return array('w3c ','w3c-','acs-','alav','alca','amoi','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','htc_','inno','ipaq','ipod','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','lg/u','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda ','xda-');
}
function socialCMS_browser_detection_ua_contains()
{
 return implode("|", array('android','blackberry','hiptop','ipod','lge vx','midp','maemo','mmp','netfront','nintendo DS','novarra','openweb','opera mobi','opera mini','palm','psp','phone','smartphone','symbian','up.browser','up.link','wap','windows ce','webos'));
}
?>