<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Date Created  : 25/06/2013          #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
header("HTTP/1.0 404 Not Found");
header('Content-Type: text/xml'); 
$message='<error>'."\n";
$message .='<status>error</status>'."\n";
$message .='<message>Page Not Found</message>'."\n";
$message.='</error>'; 	
	echo $message;
?>