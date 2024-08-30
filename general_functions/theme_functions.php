<?
/*********************************************************
**********#	Name				  : Shambhu Prasad Patnaik		   #*******
**********#	Company			: Aynsoft							         #**********
**********#	Copyright (c) www.aynsoft.com 2004	#**********
*********************************************************/
////
function get_theme_info($theme_file) 
{
 if(!file_exists($theme_file) || !is_readable($theme_file))
  return 0;
 else
	$theme_data = implode('', file($theme_file));
	preg_match("|Theme Name:(.*)|i", $theme_data, $theme_name);
	preg_match("|Description:(.*)|i", $theme_data, $description);
	preg_match("|Key Feature:(.*)|i", $theme_data, $feature);
 if ( preg_match("|Version:(.*)|i", $theme_data, $version) )
		$version = trim($version[1]);
	else
		$version ='';

	$description = stripslashes(strip_tags(trim($description[1]),'<a><u><b><i><br>'));
	$feature     = stripslashes(strip_tags(trim($feature[1])));
	$name = $theme_name[1];
	$name = trim($name);
	$theme = $name;
	return array('name' => $name, 'description' => $description, 'version' => $version,'feature'=>$feature);
}
function check_theme_info($theme_dir) 
{
 if(is_dir($theme_dir))
 {
  if(!get_theme_info($theme_dir.'/info.txt')) 
   return false;
  else  
  {
   if(!file_exists($theme_dir.'/home.php') || !is_readable($theme_dir.'/home.php'))
   {
    return false;
   }
   elseif(!file_exists($theme_dir.'/text.htm') || !is_readable($theme_dir.'/text.htm'))
   {
    return false;
   }
   else
    return true;
  }
 }
 else
  return false;
}

function get_themes() 
{
	
 $themes = array();
	$broken_themes = array();
 $theme_array=array();
 $theme_root = PATH_TO_MAIN_PHYSICAL_THEMES;
 
	// Files in themes directory//
	$themes_dir = @ dir($theme_root);
	if ( $themes_dir )
 {
		while(($theme_dir = $themes_dir->read()) !== false)
  {
			if (is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir)) 
   {
				if ( $theme_dir == '.' || $theme_dir == '..')// $theme_dir{0} == '.'
    {
					continue;
				}
    $error =false;
    if(!file_exists($theme_root . '/' . $theme_dir.'/text.htm'))
    {
     $error[]='Home Template is missing';
    }			
    if(!file_exists($theme_root . '/' . $theme_dir.'/home.php'))
    {
     $error[]='home.php file is missing';
    }

    if ($error)
    {
					$broken_themes[$theme_dir] = array( 'name' => $theme_dir, 'error' => implode("\n",$error));
				}
    else
    {
     if($theme_info=get_theme_info($theme_root.'/' . $theme_dir.'/info.txt'))
     {
      $themes[$theme_dir] = $theme_info;
     }
     else
      $themes[$theme_dir] =array( 'name' => $theme_dir, 'description' => '', 'version' => '');

     ////////screenshot///////////////
     $screenshot='';
     foreach (array('png', 'gif', 'jpg', 'jpeg') as $ext)
     {
      if (file_exists($theme_root.'/' . $theme_dir."/screenshot.$ext")) 
      {
       $screenshot = "screenshot.$ext";
       break;
      }
     }
     $themes[$theme_dir]['screenshot']=$screenshot;
     //////////////////////////////////////////
    }
			}
		}
  ///////////////////
  $theme_array=array('broken'=>$broken_themes,'theme'=>$themes);
	}
	return $theme_array;
}
function get_site_theme_screen($theme_dir)
{
	$themes_screen = array();
 if($theme_dir=='')
 return  false;
 $theme_root = PATH_TO_MAIN_PHYSICAL.'/'.$theme_dir; 
	// Files in themes directory//
	$themes_dir = @ dir($theme_root);
	if ( $themes_dir )
 {
		while(($theme_dir = $themes_dir->read()) !== false)
  {
			if (is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir)) 
   {
				continue;
			}
   if(preg_match('/^screenshot((\d)+)?\.(gif|jpg|jpeg|png)/i',$theme_dir, $match))
   $themes_screen[]=$match[0];
		}
  return $themes_screen;
  ///////////////////
	}
	return false;
}
?>