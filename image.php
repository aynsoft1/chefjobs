<?
 include_once("include_files.php");

function thumb_nail_create($file_name,$fixed_width,$fixed_height)
{
 list($width,$height,$type,$attr)=getimagesize($file_name);
 //////////// 
 if(isset($_GET['size']) && $_GET['size']>0 )
 {
  $fixed_width=$_GET['size'];
  if($width<$fixed_width) 
   $fixed_width=$width;
  $new_width=($width>$fixed_width)?$fixed_width:$width;
  $ratio=($new_width/$width);
  $new_height=$height*$ratio;
 } //////////////
 else
 {
  $new_width=($width>$fixed_width)?$fixed_width:$width;
  $ratio=($new_width/$width);
  $new_height=($height>$fixed_height)?$fixed_height:$height*$ratio;
 } 
 $contentHeader="Content-type: ";
 $imageType="";
 switch($type)
 {
  case 1://GIF
   $imageType=IMAGETYPE_GIF;
   break;
  case 2://JPEG | JPG
   $imageType=IMAGETYPE_JPEG;
   break;
  case 3://PNG
   $imageType=IMAGETYPE_PNG;
   break;
 }
 $contentHeader.=image_type_to_mime_type($imageType);
 header("$contentHeader");
 $image_p = imagecreatetruecolor($new_width, $new_height);

 $image="";

 switch($type)
 {
  case 1:
   $image = imagecreatefromgif($file_name);
   break;
  case 2:
   $image = imagecreatefromjpeg($file_name);
   break;
  case 3:
   imagealphablending($image_p, false);
  imageSaveAlpha($image_p, true);
   imagecolortransparent($image_p, imagecolorallocate($image_p, 0, 0, 0));
   $image = imagecreatefrompng($file_name);
   break;
 }
 imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
 switch($type)
 {
  case 1:
   imagegif($image_p,null);
   break;
  case 2:
   imagejpeg($image_p,null, 100);
   break;
  case 3:
   imagecolortransparent($image_p, imagecolorallocate($image_p, 0, 0, 0));
   imagepng($image_p,null, 9);
   break;
 }
 imagedestroy($image_p);
}
 $file_name=PATH_TO_MAIN_PHYSICAL.$_GET['image_name'];
$width=(isset($_GET['width']) ? $_GET['width'] : '');
$height=(isset($_GET['height']) ? $_GET['height'] : '');
if(tep_not_null($width))
{
 $width=(int)$width;
}
else
 $width=SMALL_IMAGE_WIDTH;
if(tep_not_null($height))
{
 $height=(int)$height;
}
else
 $height=SMALL_IMAGE_HEIGHT;
if(tep_not_null($_GET['image_name']) && is_file($file_name))
 {
// echo $file_name.":".$width.":".$height;die();
 thumb_nail_create($file_name,$width,$height);
}
?>