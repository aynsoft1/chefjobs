<?
class theme_careerjobs
{
 var $theme_id;
 function __construct()
 {
  $this->theme_id = 'careerjobs';
 }
 function install_theme()
 {
 tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Maximum Home Middle Banner display', 'MODULE_THEME_CAREERJOBS_JOBS_MAX_FEATURED_BANNER', '9', 'Maximum home middle banner display', '9', '1', '', now())");
  tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Maximum latest jobs  display', 'MODULE_THEME_CAREERJOBS_MAX_LATEST_JOB', '6', 'Maximum latest jobs display', '9', '1', '', now())");
  tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Maximum category  display', 'MODULE_THEME_CAREERJOBS_MAX_JOB_CATEORY', '30', 'Maximum category display', '9', '2', '', now())");
  tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Maximum partners banner display', 'MODULE_THEME_CAREERJOBS_MAX_PARTNERS_BANNER', '6', 'Maximum partners banner', '9', '3', '', now())");
  tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Maximum career tools display', 'MODULE_THEME_CAREERJOBS_MAX_FEATURED_ARTICLE', '3', 'Maximum  article display', '9', '3', '', now())");
  tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Theme Menu Management', 'MODULE_THEME_CUSTOM_MENU', 'enable', 'Custom Menu', '9', '5', '', now())");

	}
 function remove_theme()
 {
  tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('".implode("', '",$this->keys())."')");
 }
 function keys()
 {
  return array('MODULE_THEME_CAREERJOBS_JOBS_MAX_FEATURED_BANNER','MODULE_THEME_CAREERJOBS_MAX_LATEST_JOB', 'MODULE_THEME_CAREERJOBS_MAX_JOB_CATEORY','MODULE_THEME_CAREERJOBS_MAX_PARTNERS_BANNER','MODULE_THEME_CAREERJOBS_MAX_FEATURED_ARTICLE','MODULE_THEME_CUSTOM_MENU');
 } 
}
?>