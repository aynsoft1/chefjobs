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
$array_selected_box=array("configuration","tools");
// default open navigation box
if($_SESSION['selected_box']=='')
{
 $_SESSION['selected_box'] = 'dashboard';
}
elseif((strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.PATH_TO_ADMIN.FILENAME_ADMIN1_CONTROL_PANEL))
{
 $_SESSION['selected_box'] = 'dashboard';
}
elseif (isset($_GET['selected_box']))
{
 $_SESSION['selected_box'] = $_GET['selected_box'];
}

$LEFT_HTML='';

$heading = array();
$contents = array();
$heading[] = array('text'  =>BOX_HEADING_DASHBOARD,
                   'link'  =>FILENAME_ADMIN1_CONTROL_PANEL.'?selected_box=dashboard',
                   'default_row'=>(($_SESSION['selected_box'] == 'dashboard') ?'1':''),
                   'text_image'=>'<ion-icon name="speedometer-outline" style="color: #696969;margin: 1px 5px 0 10px;font-size: 18px;position:absolute;"></ion-icon>',
                   );
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);

if (tep_admin_check_boxes('admin_recruiters.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_recruiters.php');
}
if (tep_admin_check_boxes('admin_jobseekers.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_jobseekers.php');
}
if (tep_admin_check_boxes('admin_applicants.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_applicants.php');
}
if (tep_admin_check_boxes('admin_jobs.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_jobs.php');
}
if (tep_admin_check_boxes('admin_orders.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_orders.php');
}
if (tep_admin_check_boxes('admin_jobfair.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_jobfair.php');
}
if (tep_admin_check_boxes('admin_quiz.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_quiz.php');
}
if (tep_admin_check_boxes('admin_lms.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_lms.php');
}
if (tep_admin_check_boxes('admin_job_alerts.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_job_alerts.php');
}
if (tep_admin_check_boxes('admin_search.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_search.php');
}
if (tep_admin_check_boxes('admin_reports.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_reports.php');
}
if (tep_admin_check_boxes('admin_themes.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_themes.php');
}
if (tep_admin_check_boxes('admin_setting.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_setting.php');
}
if (tep_admin_check_boxes('admin_rate_card.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_rate_card.php');
}
if (tep_admin_check_boxes('admin_coupon_manager.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_coupon_manager.php');
}
if (tep_admin_check_boxes('admin_seo.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_seo.php');
}
if (tep_admin_check_boxes('admin_social.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_social.php');
}
if (tep_admin_check_boxes('admin_banner_management.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_banner_management.php');
}
if (tep_admin_check_boxes('admin_tools.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_tools.php');
}
if (tep_admin_check_boxes('admin_email_template.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_email_template.php');
}
if (tep_admin_check_boxes('admin_email_template1.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_email_template1.php');
}
if (tep_admin_check_boxes('admin_import_jobs.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_import_jobs.php');
}
if (tep_admin_check_boxes('admin_newsletter.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_newsletter.php');
}
if (tep_admin_check_boxes('admin_messages.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_messages.php');
}
if (tep_admin_check_boxes('admin_configuration.php') == true)
{
include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_configuration.php');
}
if (tep_admin_check_boxes('admin_administrator.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_administrator.php');
}
if (tep_admin_check_boxes('admin_localization.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_localization.php');
}
if (tep_admin_check_boxes('admin_article_manager.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_article_manager.php');
}
if (tep_admin_check_boxes('admin_course_manager.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_course_manager.php');
}
if (tep_admin_check_boxes('admin_forum_manager.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_forum_manager.php');
}
if (tep_admin_check_boxes('admin_cron.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_cron.php');
}
if (tep_admin_check_boxes('admin_print.php') == true)
{
 include_once(PATH_TO_MAIN_PHYSICAL_BOX.'admin_print.php');
}
if($LEFT_HTML=='')
{
 $LEFT_HTML='<font size="1">&nbsp;Sorry You have no &nbsp;permission.</font>';
}
?>