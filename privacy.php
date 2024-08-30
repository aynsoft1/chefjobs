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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_PRIVACY);
$template->set_filenames(array('privacy' => 'privacy.htm'));
include_once(FILENAME_BODY);
$template->assign_vars(array(
'HEADING_TITLE'=>HEADING_TITLE,
'HEADING_ONE'=>HEADING_ONE,
'HEADING_TWO'=>HEADING_TWO,
'HEADING_THREE'=>HEADING_THREE,
'HEADING_FOUR'=>HEADING_FOUR,
'HEADING_FIVE'=>HEADING_FIVE,
'HEADING_SIX'=>HEADING_SIX,
'HEADING_SEVEN'=>HEADING_SEVEN,
'HEADING_EIGHT'=>HEADING_EIGHT,
'HEADING_NINE'=>HEADING_NINE,
'HEADING_TEN'=>HEADING_TEN,
'PARA_ONE'=>PARA_ONE,
'PARA_TWO'=>PARA_TWO,
'PARA_THREE'=>PARA_THREE,
'PARA_FOUR'=>PARA_FOUR,
'PARA_FIVE'=>PARA_FIVE,
'PARA_SIX'=>PARA_SIX,
'PARA_SEVEN'=>PARA_SEVEN,
'PARA_EIGHT'=>PARA_EIGHT,
'PARA_NINE'=>PARA_NINE,
'PARA_TEN'=>PARA_TEN,
'PARA_ELEVEN'=>PARA_ELEVEN,
'PARA_TWELVE'=>PARA_TWELVE,
'PARA_THIRTEEN'=>PARA_THIRTEEN,
'PARA_FOURTEEN'=>PARA_FOURTEEN,
'PARA_FIFTEEN'=>PARA_FIFTEEN,
'PARA_SIXTEEN'=>PARA_SIXTEEN,
'PARA_SEVENTEEN'=>PARA_SEVENTEEN,
'PARA_EIGHTEEN'=>PARA_EIGHTEEN,
'PARA_NINETEEN'=>PARA_NINETEEN,
'PARA_TWENTY'=>PARA_TWENTY,
'UL_ONE'=>UL_ONE,
'UL_TWO'=>UL_TWO,
'UL_THREE'=>UL_THREE,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('privacy');
?>