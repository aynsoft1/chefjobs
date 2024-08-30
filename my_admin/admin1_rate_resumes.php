<?
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("../include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_RATE_RESUMES);
$template->set_filenames(array('search' => 'admin1_resumes_search.htm',
                               'search_result'=>'admin1_rate_resumes.htm',
                               'email'=>'admin1_send_email.htm',
                               'preview'=>'admin1_preview_email.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_POST);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$email = (isset($_POST['email']) ? $_POST['email'] : '');
$excel = (isset($_POST['excel']) ? $_POST['excel'] : '');
$preview = (isset($_POST['preview']) ? $_POST['preview'] : '');
////// send email ///
if(isset($_POST['send_mail_x']) && tep_not_null($_POST['send_mail_x']))
{
 $email_address=$_POST['email_address1'];
 if($email_address['0']=='-1')
 {
  array_shift($email_address);
 }
 $email_address=implode(", ",$email_address);
 $subject=tep_db_output($_POST['TR_subject']);
 $email_text=stripslashes($_POST['TR_message']);
 //$text = strip_tags($email_text);
 if (SEND_EMAILS == 'true')
 {
  //$message = new email();
  if(tep_not_null($_POST['attachment']))
  {
    $destination=PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment'];
    $file_name = basename($destination);
    //$handle    = fopen($destination, "r");
    //$contents = fread($handle, filesize($destination));
    //fclose($handle);
    //if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']))
    //@unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']);
    //$message->add_attachment($contents,substr($file_name,14));
  }
  /*
  if (EMAIL_USE_HTML == 'true')
  {
   $message->add_html($email_text);
  }
  else
  {
   $message->add_text($text);
  }
		// Send message
			$message->build_message();
			$message->send('', $email_address, SITE_OWNER, ADMIN_EMAIL, $subject);
			*/
  tep_new_mail('',$email_address, $subject, $email_text,SITE_OWNER, EMAIL_FROM,$destination,substr($file_name,14)) ;
  if(tep_not_null($_POST['attachment']))
  {
   if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']))
   @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']);
  }

 }
//tep_mail('' , $email_address, $subject, $email_text, SITE_OWNER, ADMIN_EMAIL);
/*
 echo "To: ".$email_address;
 echo "<br>Subject: ".$subject;
 echo "<br>Message: ".$email_text;
 */
 $messageStack->add_session(MESSAGE_SUCCESS_SENT, 'success');
 tep_redirect(FILENAME_ADMIN1_RATE_RESUMES);
}
//////
if($preview=='preview')
{
 $preview=true;
}
else
{
 $preview=false;
}
if($email=='email')
{
 $email=true;
}
elseif($email=='back')
{
 $email='back';
}
else
{
 $email=false;
}
if($excel=='excel')
{
 $excel=true;
}
else
{
 $excel=false;
}
//echo $jID;
// search
if(tep_not_null($_POST['keyword']))
{
 $keyword=tep_db_prepare_input($_POST['keyword']);
}
if(tep_not_null($_POST['word1']))
{
 $word1=tep_db_prepare_input($_POST['word1']);
}
if(tep_not_null($_POST['first_name']))
{
 $first_name=tep_db_prepare_input($_POST['first_name']);
}
if(tep_not_null($_POST['last_name']))
{
 $last_name=tep_db_prepare_input($_POST['last_name']);
}
if(tep_not_null($_POST['email_address']))
{
 $email_address=tep_db_prepare_input($_POST['email_address']);
}
if(tep_not_null($_POST['country']))
{
 $country=(int)tep_db_prepare_input($_POST['country']);
}
if(tep_not_null($_POST['state']) or tep_not_null($_POST['state1']))
{
 if(isset($_POST['state']) and $_POST['state']!='')
 $state=tep_db_prepare_input($_POST['state']);
 elseif(isset($_POST['state1']))
 $state=tep_db_prepare_input($_POST['state1']);
}
if(tep_not_null($_POST['city']))
{
 $city=tep_db_prepare_input($_POST['city']);
}
if(tep_not_null($_POST['zip']))
{
 $zip=tep_db_prepare_input($_POST['zip']);
}
if(tep_not_null($_POST['industry_sector']))
{
 $industry_sector=$_POST['industry_sector'];
 $industry_sector1=implode(",",$industry_sector);
 $industry_sector1=remove_child_job_category($industry_sector1);
}
if(tep_not_null($_POST['experience']))
{
 $experience=$_POST['experience'];
}
$minimum_rating =  tep_db_prepare_input($_POST['minimum_rating']);
$maximum_rating =  tep_db_prepare_input($_POST['maximum_rating']);

// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';

   if(tep_not_null($keyword)) //   keyword starts //////
   {
    $whereClause1='';
    $hidden_fields.=tep_draw_hidden_field('keyword',$keyword);
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    if($word1=='Yes')
    {
     $hidden_fields.=tep_draw_hidden_field('word1',$word1);
     $explode_string=explode(' ',$keyword);
    }
    else
    {
     $explode_string=array('0'=>$keyword);
    }
    $whereClause1.='( ';
    for($i=0;$i<count($explode_string);$i++)
    {
     $whereClause1.=" jl.jobseeker_email_address like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" j.jobseeker_first_name like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" j.jobseeker_last_name like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" j.jobseeker_address1 like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" j.jobseeker_address2 like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" j.jobseeker_city like '%".tep_db_input($explode_string[$i])."%' or ";
     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.jobseeker_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) or ";
      tep_db_free_result($temp_result);
     }
     $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where country_name like '%".tep_db_input($explode_string[$i])."%'");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.jobseeker_country_id ='".$temp_row['id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) or ";
      tep_db_free_result($temp_result);
     }
     /*
     $temp_result=tep_db_query("select id from ".JOB_CATEGORY_TABLE." where category_name like '%".tep_db_input($explode_string[$i])."%'");
     if(tep_db_num_rows($temp_result) > 0)
     {
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $temp_array1[]= get_job_category_child($temp_row['id']);
      }
      $search_category11=implode(',',$temp_array1);
      $search_category2=explode(',',$search_category11);
      $final_search_category2= array_unique($search_category2);
      sort($final_search_category2);
      $whereClause1.=" (  ";
      for($i=0;$i<count($final_search_category2);$i++)
      {
        $whereClause1.=" jr1.job_category like '".tep_db_input($final_search_category2[$i]).",%' or ";
        $whereClause1.=" jr1.job_category like '%,".tep_db_input($final_search_category2[$i])."' or ";
        $whereClause1.=" jr1.job_category like '%,".tep_db_input($final_search_category2[$i]).",%' or ";
        $whereClause1.=" jr1.job_category ='".tep_db_input($final_search_category2[$i])."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) or ";
      tep_db_free_result($temp_result);
     }*/
     ////////////////*
     /*
     $temp_result=tep_db_query("select jr1.resume_id,jr1.jobseeker_resume from ".JOBSEEKER_RESUME1_TABLE." as jr1  ");
     if(tep_db_num_rows($temp_result) > 0)
     {
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $resume_id=$temp_row["resume_id"];
       $file_type=substr($temp_row['jobseeker_resume'],-3,3);
       if($file_type=='txt' || $file_type=='doc' ||$file_type=='pdf')
       {
        $resume_directory=get_file_directory($temp_row['jobseeker_resume'],6);
        $lines = @file(PATH_TO_MAIN_PHYSICAL.'resume/'.$resume_directory.'/'.$temp_row['jobseeker_resume']);
        if(count($lines) > 1)
        {
         foreach ($lines as $line_num => $line)
         {
          if(preg_match ("/$keyword/i", $line))
          {
           $whereClause1.=" jr1.resume_id='$resume_id' or ";
           break;
          }
         }
        }
       }//else echo  $file_type; exit;
      }
      tep_db_free_result($temp_result);
     }//*/
    }
    if($word1=='Yes')
    {
     $whereClause1.="  (MATCH(jr2.description) AGAINST ('".tep_db_input($keyword)."')) ";
     $whereClause1.=" or (MATCH(jr3.related_info) AGAINST ('".tep_db_input($keyword)."')) or ";
    }
    else
     $whereClause1.="  (jr2.description like '%".tep_db_input($keyword)."%' or jr3.related_info like '%".tep_db_input($keyword)."%') or ";


    $whereClause1=substr($whereClause1,0,-4);
    $whereClause1.=" ) ";
    $whereClause.=$whereClause1;
   }
   ///   keyword ends //////
   // first name starts ///
   if(tep_not_null($first_name))
   {
    $hidden_fields.=tep_draw_hidden_field('first_name',$first_name);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_first_name like '%".tep_db_input($first_name)."%' ) ";
   }
   // first name ends ///
   // last name starts ///
   if(tep_not_null($last_name))
   {
    $hidden_fields.=tep_draw_hidden_field('last_name',$last_name);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_last_name like '%".tep_db_input($last_name)."%' ) ";
   }
   //last name ends ///
   // email_address starts ///
   if(tep_not_null($email_address))
   {
    $hidden_fields.=tep_draw_hidden_field('email_address',$email_address);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( jl.jobseeker_email_address like '%".tep_db_input($email_address)."%' ) ";
   }
   // email_address ends ///
   // country starts ///
   if((int)$country>0)
   {
    $hidden_fields.=tep_draw_hidden_field('country',$country);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_country_id ='".(int)tep_db_input($country)."' ) ";
   }
   // country ends ///
   // state starts ///
   if(tep_not_null($state))
   {
    $hidden_fields.=tep_draw_hidden_field('state',$state);
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (zone_name like '%" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':'(');
    $whereClause.=" ( j.jobseeker_state like '%".tep_db_input($state)."%' ) ";
    if(tep_db_num_rows($temp_result) > 0)
    {
     $whereClause.=' or ( ';
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause.=" j.jobseeker_state_id ='".$temp_row['zone_id']."' or ";
     }
     $whereClause=substr($whereClause,0,-4);
     $whereClause.=" )";
     tep_db_free_result($temp_result);
    }
    $whereClause.=" )";
   }
   // state ends ///
   // city starts ///
   if(tep_not_null($city))
   {
    $hidden_fields.=tep_draw_hidden_field('city',$city);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_city like '%".tep_db_input($city)."%' ) ";
   }
   //city ends ///
   // zip starts ///
   if(tep_not_null($zip))
   {
    $hidden_fields.=tep_draw_hidden_field('zip',$zip);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.jobseeker_zip like '%".tep_db_input($zip)."%' ) ";
   }
   //zip ends ///
   // industry sector starts ///
   if(tep_not_null($industry_sector))
   {
    if($industry_sector['0']!='0')
    {
     $industry_sector1=remove_child_job_category($industry_sector1);
     $industry_sector=explode(',',$industry_sector1);
     $count_industry_sector=count($industry_sector);
     for($i=0;$i<$count_industry_sector;$i++)
     {
      $hidden_fields.=tep_draw_hidden_field('industry_sector[]',$industry_sector[$i]);
     }
     $search_category1 =get_search_job_category($industry_sector1);
     $whereClause_job_category=" select distinct (jr.resume_id) from ".JOBSEEKER_RESUME1_TABLE."  as jr1  left join ".RESUME_JOB_CATEGORY_TABLE." as jr on(jr1.resume_id=jr.resume_id ) where jr1.search_status='Yes' and  jr.job_category_id in (".$search_category1.")";
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and jr1.resume_id in ( ':' jr1.resume_id in ( ');
     $whereClause.=$whereClause_job_category;
     $whereClause.=" ) ";
    }
   }
   // industry sector ends ///
   // work experience start ///
   if(tep_not_null($experience))
   {
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $work_experince=get_name_from_table(EXPERIENCE_TABLE,'id', 'min_experience',tep_db_input($explode_string[0]));
    $whereClause.=" ( jr1.work_experince = '".(int)tep_db_input($work_experince)."' ) ";
   }
   if(tep_not_null($minimum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('minimum_rating',$minimum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" jrr.point >= '".tep_db_input($minimum_rating)."'";
   }
   if(tep_not_null($maximum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('maximum_rating',$maximum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   if(!tep_not_null($experience))
    $whereClause.=" (jrr.point is null or jrr.point <= '".tep_db_input($maximum_rating)."')";
			else
    $whereClause.=" jrr.point <= '".tep_db_input($maximum_rating)."'";
   }

   // work experience ends ///

   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////

   $now=date('Y-m-d H:i:s');


   $field_names="jl.jobseeker_id,jr1.resume_id,jl.inserted,jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,j.jobseeker_privacy,jr1.availability_date,jr1.resume_title,jrr.point";
   if(tep_not_null($keyword))
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl left join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id)   left join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left  join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left  join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id) ";
   else
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl left join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id)   left join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id)";
   if(tep_not_null($minimum_rating) || tep_not_null($maximum_rating))
   $table_names1.="  left join  ".JOBSEEKER_RATING_TABLE." as jrr on  (jr1.resume_id=jrr.resume_id and jrr.admin_rate='Y') ";
   $whereClause.="  jr1.search_status='Yes'";
   $query2 = "select distinct(jr1.resume_id) from $table_names1 where $whereClause ";
   $whereClause=" jr1.resume_id in (".$query2.")";

   $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id)  ";
   $table_names.="  left join  ".JOBSEEKER_RATING_TABLE." as jrr on  (jr1.resume_id=jrr.resume_id and jrr.admin_rate='Y') ";

   $query1 = "select count(jl.jobseeker_id) as x1 from $table_names where $whereClause ";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;//exit;
   //////////////////
   ///only for sorting starts
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
   $sort_array=array("jobseeker_name",'jl.jobseeker_email_address','jrr.point');
   $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.inserted desc, j.jobseeker_last_name asc');
   $order_by_clause=$obj_sort_by_clause->return_value;
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.inserted desc, j.jobseeker_last_name',$order,'asc',$lower,'0',$higher,'50');
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);
   $template->assign_vars(array('INFO_TEXT_JOBSEEKER_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
//                              'INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>"
                                'TABLE_HEADING_RATING'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_RATING.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
   ));
   ///only for sorting ends

   $totalpage=ceil($x1/$higher);
   $query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher";
   $result=tep_db_query($query);
   //echo "<br>$query";//exit;
   $x=tep_db_num_rows($result);
   //echo $x;exit;
   $pno= ceil($lower+$higher)/($higher);
   if($x > 0 && $x1 > 0)
   {
    $alternate=1;
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["resume_id"];
     $query_string1=encode_string("search_id==".$ide."==search");
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
     /*/ $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1) . '\'"';
     list($year,$month,$date)=explode("-",$row['availability_date']);
     $now=date("Y-m-d");
     $available_status=false;
     if(checkdate((int)$month,(int)$date,(int)$year))
     {
      if(date("Y-m-d",mktime(0,0,0,$month,($date+7),$year))>=$now)
      {
       $available_status=true;
      }
     }
     */
     if(tep_not_null($row['availability_date']))
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 20);
     }
     else
     {
      $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 30, 20);
     }
     $template->assign_block_vars('search_resume_result', array(
      'name' => tep_db_output($row['jobseeker_name']),
      'rating' => tep_db_output($row['point']),
      'email_address' =>($row['jobseeker_privacy']==3?tep_db_output($row['jobseeker_email_address']):'*****'),//tep_db_output($row['jobseeker_email_address']),
      'resume_title' => tep_db_output($row['resume_title']),
      'inserted' => tep_date_long($row['inserted']),
      'view' => '<a  target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'"><u>view</u></a>',
      'available_status'=>$available_status,
      'row_selected'=>$row_selected,
      ));
     $alternate++;
     $lower = $lower + 1;
    }
    $plural=($x1=="1")?"Resume":"Resumes";
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." has matched <font color='red'><b>$x1</b></font> ".$plural." to your search criteria."));
   }
   else
   {
    $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." has not matched any resume to your search criteria."));
   }
   see_page_number();
   tep_db_free_result($result1);
  }
}
$search_resume_text='<span class="small_red"><ul><li style="text-align:justify">If your search results do not yield qualified candidates that suit your needs - please search in surrounding states or nationwide - many candidates are willing to move if you show interest in them.</li></ul></span>';

if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='205';
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
$hidden_fields.=tep_draw_hidden_field('email');
$hidden_fields.=tep_draw_hidden_field('preview');
$hidden_fields.=tep_draw_hidden_field('excel');

if($action=='')
{
	$minimum_rating_array=array();
	$minimum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
	for($i=1;$i<=10;$i++)
		$minimum_rating_array[]=array("id"=>$i,"text"=>$i);
	$minimum_rating_string.=tep_draw_pull_down_menu('minimum_rating', $minimum_rating_array, $minimum_rating, 'class=form-control form-control-sm', false);

	$maximum_rating_array=array();
	$maximum_rating_array[0]=array("id"=>'',"text"=>INFO_TEXT_ALL);
	for($j=1;$j<=10;$j++)
		$maximum_rating_array[]=array("id"=>$j,"text"=>$j);
	$maximum_rating_string.=tep_draw_pull_down_menu('maximum_rating', $maximum_rating_array, $maximum_rating, 'class=form-control form-control-sm', false);

 $template->assign_vars(array( 'INFO_TEXT_KEYWORD'       => INFO_TEXT_KEYWORD,
 'INFO_TEXT_KEYWORD1'      => tep_draw_input_field('keyword', $keyword,'size="50" class="form-control form-control-sm" placeholder="'.INFO_TEXT_KEYWORD_EXAMPLE.'" ',false),
 'INFO_TEXT_KEYWORD3'      => '<span class="mr-1">'.INFO_TEXT_KEYWORD_CRITERIA.'</span><div class="form-check form-check-inline">'.tep_draw_radio_field('word1', 'Yes', 'Yes', $word1,'id="radio_word1" class="form-check-input"').'<label for="radio_word1" class="form-check-label">'.INFO_TEXT_KEYWORD_WORD1.'</label></div><div class="form-check form-check-inline">'.tep_draw_radio_field('word1', 'No', '', $word1,'id="radio_word2" class="form-check-input"').'<label for="radio_word2" class="form-check-label">'.INFO_TEXT_KEYWORD_WORD2.'</label></div>',
 'INFO_TEXT_FIRST_NAME'    => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1'   => tep_draw_input_field('first_name', $first_name,'size="30" class="form-control form-control-sm"',false),
 'INFO_TEXT_LAST_NAME'     => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'    => tep_draw_input_field('last_name', $last_name,'size="30" class="form-control form-control-sm"',false),
 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('email_address', $email_address,'size="30" class="form-control form-control-sm"',false),
 'INFO_TEXT_COUNTRY'       => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'      => LIST_TABLE(COUNTRIES_TABLE,"country_name"," priority ,country_name","name='country' class='form-control form-control-sm' ","All countries","",$country),
 'INFO_TEXT_STATE'         => INFO_TEXT_STATE,
 'INFO_TEXT_STATE1'        => tep_draw_input_field('state1', $state,'size="25" class="form-control form-control-sm"',false),
 //'INFO_TEXT_STATE1'        => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_name',"zone_name",'name="state"',"state",'',$state)." ".tep_draw_input_field('state1', $state,'size="25"',false),
 'INFO_TEXT_CITY'          => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'         => tep_draw_input_field('city', $city,'size="30" class="form-control form-control-sm"',false),
 'INFO_TEXT_ZIP'           => INFO_TEXT_ZIP,
 'INFO_TEXT_ZIP1'          => tep_draw_input_field('zip', $zip,'size="30" class="form-control form-control-sm"',false),
 'INFO_TEXT_JOB_CATEGORY'  => INFO_TEXT_JOB_CATEGORY,
 'INFO_TEXT_JOB_CATEGORY1' => get_drop_down_list(JOB_CATEGORY_TABLE,"name='industry_sector[]' class='form-control form-control-sm' size='6' multiple","All Job Categorys","0",$industry_sector1),
 'INFO_TEXT_EXPERIENCE'    => INFO_TEXT_EXPERIENCE,
 'INFO_TEXT_EXPERIENCE1'   => experience_drop_down('name="experience" class="form-control form-control-sm"', 'Any experience', '', $experience),
	'INFO_TEXT_MINIMUM_RATING'=> INFO_TEXT_MINIMUM_RATING,
 'INFO_TEXT_MINIMUM_RATING1'=> $minimum_rating_string,

 'INFO_TEXT_MAXIMUM_RATING'=> INFO_TEXT_MAXIMUM_RATING,
 'INFO_TEXT_MAXIMUM_RATING1'=> $maximum_rating_string,
 ));

}
$template->assign_vars(array( 'hidden_fields' => $hidden_fields,
 'HEADING_TITLE'           =>HEADING_TITLE,
 'button'                  => tep_button_submit('btn btn-primary', IMAGE_SEARCH),
 'form'                    => tep_draw_form('search', PATH_TO_ADMIN.FILENAME_ADMIN1_RATE_RESUMES,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
 'save_search'             => tep_draw_form('save_search', FILENAME_ADMIN1_RATE_RESUMES,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','save_search'),
// 'save_button'             => tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).($action1=='save_search'?'&nbsp;'.'<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_RESUME_SEARCH_AGENTS).'">'.tep_image(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>':''),
 'TABLE_HEADING_EMAIL_ADDRESS'=>TABLE_HEADING_EMAIL_ADDRESS,
 'TABLE_HEADING_RESUME_TITLE'=>TABLE_HEADING_RESUME_TITLE,
 'TABLE_HEADING_INSERTED'  => TABLE_HEADING_INSERTED,
 'TABLE_HEADING_RESUME'    => TABLE_HEADING_RESUME,


 //'SCRIPT'                  => country_state($c_name='country',$c_d_value='All countries',$s_name='state',$s_d_value='state','zone_name',$state),
 'INFO_TEXT_SEARCH_RESUME_TEXT'=>$search_resume_text,
 'TABLE_HEADING_AVAILABILITY'=>TABLE_HEADING_AVAILABILITY,
 'hidden_fields' => $hidden_fields,
 'RIGHT_BOX_WIDTH' => $RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => $RIGHT_HTML,
 'update_message' => $messageStack->output(),
 ));
if($preview)
{
 $TR_email_address=$_POST['TR_email_address'];
 for($i=0;$i<count($TR_email_address);$i++)
  $hidden_fields.=tep_draw_hidden_field('email_address1[]',$TR_email_address[$i]);
 $TREF_to=implode(", ",$TR_email_address);
 if($TR_email_address['0']=='-1')
 {
  $TREF_to='All Jobseekers';
 }
 $TREF_from=$_POST['TREF_from'];
 $hidden_fields.=tep_draw_hidden_field('TREF_from',$TREF_from);
 $TR_subject=$_POST['TR_subject'];
 $hidden_fields.=tep_draw_hidden_field('TR_subject',$TR_subject);
 $TR_message=$_POST['TR_message'];
 $hidden_fields.=tep_draw_hidden_field('TR_message',stripslashes($TR_message));
 //////// file upload Attachment starts //////
 if(tep_not_null($_FILES['attachment']['name']))
 {
  if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT,'644',array('doc','pdf','txt','jpg','gif','png')))
  {
   $attachment_file_name=tep_db_input($obj_resume->filename);
  }
  else
  {
   $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
  }
 }
 //////// file upload ends //////
 ////////////////   Attachment ///////////////
 if($attachment_file_name!='')
 {
  $hidden_fields.=tep_draw_hidden_field('attachment',stripslashes($attachment_file_name));
 }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_db_output($TREF_to),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_db_output($TREF_from),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_db_output($TR_subject).(($attachment_file_name!='')?"<span class='small'> <br>Attachment : ".tep_db_output(substr($attachment_file_name,14))."</span>":''),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>stripslashes($TR_message),
  'buttons'=>'<a href="#" onclick="javascript: submitform();">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_send_mail.gif', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'form'=>tep_draw_form('preview_mail', PATH_TO_ADMIN.FILENAME_ADMIN1_RATE_RESUMES, '', 'post', 'onsubmit="return ValidateForm(this)"'),
  'hidden_fields'=>$hidden_fields
  ));
 $template->pparse('preview');
}
else if($email ||$email=='back')
{
 if($email=='back')
 {
  if($_POST['attachment']!='')
   if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']))
   @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$_POST['attachment']);
 }
 if(isset($_POST['TREF_from']))
 {
  $email_address1=$_POST['email_address1'];
  $TREF_from=$_POST['TREF_from'];
  $TR_subject=$_POST['TR_subject'];
  $TR_message=stripslashes($_POST['TR_message']);
 }
 else
 {
  $TREF_from=ADMIN_EMAIL;
 }
 $template->assign_vars(array(
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_draw_pull_down_menu('TR_email_address[]', $email_array, $email_address1, 'size="5" multiple', true),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_draw_input_field('TREF_from',$TREF_from, 'size="35"', true ),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'size="35"', true ),
  'MAIL_ATTACHMENT'=>MAIL_ATTACHMENT,
  'MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '80%', '10', $TR_message, '', true, true),
  'buttons'=>'<a href="#" onclick="page_submit(\'preview\');">'.tep_image(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL).'</a>',
  'form'=>tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_RATE_RESUMES, '', 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"')
  ));
 $template->pparse('email');
}
else if($action=='search')
{
 $template->pparse('search_result');
}
else
{
 $template->pparse('search');
}
?>