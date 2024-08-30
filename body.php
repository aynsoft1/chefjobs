<?
 if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/header.php'))
  include_once(PATH_TO_THEMES.MODULE_THEME_DEFAULT_THEME.'/header.php');
 else
    include_once('header.php');

 if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/header_middle.php'))
  include_once(PATH_TO_THEMES.MODULE_THEME_DEFAULT_THEME.'/header_middle.php');
 else
    include_once('header_middle.php');
 
 if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/footer.php'))
  include_once(PATH_TO_THEMES.MODULE_THEME_DEFAULT_THEME.'/footer.php');
 else
    include_once('footer.php');
include_once("right.php");
include_once("left.php");
if(!defined("LEFT_HTML"))
define("LEFT_HTML","");
if(!defined("RIGHT_HTML"))
define("RIGHT_HTML","");
$template->assign_vars(array('HEADER_HTML' => HEADER_HTML,
 'HEADER_MIDDLE_HTML' => HEADER_MIDDLE_HTML,
 'FOOTER_HTML' => FOOTER_HTML,
 'RIGHT_HTML'  => RIGHT_HTML,
 'HOST_NAME'   => HOST_NAME, 
));
?>