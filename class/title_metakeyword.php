<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
///*
include_once(PATH_TO_MAIN_PHYSICAL."general_functions/google_job_functions.php");

class title_metakeyword
{
 var $title,$metakeywords;
 function __construct()
 {
  $length=0;
  if(tep_not_null(PATH_TO_MAIN))
  {
   $length=strlen(PATH_TO_MAIN);
  }

  $request_url=substr(strtolower($_SERVER['REQUEST_URI']),1+$length);
  $file_name=substr(strtolower($_SERVER['PHP_SELF']),1+$length);
  if(tep_not_null($request_url) && substr($request_url,0,7) =='article')
  {
   $file_name=$request_url;
  }
  elseif($file_name==FILENAME_ARTICLE)
  {
   if(tep_not_null($_GET['article_seo']))
   {
	$article_seo = tep_db_prepare_input($_GET['article_seo']);
 	if($row_check=getAnyTableWhereData(ARTICLE_TABLE,"seo_name='".tep_db_input($article_seo)."'","id"))
    $file_name='article_'.$row_check['id'].'.html';
   }
  }		
  if($row=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='".tep_db_input($file_name)."'"))
  {
   $languages_row=getAnyTableWhereData(LANGUAGE_TABLE,"name='".$_SESSION['language']."'","*");
   (($languages_row['code']=='en')?$languae_code='':$languae_code=$languages_row['code'].'_');
   if(tep_not_null($row[$languae_code.'title']))
	  {
    $this->title=stripslashes($row[$languae_code.'title']);
    $this->metakeywords=stripslashes($row[$languae_code.'meta_keyword']);
	  }
   else
   {
    $this->title=stripslashes(SITE_TITLE1);
    $this->metakeywords=stripslashes($row['meta_keyword']);
   }
  }
  elseif($row=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='default.php'"))
  {
   $this->title=stripslashes(SITE_TITLE1);
   $this->metakeywords=stripslashes($row['meta_keyword']);
   if($file_name==PATH_TO_FORUM.FILENAME_INDEX && !isset($_GET['category']))
   {
    if($row=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='forum_index.php'"))
    {
     if(tep_not_null($row['title']))
     $this->title=stripslashes($row['title']);
     if(tep_not_null($row['meta_keyword']))
     $this->metakeywords=stripslashes($row['meta_keyword']);
    }
   }
  }
  else
  {
   $this->title=stripslashes(SITE_TITLE1);
   $this->metakeywords='';
   if($file_name==PATH_TO_FORUM.FILENAME_INDEX && !isset($_GET['category']))
   {
    if($row=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='forum_index.php'"))
    {
     if(tep_not_null($row['title']))
     $this->title=stripslashes($row['title']);
     if(tep_not_null($row['meta_keyword']))
     $this->metakeywords=stripslashes($row['meta_keyword']);
    }
   }
  }
  if($file_name==FILENAME_JOB_DETAILS)
  {
   $google_s =getGoogleJobData(); 
   if($google_s!='')
    $this->metakeywords = $this->metakeywords.$google_s;
  }
  elseif(substr(strtolower($_SERVER['PHP_SELF']),1+$length)==FILENAME_ARTICLE && tep_not_null($_GET['article_seo']))
  {
    $google_s =getGoogleArticleData(); 
   if($google_s!='')
    $this->metakeywords = $this->metakeywords.$google_s;
  }

 }
}
?>