<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ZIP_CODES);
$template->set_filenames(array('zip_code' => 'admin1_zip_codes.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$search_zip_code=tep_db_prepare_input($_GET['search_zip_code']);
$search_state   =tep_db_prepare_input($_GET['search_state']);

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from ".ZIP_CODE_TABLE." where zip_code = '" . $id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page']));
  case 'insert':
  case 'save':
   $zip_code  = tep_db_prepare_input($_POST['TR_zip_code']);
   $city      = tep_db_prepare_input($_POST['city']);
   $state     = tep_db_prepare_input($_POST['TR_state']);
   $latitude  = tep_db_prepare_input($_POST['TR_latitude']);
   $longitude = tep_db_prepare_input($_POST['TR_longitude']);
   $sql_data_array=array('zip_code'  => $zip_code,
                         'city'      => $city,
                         'state'     => $state,
                         'latitude'  => $latitude,
                         'longitude' => $longitude,
                        );

   $error=false;
   if(!tep_not_null($zip_code))
   {
    $error=true;
				$messageStack->add(MESSAGE_ZIP_CODE_ERROR, 'error');
   }
   if(!tep_not_null($state))
   {
    $error=true;
				$messageStack->add(MESSAGE_STATE_ERROR, 'error');
   }
   if(!tep_not_null($latitude))
   {
    $error=true;
				$messageStack->add(MESSAGE_LATITUDE_ERROR, 'error');
   }
   if(!tep_not_null($longitude))
   {
    $error=true;
				$messageStack->add(MESSAGE_LONGITUDE_ERROR, 'error');
   }
   
			if($action=='insert' && !$error)
			{
				if($row_chek=getAnyTableWhereData(ZIP_CODE_TABLE,"zip_code='".tep_db_input($zip_code)."'",'zip_code'))
				{
     $error=true;
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
    if(!$error)
				{
     tep_db_perform(ZIP_CODE_TABLE, $sql_data_array);
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ZIP_CODES);
				}
			}
			elseif(!$error)
			{
    $id =tep_db_prepare_input($_GET['id']);
				if($row_chek=getAnyTableWhereData(ZIP_CODE_TABLE,"zip_code='".tep_db_input($zip_code)."' and zip_code!='$id'",'zip_code'))
				{
     $error=true;
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
    if(!$error)
				{
     tep_db_perform(ZIP_CODE_TABLE, $sql_data_array, 'update', "zip_code = '" .$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ZIP_CODES.'?page='.$_GET['page'].'&id='.$zip_code);
				}    
			}
  break;
 }
}
///////////// Middle Values 
$sort_array=array("zip_code","city","zone_name");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'zc.zip_code asc');
$order_by_clause=$obj_sort_by_clause->return_value;

$whereClause='';
if(tep_not_null($search_zip_code))
{
 $whereClause= "zc.zip_code='".tep_db_input($search_zip_code)."'";
}
if(tep_not_null($search_state))
{
 $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
 $whereClause.= "zc.state='".tep_db_input($search_state)."'";
}
$whereClause=(!tep_not_null($whereClause)?'1':$whereClause);
$zip_code_query_raw="select zc.zip_code,zc.city,z.zone_name from ".ZIP_CODE_TABLE." as zc left outer join ".ZONES_TABLE."  as z on (zc.state=z.zone_id)  where $whereClause order by ".$order_by_clause;
$zip_code_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $zip_code_query_raw, $zip_code_query_numrows);
$zip_code_query = tep_db_query($zip_code_query_raw);
if(tep_db_num_rows($zip_code_query) > 0)
{
 $alternate=1;
 while ($zip_codes = tep_db_fetch_array($zip_code_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $zip_codes['zip_code']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($zip_codes);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($zip_codes['zop_code'] == $cInfo->zip_code) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ZIP_CODES . '?page='.$_GET['page'].'&id=' . $cInfo->zip_code. '&action=edit&'.tep_get_all_get_params(array('action','id','page')).'\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ZIP_CODES . '?page='.$_GET['page'].'&id=' . $zip_codes['zip_code'] . '&'.tep_get_all_get_params(array('action','id','page')).'\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($zip_codes['zip_code'] == $cInfo->zip_code) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page='.$_GET['page'].'&id=' . $zip_codes['zip_code'].'&'.tep_get_all_get_params(array('action','id','page'))).'">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('zip_code', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($zip_codes['zip_code']),
   'city' => tep_db_output($zip_codes['city']),
   'state' => tep_db_output($zip_codes['zone_name']),
   'row_selected' => $row_selected
   ));
 }
}
//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{
 case 'new':
 case 'insert':
    $heading[] = array('text' => '<div class="list-group">
    <div class="font-weight-bold text-primary">
    '.TEXT_INFO_HEADING_ZIP_CODE.'</div>
    </div>');
    $contents = array('form' => tep_draw_form('zip_code', PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
		
    // $contents[] = array('text' => TEXT_INFO_NEW_INTRO);
		// $contents[] = array('text' => '<br>'.TEXT_INFO_ZIP_CODE.'<br>'.tep_draw_input_field('TR_zip_code', $zip_code,'', true ));
		// $contents[] = array('text' => INFO_TEXT_ZIP_CODE_CITY.'<br>'.tep_draw_input_field('city', $city, '' ));
		// $contents[] = array('text' => TEXT_INFO_ZIP_CODE_STATE.'<br>'.);
		// $contents[] = array('text' => TEXT_INFO_ZIP_CODE_LATITUDE.'<br>'.tep_draw_input_field('TR_latitude', $latitude, '',true ));
		// $contents[] = array('text' => TEXT_INFO_ZIP_CODE_LONGITUDE.'<br>'.tep_draw_input_field('TR_longitude', $longitude,'',true ));
    
    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE.'</label>
    '.tep_draw_input_field('TR_zip_code', $zip_code,'class="form-control form-control-sm"', true ).'
    </div>
    <div class="form-group">
    <label>'.INFO_TEXT_ZIP_CODE_CITY.'</label>
    '.tep_draw_input_field('city', $city, 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_STATE.'</label>
    '.LIST_SET_DATA(ZONES_TABLE," where zone_country_id in (222,223)",'zone_name','zone_id',"zone_country_id,zone_name",'name="TR_state" class="form-control form-control-sm"',"",'',$state).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_LATITUDE.'</label>
    '.tep_draw_input_field('TR_latitude', $latitude, 'class="form-control form-control-sm"',true ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_LONGITUDE.'</label>
    '.tep_draw_input_field('TR_longitude', $longitude,'class="form-control form-control-sm"',true ).'
    </div>
    '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES) . '">' 
    . IMAGE_CANCEL . '</a>
    </div>');
  break;
 case 'edit':
 case 'save':
  $value_field=tep_draw_input_field('TR_zip_code', $cInfo->zip_code, '' );
  if(!$error)
  {
		 $row_data=getAnyTableWhereData(ZIP_CODE_TABLE,"zip_code='".tep_db_input($cInfo->zip_code)."'",'state,latitude,longitude');
   $state     = $row_data['state'];
   $latitude  = $row_data['latitude'];
   $longitude = $row_data['longitude'];
  }
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_ZIP_CODE.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('zip_code', PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'id=' . $cInfo->zip_code.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('align' => 'left', 'text' => '
  <div class="py-2">
    <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE.'</label>
    '.tep_draw_input_field('TR_zip_code',isset($_POST['zip_code'])?$zip_code:$cInfo->zip_code, 'class="form-control form-control-sm"' ,true).'
    </div>
    <div class="form-group">
    <label>'.INFO_TEXT_ZIP_CODE_CITY.'</label>
    '.tep_draw_input_field('city',isset($_POST['city'])?$city: $cInfo->city, 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_STATE.'</label>
    '.LIST_SET_DATA(ZONES_TABLE," where zone_country_id in (222,223)",'zone_name','zone_id',"zone_country_id,zone_name",'name="TR_state" class="form-control form-control-sm"',"",'',$state).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_LATITUDE.'</label>
    '.tep_draw_input_field('TR_latitude', $latitude, 'class="form-control form-control-sm"',true ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_ZIP_CODE_LONGITUDE.'</label>
    '.tep_draw_input_field('TR_longitude', $longitude,'class="form-control form-control-sm"',true ).'
    </div>'.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES,'id=' . $cInfo->zip_code). '&'.tep_get_all_get_params(array('action','id')) . '">' 
    . IMAGE_CANCEL . '
    </a>
  </div>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold ">
  '.$cInfo->zip_code.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('zip_code_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] . '&id=' . $nInfo->zip_code. '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
  <p>'.$zInfo->zone_name.'</p></div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete&'.tep_get_all_get_params(array('action','id','page'))) . '">' 
  . IMAGE_CONFIRM . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_ZIP_CODE.'</div></div>');
  //  $contents[] = array('align' => 'left', 'text' => '<br><a href="' .  . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' .  . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');

   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->zip_code).'
   <strong class="d-block">
   '.TEXT_INFO_ACTION.'
   </strong></div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] .'&id=' . $cInfo->zip_code. '&action=edit&'.tep_get_all_get_params(array('action','id','page'))) . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] .'&id=' . $cInfo->zip_code. '&action=delete&'.tep_get_all_get_params(array('action','id','page'))) . '">'
   .IMAGE_DELETE.'</a>
   </div>');
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
 'TABLE_HEADING_ZIP_CODE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_ZIP_CODE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_ZIP_CODE_CITY'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_ZIP_CODE_CITY.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_ZIP_CODE_STATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_ZIP_CODE_STATE.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'INFO_TEXT_SEARCH_ZIP_CODE' => INFO_TEXT_SEARCH_ZIP_CODE,
 'INFO_TEXT_SEARCH_ZIP_CODE1'=> tep_draw_input_field('search_zip_code', $search_zip_code, 'class="form-control form-control-sm" placeholder="'.INFO_TEXT_SEARCH_ZIP_CODE.'"' ),
 'INFO_TEXT_SEARCH_STATE'    => LIST_SET_DATA(ZONES_TABLE," where zone_country_id in (222,223)",'zone_name','zone_id',"zone_country_id,zone_name",'name="search_state" class="form-control form-control-sm"',"State",'',$search_state),

 'count_rows'=>$zip_code_split->display_count($zip_code_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ZIP_CODE),
 'no_of_pages'=>$zip_code_split->display_links($zip_code_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','id','action'))),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ZIP_CODES, 'page=' . $_GET['page'] .'&action=new') . '"><i class="fa fa-plus fa-admin-icons" aria-hidden="true"></i> '.IMAGE_NEW.'</a>&nbsp;&nbsp;',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('zip_code');
?>