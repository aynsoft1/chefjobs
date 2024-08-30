<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_CONTACT_LIST);
$template->set_filenames(array('contact_list' => 'contact_list.htm','contact_list1' => 'contact_list1.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'contact_list.js';
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if(!check_login("jobseeker") && !check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_LOGIN);
}
if(check_login("jobseeker"))
{
 $user_id=$_SESSION['sess_jobseekerid'];
 $user_type="jobseeker";
}
else
{
 $user_id=$_SESSION['sess_recruiterid'];
 $user_type="recruiter";
}
if($user_type=='recruiter')
$LEFT_HTML=LEFT_HTML;
elseif($user_type=='jobseeker')
$LEFT_HTML=LEFT_HTML_JOBSEEKER;
//print_r($_POST);
$contact_id   = (int) tep_db_prepare_input($_POST['contact_id']);
if(tep_not_null($action))
{
 switch($action)
 {
  case 'add':
  case 'update':
   $contact_id   = (int) tep_db_prepare_input($_POST['contact_id']);
   $first_name   = tep_db_prepare_input($_POST['TR_first_name']);
   $middle_name  = tep_db_prepare_input($_POST['middle_name']);
   $last_name    = tep_db_prepare_input($_POST['last_name']);
   $email_address= tep_db_prepare_input($_POST['TNEF_email_address']);
   $address1     = tep_db_prepare_input($_POST['address_1']);
   $address2     = tep_db_prepare_input($_POST['address_2']);
   $country      = (int)tep_db_prepare_input($_POST['country1']);
   /*if(isset($_POST['state']) && $_POST['state']!='')
   $state_value  = tep_db_prepare_input($_POST['state']);
   elseif(isset($_POST['state1']))
   $state_value  = tep_db_prepare_input($_POST['state1']);*/
   $city         = tep_db_prepare_input($_POST['city1']);
   $zip          = tep_db_prepare_input($_POST['zip']);
   $home_phone   = tep_db_prepare_input($_POST['phone1']);
   $mobile       = tep_db_prepare_input($_POST['mobile']);
   $error        = false;
   if(!$error)
   {
    $error_email=false;
    if(tep_not_null($email_address))
    if(tep_validate_email($email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'user_account');
    }
    if (strlen($first_name) <= 0)
    {
     $error = true;
     $messageStack->add(FIRST_NAME_ERROR,'user_account');
    }
    /////////// check state //
  /*  if(is_numeric($state_value))
    {
     $zone_id = 0;//echo $state_value;
     if($check_query = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
     {
      $zone_query = tep_db_query("select distinct zone_id from " . ZONES_TABLE . " where zone_country_id = '" . tep_db_input($country) . "' and (zone_id ='" . tep_db_input($state_value) . "' )");
      if (tep_db_num_rows($zone_query) == 1)
      {
       $zone = tep_db_fetch_array($zone_query);
       $zone_id = $zone['zone_id'];
      }
      else
      {
       //$state_error=true;
       //$error = true;
       //$messageStack->add(ENTRY_STATE_ERROR_SELECT,'user_account');
      }
     }
     else
     {
      //$state_error=true;
      //$error = true;
      //$messageStack->add(ENTRY_STATE_ERROR_SELECT,'user_account');
     }
    }
    else
    {
     if(tep_not_null($state_value))
     if($row11 = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
     {
      $state_error=true;
      $error = true;
      $messageStack->add(ENTRY_STATE_ERROR_SELECT,'user_account');
     }
     elseif (strlen($state_value) <= 0)
     {
      //$state_error=true;
      //$error = true;
      //$messageStack->add(ENTRY_STATE_ERROR,'user_account');
     }
    }*/
    /////////  /////////// end check state ///////////////////////
   }
   if(!$error)
   {
     $sql_data_array=array('user_first_name'   => $first_name,
                           'user_middle_name'  => $middle_name,
                           'user_last_name'    => $last_name,
                           'user_address1'     => $address1,
                           'user_address2'     => $address2,
                           'user_country_id'   => $country,
                           'user_city'         => $city,
                           'user_zip'          => $zip,
                           'user_phone'        => $home_phone,
                           'user_mobile'       => $mobile,
                           'user_id'           => $user_id,
                           'user_type'         => $user_type,
                           'user_email_address'=> $email_address
                           );
    /*if($country>0)
    {
     if($zone_id > 0)
     {
      $sql_data_array['user_state']=NULL;
      $sql_data_array['user_state_id']=$zone_id;
     }
     else
     {
      $sql_data_array['user_state']=$state_value;
      $sql_data_array['user_state_id']=0;
     }
    }*/
    //////////////////////////////////////////////////
    if($action=='update')
    {
     tep_db_perform(USER_CONTACT_TABLE, $sql_data_array, 'update', " id = '".$contact_id."' &&  user_id = '".$user_id."' &&  user_type = '".$user_type."' ");
 	   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTACT_LIST));
    }
    else
    {
     tep_db_perform(USER_CONTACT_TABLE, $sql_data_array);
 	   $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTACT_LIST));
   	}
   }
   else
   {
    if($action=='update')
     $action='edit';
    else
     $action='new';
   }
   break;
  case 'delete':
     tep_db_query("delete from ".USER_CONTACT_TABLE." where  id='".$contact_id."' && user_id='".$user_id."' && user_type='".$user_type."'");
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
     tep_redirect(FILENAME_RECRUITER_CONTACT_LIST);
   break;
 }
}
$action;
if($action=='edit' || $action=='new')
{
if($action=='edit')
{
 $add_save_button='<button class="btn btn-primary px-3" type="submit">'.IMAGE_UPDATE.'</button>';
 $contact_form=tep_draw_form('contact_list',FILENAME_RECRUITER_CONTACT_LIST,'','post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update').tep_draw_hidden_field('contact_id',$contact_id);
}
else
{
 $add_save_button='<button class="btn btn-primary px-3" type="submit">'.IMAGE_ADD.'</button>';
 $contact_form=tep_draw_form('contact_list', FILENAME_RECRUITER_CONTACT_LIST, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add');
}
if($error)
{
 $TR_first_name=$first_name;
 $last_name=$last_name;
 $middle_name=$middle_name;
 $TNEF_email_address=$email_address;
 $address_1=$address1;
 $address_2=$address2;
 $country1=$country;
// $state_value=$state_value;
 $city1=$city;
 $zip=$zip;
 $phone1=$home_phone;
 $mobile=$mobile;
}
else if($contact_id)
{
 $fields="uc.user_first_name, uc.user_last_name, uc.user_middle_name, ";
 $fields.="uc.user_email_address, uc.user_address1, uc.user_address2, ";
 $fields.="uc.user_country_id, uc.user_state_id, uc.user_state, ";
 $fields.="uc.user_city, uc.user_zip, uc.user_phone, uc.user_mobile";
 $row=getAnyTableWhereData(USER_CONTACT_TABLE.' as uc'," user_id='".$user_id."' and user_type='".$user_type."' and id='".$contact_id."'",$fields);
 $TR_first_name      = $row['user_first_name'];
 $last_name          = $row['user_last_name'];
 $middle_name        = $row['user_middle_name'];
 $TNEF_email_address = $row['user_email_address'];
 $address_1          = $row['user_address1'];
 $address_2          = $row['user_address2'];
 $country1           = $row['user_country_id'];
/* $state_value        = (int)$row['user_state_id'];
 if($state_value > 0 and is_int($state_value) )
 {
  $state_value=$state_value;
 }
 else
 {
  $state_value    = $row['user_state'];
 }*/
 $city1    = $row['user_city'];
 $zip      = $row['user_zip'];
 $phone1   = $row['user_phone'];
 $mobile   = $row['user_mobile'];
}
else
{
 $TR_first_name='';
 $last_name='';
 $middle_name='';
 $TNEF_email_address='';
 $address_1='';
 $address_2='';
 $country1=DEFAULT_COUNTRY_ID;
// $state_value="";
 $city1="";
 $zip="";
 $phone1="";
 $mobile="";
}
 $template->assign_vars(array(
 'HEADING_TITLE'           => HEADING_TITLE1,
 //'INFO_TEXT_MAIN'          => INFO_TEXT_MAIN,
 'add_save_button'         => $add_save_button,
 'contact_form'            => $contact_form,
 'INFO_TEXT_FIRST_NAME'    => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1'   => tep_draw_input_field('TR_first_name', $TR_first_name,'size="30" class="form-control required"',false),
 'INFO_TEXT_MIDDLE_NAME'   => INFO_TEXT_MIDDLE_NAME,
 'INFO_TEXT_MIDDLE_NAME1'  => tep_draw_input_field('middle_name', $middle_name,'size="30" class="form-control"'),
 'INFO_TEXT_LAST_NAME'     => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'    => tep_draw_input_field('last_name', $last_name,'size="30" class="form-control"'),
 'INFO_TEXT_HOME_PHONE'    => INFO_TEXT_HOME_PHONE,
 'INFO_TEXT_HOME_PHONE1'   => tep_draw_input_field('phone1', $phone1,'size="30" class="form-control"'),
 'INFO_TEXT_MOBILE'        => INFO_TEXT_MOBILE,
 'INFO_TEXT_MOBILE1'       => tep_draw_input_field('mobile', $mobile,'size="30" class="form-control"'),

 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('TNEF_email_address', $TNEF_email_address,'size="30" class="form-control"'),
 'INFO_TEXT_ADDRESS1'      => INFO_TEXT_ADDRESS1,
 'INFO_TEXT_ADDRESS11'     => tep_draw_input_field('address_1', $address_1,'size="30" class="form-control"'),
 'INFO_TEXT_ADDRESS2'      => INFO_TEXT_ADDRESS2,
 'INFO_TEXT_ADDRESS21'     => tep_draw_input_field('address_2', $address_2,'size="30" class="form-control"',false),
 'INFO_TEXT_COUNTRY'       => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'      => tep_get_country_list('country1',$country1, 'class="form-select"'),
// 'INFO_TEXT_STATE'         => INFO_TEXT_STATE,
 //'INFO_TEXT_STATE1'        => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_id',"zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1',is_numeric($state_value)?'': $state_value,'size="25"',false),
 'INFO_TEXT_CITY'          => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'         => tep_draw_input_field('city1', $city1,'size="30" class="form-control"'),
 'INFO_TEXT_ZIP'           => INFO_TEXT_ZIP,
 'INFO_TEXT_ZIP1'          => tep_draw_input_field('zip', $zip,'size="30" class="form-control"',false),
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
// 'COUNTRY_STATE_SCRIPT'    => country_state($c_name='country1',$c_d_value='Please select a countries...',$s_name='state',$s_d_value='state','zone_id',$state_value),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>$LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
 $template->pparse('contact_list');
}
else
{
 $whereClause="where user_id='".$user_id."' and user_type='".$user_type."'";
 $query1 = "select count(id ) as x1 from ".USER_CONTACT_TABLE." $whereClause";
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];//echo $query1;
 //echo $x1;die();
 ///only for sorting starts
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("title_name",'inserted');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'user_first_name asc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 $see_before_page_number_array=see_before_page_number($sort_array,$field,'user_first_name',$order,'asc',$lower,'0',$higher,'20');
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $totalpage=ceil($x1/$higher);
 $fields="uc.id,uc.user_first_name, uc.user_last_name, uc.user_middle_name,uc.user_email_address, uc.user_address1, uc.user_address2,uc.user_city, uc.user_zip, uc.user_phone, uc.user_mobile,if(uc.user_state_id,z.zone_name,uc.user_state)  as state ,c.".TEXT_LANGUAGE."country_name";
 $query = "select $fields  from ".USER_CONTACT_TABLE." as uc left join ".ZONES_TABLE." as z on ( uc.user_state_id=z.zone_id ) left join ".COUNTRIES_TABLE." as c on ( uc.user_country_id=c.id ) $whereClause ORDER BY $field $order limit $lower,$higher ";
 $result=tep_db_query($query);//echo "<br>$query";//exit;
 $x=tep_db_num_rows($result);//echo $x;exit;
 $pno= ceil($lower+$higher)/($higher);
 if($x > 0 && $x1 > 0)
 {
  $hidden_fields.=tep_draw_hidden_field('contact_id','');
  $alternate=1;
  $rowcount=1;
  while($row =  tep_db_fetch_array($result))
  {

   $ide=$row["id"];
   $user_address="";
   if(tep_not_null($row['user_email_address']))
   $user_address="<a href='mailto:".tep_db_output($row['user_email_address'])."'>".tep_db_output($row['user_email_address'])."</a><br>";

   if(tep_not_null($row['user_address1'])||tep_not_null($row['user_address2']) )
    $user_address.=tep_db_output($row['user_address1']).' '.(tep_not_null($row['user_address2'])?$row['user_address2']:'')."<br>";

   $user_address.=tep_db_output((tep_not_null($row['user_city'])?' '.tep_db_output($row['user_city']):''));
   if(tep_not_null($row['user_city']) && tep_not_null($row['user_zip']))
    $user_address.=', ';
   $user_address.=(tep_not_null($row['user_zip'])?tep_db_output($row['user_zip']):'');
   if(tep_not_null($row['user_city']) && tep_not_null($row['user_zip']))
    $user_address.='<br>';
   //$user_state=(tep_not_null($row['state'])?tep_db_output($row['state']):'');
   //$user_address.=$user_state;
//   if(tep_not_null($user_state) && tep_not_null($row['country_name']))
  //  $user_address.=', ';
   $user_address.=(tep_not_null($row[TEXT_LANGUAGE.'country_name'])?tep_db_output($row[TEXT_LANGUAGE.'country_name']):'');
   if(tep_not_null($row[TEXT_LANGUAGE.'country_name']))
    $user_address.='<br>';
   $user_address.=(tep_not_null($row['user_phone'])?'Phone: '.tep_db_output($row['user_phone'])."<br>":'');
   $user_address.=(tep_not_null($row['user_mobile'])?'Mobile: '.tep_db_output($row['user_mobile'])."<br>":'');

   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $alternate++;
   $template->assign_block_vars('contact_list', array( 'row_selected'   => $row_selected,
                                                       'user_name'      => tep_db_output($row['user_first_name']).' '.tep_db_output($row['user_middle_name']).' '.tep_db_output($row['user_last_name']),
                                                       'user_address'   => $user_address,
                                                       'edit'           => '<a href="#" onclick="set_action1(\'edit\',\''.$ide.'\');">'.TABLE_HEADING_EDIT.'</a>',
                                                       'delete'         => '<a href="#" onclick="set_action1(\'delete\',\''.$ide.'\');">'.TABLE_HEADING_DELETE.'</a>',
                                                       ));
   $lower = $lower + 1;
   $rowcount++;
  }
  see_page_number();
  $template->assign_vars(array('total'=>INFO_RECORDS_FOUND_YOU_HAVE.$x1.INFO_RECORDS_FOUND_CONTACT_LIST));
 }
 else
 {
  $template->assign_vars(array('total'=>INFO_NO_RECORDS_FOUND));
 }
 tep_db_free_result($result);
 tep_db_free_result($result1);
/////////////////////////////////////////////////////////////////////////////
$contact_form=tep_draw_form('contact_list', FILENAME_RECRUITER_CONTACT_LIST, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','');

$template->assign_vars(array(
 'HEADING_TITLE'          => HEADING_TITLE,
 'TABLE_HEADING_USER_NAME'=> TABLE_HEADING_USER_NAME,
 'TABLE_HEADING_ADDRESS'  => TABLE_HEADING_ADDRESS,
 'TABLE_HEADING_EDIT'     => TABLE_HEADING_EDIT,
 'TABLE_HEADING_DELETE'   => TABLE_HEADING_DELETE,
 'add_new'                => "<a class='btn btn-sm btn-primary' href='#' onclick='set_action(\"new\")'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-plus-lg' viewBox='0 0 16 16'>
 <path fill-rule='evenodd' d='M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z'/>
</svg> ".HEADING_TITLE1."</a>",
 'contact_form'           => $contact_form,
 'hidden_fields'          =>  $hidden_fields,
 'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
 'LEFT_BOX_WIDTH'         => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'        => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'              => $LEFT_HTML,
 'RIGHT_HTML'             => RIGHT_HTML,
 'update_message'         => $messageStack->output()));
$template->pparse('contact_list1');
}
?>