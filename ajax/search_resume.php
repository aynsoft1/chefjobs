<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft              #*********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
//ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_SEARCH_RESUME);
if(!check_login("recruiter"))
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
if(isset($_POST['action']))
{
 //print_r($_POST);die();
}
//print_r($_POST);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');


//print_r($_POST);
$state_error=false;
$map_view=tep_db_prepare_input($_POST['map_view']);
$show_page=tep_db_prepare_input($_POST['show_page']);
$form_name=tep_db_prepare_input($_POST['args_obj']);

if($form_name=='page')
{
 $_POST['lower']=($show_page-1)*MAX_DISPLAY_SEARCH_RESULTS ;
}
else
{
 $_POST['lower_1']=($show_page-1)*MAX_DISPLAY_SEARCH_RESULTS ;
}

// initialize 
if(tep_not_null($_POST['keyword']))
{
 $keyword=tep_db_prepare_input($_POST['keyword']);
}
if(tep_not_null($_POST['word1']))
{
 $word1=tep_db_prepare_input($_POST['word1']);
}
if(tep_not_null($_POST['minimum_rating']))
{
 $minimum_rating=tep_db_prepare_input($_POST['minimum_rating']);
}
if(tep_not_null($_POST['maximum_rating']))
{
 $maximum_rating=tep_db_prepare_input($_POST['maximum_rating']);
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
if(tep_not_null($_POST['nationality']))
{
 $nationality=(int)tep_db_prepare_input($_POST['nationality']);
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
if(tep_not_null($_POST['certifications']))
{
 $certifications=tep_db_prepare_input($_POST['certifications']);
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
   $lower_1=(int)tep_db_prepare_input($_POST['lower_1']);
   $higher_1=(int)tep_db_prepare_input($_POST['higher_1']);
   $whereClause='';
   
   if(tep_not_null($keyword) && strtolower($keyword)!='keyword') //   keyword starts //////
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
     $whereClause1.=" j.jobseeker_zip like '%".tep_db_input($explode_string[$i])."%' or ";
     $whereClause1.=" jr1.target_job_titles like '%".tep_db_input($explode_string[$i])."%' or ";
     //$whereClause1.=" jr2.description  like '%".tep_db_input($explode_string[$i])."%' or ";
     //$whereClause1.=" jr3.related_info like '%".tep_db_input($explode_string[$i])."%' or ";
     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
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
     $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($explode_string[$i])."%'");
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
   // minimum rating starts ///
   if(tep_not_null($minimum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('minimum_rating',$minimum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" (( jrt1.point >= '".tep_db_input($minimum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$_SESSION['sess_recruiterid']."'  and  jrt.point >= '".tep_db_input($minimum_rating)."')) ";
   }
   // minimum rating ends ///
   // maximum rating starts ///
   if(tep_not_null($maximum_rating))
   {
    $hidden_fields.=tep_draw_hidden_field('maximum_rating',$maximum_rating);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" (( jrt1.point <= '".tep_db_input($maximum_rating)."' and jrt1.admin_rate = 'Y' )or (jrt.recruiter_id='".$_SESSION['sess_recruiterid']."'  and  jrt.point <= '".tep_db_input($maximum_rating)."')) ";
   }
   // maximum rating ends ///
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
      // nationality starts ///
   if((int)$nationality>0)
   {
    $hidden_fields.=tep_draw_hidden_field('nationality',$nationality);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( jr1.jobseeker_nationality ='".(int)tep_db_input($nationality)."' ) ";
   }
   // nationality ends ///

   // state starts ///
   if(tep_not_null($state))
   {
    $hidden_fields.=tep_draw_hidden_field('state',$state);
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");
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
			// certifications starts ///
   if(tep_not_null($certifications))
   {
    $hidden_fields.=tep_draw_hidden_field('certifications',$certifications);
    $whereClause_1=" select distinct (jr1.resume_id) from ".JOBSEEKER_RESUME1_TABLE."  as jr1  left join ".LISCENCE_CERTIFICATION_TABLE." as jl on(jr1.resume_id=jl.resume_id ) where jr1.search_status='Yes' and  jl.title in ('".tep_db_input($certifications)."')"; 
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and jr1.resume_id in ( ':' jr1.resume_id in ( ');
    $whereClause.=$whereClause_1;
    $whereClause.=" ) ";
   }
   // certifications ends ///

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
   // work experience ends ///

   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   //$premium_jobseekers_query="select distinct(jah.jobseeker_id) from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." as jah where jah.start_date<=CURDATE() and jah.end_date >=CURDATE()";
   $now=date('Y-m-d H:i:s');
   $field_names="jl.jobseeker_id,jr1.resume_id,jr1.inserted,jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,j.jobseeker_privacy,jr1.availability_date,jr1.jobseeker_nationality,jrt.private_notes, jrt1.point as point1 ,jrt.point as  point,jrt.admin_rate,jr1.jobseeker_photo,j.jobseeker_city,if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,jobseeker_state) as jobseeker_state,c.country_name as jobseeker_country ";
   if(tep_not_null($keyword))
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RESUME2_TABLE." as jr2 on (jr1.resume_id=jr2.resume_id) left join ".JOBSEEKER_RESUME3_TABLE." as jr3 on (jr1.resume_id=jr3.resume_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   else
    $table_names1=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left  join ".JOBSEEKER_RESUME1_TABLE." as jr1 on (j.jobseeker_id=jr1.jobseeker_id)  left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y')  ";
   $whereClause.="  jr1.search_status='Yes' and jl.jobseeker_status='Yes'";

   if($form_name=='page')
   {    
				if($map_view==1)
				{
    $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' ) left outer join  ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) left outer join  ".COUNTRIES_TABLE." as c on (c.id=j.jobseeker_country_id) left outer join ".COUNTRIES_TABLE." as n on (n.id=jr1.jobseeker_nationality)"  ;
    $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause ";
    // echo "<br>$query1";//exit;
    $result1=tep_db_query($query1);
    $tt_row=tep_db_fetch_array($result1);
    $x1=$tt_row['x1'];
    //echo $x1;//exit;
    //////////////////
    ///only for sorting starts
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
	  	$sort='';
			 if(isset($_POST['sort']))
    $sort=tep_db_prepare_input($_POST['sort']);
    $sort_array=array("jobseeker_name",'jl.jobseeker_email_address');
    $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc, j.jobseeker_last_name asc , jr1.inserted desc');
    $order_by_clause=$obj_sort_by_clause->return_value;
    $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_SEARCH_RESULTS);
    $lower=$see_before_page_number_array['lower'];
    $higher=$see_before_page_number_array['higher'];
    $field=$see_before_page_number_array['field'];
    $order=$see_before_page_number_array['order'];
    $hidden_fields.=tep_draw_hidden_field('sort',$sort);

    ///only for sorting ends

    $totalpage=ceil($x1/$higher);

    $query = "select jl.jobseeker_id,jr1.resume_id,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,j.latitude,j.longitude,j.jobseeker_city,if(j.jobseeker_state_id,z.zone_name,jobseeker_state) as jobseeker_state,c.country_name as jobseeker_country from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher";
    $result=tep_db_query($query);
    //echo "<br>$query";//exit;
    $x=tep_db_num_rows($result);
    //echo $x;exit;
    $pno= ceil($lower+$higher)/($higher);
    if($x > 0 && $x1 > 0)
    {
     $alternate=1;
					$search_string='';
     while($row = tep_db_fetch_array($result))
     {
      $ide=$row["resume_id"];
      $query_string1=encode_string("search_id==".$ide."==search");
						$jobseeker_loction = tep_db_output($row['jobseeker_city']).(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']);
						$data_array[]=array('resume_id'=>$ide,
	                         'location_id'=>encode_string($row['latitude'].$row['longitude']),
                          'jobseeker_name'=>$row['jobseeker_name'],
                          'jobseeker_location'=>$jobseeker_loction,
                          'jobseeker_latitude'=>$row['latitude'],
                          'jobseeker_longitude'=>$row['longitude'],
                          'resume_link'=>tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1),
                         );   
						
      
     }
     $plural=($x1=="1")?TABLE_HEADING_RESUME:TABLE_HEADING_RESUMES;
     $paging_array= array('total'=>tep_db_output(SITE_TITLE).'&nbsp;'.INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH,'rate_mark'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span><span style="color:blue">*</span><span class="resume_result3">'.INFO_TEXT_RATED_SELF.'</span>');     
     ////////////////// map
   		 {
		     $data_map_array=array();
			    foreach($data_array as $key => $value)
							{
					 	 $location_id=$value['location_id'];
     			if(isset($data_map_array[$location_id]))
        {
         $data_map_array[$location_id][]=array('resume_id'=>$value['resume_id'],
                                               'jobseeker_name'=>$value['jobseeker_name'],
                                               'resume_link'=>$value['resume_link'],
                                               'jobseeker_latitude'=>$value['jobseeker_latitude'],
                                               'jobseeker_longitude'=>$value['jobseeker_longitude'],
                                               'jobseeker_location'=>$value['jobseeker_location'],
                                              );
        }
        else
        {
         $data_map_array[$location_id][0]=array('resume_id'=>$value['resume_id'],
                                               'jobseeker_name'=>$value['jobseeker_name'],
                                               'resume_link'=>$value['resume_link'],
                                               'jobseeker_latitude'=>$value['jobseeker_latitude'],
                                               'jobseeker_longitude'=>$value['jobseeker_longitude'],
                                               'jobseeker_location'=>$value['jobseeker_location'],
                                              );
        }
							}
							unset($data_array);
	  
							$map_java_script='';
							$content='<div id="map-canvas" > </div>';
       $div_content='';
       foreach($data_map_array as $key => $value)
       {
        $div_id=trim('jobseeker_detail_'.$key);
        $marker_location=$value[0]['jobseeker_location'];
        if($map_java_script=='')
        {//echo $value[0]['job_latitude'] .$value[0]['job_longitude'];
           $map_java_script='function initialize() 
                          {
                           var mapDiv = document.getElementById(\'map-canvas\');
                           map = new google.maps.Map(mapDiv, {center: new google.maps.LatLng('.$value[0]['jobseeker_latitude'].', '.$value[0]['jobseeker_longitude'].'),zoom:5,mapTypeId: google.maps.MapTypeId.ROADMAP});
                           infoWindow = new google.maps.InfoWindow();
                           google.maps.event.addListenerOnce(map, \'tilesloaded\', addMarkers);
                           }'."\n";
        }

        $map_java_script.='setMarkers('.$value[0]['jobseeker_latitude'].','.$value[0]['jobseeker_longitude'].',\''.($marker_location).'\',\''.$div_id.'\')'."\n";
        $div_content='<b>'.$marker_location.'</b>';
        $div_content.='<ul class="map_result">';
        foreach($value as $key1 => $job_detail)
        {
         $div_content.='<li><a href="'.$job_detail['resume_link'].'" target="_blank"><nobr>'.tep_db_output($job_detail['jobseeker_name']).'</nobr></a></li>';
        }
        $div_content.='</ul>';
        $content.='<div id="'.$div_id.'" style="display:none">'.$div_content.'</div>';
       }
							echo '
													
				<script language="javascript">
																		<!--
					var Markers=Array();

																		'.$map_java_script.'
																	//-->
																	</script>';
							//echo $content;
						}

    } 	 





    $list_string=see_page_number2();
				//print_r($list_string);
		  tep_db_free_result($result1);
  	////////////// ajax 
			$ajax_content='<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                   <tr>
																				<td>					   
																				 <table width="100%"  border="0" cellspacing="0" cellpadding="0">                        
																				  <tr>
																				   <td height="25" >'.$paging_array['total'].'</td>
																				   <td height="25" ><div align="right">'.$list_string['list_page'].' &nbsp; '.$list_string['page'].'&nbsp;&nbsp;&nbsp;</div></td>
																				  </tr>
																				  <tr>
																				   <td  colspan="2"><img src="img/spacer.gif" width="5" height="5"></td>
																				  </tr>
																				  <tr>
																				   <td height="25"  valign="top" ></td>
																				   <td align="right">'.$list_string['map_view_link'].'</td>
																				  </tr>
																				 </table> 
																				</td>
                   </tr>
																			<tr>
																				<td>
																				 
																				   ';
			$ajax_content.=$content.$hidden_fields;
			$ajax_content.='     
                      
																				 
                    </td>
                   </tr>
																			
                  </table>';
    echo $ajax_content;die();
			 }
				else
				{
    $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' ) left outer join  ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) left outer join  ".COUNTRIES_TABLE." as c on (c.id=j.jobseeker_country_id) left outer join ".COUNTRIES_TABLE." as n on (n.id=jr1.jobseeker_nationality)"  ;
    $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause ";
    // echo "<br>$query1";//exit;
    $result1=tep_db_query($query1);
    $tt_row=tep_db_fetch_array($result1);
    $x1=$tt_row['x1'];
    //echo $x1;//exit;
    //////////////////
    ///only for sorting starts
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
	  	$sort='';
			 if(isset($_POST['sort']))
    $sort=tep_db_prepare_input($_POST['sort']);
    $sort_array=array("jobseeker_name",'jl.jobseeker_email_address');
    $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc, j.jobseeker_last_name asc , jr1.inserted desc');
    $order_by_clause=$obj_sort_by_clause->return_value;
    $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order,'asc',$lower,'0',$higher,MAX_DISPLAY_SEARCH_RESULTS);
    $lower=$see_before_page_number_array['lower'];
    $higher=$see_before_page_number_array['higher'];
    $field=$see_before_page_number_array['field'];
    $order=$see_before_page_number_array['order'];
    $hidden_fields.=tep_draw_hidden_field('sort',$sort);

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
					$search_string='';
     while($row = tep_db_fetch_array($result))
     {
      $ide=$row["resume_id"];
      $query_string1=encode_string("search_id==".$ide."==search");
      $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
					 $jobseeker_education_check=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE.' as e left outer join '.EDUCATION_LEVEL_TABLE.' as el on (e.degree=el.id) ',"resume_id='".$ide."' order by start_year desc ,start_month desc","e.specialization,el.".TEXT_LANGUAGE."education_level_name as education_level_name");
					 $experience_row=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE.' as ex ',"resume_id='".$ide."' order by start_year desc ,start_month desc","ex.company,ex.job_title");

					 if(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo']))
      {
       $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$row['jobseeker_photo'].'&size=60','','','');
      }
					 else
					 {
       $photo ='';
					 }
      if(tep_not_null($row['availability_date'])) 
      {
       $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 10, 10);
      } 
      else 
      {
       $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 10, 10);
      }
					 $div_extended_values='
																								<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
																									<tr bgcolor="#FFFFFF">
																										<td valign="top" >'.$photo.'</td>
																										<td valign="top" width="2"></td>
																										<td valign="top" width="95%">
																										 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_name']).'</td>
																												</tr>'.($row['jobseeker_privacy']==3?'
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_city']).(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']).'</td>
																												</tr>':'').((!$jobseeker_education_check)?'':'
																												<tr>    
                             <td valign="top">'.tep_db_output($jobseeker_education_check['education_level_name']).(tep_not_null($jobseeker_education_check['specialization'])?' ('.tep_db_output($jobseeker_education_check['specialization']).')':'').'</td>
			                         </tr>').((!$experience_row)?'':' 
																												<tr>    
                             <td valign="top">'.(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :'').'</td>
			                         </tr>').' 
																											</table>
																										</td>
																									</tr>
																								</table>
                     					';
      $rating = (tep_not_null($row['point'])?tep_db_output($row['point']).'<span style="color:blue"> *</span>,':'').(tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':'');

      $search_string.='<tr '.$row_selected.'>
						                 <td><div align="center">'.tep_draw_checkbox_field('resume_id[]',$ide,false).'</div></td>
																						 <td class="resume_result2">'.tep_db_output($row['jobseeker_name']).'</td>
																						 <td class="resume_result2">'.ucfirst(tep_db_output($row['jobseeker_city'])).'</td>
																						 <td class="resume_result3" align="center"><a target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'" class="resume_result3">'.INFO_TEXT_VIEW.'</a></td>
																							<td><div align="center"><a target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=download').'"><img src="img/html_icon.jpeg" width="20" height="20"></a></div></td>
																							<td><div align="center">'.$rating.'</div></td>
																							<td><div align="center">'.$available_status.'</div></td>
																							<td align="center"><a href="#normal_resume" onclick="collapse(\'normal_'.$ide.'\')"><img src="img/plus.gif" width="9" height="9"></a></td>
																					 </tr>
																						<tr> 
																							<td colspan="8">
																								<div id="normal_'.$ide.'" style="display:none;">
																								<table width="100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
																								 <tr bgcolor="#FFFFFF">
																								  <td valign="top">
																								   '.$div_extended_values.'
																							   </td>
																							  </tr>
																						  </table>
																						 	</div>
																							</td>
																						</tr>';
     
      $alternate++;
      $lower = $lower + 1;
     }
     $plural=($x1=="1")?TABLE_HEADING_RESUME:TABLE_HEADING_RESUMES;
				 $check_link = '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#normal_resume" class="resume_result3" onclick="checkall()">'.tep_db_output(INFO_TEXT_SELECT_ALL).'</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="#normal_resume" class="resume_result3" onclick="uncheckall()">'.tep_db_output(INFO_TEXT_CLEAR_SELECTED).'</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="#normal_resume" class="resume_result3" onclick="SaveSelected(\'save\')">'.tep_db_output(INFO_TEXT_SAVE_SELECTED).'</a>';
				 ($x > 0?$check_uncheck=$check_link:$check_uncheck='');
     $paging_array= array('total'=>tep_db_output(SITE_TITLE).'&nbsp;'.INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH,'rate_mark'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span><span style="color:blue">*</span><span class="resume_result3">'.INFO_TEXT_RATED_SELF.'</span>',
                          'result_class' =>'',
					                     'check_link'=>$check_uncheck);     
    }
    else
    {
     $paging_array= array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED,
                          'result_class' =>'class="result_hide"',
                    						'check_link'=>'');
    }
    $list_string=see_page_number2();
				//print_r($list_string);
		  tep_db_free_result($result1);
  	////////////// ajax 
			$ajax_content='<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                   <tr>
																				<td>					   
																				 <table width="100%"  border="0" cellspacing="0" cellpadding="0">                        
																				  <tr>
																				   <td height="25" >'.$paging_array['total'].'</td>
																				   <td height="25" ><div align="right">'.$list_string['list_page'].' &nbsp; '.$list_string['page'].'&nbsp;&nbsp;&nbsp;</div></td>
																				  </tr>
																				  <tr>
																				   <td  colspan="2"><img src="img/spacer.gif" width="5" height="5"></td>
																				  </tr>
																				  <tr>
																				   <td height="25"  valign="top" ><div align="left">'.$paging_array['check_link'].'</div></td>
																				   <td align="right">'.$list_string['map_view_link'].'</td>
																				  </tr>
																				 </table> 
																				</td>
                   </tr>
																			<tr>
																				<td>
																				 <div id="general_search_result" '.$paging_array['result_class'].'> 
																				  <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#CFCDCC">
																				   <tr bgcolor="#72900c">
																				    <td width="6%"  background="img/resume1.gif" height="35" background="img/resume1.gif"></td>
																				    <td width="40%" background="img/resume1.gif" class="resume_result1"><a href="#"  onclick="submit_thispage(\''.$obj_sort_by_clause->return_sort_array['name'][0].'\','.$lower.');"><u>'.INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array["image"][0].'</a></td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_CITY.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1" align="center">'.TABLE_HEADING_RESUME.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1" align="center">'.TABLE_HEADING_DOWNLOAD.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_RATING.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_AVAILABILITY.'</td>
																				    <td width="4%" background="img/resume1.gif" >&nbsp;</td>
																				   </tr>';
			$ajax_content.=$search_string.$hidden_fields;
                    //    {hidden_fields}{hidden_fields1}
			$ajax_content.='     </form>
                      </table>
																					</div>					 
                    </td>
                   </tr>
																			<tr>
                    <td   align="right">'.$paging_array[rate_mark].'</td>
                   </tr>
                  </table>';
    echo $ajax_content;die();
			}
 
   }
			else
		 {    
				if($map_view==1)
				{
				}
				else
				{
     $whereClause1=$whereClause."and jl.jobseeker_id in(select distinct(jah.jobseeker_id) from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." as jah where jah.start_date<=CURDATE() and jah.end_date >=CURDATE())";
     $table_names=JOBSEEKER_LOGIN_TABLE." as jl join  ".JOBSEEKER_TABLE." as j on  (jl.jobseeker_id=j.jobseeker_id) join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (j.jobseeker_id=jr1.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as jrt on (jr1.resume_id=jrt.resume_id and jrt.recruiter_id='".$_SESSION['sess_recruiterid']."') left join ".JOBSEEKER_RATING_TABLE." as jrt1 on (jr1.resume_id=jrt1.resume_id and jrt1.admin_rate='Y' ) left outer join  ".ZONES_TABLE." as z on (z.zone_id=j.jobseeker_state_id) left outer join  ".COUNTRIES_TABLE." as c on (c.id=j.jobseeker_country_id) left outer join ".COUNTRIES_TABLE." as n on (n.id=jr1.jobseeker_nationality)"  ;
     $query1 = "select count(jr1.resume_id) as x1 from $table_names where $whereClause1 ";
    // echo "<br>$query1";//exit;
    $result1=tep_db_query($query1);
    $tt_row=tep_db_fetch_array($result1);
    $x2=$tt_row['x1'];
    //echo $x1;//exit;
    //////////////////
    ///only for sorting starts
    include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
	  	$sort_1='';
			 if(isset($_POST['sort_1']))
    $sort_1=tep_db_prepare_input($_POST['sort_1']);
    $sort_array=array("jobseeker_name",'jl.jobseeker_email_address');
    $obj_sort_by_clause=new sort_by_clause($sort_array,'jr1.availability_date desc, j.jobseeker_last_name asc , jr1.inserted desc','sort_1');
    $order_by_clause=$obj_sort_by_clause->return_value;
    $see_before_page_number_array=see_before_page_number($sort_array,$field,'jr1.availability_date desc , jr1.inserted desc , j.jobseeker_last_name',$order,'asc',$lower_1,'0',$higher_1,MAX_DISPLAY_SEARCH_RESULTS);
    $lower_1=$see_before_page_number_array['lower'];
    $higher_1=$see_before_page_number_array['higher'];
    $field_1=$see_before_page_number_array['field'];
    $order_1=$see_before_page_number_array['order'];
    $hidden_fields.=tep_draw_hidden_field('sort_1',$sort_1);

    ///only for sorting ends

    $totalpage_1=ceil($x2/$higher_1);

    $query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower_1,$higher_1";
    $result=tep_db_query($query);
    //echo "<br>$query";//exit;
    $x=tep_db_num_rows($result);
    //echo $x;exit;
    $pno_1= ceil($lower_1+$higher_1)/($higher_1);
    if($x > 0 && $x2 > 0)
    {
     $alternate=1;
					$search_string='';
     while($row = tep_db_fetch_array($result))
     {
      $ide=$row["resume_id"];
      $query_string1=encode_string("search_id==".$ide."==search");
      $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
					 $jobseeker_education_check=getAnyTableWhereData(JOBSEEKER_RESUME3_TABLE.' as e left outer join '.EDUCATION_LEVEL_TABLE.' as el on (e.degree=el.id) ',"resume_id='".$ide."' order by start_year desc ,start_month desc","e.specialization,el.".TEXT_LANGUAGE."education_level_name as education_level_name");
					 $experience_row=getAnyTableWhereData(JOBSEEKER_RESUME2_TABLE.' as ex ',"resume_id='".$ide."' order by start_year desc ,start_month desc","ex.company,ex.job_title");

					 if(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo']))
      {
       $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$row['jobseeker_photo'].'&size=60','','','');
      }
					 else
					 {
       $photo ='';
					 }
      if(tep_not_null($row['availability_date'])) 
      {
       $available_status=tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 10, 10);
      } 
      else 
      {
       $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLE, 10, 10);
      }
					 $div_extended_values='
																								<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
																									<tr bgcolor="#FFFFFF">
																										<td valign="top" >'.$photo.'</td>
																										<td valign="top" width="2"></td>
																										<td valign="top" width="95%">
																										 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_name']).'</td>
																												</tr>'.($row['jobseeker_privacy']==3?'
                            <tr>
 																												<td valign="top">'.tep_db_output($row['jobseeker_city']).(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_state']:$row['jobseeker_state']).' '.($row['jobseeker_country']).'</td>
																												</tr>':'').((!$jobseeker_education_check)?'':'
																												<tr>    
                             <td valign="top">'.tep_db_output($jobseeker_education_check['education_level_name']).(tep_not_null($jobseeker_education_check['specialization'])?' ('.tep_db_output($jobseeker_education_check['specialization']).')':'').'</td>
			                         </tr>').((!$experience_row)?'':' 
																												<tr>    
                             <td valign="top">'.(tep_not_null($experience_row['company'])?'Working in '.$experience_row['company'] :'').(tep_not_null($experience_row['job_title'])? ' As '.$experience_row['job_title'] :'').'</td>
			                         </tr>').' 
																											</table>
																										</td>
																									</tr>
																								</table>
                     					';
      $rating = (tep_not_null($row['point'])?tep_db_output($row['point']).'<span style="color:blue"> *</span>,':'').(tep_not_null($row['point1'])?tep_db_output($row['point1']).' <span class="red"> **</span>':'');

      $search_string.='<tr '.$row_selected.'>
						                 <td><div align="center">'.tep_draw_checkbox_field('resume_id[]',$ide,false).'</div></td>
																						 <td class="resume_result2">'.tep_db_output($row['jobseeker_name']).'</td>
																						 <td class="resume_result2">'.ucfirst(tep_db_output($row['jobseeker_city'])).'</td>
																						 <td class="resume_result3" align="center"><a target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'" class="resume_result3">'.INFO_TEXT_VIEW.'</a></td>
																							<td><div align="center"><a target="_blank" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1.'&action=download').'"><img src="img/html_icon.jpeg" width="20" height="20"></a></div></td>
																							<td><div align="center">'.$rating.'</div></td>
																							<td><div align="center">'.$available_status.'</div></td>
																							<td align="center"><a href="#normal_resume" onclick="collapse(\'normal_'.$ide.'\')"><img src="img/plus.gif" width="9" height="9"></a></td>
																					 </tr>
																						<tr> 
																							<td colspan="8">
																								<div id="normal_'.$ide.'" style="display:none;">
																								<table width="100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
																								 <tr bgcolor="#FFFFFF">
																								  <td valign="top">
																								   '.$div_extended_values.'
																							   </td>
																							  </tr>
																						  </table>
																						 	</div>
																							</td>
																						</tr>';
     
      $alternate++;
      $lower_1 = $lower_1 + 1;
     }
     $plural=($x1=="1")?TABLE_HEADING_RESUME:TABLE_HEADING_RESUMES;
				 $check_link = '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#featured_resume" class="resume_result3" onclick="checkall1()">'.tep_db_output(INFO_TEXT_SELECT_ALL).'</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="#featured_resume" class="resume_result3" onclick="uncheckall1()">'.tep_db_output(INFO_TEXT_CLEAR_SELECTED).'</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="#featured_resume" class="resume_result3" onclick="SaveSelected1(\'save\')">'.tep_db_output(INFO_TEXT_SAVE_SELECTED).'</a>';
				 ($x > 0?$check_uncheck=$check_link:$check_uncheck='');
     $paging_array1= array('total'=>tep_db_output(SITE_TITLE).'&nbsp;'.INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x2</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH,
				                      'rate_mark'=>'<span class="red">**</span><span class="resume_result3">'.INFO_TEXT_RATED_ADMIN.'</span><span style="color:blue">*</span><span class="resume_result3">'.INFO_TEXT_RATED_SELF.'</span>',
                          'result_class' =>'',
					                     'check_link'=>$check_uncheck);     
    }
    else
    {
     $paging_array1= array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED,
                          'result_class' =>'class="result_hide"',
                    						'check_link'=>'');
    }
    $list_string1=see_page_number1('page1','lower_1','higher_1',$lower_1,$x2,$total_1,$pno_1,$higher_1,$totalpage_1);
		  tep_db_free_result($result1);
  	////////////// ajax 
			$ajax_content='<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                   <tr>
																				<td>					   
																				 <table width="100%"  border="0" cellspacing="0" cellpadding="0">                        
																				  <tr>
																				   <td height="25" >'.$paging_array1['total'].'</td>
																				   <td height="25" ><div align="right">'.$list_string1['list_page'].' &nbsp; '.$list_string1['page'].'&nbsp;&nbsp;&nbsp;</div></td>
																				  </tr>
																				  <tr>
																				   <td  colspan="2"><img src="img/spacer.gif" width="5" height="5"></td>
																				  </tr>
																				  <tr>
																				   <td height="25"  valign="top" ><div align="left">'.$paging_array1['check_link'].'</div></td>
																				   <td align="right"></td>
																				  </tr>
																				 </table> 
																				</td>
                   </tr>
																			<tr>
																				<td>
																				 <div id="general_search_result" '.$paging_array1['result_class'].'> 
																				  <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#CFCDCC">
																				   <tr bgcolor="#72900c">
																				    <td width="6%"  background="img/resume1.gif" height="35" background="img/resume1.gif"></td>
																				    <td width="40%" background="img/resume1.gif" class="resume_result1"><a href="#"  onclick="submit_thispage1(\'page1\',\'lower_1\',\'sort_1\',\''.$obj_sort_by_clause->return_sort_array['name'][0].'\','.$lower_1.');"><u>'.INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array["image"][0].'</a></td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_CITY.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1" align="center">'.TABLE_HEADING_RESUME.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1" align="center">'.TABLE_HEADING_DOWNLOAD.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_RATING.'</td>
																				    <td width="10%" background="img/resume1.gif" class="resume_result1">'.TABLE_HEADING_AVAILABILITY.'</td>
																				    <td width="4%" background="img/resume1.gif" >&nbsp;</td>
																				   </tr>';
			$ajax_content.=$search_string.$hidden_fields;
                    //    {hidden_fields}{hidden_fields1}
			$ajax_content.='     </form>
                      </table>
																					</div>					 
                    </td>
                   </tr>
																			
                  </table>';
    echo $ajax_content;die();
			}
 
   }
  }
}
?>