<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_JOBFAIRS);
$template->set_filenames(array('jobfair' => 'admin1_list_jobfairs.htm','jobfair1' => 'admin1_jobfair.htm','preview' => 'admin1_jobfair1.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_POST);//die();
////////////////
$edit=false;
$error =false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$jobfair_id=(isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
if(tep_not_null($jobfair_id))
{
 if(!$row_check_jobfair_id=getAnyTableWhereData(JOBFAIR_TABLE,"id='".tep_db_input($jobfair_id)."'"))
 {
  $messageStack->add_session(MESSAGE_JOBFAIR_ERROR, 'error');
  tep_redirect(FILENAME_ADMIN1_LIST_OF_JOBFAIRS);
 }
 $jobfair_id=$row_check_jobfair_id['id'];
 $edit=true;
}
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);
if(tep_not_null($action))
{
 switch($action)
 {
 /* case 'deletePlogo':
    $logo_id          = tep_db_prepare_input($_POST['logo_id']);
    if($row_f=getAnyTableWhereData(JOBFAIR_PARTNERS_TABLE,"id ='".tep_db_input($logo_id)."'",'partner_logo'))
    {
	 $fileName =$row_f['partner_logo'];
	 if(file_exists(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName))
      @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName);
      tep_db_query("delete from ".JOBFAIR_PARTNERS_TABLE." where id='".tep_db_input($logo_id)."'");
	 die('success');
    }
    die();
	  break;
*/
  case 'save_matatags':
   $meta_title          = tep_db_prepare_input($_POST['meta_title']);
   $metatags            = stripslashes($_POST['metatags']);
   $sql_data_array1=array('file_name'=>'jobfair_'.$jobfair_id.'.html',
                          'fr_title'=>$meta_title,
                          'fr_meta_keyword'=>$metatags
                          );
   if($row_meta=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='jobfair_".tep_db_input($jobfair_id).".html'",'id'))
   {
 	tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE, $sql_data_array1,'update',"id='".tep_db_input($row_meta['id'])."'");
   }
   else
   {
 	tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE,$sql_data_array1);
   }
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS,tep_get_all_get_params(array('action','selected_box'))));
   break;

  case 'confirm_delete':
   if($edit && tep_not_null($row_check_jobfair_id['jobfair_logo']))
   {
    $old_photo= $row_check_jobfair_id['jobfair_logo'];
    if(is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$old_photo))
    @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$old_photo);
/*    $old_pdf= $row_check_jobfair_id['jobfair_pdf'];
    if(is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$old_pdf))
    @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$old_pdf);
*/
   }
   tep_db_query("delete from ".TITLE_KEYWORDMETATYPE_TABLE." where file_name='jobfair_".tep_db_input($jobfair_id).".html'");
   //////
/*   $query1 ='SELECT * FROM '.JOBFAIR_PARTNERS_TABLE.' WHERE  jobfair_id = "'.$jobfair_id.'"';
   $result1=tep_db_query($query1);
   $x=tep_db_num_rows($result1);
   if($x>0)
   {
    while($row1 = tep_db_fetch_array($result1))
	{
 	 $fileName =$row_f['partner_logo'];
	 if(file_exists(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName))
      @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName);
	}
    tep_db_query("delete from ".JOBFAIR_PARTNERS_TABLE." where jobfair_id='".tep_db_input($jobfair_id)."'");
	tep_db_free_result($result1);
   }
*/
   //////
   tep_db_query("delete from ".JOBFAIR_TABLE." where id='".tep_db_input($jobfair_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(FILENAME_ADMIN1_LIST_OF_JOBFAIRS);
   break;
  case 'jobfair_active':
  case 'jobfair_inactive':
   tep_db_query("update ".JOBFAIR_TABLE." set jobfair_status='".($action=='jobfair_active'?'Yes':'No')."' where id='".tep_db_input($jobfair_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'preview':
    $hidden_fields='';
    $oneday				  = tep_db_prepare_input($_POST['oneday']);
	$jobfair_title        = tep_db_prepare_input($_POST['TR_jobfair_title']);
	$jobfair_location     = tep_db_prepare_input($_POST['TR_jobfair_location']);
	$jobfair_venue        = tep_db_prepare_input($_POST['TR_jobfair_venue']);
    $jobfair_googlemap_url= tep_db_prepare_input($_POST['TR_jobfair_googlemap_url']);
    $jobfair_video        = tep_db_prepare_input($_POST['jobfair_video']);
    $jobfair_begindate = tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']);
    $jobfair_enddate   = ($oneday=='Yes'?tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']):tep_db_prepare_input($_POST['TR_endyear']."-".$_POST['TR_endmonth']."-".$_POST['TR_enddate']));
    $jobfair_reg_begindate = tep_db_prepare_input($_POST['TR_reg_beginyear']."-".$_POST['TR_reg_beginmonth']."-".$_POST['TR_reg_begindate']);
    $jobfair_reg_enddate   = tep_db_prepare_input($_POST['TR_reg_endyear']."-".$_POST['TR_reg_endmonth']."-".$_POST['TR_reg_enddate']);
    $description       = stripslashes($_POST['description']);
    $short_description = stripslashes($_POST['short_description']);
    $jobfair_status    = tep_db_prepare_input($_POST['jobfair_status']);
	$partner_logo_array= array();

    $hidden_fields.=tep_draw_hidden_field('oneday',$oneday);
    $hidden_fields.=tep_draw_hidden_field('TR_jobfair_title',$jobfair_title);
    $hidden_fields.=tep_draw_hidden_field('TR_jobfair_location',$jobfair_location);
    $hidden_fields.=tep_draw_hidden_field('TR_jobfair_venue',$jobfair_venue);
    $hidden_fields.=tep_draw_hidden_field('TR_jobfair_googlemap_url',$jobfair_googlemap_url);
    $hidden_fields.=tep_draw_hidden_field('jobfair_video',$jobfair_video);
    $hidden_fields.=tep_draw_hidden_field('TR_beginyear',$_POST['TR_beginyear']);
    $hidden_fields.=tep_draw_hidden_field('TR_beginmonth',$_POST['TR_beginmonth']);
    $hidden_fields.=tep_draw_hidden_field('TR_begindate',$_POST['TR_begindate']);
    $hidden_fields.=tep_draw_hidden_field('TR_endyear',$_POST['TR_endyear']);
    $hidden_fields.=tep_draw_hidden_field('TR_endmonth',$_POST['TR_endmonth']);
    $hidden_fields.=tep_draw_hidden_field('TR_enddate',$_POST['TR_enddate']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_beginyear',$_POST['TR_reg_beginyear']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_beginmonth',$_POST['TR_reg_beginmonth']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_begindate',$_POST['TR_reg_begindate']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_endyear',$_POST['TR_reg_endyear']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_endmonth',$_POST['TR_reg_endmonth']);
    $hidden_fields.=tep_draw_hidden_field('TR_reg_enddate',$_POST['TR_reg_enddate']);
    $hidden_fields.=tep_draw_hidden_field('description',$description);
    $hidden_fields.=tep_draw_hidden_field('short_description',$short_description);
    $hidden_fields.=tep_draw_hidden_field('jobfair_status',$jobfair_status);
    if(strlen($jobfair_title)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_TITLE, 'error');
     $error=true;
    }
    if(strlen($jobfair_location)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_LOCATION, 'error');
     $error=true;
    }
    if(strlen($jobfair_venue)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VENUE, 'error');
     $error=true;
    }

/*    if(strlen($jobfair_video)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VIDEO, 'error');
     $error=true;
    }
*/
    if(strlen($jobfair_googlemap_url)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_GOOGLE_MAP_URL, 'error');
     $error=true;
    }
    if(strlen($short_description)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_SHORT_DESCRIPTION, 'error');
     $error=true;
    }
   if(($jobfair_begindate>$jobfair_enddate ) && ($oneday!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_DATE_ERROR, 'error');
   }
   if($jobfair_reg_begindate>$jobfair_reg_enddate )
   {
    $error=true;
    $messageStack->add(REGISTRATION_DATE_ERROR, 'error');
   }

    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_DESCRIPTION, 'error');
     $error=true;
    }
    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['jobfair_logo']['name']))
     {
      if($obj_resume = new upload('jobfair_logo', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('jpg','gif','png')))
      {
       $jobfair_logo_name=tep_db_input($obj_resume->filename);
       $hidden_fields.=tep_draw_hidden_field('jobfair_logo_name',$jobfair_logo_name);
      }
     }
     //////// file upload ends //////
/*
     //////// pdf upload Attachment starts //////
     if(tep_not_null($_FILES['jobfair_pdf']['name']))
     {
      if($obj_pdf = new upload('jobfair_pdf', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('pdf')))
      {
       $jobfair_pdf_name=tep_db_input($obj_pdf->filename);
       $hidden_fields.=tep_draw_hidden_field('jobfair_pdf_name',$jobfair_pdf_name);
      }
     }
     //////// file upload ends //////


	 $counter = 0;
     $partners_logo='';
     foreach($_FILES["files"] as $f ){
     $name = $_FILES["files"]["name"][$counter];
	 $type = $_FILES["files"]["type"][$counter];
	 $tmp_name = $_FILES["files"]["tmp_name"][$counter];
	 $error1 = $_FILES["files"]["error"][$counter];
	 $size = $_FILES["files"]["size"][$counter];
	 $_FILES['partner_file'] =array('name'=>$name,''=>$name,'type'=>$type,'tmp_name'=>$tmp_name,'error'=>$error1,'size'=>$size);
	 if($tmp_name!='')
	 if($obj_file = new upload('partner_file', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('jpg','gif','png')))
     {
      $partner_file_name=tep_db_input($obj_file->filename);
      $hidden_fields.=tep_draw_hidden_field('partner_file[]',$partner_file_name);
	  $partners_logo.=' <div id="img_'.$counter.'" class="img_box">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_TEMP.$partner_file_name.'&size=100','','','','').'</div>';
     }
	 $counter ++;
	}
*/
    }
    break;
  case 'back':
   	$category_id			= tep_db_prepare_input($_POST['TR_category']);
    $jobfair_title          = tep_db_prepare_input($_POST['TR_jobfair_title']);
    $jobfair_location       = tep_db_prepare_input($_POST['TR_jobfair_location']);
    $jobfair_venue          = tep_db_prepare_input($_POST['TR_jobfair_venue']);
    $jobfair_googlemap_url  = tep_db_prepare_input($_POST['TR_jobfair_googlemap_url']);
    $jobfair_video          = tep_db_prepare_input($_POST['jobfair_video']);
    $jobfair_begindate      = tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']);
    $jobfair_enddate   = ($oneday=='Yes'?tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']):tep_db_prepare_input($_POST['TR_endyear']."-".$_POST['TR_endmonth']."-".$_POST['TR_enddate']));
    $jobfair_reg_begindate = tep_db_prepare_input($_POST['TR_reg_beginyear']."-".$_POST['TR_reg_beginmonth']."-".$_POST['TR_reg_begindate']);
    $jobfair_reg_enddate   = tep_db_prepare_input($_POST['TR_reg_endyear']."-".$_POST['TR_reg_endmonth']."-".$_POST['TR_reg_enddate']);
    $description			= stripslashes($_POST['description']);
    $short_description		= stripslashes($_POST['short_description']);
    $jobfair_status         = tep_db_prepare_input($_POST['jobfair_status']);
    $jobfair_logo_name		= tep_db_prepare_input($_POST['jobfair_logo_name']);
//    $jobfair_pdf_name		= tep_db_prepare_input($_POST['jobfair_pdf_name']);
    $oneday					= tep_db_prepare_input($_POST['oneday']);
    if(tep_not_null($jobfair_logo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name) )
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name);
    }
/*    if(tep_not_null($jobfair_pdf_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_pdf_name))
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_pdf_name);
    }

	if(isset($_POST['partner_file']))
    foreach($_POST["partner_file"] as $fname)
	{
	  if(tep_not_null($fname) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$fname) )
     {
      @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$fname);
     }
	}
*/
    if(strlen($jobfair_title)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_TITLE, 'error');
     $error=true;
    }
    if(strlen($jobfair_location)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_LOCATION, 'error');
     $error=true;
    }
    if(strlen($jobfair_venue)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VENUE, 'error');
     $error=true;
    }
/*    if(strlen($jobfair_video)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VIDEO, 'error');
     $error=true;
    }
*/
    if(strlen($jobfair_googlemap_url)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_GOOGLE_MAP_URL, 'error');
     $error=true;
    }

    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_DESCRIPTION, 'error');
     $error=true;
    }
    if(strlen($short_description)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_SHORT_DESCRIPTION, 'error');
     $error=true;
    }
   if(($jobfair_begindate>$jobfair_enddate ) && ($oneday!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_DATE_ERROR, 'error');
   }
   if($jobfair_reg_begindate>$jobfair_reg_enddate )
   {
    $error=true;
    $messageStack->add(REGISTRATION_DATE_ERROR, 'error');
   }


    break;
  case 'add':
  case 'save':
     $now=date("Y-m-d H:i:s");
      $oneday			   = tep_db_prepare_input($_POST['oneday']);
    $jobfair_title        = tep_db_prepare_input($_POST['TR_jobfair_title']);
     $jobfair_location     = tep_db_prepare_input($_POST['TR_jobfair_location']);
     $jobfair_venue        = tep_db_prepare_input($_POST['TR_jobfair_venue']);
     $jobfair_googlemap_url= tep_db_prepare_input($_POST['TR_jobfair_googlemap_url']);
     $jobfair_video        = tep_db_prepare_input($_POST['jobfair_video']);
     $jobfair_begindate    = tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']);
    $jobfair_enddate   = ($oneday=='Yes'?tep_db_prepare_input($_POST['TR_beginyear']."-".$_POST['TR_beginmonth']."-".$_POST['TR_begindate']):tep_db_prepare_input($_POST['TR_endyear']."-".$_POST['TR_endmonth']."-".$_POST['TR_enddate']));
    $jobfair_reg_begindate = tep_db_prepare_input($_POST['TR_reg_beginyear']."-".$_POST['TR_reg_beginmonth']."-".$_POST['TR_reg_begindate']);
    $jobfair_reg_enddate   = tep_db_prepare_input($_POST['TR_reg_endyear']."-".$_POST['TR_reg_endmonth']."-".$_POST['TR_reg_enddate']);
     $description		   = stripslashes($_POST['description']);
     $short_description	   = stripslashes($_POST['short_description']);
     $jobfair_status       = tep_db_prepare_input($_POST['jobfair_status']);
     $jobfair_logo_name	   = tep_db_prepare_input($_POST['jobfair_logo_name']);
//     $jobfair_pdf_name	   = tep_db_prepare_input($_POST['jobfair_pdf_name']);

    if(strlen($jobfair_title)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_TITLE, 'error');
     $error=true;
    }
    if(strlen($jobfair_location)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_LOCATION, 'error');
     $error=true;
    }
    if(strlen($jobfair_venue)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VENUE, 'error');
     $error=true;
    }

/*    if(strlen($jobfair_video)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_VIDEO, 'error');
     $error=true;
    }
*/
    if(strlen($jobfair_googlemap_url)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_GOOGLE_MAP_URL, 'error');
     $error=true;
    }
     if(strlen($short_description)<=0)
     {
      $messageStack->add(ERROR_JOBFAIR_SHORT_DESCRIPTION, 'error');
      $error=true;
     }
	if(strlen($description)<=0)
     {
      $messageStack->add(ERROR_JOBFAIR_DESCRIPTION, 'error');
      $error=true;
     }
 /*   if(strlen($jobfair_logo_name)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_LOGO, 'error');
     $error=true;
    }
    if(strlen($jobfair_pdf_name)<=0)
    {
     $messageStack->add(ERROR_JOBFAIR_PDF_DOCUMENT, 'error');
     $error=true;
    }
*/
   if(($jobfair_begindate>$jobfair_enddate ) && ($oneday!='Yes'))
   {
    $error=true;
    $messageStack->add(MESSAGE_DATE_ERROR, 'error');
   }
   if($jobfair_reg_begindate>$jobfair_reg_enddate )
   {
    $error=true;
    $messageStack->add(REGISTRATION_DATE_ERROR, 'error');
   }

     if(!$error)
     {
      $sql_data_array=array( 'jobfair_title'			=> $jobfair_title,
                             'jobfair_location'			=> $jobfair_location,
                             'jobfair_venue'			=> $jobfair_venue,
                             'jobfair_googlemap_url'    => $jobfair_googlemap_url,
                             'jobfair_video'			=> $jobfair_video,
                             'jobfair_begindate'		=> $jobfair_begindate,
                             'jobfair_enddate'			=> ($oneday=='Yes'?$jobfair_begindate:$jobfair_enddate),
                             'jobfair_reg_begindate'	=> $jobfair_reg_begindate,
                             'jobfair_reg_enddate'		=> $jobfair_reg_enddate,
                             'jobfair_description'      => $description,
                             'jobfair_short_description'=> $short_description,
                             'jobfair_status'			=> $jobfair_status,
	//						 'inserted'					=> $now
                            );
       if(tep_not_null($jobfair_logo_name))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name))
        {
         $target_file_name=PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$jobfair_logo_name;
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name,$target_file_name);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name);
         chmod($target_file_name, 0644);
         $sql_data_array['jobfair_logo']=$jobfair_logo_name;
         if($edit && tep_not_null($row_check_jobfair_id['jobfair_logo']))
         {
          $old_photo= $row_check_jobfair_id['jobfair_logo'];
          if(is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$old_photo))
          @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$old_photo);
         }
        }
       }
       ///////////////////////////////////////

/*       if(tep_not_null($jobfair_pdf_name))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_pdf_name))
        {
         $target_pdf_name=PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$jobfair_pdf_name;
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_pdf_name,$target_pdf_name);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_pdf_name);
         chmod($target_pdf_name, 0644);
         $sql_data_array['jobfair_pdf']=$jobfair_pdf_name;
         if($edit && tep_not_null($row_check_jobfair_id['jobfair_pdf']))
         {
          $old_pdf= $row_check_jobfair_id['jobfair_pdf'];
          if(is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$old_pdf))
          @unlink(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$old_pdf);
         }
        }
       }
*/
       ////////////////////////////////////////////////
       if($edit)
       {
		$sql_data_array['updated']=$now;
        if($jobfair_seo_url = get_canonical_title($jobfair_seo_url,$jobfair_id,'jobfair'))
        $sql_data_array['jobfair_seo_name']=$jobfair_seo_url;
 /////////***************////////////////
		if($_POST['oneday']=='yes')
			$sql_data_array['jobfair_enddate']=$jobfair_begindate;
//////////////////////////////////////////
       tep_db_perform(JOBFAIR_TABLE, $sql_data_array,'update',"id='".$jobfair_id."'");
/*        if(isset($_POST['partner_file']))
        foreach($_POST["partner_file"] as $fname)
	    {
		 $fileName       = tep_db_prepare_input($fname);
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$fileName,PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$fileName);
         chmod(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName, 0644);
		 $sql_data_array2=array('jobfair_id'   => $jobfair_id,
                               'partner_logo'  => $fileName,
                               'inserted'=> $now,
                               );
      	   tep_db_perform(JOBFAIR_PARTNERS_TABLE,$sql_data_array2);
          }
		 $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
*/
       }
       else
       {
        $sql_data_array['inserted']=$now;
        $jobfair_seo_url =get_canonical_title($jobfair_title);
        $sql_data_array['jobfair_seo_name']= $jobfair_seo_url;
        tep_db_perform(JOBFAIR_TABLE, $sql_data_array);
		$row_id=getAnyTableWhereData(JOBFAIR_TABLE," inserted='".tep_db_input($now)."' and jobfair_title='".tep_db_input($jobfair_title)."' order by  inserted desc",'id');
        $id = $row_id['id'];
        if($id)
        {
         //*
         $meta_description='<meta name="description" content="'.strip_tags(substr($short_description,0,200)).'">';
         $sql_data_array1=array('file_name'   => 'jobfair_'.$id.'.html',
                               'title'       => $jobfair_title,
                               'meta_keyword'=> $meta_description,
                               );
      	  tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE,$sql_data_array1);
		  //*/
/*		  if(isset($_POST['partner_file']))
          foreach($_POST["partner_file"] as $fname)
		  {
		   $fileName       = tep_db_prepare_input($fname);
           copy(PATH_TO_MAIN_PHYSICAL_TEMP.$fileName,PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName);
           @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$fileName);
           chmod(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PARTNERS.$fileName, 0644);
		   $sql_data_array2=array('jobfair_id'   => $id,
                               'partner_logo'  => $fileName,
                               'inserted'=> $now,
                               );
      	   tep_db_perform(JOBFAIR_PARTNERS_TABLE,$sql_data_array2);
          }
*/
        }

        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       }
       tep_redirect(FILENAME_ADMIN1_LIST_OF_JOBFAIRS);
     }

 }//if($error)die("ok");
}
////////////////////////////////////////////
if($error)
{
 if($edit)
  $action='edit';
 else
  $action='new';
}
if($action=='new' || $action=='edit' || $action=='back')
{
 if(tep_not_null($row_check_jobfair_id['jobfair_logo']) && is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_LOGO.$row_check_jobfair_id['jobfair_logo']) )
 {
  $jobfair_logo1="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_JOBFAIR_LOGO.$row_check_jobfair_id['jobfair_logo']."','')\" class='label'>Preview</a>&nbsp;&nbsp;]";
 }
/* if(tep_not_null($row_check_jobfair_id['jobfair_pdf']) && is_file(PATH_TO_MAIN_PHYSICAL_JOBFAIR_PDF.$row_check_jobfair_id['jobfair_pdf']) )
 {
  $jobfair_pdf1="&nbsp;&nbsp;[&nbsp;&nbsp;<a target='_blank' class='label'href='".tep_href_link(PATH_TO_JOBFAIR_PDF.$row_check_jobfair_id['jobfair_pdf'])."'>Preview</a>&nbsp;&nbsp;]";
 }
*/
 if($edit)
 {
  $form=tep_draw_form('jobfair', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'id='.$jobfair_id.'&action=preview', 'post', 'enctype="multipart/form-data"   onsubmit="return ValidateForm(this )"');
 }
 else
 {
  $form=tep_draw_form('jobfair', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'action=preview', 'post', ' enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"');
 }
 $view_list_of_jobfairs='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS).'">'.INFO_TEXT_VIEW_LIST_OF_JOBFAIRS.'</a>';
}
elseif($action=='preview')
{
 if(tep_not_null($jobfair_logo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$jobfair_logo_name) )
 {
  $jobfair_logo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_TEMP.$jobfair_logo_name."&size=220");
 }
 elseif(tep_not_null($row_check_jobfair_id['jobfair_logo']) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$row_check_jobfair_id['jobfair_logo']) )
 {
  $jobfair_logo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_JOBFAIR_LOGO.$row_check_jobfair_id['jobfair_logo']."&size=220");
 }

 if($edit)
 {
  $form=tep_draw_form('jobfair', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'id='.$jobfair_id.'&action=save', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a href="#" onclick="set_action(\'back\')">'.tep_button('Back','class="btn btn-secondary"').'</a> '.tep_draw_submit_button_field('','Update','class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
 }
 else
 {
  $form=tep_draw_form('jobfair', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'action=add', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a href="#" onclick="set_action(\'back\')">'.tep_button('Back','class="btn btn-secondary"').'</a>'.tep_draw_submit_button_field('','Save','class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
 }
 $view_list_of_jobfairs='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS).'">'.INFO_TEXT_VIEW_LIST_OF_JOBFAIRS.'</a>';
}
else
{
//////////////////
///only for sorting starts
$sort_array=array("jf.jobfair_title","jf.jobfair_location","jf.jobfair_begindate","jf.jobfair_enddate","jf.jobfair_reg_begindate","jf.jobfair_reg_enddate","jf.jobfair_status","jf.inserted");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

 ///////////// Middle Values
 $now=date("Y-m-d H:i:s");
 $jobfair_query_raw="select jf.* from " . JOBFAIR_TABLE ." as jf order by ".$order_by_clause;//begindate desc, inserted desc";
 $jobfair_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $jobfair_query_raw, $jobfair_query_numrows);
 $jobfair_query = tep_db_query($jobfair_query_raw);
 //echo tep_db_num_rows($jobfair_query);
 if(tep_db_num_rows($jobfair_query) > 0)
 {
  $alternate=1;
  while ($jobfair = tep_db_fetch_array($jobfair_query))
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $jobfair['id']))) && !isset($jInfo) && (substr($action, 0, 3) != 'new'))
   {
    $jInfo = new objectInfo($jobfair);
    //print_r($jInfo);
   }
   if ( (isset($jInfo) && is_object($jInfo)) && ($jobfair['id'] == $jInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_JOBFAIRS . '?page='.$_GET['page'].'&id=' . $jInfo->id . '&action=edit\'"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_JOBFAIRS . '?page='.$_GET['page'].'&id=' . $jobfair['id'] . '\'"';
   }
   $alternate++;
   if ( (isset($jInfo) && is_object($jInfo)) && ($jobfair['id'] == $jInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'page='.$_GET['page'].'&id=' . $jobfair['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }

   if ($jobfair['jobfair_status'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $jobfair['id'] . '&action=jobfair_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_JOBFAIR_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_JOBFAIR_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_JOBFAIR_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $jobfair['id'] . '&action=jobfair_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_JOBFAIR_ACTIVATE, 28, 22) . '</a>';
   }

//////// Jobfair logo display////////////
$logo=$jobfair['jobfair_logo'];

if(tep_not_null($logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_JOBFAIR_LOGO.$logo))
  $logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_JOBFAIR_LOGO.$logo.'&size=50','','','','class="img-thumbnail resume--result-profile-img"');
else
  $logo=defaultProfilePhotoUrl($jobfair['jobfair_title'], true, 50, 'class="no-pic" id="seeker-img"');

//////////*********************************////

   $template->assign_block_vars('jobfair', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'status' => $status,
    'jobfair_logo' => $logo,
    'jobfair_title' => tep_db_output($jobfair['jobfair_title']),
    'jobfair_location' => tep_db_output($jobfair['jobfair_location']),
    'jobfair_begindate' => tep_date_short($jobfair['jobfair_begindate']),
    'jobfair_enddate' => tep_date_short($jobfair['jobfair_enddate']),
    'jobfair_reg_begindate' => tep_date_short($jobfair['jobfair_reg_begindate']),
    'jobfair_reg_enddate' => tep_date_short($jobfair['jobfair_reg_enddate']),
    'inserted' => tep_date_short($jobfair['inserted']),
    ));
  }
  tep_db_free_result($jobfair_query);
 }
}


//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  $contents[] = array('text' => '<b>' . tep_db_output($jInfo->jobfair_title) . '</b>');
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . tep_db_output($jInfo->jobfair_title) . '</b>');
  $contents[] = array('align' => 'left', 'text' => '
  <table>
  <tbody>
  <tr>
    <td>
    <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'page=' . $_GET['page'] . '&id=' . $jInfo->id.'&action=confirm_delete') . '">'
                    .tep_button('Confirm','class="btn btn-primary"').'</a>

    </td>
    <td>
    <a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'page=' . $_GET['page'] . '&id=' . $jInfo->id) . '">'
                    . tep_button('Cancel','class="btn btn-secondary"') . '</a>
    </td>
  </tr>
  </tbody>
  </table>
');
  break;
 case 'edit_metatags':
  $heading[]  = array('text' => '<b>'.TEXT_INFO_HEADING_METATAGS.'</b>');
    $contents=array('form' => tep_draw_form('metatags', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS,'page='.$_GET['page'].'&id='.$jInfo->id.'&action=save_matatags','post','  onsubmit="return ValidateForm(this)"'));
  $contents[] = array('text' => '<b>' . tep_db_output('jobfair_'.$jInfo->id.'.html') . '</b>');
  if(!$error)
  {
   if($row_meta=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='jobfair_".tep_db_input($jobfair_id).".html'",'fr_title,fr_meta_keyword'))
   {
    $meta_title=tep_db_output($row_meta['fr_title']);
    $metatags=tep_db_output($row_meta['fr_meta_keyword']);
   }
  }
  $contents[] = array('text' => '<br>'.INFO_TEXT_META_TITLE.'<br>'.tep_draw_input_field('meta_title', $meta_title, 'class="form-control form-control-sm"' ));
 	$contents[] = array('text' => '<br>'.INFO_TEXT_METATAGS.'<br>'.tep_draw_textarea_field('metatags','','35','7',$metatags,'class="form-control form-control-sm"',"",false));
  $contents[] = array('align' => 'left', 'text' => '<br>'.tep_draw_submit_button_field('','Save','class="btn btn-primary"').'</a>
  <a href="'
                    . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'page=' . $_GET['page'] . '&id=' . $jInfo->id) . '">'
                    . tep_button('Cancel','class="btn btn-secondary"') . '</a>');
  break;

 default:
	 ////analysis///////////////////
	 $total_rec=no_of_records(RECRUITER_JOBFAIR_TABLE, " jobfair_id ='" . $jInfo->id. "' and approved= 'Yes'", 'recruiter_id');
	 $total_jobs=no_of_records(JOB_JOBFAIR_TABLE, " jobfair_id ='" . $jInfo->id. "'", 'job_id');

 $jobfair_url=tep_href_link(get_display_link($jInfo->id,$jInfo->jobfair_seo_name.'-jobfair'));
	 //////////////// ////////////////////////////

  if (isset($jInfo) && is_object($jInfo))
		{
   $heading[] = array('text' => '<div class="font-weight-bold mb-2">'.TEXT_INFO_HEADING_TITLE.'</div>');
   $contents[] = array('text' => tep_db_output($jInfo->jobfair_title));
   $contents[] = array('text' => ''.TEXT_INFO_ACTION);
   $contents[] = array('align' => '', 'text' => '
    <table>
    <tbody>
    <tr>
        <td>
        <a class="" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'id=' . $jInfo->id . '&action=edit') . '">
        '.tep_button('Edit','class="btn btn-primary"').'
        </a>
        </td>
        <td>
        <a class="" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'page=' . $_GET['page'] .'&id=' . $jInfo->id . '&action=delete') . '">
        '.tep_button('Delete','class="btn btn-secondary"').'
        </a>
        </td>
		<td>
        <a class="" href="'.$jobfair_url.'" target="_blank">
        '.tep_button('View','class="btn btn-secondary"').'
        </a>
        </td>
  </tr>
    <tr>
        <td colspan="2">
        <a class="mt-2 mb-2" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'id=' . $jInfo->id . '&action=edit_metatags').'">
        '.tep_button('Title/Metatags','class="btn btn-secondary"').'
        </a>
        </td>
    </tr>
    </tbody>
    </table>
    ');
	  $contents[] = array('text' => '<br><b>Registered Recruiters: </b>' . $total_rec . '');
  $contents[] = array('text' => '<b>Total Jobs: </b>' .  $total_jobs);

//    $contents[] = array('align' => 'left', 'text' => '<a href="'
//                 . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'id=' . $jInfo->id . '&action=edit_metatags').'">'
//                 .tep_button('Title/Metatags','class="btn btn-primary"').'</a>');
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
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
////////////////////////////////////////////////////////////////////////
if(!$error && $action=='edit')
{
 $jInfo			= new objectInfo($row_check_jobfair_id);
 $jobfair_title = $jInfo->jobfair_title;
 $jobfair_video = $jInfo->jobfair_video;
 $jobfair_begindate    = substr($jInfo->jobfair_begindate,0,10);
 $jobfair_enddate      = substr($jInfo->jobfair_enddate,0,10);
 $jobfair_reg_begindate= substr($jInfo->jobfair_reg_begindate,0,10);
 $jobfair_reg_enddate  = substr($jInfo->jobfair_reg_enddate,0,10);
 $description = stripslashes($jInfo->jobfair_description);
 $jobfair_status     = $jInfo->jobfair_status;
 $short_description = stripslashes($jInfo->jobfair_short_description);
 $jobfair_location = $jInfo->jobfair_location;
 $jobfair_venue = $jInfo->jobfair_venue;
 $jobfair_googlemap_url    = $jInfo->jobfair_googlemap_url;
}
elseif(!$error && $action=='new')
{
 $jobfair_reg_begindate   = date("Y-m-d");
 $jobfair_reg_enddate   = date("Y-m-d");
// $jobfair_begindate   = date("Y-m-d");
// $jobfair_enddate   = date("Y-m-d");
 $jobfair_status     = 'Yes';
}
if($action=='preview')
{

 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
 'INFO_TEXT_JF_TITLE'    => INFO_TEXT_JF_TITLE,
 'INFO_TEXT_JF_TITLE1'   => tep_db_output($jobfair_title),
 'INFO_TEXT_JF_LOCATION'    => INFO_TEXT_JF_LOCATION,
 'INFO_TEXT_JF_LOCATION1'   => tep_db_output($jobfair_location),
 'INFO_TEXT_JF_VENUE'       => INFO_TEXT_JF_VENUE,
 'INFO_TEXT_JF_VENUE1'      => tep_db_output($jobfair_venue),
 'INFO_TEXT_JF_GOOGLEMAP_URL'=> INFO_TEXT_JF_GOOGLEMAP_URL,
 'INFO_TEXT_JF_GOOGLEMAP_URL1'=> tep_db_output($jobfair_googlemap_url),
 'INFO_TEXT_JF_VIDEO'    => INFO_TEXT_JF_VIDEO,
 'INFO_TEXT_JF_VIDEO1'   => tep_db_output($jobfair_video),
 'INFO_TEXT_JOBFAIR_BEGINDATE'   => INFO_TEXT_JOBFAIR_BEGINDATE,
 'INFO_TEXT_JOBFAIR_BEGINDATE1'  => tep_db_output(formate_date($jobfair_begindate)),
 'INFO_TEXT_JOBFAIR_ENDDATE'   => INFO_TEXT_JOBFAIR_ENDDATE,
 'INFO_TEXT_JOBFAIR_ENDDATE1'  => tep_db_output(formate_date($jobfair_enddate)),
 'INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE'   => INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE,
 'INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE1'  => tep_db_output(formate_date($jobfair_reg_begindate)),
 'INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE'   => INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE,
 'INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE1'  => tep_db_output(formate_date($jobfair_reg_enddate)),
 'INFO_TEXT_JOBFAIR_LOGO'=>INFO_TEXT_JOBFAIR_LOGO,
 'INFO_TEXT_JOBFAIR_LOGO1'=>$jobfair_logo1,
 'INFO_TEXT_SHORT_DESCRIPTION' => INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_JOBFAIR_PARTNER_LOGO'=>$partners_logo,
 'INFO_TEXT_SHORT_DESCRIPTION1'=> nl2br(stripslashes($short_description)),
 'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=> nl2br(stripslashes($description)),
 'INFO_TEXT_JOBFAIR_STATUS'=> INFO_TEXT_JOBFAIR_STATUS,
 'INFO_TEXT_JOBFAIR_STATUS1' => tep_db_output($jobfair_status),
 'button'=>$button,
 'form'=>$form,
 'view_list_of_jobfairs'=>$view_list_of_jobfairs,
  ));
 $template->pparse('preview');
}
elseif($action=='new' || $action=='edit' || $action=='back')
{
/* $partners_logo='';
 if(tep_not_null($jobfair_id))
 {
  $query1 ='SELECT * FROM '.JOBFAIR_PARTNERS_TABLE.' WHERE  jobfair_id = "'.$jobfair_id.'"';
  $result1=tep_db_query($query1);
  $x=tep_db_num_rows($result1);
  if($x>0)
  {
	$partners_logo='<div>';
    while($row1 = tep_db_fetch_array($result1))
	{
 	 $partners_logo.=' <div id="img_'.$row1['id'].'" class="img_box"><a class="delete_img">delete</a> '.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_JOBFAIR_PARTNERS.$row1['partner_logo'].'&size=100',' ','','','').'</div>';

	}
	$partners_logo.='</div>';
	tep_db_free_result($result1);
  }
 }
*/
 $template->assign_vars(array(
 'INFO_TEXT_JF_TITLE'=>INFO_TEXT_JF_TITLE,
 'INFO_TEXT_JF_TITLE1'=>tep_draw_input_field('TR_jobfair_title',$jobfair_title,'size="45" class="form-control form-control-sm"',true),
 'INFO_TEXT_JF_LOCATION'=>INFO_TEXT_JF_LOCATION,
 'INFO_TEXT_JF_LOCATION1'=>tep_draw_input_field('TR_jobfair_location',$jobfair_location,'size="45" class="form-control form-control-sm"',true),
 'INFO_TEXT_JF_VENUE'=>INFO_TEXT_JF_VENUE,
 'INFO_TEXT_JF_VENUE1'			=> tep_draw_textarea_field('TR_jobfair_venue', 'soft', '40', '3', $jobfair_venue, 'class="form-control"', true),
 'INFO_TEXT_JF_GOOGLEMAP_URL'     => INFO_TEXT_JF_GOOGLEMAP_URL,
 'INFO_TEXT_JF_GOOGLEMAP_URL1'    => tep_draw_textarea_field('TR_jobfair_googlemap_url', 'soft', '70', '4', $jobfair_googlemap_url, 'class="form-control"', true),//tep_draw_input_field('TR_jobfair_googlemap_url',$jobfair_googlemap_url,'size="45"',true),
 'INFO_TEXT_JF_VIDEO'=>INFO_TEXT_JF_VIDEO,
 'INFO_TEXT_JF_VIDEO1'=>tep_draw_input_field('jobfair_video',$jobfair_video,'class="form-control form-control-sm"',false),
 'INFO_TEXT_JOBFAIR_BEGINDATE'=>INFO_TEXT_JOBFAIR_BEGINDATE,
 'INFO_TEXT_JOBFAIR_BEGINDATE1'=>datelisting_admin($jobfair_begindate, 'name="TR_begindate"', 'name="TR_beginmonth"', 'name="TR_beginyear"', "2019", date("Y")+1, true),
 'INFO_TEXT_ONE_DAY1'  => tep_draw_checkbox_field('oneday', 'Yes', '',$oneday,'id="checkbox_oneday" onclick="set_oneday_date()"')."&nbsp;<span class='small'><label for='checkbox_oneday'>".INFO_TEXT_ONE_DAY."</label></span>",

 'INFO_TEXT_JOBFAIR_ENDDATE'=>INFO_TEXT_JOBFAIR_ENDDATE,
 'INFO_TEXT_JOBFAIR_ENDDATE1'=>datelisting_admin($jobfair_enddate, 'name="TR_enddate"', 'name="TR_endmonth"', 'name="TR_endyear"', "2019", date("Y")+1, true),
 'INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE'=>INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE,
 'INFO_TEXT_JOBFAIR_REGISTRATION_BEGINDATE1'=>datelisting_admin($jobfair_reg_begindate, 'name="TR_reg_begindate"', 'name="TR_reg_beginmonth"', 'name="TR_reg_beginyear"', "2019", date("Y")+1, true),
 'INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE'=>INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE,
 'INFO_TEXT_JOBFAIR_REGISTRATION_ENDDATE1'=>datelisting_admin($jobfair_reg_enddate, 'name="TR_reg_enddate"', 'name="TR_reg_endmonth"', 'name="TR_reg_endyear"', "2019", date("Y")+1, true),

 'INFO_TEXT_JOBFAIR_LOGO'=>INFO_TEXT_JOBFAIR_LOGO,
 'INFO_TEXT_JOBFAIR_LOGO1'=>tep_draw_file_field("jobfair_logo").$jobfair_logo1,
/* 'INFO_TEXT_JOBFAIR_PDF'=>INFO_TEXT_JOBFAIR_PDF,
 'INFO_TEXT_JOBFAIR_PDF1'=>tep_draw_file_field("jobfair_pdf").$jobfair_pdf1,
*/
 'INFO_TEXT_DESCRIPTION'=>INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=>tep_draw_textarea_field('description', 'soft', '60', '10',  stripslashes($description), '', '', true),
 'INFO_TEXT_SHORT_DESCRIPTION'=>INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_SHORT_DESCRIPTION1'=>tep_draw_textarea_field('short_description', 'soft', '70', '4', stripslashes($short_description), '', '', true),
 'INFO_TEXT_JOBFAIR_STATUS'=>INFO_TEXT_JOBFAIR_STATUS,
 'INFO_TEXT_JOBFAIR_STATUS1'=>tep_draw_radio_field('jobfair_status', 'Yes', '', $jobfair_status, 'id="radio_jobfair_status1"').'&nbsp;<label for="radio_jobfair_status1">Yes</label>&nbsp;'.tep_draw_radio_field('jobfair_status', 'No', '', $jInfo->jobfair_status, 'id="radio_jobfair_status2"').'&nbsp;<label for="radio_jobfair_status2">No</label>',
 'button'=>tep_draw_submit_button_field('','Preview','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif',IMAGE_PREVIEW),
 'form'=>$form,
'click_logo_to_delete'=>($edit && $partners_logo!=''?'click on logo to delete.':''),
 'INFO_PARTNERS_UPLOAD'=>INFO_PARTNERS_UPLOAD,
  'INFO_TEXT_PARTNERS_LOGO'=> $partners_logo,
 'ajax_url' => 'var ajax_url="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS).'"; ',
 'view_list_of_jobfairs'=>$view_list_of_jobfairs,
  ));
 $template->pparse('jobfair1');
}
else
{
 $template->assign_vars(array(
  'TABLE_HEADING_JF_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_JF_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_JF_BEGINDATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_JF_BEGINDATE.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_JF_ENDDATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_JF_ENDDATE.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_JF_REGISTRATION_BEGINDATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_JF_REGISTRATION_BEGINDATE.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_JF_REGISTRATION_ENDDATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_JF_REGISTRATION_ENDDATE.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  'TABLE_HEADING_JF_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][5]."' class='white'>".TABLE_HEADING_JF_STATUS.$obj_sort_by_clause->return_sort_array['image'][5]."</a>",
  'TABLE_HEADING_JF_DATE_ADDED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][6]."' class='white'>".TABLE_HEADING_JF_DATE_ADDED.$obj_sort_by_clause->return_sort_array['image'][6]."</a>",
  'count_rows'=>$jobfair_split->display_count($jobfair_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBFAIRS),
  'no_of_pages'=>$jobfair_split->display_links($jobfair_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_JOBFAIRS, 'action=new') . '"><i class="bi bi-plus-lg me-2"></i>' .IMAGE_NEW . '</a>',
  'jobfair_button'=>'<a class="btn btn-primary" href="' . tep_href_link(FILENAME_JOBFAIR) . '">' .IMAGE_JOBFAIR . '</a>',

 ));
 $template->pparse('jobfair');
}
?>