<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
if(isset($_SESSION['google_error']))
{
 $message=$_SESSION['google_error'];
 $messageStack->add_session($message,'error');
// unset($_SESSION['google_error']);
}
if(tep_not_null($_SESSION['REDIRECT_URL']))
{
 $url=$_SESSION['REDIRECT_URL'];
 unset($_SESSION['REDIRECT_URL']);
}
else
 $url=FILENAME_INDEX;
?>
<html>
<head>
<title>Google Logout</title>

<script type="text/javascript">
  function GooogleLogout()
  {
    window.location="<?echo $url;?>";
  }
  function GoogleLogoutStart()
  {
   var t=setTimeout("GooogleLogout()",1000);
  }
</script>
</head>
<body>
<iframe style="display:none" src="https://accounts.google.com/Logout?hl=en&continue=http://www.google.com" onload="GoogleLogoutStart();"></iframe>
<div id="profiles">
<br>
 Logout to google account ..............
<br>
<br>
<br>

if not redirect automatically then <a href="<?echo $url;?>">click here</a>
</div>
</body>
</html>