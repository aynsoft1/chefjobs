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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_BANNER_UPLOAD);
$template->set_filenames(array('bannerupload' => 'admin1_banner_upload.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_POST);
//print_r($_GET);	
$banner_type='image';
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'insert_banner':
  case 'save_banner':
   $duration_unlimited=tep_db_prepare_input($_POST['TR_duration_unlimited']);
   $f_date=tep_db_prepare_input($_POST['from_date']);
   $f_month=tep_db_prepare_input($_POST['from_month']);
   $f_year=tep_db_prepare_input($_POST['from_year']);
   $t_date=tep_db_prepare_input($_POST['to_date']);
   $t_month=tep_db_prepare_input($_POST['to_month']);
   $t_year=tep_db_prepare_input($_POST['to_year']);
   $duration_from=$f_year."-".$f_month."-".$f_date;
   $duration_to=$t_year."-".$t_month."-".$t_date;
   //echo "dob=".$from_dob;
   //echo "date=".$to_dob;
   if($duration_unlimited=='F')
   {
    if(!@checkdate($f_month,$f_date,$f_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_FROM_DATE_ERROR,'Wrong date entered');
    }
    if(!@checkdate($t_month,$t_date,$t_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_TO_DATE_ERROR,'Wrong date entered');
    }
    if($duration_from > $duration_to)
    {
     $error = true;
     $messageStack->add(MESSAGE_DATE_ERROR,'Wrong date entered');
    }
   }

  //print_r($_POST);die();
  //echo $duration_unlimited;
   $title           = tep_db_prepare_input($_POST['TR_banner_title']);
   $banner_type     = tep_db_prepare_input($_POST['TR_banner_type']);
   $href            = tep_db_prepare_input($_POST['banner_href']);
   $alt             = tep_db_prepare_input($_POST['banner_alt']);
   $script          = stripslashes(trim($_POST['banner_script']));
		 $banner_location = tep_db_prepare_input($_POST['TR_banner_location']);
			$now             = date('Y-m-d H:i:s');
   $company_name    = tep_db_prepare_input($_POST['company_name']);
   $company_contact = tep_db_prepare_input($_POST['company_contact']);
   $company_email   = tep_db_prepare_input($_POST['company_email']);
			$banner_costing  = tep_db_prepare_input($_POST['banner_costing']);
   $company_comments= tep_db_prepare_input($_POST['company_comments']);
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
     if(!tep_not_null($_FILES['banner']['name']))
     {
      $error = true;
      $messageStack->add(UPLOAD_ERROR,'error');
     }
			 }
			 elseif($banner_type=='script')
		  { 
     if(!tep_not_null($script))
					{
      $error = true;
      $messageStack->add(SCRIPT_ERROR,'error');
     }
			 }
		 }
   $sql_data_array['title']       = $title;
   $sql_data_array['banner_type'] = $banner_type;
   $sql_data_array['href']        = $href;
   $sql_data_array['alt']         = $alt;
   $sql_data_array['script']      = $script;
   $sql_data_array['location']    = $banner_location;
   $sql_data_array['company_name']= $company_name;
   $sql_data_array['company_contact'] = $company_contact;   
   $sql_data_array['company_email']   = $company_email;   
   $sql_data_array['banner_costing']  = $banner_costing;   
   $sql_data_array['company_comments']= $company_comments;   
   $sql_data_array['banner_duration_from']= $duration_from;
   $sql_data_array['banner_duration_to']  = $duration_to;
   $sql_data_array['duration_unlimited']  = $duration_unlimited;
			$sql_data_array['banner_date']         = $now;
 
   $filetype_array=array('jpg','jpeg','gif','bmp','jpeg');
   $errmsg="Only jpg,jpeg,gif,bmp,jpeg File Types are Allowed to upload.";
   if($action=='insert_banner')
   {
  	 if($row_chek=getAnyTableWhereData(BANNER_TABLE," title ='".$title."'",'id'))
  	 {
  	 	$messageStack->add(MESSAGE_NAME_ERROR, 'error');
   	}
    elseif($banner_type=='image')
    {
					if(!$error)
					{
						if(tep_not_null($_FILES['banner']['name']))
							{
								if($obj_logo = new upload('banner',PATH_TO_MAIN_PHYSICAL_BANNER,'644',array('gif','jpg','png','jpeg')))
								{
									$image=tep_db_input($obj_logo->filename);
								}
								else
								{
									$error=true;
								}
							}
							else
					  {
								$error=true;
  	 	   $messageStack->add(UPLOAD_ERROR, 'error');
							}
	     if($image!='')
	     {
       $sql_data_array['src']=$image;
       tep_db_perform(BANNER_TABLE,$sql_data_array);
       $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD)); 
						}
				 }
    }
			 elseif($banner_type=='script' && !$error)
			 {  
     tep_db_perform(BANNER_TABLE,$sql_data_array);
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD)); 
			 }// else end
   }//action=inser_banner end
   break;
 }
}
///////////// Middle Values 
$banner_query_raw="select * from " . BANNER_TABLE ." order by title";
$banner_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banner_query_raw, $banner_query_numrows);
$banner_query = tep_db_query($banner_query_raw);
if(tep_db_num_rows($banner_query) > 0)
{
 $alternate=1;
 while ($banner = tep_db_fetch_array($banner_query)) 
 {
  if ((!isset($_GET['bID']) || (isset($_GET['bID']) && ($_GET['bID'] == $banner['id']))) && !isset($bInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $bInfo = new objectInfo($banner);
  }
  $alternate++;
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $view='';
  if($banner['banner_type']=='image')
   $view=  "<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_BANNER.$banner['src']."','".tep_db_output($banner['title'])."')\"> <img src='".tep_href_link("img/find.gif")."' alt='view'></a>"; 

  $template->assign_block_vars('banner', array( 'row_selected' => $row_selected,
   'title' => tep_db_output($banner['title']),
   'src' => tep_db_output($banner['src']),
   'company'=>tep_db_output($banner['company_name']),
   'duration'=>(($banner['duration_unlimited']=='T')?tep_db_output('Unlimited'):tep_db_output('From:'.$banner['banner_duration_from'].' to '.$banner['banner_duration_to'])),
   'location'=>$location,
   'bdate'=>tep_db_output(tep_date_short($banner['banner_date'])),
   'view'=> $view  ,
  // 'action'=>$action_image,
   ));
 }
}
//// for right side
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action) 
{ 
 case 'new_banner':
 case 'insert_banner':
	$heading[]=array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_NEW_BANNER.'</div');
// creating banner location array from banner_location table
	$banner_location_query = tep_db_query("select banner_location_id, banner_location_name from " . BANNER_LOCATION_TABLE);
	while ($banner_location = tep_db_fetch_array($banner_location_query)) 
	{
		 $banner_location_array[] = array('id' => $banner_location['banner_location_id'],
                          'text' => $banner_location['banner_location_name']);
	}
	$contents=array('form' => tep_draw_form('banner', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD, 'action=insert_banner','post',' enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"'));
	$contents[]=array('text' =>'<div class="mb-1 text-danger">' .TEXT_INFO_UPLOAD_INTRO. '</div>');
	$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_TITLE.'<br>'.tep_draw_input_field('TR_banner_title', $title, 'class="form-control form-control-sm"',true ));

	$contents[] = array('text' => '<br>&nbsp;' .INFO_TEXT_BANNER_TYPE.'<br>&nbsp;'. tep_draw_radio_field("TR_banner_type", 'image', '' ,$banner_type, 'onclick="set_type()"').'&nbsp;Image&nbsp;'.tep_draw_radio_field("TR_banner_type", 'script', '',$banner_type,'onclick="set_type()"').'&nbsp;Script' );
 $contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_HREF.'<br>'.tep_draw_input_field('banner_href', $href, 'class="form-control form-control-sm"' ));
 $contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_ALT.'<br>'.tep_draw_input_field('banner_alt', $alt, 'class="form-control form-control-sm"' ));
	$contents[]=array('text' => '<br><div id=image>'.INFO_TEXT_BANNER.'<br>'.tep_draw_file_field('banner', $banner, '' ).'</div>');
	$contents[]=array('text' => '<br><div id=script>'.INFO_TEXT_BANNER_SCRIPT.'<br>'.tep_draw_textarea_field('banner_script','','30','4',$script,'class="form-control form-control-sm"',"",false).'</div>');

	$contents[]=array('text' => '<br>'.INFO_TEXT_BANNER_LOCATION.'<br>' . tep_draw_pull_down_menu('TR_banner_location',  $banner_location_array));
	//period of banner
	$contents[] = array('text' => '<br>&nbsp;' .INFO_TEXT_BANNER_DURATION.'<br>&nbsp;'. tep_draw_radio_field("TR_duration_unlimited", 'T', 'checked' ,'', 'onclick="set_fixed_duration()"').'&nbsp;Unlimited&nbsp;'.tep_draw_radio_field("TR_duration_unlimited", 'F', ($_POST['TR_duration_unlimited']=='F'?true:false),'','onclick="set_fixed_duration()"').'&nbsp;Fixed' );
	$contents[]=array('text' => '<br><div id=frombanner>'.'From: '.'<br>'.datelisting(gmdate("Y-m-d"), 'name="from_date" disabled', 'name="from_month" disabled', 'name="from_year" disabled', '', "2005", gmdate("Y")+2).'</div>');
	$contents[]=array('text' => '<br><div id=tobanner>'.'To: '.'<br>'.datelisting(gmdate("Y-m-d",mktime(0,0,0,gmdate("m"),gmdate("d")+7,gmdate("Y"))), 'name="to_date" disabled', 'name="to_month" disabled', 'name="to_year" disabled', "2006", gmdate("Y")+2).'</div>');

	//company information details
	
	$contents[]=array('text' => '<br><br><hr><font size=2><div class="mb-2 font-weight-bold">'.TEXT_COMPANY_INFO.'</div></font>');
	$contents[]=array('text' => ''.tep_draw_input_field('company_name', $company_name, 'placeholder="Company name" class="form-control form-control-sm"' ));
	$contents[]=array('text' => '<br>'.tep_draw_input_field('company_contact', $company_contact, 'placeholder="Contact Number" class="form-control form-control-sm"' ));
	$contents[]=array('text' => '<br>'.tep_draw_input_field('company_email', $company_email, 'placeholder="Email" class="form-control form-control-sm"' ));
	$contents[]=array('text' => '<br>'.tep_draw_input_field('banner_costing', $banner_costing, 'placeholder="Costing" class="form-control form-control-sm"' ));
	$contents[]=array('text' =>'<br>'.tep_draw_textarea_field('company_comments',1,30,2, $company_comments, 'placeholder="Comments" class="form-control form-control-sm"' ));
	$contents[]=array('align' => 'left', 'text' => '<br>
  
  '.tep_draw_submit_button_field('', IMAGE_UPLOAD,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD).'">'.IMAGE_CANCEL.'</a>');
 break;
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

 'TABLE_HEADING_BANNER'=>TABLE_HEADING_BANNER,
 'TABLE_HEADING_BANNER_COMPANYNAME'=>TABLE_HEADING_BANNER_COMPANYNAME,
 'TABLE_HEADING_BANNER_DURATION'=>TABLE_HEADING_BANNER_DURATION,
 'TABLE_HEADING_BANNER_DATE'=>TABLE_HEADING_BANNER_DATE,
 'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$banner_split->display_count($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS),
 'no_of_pages'=>$banner_split->display_links($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
 'button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD, 'action=new_banner') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'form'=>tep_draw_form('add_edit', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_UPLOAD,'ID=' . $id . '&action=insert','post', 'onsubmit="return ValidateForm(this)"'),
	'HEADING_TITLE'=>HEADING_TITLE,
 'BANNER_DURATION_FROM'=>datelisting(gmdate("Y-m-d",mktime(0,0,0,gmdate("m"),gmdate("d")+7,gmdate("Y"))), 'name="Date"', 'name="Month"', 'name="Year"', "2005", gmdate("Y")+2),
 'BANNER_DURATION_TO'=>datelisting(gmdate("Y-m-d",mktime(0,0,0,gmdate("m"),gmdate("d")+7,gmdate("Y"))), 'name="Date"', 'name="Month"', 'name="Year"', "2005", gmdate("Y")+2),

 'MIDDLE_STRING'=>$middle_string,
	'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
	'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
	'update_message'=>$messageStack->output()));
 $template->pparse('bannerupload');
 ?>