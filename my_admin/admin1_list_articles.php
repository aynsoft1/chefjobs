<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LIST_OF_ARTICLES);
$template->set_filenames(array('article' => 'admin1_list_articles.htm','article1' => 'admin1_article.htm','preview' => 'admin1_article1.htm'));
include_once(FILENAME_ADMIN_BODY);
////////////////
$edit=false;
$error =false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$article_id=(isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
if(tep_not_null($article_id))
{
 if(!$row_check_article_id=getAnyTableWhereData(ARTICLE_TABLE,"id='".tep_db_input($article_id)."'"))
 {
  $messageStack->add_session(MESSAGE_ARTCLE_ERROR, 'error');
  tep_redirect(FILENAME_ADMIN1_LIST_OF_ARTICLES);
 }
 $article_id=$row_check_article_id['id'];
 $edit=true;
}
if(isset($_POST['action1']) && tep_not_null($_POST['action1']))
$action=tep_db_prepare_input($_POST['action1']);
if(tep_not_null($action))
{
 switch($action)
 {
  case 'save_matatags':
   $meta_title          = tep_db_prepare_input($_POST['meta_title']);
   $metatags            = stripslashes($_POST['metatags']);
   $sql_data_array1=array('file_name'=>'article_'.$article_id.'.html',
                          'title'=>$meta_title,
                          'meta_keyword'=>$metatags
                          );
   if($row_meta=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='article_".tep_db_input($article_id).".html'",'id'))
   {
 			tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE, $sql_data_array1,'update',"id='".tep_db_input($row_meta['id'])."'");
   }
   else
   {
 			tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE,$sql_data_array1);
   }
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'confirm_delete':
   if($edit && tep_not_null($row_check_article_id['article_photo']))
   {
    $old_photo= $row_check_article_id['article_photo'];
    if(is_file(PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$old_photo))
    @unlink(PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$old_photo);
   }
   tep_db_query("delete from ".TITLE_KEYWORDMETATYPE_TABLE." where file_name='article_".tep_db_input($article_id).".html'");
   tep_db_query("delete from ".ARTICLE_TABLE." where id='".tep_db_input($article_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(FILENAME_ADMIN1_LIST_OF_ARTICLES);
   break;
  case 'article_active':
  case 'article_inactive':
   tep_db_query("update ".ARTICLE_TABLE." set is_show='".($action=='article_active'?'Yes':'No')."' where id='".tep_db_input($article_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'preview':
    $hidden_fields='';
   	$category_id       = tep_db_prepare_input($_POST['TR_category']);
    $title             = tep_db_prepare_input($_POST['TR_title']);
    $seo_name          = tep_db_prepare_input($_POST['TR_seo_name']);
    $author            = tep_db_prepare_input($_POST['TR_author']);
    $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
    $description       = stripslashes($_POST['description1']);
    $short_description = tep_db_prepare_input($_POST['TR_short_description']);
    $is_show           = tep_db_prepare_input($_POST['show']);

    $hidden_fields.=tep_draw_hidden_field('TR_category',$category_id);
    $hidden_fields.=tep_draw_hidden_field('TR_title',$title);
    $hidden_fields.=tep_draw_hidden_field('TR_seo_name',$seo_name);
    $hidden_fields.=tep_draw_hidden_field('TR_author',$author);
    $hidden_fields.=tep_draw_hidden_field('TR_year',$_POST['TR_year']);
    $hidden_fields.=tep_draw_hidden_field('TR_month',$_POST['TR_month']);
    $hidden_fields.=tep_draw_hidden_field('TR_date',$_POST['TR_date']);
    $hidden_fields.=tep_draw_hidden_field('description1',$description);
    $hidden_fields.=tep_draw_hidden_field('TR_short_description',$short_description);
    $hidden_fields.=tep_draw_hidden_field('show',$is_show);
    if(strlen($short_description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_SHORT_DESCRIPTION, 'error');
     $error=true;
    }
    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
    if(!$error)
    {
     //////// file upload Attachment starts //////
     if(tep_not_null($_FILES['article_photo']['name']))
     {
      if($obj_resume = new upload('article_photo', PATH_TO_MAIN_PHYSICAL_TEMP,'644',array('jpg','gif','png','jpeg')))
      {
       $article_photo_name=tep_db_input($obj_resume->filename);
       $hidden_fields.=tep_draw_hidden_field('article_photo_name',$article_photo_name);
      }
     }
     //////// file upload ends //////
    }
    break;
  case 'back':
   	$category_id       = tep_db_prepare_input($_POST['TR_category']);
    $title             = tep_db_prepare_input($_POST['TR_title']);
    $seo_name          = tep_db_prepare_input($_POST['TR_seo_name']);
    $author            = tep_db_prepare_input($_POST['TR_author']);
    $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
    $description       = stripslashes($_POST['description1']);
    $short_description = tep_db_prepare_input($_POST['TR_short_description']);
    $is_show           = tep_db_prepare_input($_POST['show']);
    $article_photo_name= tep_db_prepare_input($_POST['article_photo_name']);
    if(tep_not_null($article_photo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name) )
    {
     @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name);
    }

    if(strlen($description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
     $error=true;
    }
    if(strlen($short_description)<=0)
    {
     $messageStack->add(ERROR_ARTICLE_SHORT_DESCRIPTION, 'error');
     $error=true;
    }


    break;
  case 'add':
  case 'save':
     $category_id       = tep_db_prepare_input($_POST['TR_category']);
     $title             = tep_db_prepare_input($_POST['TR_title']);
     $seo_name          = tep_db_prepare_input($_POST['TR_seo_name']);
     $author            = tep_db_prepare_input($_POST['TR_author']);
     $show_date         = tep_db_prepare_input($_POST['TR_year']."-".$_POST['TR_month']."-".$_POST['TR_date']);
     $description       = stripslashes($_POST['description1']);
     $short_description = tep_db_prepare_input($_POST['TR_short_description']);
     $is_show           = tep_db_prepare_input($_POST['show']);
     $article_photo_name= tep_db_prepare_input($_POST['article_photo_name']);
     if(strlen($description)<=0)
     {
      $messageStack->add(ERROR_ARTICLE_DESCRIPTION, 'error');
      $error=true;
     }
     if(strlen($short_description)<=0)
     {
      $messageStack->add(ERROR_ARTICLE_SHORT_DESCRIPTION, 'error');
      $error=true;
     }
     if(!$error)
     {
      $sql_data_array=array('category_id'       => $category_id,
                             'title'            => $title,
                             'author'           => $author,
                             'show_date'        => $show_date,
                             'description'      => $description,
                             'short_description'=> $short_description,
                             'is_show'          => $is_show
                            );
       if(tep_not_null($article_photo_name))
       {
        if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name))
        {
         $target_file_name=PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$article_photo_name;
         copy(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name,$target_file_name);
         @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name);
         chmod($target_file_name, 0644);
         $sql_data_array['article_photo']=$article_photo_name;
         if($edit && tep_not_null($row_check_article_id['article_photo']))
         {
          $old_photo= $row_check_article_id['article_photo'];
          if(is_file(PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$old_photo))
          @unlink(PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$old_photo);
         }
        }
       }
       if($edit)
       {
        $sql_data_array['updated']='now()';
        if($seo_url = get_canonical_title($seo_name,$article_id,'article'))
        $sql_data_array['seo_name']=$seo_url;
        tep_db_perform(ARTICLE_TABLE, $sql_data_array,'update',"id='".$article_id."'");
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
       }
       else
       {
        $now=date("Y-m-d H:i:s");
        $sql_data_array['inserted']=$now;
        $seo_url =get_canonical_title($title);
        $sql_data_array['seo_name']= $seo_url;
        tep_db_perform(ARTICLE_TABLE, $sql_data_array);
        $row_id=getAnyTableWhereData(ARTICLE_TABLE," inserted='".tep_db_input($now)."' and title='".tep_db_input($title)."' order by  inserted desc",'id');
        $id = $row_id['id'];
        if($id)
        {
         $meta_description='<meta name="description" content="'.strip_tags(substr($short_description,0,200)).'">';
         $sql_data_array1=array('file_name'   => 'article_'.$id.'.html',
                               'title'       => $title,
                               'meta_keyword'=> $meta_description,
                               );
      			tep_db_perform(TITLE_KEYWORDMETATYPE_TABLE,$sql_data_array1);
        }
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
       }
       tep_redirect(FILENAME_ADMIN1_LIST_OF_ARTICLES);
     }

 }
}
////////////////////////////////////////////
if($error)
{
 if($edit)
  $action='edit';
 else
  $action='new';
}
if($action=='new' || $action=='edit' || $action=='back')
{
 if(tep_not_null($row_check_article_id['article_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_ARTICLE_PHOTO.$row_check_article_id['article_photo']) )
 {
  $artcle_photo1="&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('".HOST_NAME.PATH_TO_ARTICLE_PHOTO.$row_check_article_id['article_photo']."','')\" class='label'>Preview</a>&nbsp;&nbsp;]";
 }

 if($edit)
 {
  $form=tep_draw_form('article', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'id='.$article_id.'&action=preview', 'post', 'enctype="multipart/form-data"   onsubmit="return ValidateForm(this )"');
 }
 else
 {
  $form=tep_draw_form('article', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'action=preview', 'post', ' enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"');
 }
 $view_list_of_articles='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES).'">'.INFO_TEXT_VIEW_LIST_OF_ARTICLES.'</a>';
}
elseif($action=='preview')
{
 if(tep_not_null($article_photo_name) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$article_photo_name) )
 {
  $artcle_photo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_TEMP.$article_photo_name."&size=220");
 }
 elseif(tep_not_null($row_check_article_id['article_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$row_check_article_id['article_photo']) )
 {
  $artcle_photo1=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_ARTICLE_PHOTO.$row_check_article_id['article_photo']."&size=220");
 }
 if($edit)
 {
  $form=tep_draw_form('article', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'id='.$article_id.'&action=save', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a class="btn btn-secondary" href="#" onclick="set_action(\'back\')">'.IMAGE_BACK.'</a> '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
 }
 else
 {
  $form=tep_draw_form('article', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'action=add', 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','');
  $button='<a class="btn btn-secondary" href="#" onclick="set_action(\'back\')">'.IMAGE_BACK.'</a>'.tep_draw_submit_button_field('',IMAGE_SAVE,'class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
 }
 $view_list_of_articles='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES).'">'.INFO_TEXT_VIEW_LIST_OF_ARTICLES.'</a>';
}
else
{
//////////////////
///only for sorting starts
$sort_array=array("a.title","ac.category_name","a.is_show ","a.show_date","a.inserted","a.viewed");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;
// print_r($obj_sort_by_clause->return_sort_array['name']);exit;
//print_r($obj_sort_by_clause->return_sort_array['image']);

///only for sorting ends

 ///////////// Middle Values
 $now=date("Y-m-d H:i:s");
 $article_query_raw="select a.*, ac.id as cat_id, ac.sub_cat_id, ac.category_name from " . ARTICLE_TABLE ." as a," . ARTICLE_CATEGORY_TABLE ." as ac where a.category_id=ac.id order by ".$order_by_clause;//show_date desc, inserted desc";
 $article_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $article_query_raw, $article_query_numrows);
 $article_query = tep_db_query($article_query_raw);
 //echo tep_db_num_rows($article_query);
 if(tep_db_num_rows($article_query) > 0)
 {
  $alternate=1;
  while ($article = tep_db_fetch_array($article_query))
  {
   if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $article['id']))) && !isset($aInfo) && (substr($action, 0, 3) != 'new'))
   {
    $aInfo = new objectInfo($article);
    //print_r($aInfo);
   }
   if ( (isset($aInfo) && is_object($aInfo)) && ($article['id'] == $aInfo->id) )
   {
    // $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_ARTICLES . '?page='.$_GET['page'].'&id=' . $aInfo->id . '&action=edit\'"';
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link($article['seo_name'].'/') . '\' "';
  }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_LIST_OF_ARTICLES . '?page='.$_GET['page'].'&id=' . $article['id'] . '\'"';
  }
   $alternate++;
   if ( (isset($aInfo) && is_object($aInfo)) && ($article['id'] == $aInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page='.$_GET['page'].'&id=' . $article['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   //$row_check=getAnyTableWhereData(ARTICLE_CATEGORY_TABLE,"id='".$article["category_id"]."'","sub_cat_id");
   $category_name='';
   if($article['sub_cat_id']!='')
   {
    $sub_cat_id=$article['sub_cat_id'];
    $sub_category_name_array=array();
    while($sub_cat_id!="")
    {
     $row_sub_cat=getAnyTableWhereData(ARTICLE_CATEGORY_TABLE,"id='".$sub_cat_id."'","category_name,sub_cat_id");
     $sub_cat_id=$row_sub_cat['sub_cat_id'];
     $sub_category_name_array[]=stripslashes($row_sub_cat['category_name']);
    }
    if(count($sub_category_name_array)>0)
    {
     $sub_category_name_array=array_reverse($sub_category_name_array);
     $category_name=implode(" -> ",$sub_category_name_array);
    }
    $category_name=$category_name." -> ".get_name_from_table(ARTICLE_CATEGORY_TABLE,'category_name','id',$article['category_id']);
   }
   else
   {
    $category_name=$article['category_name'];
   }
   if ($article['is_show'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $article['id'] . '&action=article_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_ARTICLE_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_ARTICLE_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_ARTICLE_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $article['id'] . '&action=article_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_ARTICLE_ACTIVATE, 28, 22) . '</a>';
   }
   $views = $article['viewed'];

   $articleTitle = tep_db_output($article['title']);
   if (tep_not_null($article["article_photo"]) && is_file(PATH_TO_MAIN_PHYSICAL . PATH_TO_ARTICLE_PHOTO . $article["article_photo"])) {
     $article_image = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_ARTICLE_PHOTO . $article["article_photo"] . "&size=50", '', '', '', 'class="img-thumbnail resume--result-profile-img"');
   } else {
    $article_image = defaultProfilePhotoUrl($articleTitle, true, 50, 'class="no-pic" id="' . $articleTitle . '"');
   }

   $template->assign_block_vars('article', array( 
    'row_selected' => $row_selected,
    'action' => $action_image,
    'status' => $status,
    'views' => $views,
    'title' => '<a href="'.tep_href_link($article['seo_name']).'/" target="_new">'.$articleTitle.'</a>',
    'article_image' => $article_image,
    'category' => $category_name,
    'show_date' => tep_date_short($article['show_date']),
    'inserted' => tep_date_short($article['inserted']),
    ));
  }
  tep_db_free_result($article_query);
 }
}


//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  // $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  // $contents[] = array('text' => '<b>' . tep_db_output($aInfo->title) . '</b>');
  // $contents[] = array('text' => TEXT_DELETE_INTRO);
  // $contents[] = array('text' => '<br><b>' . tep_db_output($aInfo->title) . '</b>');
  $heading[] = array('text' => '<div>
  <div class="">
  '.TEXT_INFO_HEADING_TITLE.'<p class="font-weight-bold">'.tep_db_output($aInfo->title).'</p></div>
  </div>');
  $contents[] = array('align' => 'left', 'text' => '<div class="">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page=' . $_GET['page'] . '&id=' . $aInfo->id.'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page=' . $_GET['page'] . '&id=' . $aInfo->id) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  break;
 case 'edit_metatags':
  // $heading[]  = array('text' => '<b>'.TEXT_INFO_HEADING_METATAGS.'</b>');
  $heading[] = array('text' => '<div>
  <div class="">'.TEXT_INFO_HEADING_METATAGS.'
  <div class="mb-1">'.tep_db_output('article_'.$aInfo->id.'.html').'</div> 
  </div>
  </div>');
 	$contents=array('form' => tep_draw_form('metatags', PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES,'page='.$_GET['page'].'&id='.$aInfo->id.'&action=save_matatags','post','  onsubmit="return ValidateForm(this)"'));
  
  // $contents[] = array('text' => '<b>' . tep_db_output('article_'.$aInfo->id.'.html') . '</b>');
  if(!$error)
  {
   if($row_meta=getAnyTableWhereData(TITLE_KEYWORDMETATYPE_TABLE,"file_name='article_".tep_db_input($article_id).".html'",'title,meta_keyword'))
   {
    $meta_title=tep_db_output($row_meta['title']);
    $metatags=tep_db_output($row_meta['meta_keyword']);
   }
  }

  // $contents[] = array('text' => '<br>'.INFO_TEXT_META_TITLE.'<br>'.tep_draw_input_field('meta_title', $meta_title, '' ));
 	// $contents[] = array('text' => '<br>'.INFO_TEXT_METATAGS.'<br>'.tep_draw_textarea_field('metatags','','35','7',$metatags,'',"",false));
  // $contents[] = array('align' => 'left', 'text' => '<br>'.tep_draw_submit_button_field('','Save','class="btn btn-primary"').'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page=' . $_GET['page'] . '&id=' . $aInfo->id) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
  
  $contents[] = array('align' => 'left', 'text' => '<div class="">
     <div class="form-group">
        <label>'.INFO_TEXT_META_TITLE.'</label>
        '.tep_draw_input_field('meta_title', $meta_title, 'class="form-control form-control-sm"' ).'
     </div>
     <div class="form-group">
     <label>'.INFO_TEXT_METATAGS.'</label>
     '.tep_draw_textarea_field('metatags','','35','7',$metatags,'class="form-control form-control-sm"',"",false).'
     </div>
     '.tep_draw_submit_button_field('',IMAGE_SAVE,'class="btn btn-primary"').'
     <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page=' . $_GET['page'] . '&id=' . $aInfo->id).'">'.IMAGE_CANCEL.'</a>
  </form>
  </div>');
  break;
 default:
  if (isset($aInfo) && is_object($aInfo))
		{
  //  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_TITLE.'</b>');
  $heading[] = array('text' => '<div class="list-group"><!--<h4>'.TEXT_INFO_HEADING_TITLE.'</h4>--></div>');
  //  $contents[] = array('text' => tep_db_output($aInfo->title));
  //  $contents[] = array('text' => ''.TEXT_INFO_ACTION);   
  $contents[] = array('align' => 'left', 'text' => '<div class="">
  <h4>'.tep_db_output($aInfo->title).'</h4>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'id=' . $aInfo->id . '&action=edit') . '">'
  .IMAGE_EDIT.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'page=' . $_GET['page'] .'&id=' . $aInfo->id . '&action=delete') . '">'
  .IMAGE_DELETE.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'id=' . $aInfo->id . '&action=edit_metatags').'">'
  .IMAGE_METATAGS.'</a>
  <!--<div class="mt-1">'.TEXT_INFO_ACTION.'</div>-->
  </div>');
  //  $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'id=' . $aInfo->id . '&action=edit_metatags').'">'.tep_button('Title/Metatags','class="btn btn-primary"').'</a>');
  


}
  break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
////////////////////////////////////////////////////////////////////////

if(!$error && $action=='edit')
{
 $aInfo       = new objectInfo($row_check_article_id);
 $category_id = $aInfo->category_id;
 $title       = $aInfo->title;
 $author      = $aInfo->author;
 $show_date   = $aInfo->show_date;
 $description = stripslashes($aInfo->description);
 $is_show     = $aInfo->is_show;
 $short_description = tep_db_output($aInfo->short_description);
 $seo_name    = $aInfo->seo_name;
}
elseif(!$error && $action=='new')
{
 $show_date   = date("Y-m-d");
 $is_show     = 'Yes';
}
if($action=='preview')
{
 $template->assign_vars(array(
 'hidden_fields'         => $hidden_fields,
 'INFO_TEXT_TITLE'       => INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'      => tep_db_output($title),
 'INFO_TEXT_SEO_NAME'    => (!$edit)?'':INFO_TEXT_SEO_NAME,
 'INFO_TEXT_SEO_NAME1'   => (!$edit)?'':tep_db_output($seo_name),
 'INFO_TEXT_CATEGORY'    => INFO_TEXT_CATEGORY,
 'INFO_TEXT_CATEGORY1'   => get_name_from_table(ARTICLE_CATEGORY_TABLE,'category_name','id',$category_id),
 'INFO_TEXT_AUTHOR'      => INFO_TEXT_AUTHOR,
 'INFO_TEXT_AUTHOR1'     => tep_db_output($author),
 'INFO_TEXT_SHOW_DATE'   => INFO_TEXT_SHOW_DATE,
 'INFO_TEXT_SHOW_DATE1'  => tep_db_output(formate_date($show_date)),
 'INFO_TEXT_ARTICLE_PHOTO'=>INFO_TEXT_ARTICLE_PHOTO,
 'INFO_TEXT_ARTICLE_PHOTO1'=>$artcle_photo1,
 'INFO_TEXT_DESCRIPTION' => INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=> nl2br(stripslashes($description)),
 'INFO_TEXT_SHOW'        => INFO_TEXT_SHOW,
 'INFO_TEXT_SHOW1'       => tep_db_output($is_show),
 'INFO_TEXT_SHORT_DESCRIPTION' => INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_SHORT_DESCRIPTION1'=> nl2br(stripslashes($short_description)),
 'button'=>$button,
 'form'=>$form,
 'view_list_of_articles'=>$view_list_of_articles,
  ));
 $template->pparse('preview');
}
elseif($action=='new' || $action=='edit' || $action=='back')
{
 $template->assign_vars(array(
 'INFO_TEXT_TITLE'=>INFO_TEXT_TITLE,
 'INFO_TEXT_TITLE1'=>tep_draw_input_field('TR_title',$title,'class="form-control form-control-sm"','',true),
 'INFO_TEXT_SEO_NAME'     => (!$edit)?'':INFO_TEXT_SEO_NAME,
 'INFO_TEXT_SEO_NAME1'    => (!$edit)?'':tep_draw_input_field('TR_seo_name',$seo_name,'class="form-control form-control-sm"',true),
 'INFO_TEXT_SEO_NAME_DES' => (!$edit)?'':INFO_TEXT_SEO_NAME_DES,
 'INFO_TEXT_CATEGORY'=>INFO_TEXT_CATEGORY,
 'INFO_TEXT_CATEGORY1'=>get_drop_down_list1(ARTICLE_CATEGORY_TABLE,'name="TR_category" class="form-control form-control-sm form-select"',"","",$category_id),
 'INFO_TEXT_AUTHOR'=>INFO_TEXT_AUTHOR,
 'INFO_TEXT_AUTHOR1'=>tep_draw_input_field('TR_author',$author,'class="form-control form-control-sm"',true),
 'INFO_TEXT_SHOW_DATE'=>INFO_TEXT_SHOW_DATE,
//  'INFO_TEXT_SHOW_DATE1'=>datelisting($show_date, 'name="TR_date" class="form-control form-control-sm"', 'name="TR_month" class="form-control form-control-sm"', 'name="TR_year" class="form-control form-control-sm"', "2004", date("Y")+1, true),
 'INFO_TEXT_SHOW_DATE1'=>datelisting_admin($show_date, 'name="TR_date" class="form-control form-control-sm d-inline form-select me-2" style="width: 25%"', 'name="TR_month" class="form-control form-control-sm d-inline form-select me-2" style="width: 25%"', 'name="TR_year" class="form-control form-control-sm d-inline form-select" style="width: 25%"', "2004", date("Y")+1, true),
 'INFO_TEXT_ARTICLE_PHOTO'=>INFO_TEXT_ARTICLE_PHOTO,
 'INFO_TEXT_ARTICLE_PHOTO1'=>tep_draw_file_field("article_photo").$artcle_photo1,

 'INFO_TEXT_DESCRIPTION'=>INFO_TEXT_DESCRIPTION,
 'INFO_TEXT_DESCRIPTION1'=>tep_draw_textarea_field('description1', 'soft', '80', '10', stripslashes($description), 'class="form-control form-control-sm"', '', true),
 'INFO_TEXT_SHORT_DESCRIPTION'=>INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_SHORT_DESCRIPTION1'=>tep_draw_textarea_field('TR_short_description', 'soft', '110', '5', stripslashes($short_description), 'class="form-control form-control-sm"', '', true),
 'INFO_TEXT_SHOW'=>INFO_TEXT_SHOW,
 'INFO_TEXT_SHOW1'=>'<div class="form-check form-check-inline">'.tep_draw_radio_field('show', 'Yes', '', $is_show, 'id="radio_show1" class="form-check-input"').'<label for="radio_show1" class="form-check-label">Yes</label></div><div class="form-check form-check-inline">'.tep_draw_radio_field('show', 'No', '', $aInfo->is_show, 'id="radio_show2" class="form-check-input"').'<label for="radio_show2" class="form-check-label">No</label></div>',
 'button'=>tep_draw_submit_button_field('',IMAGE_PREVIEW,'class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif',IMAGE_PREVIEW),
 'form'=>$form,
 'view_list_of_articles'=>$view_list_of_articles,
  ));
 $template->pparse('article1');
}
else
{
 $template->assign_vars(array(
  'TABLE_HEADING_ARTICLE_TITLE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_ARTICLE_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_ARTICLE_CATEGORY'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_ARTICLE_CATEGORY.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_ARTICLE_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_ARTICLE_STATUS.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_ARTICLE_SHOW_DATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_ARTICLE_SHOW_DATE.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_ARTICLE_DATE_ADDED'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][4]."' class='white'>".TABLE_HEADING_ARTICLE_DATE_ADDED.$obj_sort_by_clause->return_sort_array['image'][4]."</a>",
  'TABLE_HEADING_ARTICLE_VIEWS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][5]."' class='white'>".TABLE_HEADING_ARTICLE_VIEWS.$obj_sort_by_clause->return_sort_array['image'][5]."</a>",
  'count_rows'=>$article_split->display_count($article_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ARTICLES),
  'no_of_pages'=>$article_split->display_links($article_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_ARTICLES, 'action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 ));
 $template->pparse('article');
}
?>