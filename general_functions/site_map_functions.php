<?
/*********************************************************
**********#	Name				  : Shambhu Prasad Patnaik		   #******
**********#	Company			: Aynsoft							         #**********
**********#	Copyright (c) www.aynsoft.com 2004	#**********
*********************************************************/
////
/////////////////////////////////////////////
function tep_set_site_map($prevent_page=array())
{
 $query="select id,title,configuration_name,priority,page_url   from ".SITE_MAP_TABLE." where status='active' order by priority, title";
 $query_result = tep_db_query($query);
 $total_record = tep_db_num_rows($query_result);
 $site_map=array();
 if($total_record>0)
 {
  while ($rows = tep_db_fetch_array($query_result)) 
  {
   if(in_array($rows['configuration_name'],$prevent_page))
   continue;   
   switch($rows['configuration_name'])
   {
    case 'SITE_HOME_PAGE':
     $site_map[]=array('title'=>tep_db_output($rows['title']),'link'=>tep_href_link(),'lable'=>1);
     break;
    case 'SITE_ARTICLE_CATEGORIES':
     $category_list=tep_get_article_cat_list();
     $total_catgories=count($category_list); 
     for($i=0;$i<$total_catgories;$i++)
     {
      $site_map[]=array('title'=>tep_db_output($category_list[$i]['name']),'link'=>getPermalink('article_category',array('ide'=>$category_list[$i]['id'])),'lable'=>$category_list[$i]['lable']);
     }
     break;
    case 'SITE_JOB_CATEGORIES':
     $category_list=tep_get_job_cat_list();
     $total_catgories=count($category_list); 
     for($i=0;$i<$total_catgories;$i++)
     {
      $site_map[]=array('title'=>tep_db_output($category_list[$i]['name']),'link'=>getPermalink('category',array('seo_name'=>$category_list[$i]['seo_name'])) ,'lable'=>$category_list[$i]['lable']); //tep_href_link($category_list[$i]['id'].'/'.encode_category($category_list[$i]['name']).'-jobs.html')
     }
     break;
				case 'SITE_JOB_LOCATION':
					$query_state_count=getAnyTableWhereData(ZONES_TABLE,'1','count(*) as count'); 
 				$total_states = $query_state_count['count'];
					$state_count1=20;
     if($total_states>$state_count1)
     $total_states =ceil($total_states/$state_count1);
     else
     $total_states=1;
     $x1=0;
     $lower_limit=0;
     $upper_limit=$state_count1;
     for($z=0;$z<$total_states;$z++)
     {
      $state_result1=tep_db_query("select * from " .ZONES_TABLE." ORDER BY zone_id asc limit $lower_limit ,$upper_limit");
      $lower_limit=$lower_limit+$state_count1;
      while ($state = tep_db_fetch_array($state_result1)) 
      {
       $country_data=getAnytableWhereData(COUNTRIES_TABLE,"id='".$state['zone_country_id']."'",'*');
    			$country_name=get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name',$country_data['id'],'id');
			    $continent_name=get_name_from_table(CONTINENT_TABLE,TEXT_LANGUAGE.'continent_name',$country_data['continent_id'],'id');
       $site_map[]=array('title'=>tep_db_output($state[TEXT_LANGUAGE.'zone_name']),'link'=>tep_href_link(encode_forum($continent_name).'/'.encode_forum($country_name).'/'.encode_forum($state[TEXT_LANGUAGE.'zone_name']).'/'),'lable'=>1);       
      }
      tep_db_free_result($state_result1);
     }
     break;
    case 'SITE_ACTIVE_JOB':
     $now=date('Y-m-d H:i:s');
     $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id ) left outer join '.RECRUITER_TABLE.' as r  on (r.recruiter_id=rl.recruiter_id ) left join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL)';
     $whereClause=" rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
     $query_count=getAnyTableWhereData($table_names,$whereClause,'count(j.job_id) as count ');
     $total_jobs = $query_count['count'];     
     $job_count1=20;
     if($total_jobs>$job_count1)
     $total_jobs =ceil($total_jobs/$job_count1);
     else
     $total_jobs=1;
     $x1=0;
     $lower_limit=0;
     $upper_limit=$job_count1;
     for($c=0;$c<$total_jobs;$c++)
     {
      $query_result1=tep_db_query("select j.job_id,j.job_title from " .$table_names." where ".$whereClause." ORDER BY j.inserted asc limit $lower_limit ,$upper_limit");
      $lower_limit=$lower_limit+$job_count1;
      while ($rows = tep_db_fetch_array($query_result1)) 
      {
       $title_format=encode_category($rows['job_title']);
       $site_map[]=array('title'=>tep_db_output($rows['job_title']),'link'=>getPermalink('job',array('ide'=>$rows['job_id'],'seo_name'=>$title_format)),'lable'=>1);       
      }
      tep_db_free_result($query_result1);
     }
     break;
    case 'SITE_ARTICLE':
     $query_count=getAnyTableWhereData(ARTICLE_TABLE . " as a "," 1 ",'count(a.id) as count ');
     $total_article =(int) $query_count['count'];
     $article_count1=2;
     if($total_article>$article_count1)
     $total_article =ceil($total_article/$article_count1);
     else
     $total_article=1;
     $x1=0;
     $lower_limit=0;
     $upper_limit=$article_count1;
     for($c=0;$c<$total_article;$c++)
     {
      $query_result1=tep_db_query("select id,title,seo_name  from " .ARTICLE_TABLE ." where 1 ORDER BY inserted asc limit $lower_limit ,$upper_limit");
      $lower_limit=$lower_limit+$article_count1;
      while ($rows = tep_db_fetch_array($query_result1)) 
      {
							$ide        = $rows['id'];
							$seo_name   = $rows['seo_name'];
 						$article_url= tep_href_link(get_display_link($ide,$seo_name));
       $site_map[]=array('title'=>tep_db_output($rows['title']),'link'=>$article_url,'lable'=>1);       
      }
      tep_db_free_result($query_result1);
     }
     //die();
     break;
    case 'SITEMAP_FORUM_TOPICS':
     $now=date('Y-m-d H:i:s');
     $table_names=FORUM_TABLE." as f ";
     $whereClause=" f.is_show ='Yes' and f.show_date <= curdate()";
     $query_count=getAnyTableWhereData($table_names,$whereClause,'count(f.id) as count ');
     $total_jobs = $query_count['count'];     
     $job_count1=20;
     if($total_jobs>$job_count1)
     $total_jobs =ceil($total_jobs/$job_count1);
     else
     $total_jobs=1;
     $x1=0;
     $lower_limit=0;
     $upper_limit=$job_count1;
     for($c=0;$c<$total_jobs;$c++)
     {
      $query_result1=tep_db_query("select f.id,f.title from " .$table_names." where ".$whereClause." order by f.inserted asc limit $lower_limit ,$upper_limit");
      $lower_limit=$lower_limit+$job_count1;
      while ($rows = tep_db_fetch_array($query_result1)) 
      {
       $title_format=encode_forum($rows['title']);
       $site_map[]=array('title'=>tep_db_output($rows['title']),'link'=>getPermalink('forum_topics',array('ide'=>$rows['id'],'seo_name'=>$title_format)),'lable'=>1);       
      }
      tep_db_free_result($query_result1);
     }
     break;
    default:
     $page_url = $rows['page_url'];
     if(substr(strtolower($page_url),0,4)!='http')
     $page_url = tep_href_link($rows['page_url']);
      $site_map[]=array('title'=>tep_db_output($rows['title']),'link'=>$page_url,'lable'=>1);
     break;
   }
  }  
 }
 tep_db_free_result($query_result); 
 return $site_map;
}
/////////////////////////////
function tep_built_gz_file($file_name,$file_content='')
{
 if($file_content=='')
  return false;
 if($file_name=='')
  return false;
 if(!is_writable($file_name))
 {
  if(file_exists($file_name))
  return false;
  elseif(!is_writable(dirname($file_name)))
  return false;
 }
 if(!$gz  = gzopen($file_name,"w1"))
  return false;
  gzwrite($gz,$file_content);
  gzclose($gz);
}
function tep_site_map_submission($url)
{
 if(!extension_loaded('curl'))
 {
   if(!function_exists("dl"))
   return false;
   elseif (!dl('php_curl.dll') && !dl('curl.so'))
   return false;
 }  
 if (function_exists('curl_init') ) 
 {
  $ch  = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $output = curl_exec($ch);
  $header=curl_getinfo($ch);
		curl_close($ch);
  $header['output']=$output;
		return $header;
	} 
 else
	return false;
}
function tep_get_article_cat_list()
{
 $query="select c.id, c.category_name  from ".ARTICLE_CATEGORY_TABLE."  as c  where c.sub_cat_id ='' order by category_name asc";
 $query_result = tep_db_query($query);
 $total_record = tep_db_num_rows($query_result);
 $category_array=array();
 if($total_record>0)
 {
  while ($article_category = tep_db_fetch_array($query_result)) 
  {
   $level=1;
   $category_array[]=array('id'=>$article_category['id'],'name'=>$article_category['category_name'],'lable'=>$level);   
   $category_array=tep_get_child_list(ARTICLE_CATEGORY_TABLE,$article_category['id'],$category_array,$level--);
  }  
 }
 return $category_array;
 tep_db_free_result($query_result); 
}
function tep_get_child_list($table_name,$sub_cat_id,$category_array,$level)
{
 //echo $level;
 $string='';
 $query="select id,sub_cat_id,category_name from $table_name where sub_cat_id='".$sub_cat_id."'";
 //echo "<br>$query";//exit;
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 //echo $x;//exit;
 while($row = tep_db_fetch_array($result))
 {
  $cat_id=$row['id'];
 
  $level++;
  $category_array[]=array('id'=>$row['id'],'name'=>$row['category_name'],'lable'=>$level); 
  $category_array=tep_get_child_list($table_name,$cat_id,$category_array,$level--);
 }
 @tep_db_free_result($result);
	return $category_array;
}
function tep_get_job_cat_list()
{
 $query="select c.id, c.category_name,seo_name  from ".JOB_CATEGORY_TABLE."  as c  where c.sub_cat_id  is  null order by category_name asc";
 $query_result = tep_db_query($query);
 $total_record = tep_db_num_rows($query_result);
 $category_array=array();
 if($total_record>0)
 {
  while ($article_category = tep_db_fetch_array($query_result)) 
  {
   $level=1;
   $category_array[]=array('id'=>$article_category['id'],'name'=>$article_category['category_name'],'seo_name'=>$article_category['seo_name'],'lable'=>$level);      
   $category_array=tep_get_child_list(JOB_CATEGORY_TABLE,$article_category['id'],$category_array,$level--);
  }  
 }
 return $category_array;
 tep_db_free_result($query_result); 
}
?>