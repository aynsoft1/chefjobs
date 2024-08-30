<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT);
$template->set_filenames(array('simplyhired' => 'admin1_simplyhired_import.htm','simplyhired1' => 'admin1_simplyhired_import1.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$search_status1=tep_db_prepare_input($_GET['search_status']);
$job_category_ids_array=array();

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . SIMPLYHIRED_FEED_TABLE . " where feed_id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page']));
  case 'fetch_now':
   $id = (int)tep_db_prepare_input($_GET['id']);
   include_once("../general_functions/simply_hired_xml_import.php");
   simplyhired_feed_import($id);
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page']));
  case 'feed_active':
  case 'feed_inactive':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("update ".SIMPLYHIRED_FEED_TABLE ." set status='".($action=='feed_active'?'active':'inactive')."' where feed_id='".$id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT,tep_get_all_get_params(array('action','selected_box'))));
  case 'save':
  case 'update':
   $campaign_name = tep_db_prepare_input($_POST['TR_campaign_name']);
   $publisher_id  = tep_db_prepare_input($_POST['TR_publisher_id']);
   $recruiter_id  = tep_db_prepare_input($_POST['TR_recruiter_id']);
   $location      = tep_db_prepare_input($_POST['location']);
   $feed_country  = tep_db_prepare_input($_POST['feed_country']);
   $job_company   = tep_db_prepare_input($_POST['job_company']);
   $sort_by       = tep_db_prepare_input($_POST['sort_by']);
   $max_feed      = tep_db_prepare_input($_POST['IR_max_feed']);
   $job_duration  = tep_db_prepare_input($_POST['IR_job_duration']);
   $job_title     = tep_db_prepare_input($_POST['job_title']);
   $job_company   = tep_db_prepare_input($_POST['job_company']);
   $status        = tep_db_prepare_input($_POST['status']);
   $total_keywords=count($_POST['check']);
   if($total_keywords>0)
   {
    $job_categories=implode(',',tep_db_prepare_input($_POST['check']));
    $job_category_ids_array=explode(',',$job_categories);
   }

   $sql_data_array=array(
                         'feed_title'   => $campaign_name,
                         'publisher_id' => $publisher_id,
                         'recruiter_id' => $recruiter_id,
                         'feed_keyword' => $job_categories,
                         'location'     => $location,
                         'country_code' => $feed_country,
                         'job_company'  => $job_company,
                         'sort_by'      => $sort_by,
                         'max_feed'     => $max_feed,
                         'job_title'    => $job_title,
                         'job_duration' => $job_duration,
                         'status '      => $status ,
                        );
   $error=false;
   if($total_keywords<=0)
   {
 			$messageStack->add(MESSAGE_KEYWORD_ERROR, 'error');
    $error=true;
   }
			if(!$recruiter_check=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id ='".tep_db_input($recruiter_id)."'",'recruiter_id'))
   {
 			$messageStack->add(MESSAGE_INVALID_RECRUITER_ERROR, 'error');
    $error=true;
	  }
			if($max_feed<=0)
   {
 			$messageStack->add(MESSAGE_INVALID_MAX_FEED_ERROR, 'error');
    $error=true;
	  }
			if($max_feed>=101)
   {
 			$messageStack->add(MESSAGE_INVALID_MAX_FEED_EXCESS_ERROR, 'error');
    $error=true;
	  }
			if($job_duration<=0)
   {
 			$messageStack->add(MESSAGE_INVALID_JOB_DURATION_ERROR, 'error');
    $error=true;
	  }
   if(!tep_not_null($campaign_name))
   {
 			$messageStack->add(MESSAGE_CAMPAIGN_NAME_ERROR, 'error');
    $error=true;
	  }
   if(!tep_not_null($publisher_id))
   {
 			$messageStack->add(MESSAGE_PUBLISHER_ID_ERROR, 'error');
    $error=true;
	  }
   if(!$error)
   {
 			if($action=='save')
    {
     if($row_chek=getAnyTableWhereData(SIMPLYHIRED_FEED_TABLE,"feed_title='".tep_db_input($campaign_name)."'",'feed_id'))
     {
      $messageStack->add(MESSAGE_NAME_ERROR, 'error');
     }
     else
     {
      $sql_data_array['inserted']='now()';
      tep_db_perform(SIMPLYHIRED_FEED_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
      tep_redirect(FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT);
     }
    }
			 else
    {
     $feed_id=(int)$_GET['id'];
     if($row_chek=getAnyTableWhereData(SIMPLYHIRED_FEED_TABLE,"feed_title='".tep_db_input($campaign_name)."' and feed_id!='$feed_id'",'feed_id'))
     {
      $messageStack->add(MESSAGE_NAME_ERROR, 'error');
     }
     else
     {
      $sql_data_array['updated']='now()';
      tep_db_perform(SIMPLYHIRED_FEED_TABLE, $sql_data_array, 'update', "feed_id = '" .$feed_id. "'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
      tep_redirect(FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT.'?page='.$_GET['page'].'&id='.$feed_id);
     }
    }
   }
  break;
 }
}
if($action=='new' || $action=='insert' || $action=='update' ||$action=='edit' ||$action=='save')
{
 $feed_id = tep_db_prepare_input($_GET['id']); 
 if($action=='new')
 {
  $sort_by    = 'dd';
  $max_feed     = 10;
  $job_duration = 60;
 }
 if(($action=='update' || $action=='edit') && $feed_id>0)
 {
  if($action=='edit')
  {
  	$row_simplyhired=getAnyTableWhereData(SIMPLYHIRED_FEED_TABLE,"feed_id='".tep_db_input($feed_id)."'");
   $campaign_name = tep_db_prepare_input($row_simplyhired['feed_title']);
   $publisher_id  = tep_db_prepare_input($row_simplyhired['publisher_id']);
   $recruiter_id  = tep_db_prepare_input($row_simplyhired['recruiter_id']);
   $location      = tep_db_prepare_input($row_simplyhired['location']);
   $feed_country  = tep_db_prepare_input($row_simplyhired['country_code']);
   $job_company   = tep_db_prepare_input($row_simplyhired['job_company']);
   $sort_by       = tep_db_prepare_input($row_simplyhired['sort_by']);
   $max_feed      = tep_db_prepare_input($row_simplyhired['max_feed']);
   $job_title     = tep_db_prepare_input($row_simplyhired['job_title']);
   $job_duration  = tep_db_prepare_input($row_simplyhired['job_duration']);   
   $status        = tep_db_prepare_input($row_simplyhired['status']);
   $job_category_ids_array=explode(',',tep_db_prepare_input($row_simplyhired['feed_keyword']));
  }
  $feed_form     = tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT,'id='.$feed_id.'&page='.$_GET['page'].'&action=update','post',' onsubmit="return ValidateForm(this)"');
  $feed_button   = '<a href="'.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT.'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a> '.tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);  
 }
 else
 {
  $feed_form     = tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'action=save','post',' onsubmit="return ValidateForm(this)"');
  $feed_button   = '<a href="'.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT.'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a> '.tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT);
 }
/////////////////////////////////////////////////////////////
 $feed_country_array=array();
 $feed_country_array[]=array('id'=>'us','text'=>'United States');
 $feed_country_array[]=array('id'=>'ar','text'=>'Argentina');
 $feed_country_array[]=array('id'=>'au','text'=>'Australia');
 $feed_country_array[]=array('id'=>'at','text'=>'Austria');
 $feed_country_array[]=array('id'=>'be','text'=>'Belgium');
 $feed_country_array[]=array('id'=>'br','text'=>'Brazil');
 $feed_country_array[]=array('id'=>'ca','text'=>'Canada');
 $feed_country_array[]=array('id'=>'fr','text'=>'France');
 $feed_country_array[]=array('id'=>'de','text'=>'Germany');
 $feed_country_array[]=array('id'=>'in','text'=>'India');
 $feed_country_array[]=array('id'=>'ie','text'=>'Ireland');
 $feed_country_array[]=array('id'=>'it','text'=>'Italy');
 $feed_country_array[]=array('id'=>'mx','text'=>'Mexico');
 $feed_country_array[]=array('id'=>'nl','text'=>'Netherlands');
 $feed_country_array[]=array('id'=>'pt','text'=>'Portugal');
 $feed_country_array[]=array('id'=>'ru','text'=>'Russia');
 $feed_country_array[]=array('id'=>'za','text'=>'South Africa');
 $feed_country_array[]=array('id'=>'es','text'=>'Spain');
 $feed_country_array[]=array('id'=>'se','text'=>'Sweden');
 $feed_country_array[]=array('id'=>'ch','text'=>'Switzerland');
 $feed_country_array[]=array('id'=>'gb','text'=>'United Kingdom');

 $feed_sort_array=array();
 $feed_sort_array[]=array('id'=>'rd','text'=>'relevance descending');//default
 $feed_sort_array[]=array('id'=>'ra','text'=>'relevance ascending');
 $feed_sort_array[]=array('id'=>'dd','text'=>'last seen date descending');
 $feed_sort_array[]=array('id'=>'td','text'=>'last seen date ascending');
 $feed_sort_array[]=array('id'=>'ta','text'=>'title ascending');
 $feed_sort_array[]=array('id'=>'ca','text'=>'company descending');
 $feed_sort_array[]=array('id'=>'cd','text'=>'company ascending');
 $feed_sort_array[]=array('id'=>'ld','text'=>'location descending');
 $feed_sort_array[]=array('id'=>'la','text'=>'location ascending');

 $field_names="id,category_name";
 $whereClause=" where sub_cat_id is null";
 $query = "select $field_names from ".JOB_CATEGORY_TABLE." $whereClause  order by category_name  ";
 $result=tep_db_query($query);
 $i=1;
 $j=0;
 $k=0;
 $l=0;
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["id"];
  if($i%3 == 1)
  {
   $template->assign_block_vars('job_category1', array( 
                                'row_selected' =>'class="dataTableRow'.(($j%2==0)?1:2).'"',  
                                'check_box' => tep_draw_checkbox_field('check[]',$ide,false,(in_array($ide,$job_category_ids_array)?$ide:''),'id="ch_'.$ide.'"'),  
                                'job_category'=>'<label for="ch_'.$ide.'">'.tep_db_output($row["category_name"]).'</label>',
                               ));  
   $j++;
  }
  elseif($i%3 == 2)
  {
   $template->assign_block_vars('job_category2', array( 
                                'row_selected' =>'class="dataTableRow'.(($k%2==0)?1:2).'"',  
                                'check_box' => tep_draw_checkbox_field('check[]',$ide,false,(in_array($ide,$job_category_ids_array)?$ide:''),'id="ch_'.$ide.'"'),  
                                'job_category'=>'<label for="ch_'.$ide.'">'.tep_db_output($row["category_name"]).'</label>',
                               ));  
   $k++;
  }
  else
  {
   $template->assign_block_vars('job_category3', array( 
                                'row_selected' => 'class="dataTableRow'.(($l%2==0)?1:2).'"',  
                                'check_box'    => tep_draw_checkbox_field('check[]',$ide,false,(in_array($ide,$job_category_ids_array)?$ide:''),'id="ch_'.$ide.'"'),  
                                'job_category' => '<label for="ch_'.$ide.'">'.tep_db_output($row["category_name"]).'</label>',
                               ));  
   $l++;
  }
  $i++;
 }
 tep_db_free_result($result);



 $template->assign_vars(array(
  'HEADING_TITLE'           => HEADING_TITLE,
  'feed_form'               => $feed_form,
  'submit_button'           => $feed_button,
  'TEXT_INFO_CAMPAIGN_NAME' => TEXT_INFO_CAMPAIGN_NAME, 
  'TEXT_INFO_CAMPAIGN_NAME1'=> tep_draw_input_field('TR_campaign_name',$campaign_name,'size="50"',true), 
  'TEXT_INFO_PUBLISHER_ID'  => TEXT_INFO_PUBLISHER_ID, 
  'TEXT_INFO_PUBLISHER_ID1' => tep_draw_input_field('TR_publisher_id',$publisher_id,'size="50"',true), 
		'TEXT_PUBLISHER_ID_HELP'  => TEXT_PUBLISHER_ID_HELP,
  'TEXT_INFO_RECRUITER_ID'  => TEXT_INFO_RECRUITER_ID, 
  'TEXT_INFO_RECRUITER_ID1' => tep_draw_input_field('TR_recruiter_id',$recruiter_id,'',true), 
		'TEXT_RECRUITER_ID_HELP'  => TEXT_RECRUITER_ID_HELP,
  'TEXT_INFO_LOCATION'      => TEXT_INFO_LOCATION, 
  'TEXT_INFO_LOCATION1'     => tep_draw_input_field('location',$location,'size="50"'), 
  'TEXT_INFO_COUNTRY'       => TEXT_INFO_COUNTRY, 
  'TEXT_INFO_COUNTRY1'      => tep_draw_pull_down_menu('feed_country', $feed_country_array, $feed_country,''), 
  'TEXT_INFO_STATUS'        => TEXT_INFO_STATUS, 
  'TEXT_INFO_STATUS1'       => tep_draw_radio_field("status", 'active',true,$status,'id="status_active"').'&nbsp; <label for="status_active" >Active </label>&nbsp;'.tep_draw_radio_field("status", 'inactive',false,$status,'id="status_inactive"').'&nbsp;<label for="status_inactive" >Inactive</label>', 
  'TEXT_INFO_SORT_BY'       => TEXT_INFO_SORT_BY, 
  'TEXT_INFO_SORT_BY1'      => tep_draw_pull_down_menu('sort_by', $feed_sort_array, $sort_by,''), 
  'TEXT_INFO_MAX_FEED'      => TEXT_INFO_MAX_FEED, 
  'TEXT_INFO_MAX_FEED1'     => tep_draw_input_field('IR_max_feed',$max_feed,"size='2'"), 
  'TEXT_INFO_JOB_TITLE'     => TEXT_INFO_JOB_TITLE, 
  'TEXT_INFO_JOB_TITLE1'    => tep_draw_input_field('job_title',$job_title,'size="50"'), 
  'TEXT_INFO_JOB_COMPANY'   => TEXT_INFO_JOB_COMPANY, 
  'TEXT_INFO_JOB_COMPANY1'  => tep_draw_input_field('job_company',$job_company,'size="50"'), 

  'TEXT_INFO_JOB_DURATION'  => TEXT_INFO_JOB_DURATION, 
  'TEXT_INFO_JOB_DURATION1' => tep_draw_input_field('IR_job_duration',$job_duration,"size='2'"), 

  'TEXT_INFO_KEYWORD'       => TEXT_INFO_KEYWORD, 
  'TEXT_INFO_KEYWORD1'      => TEXT_INFO_SORT_BY, 
  'update_message'=>$messageStack->output()));
 $template->pparse('simplyhired1');
}
else
{
 ///////////// Middle Values 
 $sort_array=array("feed_title","publisher_id","last_active","status");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'feed_id desc');
 $order_by_clause=$obj_sort_by_clause->return_value;

 if(tep_not_null($search_status1))
 {
  if($search_status1=='active')
  {
   $simplyhired_feeds_query_raw="select feed_id,feed_title,publisher_id,last_active,import_jobs,status from " . SIMPLYHIRED_FEED_TABLE ." where status='active' order by ".$order_by_clause;
  }
  elseif($search_status1=='inactive')
  {
   $simplyhired_feeds_query_raw="select feed_id,feed_title,publisher_id, last_active,import_jobs,status from " . SIMPLYHIRED_FEED_TABLE ." where status='inactive'  order by ".$order_by_clause;
  }
 }
 else
 $simplyhired_feeds_query_raw="select feed_id,feed_title,publisher_id,last_active,import_jobs,status from " . SIMPLYHIRED_FEED_TABLE ." order by ".$order_by_clause;
 $simplyhired_feeds_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $simplyhired_feeds_query_raw, $simplyhired_feeds_query_numrows);
 $simplyhired_feeds_query = tep_db_query($simplyhired_feeds_query_raw);
 if(tep_db_num_rows($simplyhired_feeds_query) > 0)
 {
  $alternate=1;
  while ($simplyhired_feeds = tep_db_fetch_array($simplyhired_feeds_query)) 
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $simplyhired_feeds['feed_id']))) && !isset($fInfo) && (substr($action, 0, 3) != 'new')) 
   {
    $fInfo = new objectInfo($simplyhired_feeds);
   }
   if ( (isset($fInfo) && is_object($fInfo)) && ($simplyhired_feeds['feed_id'] == $fInfo->feed_id) ) 
   {
    $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT . '?page='.$_GET['page'].'&id=' . $fInfo->feed_id . '&action=edit\'"';
   } 
   else 
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT . '?page='.$_GET['page'].'&id=' . $simplyhired_feeds['feed_id'] . '\'"';
   }
   $alternate++;
   if ( (isset($fInfo) && is_object($fInfo)) && ($simplyhired_feeds['feed_id'] == $fInfo->feed_id) ) 
   { 
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
   } 
   else 
   { 
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page='.$_GET['page'].'&id=' . $simplyhired_feeds['feed_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
   }

   if ($simplyhired_feeds['status'] == 'active') 
   {
    $status='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$simplyhired_feeds['feed_id'].'&action=feed_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_FEED_INACTIVATE, 30, 20) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_feed_active, 30, 20);
   } 
   else 
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif',STATUS_feed_inactive, 30, 20) . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$simplyhired_feeds['feed_id'].'&action=feed_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_FEED_ACTIVATE, 30, 20) . '</a>';
   }
   $template->assign_block_vars('simplyhired_feeds', array( 'row_selected' => $row_selected,
                                                     'action'      => $action_image,
                                                     'name'        => tep_db_output($simplyhired_feeds['feed_title']),
                                                     'publisher'   => tep_db_output($simplyhired_feeds['publisher_id']),
                                                     'last_active' =>($simplyhired_feeds['last_active']=='0000-00-00 00:00:00')?'':tep_db_output(formate_date1($simplyhired_feeds['last_active'])),
                                                     'import_jobs' => tep_db_output($simplyhired_feeds['import_jobs']),
                                                     'status'      => $status,
                                                     'fatch'      => '<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$simplyhired_feeds['feed_id'].'&action=fetch_now' . '" style=""><nobr>Fetch Now</nobr></a>',
                                                     'row_selected'=> $row_selected
                                                     ));
  }
 }
 //// for right side
 $ADMIN_RIGHT_HTML="";

 $heading = array();
 $contents = array();
 switch ($action) 
 {
  case 'delete':
   $heading[] = array('text' => '<b>' . $fInfo->feed_title . '</b>');
   $contents = array('form' => tep_draw_form('search_FEED_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] . '&id=' . $nInfo->feed_id . '&action=deleteconfirm'));
   $contents[] = array('text' => TEXT_DELETE_INTRO);
   $contents[] = array('text' => '<br><b>' . $fInfo->feed_title . '</b>');
   $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
  default:
   if (isset($fInfo) && is_object($fInfo)) 
   {
    $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_CAMPAIGN.'</b>');
    $contents[] = array('text' => tep_db_output($fInfo->feed_title));
    $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] .'&id=' . $fInfo->feed_id .'&action=edit') . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] .'&id=' . $fInfo->feed_id. '&action=delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
    $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
   }
   break;
 }
 if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
 {
  $box = new right_box;
  $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
  $RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
 }
 else
 {
  $RIGHT_BOX_WIDTH='0';
 }
 /////
 $search_status_array=array();
 $search_status_array[]=array('id'=>'','text'=>'All');
 $search_status_array[]=array('id'=>'active','text'=>'active');
 $search_status_array[]=array('id'=>'inactive','text'=>'inactive');

 $template->assign_vars(array(
  'TABLE_HEADING_SIMPLYHIRED_FEED_NAME'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_SIMPLYHIRED_FEED_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_SIMPLYHIRED_FEED_PUBLISHER_ID'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_SIMPLYHIRED_FEED_PUBLISHER_ID.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_SIMPLYHIRED_FEED_LAST_ACTIVE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_SIMPLYHIRED_FEED_LAST_ACTIVE.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_SIMPLYHIRED_FEED_IMPORT_JOBS'=>TABLE_HEADING_SIMPLYHIRED_FEED_IMPORT_JOBS,
  'TABLE_HEADING_SIMPLYHIRED_FEED_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_SIMPLYHIRED_FEED_STATUS.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_SIMPLYHIRED_FEED_FATCH' => TABLE_HEADING_SIMPLYHIRED_FEED_FATCH,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'INFO_TEXT_STATUS'=>INFO_TEXT_STATUS,
  'INFO_TEXT_STATUS1'=>tep_draw_pull_down_menu('search_status', $search_status_array, $search_status1,'onchange="document.disply.submit();"'),
  'count_rows'=>$simplyhired_feeds_split->display_count($simplyhired_feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SIMPLYHIRED_IMPORTER),
  'no_of_pages'=>$simplyhired_feeds_split->display_links($simplyhired_feeds_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','id','action'))),
  'new_button'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SIMPLYHIRED_FEED_IMPORT, 'page=' . $_GET['page'] .'&action=new') . '">'.tep_image_button(PATH_TO_BUTTON.'button_new.gif',IMAGE_NEW).'</a>&nbsp;',
  'HEADING_TITLE'=>HEADING_TITLE,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('simplyhired');
}
?>