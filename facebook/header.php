<?
define('FACEBOOK_HEADER_HTML','<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:fb="http://ogp.me/ns/fb#">
<head>
<title>'.$obj_title_metakeyword->title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
'.$obj_title_metakeyword->metakeywords.'
<meta http-equiv="Cache-Control" content="no-cache" />
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script src="jquery-1.9.1.js"></script>
<script src="search.js"></script>
<script src="search.js"></script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main_table" align="center">
	<tr>
   <td id="header_top_home">
  	<div class="header"><img src="img/logo.png" /></div>
			<div class="search_box_home">
							 <div >
        <form name="search_form" action="'.FILENAME_JOB_SEARCH.'" method="post">
							<input name="keyword" type="text"  class="textfield_effect1" placeholder="Keyword"  id="search_keyword"  OnFocus=""/>
							<input name="location" type="text"  class="textfield_effect1" placeholder="Location" id="search_location"  OnFocus=""/>
											<input type="image" src="img/find.png" alt="Find" border="0"  class="find_job">
											</form>
							 </div>
			</td>
 </tr>');
	?>