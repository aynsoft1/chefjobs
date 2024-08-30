<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_BY_MAP);
$template->set_filenames(array('job_by_map' => 'job_by_map.htm'));
include_once(FILENAME_BODY);

$template->assign_vars(array(
 'HEADING_TITLE'    => HEADING_TITLE,
 //'INFO_TEXT_MAIN'   => INFO_TEXT_MAIN,
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'MAP_SCRIPT'        => 'jobsearch_pagination(0,"","","","'.DEFAULT_COUNTRY_ID.'","","0","","","1","","0","'.HOST_NAME.'",2);',
 'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('job_by_map');
?>