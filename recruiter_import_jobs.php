<?
/*
***********************************************************
***********************************************************
**********#	Name				      : Shambhu Prasad Patnaik#********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #**********
**********#	Copyright (c) www.aynsoft.com 2004	#***********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_IMPORT_JOBS);
$template->set_filenames(array('import_jobs' => 'recruiter_import_jobs.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'recruiter_import_jobs.js';
//////
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_RECRUITER_LOGIN));
}
include_once("general_functions/import_jobs.php");

$action=tep_db_prepare_input($_POST['action']);
if(tep_not_null($action))
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
      $content= get_csv_file_content(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name,100);
      unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name);
      if(is_array($content))
      {
       $message = recruiter_import_job($content);
       $total_message=count($message);
       if($total_message)
       {
        for($i=0;$i<$total_message;$i++)
        {
         if($message[$i]['error']!='')
         $messageStack->add_session($message[$i]['error'],'error');
         else
         {
          $row_get_job=getAnyTableWhereData(JOB_TABLE,"job_id='".tep_db_input($message[$i]['success'])."' and recruiter_id ='".$_SESSION['sess_recruiterid']."'",'job_title');
          $messageStack->add_session('"'.INFO_TEXT_SUCCESSFULLY_JOB.tep_db_output($row_get_job['job_title']).INFO_TEXT_INSERTED.'" ','success');
         }
        }
       }
      }
      tep_redirect(tep_href_link(FILENAME_RECRUITER_IMPORT_JOBS));
      break;
     case 'xml' :
      $content= read_xml_job(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name,100);
      unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$import_file_name);
      if(is_array($content))
      {
       $message = recruiter_import_job($content);
       $total_message=count($message);
       if($total_message)
       {
        for($i=0;$i<$total_message;$i++)
        {
         if($message[$i]['error']!='')
         $messageStack->add_session($message[$i]['error'],'error');
         else
         {
          $row_get_job=getAnyTableWhereData(JOB_TABLE,"job_id='".tep_db_input($message[$i]['success'])."' and recruiter_id ='".$_SESSION['sess_recruiterid']."'",'job_title');
          $messageStack->add_session('"'.INFO_TEXT_SUCCESSFULLY_JOB .tep_db_output($row_get_job['job_title']).INFO_TEXT_INSERTED.'"','success');
         }
        }
       }
      }
      tep_redirect(tep_href_link(FILENAME_RECRUITER_IMPORT_JOBS));
      break;
    }
   }
   break;
 }
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'SECTION_INFO_TEXT_IMPORT' => SECTION_INFO_TEXT_IMPORT,
 'INFO_TEXT_IMPORT_FILE'    => INFO_TEXT_IMPORT_FILE,
 'INFO_TEXT_IMPORT_FILE1'   => tep_draw_file_field("import_file",true),
 'INFO_TEXT_IMPORT_FILE2'   => INFO_TEXT_IMPORT_FILE2,
 'INFO_TEXT_SAMPLE_FILE'    => INFO_TEXT_SAMPLE_FILE,
 'INFO_TEXT_SAMPLE_FILE1'   => '<a class="" href="'.tep_href_link('jobs.csv').'" target="_blank">'.INFO_TEXT_CSV_FILE.'</a>, <a class="" href="'.tep_href_link('job_xml.xml').'" target="_blank">'.INFO_TEXT_XML.'</a>',
 'upload_form'              => tep_draw_form('resume_upload',FILENAME_RECRUITER_IMPORT_JOBS,'', 'post', 'enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add'),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'INFO_TEXT_IMPORT_MULTIPLE_JOBS'=>INFO_TEXT_IMPORT_MULTIPLE_JOBS,
 'INFO_TEXT_TO_IMPORT_MULTIPLE_JOBS'=>INFO_TEXT_TO_IMPORT_MULTIPLE_JOBS,
 'INFO_TEXT_SELECT_CSV_FILE'  =>INFO_TEXT_SELECT_CSV_FILE,
 'INFO_TEXT_SAMPLE_CSV_FILE'  =>INFO_TEXT_SAMPLE_CSV_FILE,
 'INFO_TEXT_SELECT_XML'       =>INFO_TEXT_SELECT_XML,
 'INFO_TEXT_SAMPLE_XML_FILE'  =>INFO_TEXT_SAMPLE_XML_FILE,
 'INFO_TEXT_SUBSEQUENTLY_THE_JOBS'=>INFO_TEXT_SUBSEQUENTLY_THE_JOBS,
 'INFO_TEXT_JOB_SEARCH_RESULTS'   =>INFO_TEXT_JOB_SEARCH_RESULTS,
 'INFO_TEXT_IMPORT'               =>INFO_TEXT_IMPORT,
 'INFO_TEXT_FIELD_OVERVIEW'       =>INFO_TEXT_FIELD_OVERVIEW,
 'INFO_TEXT_FIELD'                =>INFO_TEXT_FIELD,
 'INFO_TEXT_EXAMPLE'              =>INFO_TEXT_EXAMPLE,
 'INFO_TEXT_NOTES'                =>INFO_TEXT_NOTES,
 'INFO_TEXT_JOB_TITLE'            =>INFO_TEXT_JOB_TITLE,
 'INFO_TEXT_PROGRAMMER'           =>INFO_TEXT_PROGRAMMER,
 'INFO_TEXT_FREE_TEXT'            =>INFO_TEXT_FREE_TEXT,
 'INFO_TEXT_JOB_REFERENCE'        =>INFO_TEXT_JOB_REFERENCE,
 'INFO_TEXT_RECRUITMENT'          =>INFO_TEXT_RECRUITMENT,
 'INFO_TEXT_FREE_TEXT_THE_REFERENCE' =>INFO_TEXT_FREE_TEXT_THE_REFERENCE,
 'INFO_TEXT_JOB_COUNTRY'          =>INFO_TEXT_JOB_COUNTRY,
 'INFO_TEXT_INDIA'                =>INFO_TEXT_INDIA,
 'INFO_TEXT_FREE_TEXT_FROM_LIST_PROVIDED'=>INFO_TEXT_FREE_TEXT_FROM_LIST_PROVIDED,
 'INFO_TEXT_JOB_STATE'            =>INFO_TEXT_JOB_STATE,
 'INFO_TEXT_DELHI'                =>INFO_TEXT_DELHI,
 'INFO_TEXT_JOB_LOCATION'         =>INFO_TEXT_JOB_LOCATION,
 'INFO_TEXT_JOB_NEW_DELHI'        =>INFO_TEXT_JOB_NEW_DELHI,
 'INFO_TEXT_JOB_SALARY'           =>INFO_TEXT_JOB_SALARY,
 'INFO_TEXT_MONEY'                =>INFO_TEXT_MONEY,
 'INFO_TEXT_JOB_INDUSTRY'         =>INFO_TEXT_JOB_INDUSTRY,
 'INFO_TEXT_IT_SOFTWARE'          =>INFO_TEXT_IT_SOFTWARE,
 'INFO_TEXT_MAXIMUN_5'            =>INFO_TEXT_MAXIMUN_5,
 'INFO_TEXT_JOB_SHORT_DESCRIPTION'=>INFO_TEXT_JOB_SHORT_DESCRIPTION,
 'INFO_TEXT_SHORT_DESCRIPTION'    =>INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_200_LIMIT'            =>INFO_TEXT_200_LIMIT,
 'INFO_TEXT_JOB_DESCRIPTION'      =>INFO_TEXT_JOB_DESCRIPTION,
 'INFO_TEXT_DETAILED_DESCRIPTION' =>INFO_TEXT_DETAILED_DESCRIPTION,
 'INFO_TEXT_1000_LIMIT'           =>INFO_TEXT_1000_LIMIT,
 'INFO_TEXT_JOB_TYPE'             =>INFO_TEXT_JOB_TYPE,
 'INFO_TEXT_CONTRACT_PERMANENT'   =>INFO_TEXT_CONTRACT_PERMANENT,
 'INFO_TEXT_JOB_EXPERIENCE'       =>INFO_TEXT_JOB_EXPERIENCE,
 'INFO_TEXT_AN_INTEGER'           =>INFO_TEXT_AN_INTEGER,
 'INFO_TEXT_JOB_DURATION'         =>INFO_TEXT_JOB_DURATION,
 'INFO_TEXT_AN_INTEGER_VALUE'     =>INFO_TEXT_AN_INTEGER_VALUE,
 'INFO_TEXT_XML_FILE_FORMAT'      =>INFO_TEXT_XML_FILE_FORMAT,
 'INFO_TEXT_SEPARATED_BY'         =>INFO_TEXT_SEPARATED_BY,
 'INFO_TEXT_SEPARATED_BY_JOB_TYPE'=>INFO_TEXT_SEPARATED_BY_JOB_TYPE,
 'INFO_TEXT_NUMBER_OF_MONTHS'     =>INFO_TEXT_NUMBER_OF_MONTHS,
 'INFO_TEXT_NUMBER_OF_DAYS'       =>INFO_TEXT_NUMBER_OF_DAYS,
 'INFO_TEXT_JSCRIPT_FILE'         => '<script src="'.$jscript_file.'"></script>',
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output(),
 ));
$template->pparse('import_jobs');
?>