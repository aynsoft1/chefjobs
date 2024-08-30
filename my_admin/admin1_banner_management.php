<?
/*
***********************************************************
***********************************************************
**********# Name          : Hema Chawla         #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_BANNER_MANAGEMENT);
$template->set_filenames(array('bannermgt' => 'admin1_banner_management.htm'));
include_once(FILENAME_ADMIN_BODY);
ini_set('max_execution_time','0');
//print_r($_POST);
//	print_r($_GET);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$banner_id = (isset($_GET['bID']) ? tep_db_prepare_input($_GET['bID']) : '');
$_GET['b_status'] = (in_array($_GET['b_status'],array('active','expired','deleted','other')) ? $_GET['b_status'] : 'active');
if(tep_not_null($banner_id))
{
 if(!$row=getAnyTableWhereData(BANNER_TABLE,"id='".(int)$banner_id."'"))
 {
  tep_redirect(FILENAME_ADMIN1_BANNER_MANAGEMENT);
 }
 else
 {
  $title=stripslashes($row['title']);
  $banner_type=stripslashes($row['banner_type']);
  $href=stripslashes($row['href']);
  $src=stripslashes($row['src']);
  $alt=stripslashes($row['alt']);
  $script=stripslashes($row['script']);
  $adviews=$row['adviews'];
  $adclicks=$row['adclicks'];
  $company_name=stripslashes($row['company_name']);
  $company_contact=stripslashes($row['company_contact']);
  $company_email=stripslashes($row['company_email']);
  $banner_costing=stripslashes($row['banner_costing']);
  $company_comments=stripslashes($row['company_comments']);
	 if(tep_not_null($src))
  {
   if(is_file(PATH_TO_MAIN_PHYSICAL_BANNER.$src))
   {
    $src1="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_BANNER.$row['src']."','".tep_db_output($row['title'])."')\">Preview</a>&nbsp;&nbsp;]";
   }
   else
    $src1='';
  }

 }
}
if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_purge':
			$bnn_query=tep_db_query("select * from ". BANNER_TABLE . " where deleted='T' && id='". tep_db_input($banner_id)."'");
			$bnn=tep_db_fetch_array($bnn_query);
			tep_db_query("delete from ".BANNER_TABLE." where deleted='T' && id='". tep_db_input($banner_id)."'");
			// to delete image from banner folder BEGIN
			if(is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_BANNER.$bnn['src']))
				@unlink(PATH_TO_MAIN_PHYSICAL.PATH_TO_BANNER.$bnn['src']);
			// to delete image from banner folder END
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT));
   break;
  case 'restore':
			tep_db_query("update ".BANNER_TABLE." set deleted='F' where id='". tep_db_input($banner_id)."'");
			$messageStack->add_session(MESSAGE_SUCCESS_RESTORED, 'success');
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT));
			break;
  case 'confirm_delete':
   $banner_query = tep_db_query("select deleted from " . BANNER_TABLE . " where id='" . tep_db_input($banner_id) . "'");
   $banner=tep_db_fetch_array($banner_query);
   $sql_data_array['deleted']='T';
   if(!$banner_chek=getAnyTableWhereData(BANNER_TABLE," id='".$banner_id."'",'deleted'))
   {
    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
   }
   else
   {
    tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner_id."'");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT));
   }
   break;
  case 'save_banner':
   $banner_type=tep_db_prepare_input($_POST['TR_banner_type']);
   if($banner_type=='image')
		 {
			 $href=tep_db_prepare_input($_POST['banner_href']);
    $alt=tep_db_prepare_input($_POST['banner_alt']);
			}
			elseif($banner_type=='script')
		 { 
				$script=stripslashes(trim($_POST['banner_script']));
		 }
			$location          = tep_db_prepare_input($_POST['TR_banner_location']);
   $status            = tep_db_prepare_input($_POST['TR_banner_status']);
   $company_name      = tep_db_prepare_input($_POST['company_name']);
   $company_contact   = tep_db_prepare_input($_POST['company_contact']);
   $company_email     = tep_db_prepare_input($_POST['TNEF_company_email']);
   $banner_costing    = tep_db_prepare_input($_POST['banner_costing']);
   $company_comments  = tep_db_prepare_input($_POST['company_comments']);
   $duration_unlimited= tep_db_prepare_input($_POST['TR_duration_unlimited']);
   $f_date            = tep_db_prepare_input($_POST['from_date']);
   $f_month           = tep_db_prepare_input($_POST['from_month']);
   $f_year            = tep_db_prepare_input($_POST['from_year']);
   $t_date            = tep_db_prepare_input($_POST['to_date']);
   $t_month           = tep_db_prepare_input($_POST['to_month']);
   $t_year            = tep_db_prepare_input($_POST['to_year']);

   $duration_from = $f_year."-".$f_month."-".$f_date;
   $duration_to   = $t_year."-".$t_month."-".$t_date;

   if($duration_unlimited=='F')
   {
    if(!@checkdate($f_month,$f_date,$f_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_FROM_DATE_ERROR,'error');
    }
    if(!@checkdate($t_month,$t_date,$t_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_TO_DATE_ERROR,'error');
    }
    if($duration_from > $duration_to)
    {
     $error = true;
     $messageStack->add(MESSAGE_DATE_ERROR,'error');
    }
			}
		 if(strlen($banner_type) <= 0)
	 	{
     $error = true;
     $messageStack->add(BANNER_TYPE_ERROR,'error');
	 	}
	 	else
		 {
	  	if($banner_type=='image')
		  {
     if(!tep_not_null($href))
     {
      $error = true;
      $messageStack->add(HREF_ERROR,'error');
     }
     if(!tep_not_null($alt))
     {
      $error = true;
      $messageStack->add(ALT_ERROR,'error');
     }
					if(!$row['src'])
					{
      if(!tep_not_null($_FILES['banner']['name']))
      {
       $error = true;
       $messageStack->add(UPLOAD_ERROR,'error');
      }
					}
  			$sql_data_array['href'] = stripslashes($href);
     $sql_data_array['alt']  = stripslashes($alt);
			 }
			 elseif($banner_type=='script')
		  {
     if(!tep_not_null($script))
     {
      $error = true;
      $messageStack->add(SCRIPT_ERROR,'error');
     }
  	  $sql_data_array['script']= stripslashes($script);
			 }
		 }			
			$sql_data_array['banner_type']       = $banner_type;
   $sql_data_array['location']          = $location;
   $sql_data_array['company_name']      = $company_name;
   $sql_data_array['company_contact']   = $company_contact;
   $sql_data_array['company_email']     = $company_email;
   $sql_data_array['banner_costing']    = $banner_costing;
   $sql_data_array['company_comments']  = $company_comments; 
   $sql_data_array['banner_duration_from']=$duration_from;
   $sql_data_array['banner_duration_to']= $duration_to;
   $sql_data_array['duration_unlimited']= $duration_unlimited;

			if(!$error)
   {
    if($action=='save_banner')
    {
     if(!$row_chek=getAnyTableWhereData(BANNER_TABLE," id='".$banner_id."'",'id'))
     {
      $messageStack->add(MESSAGE_NAME_ERROR, 'error');
     }
					elseif($_POST['TR_banner_type']=='image')
     {
							$logo_check=getAnyTableWhereData(BANNER_TABLE,"id='".(int)$banner_id."'",'src');
							$image='';
							if(tep_not_null($_FILES['banner']['name']))
							{
								if($obj_logo = new upload('banner',PATH_TO_MAIN_PHYSICAL_BANNER,'644',array('gif','jpg','png','jpeg')))
								{
									$image=tep_db_input($obj_logo->filename);
									if(tep_not_null($src))
									{
										if(tep_not_null($logo_check['src']))
										{
											if(is_file(PATH_TO_MAIN_PHYSICAL_BANNER.$logo_check['src']))
											{
												@unlink(PATH_TO_MAIN_PHYSICAL_BANNER.$logo_check['src']);
											}
										}
									}
									else
									{
										$error = true;
										$messageStack->add(FILE_UPLOAD_ERROR,'user_account');
									}
								}
								else
								{
									$error=true;
								}
							}      
	      if($image!='')
	      {
	       $sql_data_array['src']=$image;
							}
       tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner_id."'");
       $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
	      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT)); 
     }
			  elseif($banner_type=='script')
			  {
    	 tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner_id."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
	     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT)); 
			  }
    }
   }
   else
   {
    $action='edit';
   }
   break;
 }
}
///////////// Middle Values 
//---BEGIN --- display of Active / Expired / Deleted Banners-----/
$today=date("Y-m-d",mktime(date("m"), date("d"), date("Y")));
switch($_GET['b_status'])
{
 case 'active':
  $whereClause=" (duration_unlimited='T' and deleted='F')|| (duration_unlimited='F' && deleted='F' && banner_duration_from <= '".$today."' and banner_duration_to >= '".$today."')";
  break;
 case 'expired':
  $whereClause=" duration_unlimited='F' && banner_duration_from < '".$today."' && banner_duration_to < '".$today."' and deleted='F'";
  break;
 case 'deleted':
  $whereClause=" deleted = 'T'";
  break;
 case 'other':
  $whereClause=" duration_unlimited='F' && banner_duration_from > '".$today."' and deleted='F'";
  break;
}
$different_banners='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=active">Active Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=expired">Expired Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=deleted">Deleted Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=other">Other Banners</a>';

$sort_array=array("bn_loc.banner_location_name","bn.title","bn.banner_date");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'bn_loc.banner_location_name asc,bn.title asc,bn.banner_date desc');
$order_by_clause=$obj_sort_by_clause->return_value;
///only for sorting ends
//---END --- display of Active / Expired / Deleted Banners-----/
$banner_query_raw="select * from " . BANNER_TABLE ." as bn LEFT JOIN ".BANNER_LOCATION_TABLE ." as bn_loc on (bn.location=bn_loc.banner_location_id) where ".$whereClause." order by ".$order_by_clause;
$banner_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banner_query_raw, $banner_query_numrows);
$banner_query = tep_db_query($banner_query_raw);
if(tep_db_num_rows($banner_query) > 0)
{
 $alternate=1;
 $pre_banner_location_name="";
 $banner_location_name="";
 while ($banner = tep_db_fetch_array($banner_query)) 
 {
  if ((!isset($_GET['bID']) || (isset($_GET['bID']) && ($_GET['bID'] == $banner['id']))) && !isset($bInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $bInfo = new objectInfo($banner);
  }
  $alternate++;
  if ( (isset($bInfo) && is_object($bInfo)) && ($banner['id'] == $bInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page='.$_GET['page'].'&bID=' . $banner['id'].'&b_status='.$_GET['b_status']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  if ( (isset($bInfo) && is_object($bInfo)) && ($banner['id'] == $bInfo->id) ) 
  {
			if($_GET['b_status']!='deleted')
			{
				$row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';// onclick="document.location.href=\'' . FILENAME_ADMIN1_BANNER_MANAGEMENT . '?page='.$_GET['page'].'&bID=' . $bInfo->id . '&b_status='.$_GET['b_status'].'&action=edit\'"
			}
			else
			{
				$row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';//onclick="document.location.href=\'' . FILENAME_ADMIN1_BANNER_MANAGEMENT . '?page='.$_GET['page'].'&bID=' . $bInfo->id . '&b_status='.$_GET['b_status'].'\'"
			}
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';// onclick="document.location.href=\'' . FILENAME_ADMIN1_BANNER_MANAGEMENT . '?page='.$_GET['page'].'&b_status='.$_GET['b_status'].'&bID=' . $banner['id'] . '\'"
  }
		$banner_location_name=$banner['banner_location_name'];
		$location="";	
  if($pre_banner_location_name !=$banner_location_name  || $pre_banner_location_name=='')
  $location='<tr><td valign="top" class="dataTableContentbold font-weight-bold text-uppercase" colspan="6">'.tep_db_output($banner['banner_location_name']).'</td></tr>';
  $view='';
  if($banner['banner_type']=='image')
   $view=  "<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_BANNER.$banner['src']."','".tep_db_output($banner['title'])."')\"> <img src='".tep_href_link("img/find.gif")."' alt='view'></a>"; 
  $template->assign_block_vars('banner', array( 'row_selected' => $row_selected,
   'title' => tep_db_output($banner['title']),
   'name' => tep_db_output($banner['src']),
   'company'=>tep_db_output($banner['company_name']),
   'duration'=>(($banner['duration_unlimited']=='T')?tep_db_output('Unlimited'):tep_db_output('From:'.$banner['banner_duration_from'].' to '.$banner['banner_duration_to'])),
   'location'=>$location,
   'bdate'=>tep_db_output(tep_date_short($banner['banner_date'])),
   'view'=>$view,
   'action' => $action_image,
   ));
		$pre_banner_location_name=$banner_location_name;
 }
 tep_db_free_result($banner_query);

}
//// for right side
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action) 
{ 
 case 'save_banner':
 case 'edit':
  if (isset($bInfo) && is_object($bInfo)) 
  {
   $bn_query=tep_db_query("select * from ".BANNER_TABLE." where id='".$_GET['bID']."'");
   $bn=tep_db_fetch_array($bn_query);
   if(is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_BANNER.$bn['src']))
   $imgsize=getimagesize(PATH_TO_MAIN_PHYSICAL_BANNER.$bn['src']);
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . tep_db_output($bInfo->title) . '</div>');
   $contents = array('form' => tep_draw_form('banner', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'bID=' . $bInfo->id.'&page='.$_GET['page'].'&b_status='.$_GET['b_status'].'&action=save_banner','post',' enctype="multipart/form-data" onsubmit="return ValidateForm(this)"'));
 		$contents[]=array('text' => '<div class="mb-1 text-danger">' .TEXT_INFO_EDIT_INTRO. '</div>');
   if ($error) 
   {
    //$contents[] = array('text' => tep_draw_hidden_field('TR_banner_src', $bInfo->src));
    $contents[] = array('text' => '' . INFO_TEXT_BANNER_SIZE .  $imgsize[0].' x '.$imgsize[1]); 
	  $contents[] = array('text' => '' .INFO_TEXT_BANNER_TYPE.''. tep_draw_radio_field("TR_banner_type", 'image','',$banner_type,'onclick="set_type()"').'&nbsp;Image&nbsp;'.tep_draw_radio_field("TR_banner_type",'script', '',$banner_type,'onclick="set_type()"').'&nbsp;Script' );
		$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_HREF.'<br>'.tep_draw_input_field('banner_href', $href, 'class="form-control form-control-sm"' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_ALT.'<br>'.tep_draw_input_field('banner_alt', $alt, 'class="form-control form-control-sm"' ));
	  $contents[]=array('text' => '<br><div id=image>'.INFO_TEXT_BANNER.'<br>'.tep_draw_file_field("banner").$src1.'</div>');
	  $contents[]=array('text' => '<br><div id=script>'.INFO_TEXT_BANNER_SCRIPT.'<br>'.tep_draw_textarea_field('banner_script','','30','4',$bInfo->script,'',"",false).'</div>');
				
		$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_LOCATION.'<br>'.LIST_SET_DATA(BANNER_LOCATION_TABLE,"",'banner_location_name','banner_location_id',"banner_location_name",'name="TR_banner_location"',"",'',$location));
    //period of banner
    $contents[] = array('text' => '<br>&nbsp;' .INFO_TEXT_BANNER_DURATION.'<br>&nbsp;'. tep_draw_radio_field("TR_duration_unlimited", 'T', ($duration_unlimited=='T'?true:false) ,'', 'onclick="set_fixed_duration()"').'&nbsp;Unlimited&nbsp;'.tep_draw_radio_field("TR_duration_unlimited", 'F', ($duration_unlimited=='F'?true:false),'','onclick="set_fixed_duration()"').'&nbsp;Fixed' );
    $contents[] = array('text' => 'From :<br>' . datelisting(@date("Y-m-d",mktime(0,0,0, $f_date, $f_month,$f_year)), "name='from_date' disabled", "name='from_month' disabled", "name='from_year' disabled", "2006", date("Y")+4));
    $contents[] = array('text' => 'To :<br>' . datelisting(@date("Y-m-d",mktime(0,0,0,$t_date,$t_month,$t_year)), "name='to_date' disabled", "name='to_month' disabled", "name='to_year' disabled", "2006", date("Y")+4));
    $contents[]=array('text' => '<br><hr><br><font size=2><b>Company Information</b></font><br>'.INFO_TEXT_COMPANY_NAME.'<br>'.tep_draw_input_field('company_name', $company_name, '' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_COMPANY_CONTACT.'<br>'.tep_draw_input_field('company_contact', $company_contact, 'class="form-control form-control-sm"' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_COMPANY_EMAIL.'<br>'.tep_draw_input_field('TNEF_company_email', $company_email, 'class="form-control form-control-sm"' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_COSTING.'<br>'.tep_draw_input_field('banner_costing', $banner_costing, 'class="form-control form-control-sm"' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_COMPANY_COMMENTS.'<br>'.tep_draw_textarea_field('company_comments',1,30,2, $company_comments, 'class="form-control form-control-sm"' ));
    $contents[]=array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>');
   }
   else 
   {
    //$contents[] = array('text' => tep_draw_hidden_field('TR_banner_src', $bInfo->src));
    $contents[] = array('text' => '<br>&nbsp;' . INFO_TEXT_BANNER_SIZE .  $imgsize[0].' x '.$imgsize[1]); 
	  $contents[] = array('text' => '<br>&nbsp;' .INFO_TEXT_BANNER_TYPE.'<br>&nbsp;'. tep_draw_radio_field("TR_banner_type", 'image','',$banner_type,'onclick="set_type()"').'&nbsp;Image&nbsp;'.tep_draw_radio_field("TR_banner_type",'script', '',$banner_type,'onclick="set_type()"').'&nbsp;Script' );
		$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_HREF.'<br>'.tep_draw_input_field('banner_href', $bInfo->href, 'class="form-control form-control-sm"' ));
    $contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_ALT.'<br>'.tep_draw_input_field('banner_alt', $bInfo->alt, 'class="form-control form-control-sm"' ));
	  $contents[]=array('text' => '<br><div id=image>'.INFO_TEXT_BANNER.'<br>'.tep_draw_file_field("banner").$src1.'</div>');
	  $contents[]=array('text' => '<br><div id=script>'.INFO_TEXT_BANNER_SCRIPT.'<br>'.tep_draw_textarea_field('banner_script','','30','4',$bInfo->script,'class="form-control form-control-sm"',"",false).'</div>');
				
		$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_LOCATION.'<br>'.LIST_SET_DATA(BANNER_LOCATION_TABLE,"",'banner_location_name','banner_location_id',"banner_location_name",'name="TR_banner_location"',"",'',$bInfo->location));
    //period of banner
    $contents[] = array('text' => '<br>&nbsp;' .INFO_TEXT_BANNER_DURATION.'<br>&nbsp;'. tep_draw_radio_field("TR_duration_unlimited", 'T', ($bInfo->duration_unlimited=='T'?true:false) ,'', 'onclick="set_fixed_duration()"').'&nbsp;Unlimited&nbsp;'.tep_draw_radio_field("TR_duration_unlimited", 'F', ($bInfo->duration_unlimited=='F'?true:false),'','onclick="set_fixed_duration()"').'&nbsp;Fixed' );
    $contents[] = array('text' => 'From :<br>' . datelisting(@date("Y-m-d",mktime(0,0,0, substr($bInfo->banner_duration_from,5,2), substr($bInfo->banner_duration_from,8,2), substr($bInfo->banner_duration_from,0,4))), "name='from_date' disabled", "name='from_month' disabled", "name='from_year' disabled", "2006", date("Y")+4));
    $contents[] = array('text' => 'To :<br>' . datelisting(@date("Y-m-d",mktime(0,0,0, substr($bInfo->banner_duration_to,5,2), substr($bInfo->banner_duration_to,8,2), substr($bInfo->banner_duration_to,0,4))), "name='to_date' disabled", "name='to_month' disabled", "name='to_year' disabled", "2006", date("Y")+4));
    $contents[]=array('text' => '<br><hr><br><font size=2><b>Company Information</b></font><br><br>'.tep_draw_input_field('company_name', $bInfo->company_name, 'class="form-control form-control-sm" placeholder="Company name"' ));
    $contents[]=array('text' => '<br>'.tep_draw_input_field('company_contact', $bInfo->company_contact, 'class="form-control form-control-sm" placeholder="Contact Number"' ));
    $contents[]=array('text' => '<br>'.tep_draw_input_field('TNEF_company_email', $bInfo->company_email, 'class="form-control form-control-sm" placeholder="Email"' ));
    $contents[]=array('text' => '<br>'.tep_draw_input_field('banner_costing', $bInfo->banner_costing, 'class="form-control form-control-sm" placeholder="Costing" ' ));
    $contents[]=array('text' => '<br>'.tep_draw_textarea_field('company_comments',1,30,2, $bInfo->company_comments, 'class="form-control form-control-sm" placeholder="Comments"' ));
    $contents[]=array('align' => 'left', 'text' => '<br>
    
    '.tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT).'">'.IMAGE_CANCEL.'</a>');
   }
		}
  break;
 case 'delete':
  $heading[]=array('text' => '<div class="text-primary font-weight-bold mb-1">' . $bInfo->title . '</div>');
  $contents=array('form' => tep_draw_form('banner_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] . '&bID=' . $bInfo->id . '&action=deleteconfirm'));
  $contents[]=array('text' =>'<div class="mb-1 text-danger">' .TEXT_DELETE_INTRO. '</div>');
  $contents[]=array('text' => '<br>' . $bInfo->title . '</b>');
  $contents[]=array('align' => 'left', 'text' => '
  <table>
   <tr>
   <td>       
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] . '&bID=' . $bInfo->id.'&action=confirm_delete') . '">'.IMAGE_CONFIRM.'</a>
   </td>
   <td>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] ) . '">' .IMAGE_CANCEL. '</a>
   </td>
   </tr>
   </table>
  ');
 break;
  case 'purge':
  $heading[]=array('text' => '<b>' . $bInfo->title . '</b>');
  $contents=array('form' => tep_draw_form('banner_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] . '&bID=' . $bInfo->id . '&action=purgeconfirm'));
  $contents[]=array('text' => TEXT_PURGE_INTRO);
  $contents[]=array('text' => '<br><b>' . $bInfo->title . '</b>');
  $contents[]=array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] . '&bID=' . $bInfo->id.'&action=confirm_purge') . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page=' . $_GET['page'] ) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
 break;
 default:
	//purge and restore button if status is deleted
	if ($_GET['b_status']!='deleted')
	{
		if (isset($bInfo) && is_object($bInfo)) 
		{
			$heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . tep_db_output($bInfo->title) . '</div>');
			$contents[] = array('align' => 'left', 'text' =>'<div class="mb-1 text-danger">' .TEXT_INFO_EDIT_INTRO. '</div>');
			$contents[] = array('align' => 'left', 'text' => '<br>'.'
      <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'bID=' . $bInfo->id.'&action=edit&b_status='.$_GET['b_status'].'&page='.$_GET['page']) . '">'.IMAGE_EDIT.'</a>
      '.'<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'bID=' . $bInfo->id.'&action=delete&b_status='.$_GET['b_status'].'&page='.$_GET['page']) . '">'.IMAGE_DELETE.'</a>');
		} 
	}
	else // b_status=deleted
	{
		if (isset($bInfo) && is_object($bInfo)) 
		{
			$heading[] = array('text' => '<b>&nbsp;' . tep_db_output($bInfo->title) . '</b>');
			$contents[] = array('align' => 'left', 'text' => TEXT_INFO_EDIT_INTRO);			
			$contents[] = array('align' => 'left', 'text' => '<br>'.'
      <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'bID=' . $bInfo->id.'&action=restore&b_status='.$_GET['b_status'].'&page='.$_GET['page']) . '">'.tep_image(PATH_TO_BUTTON.'button_restore.gif',IMAGE_RESTORE).'</a>
      '.'<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'bID=' . $bInfo->id.'&action=purge&b_status='.$_GET['b_status'].'&page='.$_GET['page']) . '">'.tep_image(PATH_TO_BUTTON.'button_delete.gif',IMAGE_PURGE).'</a>&nbsp;');
			}
	}
}

////

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
if($messageStack->size('error') > 0)
 $update_message=$messageStack->output('error');
else
 $update_message=$messageStack->output();

$template->assign_vars(array(
 'TABLE_HEADING_BANNER_NAME'=>TABLE_HEADING_BANNER_NAME,
 'TABLE_HEADING_BANNER_COMPANYNAME'=>TABLE_HEADING_BANNER_COMPANYNAME,
 'TABLE_HEADING_BANNER_DURATION'=>TABLE_HEADING_BANNER_DURATION,
 'TABLE_HEADING_BANNER_DATE'=>TABLE_HEADING_BANNER_DATE,
 'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$banner_split->display_count($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS),
 'no_of_pages'=>$banner_split->display_links($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
 'form'=>tep_draw_form('add_edit', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT,'ID=' . $id . '&action=insert','post', 'onsubmit="return ValidateForm(this)"'),
 'HEADING_TITLE'=>HEADING_TITLE,
 'MIDDLE_STRING'=>$middle_string,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
	'different_banners'=>$different_banners,
 'update_message'=>$messageStack->output()));
 $template->pparse('bannermgt');
?>