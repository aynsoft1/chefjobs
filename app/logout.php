<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Date Created  : 25/06/2013          #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
header('Content-Type: text/xml'); 
session_start();
@session_unset();
@session_destroy();
echo '<success>'."\n".'<status>error</status>'."\n".'<message>successfully logout</message>'."\n".'</success>';
?>