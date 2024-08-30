<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_IMPORT_RECRUITER);
$template->set_filenames(array('user_import' => 'admin1_import_recruiter.htm'));
include_once(FILENAME_ADMIN_BODY);
include_once("../general_functions/import_users.php");


//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
//print_r($_POST);die();
if (tep_not_null($action)) 
{
 switch($action)
 { 
  case 'add': 
   $error=false;
   if(tep_not_null($_FILES['import_file']['name']))
   {     
    if($obj_resume = new upload('import_file',PATH_TO_MAIN_PHYSICAL_TEMP.'/','644',array('csv','xml')))
    {
     $import_file_name=tep_db_input($obj_resume->filename);
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
   if(!$error)
   {
    unset($_SESSION['messageToStack']);
    $file_type=substr($import_file_name,-3);    
    switch($file_type)
    {
     case 'csv' :
      $content= get_csv_file_content(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name,5000);
      unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name);
      if(is_array($content))
      {
       $message = import_user_recruiter($content);
 	   if($message)
	   {
       $total_message=count($message);
       if($total_message)
       {
        for($i=0;$i<$total_message;$i++)
        {
         if($message[$i]['error']!='')
         $messageStack->add_session($message[$i]['error'],'error');
         else if($message[$i]['success'])
         {
          $messageStack->add_session('"'.INFO_TEXT_SUCCESSFULLY_JOBSEEKER."  ".tep_db_output($message[$i]['success'])." ".INFO_TEXT_INSERTED.'" ','success');
         }
		 else
		  $messageStack->add_session($message['error'],'error');

        }
       }
	  }
      }
      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IMPORT_RECRUITER));
      break;
    }
   }
   break;
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'         => HEADING_TITLE,
 'import_form'           => tep_draw_form('user_import',PATH_TO_ADMIN.FILENAME_ADMIN1_IMPORT_RECRUITER,'', 'post','enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add'),
 'INFO_TEXT_IMPORT_USER' =>INFO_TEXT_IMPORT_USER,
 'INFO_TEXT_IMPORT_USER1'=>tep_draw_file_field("import_file",true),
 'INFO_TEXT_SAMPLE_FILE' => INFO_TEXT_SAMPLE_FILE,
 'button' => '<input class="btn btn-primary" type="submit" value="'.IMAGE_IMPORT_NOW.'">',
 'update_message'=>$messageStack->output()));
$template->pparse('user_import');
?>