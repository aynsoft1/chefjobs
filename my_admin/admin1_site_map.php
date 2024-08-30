<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_SITE_MAP);
$template->set_filenames(array('site_map' => 'admin1_site_map.htm'));
include_once(FILENAME_ADMIN_BODY);
include_once("../general_functions/site_map_functions.php");
$action  = (isset($_GET['action']) ? $_GET['action'] : '');
$site_map_id = tep_db_prepare_input($_GET['id']);
if(tep_not_null($site_map_id))
{
 if(!$row_check=getAnyTableWhereData(SITE_MAP_TABLE," id='".tep_db_input($site_map_id)."'",'id,configuration_name'))
 {
  $messageStack->add_session(MESSAGE_ERROR_SITE_MAP, 'error');
  tep_redirect(FILENAME_ADMIN1_SITE_MAP.'?page='.$_GET['page']);
 }
}
if($action!="")
{
 switch ($action)
	{
  case 'ping_sitemap':
     $ping_error=false;
     $lines = file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt');
     $lines=implode(' ',$lines);
     preg_match('/sitemap file : "(.*)"/i',$lines , $file_detail);
     $sitemap_file  = tep_db_prepare_input($file_detail[1]);

     if(file_exists(PATH_TO_MAIN_PHYSICAL.$sitemap_file.'.gz'))
     $sitemap_url= tep_href_link($sitemap_file.'.gz');
     elseif(file_exists(PATH_TO_MAIN_PHYSICAL.$sitemap_file))
     $sitemap_url= tep_href_link($sitemap_file);
     else
     {
      $messageStack->add_session(sprintf(FILE_NOT_EXIST_ERROR,$sitemap_file),'error');
      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,tep_get_all_get_params(array('action','selected_box'))));
     }

     $total_site=count($_POST['ping']);
     $site=array();
     if($total_site<=0)
     {
      $ping_error=true;
      $messageStack->add(PING_SITE_ERROR,'error');
     }
     for($i=0;$i<$total_site;$i++)
     $site[]      = tep_db_prepare_input($_POST['ping'][$i]);

     $yahoo_appid = tep_db_prepare_input($_POST['yahoo_appid']);

     if(in_array('yahoo',$site) && !tep_not_null($yahoo_appid))
     {
      $ping_error=true;
      $messageStack->add(YAHOO_APP_ID_ERROR,'error');
     }
     if(!$ping_error)
     {
      preg_match('/Google : "(.*)"/i',$lines , $google_detail);
      preg_match('/Yahoo : "(.*)"/i',$lines , $yahoo_detail);
      preg_match('/MSN : "(.*)"/i',$lines , $msn_detail);
      preg_match('/Bing.com : "(.*)"/i',$lines , $bing_detail);
      preg_match('/Ask.com : "(.*)"/i',$lines , $ask_detail);
      $google_detail = tep_db_prepare_input($google_detail[1]);
      $yahoo_detail = tep_db_prepare_input($yahoo_detail[1]);
      $msn_detail    = tep_db_prepare_input($msn_detail[1]);
      $bing_detail   = tep_db_prepare_input($bing_detail[1]);
      $ask_detail    = tep_db_prepare_input($ask_detail[1]);
      for($i=0;$i<$total_site;$i++)
      {
       switch($site[$i])
       {
        case 'google':
         $url= 'https://www.google.com/webmasters/tools/ping?sitemap='.urlencode($sitemap_url);
         $header =tep_site_map_submission($url);
         if($header['http_code']==404 || $header['http_code']==403 || $header['http_code']>=500)
         {
          if($google_detail=='')
          $google_detail='error';
          $messageStack->add_session(GOOGLE_NOTIFYING_ERROR,'error');
         }
         else
         {
          $google_detail=date("Y-m-d H:i:s");
          $messageStack->add_session(GOOGLE_NOTIFYING_SUCCESS,'success');
         }
         break;
        case 'yahoo':
         $url= 'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid='.$yahoo_appid.'&url='.urlencode($sitemap_url);
         $header =tep_site_map_submission($url);
         if((400<= $header['http_code'] && $header['http_code'] >=411  )|| $header['http_code']>=500)
         {
          if($yahoo_detail=='')
          $yahoo_detail=$yahoo_appid.'##error';
          $messageStack->add_session(YAHOO_NOTIFYING_ERROR,'error');
         }
         elseif($header['http_code']=='200' && preg_match("/<success /si",$header['output']))
         {
          $yahoo_detail=$yahoo_appid.'##'.date("Y-m-d H:i:s");
          $messageStack->add_session(YAHOO_NOTIFYING_SUCCESS,'success');
         }
         else
         {
          if($yahoo_detail=='')
          $yahoo_detail=$yahoo_appid.'##error';
          $messageStack->add_session(YAHOO_NOTIFYING_ERROR,'error');
         }
         break;
        case 'ask':
         $url= 'http://submissions.ask.com/ping?sitemap='.urlencode($sitemap_url);
         $header =tep_site_map_submission($url);
         if($header['http_code']=='200' && preg_match("/successfully received and added/si",$header['output']))
         {
          $ask_detail=date("Y-m-d H:i:s");
          $messageStack->add_session(ASK_NOTIFYING_SUCCESS,'success');
         }
         else
         {
          if($ask_detail=='')
          $ask_detail='error';
          $messageStack->add_session(ASK_NOTIFYING_ERROR,'error');
         }
         break;
        case 'msn':
         $url= 'http://webmaster.live.com/ping.aspx?siteMap='.urlencode($sitemap_url);
         $header =tep_site_map_submission($url);
         if($header['http_code']=='200' && preg_match("/Thanks for submitting your sitemap/si",$header['output']))
         {
          $msn_detail=date("Y-m-d H:i:s");
          $messageStack->add_session(MSN_NOTIFYING_SUCCESS,'success');
         }
         else
         {
          if($msn_detail=='')
          $msn_detail='error';
          $messageStack->add_session(MSN_NOTIFYING_ERROR,'error');
         }
         break;
        case 'bing':
         $url= 'https://www.bing.com/webmaster/ping.aspx?siteMap='.urlencode($sitemap_url);
         $header =tep_site_map_submission($url);
         if($header['http_code']=='200' && preg_match("/Thanks for submitting your sitemap/si",$header['output']))
         {
          $bing_detail=date("Y-m-d H:i:s");
          $messageStack->add_session(BING_NOTIFYING_SUCCESS,'success');
         }
         else
         {
          if($bing_detail=='')
          $bing_detail='error';
          $messageStack->add_session(BING_NOTIFYING_ERROR,'error');
         }
         break;
       }
      }
      ///////////////////////////////
      $sitemap_content='sitemap file : "'.$sitemap_file.'"'."\n";
      $sitemap_content.='Google : "'.$google_detail.'"'."\n";
      $sitemap_content.='Yahoo : "'.$yahoo_detail.'"'."\n";
      $sitemap_content.='MSN : "'.$msn_detail.'"'."\n";
      $sitemap_content.='Ask.com : "'.$ask_detail.'"'."\n";
      $sitemap_content.='Bing.com : "'.$bing_detail.'"'."\n";
      $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt', "w");
      fwrite($handle,stripslashes($sitemap_content));
      fclose($handle);
      //////////////////////////////////
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,tep_get_all_get_params(array('action','selected_box'))));
     }
   break;
  case 'generate_sitemap':
   $file_error=false;
   $file_name = tep_db_prepare_input($_POST['TRFN_file_name']);
   $file_type = tep_db_prepare_input($_POST['TR_file_type']);
   if(strlen($file_name)<=0)
   {
    $file_error=true;
    $messageStack->add_session(INVALID_FILE_NAME_ERROR, 'error');
   }
   if(preg_match("/^([a-z0-9-_]+)$/",$file_name,$match)==0)
   {
    $file_error=true;
    $messageStack->add_session(INVALID_FILE_NAME_ERROR, 'error');
   }
   if($file_type!='txt' &&  $file_type!='xml')
   {
    $messageStack->add_session(INVALID_FILE_TYPE_ERROR, 'error');
    $file_error=true;
   }

   if(!$file_error)
   {
    switch($file_type)
    {
     case 'xml':
       $content =tep_set_site_map();
       $total_content=count($content);
       $string='<?xml version="1.0" encoding="UTF-8"?>'."\n";
       if(file_exists(PATH_TO_MAIN_PHYSICAL.'sitemap.xsl'))
       {
        /*
        $string.='<?xml-stylesheet type="text/xsl" href="'.tep_href_link('sitemap.xsl').'"?>'."\n";
        //*/
       }
       $string.='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9			    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";
       for($i=0;$i<$total_content;$i++)
       {
        $string.='<url>'."\n";
        $string.='<loc>'.utf8_encode(htmlspecialchars ($content[$i]['link'],ENT_QUOTES)).'</loc>'."\n";
        $string.='</url>'."\n";
       }
       $string.='</urlset>'."\n";
       /// create a site map file  starts //
       $handle = fopen(PATH_TO_MAIN_PHYSICAL.$file_name.'.xml', "w");
       fwrite($handle,stripslashes($string));
       fclose($handle);
       /// create a site map file  ends //
       ////////////////////////
       tep_built_gz_file(PATH_TO_MAIN_PHYSICAL.$file_name.'.xml.gz',stripslashes($string));
       ///////////////////////
       if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt'))
       {
        $lines = file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt');
        preg_match('/sitemap file : "(.*)"/i', $lines[0], $match);
        $old_file_name=$match[1];
       }
       if($old_file_name!=$file_name.'.xml')
       {
        $sitemap_content='sitemap file : "'.$file_name.'.xml"'."\n";
        $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt', "w");
        fwrite($handle,stripslashes($sitemap_content));
        fclose($handle);
       }
      break;
     case 'txt':
       $content =tep_set_site_map();
       $total_content=count($content);
       for($i=0;$i<$total_content;$i++)
       {
        $string.=$content[$i]['link']."\n";
       }
       /// create a site map file  starts //
       $handle = fopen(PATH_TO_MAIN_PHYSICAL.$file_name.'.txt', "w");
       fwrite($handle,stripslashes($string));
       fclose($handle);
       /// create a site map file  ends //
       ////////////////////////
       tep_built_gz_file(PATH_TO_MAIN_PHYSICAL.$file_name.'.txt.gz',stripslashes($string));
       ///////////////////////
       if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt'))
       {
        $lines = file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt');
        preg_match('/sitemap file : "(.*)"/i', $lines[0], $match);
        $old_file_name=$match[1];
       }
       if($old_file_name!= $file_name.'.txt')
       {
        $sitemap_content='sitemap file : "'.$file_name.'.txt"'."\n";
        $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt', "w");
        fwrite($handle,stripslashes($sitemap_content));
        fclose($handle);
       }
      break;
    }
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,tep_get_all_get_params(array('action','selected_box'))));
   }
   break;
  case 'site_map_active':
  case 'site_map_inactive':
   tep_db_query("update ".SITE_MAP_TABLE." set status='".($action=='site_map_active'?'active':'inactive')."' where id='".tep_db_input($site_map_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,tep_get_all_get_params(array('action','selected_box'))));
   break;
  case 'confirm_delete':
   $forbidden_pages=array('SITE_HOME_PAGE','SITE_PAGES','SITE_CATEGORIES','SITE_ARTICLE');
   if(in_array($row_check['configuration_name'],$forbidden_pages))
   {
    $messageStack->add_session(MESSAGE_FORBIDDEN_ERROR, 'error');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page']));
   }
   tep_db_query("delete from " . SITE_MAP_TABLE . " where id = '".$site_map_id."'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page']));
   break;
  case 'save':
   $site_map_title  = tep_db_prepare_input($_POST['TR_page_title']);
   $page_url        = tep_db_prepare_input($_POST['TR_page_url']);
   $priority        = tep_db_prepare_input($_POST['priority']);
   $error           = false;
   $sql_data_array  = array('title'     => $site_map_title,
                             'priority' => $priority,
                             'page_url' => $page_url,
                            );
   if(strlen($site_map_title)<=0)
   {
		  $error           = true;
				$messageStack->add(MESSAGE_NAME_ERROR, 'error');
			}
   else if($row_check=getAnyTableWhereData(SITE_MAP_TABLE,"  title  ='".tep_db_input($site_map_title)."'",'id'))
   {
		  $error           = true;
				$messageStack->add(MESSAGE_NAME_EXIST_ERROR, 'error');
			}
   if(strlen($page_url)<=0)
   {
		  $error           = true;
				$messageStack->add(MESSAGE_PAGE_URL_ERROR, 'error');
			}
   if(!$error)
   {
    tep_db_perform(SITE_MAP_TABLE, $sql_data_array);
    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
				tep_redirect(FILENAME_ADMIN1_SITE_MAP.'?page='.$_GET['page']);
   }
   else
    $action ='new';
		 break;
  case 'update':
   $site_map_title  = tep_db_prepare_input($_POST['TR_page_title']);
   $page_url        = tep_db_prepare_input($_POST['TR_page_url']);
   $priority        = tep_db_prepare_input($_POST['priority']);
   $error           = false;
   $sql_data_array  = array('title'     => $site_map_title,
                             'priority' => $priority,
                             'page_url' => $page_url,
                            );
   if(strlen($site_map_title)<=0)
   {
		  $error           = true;
				$messageStack->add(MESSAGE_NAME_ERROR, 'error');
			}
   else if($row_check=getAnyTableWhereData(SITE_MAP_TABLE," id !='".tep_db_input($site_map_id)."' and title  ='".tep_db_input($site_map_title)."'",'id'))
   {
		  $error           = true;
				$messageStack->add(MESSAGE_NAME_EXIST_ERROR, 'error');
			}

   if(!$error)
   {
    tep_db_perform(SITE_MAP_TABLE, $sql_data_array, 'update', "id = '" . $site_map_id . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
				tep_redirect(FILENAME_ADMIN1_SITE_MAP.'?id='.$site_map_id.'&page='.$_GET['page']);
   }
   else
    $action ='edit';
		 break;
 }
}

{
 ///////////// Middle Values
 $site_map_query_raw="select id,title,status,configuration_name,priority,page_url   from ".SITE_MAP_TABLE."  order by priority, title";
 $site_map_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $site_map_query_raw, $site_map_query_numrows);
 $site_map_query = tep_db_query($site_map_query_raw);

 if(tep_db_num_rows($site_map_query) > 0)
 {
  $alternate=1;
  while ($site_map = tep_db_fetch_array($site_map_query))
  {
   if ((!isset($_GET['id']) || (isset($site_map_id) && ($site_map_id == $site_map['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
   {
    $cInfo = new objectInfo($site_map);
   }
   if ( (isset($cInfo) && is_object($cInfo)) && ($site_map['id'] == $cInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_SITE_MAP . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_SITE_MAP . '?page='.$_GET['page'].'&id=' . $site_map['id'] . '\'"';
   }
   $alternate++;
   if ( (isset($cInfo) && is_object($cInfo)) && ($site_map['id'] == $cInfo->id))
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'page='.$_GET['page'].'&id=' . $site_map['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
   if ($site_map['status'] == 'active')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $site_map['id'] . '&action=site_map_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_MENU_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_MENU_ACTIVE, 28, 22);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_MENU_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, tep_get_all_get_params(array('id','action','selected_box'))).'&id=' . $site_map['id'] . '&action=site_map_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_MENU_ACTIVATE, 28, 22) . '</a>';
   }
   $template->assign_block_vars('site_map', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'name' => tep_db_output($site_map['title']),
    'priority' => tep_db_output($site_map['priority']),
    'status' => $status,
    'row_selected' => $row_selected
    ));
  }
 }
}
//// for right side


$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action)
{
 case 'new':
   $heading[]  = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SITE_MAP.'</div>');
   $contents   = array('form' => tep_draw_form('site_map', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
   $contents[] = array('text' =>'<div class="mb-1 text-danger">' . TEXT_INFO_NEW_INTRO. '</div>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_SITE_MAP_PAGE.'<br>'.tep_draw_input_field('TR_page_title', ($error?$site_map_title:''), 'class="form-control form-control-sm"' ));
   $contents[] = array('text' => '<br>'.TEXT_INFO_SITE_MAP_URL.'<br>'.tep_draw_input_field('TR_page_url', ($error?$page_url:''), 'class="form-control form-control-sm"' ));
   $contents[] = array('text' => '<br>'.TEXT_INFO_PRIORITY.'<br>'.tep_draw_input_field('priority',($error?$priority:'1'),'size="2"'));
   $contents[] = array('align'=> 'left', 'text' => '<br>
   '.tep_draw_submit_button_field('', IMAGE_SAVE,'class="btn btn-primary"').'
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page']).'">'.IMAGE_CANCEL.'</a>');
  break;
 case 'edit':
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SITE_MAP.'</div>');
   $contents   = array('form' => tep_draw_form('site_map', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'id='.$site_map_id.'&page='.$_GET['page'].'&action=update','post',' onsubmit="return ValidateForm(this)"'));
   $contents[] = array('text' =>'<div class="mb-1 text-danger">' .TEXT_INFO_EDIT_INTRO. '</div>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_SITE_MAP_PAGE.'<br>'.tep_draw_input_field('TR_page_title', ($error?$site_map_title:$cInfo->title), 'class="form-control form-control-sm"' ));
   $contents[] = array('text' => '<br>'.TEXT_INFO_SITE_MAP_URL.'<br>'.tep_draw_input_field('TR_page_url', ($error?$page_url:$cInfo->page_url), 'class="form-control form-control-sm"' ));
   $contents[] = array('text' => '<br>'.TEXT_INFO_PRIORITY.'<br>'.tep_draw_input_field('priority',($error?$priority:$cInfo->priority),'size="2"'));
   $contents[] = array('align' => 'left', 'text' => '<br>
   
   '.tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"').'
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page']).'">'.IMAGE_CANCEL.'</a>');
  break;
 case 'delete':
  $heading[]  = array('text' => '<div class="text-primary font-weight-bold mb-1">' .$cInfo->title.'</div>');
  $contents   = array('form' => tep_draw_form('site_map_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'page='.$_GET['page'].'&id='.$cInfo->id.'&action=deleteconfirm'));
  $contents[] = array('text' =>'<div class="mb-1 text-danger">' .TEXT_DELETE_INTRO. '</div>');
  $contents[] = array('text' => '<br><b>' . $cInfo->title. '</b>');
  $contents[] = array('align' => 'left', 'text' => '
  <table>
   <tr>
   <td>       
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'page='.$_GET['page'].'&id='.$cInfo->id.'&action=confirm_delete').'">'.IMAGE_CONFIRM.'</a>
   </td>
   <td>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page'].'&id='.$cInfo->id) . '">' .IMAGE_CANCEL . '</a>
   </td>
   </tr>
   </table>
  ');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SITE_MAP.'</div>');
   $contents[] = array('text' =>'<div class="mb-1 text-danger">' .  tep_db_output($cInfo->title) . '</div>');
   $contents[] = array('align' => 'left', 'text' => '<br>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page'].'&id='.$cInfo->id.'&action=edit').'">'.IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'page='.$_GET['page'].'&id=' . $cInfo->id. '&action=delete') . '">'.IMAGE_DELETE.'</a>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
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
/////
$file_type_array=array();
$file_type_array[]=array('id'=>'xml','text'=>'xml');
$file_type_array[]=array('id'=>'txt','text'=>'txt');

$file_info_array=array();
if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt'))
{
 $lines = file(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN.'sitemap.txt');
 $lines=implode(' ',$lines);
 preg_match('/sitemap file : "(.*)"/i', $lines, $match);
 preg_match('/Google : "(.*)"/i',$lines , $google_detail);
 preg_match('/Yahoo : "(.*)"/i',$lines , $yahoo_detail);
 preg_match('/MSN : "(.*)"/i',$lines , $msn_detail);
 preg_match('/Bing.com : "(.*)"/i',$lines , $bing_detail);
 preg_match('/Ask.com : "(.*)"/i',$lines , $ask_detail);

 $sitemap_file_name=substr($match[1],0,-4);
 $sitemap_file_type=substr($match[1],-3);
 $sitemap_file=tep_db_prepare_input($match[1]);


 if(file_exists(PATH_TO_MAIN_PHYSICAL.$sitemap_file))
 {
  $file_info_array[]='Your <a href="'.tep_href_link($sitemap_file).'" style="color:blue">sitemap</a> was last modified on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a',filemtime(PATH_TO_MAIN_PHYSICAL.tep_db_prepare_input($sitemap_file))).' .</span>';
  $sitemap_url= tep_href_link($sitemap_file);
 }
 if(file_exists(PATH_TO_MAIN_PHYSICAL.$sitemap_file.'.gz'))
 {
  $file_info_array[]=' Your sitemap (<a href="'.tep_href_link($sitemap_file.'.gz').'" style="color:blue">zipped</a>) was last modified on <span style="font-size:11px;font-weight:bold">'.date('M d,Y h:i a',filemtime(PATH_TO_MAIN_PHYSICAL.tep_db_prepare_input($sitemap_file.''))).' .</span>';
  $sitemap_url= tep_href_link($sitemap_file.'.gz');
 }
 if(count($file_info_array)>0)
 {
  $google_detail = tep_db_prepare_input($google_detail[1]);
  $yahoo_detail  = tep_db_prepare_input($yahoo_detail[1]);
  $msn_detail    = tep_db_prepare_input($msn_detail[1]);
  $bing_detail   = tep_db_prepare_input($bing_detail[1]);
  $ask_detail    = tep_db_prepare_input($ask_detail[1]);
  if(tep_not_null($google_detail))
  {
   $ping_google='google';
   $ping_date=$google_detail;
   if($google_detail!='error')
   {
    $ping_date=$google_detail;
    $file_info_array[]='Google last notified  on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a', mktime(substr($ping_date,11,2),substr($ping_date,14,2),substr($ping_date,17,2),substr($ping_date,5,2),substr($ping_date,8,2),substr($ping_date,0,4))).' .</span>';
   }
   else
    $file_info_array[]='<span style="color:#ff0000">There was a problem while notifying Google</span>';
  }
  if(tep_not_null($yahoo_detail))
  {
   $yahoo_detail=explode('##',$yahoo_detail,2);
   $ping_yahoo='yahoo';
   $yahoo_appid=$yahoo_detail[0];
   if($yahoo_detail[1]!='error')
   {
    $ping_date=$yahoo_detail[1];
    $file_info_array[]='Yahoo! last notified  on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a', mktime(substr($ping_date,11,2),substr($ping_date,14,2),substr($ping_date,17,2),substr($ping_date,5,2),substr($ping_date,8,2),substr($ping_date,0,4))).' .</span>';
   }
   else
    $file_info_array[]='<span style="color:#ff0000">There was a problem while notifying Yahoo.</span><a href="'.'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid='.$yahoo_appid.'&url='.urlencode($sitemap_url).'" target="_blank" style="color:blue">View Detail</a>';
  }
  if(tep_not_null($msn_detail))
  {
   $ping_msn='msn';
   if($msn_detail!='error')
   {
    $ping_date=$msn_detail;
    $file_info_array[]='MSN last notified  on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a', mktime(substr($ping_date,11,2),substr($ping_date,14,2),substr($ping_date,17,2),substr($ping_date,5,2),substr($ping_date,8,2),substr($ping_date,0,4))).' .</span>';
   }
   else
     $file_info_array[]='<span style="color:#ff0000">There was a problem while notifying MSN.</span><a href="'.'http://webmaster.live.com/ping.aspx?siteMap='.urlencode($sitemap_url).'" target="_blank" style="color:blue">View Detail</a>';

  }
  if(tep_not_null($ask_detail))
  {
   $ping_ask='ask';
   if($ask_detail!='error')
   {
    $ping_date=$ask_detail;
    $file_info_array[]='Ask.com last notified  on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a', mktime(substr($ping_date,11,2),substr($ping_date,14,2),substr($ping_date,17,2),substr($ping_date,5,2),substr($ping_date,8,2),substr($ping_date,0,4))).' .</span>';
   }
   else
    $file_info_array[]='<span style="color:#ff0000">There was a problem while notifying Ask.com.</span><a href="'.'http://submissions.ask.com/ping?sitemap='.urlencode($sitemap_url).'" target="_blank" style="color:blue">View Detail</a>';
  }
  if(tep_not_null($bing_detail))
  {
   $ping_bing='bing';
   if($bing_detail!='error')
   {
    $ping_date=$bing_detail;
    $file_info_array[]='Bing.com last notified  on <span style="font-size:11px;font-weight:bold">'.date('M d, Y h:i a', mktime(substr($ping_date,11,2),substr($ping_date,14,2),substr($ping_date,17,2),substr($ping_date,5,2),substr($ping_date,8,2),substr($ping_date,0,4))).' .</span>';
   }
   else
    $file_info_array[]='<span style="color:#ff0000">There was a problem while notifying Bing.com.</span><a href="'.'http://www.bing.com/webmaster/ping.aspx?siteMap'.urlencode($sitemap_url).'" target="_blank" style="color:blue">View Detail</a>';
  }
 }


}
$total_info=count($file_info_array);
for($i=0;$i<$total_info;$i++)
{
  $row_selected=' class="dataTableRow'.($alternate%2==0?'1':'2').'"';
  $template->assign_block_vars('site_map_status', array( 'row_selected' => $row_selected,
   'file_status'   => $file_info_array[$i],
   ));
}

$template->assign_vars(array(
 'TABLE_HEADING_SITE_MAP_TITLE' => TABLE_HEADING_SITE_MAP_TITLE,
 'TABLE_HEADING_PRIORITY'       => TABLE_HEADING_PRIORITY,
 'TABLE_HEADING_SITE_MAP_STATUS'=> TABLE_HEADING_SITE_MAP_STATUS,
 'TABLE_HEADING_ACTION'     => TABLE_HEADING_ACTION,
 'count_rows'               => (!is_object($site_map_split))?'':$site_map_split->display_count($site_map_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ITEMS),
 'no_of_pages'              => (!is_object($site_map_split))?'':$site_map_split->display_links($site_map_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 
 'new_button'               => '<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 
 'INFO_TEXT_FILE_NAME'      => tep_draw_input_field('TRFN_file_name',((!$file_error)?$sitemap_file_name:$file_name),'', true),
 'INFO_TEXT_FILE_TYPE'      => tep_draw_pull_down_menu('TR_file_type', $file_type_array, ((!$file_error)?$sitemap_file_type:$file_type),''),
 'generate_form'            => tep_draw_form('site_map', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page'].'&action=generate_sitemap','post',' onsubmit="return ValidateForm(this)"'),
 'INFO_CHECKBOX_PING_GOOGLE'=> tep_draw_checkbox_field('ping[]','google','',$ping_google,'id="ping_google"'),
 'INFO_CHECKBOX_PING_MSN'   => tep_draw_checkbox_field('ping[]','msn','',$ping_msn,'id="ping_msn"'),
 'INFO_CHECKBOX_PING_ASK'   => tep_draw_checkbox_field('ping[]','ask','',$ping_ask,'id="ping_ask"'),
 'INFO_CHECKBOX_PING_YAHOO' => tep_draw_checkbox_field('ping[]','yahoo','',$ping_yahoo,'id="ping_yahoo"'),
 'INFO_CHECKBOX_PING_BING'  => tep_draw_checkbox_field('ping[]','bing','',$ping_bing,'id="ping_bing"'),
 'INFO_TEXT_YAHOO_APPID'    => tep_draw_input_field('yahoo_appid',$yahoo_appid,' size="35"', true),
 'site_map_ping_form'       => tep_draw_form('site_map_ping', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_MAP,'page='.$_GET['page'].'&action=ping_sitemap','post',' onsubmit="return CheckForm()"'),

 'HEADING_TITLE'            => HEADING_TITLE,
 'RIGHT_BOX_WIDTH'          => RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'         => $ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('site_map');
?>