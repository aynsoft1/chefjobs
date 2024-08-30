<?php
include_once("../include_files.php");
include_once('facebook_body.php');
$template->set_filenames(array('job_search_result'=>'job_search_result.htm','job_search_result1'=>'job_search_result1.htm'));
$keyword  = tep_db_prepare_input($_POST['keyword']);
$location = tep_db_prepare_input($_POST['location']);
if(isset($_POST['page']))
{
 $page=tep_db_prepare_input($_POST['page']);
}
else
{
	$page=0;
}
 $action1='search';
 switch($action1)
 {
  case 'search':
   $whereClause='';
   if ((preg_match("/http:\/\//i",$keyword)))
   $keyword='';
   if(tep_not_null($keyword)  && (($keyword!='keyword')) ) //   keyword starts //////
   {
    if($_SESSION['sess_jobsearch']!='y')
    tag_key_check($keyword);
    $_SESSION['sess_jobsearch']='y';

    $whereClause1='(';
    $search = array ("'[\s]+'");                    
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    if($word1=='Yes')
    {
     $explode_string=explode(' ',$keyword);
		   $total_keys = count($explode_string);
					for($i=0;$i<$total_keys;$i++)
					{
		    if(strlen($explode_string[$i])< 3 or strtolower($explode_string[$i])=='and')
					 {
       unset($explode_string[$i]);
					 }
					}
					sort($explode_string);
     $whereClause1.='(';
		   $total_keys = count($explode_string);
     for($i=0;$i<$total_keys;$i++)
     {
      if($i>0)
      $whereClause1.='or ( ';
      $whereClause1.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" r.recruiter_company_name like '%".tep_db_input($explode_string[$i])."%' or ";

      $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($explode_string[$i])."%'");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
     }
					if($total_keys<=0)
					$whereClause1='';
    }
    else
    {
     $whereClause1.=" j.job_title like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_state like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_location like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_short_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or j.job_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or r.recruiter_company_name like '%".tep_db_input($keyword)."%'";

     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($keyword) . "%' or zone_code like '%" . tep_db_input($keyword) . "%')");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
     $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($keyword)."%'");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
    }
 			if($whereClause1!='')
    $whereClause1.=" ) ";   
    $whereClause.=$whereClause1;
   }
   // keyword ends //////
   //   location starts //////
   if(tep_not_null($location) && $location!='location') 
   {
    $whereClause1='(';
    $search = array ("'[\s]+'");                    
    $replace = array (" ");
    $location = preg_replace($search, $replace, $location);
    //if($word1=='Yes')
    //{
     $explode_string=explode(',',$location);
     $whereClause1.='( ';
     for($i=0;$i<count($explode_string);$i++)
     {
      if(!tep_not_null($explode_string[$i]))
      continue;
      if($i>0 &&  $explode_string[($i-1)]!='')
      $whereClause1.='or ( ';
      $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
      
      $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
     }
    //}
    $whereClause1.=" )";   
    if($whereClause1!="((  )")
    {
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
     $whereClause.=$whereClause1;
    }
   }
   // state ends ///
   
			
   // country ends ///
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id)';
   $whereClause.="   rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description,  j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name"; //j.job_state, j.job_state_id,j.job_country_id
   //$query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";

   //////////////////
			$query = "select $field_names from $table_names where $whereClause ORDER BY if(j.job_source ='jobsite',0,1)  asc, j.inserted desc, j.job_featured='Yes' desc";
			$recpage = 10;//MAX_DISPLAY_SEARCH_RESULTS;
			$obj = new pagination_class($query,$page,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius);		
			$result = $obj->result;
			$x=tep_db_num_rows($result);
			$content='';
			$count=1;
   if(mysql_num_rows($result)!=0)
   {
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["job_id"];
     $recruiter_logo='';
     $company_logo=$row['recruiter_logo'];
     $title_format=encode_category($row['job_title']);
     $query_string=encode_string("job_id=".$ide."=job_id");

					if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");

     if($row['job_featured']=='Yes')
					{
					 $row_selected='jobSearchRowFea';
					}
					else
					{
					 $row_selected=($count%2==1)?'jobSearchRow1':'jobSearchRow2';
						
						$count++;
					}
     
					$template->assign_block_vars('job_search_result', array( 
                                  'row_selected' => $row_selected,
                                  'job_id' => $ide,
                                  'job_title' => '<h2 class="job_search_title" id="j_'.$ide.'">'.tep_db_output($row['job_title']).'</h2>',
 						                           'company_name' =>tep_db_output($row['recruiter_company_name']),
                                  'job_url' => tep_href_link($ide.'/'.$title_format.'.html'),
 						                           'location' =>tep_db_output($row['location'].' '.$row['country_name']),
						                            'description' => nl2br(tep_db_output(strip_tags($row['job_short_description']))),
                                  'apply_before' => tep_date_long($row['expired']),
                                  'logo'      => $recruiter_logo,
						                            ));



     
     /////////////////////////////////////////////////////////
     if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$ide."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$ide."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
     }					
     /////////////////////////////////////////////////////////  
    }
			 $template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total));			
   }
   else
   {
    $template->assign_vars(array('content_hide'=>'result_hide','total'=>"Not matched any Job to your search criteria <br><br>&nbsp;&nbsp;&nbsp;"));
   }
  break;
 }

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
{
 echo $template->pparse1('job_search_result1');die();
}
else
{
 $template->pparse('job_search_result');
}
?>