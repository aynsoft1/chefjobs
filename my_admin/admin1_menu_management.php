<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/theme_functions.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_MENU_MANAGEMENT);
$template->set_filenames(array('themes' => 'admin1_menu_management.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_POST['action1']) ? $_POST['action1'] : '');
 if($action!="")
{
 switch ($action)
 {
  case 'set_menu':
      $row_id = tep_db_prepare_input($_POST['row_id']);
	   $data1 =array();
       $total_record =count($row_id);
	   $is_parent ='';
	   $p_count =0;
	  // print_r($_POST);
      //die();
  	   foreach($row_id as   $ide)
	   {
        if($check_row=getAnyTableWhereData(THEME_MENU_TABLE,"id='".tep_db_input($ide)."'"))
		{
         if(tep_not_null($check_row['menu_parent']))
		 {
		  $is_parent =false;
		  $c_count++;
         }
         else
		 {
		  $is_parent =true;
	      $p_count++;
		  $c_count =0;
	     }

         if(isset($_POST['field_'.$ide]))
         $status='active';
		 else
         $status='inactive';

		 $sql_data_array=array( 'priority'  => (($is_parent)?$p_count:$c_count),
                              'status'  => $status,
                              );
         tep_db_perform(THEME_MENU_TABLE, $sql_data_array,'update',"id='".tep_db_input($ide)."'");
   
 		}
 	   }
	   update_theme_menu();
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success'); 
      tep_redirect(FILENAME_ADMIN1_MENU_MANAGEMENT);
	  
   break;
  case 'delete_sub_menu':
     $id = tep_db_prepare_input($_POST['id']);
     $output = tep_db_prepare_input($_POST['output']);
	 tep_db_query("delete from ".THEME_MENU_TABLE." where id='".tep_db_input($id)."'");
	 update_theme_menu();
     if($output=='ajax')
	  die('success');
	 $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(FILENAME_ADMIN1_MENU_MANAGEMENT);

   break;

  case 'delete_menu':
     $id = tep_db_prepare_input($_POST['id']);
     $output = tep_db_prepare_input($_POST['output']);
	 tep_db_query("delete from ".THEME_MENU_TABLE." where menu_parent='".tep_db_input($id)."'");
	 tep_db_query("delete from ".THEME_MENU_TABLE." where id='".tep_db_input($id)."'");
	 update_theme_menu();
     if($output=='ajax')
	  die('success');
	 $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(FILENAME_ADMIN1_MENU_MANAGEMENT);

   break;

  case 'save_menu':
  case 'update_menu':
    $parent = tep_db_prepare_input($_POST['parent']);
    $menu_text = tep_db_prepare_input($_POST['TR_menu_text']);
    $menu_text1 = tep_db_prepare_input($_POST['TR_menu_text1']);
    $menu_link = tep_db_prepare_input($_POST['menu_link']);
    $custom_link = stripslashes($_POST['custom_link']);
    $parameter = stripslashes($_POST['parameter']);
    $user_type = stripslashes($_POST['user_type']);
    $priority = stripslashes($_POST['MR_priority']);
	//print_r($_POST);die();
	$sql_data_array=array('menu_parent'=> (($parent=='')?'null':$parent),
                           'menu_title' => $menu_text,
                           'menu_title1' => $menu_text1,
                           'menu_link' => (($menu_link=='custom')?'##'.$custom_link:$menu_link),
                           'menu_parameter' => $parameter,
                           'user_type' => $user_type,
                           'priority'  => $priority,
                            );
  
	if($action=='update_menu')
	{
     if(tep_not_null($menu_text))
     {
      $ide = tep_db_prepare_input($_POST['id']);
      tep_db_perform(THEME_MENU_TABLE, $sql_data_array,'update',"id='".tep_db_input($ide)."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
     }
	}
	else
	{
     if(tep_not_null($menu_text))
     {
      tep_db_perform(THEME_MENU_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     }
	}
	update_theme_menu();
    tep_redirect(FILENAME_ADMIN1_MENU_MANAGEMENT);
    break;
  case 'add_item':
     echo $popup_box='<script type="text/javascript">
     <!--
		 $("#menu_link").on( "change",function(){
        set_custom_link();
  
   });
  
     //-->
     </script><div class="modal" tabindex="-1" id="myModal"  data-bs-backdrop="static" data-bs-keyboard="false"  >
						<div class="modal-dialog">
						 <div class="modal-content">
						  <div class="modal-header">
							<h3 class="modal-title">'.INFO_TEXT_ADD_MENU_ITEM.'</h3>
							<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
						  </div>
						   <div class="modal-body">
							 <!----->
								'.tep_draw_form('menu', PATH_TO_ADMIN.FILENAME_ADMIN1_MENU_MANAGEMENT,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','save_menu').'
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_PARENT.'</div>
						      <div class="col-md-8">'.LIST_SET_DATA(THEME_MENU_TABLE," where menu_parent is null",'menu_title','id',"menu_title",'name="parent" class="form-select mb-2"'," ",'','').'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_TEXT.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('TR_menu_text', '','placeholder="'.INFO_MENU_TEXT.'" class="form-control mb-2 required"',true).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_TEXT1.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('TR_menu_text1', '','placeholder="'.INFO_MENU_TEXT1.'" class="form-control mb-2 required"',true).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_LINK.'</div>
						      <div class="col-md-8">'.tep_get_menu_link_list().'<div id="custom_link1">'.tep_draw_input_field('custom_link', '','placeholder="'.INFO_CUSTOM_LINK.'" class="form-control mb-2 "',false).'</div></div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_PARAMETER.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('parameter', '','placeholder="'.INFO_MENU_PARAMETER.'" class="form-control mb-2 "',false).'</div>
							 </div>
							 <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_USER.'</div>
						      <div class="col-md-8">'.tep_get_user_list('user_type','').'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_PRIORITY.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('MR_priority', '','placeholder="'.INFO_MENU_PRIORITY.'" class="form-control mb-2 "',false).'</div>
							 </div>
								'.tep_draw_submit_button_field('','Save','class="btn btn-primary float-right"').'
								</form>
							 <!----->
						   </div>
						</div>
					  </div>
					</div>';
					die();   
	   break;
   case 'edit_menu':
     $ide = tep_db_prepare_input($_POST['id']);

	  if(!$check_row=getAnyTableWhereData(THEME_MENU_TABLE,"id='".tep_db_input($ide)."'"))
	 {
	  if($output=='ajax')
	  die('error');
      tep_redirect(FILENAME_ADMIN1_MENU_MANAGEMENT);
	 }
     $menu_link = $check_row['menu_link'];
	 $custom_link='';
	 if(tep_not_null($menu_link))
	 {
      if(substr($menu_link,0,2)=='##') 
	  {
	   $custom_link=substr($menu_link,2);
	   $menu_link='custom';
	  }  
	 }
	 $user_type = $check_row['user_type'];

      echo $popup_box='<script type="text/javascript">
     <!--
		 $("#menu_link").on( "change",function(){
        set_custom_link();
  
   });
  
     //-->
     </script><div class="modal" tabindex="-1" id="myModal"  data-bs-backdrop="static" data-bs-keyboard="false"  >
						<div class="modal-dialog">
						 <div class="modal-content">
						  <div class="modal-header">
							<h3 class="modal-title">'.INFO_TEXT_EDIT_MENU_ITEM.'</h3>
							<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
						  </div>
						   <div class="modal-body">
							 <!----->
								'.tep_draw_form('menu', PATH_TO_ADMIN.FILENAME_ADMIN1_MENU_MANAGEMENT,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('id',$ide).tep_draw_hidden_field('action1','update_menu').'
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_PARENT.'</div>
						      <div class="col-md-8">'.LIST_SET_DATA(THEME_MENU_TABLE," where menu_parent is null",'menu_title','id',"menu_title",'name="parent" class="form-select mb-2"'," ",'',$check_row['menu_parent']).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_TEXT.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('TR_menu_text', tep_db_output($check_row['menu_title']),'placeholder="'.INFO_MENU_TEXT.'" class="form-control mb-2 required"',true).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_TEXT1.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('TR_menu_text1', tep_db_output($check_row['menu_title1']),'placeholder="'.INFO_MENU_TEXT1.'" class="form-control mb-2 required"',true).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_LINK.'</div>
						      <div class="col-md-8">'.tep_get_menu_link_list($menu_link).'<div id="custom_link1">'.tep_draw_input_field('custom_link', $custom_link,'placeholder="'.INFO_CUSTOM_LINK.'" class="form-control mb-2 "',false).'</div></div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_PARAMETER.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('parameter', stripslashes($check_row['menu_parameter']),'placeholder="'.INFO_MENU_PARAMETER.'" class="form-control mb-2 "',false).'</div>
							 </div>
							 <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_USER.'</div>
						      <div class="col-md-8">'.tep_get_user_list('user_type',$user_type).'</div>
							 </div>
						     <div class="row">
						      <div class="col-md-4">'.INFO_TEXT_MENU_PRIORITY.'</div>
						      <div class="col-md-8">'.tep_draw_input_field('MR_priority', tep_db_output($check_row['priority']),'placeholder="'.INFO_MENU_PRIORITY.'" class="form-control mb-2 "',false).'</div>
							 </div>
								'.tep_draw_submit_button_field('','Save','class="btn btn-primary float-right"').'
								</form>
							 <!----->
						   </div>
						</div>
					  </div>
					</div>';
					die("");   
	   break;


 }
}
    $sortable_script='';
    $db_menu_query_raw = "select * from " . THEME_MENU_TABLE . " as m   where menu_parent is null order by  priority asc ";
    //echo $db_menu_query_raw; die();
    $db_menu_split = new splitPageResults($_GET['page'], 50, $db_menu_query_raw, $db_menu_query_numrows);
    $db_menu_query = tep_db_query($db_menu_query_raw);
    $db_menu_num_row = tep_db_num_rows($db_menu_query);
    if($db_menu_num_row > 0)
    {
      $alternate=1;
      while ($menu = tep_db_fetch_array($db_menu_query))
      {
		   $sql = "select * from " . THEME_MENU_TABLE . " as m   where menu_parent ='".tep_db_input($menu['id'])."' order by  priority asc ";
           $result = tep_db_query($sql);
           $num_row = tep_db_num_rows($result);

		   if($num_row>0)
		   {
			$sub_title= '<ul class="sortable" id="sortable_'.$menu['id'].'">';
			if($num_row>1)
            $sortable_script.='jQuery( "#sortable_'.$menu['id'].'" ).sortable({placeholder: "sortable-highlight"});'."\n";
			 while ($data = tep_db_fetch_array($result))
			 {
			  $sub_title.= '<li class="ui-state-default" id="sub_menu_'.$data['id'].'"> <span class="ui-icon ui-icon-arrowthick-2-n-s"></span> '.tep_draw_checkbox_field('field_'.$data['id'],'active','',$data['status']).' '.tep_db_output($data['menu_title']).tep_draw_hidden_field('row_id[]',$data['id']).' 
			                                                                             
																						 <i class="fa fa-trash text-muted" aria-hidden="true" onclick="delete_data(\'sub_menu\','.$data['id'].');" style="float: right;">&nbsp;&nbsp;&nbsp;</i>
																						 <i class="fa fa-pencil-square-o "  onclick="edit_data('.$data['id'].');" style="float: right;">&nbsp;&nbsp;&nbsp;</i></li>';
			 }
			$sub_title.= '</ul>';
		   }
		   else
			$sub_title= '';
 		   $template->assign_block_vars('menu', array( 'checkbox'   => tep_draw_checkbox_field('field_'.$menu['id'],'active','',$menu['status']),
                                                       'title'     =>tep_db_output($menu['menu_title']).tep_draw_hidden_field('row_id[]',$menu['id']),//tep_draw_input_field('field_'.$menu['id'],$menu['menu_title'],'class="form-control"') ,
                                                       'sub_title' => $sub_title,
                                                       'id' => $menu['id'],
										));
 
	  }
	  tep_db_free_result($db_menu_query);
	}

  

 
/////
$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'TABLE_HEADING_VISIBLE'        => TABLE_HEADING_VISIBLE,
 'TABLE_HEADING_TITTLE'			=> TABLE_HEADING_TITTLE,
 'TABLE_HEADING_ORDER'			=> TABLE_HEADING_ORDER,
 'sortable_script'              => $sortable_script,
 'menu_form'   => tep_draw_form('menu', PATH_TO_ADMIN.FILENAME_ADMIN1_MENU_MANAGEMENT,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','set_menu'),
 'button'       => tep_draw_submit_button_field('','Save','class="btn btn-primary float-right"'),//tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
  'new_button'       => tep_draw_button_field('new','Add Menu Item','id="add_new_button" class="btn btn-primary float-right"'),//tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
 'update_message'=>$messageStack->output()));
$template->pparse('themes');
?>