<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
$linkedin_app_key  = MODULE_LINKEDIN_PLUGIN_APP_KEY; 
if(isset($_SESSION['linkedin_error']))
{
 $message=$_SESSION['linkedin_error'];
 $messageStack->add_session($message,'error');
 unset($_SESSION['linkedin_error']);
}
if(tep_not_null($_SESSION['REDIRECT_URL']))
{
 $url=$_SESSION['REDIRECT_URL'];
 unset($_SESSION['REDIRECT_URL']);
}
else
 $url=FILENAME_INDEX;
if(!isset($_SESSION['linkedin_page']))
tep_redirect(FILENAME_INDEX);

?>
<html>
<head>
<title>LinkedIn Logout</title>
<!-- 1. Include the LinkedIn JavaScript API and define a onLoad callback function -->
<script type="text/javascript" src="https://platform.linkedin.com/in.js">
  api_key:<?echo $linkedin_app_key;echo"\n";?>
  onLoad: onLinkedInLoad
  authorize: true
</script>

<script type="text/javascript">
  // 2. Runs when the JavaScript framework is loaded

  function onLinkedInLoad() {
    IN.Event.on(IN, "auth", onLinkedInAuth);
    IN.Event.on(IN, "logout",onLinkedInLogout);
  }

  // 2. Runs when the viewer has authenticated

  function onLinkedInAuth() {
    IN.User.logout();
  }
  function onLinkedInLogout()
  {
    window.location="<?echo $url;?>";
  }
  // 2. Runs when the Profile() API call returns successfully
</script>
</head>
<body>
<!-- 3. Displays a button to let the viewer authenticate -->
<script type="IN/Login">
Hello, <? echo '<';?>?js= firstName ?<? echo '>';?> <? echo '<';?>?js= lastName ?<? echo '>';?>.</script>

<!-- 4. Placeholder for the greeting -->
<div id="profiles">
<br>
logout to linkenin account ..............
<br>
<br>
<br>

if not redirect automatically then <a href="<?echo $url;?>">click here</a>
</div>
</body>
</html>