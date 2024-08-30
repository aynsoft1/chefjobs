<?
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #**********
**********# Company       : Aynsoft                 #**********
**********# Copyright (c) www.aynsoft.com 2004     #**********
***********************************************************
*/
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_ALERT_AGENT_DIRECT);
$template->set_filenames(array('job_alert_agent_direct' => 'job_alert_agent_direct.htm','job_alert_agent_direct1'=>'job_alert_agent_direct1.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'job_alert_agent.js';

$state_error=false;
//print_r($_POST);
$action = (isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action'])? $_GET['action'] :''));
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
//$sID = (isset($_GET['sID']) ?(int)$_GET['sID'] : '');
//$edit=false;
$search_name='';

//$TREF_job_alert_email=$_POST['TREF_job_alert_email'];
/*********************************************************
if(!tep_not_null($TREF_job_alert_email) || tep_validate_email($TREF_job_alert_email) == false)
	$messageStack->add(ENTER_EMAIL_ADDRESS,'error');
*********************************************************/


// initialize
if(tep_not_null($_POST['keyword']) && (($_POST['keyword']!='keyword') && ($_POST['keyword']!='job search keywords')) )
{
 $keyword=tep_db_prepare_input($_POST['keyword']);
}
if(tep_not_null($_POST['location']) && ($_POST['location']!='location'))
{
 $location=tep_db_prepare_input($_POST['location']);
}
if(tep_not_null($_POST['TREF_job_alert_email']))
{
 $TREF_job_alert_email=tep_db_prepare_input($_POST['TREF_job_alert_email']);
}
if(tep_not_null($_POST['company']))
{
 $company=tep_db_prepare_input($_POST['company']);
}
if(tep_not_null($_POST['job_post_day']))
{
 $job_post_day=tep_db_prepare_input($_POST['job_post_day']);
}
if(tep_not_null($_POST['inserted_date']))
{
 $inserted_date=tep_db_prepare_input($_POST['inserted_date']);
}
if(tep_not_null($_POST['word1']))
{
 $word1=tep_db_prepare_input($_POST['word1']);
}
if(tep_not_null($_POST['country']))
{
 $country=(int)tep_db_prepare_input($_POST['country']);
}
if(tep_not_null($_POST['job_alert']))
{
 $job_alert=tep_db_prepare_input($_POST['job_alert']);
}
if(isset($_POST['zip_code']))
$zip_code       = tep_db_prepare_input($_POST['zip_code']);
if(isset($_POST['radius']))
$radius         = (int)tep_db_prepare_input($_POST['radius']);
$search_zip_code=1;
if(tep_not_null($zip_code))
$search_zip_code= 2;
if(tep_not_null($_POST['state']))
{
 if(is_array($_POST['state']))
  $state=implode(',',tep_db_prepare_input($_POST['state']));
 else
  $state=tep_db_prepare_input($_POST['state']);
 if($state[0]==',')
  $state=substr($state,1);
}
elseif(tep_not_null($_POST['state1']))
{
 $state=tep_db_prepare_input($_POST['state1']);
}
if(tep_not_null($_POST['job_category']))
{
 $job_category=$_POST['job_category'];
 $job_category1=implode(",",$job_category);
}
if(tep_not_null($_POST['experience']))
{
 $experience=$_POST['experience'];
}

$job_alert_direct_id = tep_db_prepare_input($_GET['id']);

//echo $job_alert_direct_id.$action;die;

// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'confirm_delete':
   $job_alert_direct_id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . JOB_ALERT_DIRECT_TABLE . " where id = '" . (int)$job_alert_direct_id . "'");
	$messageStack->add(MESSAGE_SUCCESS_DELETED, 'success');
  // tep_redirect(tep_href_link(""));
  break;


  case 'search':
   $hidden_fields1='';
 		$error=false;
		//	$agent_name=tep_db_prepare_input($_POST['TR_agent_name']);
   $TREF_job_alert_email=tep_db_prepare_input($_POST['TREF_job_alert_email']);
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';
   if ((preg_match("/http:\/\//i",$keyword)))
   $keyword='';
/*			if (strlen($agent_name) <=0)
   {
    $error = true;
    $messageStack->add(ENTER_AGENT_NAME_ERROR,'error');
   }
*/
/*************************************************************/
    if(tep_not_null($TREF_job_alert_email) == false || tep_validate_email($TREF_job_alert_email) == false)
    {
     $error = true;
     $messageStack->add(ENTER_EMAIL_ADDRESS,'Error');
    }
/*******************************************************************/
			if(!$error)
		 {
				if(tep_not_null($keyword)  && (($_POST['keyword']!='keyword') && ($_POST['keyword']!='job search keywords')) ) //   keyword starts //////
				{
			/*		if($_SESSION['sess_jobsearch']!='y')
					tag_key_check($keyword);
					$_SESSION['sess_jobsearch']='y';
*/

					$whereClause1='(';
					$hidden_fields1.=tep_draw_hidden_field('keyword',$keyword);
					$search = array ("'[\s]+'");
					$replace = array (" ");
					$keyword = preg_replace($search, $replace, $keyword);
					if($word1=='Yes')
					{
						$hidden_fields.=tep_draw_hidden_field('word1',$word1);
						$explode_string=explode(' ',$keyword);
						$whereClause1.='(';
						for($i=0;$i<count($explode_string);$i++)
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
					$whereClause1.=" ) ";
					$whereClause.=$whereClause1;
				}
				// keyword ends //////
				//   location starts //////
				if(tep_not_null($location) && $_POST['location']!='location')
				{
					$whereClause1='(';
					$hidden_fields1.=tep_draw_hidden_field('location',$location);
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
				//   location ends //////

				// job_post_day starts //
				if(tep_not_null($_POST['job_post_day']))
				{
					$job_post_day=abs((int)($_POST['job_post_day']));
					$hidden_fields.=tep_draw_hidden_field('job_post_day',$job_post_day);
					$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
					$whereClause.=" ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$job_post_day, date("Y")))."' ) ";
				}
				// job_post_day end //
				// inserted date starts //
				if(tep_not_null($_POST['inserted_date']))
				{
					$inserted_date=($_POST['inserted_date']);
					$hidden_fields.=tep_draw_hidden_field('inserted_date',$inserted_date);
					$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
					$whereClause.=" ( j.re_adv ='".$inserted_date."' ) ";
				}
				// inserted date end //
				// company starts //
				//*
				if(tep_not_null($_POST['company']))
				{
					$hidden_fields.=tep_draw_hidden_field('company',$company);
					$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
					$whereClause.=" ( r.recruiter_company_name ='".tep_db_input($company)."' )";
				}
				//*/// company ends ///
				// experience starts //
				//*
				if(tep_not_null($_POST['experience']))
				{
					$experience=$_POST['experience'];
					$hidden_fields.=tep_draw_hidden_field('experience',$experience);
					$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
					$explode_string=explode("-",$experience);
					$whereClause.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
				}
				//*/// experience ends ///
				// industry job_category  starts ///
				if(tep_not_null($_POST['job_category']))
				{
					$job_category=$_POST['job_category'];
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
				// Job Alert starts ///
				if(tep_not_null($job_alert))
				{
					$hidden_fields.=tep_draw_hidden_field('job_alert',$job_alert);
				}
				// Job Alert ends ///
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
				// state ends ///
					// country starts ///
				if(tep_not_null($country))
					{
						$whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
						$country_temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where id=".$country."");
						if(tep_db_num_rows($country_temp_result) > 0)
							{
								while($temp_row = tep_db_fetch_array($country_temp_result))
								{
									$whereClause.=" j.job_country_id ='".$temp_row['id']."'";
								}
								tep_db_free_result($country_temp_result);
							}
					$whereClause.="  )";
					}
				// country ends ///
				$whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
				////
				$now=date('Y-m-d H:i:s');
    $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id)';
    $whereClause.="   rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
    $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description,  j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name"; //j.job_state, j.job_state_id,j.job_country_id
				$query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";
				//tep_mail("Kamal" , 'kamal@erecruitmentsoftware.com', "Subseawork job search", $query1, SITE_OWNER, ADMIN_EMAIL);
				//echo "<br>$query1";//exit;   print_r($_POST);
				$result1=tep_db_query($query1);
				$tt_row=tep_db_fetch_array($result1);
				$x1=$tt_row['x1'];
				//echo $x1;

				//////////////////
				$query = "select $field_names from $table_names where $whereClause ORDER BY j.job_source asc, j.inserted desc, j.job_featured='Yes' desc";
				$starting=0;
				$recpage = MAX_DISPLAY_SEARCH_RESULTS;
				$obj = new pagination_class($query,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius);
				$result = $obj->result;
				$x=tep_db_num_rows($result);
				$content='';
				$count=1;
				if(tep_db_num_rows($result)!=0)
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
					$template->assign_block_vars('job_search_result', array(
                                  'row_selected' => $row_selected,
                                  'check_box' => (($row['post_url']=='Yes'  )?'':'<input type="checkbox" name="apply_job" value="'.$query_string.'">'),
                                  'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'" class="job_search_title" target="_blank">'.tep_db_output($row['job_title']).'</a>',
 						                           'company_name' =>tep_db_output($row['recruiter_company_name']),
 						                           'location' =>tep_db_output($row['location'].' '.$row['country_name']),
                                  'experience' =>tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                                  'salary' =>(tep_not_null($row['job_salary']))?tep_db_output($row['job_salary']):'',
                                  'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
						                            'description' => nl2br(tep_db_output(strip_tags($row['job_short_description']))),
                                  'apply_before' => tep_date_long($row['expired']),
                                  'logo'      => $recruiter_logo,
	 					                           'email_job' => $email_job,
	 					                           'apply_job' => $apply_job,
						                            ));


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

					}
					$template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total));
					$plural=($x1=="1")?INFO_TEXT_JOB:INFO_TEXT_JOBS;
					$template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
				}
				else
				{
					$template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
				}
				if(!$error)
				{
					$sql_data_array=array( 'inserted'=>'now()',
							'keyword'=>$keyword,
							'location'=>$location,
							'company'=>$company,
							'word1'=>$word1,
							'country'=>$country,
							'state'=>$state,
							'industry_sector'=>$job_category1,
							'experience'=>$experience,
                            'zip_code'=>$zip_code,
                            'radius'=>$radius,
							'email_id' => $TREF_job_alert_email,
							'job_alert'    => $job_alert
						);
						 tep_db_perform(JOB_ALERT_DIRECT_TABLE, $sql_data_array);
						 $messageStack->add(MESSAGE_SUCCESS_INSERTED, 'success');
			 }
			}
  break;
 }
}
//echo  $whereClause;
if(!in_array($word1,array('Yes','No')))
 $word1='Yes';
if(!in_array($job_alert,array('daily','weekly','monthly')))
 $job_alert='daily';
if($action=='' && !isset($_GET['sID']))
 $country=(int)DEFAULT_COUNTRY_ID;

if($search_zip_code==2)
{
 $default_tab=2;
}
else
{
 $default_tab=1;
 $search_zip_code=1;
}

//$cat_array=tep_get_diving_main_categories(DIVING_CATEGORY_TABLE);
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_JOB_CATEGORY));
if($messageStack->size('success') > 0)
 $update_message=$messageStack->output('success');
else
$update_message=$messageStack->output();
if($action!='search'  || $error)
{
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'form'                   => tep_draw_form('search', FILENAME_JOB_ALERT_AGENT_DIRECT,($edit?'sID='.$save_search_id:''),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','search').tep_draw_hidden_field('search_zip_code',$search_zip_code),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_ALERT_AGENT_DIRECT,'post').tep_draw_hidden_field('action','search'),
'INFO_TEXT_JOB_ALERT_EMAIL'   => INFO_TEXT_JOB_ALERT_EMAIL,
  'INFO_TEXT_JOB_ALERT_EMAIL1'  => tep_draw_input_field('TREF_job_alert_email', $TREF_job_alert_email,'size="26" maxlength="32" class="form-control required"',false),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $keyword,'size="40" class="form-control"',false),
  'INFO_TEXT_KEYWORD_CRITERIA'=>INFO_TEXT_KEYWORD_CRITERIA,
  'INFO_TEXT_KEYWORD3'     => tep_draw_radio_field('word1', 'Yes', '', $word1,'id=radio_word1').' <label class="form-check-label" for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').' <label class="form-check-label" for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label>',
		'INFO_TEXT_JOB_ALERT_CRITERIA' => INFO_TEXT_JOB_ALERT_CRITERIA,
  'INFO_TEXT_JOB_ALERT_CRITERIA1'=> tep_draw_radio_field('job_alert', 'daily', '', $job_alert,'id=radio_daily class="form-check-input me-1"').'<label class="form-check-label me-3" for="radio_daily">'.INFO_TEXT_DAILY.'</label> '.tep_draw_radio_field('job_alert', 'weekly', '', $job_alert,'id=radio_weekly class="form-check-input me-1"').'<label class="me-3 form-check-label" for="radio_weekly">'.INFO_TEXT_WEEKLY.'</label> '.tep_draw_radio_field('job_alert', 'monthly', '', $job_alert,'id=radio_monthly class="form-check-input me-1"').'<label class="form-check-label" for="radio_monthly">'.INFO_TEXT_MONTHLY.'</label> ',

  'INFO_TEXT_LOCATION'     => (($search_zip_code==2)?INFO_TEXT_ZIP_CODE:INFO_TEXT_LOCATION_NAME),
  'INFO_TEXT_LOCATION1'    => (($search_zip_code==2)?tep_draw_input_field('zip_code',$zip_code,'').''.zone_radius('radius',"","",$radius,true).tep_draw_hidden_field('location',''):tep_draw_input_field('location', $location,' class="form-control" ',false)),
  'INFO_TEXT_SEARCH_COUNTRY_STATE' => INFO_TEXT_SEARCH_COUNTRY_STATE,

  'INFO_TEXT_ZIP_CODE'     => INFO_TEXT_ZIP_CODE,
  'INFO_TEXT_RADIUS'       => INFO_TEXT_RADIUS,
  'INFO_CODE_IF_COUNTRY_US'=>($country=='223'?'<div class="form-group row">
                        <div class="col-md-3 col-form-label">
                            '.INFO_TEXT_SEARCH_US_ZIP.' :
                        </div>
                        <div class="col-md-5 mb-3">
                          <label for="inputZip">'.INFO_TEXT_ZIP_CODE.'</label>
                          '.tep_draw_input_field('zip_code',$zip_code,' class="form-control" ').'
                        </div>
                        <div class="col-md-4">
                            <label for="inputRadius">'.INFO_TEXT_RADIUS.'</label>
                            '.zone_radius('radius',"","",$radius,true,' class="form-select"').'
                        </div>
                    </div>
':''),

  'INFO_TEXT_COUNTRY'      => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'     => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-select' ","All countries","",$country),
  'INFO_TEXT_DEFAULT_TAB'  => $default_tab,
  'INFO_TEXT_JOB_CATEGORY' => INFO_TEXT_JOB_CATEGORY,
  'INFO_TEXT_JOB_CATEGORY_TEXT' => INFO_TEXT_JOB_CATEGORY_TEXT,
  'INFO_TEXT_JOB_CATEGORY1'=> tep_draw_pull_down_menu('job_category[]', $cat_array, explode(",",$job_category1), ' class="form-select"', false),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => experience_drop_down('name="experience" class="form-select" ', 'Any experience', '', $experience),
  'INFO_TEXT_JOB_POSTED'   => INFO_TEXT_JOB_POSTED,
  'INFO_TEXT_JOB_POSTED1'  => LIST_SET_DATA(JOB_POSTED_TABLE,"",TEXT_LANGUAGE.'type_name','value',"priority","name='job_post_day' class='form-select' " ,INFO_TEXT_DEFAULT_JOB_POST_DAY,'',$job_post_day),
  'button'                 => '<button class="btn btn-primary" type="submit">Create job alert</button>',
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
  ));
}
else
{$key1=(tep_not_null($keyword)?$key1=$keyword:'keyword');
 $loc1=(tep_not_null($location)?$loc1=$location:'location');
//	$agent_id=getAnyTableWhereData(JOB_ALERT_DIRECT_TABLE,"id=".tep_db_insert_id(),"id");
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'hidden_fields1'          => $hidden_fields1,
//  'form'                   => tep_draw_form('page', FILENAME_JOB_ALERT_AGENT_DIRECT,($edit?'sID='.$save_search_id:''),'post'),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_ALERT_AGENT_DIRECT,'','post').tep_draw_hidden_field('action','search'),
  'button'                 => tep_image_submit(PATH_TO_BUTTON.'button_refine_search.gif', IMAGE_SEARCH),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $key1,' class="form-control"',false),
  'INFO_TEXT_LOCATION'     => INFO_TEXT_LOCATION,
  'INFO_TEXT_LOCATION1'    => tep_draw_input_field('location', $loc1 ,' class="form-control"',false),
  'INFO_TEXT_APPLY_NOW'    => (($x>0)?INFO_TEXT_APPLY_NOW:''),
  'INFO_TEXT_APPLY_NOW1'   => (($x>0)?INFO_TEXT_APPLY_NOW1:''),
  'INFO_TEXT_APPLY_ARROW'  => (($x>0)?tep_image('img/job_search_arrow.gif',''):''),
  'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?tep_image_button(PATH_TO_BUTTON.'button_apply_selectedjob.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"'):tep_image_button(PATH_TO_BUTTON.'button_registered_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"').' '.tep_image_button(PATH_TO_BUTTON.'button_new_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'new\');" style="cursor:pointer;"')):''),
	//	'INFO_TEXT_CHANGE_CRITERIA' => (tep_not_null($sID)?'<a href="'.tep_href_link(FILENAME_JOB_ALERT_AGENT_DIRECT,'sID='.$sID).'" class="blue">Change Criteria</a>':'<a href="'.tep_href_link(FILENAME_JOB_ALERT_AGENT_DIRECT,'sID='.$agent_id['id']).'" class="blue">Change Criteria</a>'),
  'INFO_TEXT_LOCATION_NAME'=> INFO_TEXT_LOCATION_NAME,
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_APPLY_BEFORE' => INFO_TEXT_APPLY_BEFORE,
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
		'INFO_TEXT_SALARY'       => INFO_TEXT_SALARY,
	//	'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
	'update_message'         => $update_message
  //'save_button'            => tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).($action1=='save_search'?'&nbsp;'.'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'">'.tep_image(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>':'').' <a href="'.tep_href_link(FILENAME_JOB_ALERT_AGENT_DIRECT).'">'.tep_image(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>',
                      ));
}
$template->assign_vars(array(
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'update_message' => $messageStack->output()));
if(($action=='search' && !$error))
{
 $template->pparse('job_alert_agent_direct1');
}
else
{
 $template->pparse('job_alert_agent_direct');
// unset($_SESSION['sess_jobsearch']);
}
?>