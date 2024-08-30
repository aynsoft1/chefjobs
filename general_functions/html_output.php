<?
if(!defined("ENABLE_SSL"))
define('ENABLE_SSL', false);
// The HTML href link wrapper function
function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL')
{
 if ($page == '')
 {
  //die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
 }
 if ($connection == 'NONSSL')
 {
  $link = HOST_NAME ;
 }
 elseif ($connection == 'SSL')
 {
  if (ENABLE_SSL == 'true')
  {
   $link = HOST_NAME ;
  }
  else
  {
   $link = HOST_NAME ;
  }
 }
 else
 {
  die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
 }
 if ($parameters == '')
 {
  $link = $link . $page ;
 }
 else
 {
  $link = $link . $page . '?' . $parameters ;
 }
 while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') )
  $link = substr($link, 0, -1);
 return $link;
}
////
// The HTML image wrapper function
function tep_image($src, $alt = '', $width = '', $height = '', $params = '')
{
 $image = '<img src="' . HOST_NAME .$src . '" border="0" alt="' . $alt . '"';
 if ($alt)
 {
  $image .= ' title=" ' . $alt . ' "';
 }
 if ($width)
 {
  $image .= ' width="' . $width . '"';
 }
 if ($height)
 {
  $image .= ' height="' . $height . '"';
 }
 if ($params)
 {
  $image .= ' ' . $params;
 }
 $image .= '>';
 return $image;
}
function tep_button($name,$params = '')
{
 $button = '<button ';
 if ($params)
 {
  $button .= ' ' . $params;
 }
 $button .= '>'.$name.'</button>';
 return $button;
}

// The HTML form submit button wrapper function
function tep_background_image($src, $alt = '', $width = '', $height = '', $params = '')
{
 $image =  HOST_NAME .$src . '" border="0" alt="' . $alt . '"';
 if ($alt)
 {
  $image .= ' title=" ' . $alt . ' "';
 }
 if ($width)
 {
  $image .= ' width="' . $width . '"';
 }
 if ($height)
 {
  $image .= ' height="' . $height . '"';
 }
 if ($params)
 {
  $image .= ' ' . $params;
 }
 return $image;
}
// Outputs a button in the selected language
function tep_image_submit($image, $alt = '', $parameters = '')
{
  $image_submit = '<input type="image" src="' . HOST_NAME . tep_output_string($image) . '" border="0" alt="' . tep_output_string($alt) . '"';
  if (tep_not_null($alt)) $image_submit .= ' title=" ' . tep_output_string($alt) . ' "';
  if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;
  $image_submit .= '>';

 return $image_submit;
}

////
// Output a separator either through whitespace, or with an image
function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1')
{
 return tep_image($image, '', $width, $height);
}
////
// Output a function button in the selected language
function tep_image_button($image, $alt = '', $params = '')
{
 return tep_image($image, $alt, '', '', $params);
}

// Output a form
function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '')
{
 $form = '<form name="' . tep_output_string($name) . '" action="';
 if (tep_not_null($parameters))
 {
  $form .= getPermalink($action, array('parameters'=>$parameters));
 }
 else
 {
  $form .= getPermalink($action);
 }
 $form .= '" method="' . tep_output_string($method) . '"';
 if (tep_not_null($params))
 {
  $form .= ' ' . $params;
 }
 $form .= '>';
 return $form;
}
////
// Output a form input field
function tep_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true)
{
 $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';
 if (tep_not_null($value))
 {
  $field .= ' value="' . tep_output_string($value) . '"';
 }
 if (tep_not_null($parameters)) $field .= ' ' . $parameters;
  $field .= '>';
 if ($required == true)
  $field .= '';
 return $field;
}
////
// Output a form password field
function tep_draw_password_field($name, $value = '', $required = true, $parameters = '')
{
 $field = tep_draw_input_field($name, $value, $parameters, $required, 'password', false);
 return $field;
}

// Output a form submit button
function tep_draw_submit_button_field($name, $value = '',$parameter='')
{
 $field = tep_draw_input_field($name, $value, $parameter, '', 'submit');
 return $field;
}

// Output a form general button
function tep_draw_button_field($name, $value = '', $parameter='')
{
 $field = tep_draw_input_field($name, $value, $parameter, '', 'button');
 return $field;
}

////
// Output a form filefield
function tep_draw_file_field($name, $required = false)
{
 $field = tep_draw_input_field($name, '', '', $required, 'file');
 return $field;
}

// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
function tep_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '', $parameter = '')
{
 $selection = '<input type="' . $type . '" name="' . $name . '"';
 if ($value != '')
 {
  $selection .= ' value="' . $value . '"';
 }
 if ( ($checked == true) || ($value && ($value == $compare)) )
 {
  $selection .= ' CHECKED';
 }
 if ($parameter != '')
 {
  $selection .= ' ' . $parameter;
 }
 $selection .= '>';
 return $selection;
}

////
// Output a form checkbox field
function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameter = '')
{
 return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare, $parameter);
}

////
// Output a form radio field
function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '', $parameter = '')
{
 return tep_draw_selection_field($name, 'radio', $value, $checked, $compare, $parameter);
}

////
// Output a form textarea field
function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true, $required = false, $additionalParameter = '')
{
 $field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '" '.$additionalParameter.' ';
 if (tep_not_null($parameters)) $field .= ' ' . $parameters;
  $field .= '>';
 if (tep_not_null($text))
 {
  $field .= $text;
 }
 $field .= '</textarea>';
 if ($required == true)
  $field .= '';
 return $field;
}

////
// Output a form hidden field
function tep_draw_hidden_field($name, $value = '', $parameters = '')
{
 $field = '<input type="hidden" name="' . tep_output_string($name) . '"';
 if (tep_not_null($value))
 {
  $field .= ' value="' . tep_output_string($value) . '"';
 }
 if (tep_not_null($parameters)) $field .= ' ' . $parameters;
  $field .= '>';
 return $field;
}

////
// Output a form pull down menu
function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false)
{
 $field = '<select name="' . tep_output_string($name) . '"';
 if (tep_not_null($parameters))
  $field .= ' ' . $parameters;
 $field .= '>';
 for ($i=0, $n=sizeof($values); $i<$n; $i++)
 {
  $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
  if(is_array($default))
  {
   if(in_array($values[$i]['id'],$default))
   {
    $field .= ' SELECTED';
   }
  }
  else
  {
   if($default==$values[$i]['id'])
   {
    $field .= ' SELECTED';
   }
  }
  $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
 }
 $field .= '</select>';
 if ($required == true) $field .= '';;
  return $field;
}


/**
 * Additional function written by Neeraj Tangariya
*/

  // Bootstrap simple submit button
  function tep_button_submit($buttonClass = 'btn btn-primary', $buttonName = 'Submit' , $additionalProperty = Null)
  {
  
    $button_submit = '<button type="submit" class="' . tep_output_string($buttonClass) . '" '.$additionalProperty.'>';

    $button_submit .= tep_output_string($buttonName);

    $button_submit .= '</button>';

  return $button_submit;
  }

 // Output a Link button 
  function tep_link_button_Name($hrefUrl = '', $className = '' , $linkButtonName = '', $additionalProperty = Null)
  {
    $link_button = '<a href="'.$hrefUrl.'" class="'.$className.'" name="'.$linkButtonName.'" '.$additionalProperty.'>'.$linkButtonName.'</a>';
    
    return $link_button;
  }


?>