<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME5);
$template->set_filenames(array('resume_step5' => 'jobseeker_resume5.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume5.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}

//print_r($_GET);die();

if(isset($_POST['action']))
{
// print_r($_POST);
// exit;
}
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$action1 = tep_db_prepare_input($_GET['action1']);
if($action=='' && tep_not_null($action1))
$action=$action1;

if (isset($_POST['query_string']))
  $resume_id =check_data($_POST['query_string'],"@@@","resume_id","resume");
elseif (isset($_GET['query_string']))
   $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");

$query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
///// Check  Resume  validity///////////
if(!$check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id ='".$resume_id."' and jobseeker_id ='".$_SESSION['sess_jobseekerid']."'",'resume_title,jobseeker_photo'))
 {
  $messageStack->add_session(MESSAGE_RESUME_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
 }
 //print_r($check1);
///////////////////////////
if(tep_not_null($action))
{
 switch($action)
 {
   case 'delete_photo':
				if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$check1['jobseeker_photo']))
				{
					@unlink(PATH_TO_MAIN_PHYSICAL_PHOTO.$check1['jobseeker_photo']);
					$sql_data_array=array('updated'=>'now()','jobseeker_photo'=>'');
					tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array, 'update', "resume_id = '" . $resume_id . "'");
				}
    $messageStack->add_session(MESSAGE_SUCCESS_PHOTO_DELETE,'success');
 			tep_redirect(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string);
				break;
   case 'edit':
    $resume_photo_check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and  resume_id ='".$resume_id."'","jobseeker_resume,jobseeker_photo");
				//////// file upload resume starts //////
				///*
				$resume='';
				if(tep_not_null($_FILES['my_resume']['name']))
				{
     $resume_directory=date("Ym");
     if(check_directory(PATH_TO_RESUME.$resume_directory))
     {
      if($obj_resume = new upload('my_resume', PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/','644',array('doc','pdf','txt','docx')))
      {
       $resume=tep_db_input($obj_resume->filename);
       $resume_directory_old=get_file_directory($resume_photo_check['jobseeker_resume'],6);
       if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory_old.'/'.$resume_photo_check['jobseeker_resume']))
       {
        @unlink(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory_old.'/'.$resume_photo_check['jobseeker_resume']);
       }
      }
      else
      {
       $error=true;
      }
     }
     else
     {
      $error=true;
     }
				}
				//*/
			 //////// file upload ends //////
   //////// file upload photo starts //////
   $photo='';
   if($_FILES['my_photo']['name']!="")
   {
    if($obj_photo = new upload('my_photo', PATH_TO_MAIN_PHYSICAL_PHOTO,'644',array('gif','jpg','jpeg','png','jpeg')))
    {
     $photo=tep_db_input($obj_photo->filename);
     if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$resume_photo_check['jobseeker_photo']))
     {
      @unlink(PATH_TO_MAIN_PHYSICAL_PHOTO.$resume_photo_check['jobseeker_photo']);
     }
    }
    else
    {
     $error=true;
    }
   }
   //////// file upload photo ends //////
   //////vedio/////////
   $jobseeker_video=tep_db_prepare_input($_POST['jobseeker_video']);
   $video_link=tep_db_prepare_input($_POST['jobseeker_video']);
   if(strlen($video_link)==32)
   $video_link='';
   if(tep_not_null($video_link))
   {
    if((substr($video_link,0,32)!='https://www.youtube.com/watch?v=') &&  (substr($video_link,0,17)!='https://youtu.be/'))
    {
     $messageStack->add(INVALID_URL_ERROR, 'error');
     $error=true;
    }
   }
	  if((!tep_not_null($video_link) || strlen($video_link)<=32) && !$error)
   {
    //$messageStack->add(INVALID_URL_ERROR, 'error');
    //$error=true;
   }
   ///////////end vedio////////.
   //Check
   $resume_text= stripslashes($_POST['resume_text']);
   if(!$error)
	  {
	   $sql_data_array=array('jobseeker_resume_text'=>$resume_text);
    if($resume!='')
    {
     $sql_data_array['jobseeker_resume']=$resume;
    }
    if($photo!='')
    {
     $sql_data_array['jobseeker_photo']=$photo;
    }
    $sql_data_array['jobseeker_video']=$video_link;
    if(tep_not_null($resume_id))
    {
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
     $sql_data_array['updated']='now()';
     tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array, 'update', "resume_id = '" . $resume_id . "'");
     tep_db_query('update '.JOBSEEKER_LOGIN_TABLE ." set updated='".date("Y-m-d H:i:s")."' where jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");

     if($_SESSION['sess_new_jobseeker']=='y')
     {
      unset($_SESSION['sess_new_jobseeker']);
    	 $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
						tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string));
     }
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
    }
			}
   break;
  }
}
/*************new addition**************/
//////////////////////////////
$upload_form=tep_draw_form('resume_upload', FILENAME_JOBSEEKER_RESUME5,'', 'post', 'enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','edit').tep_draw_hidden_field('query_string',$query_string);
/****************above new additon***********/
if($error)
{
 $query_string=encode_string("resume_id@@@".$resume_id."@@@resume");
 if($_POST['action']=="edit")
 {
  if($_POST['action']=="edit")
  {
   $resume_id                = $resume_id;
   $resume_name              = $resume_name;
   $resume                   = $resume;
   $resume_text              = $resume_text;
  }
 }
}
else
{
 /*********************addition of photo*******************/
	if(tep_not_null($resume_id))
	{
		$fields="jobseeker_resume,jobseeker_photo,jobseeker_resume_text,jobseeker_video,jobseeker_id";
		$row=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."'",$fields);
		$resume_text=$row['jobseeker_resume_text'];
		$resume=$row['jobseeker_resume'];        //print_r($resume);
		if(tep_not_null($resume))
		{
   $resume_directory=get_file_directory($resume,6);
			if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$resume))
			{
					$query_string1 = encode_string("resume_id@@@".$resume_id."@@@resume");
     $resume="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='".tep_href_link(FILENAME_JOBSEEKER_RESUME_DOWNLOAD,(tep_not_null($resume_id)?'query_string='.$query_string1:''))."'>Download</a>&nbsp;&nbsp;]";
				//print_r($resume);
			}
			else
			{
				$resume='';
			}
		}
		$photo=$row['jobseeker_photo'];
		if(tep_not_null($photo))
		{
			if(is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$photo))
			{
				//$photo="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".PATH_TO_PHOTO.$photo."','')\">Preview</a>&nbsp;&nbsp;]";
				$photo="<a class='me-2 text-dark' style='font-size:13px; text-align:right;' href='#' onclick=\"javascript:popupimage('".PATH_TO_PHOTO.$photo."','')\"><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-eye' viewBox='0 0 16 16'>
        <path d='M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z'/>
        <path d='M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z'/>
      </svg></a> <a class='me-2 text-danger' style='font-size:13px; text-align:right;' href='".tep_href_link(FILENAME_JOBSEEKER_RESUME5,'query_string='.$query_string.'&action1=delete_photo')."' onclick='return confirm(\"Do you want to delete this photo\");'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
      <path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z'/>
    </svg></a>";


			}
			else
			{
				$photo='';
			}
		}
		$video_link=$row['jobseeker_video'];
        $jobseeker_video =$video_link;
		if(tep_not_null($video_link) && strlen($video_link)>17 )
		{
 		$jobseeker_id=$row['jobseeker_id'];
        $query_string=encode_string("video_dispaly===".$resume_id."===videoid");

        if (preg_match("/watch\?v=/i",$video_link))
		{
         $photo_arr=(explode("watch?v=",(basename($video_link))));
         $video_img       ='<img src="https://img.youtube.com/vi/'.trim($photo_arr[1]).'/2.jpg">';
		}
 		elseif (preg_match("#youtu.be/(.*)#i",$video_link,$mat))
		 $video_img ='<img src="https://img.youtube.com/vi/'.trim($mat[1]).'/2.jpg">';

   $vedio   = "<a class='mt-2 d-inline-block img-thumbnail' href='#' onclick=\"popUp('".tep_href_link(FILENAME_DISPLAY_VIDEO,'query_string1='.$query_string)."')\" >".$video_img."</a>";
		}
		if(tep_not_null($resume) || tep_not_null($resume_text) || tep_not_null($photo) || tep_not_null($vedio) )
		{
   $upload_button='<button class="btn btn-primary " type="submit">'.TEXT_UPDATE.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
		}
		else
		{
  	$upload_button='<button class="btn btn-primary " type="submit">'.TEXT_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT);
		}
	}
}
 /*********************************************************/
$query_string       = encode_string("resume_id@@@".$resume_id."@@@resume");
$add_next_button2    = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES)."' class='btn btn-skip-next'>'.SKIP_THE_STEP.'</a>";

 $resume1='<div class="step ms-0"><a class="" href ="#"  onclick="document.resume.submit()">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
		  $resume4='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_SKILLS.'</a></div>';
		  $resume5='<div class="step current"><a class=" " href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_UPLOAD.'</a></div>';
				$resume6='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_REFERENCE.'</a></div>';
		  $view_resume='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_VIEW_RESUME.'</a></div>';
//////////////Jobseeker resume left start//////
	define('JOBSEEKER_RESUME_LEFT','


  <div class="mb-3">
  <div class="row">
	<div class="">
	<div class="arrow-steps clearfix mx-auto">
    '.$resume1.'
    '.$resume2.'
   '.$resume6.'
  '.$resume3.'
   '.$resume4.'
   '.$resume5.'
   '.$view_resume.'
   </div>
   </div>
   </div>
   </div>
	
	<td width="19%">
	   <div class="resume-side-menu" style="display:none;">
	   <ul class="resume-side-nav">'.tep_draw_form('resume', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
	   
	   <li class="resume-left-title-inactive"><i class="fa fa-file-text resume-inactive-icon" aria-hidden="true"></i> '.$resume1.'</li>
	   </form>
										'.tep_draw_form('resume1', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#resume_name" onclick="document.resume1.submit()">'.INFO_TEXT_RESUME_NAME.'</a></li></form>
											'.tep_draw_form('resume2', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#objective" onclick="document.resume2.submit()">'.INFO_TEXT_OBJECTIVE.'</a></li></form>
											'.tep_draw_form('resume3', FILENAME_JOBSEEKER_RESUME1, '', 'post').tep_draw_hidden_field('resume_id',$resume_id).'
												<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="#target_job" onclick="document.resume3.submit()">'.INFO_TEXT_TARGET_JOB.'</a></li></form>
											</ul>
											<ul class="resume-side-nav"><li class="resume-left-title-inactive"><i class="fa fa-briefcase resume-inactive-icon" aria-hidden="true"></i> '.$resume2.'</li><li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#total_experience" >'.INFO_TEXT_TOTAL_WORK_EXP.'</a></li>
											<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'#experience" >'.INFO_TEXT_YOUR_WORK_EXPERIENCE.'</a></li></ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-bookmark resume-inactive-icon" aria-hidden="true"></i>'.$resume6.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-graduation-cap resume-inactive-icon" aria-hidden="true"></i>'.$resume3.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-user resume-inactive-icon" aria-hidden="true"></i>'.$resume4.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'#skill" >'.INFO_TEXT_YOUR_SKILLS.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'#language" >'.INFO_TEXT_LANGUAGES.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-active"><i class="fa fa-upload resume-active-icon" aria-hidden="true"></i>'.$resume5.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_RESUME.'</a></li>
</ul>
<ul class="resume-side-nav">
	<li class="resume-left-title-inactive"><i class="fa fa-eye resume-inactive-icon" aria-hidden="true"></i>'.$view_resume.'</li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#profile" >'.INFO_TEXT_PERSONAL_PROFILE.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#work_experience" >'.INFO_TEXT_EXPERIENCE.'</a></li>
	<li><i class="fa fa-angle-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#target_job" >'.INFO_TEXT_TARGET_JOB.'</a></li></ul></div></td>');

$update_message=$messageStack->output();
if(!tep_not_null($jobseeker_video))
 $jobseeker_video='https://www.youtube.com/watch?v=';

$template->assign_vars(array(
 'HEADING_TITLE'                   => HEADING_TITLE,
 'add_next_button2'                => $add_next_button2,
 'upload_form'                     => $upload_form,
 'upload_button'                   => $upload_button ,

 'SECTION_ACCOUNT_RESUME_NAME'     => SECTION_ACCOUNT_RESUME_NAME,
 'SECTION_DOCUMENT_UPLOAD'         => SECTION_DOCUMENT_UPLOAD,

 'REQUIRED_INFO'                   => REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'           => INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'          => $check1['resume_title'],

 'INFO_TEXT_UPLOAD_RESUME'         => INFO_TEXT_UPLOAD_RESUME,
 'INFO_TEXT_UPLOAD_RESUME1'        => tep_draw_file_field("my_resume").$resume.'<span style="font-size:13px; text-align:right;" class="m-display-table">'.INFO_TEXT_UPLOAD_RESUME_HELP,'</span>',

 'INFO_TEXT_UPLOAD_PHOTO'          => INFO_TEXT_UPLOAD_PHOTO,
 'INFO_TEXT_PHOTO1'                => tep_draw_file_field("my_photo").$photo.'<span style="font-size:13px; text-align:right;" class="m-display-table">'.INFO_TEXT_UPLOAD_PHOTO_HELP,'</span>',

 'INFO_TEXT_CUT_PASTE_CV'          => INFO_TEXT_CUT_PASTE_CV,
 'INFO_TEXT_CUT_PASTE_CV1'         => tep_draw_textarea_field('resume_text', 'soft', '50', '12', stripslashes($resume_text), '', '', false),
 //'RESUME_NAVIGATION'               => $resume_navegation,
 'INFO_TEXT_ADD_VIDEO'             => INFO_TEXT_ADD_VIDEO,
 'INFO_TEXT_ADD_VIDEO1'            => tep_draw_input_field("jobseeker_video",$jobseeker_video,'class="form-control" placeholder="https://www.youtube.com/watch?v=" size="60"',false).$vedio,
 'INFO_TEXT_JSCRIPT_FILE'          => $jscript_file,
 'LEFT_BOX_WIDTH'                  => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'                 => RIGHT_BOX_WIDTH1,
	'JOBSEEKER_RESUME_LEFT'           => JOBSEEKER_RESUME_LEFT,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'                      => RIGHT_HTML,
 'update_message'                  => $update_message));
 $template -> pparse('resume_step5');
?>