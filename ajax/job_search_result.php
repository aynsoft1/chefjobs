<?
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #**********
**********# Company       : Aynsoft                 #**********
**********# Copyright (c) www.aynsoft.com 2004     #**********
***********************************************************
*/
include_once("../include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH);
if(isset($_GET['starting'])&& !isset($_REQUEST['submit']))
{
 $starting=$_GET['starting'];
}
else
{
	$starting=0;
}
//print_r($_GET);
$recpage = MAX_DISPLAY_SEARCH_RESULTS;
if(isset($_GET['keyword']) && (($_GET['keyword']!='keyword') && ($_GET['keyword']!='job search keywords')))
{
	$keyword=tep_db_prepare_input($_GET['keyword']);
}
if(isset($_GET['location']) && ($_GET['location']!='location'))
{
	$location=tep_db_prepare_input($_GET['location']);
}
if(isset($_GET['job_post_day']))
{
	$job_post_day=tep_db_prepare_input($_GET['job_post_day']);
}
if(isset($_GET['word1']))
{
	$word1=tep_db_prepare_input($_GET['word1']);
}
if(isset($_GET['country']))
{
	$country=(int)tep_db_prepare_input($_GET['country']);
}
if(tep_not_null($_GET['experience']))
{
 $experience=$_GET['experience'];
}
if(tep_not_null($_GET['job_category']))
{
 $job_category=$_GET['job_category'];
 $job_category1=implode(",",$job_category);
}
if(tep_not_null($_GET['company']))
{
 $company=tep_db_prepare_input($_GET['company']);
}
if(tep_not_null($_GET['inserted_date']))
{
 $inserted_date=tep_db_prepare_input($_GET['inserted_date']);
}
$zip_code       = tep_db_prepare_input($_GET['zip_code']);
$radius         = (int)tep_db_prepare_input($_GET['radius']);
$search_zip_code=1;
if(tep_not_null($zip_code))
$search_zip_code= 2;
if(tep_not_null($_GET['state']))
{
 if(is_array($_GET['state']))
  $state=implode(',',tep_db_prepare_input($_GET['state']));
 else
  $state=tep_db_prepare_input($_GET['state']);
 if($state[0]==',')
  $state=substr($state,1);
}
elseif(tep_not_null($_GET['state1']))
{
 $state=tep_db_prepare_input($_GET['state1']);
}
$whereClause='';
//   keyword starts //////
if ((preg_match("/http:\/\//i",$keyword)))
 $keyword='';
 if(tep_not_null($keyword)  && (($_GET['keyword']!='keyword') && ($_GET['keyword']!='job search keywords')) )
 {
  if($_SESSION['sess_jobsearch']!='y')
   tag_key_check($keyword);
  $_SESSION['sess_jobsearch']='y';
  $whereClause1='(';
  $hidden_fields1.=tep_draw_hidden_field('keyword',$keyword);
  $search = array ("'[\s]+'");
  $replace = array (" ");
  $keyword = preg_replace($search, $replace, $keyword);
  if($word1=='Yes')
  {
   $hidden_fields.=tep_draw_hidden_field('word1',$word1);
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
     //tep_db_free_result($temp_result);
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
  if(tep_not_null($location) && $_GET['location']!='location')
  {
   $whereClause1='(';
   $hidden_fields1.=tep_draw_hidden_field('location',$location);
   $search = array ("'[\s]+'");
   $replace = array (" ");
   $location = preg_replace($search, $replace, $location);
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
    $whereClause1.=" )";
    if($whereClause1!="((  )")
    {
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
     $whereClause.=$whereClause1;
    }
   }
   //   location ends //////
   // job_post_day starts //
   if(tep_not_null($_GET['job_post_day']))
   {
    $job_post_day=abs((int)($_GET['job_post_day']));
    $hidden_fields.=tep_draw_hidden_field('job_post_day',$job_post_day);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$job_post_day, date("Y")))."' ) ";
   }
   // job_post_day end //
   // inserted date starts //
   if(tep_not_null($_GET['inserted_date']))
   {
    $inserted_date=($_GET['inserted_date']);
    $hidden_fields.=tep_draw_hidden_field('inserted_date',$inserted_date);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv ='".$inserted_date."' ) ";
   }
   // inserted date end //
   // company starts //
   //*
   if(tep_not_null($_GET['company']))
   {
    $hidden_fields.=tep_draw_hidden_field('company',$company);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( r.recruiter_company_name ='".tep_db_input($company)."' )";
   }
   //*/// company ends ///
   // experience starts //
   //*
   if(tep_not_null($_GET['experience']))
   {
    $experience=$_GET['experience'];
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $whereClause.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
   }
   //*/// experience ends ///
   // industry job_category  starts ///
   if(tep_not_null($_GET['job_category']))
   {
    $job_category=$_GET['job_category'];
    if($job_category['0']!='0')
    {
     $job_category1=remove_child_job_category($job_category1);
     $job_category=explode(',',$job_category1);
     $count_job_category=count($job_category);
     for($i=0;$i<$count_job_category;$i++)
     {
      $hidden_fields.=tep_draw_hidden_field('job_category[]',$job_category[$i]);
     }
     $search_category1 =get_search_job_category($job_category1);
     $now=date('Y-m-d H:i:s');
     $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category1.")";
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and job_id in ( ':' job_id in ( ');
     $whereClause.=$whereClause_job_category;
     $whereClause.=" ) ";
    }
    else
    {
     $hidden_fields.=tep_draw_hidden_field('job_category[]','0');
    }
   }
   // industry job_category1 ends ///
   if($search_zip_code==2)
   {
    ////zip code ////////////
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $hidden_fields1.=tep_draw_hidden_field('zip_code',$zip_code);
    $hidden_fields1.=tep_draw_hidden_field('radius',$radius);
    $hidden_fields.=tep_draw_hidden_field('search_zip_code',2);
    if($row=getAnyTableWhereData(ZIP_CODE_TABLE," zip_code='".tep_db_input($zip_code)."'",'*'))
    {
     ////////////////////
     $today=date('Y-m-d');
     if($row_cache=getAnyTableWhereData(ZIP_CODE_SEARCH_TABLE," zip_code='".tep_db_input($zip_code)."' and  radius='".tep_db_input($radius)."'",'state'))
     {
      $state_array =explode(',',$row_cache['state']);
     }
     else
     {
      $state_array=array();
      $temp_state_result = tep_db_query("select distinct(state) as state from " . ZIP_CODE_TABLE. " where ( 3959 * acos( cos( radians( ".tep_db_input($row['latitude']).") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".tep_db_input($row['longitude']).") ) + sin( radians( ".tep_db_input($row['latitude']).") ) * sin( radians( latitude ) ) ) ) <=".tep_db_input($radius)."");
      if(tep_db_num_rows($temp_state_result) > 0)
      {
       while($temp_row = tep_db_fetch_array($temp_state_result))
       {
        $state_array[]=trim($temp_row['state']);
       }
       $state_zip=implode(',',$state_array);
       $sql_data_search_array=array('zip_code'=>$zip_code,
                        'radius'=>$radius,
                        'state'=>$state_zip,
                        'inserted'=>$today,
                       );
       tep_db_perform(ZIP_CODE_SEARCH_TABLE, $sql_data_search_array);
      }
      tep_db_free_result($temp_state_result);
     }
     $total_state=count($state_array);
     if($total_state>0)
     {
      $whereClause.='( ';
      for($i=0;$i<$total_state;$i++)
      {
       $search_state= $state_array[$i];
       if($row_state=getAnyTableWhereData(ZONES_TABLE," zone_id='".tep_db_input($search_state)."'",'zone_name'))
        $whereClause.="  ( j.job_state = '".tep_db_input($row_state['zone_name'])."' or  j.job_state_id = '".tep_db_input($search_state)."') or ";
       else
        $whereClause.="(j.job_state_id ='".tep_db_input($search_state)."') or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
     }
     else
     {
      $whereClause.=' 0 ';
     }
    }
	   else
    $whereClause.=' 0 ';
    ///////////////////
   }
	  // country starts ///
   if(tep_not_null($country) && $country > 0)
				{
					$whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
				 $whereClause.=" j.job_country_id ='".tep_db_input($country)."'";
 				$whereClause.="  )";
				}
   // country ends ///
   // state starts ///
   if(tep_not_null($state))
   {
    $state1=explode(',',$state);//print_r($state1);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
    for($i=0;$i<count($state1);$i++)
    {
     $hidden_fields.=tep_draw_hidden_field('state[]',$state1[$i]);
     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (zone_name like '%" . tep_db_input($state1[$i]) . "%' or zone_code like '%" . tep_db_input($state1[$i]) . "%')");
     $whereClause.="  ( j.job_state like '%".tep_db_input($state1[$i])."%' )  ";
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause.=' or ( ';
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
      tep_db_free_result($temp_result);
     }
     $whereClause.=" or ";
    }
    $whereClause=substr($whereClause,0,-4);
    $whereClause.="  )";

   }
			$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id)';
   $whereClause.="  rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description, j.job_description,   j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name";

 		$query = "select $field_names from $table_names where $whereClause ORDER BY j.inserted desc, if(j.job_source ='jobsite',0,1)  asc,  j.job_featured='Yes' desc";
 		$obj = new pagination_class($query,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius,HOST_NAME);
			$result1 = $obj->result;
			$content='';
   if(tep_db_num_rows($result1)!=0)
   {
				$count=1;
				$pages='<div class="card-footer card-footer-custom"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="80%">'.$obj->anchors.'</td>';
    $total='<td id="pagination-flickr" align="right">'.$obj->total.'</td></tr></table></div>';
				//echo $pages;
   // echo $total;
		  $content.='<table width="100%"  border="0" cellspacing="0" cellpadding="0">
														  <tr>
               	 <td>&nbsp;</td>
                </tr>';
    while($row = tep_db_fetch_array($result1))
    {
					$ide=$row["job_id"];
     $query_string=encode_string("job_id=".$ide."=job_id");

     $recruiter_logo='';
     $company_logo=$row['recruiter_logo'];
     $title_format=encode_category($row['job_title']);
     $company_name=$row['recruiter_company_name'];
			  $apply_before=tep_date_long($row['expired']);

     if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");

					$email_job    ='<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'" target="_blank">'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'</a>';
					$apply_job    ='<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'"  target="_blank">'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'</a>';
					if($row['job_salary']=='' || $row['job_salary']=='0')
	    {
	     $salary_class='result_hide';
	    }
	    else
	    {
	     $salary_class='';
	    }
					if($row['job_featured']=='Yes')
 			 $row_selected=' class="jobSearchRowFea"';
					else
					{
 					$row_selected='class="jobSearchRow1"';
 					$count++;
					}

  $content.='
	          <tr >
            <td '.$row_selected.' >
		           <table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
												   <td width="15" valign="top">'.(($row['post_url']=='Yes')?'':'<input type="checkbox" name="apply_job" value="'.$query_string.'">').'</td>
															<td>
																<table width="100%"  border="0" cellspacing="1" cellpadding="1">
																	<tr>
												      <td colspan="4"><a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)) .'" class="job_search_title"  target="_blank">'.tep_db_output($row['job_title']).'</a></td>
  															</tr>
												     <tr>
               	  <td class="job_search_content">'.INFO_TEXT_COMPANY_NAME.'</td>
               	  <td class="job_search_content1">:</td>
               	  <td class="job_search_content1">'.tep_db_output($company_name).'</td>
               	  <td rowspan="4" valign="top" align="right">'.$recruiter_logo.'</td>
               	 </tr>
												     <tr>
           	      <td width="15%" class="job_search_content">'.INFO_TEXT_LOCATION_NAME.'</td>
           	      <td class="job_search_content1" width="5">:</td>
           	      <td class="job_search_content1">'.tep_db_output($row['location'].' '.$row['country_name']).'</td>
           	     </tr>
												     <tr>
           	      <td class="job_search_content">'.INFO_TEXT_EXPERIENCE.'</td>
           	      <td class="job_search_content1" width="1">:</td>
           	      <td class="job_search_content1">'.tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])).'</td>
           	     </tr>
												     <tr class="'.$salary_class.'">
               	   <td class="job_search_content">'.INFO_TEXT_SALARY.'</td>
               	   <td class="job_search_content1" width="1">:</td>
               	   <td class="job_search_content1">'.tep_db_output($row['job_salary']).'</td>
               	 </tr>
												     <tr>
               	  <td colspan="4" class="job_search_content1-desc">'.nl2br(tep_db_output(strip_tags($row['job_description']))).'</td>
               	 </tr>
												     <tr>
               	  <td colspan="4">
												       <table width="100%" border="0">
												        <tr>
												         <td class="job_search_apply">'.$apply_job.'</td>
												         <td class="job_search_email" valign="top">'.$email_job.'</td>
												         <td class="job_search_content1" width="50%" align="right">'.tep_db_output(INFO_TEXT_APPLY_BEFORE).' '.$apply_before.'</td>
												        </tr>
												       </table>
												      </td>
												     </tr>
																</table>
															</td>
														</tr>
             </table>
												</td>
           </tr>
											<tr>
												<td>&nbsp;</td>
										 </tr>';
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
	  $curr_date =date('Y-m-d');
	 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".tep_db_input($ide)."' and  date='".tep_db_input($curr_date)."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".$ide."' and  date='".tep_db_input($curr_date)."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
		                    'date'=>$curr_date,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
     }
     /////////////////////////////////////////////////////////
    }

			  $content.='</table>';

     echo $content;
					echo $pages;
					echo $total;
   }
   else
   {
    //$template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
   }
  $template->assign_vars(array(
  'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
  'RIGHT_HTML' => RIGHT_HTML,
  'update_message' => $messageStack->output()));
?>