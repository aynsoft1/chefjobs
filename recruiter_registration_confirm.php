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

include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_REGISTRATION_CONFIRM);

$template->set_filenames(array('confirm' => 'recruiter_registration_confirm.htm'));

include_once(FILENAME_BODY);

if(!isset($_SESSION['sess_new_recruiter']))

{

 tep_redirect(tep_href_link(FILENAME_INDEX));

}

$template->assign_vars(array(

 'HEADING_TITLE'=>HEADING_TITLE,

//'TEXT_ACCOUNT_CREATED'=>sprintf(TEXT_ACCOUNT_CREATED,tep_db_output($_SESSION['sess_user_name']),tep_db_output($_SESSION['sess_email_address']),tep_db_output($_SESSION['sess_password'])),
 'TEXT_ACCOUNT_CREATED'=>sprintf(TEXT_ACCOUNT_CREATED,tep_db_output($_SESSION['sess_user_name']),tep_db_output($_SESSION['sess_email_address']),'XXXXXXXXXX'),
 'button'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_RATES).'" class="btn btn-primary">'.BTN_NEXT.'</a>',

 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,

 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,

 'LEFT_HTML'=>LEFT_HTML,

 'RIGHT_HTML'=>RIGHT_HTML,

 'update_message'=>$messageStack->output()));

$template->pparse('confirm');

unset($_SESSION['sess_new_recruiter']);

unset($_SESSION['sess_user_name']);

unset($_SESSION['sess_email_address']);

unset($_SESSION['sess_password']);

?>