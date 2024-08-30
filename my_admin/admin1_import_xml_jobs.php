<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2022  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_IMPORT_XML_JOBS);
$template->set_filenames(array('jobs_import' => 'admin1_import_xml_jobs.htm'));
include_once(FILENAME_ADMIN_BODY);
include_once("../general_functions/import_xml_jobs.php");


//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
//print_r($_POST);die();
if (tep_not_null($action)) 
{
 switch($action)
 { 
  case 'add': 
   $error=false;
   $IR_recruiter_id=tep_db_prepare_input($_POST['IR_recruiter_id']);
   if(!tep_not_null($IR_recruiter_id))
   {
    $messageStack->add(RECRUITER_ID_ERROR,'error');
    $error=true;
   }
   if(!$row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".(int)tep_db_input($IR_recruiter_id)."'",'recruiter_id'))
   {
    $messageStack->add(RECRUITER_ID_ERROR,'error');
    $error=true;
   }
   if(tep_not_null($_FILES['import_file']['name']) && !$error)
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
     case 'xml' :
      $content= readXML_job(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name);
      unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name);
      if(is_array($content))
      {
       $message = recruiter_import_job($content,$IR_recruiter_id);
	   if($message)
	   {
       $total_message=count($message);
       if($total_message)
       {
        for($i=0;$i<$total_message;$i++)
        {
         if($message[$i]['error']!='')
         $messageStack->add_session($message[$i]['error'],'error');
         else
         {
          $messageStack->add_session('"'.INFO_TEXT_SUCCESSFULLY_JOBS."  ".tep_db_output($message[$i]['success'])." ".INFO_TEXT_INSERTED.'" ','success');
         }
        }
       }
	  }
      }
      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IMPORT_XML_JOBS));
      break;
    }
   }
   break;
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'         => HEADING_TITLE,
 'import_form'           => tep_draw_form('jobs_import',PATH_TO_ADMIN.FILENAME_ADMIN1_IMPORT_XML_JOBS,'', 'post','enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add'),
 'INFO_TEXT_RECRUITER_ID' =>INFO_TEXT_RECRUITER_ID,
 'INFO_TEXT_RECRUITER_ID1' =>tep_draw_input_field('IR_recruiter_id', $IR_recruiter_id,'placeholder="Recuiter ID" class="form-control"',true),
 'INFO_TEXT_IMPORT_JOBS' =>INFO_TEXT_IMPORT_JOBS,
 'INFO_TEXT_IMPORT_JOBS1'=>tep_draw_file_field("import_file",true),
 'INFO_TEXT_SAMPLE_FILE' => INFO_TEXT_SAMPLE_FILE,
 'button' => '<input type="submit" value="'.IMAGE_IMPORT_NOW.'">',
 
 'INFO_TEXT_XML_FILE_FORMAT'       =>INFO_TEXT_XML_FILE_FORMAT,
  
 'update_message'=>$messageStack->output()));
$template->pparse('jobs_import');
?>