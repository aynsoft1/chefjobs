<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("include_files.php");
ini_set('max_execution_time','60');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH_BY_LOCATION);
$template->set_filenames(array('job_search' => 'job_search_by_location.htm','job_search_states'=>'job_search_by_location1.htm','job_search_result'=>'job_search_result_location.htm'));
include_once(FILENAME_BODY);
$jscript_file_search=PATH_TO_LANGUAGE.$language."/jscript/".'jobs_search.js';
$preview_box_jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'previewbox.js';
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobs_by_location.js';
//$template->assign_vars(array('HEADING_TITLE'=>HEADING_TITLE));
$state_error=false;
//print_r($_POST);
//print_r($_GET);
if(tep_not_null($_POST['action']))
$action = (isset($_POST['action']) ? $_POST['action'] : '');
elseif(tep_not_null($_GET['action']))
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$search_name='';
// initialize
if(isset($_GET['country']))
{
 $data=decode_country($_GET['country']);
 if(tep_not_null($data))
 list($country_id, $country_name, $fr_country_name) = (isset($_GET['country'])?array_values($data):'');
 else
 tep_redirect(getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION));

}
if(isset($_GET['state']))
{
 $data=(isset($_GET['state'])?(decode_state($_GET['state'])):'');
 if(tep_not_null($data))
 list($zone_id, $zone_country_id,$zone_code , $zone_name, $fr_zone_name) = array_values($data);
 else
 tep_redirect(getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION));
}
//echo decode_country($_GET['country']);
if(tep_not_null($_POST['country']))
{
 $country=(int)tep_db_prepare_input($_POST['country']);
}
if(tep_not_null($_GET['country']))
{
 $country=(int)$country_id;
}
if(tep_not_null($zone_name))
{
 $state=tep_db_prepare_input($zone_name);
}
elseif(tep_not_null($_POST['state']))
{
 $state=tep_db_prepare_input($_POST['state']);
}
else
 $state='';
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
  // $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';
   if($country > 0)
   {
    $hidden_fields.=tep_draw_hidden_field('country',$country);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.job_country_id='".tep_db_input($country)."' )";
   }
   // country ends ///
   // state starts ///
   if(tep_not_null($state))
   {
    $hidden_fields.=tep_draw_hidden_field('state',$state);
    $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( ( j.job_state like '%".tep_db_input($state)."%' ) ";
    if(tep_db_num_rows($temp_result) > 0)
    {
     $whereClause.=' or ( ';
     while($temp_row = tep_db_fetch_array($temp_result))
     {
      $whereClause.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
     }
     $whereClause=substr($whereClause,0,-4);
     $whereClause.=" ) )";
     tep_db_free_result($temp_result);
    }
    else
    {
     $whereClause.=" )";
    }
   }
   // array state ends //*/
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r  on (r.recruiter_id=rl.recruiter_id) left join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id=c.id)';
   $whereClause.=" j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description,  j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name,j.job_skills";
   $query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $tt_row=tep_db_fetch_array($result1);
   $x1=$tt_row['x1'];
   //echo $x1;
   //////////////////
 		 $query = "select $field_names from $table_names where $whereClause ORDER BY if(j.job_source ='jobsite',0,1)  asc , j.inserted desc, j.job_featured='Yes' desc";
		 $starting=0;
			$recpage = MAX_DISPLAY_SEARCH_RESULTS;
			$obj = new pagination_class1($query,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius,0,'');
			$result = $obj->result;
			$x=tep_db_num_rows($result);
			$content='';
			$count=1;
   if($x!=0)
   {
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["job_id"];
     //$recruiter_id=$row["recruiter_id"];
     $recruiter_logo='';
     $company_logo=$row['recruiter_logo'];
     $title_format=encode_category($row['job_title']);
     $company_format=str_replace(' ','-',$row['recruiter_company_name']);
     if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");
     $query_string=encode_string("job_id=".$ide."=job_id");
					$email_job    ='<a class="btn btn-sm btn-text border bg-white mr-3" href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'" target="_blank"><i class="fa fa-envelope-o mr-1" aria-hidden="true"></i> '.INFO_TEXT_EMAIL_THIS_JOB.'</a>';
					$apply_job    ='<a class="btn btn-block btn-sm btn-primary" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.'</a>';

					if($row['job_featured']=='Yes')
					{
					 $row_selected='jobSearchRowFea';
					}
					else
					{
					 $row_selected='jobSearchRow1';
						$count++;
					}
					$job_skill_1= getSkillTagLink($row['job_skills']);

////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

					$template->assign_block_vars('job_search_result', array(
                                  'row_selected' => $row_selected,
                                  'check_box' => (($row['post_url']=='Yes'  )?'':'<input type="checkbox" name="apply_job" value="'.$query_string.'">'),
                                  'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'" class="job_search_title" target="_blank">'.tep_db_output($row['job_title']).'</a>',
 						                           'company_name' =>tep_db_output($row['recruiter_company_name']),
 						                           'location' =>tep_db_output($row['location'].' '.$row['country_name']),
                                  'experience' =>tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                                  'salary' =>(tep_not_null($row['job_salary']))?$sym_left.tep_db_output($row['job_salary']).$sym_rt:'',
                                  'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
                                  'job_skill' =>(tep_not_null($row['job_skills']))? $job_skill_1:'',
                                  'skill_class' =>(tep_not_null($row['job_skills']))?'':'result_hide',
						          'description' => nl2br(tep_db_output(strip_tags($row['job_short_description']))),
                                  'apply_before' => tep_date_long($row['expired']),
                                  'jobId' => $row['job_id'],  
                                  'logo'      => $recruiter_logo,
	 					                           'email_job' => $email_job,
	 					                           'apply_job' => $apply_job,
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


    }
			 $template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total,'page_view'=>$obj->show_view));
    $plural=($x1=="1")?INFO_TEXT_JOB:INFO_TEXT_JOBS;
    $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
   }
   else
   {
    $template->assign_vars(array('content_hide'=>'result_hide','total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
   }
  break;
 }
}
//echo  $whereClause;
////////continent-country list start///////////
if($action!='search' && $_GET['country']=='')
{
		$field_names="id,".TEXT_LANGUAGE."continent_name";
		$continent_query = "select $field_names from ".CONTINENT_TABLE." where id=6 order by continent_name asc ";
		$continent_result=tep_db_query($continent_query);
		$x=tep_db_num_rows($continent_result);
		$i=1;
		$category_name_for_meta_titles='';
		if($x>0)
		{
		while($continent = tep_db_fetch_array($continent_result))
		{
			$category_name_for_meta_titles.=$row11['category_name'].' forum, ';
			$ide=$continent["id"];
			$key=((strlen($continent[TEXT_LANGUAGE."continent_name"])<20)?$continent[TEXT_LANGUAGE."continent_name"]:substr($continent[TEXT_LANGUAGE."continent_name"],0,18)."..");
			$key1=$continent[TEXT_LANGUAGE."continent_name"];
			$continent="<div class='fw-bold text-dark mt-4'>".tep_db_output($key1)."</div>";
			$country_query  = "select * from ".COUNTRIES_TABLE." where continent_id=".$ide." order by country_name  asc";
			$country_result = tep_db_query($country_query);
			$j=0;
			$div_id='';
			$country_list='';
			$country_list.='<table class="" border="0" width="100%"><tr>';
			$no_of_countries=tep_db_num_rows($country_result);
			if($no_of_countries>1)
   while($country = tep_db_fetch_array($country_result))
			{
				/////////no of jobs in a country start/////////
				$now=date('Y-m-d H:i:s');
				$table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r  on (r.recruiter_id=rl.recruiter_id) left join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL)';
    $whereClause=" j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and j.job_country_id='".$country['id']."'";
    $field_names="j.job_id";
    $jobs_in_country_query = "select count(j.job_id) as x1 from $table_names where $whereClause ";
			 $no_of_jobs_result=tep_db_query($jobs_in_country_query);
    $jobs_row=tep_db_fetch_array($no_of_jobs_result);
    $no_of_jobs=$jobs_row['x1'];
				$jobs=($no_of_jobs>0?'&nbsp;('.$no_of_jobs.')':'');
				/////////no of jobs in a country end/////////
				$continent_name=get_name_from_table(CONTINENT_TABLE,TEXT_LANGUAGE.'continent_name','id',$country['continent_id']);
				if($j%3==0)
				{
					$country_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country[TEXT_LANGUAGE.'country_name']).'/').'" title="'.$country[TEXT_LANGUAGE.'country_name'].'" class="style27 skeleton">'.$country[TEXT_LANGUAGE.'country_name'].'</a><span class="style27">'.$jobs.'</span></td>';
				}
				else if($j%3==1)
				{
					$country_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country[TEXT_LANGUAGE.'country_name']).'/').'" title="'.$country[TEXT_LANGUAGE.'country_name'].'" class="style27 skeleton">'.$country[TEXT_LANGUAGE.'country_name'].'</a><span class="style27">'.$jobs.'</span></td>';
				}
				else
				{
					$country_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country[TEXT_LANGUAGE.'country_name']).'/').'" title="'.$country[TEXT_LANGUAGE.'country_name'].'" class="style27 skeleton">'.$country[TEXT_LANGUAGE.'country_name'].'</a><span class="style27">'.$jobs.'</span></td></tr>';
				}
				$j++;
			}
			$country_list.='</table>';
			$template->assign_block_vars('continent_country', array(
																														'country'=> $country_list,
																														'continent'=> $continent
																													));

			$i++;
		}
		tep_db_free_result($country_result);
		tep_db_free_result($continent_result);
		}
 $template->assign_vars(array(
		'HEADING_TITLE'       => HEADING_TITLE,
  'button' => '<button class="btn btn-sm btn-primary" type="submit" id="button-addon2" style="
  border-top-left-radius:0px;
  border-bottom-left-radius:0px;height: 50px;
  ">'.IMAGE_SEARCH.'</button>',//tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
  // 'form' => tep_draw_form('search', FILENAME_JOB_SEARCH_BY_LOCATION,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
  'form' => tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_COUNTRY'   => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'  => LIST_SET_DATA(COUNTRIES_TABLE,"",TEXT_LANGUAGE.'country_name','id',"country_name",'name="country" class="form-control form-select" style="
  border-top-left-radius:50px!important;
  border-bottom-left-radius:50px!important;
  " ',INFO_TEXT_ALL_COUNTRIES,'',$country),
  'INFO_TEXT_STATE'     => INFO_TEXT_STATE,
  'INFO_TEXT_STATE1'    => LIST_SET_DATA(ZONES_TABLE,"",TEXT_LANGUAGE.'zone_name',TEXT_LANGUAGE.'zone_name',TEXT_LANGUAGE."zone_name",'name="state[]" style="font-size: 12px;color: #626262;"',"All state",'',$state)." ".tep_draw_input_field('state1',$state,'size="20"'),
  'COUNTRY_STATE_SCRIPT'=> country_state($c_name='country',$c_d_value='All countries',$s_name='state[]',$s_d_value='state',TEXT_LANGUAGE.'zone_name',$state),
  'JOB_SEARCH_LEFT'     => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'INFO_TEXT_JSCRIPT_SEARCH_FILE'  => $jscript_file_search,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,

  ));
}//////////continent-country list end///////////////
/////////state list starts///////////
elseif($action!='search' && $_GET['country']!='')
{
	 $state_query  = "select * from ".ZONES_TABLE." where zone_country_id='".$country_id."' order by zone_name  asc";
		$state_result = tep_db_query($state_query);
		$no_of_states=tep_db_num_rows($state_result);
		$s=0;
		$state_list='';
		$state_list.='<table class="" border="0" width="100%"><tr>';
		if($no_of_states>1)
		while($states = tep_db_fetch_array($state_result))
		{
			/////////no of jobs in a state start/////////
			$now=date('Y-m-d H:i:s');
			$table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r  on (r.recruiter_id=rl.recruiter_id) left join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL)';
			$whereClause=" j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and j.job_state_id='".$states['zone_id']."'";
			$field_names="j.job_id";
			$jobs_in_state_query = "select count(j.job_id) as x1 from $table_names where $whereClause ";
			$no_of_jobs_result=tep_db_query($jobs_in_state_query);
			$jobs_row=tep_db_fetch_array($no_of_jobs_result);
			$no_of_jobs=$jobs_row['x1'];
			$jobs=($no_of_jobs>0?'&nbsp;('.$no_of_jobs.')':'');
			/////////no of jobs in a state end/////////
			$country_data=getAnytableWhereData(COUNTRIES_TABLE,"id='".$states['zone_country_id']."'",'*');
			$country_name=get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$country_data['id']);
			$continent_name=get_name_from_table(CONTINENT_TABLE,TEXT_LANGUAGE.'continent_name','id',$country_data['continent_id']);
			if($s%3==0)
			{
				 $state_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country_name).'/'.encode_forum($states[TEXT_LANGUAGE.'zone_name']).'/').'" title="'.$states[TEXT_LANGUAGE.'zone_name'].'" class="style27">'.$states[TEXT_LANGUAGE.'zone_name'].'</a><span class="style27">'.$jobs.'</span></td>';
				/*$state_list.= '<td>'. tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post')
        .tep_draw_hidden_field('action','search')
        .tep_draw_hidden_field('keyword',$states[TEXT_LANGUAGE.'zone_name']).
        '<button type="submit" class="btn btn-text skeleton p-0 text-dark">'.$states[TEXT_LANGUAGE.'zone_name'].$jobs.'</button></form></td>';*/
			}
			else if($s%3==1)
			{
				 $state_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country_name).'/'.encode_forum($states[TEXT_LANGUAGE.'zone_name']).'/').'" title="'.$states[TEXT_LANGUAGE.'zone_name'].'" class="style27">'.$states[TEXT_LANGUAGE.'zone_name'].'</a><span class="style27">'.$jobs.'</span></td>';
				/*$state_list.='<td>'. tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post')
        .tep_draw_hidden_field('action','search')
        .tep_draw_hidden_field('keyword',$states[TEXT_LANGUAGE.'zone_name']).
        '<button type="submit" class="btn btn-text skeleton p-0 text-dark">'.$states[TEXT_LANGUAGE.'zone_name'].$jobs.'</button></form></td>';*/
			}
			else
			{
				$state_list.='<td><a href="'.tep_href_link(encode_forum($continent_name).'/'.encode_forum($country_name).'/'.encode_forum($states[TEXT_LANGUAGE.'zone_name']).'/').'" title="'.$states[TEXT_LANGUAGE.'zone_name'].'" class="style27">'.$states[TEXT_LANGUAGE.'zone_name'].'</a><span class="style27">'.$jobs.'</span></td></tr>';
				/*$state_list.='<td>'. tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post')
        .tep_draw_hidden_field('action','search')
        .tep_draw_hidden_field('keyword',$states[TEXT_LANGUAGE.'zone_name']).
        '<button type="submit" class="btn btn-text skeleton p-0 text-dark">'.$states[TEXT_LANGUAGE.'zone_name'].$jobs.'</button></form></td></tr>';*/
			}
			$s++;
  }
 		$state_list.='</table>';
	 	$template->assign_block_vars('states', array(
																														  'state'=> $state_list,
																													));

		tep_db_free_result($state_result);
 $template->assign_vars(array(
		'HEADING_TITLE'       => HEADING_TITLE,
  'button' => '<button class="btn btn-primary" type="submit" style="border-top-left-radius: 0px;border-bottom-left-radius: 0px;height: 50px;">Search</button>',//tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
  // 'form' => tep_draw_form('search', FILENAME_JOB_SEARCH_BY_LOCATION,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
  'form' => tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_COUNTRY'   => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'  => LIST_SET_DATA(COUNTRIES_TABLE,"",TEXT_LANGUAGE.'country_name','id',"country_name",'name="country" class="form-control" style="font-size: 12px;color: #626262; width:270;"',INFO_TEXT_ALL_COUNTRIES,'',$country),
  'INFO_TEXT_STATE'     => INFO_TEXT_STATE,
  'INFO_TEXT_STATE1'    => LIST_SET_DATA(ZONES_TABLE,"",TEXT_LANGUAGE.'zone_name',TEXT_LANGUAGE.'zone_name',TEXT_LANGUAGE."zone_name",'name="state[]" style="font-size: 12px;color: #626262;"',"All state",'',$state)." ".tep_draw_input_field('state1',$state,'size="20"'),
  'COUNTRY_STATE_SCRIPT'=> country_state($c_name='country',$c_d_value='All countries',$s_name='state[]',$s_d_value='state',TEXT_LANGUAGE.'zone_name',$state),
  'JOB_SEARCH_LEFT'     => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'INFO_TEXT_JSCRIPT_SEARCH_FILE'  => $jscript_file_search,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
  'base_url'=> tep_href_link(),

  ));
}/////////state list ends///////////////
else
{
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
		'HEADING_TITLE'          => $zone_name.' jobs',
  // 'form' => tep_draw_form('page', FILENAME_JOB_SEARCH_BY_LOCATION,($edit?'sID='.$save_search_id:''),'post'),
  'form' => tep_draw_form('page', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'post'),
  'INFO_TEXT_APPLY_NOW'    => (($x>0)?INFO_TEXT_APPLY_NOW:''),
  'INFO_TEXT_APPLY_NOW1'   => (($x>0)?INFO_TEXT_APPLY_NOW1:''),
  'INFO_TEXT_APPLY_ARROW'  => (($x>0)?tep_image('img/job_search_arrow.gif',''):''),
 'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?'<a class="btn btn-success" onclick="ckeck_application(\'\');" role="button">Apply to Selected Jobs</a>':'<a class="btn btn-primary" onclick="ckeck_application(\'\');" role="button">Registered User</a> <a class="btn btn-success ml-2" onclick="ckeck_application(\'new\');" role="button">New User</a>'):''),
 // 'INFO_TEXT_JOB_SEARCH_PLEASE_SELECT_ATLEAST'=>INFO_TEXT_JOB_SEARCH_PLEASE_SELECT_ATLEAST,
  'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
  'INFO_TEXT_LOCATION'     => INFO_TEXT_LOCATION,
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_APPLY_BEFORE' => INFO_TEXT_APPLY_BEFORE,
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'INFO_TEXT_LOCATION_NAME'=> INFO_TEXT_LOCATION_NAME,
  'INFO_TEXT_SALARY'       => INFO_TEXT_SALARY,
  'INFO_TEXT_JOB_SKILL'    =>INFO_TEXT_JOB_SKILL,
  'INFO_TEXT_JSCRIPT_SEARCH_FILE'  => $jscript_file_search,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
   'base_url'=> tep_href_link(),
 ));
}

$template->assign_vars(array(
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML'       => RIGHT_HTML,
 'JOB_SEARCH_LEFT'  => JOB_SEARCH_LEFT,
   'LEFT_HTML'=>LEFT_HTML,
	'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
 'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',
 'update_message'   => $messageStack->output()));
if($action=='search' || $action=='save_search')
{
 $template->pparse('job_search_result');
}
elseif($_GET['country'])
{
 $template->pparse('job_search_states');
}
else
{
 $template->pparse('job_search');
}
?>