<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/

function tep_exit() 
{
 exit();
}
// Redirect to another page or site
function tep_redirect($url) 
{
 header('Location: ' . $url);
 tep_exit();
}
// Parse the data used in the html tags to ensure the tags will not break
function tep_parse_input_field_data($data, $parse) 
{
 return strtr(trim($data), $parse);
}

function tep_output_string($string, $translate = false, $protected = false) 
{
 if ($protected == true) 
 {
  return htmlspecialchars($string);
 } 
 else 
 {
  if ($translate == false) 
  {
   return tep_parse_input_field_data($string, array('"' => '&quot;'));
  } 
  else 
  {
   return tep_parse_input_field_data($string, $translate);
  }
 }
}
function tep_output_string_protected($string) 
{
 return tep_output_string($string, false, true);
}
function tep_sanitize_string($string) 
{
 $patterns = array ('/ +/','/[<>]/');
 $replace = array (' ', '_');
 return preg_replace($patterns, $replace, trim($string));
}
// Return a random row from a database query
function tep_random_select($query) 
{
 $random_product = '';
 $random_query = tep_db_query($query);
 $num_rows = tep_db_num_rows($random_query);
 if ($num_rows > 0) 
 {
  $random_row = tep_rand(0, ($num_rows - 1));
  tep_db_data_seek($random_query, $random_row);
  $random_product = tep_db_fetch_array($random_query);
 }
 return $random_product;
}

////
// Break a word in a string if it is longer than a specified length ($len)
function tep_break_string($string, $len, $break_char = '-') 
{
 $l = 0;
 $output = '';
 for ($i=0, $n=strlen($string); $i<$n; $i++) 
 {
  $char = substr($string, $i, 1);
  if ($char != ' ') 
  {
   $l++;
  } 
  else 
  {
   $l = 0;
  }
  if ($l > $len) 
  {
   $l = 1;
   $output .= $break_char;
  }
  $output .= $char;
 }
 return $output;
}

////
// Return all HTTP GET variables, except those passed as a parameter
function tep_get_all_get_params($exclude_array = '') 
{
 if (!is_array($exclude_array)) 
  $exclude_array = array();
 $get_url = '';
 if (is_array($_GET) && (sizeof($_GET) > 0)) 
 {
  reset($_GET);
  //while (list($key, $value) = each($_GET)) 
  foreach($_GET as $key=> $value)
  {
   if ( (strlen($value) > 0) && ($key != session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) 
   {
    $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
   }
  }
 }
 return $get_url;
}

// Returns the clients browser
function tep_browser_detect($component) 
{
 global $HTTP_USER_AGENT;
 return stristr($HTTP_USER_AGENT, $component);
}

// Wrapper function for round()
function tep_round($number, $precision) 
{
 if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) 
 {
  $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);
  if (substr($number, -1) >= 5) 
  {
   if ($precision > 1) 
   {
    $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
   } 
   elseif ($precision == 1) 
   {
    $number = substr($number, 0, -1) + 0.1;
   } 
   else 
   {
    $number = substr($number, 0, -1) + 1; 
   }
  } 
  else 
  {
   $number = substr($number, 0, -1);
  }
 }
 return $number;
}
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
function tep_date_long($raw_date) 
{
 if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) 
  return false;
 $year = (int)substr($raw_date, 0, 4);
 $month = (int)substr($raw_date, 5, 2);
 $day = (int)substr($raw_date, 8, 2);
 $hour = (int)substr($raw_date, 11, 2);
 $minute = (int)substr($raw_date, 14, 2);
 $second = (int)substr($raw_date, 17, 2);
 return strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
}
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: MM-DD-YYYY
function tep_date_veryshort($raw_date) 
{
 if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) 
  return false;
 $year = (int)substr($raw_date, 0, 4);
 $month = (int)substr($raw_date, 5, 2);
 $day = (int)substr($raw_date, 8, 2);
 $hour = (int)substr($raw_date, 11, 2);
 $minute = (int)substr($raw_date, 14, 2);
 $second = (int)substr($raw_date, 17, 2);
return date("m-d-Y", mktime($hour,$minute,$second,$month,$day,$year));
}

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
function tep_date_short($raw_date) 
{
 if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) 
  return false;
 $year = substr($raw_date, 0, 4);
 $month = (int)substr($raw_date, 5, 2);
 $day = (int)substr($raw_date, 8, 2);
 $hour = (int)substr($raw_date, 11, 2);
 $minute = (int)substr($raw_date, 14, 2);
 $second = (int)substr($raw_date, 17, 2);
 if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) 
 {
  return date(DATE_FORMAT_SHORT, mktime($hour, $minute, $second, $month, $day, $year));
 } 
 else 
 {
  return preg_replace('/2037$/', $year, date(DATE_FORMAT_SHORT, mktime($hour, $minute, $second, $month, $day, 2037)));
 }
}


////
// Parse search string into indivual objects
function tep_parse_search_string($search_str = '', &$objects) 
{
 $search_str = trim(strtolower($search_str));
 // Break up $search_str on whitespace; quoted string will be reconstructed later
 $pieces = preg_split('/[[:space:]]+/',$search_str);
 $objects = array();
 $tmpstring = '';
 $flag = '';
 for ($k=0; $k<count($pieces); $k++) 
 {
  while (substr($pieces[$k], 0, 1) == '(') 
  {
   $objects[] = '(';
   if (strlen($pieces[$k]) > 1) 
   {
    $pieces[$k] = substr($pieces[$k], 1);
   } 
   else 
   {
    $pieces[$k] = '';
   }
  }
  $post_objects = array();
  while (substr($pieces[$k], -1) == ')')  
  {
   $post_objects[] = ')';
   if (strlen($pieces[$k]) > 1) 
   {
    $pieces[$k] = substr($pieces[$k], 0, -1);
   } 
   else 
   {
    $pieces[$k] = '';
   }
  }
  // Check individual words
  if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) 
  {
   $objects[] = trim($pieces[$k]);
   for ($j=0; $j<count($post_objects); $j++) 
   {
    $objects[] = $post_objects[$j];
   }
  } 
  else 
  {
   /* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
   */
   // Add this word to the $tmpstring, starting the $tmpstring
   $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));
   // Check for one possible exception to the rule. That there is a single quoted word.
   if (substr($pieces[$k], -1 ) == '"') 
   {
    // Turn the flag off for future iterations
    $flag = 'off';
    $objects[] = trim($pieces[$k]);
    for ($j=0; $j<count($post_objects); $j++) 
    {
     $objects[] = $post_objects[$j];
    }
    unset($tmpstring);
    // Stop looking for the end of the string and move onto the next word.
    continue;
   }
   // Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
   $flag = 'on';
   // Move on to the next word
   $k++;
   // Keep reading until the end of the string as long as the $flag is on
   while ( ($flag == 'on') && ($k < count($pieces)) ) 
   {
    while (substr($pieces[$k], -1) == ')') 
    {
     $post_objects[] = ')';
     if (strlen($pieces[$k]) > 1) 
     {
      $pieces[$k] = substr($pieces[$k], 0, -1);
     } 
     else 
     {
      $pieces[$k] = '';
     }
    }
    // If the word doesn't end in double quotes, append it to the $tmpstring.
    if (substr($pieces[$k], -1) != '"') 
    {
     // Tack this word onto the current string entity
     $tmpstring .= ' ' . $pieces[$k];
     // Move on to the next word
     $k++;
     continue;
    } 
    else 
    {
     /* If the $piece ends in double quotes, strip the double quotes, tack the
     $piece onto the tail of the string, push the $tmpstring onto the $haves,
     kill the $tmpstring, turn the $flag "off", and return.
     */
     $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));
     // Push the $tmpstring onto the array of stuff to search for
     $objects[] = trim($tmpstring);
     for ($j=0; $j<count($post_objects); $j++) 
     {
      $objects[] = $post_objects[$j];
     }
     unset($tmpstring);
     // Turn off the flag to exit the loop
     $flag = 'off';
    }
   }
  }
 }
 // add default logical operators if needed
 $temp = array();
 for($i=0; $i<(count($objects)-1); $i++) 
 {
  $temp[] = $objects[$i];
  if ( ($objects[$i] != 'and') &&
       ($objects[$i] != 'or') &&
       ($objects[$i] != '(') &&
       ($objects[$i+1] != 'and') &&
       ($objects[$i+1] != 'or') &&
       ($objects[$i+1] != ')') ) 
  {
   $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
  }
 }
 $temp[] = $objects[$i];
 $objects = $temp;
 $keyword_count = 0;
 $operator_count = 0;
 $balance = 0;
 for($i=0; $i<count($objects); $i++) 
 {
  if ($objects[$i] == '(') $balance --;
  if ($objects[$i] == ')') $balance ++;
  if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) 
  {
   $operator_count ++;
  } 
  elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) 
  {
   $keyword_count ++;
  }
 }
 if ( ($operator_count < $keyword_count) && ($balance == 0) ) 
 {
  return true;
 } 
 else 
 {
  return false;
 }
}

////
// Check date
function tep_checkdate($date_to_check, $format_string, &$date_array) 
{
 $separator_idx = -1;
 $separators = array('-', ' ', '/', '.');
 $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
 $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
 $format_string = strtolower($format_string);
 if (strlen($date_to_check) != strlen($format_string)) 
 {
  return false;
 }
 $size = sizeof($separators);
 for ($i=0; $i<$size; $i++) 
 {
  $pos_separator = strpos($date_to_check, $separators[$i]);
  if ($pos_separator != false) 
  {
   $date_separator_idx = $i;
   break;
  }
 }
 for ($i=0; $i<$size; $i++) 
 {
  $pos_separator = strpos($format_string, $separators[$i]);
  if ($pos_separator != false) 
  {
   $format_separator_idx = $i;
   break;
  }
 }
 if ($date_separator_idx != $format_separator_idx) 
 {
  return false;
 }
 if ($date_separator_idx != -1) 
 {
  $format_string_array = explode( $separators[$date_separator_idx], $format_string );
  if (sizeof($format_string_array) != 3) 
  {
   return false;
  }
  $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
  if (sizeof($date_to_check_array) != 3) 
  {
   return false;
  }
  $size = sizeof($format_string_array);
  for ($i=0; $i<$size; $i++) 
  {
   if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') 
    $month = $date_to_check_array[$i];
   if ($format_string_array[$i] == 'dd') 
    $day = $date_to_check_array[$i];
   if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) 
    $year = $date_to_check_array[$i];
  }
 } 
 else 
 {
  if (strlen($format_string) == 8 || strlen($format_string) == 9) 
  {
   $pos_month = strpos($format_string, 'mmm');
   if ($pos_month != false) 
   {
    $month = substr( $date_to_check, $pos_month, 3 );
    $size = sizeof($month_abbr);
    for ($i=0; $i<$size; $i++) 
    {
     if ($month == $month_abbr[$i]) 
     {
      $month = $i;
      break;
     }
    }
   } 
   else 
   {
    $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
   }
  } 
  else 
  {
   return false;
  }
  $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
  $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
 }
 if (strlen($year) != 4) 
 {
  return false;
 }
 if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) 
 {
  return false;
 }
 if ($month > 12 || $month < 1) 
 {
  return false;
 }
 if ($day < 1) 
 {
  return false;
 }
 if (tep_is_leap_year($year)) 
 {
  $no_of_days[1] = 29;
 }
 if ($day > $no_of_days[$month - 1]) 
 {
  return false;
 }
 $date_array = array($year, $month, $day);
 return true;
}
////
// Check if year is a leap year
function tep_is_leap_year($year) 
{
 if ($year % 100 == 0) 
 {
  if ($year % 400 == 0) return true;
 } 
 else 
 {
  if (($year % 4) == 0) return true;
 }
 return false;
}

////
// Return table heading with sorting capabilities
function tep_create_sort_heading($sortby, $colnum, $heading) 
{
 $sort_prefix = '';
 $sort_suffix = '';
 if ($sortby) 
 {
  $sort_prefix = '<a href="' . tep_href_link(basename(strtolower($_SERVER['PHP_SELF'])), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . tep_output_string(TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? TEXT_ASCENDINGLY : TEXT_DESCENDINGLY) . TEXT_BY . $heading) . '" class="productListing-heading">' ;
  $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
 }
 return $sort_prefix . $heading . $sort_suffix;
}

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient,
//                    e.g. jan.wildeboer@gmx.de
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender,
//                    e.g. info@mytepshop.com

function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) 
{
 if (SEND_EMAILS != 'true') return false;
   if (EMAIL_TRANSPORT == 'smtp')
  {
   include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'class-phpmailer.php');
    $mail = new PHPMailer;
	//Enable SMTP debugging. 
	$mail->SMTPDebug = 0;                               
	//Set PHPMailer to use SMTP.
	$mail->isSMTP();            
	//Set SMTP host name                          
	$mail->Host = EMAIL_SMTP_HOST;
	//Set this to true if SMTP host requires authentication to send email
	$mail->SMTPAuth = true;                          
	//Provide username and password     
	$mail->Username = EMAIL_SMTP_USER;
	$pass  =check_data1(EMAIL_SMTP_PASSWORD,"=","mail","pass");
    $mail->Password = $pass;
	//If SMTP requires TLS encryption then set it
	$mail->SMTPSecure = 'tls';                           
	//Set TCP port to connect to 
	$mail->Port = EMAIL_SMTP_PORT;                                   
    $mail->XMailer  = SITE_TITLE;
	if(EMAIL_SMTP_HOST=='smtp.office365.com')
	{
	  $mail->From     = EMAIL_SMTP_USER;
   	  $mail->FromName = $from_email_name;
    }
	else
	{
	  $mail->From     = $from_email_address;
	  $mail->FromName = $from_email_name;
	}

	$mail->addAddress($to_email_address, $to_name);

	$mail->isHTML(true);

	$mail->Subject = $email_subject;
	$mail->Body = $email_text;
	if(!$mail->Send()) 
    {
     //echo $r_message = "Mailer Error: " . $mail->ErrorInfo;
   } 
 }
 else
 {
  // Instantiate a new mail object
  $message = new email(array('X-Mailer: '.SITE_TITLE.' Mailer'));
  // Build the text version
  $text = strip_tags($email_text);
  if (EMAIL_USE_HTML == true) 
  {
   $message->add_html($email_text, $text);
  } 
  else 
  {
   $message->add_text($text);
  }
  // Send message
  $message->build_message();
  $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
 }
}
////

// Get the number of times a word/character is present in a string
function tep_word_count($string, $needle) 
{
 $temp_array = preg_split('/'.$needle.'/',$string);
 return sizeof($temp_array);
}

function tep_create_random_value($length, $type = 'mixed') 
{
 if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;
 $rand_value = '';
 while (strlen($rand_value) < $length) 
 {
  if ($type == 'digits') 
  {
   $char = tep_rand(0,9);
  } 
  else 
  {
   $char = chr(tep_rand(0,255));
  }
  if ($type == 'mixed') 
  {
   if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
  } 
  elseif ($type == 'chars') 
  {
   if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
  } 
  elseif ($type == 'digits') 
  {
   if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
  }
 }
 return $rand_value;
}

function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') 
{
 if (!is_array($exclude)) $exclude = array();
 $get_string = '';
 if (sizeof($array) > 0) 
 {
 // while (list($key, $value) = each($array)) 
   foreach($array as $key=> $value)
  {
   if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) 
   {
    $get_string .= $key . $equals . $value . $separator;
   }
  }
  $remove_chars = strlen($separator);
  $get_string = substr($get_string, 0, -$remove_chars);
 }
 return $get_string;
}
function tep_not_null($value) 
{
 if (is_array($value)) 
 {
  if (sizeof($value) > 0) 
  {
   return true;
  } 
  else 
  {
   return false;
  }
 } 
 else 
 {
  if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) 
  {
   return true;
  } 
  else 
  {
   return false;
  }
 }
}
function tep_string_to_int($string) 
{
 return (int)$string;
}
////
// Return a random value
function tep_rand($min = null, $max = null) 
{
 static $seeded;
 if (!isset($seeded)) 
 {
  mt_srand((double)microtime()*1000000);
  $seeded = true;
 }
 if (isset($min) && isset($max)) 
 {
  if ($min >= $max) 
  {
   return $min;
  } 
  else 
  {
   return mt_rand($min, $max);
  }
 } 
 else 
 {
  return mt_rand();
 }
}
function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) 
{
 setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);
}

function tep_get_ip_address() 
{
 if (isset($_SERVER)) 
 {
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
  {
   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } 
  elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
  {
   $ip = $_SERVER['HTTP_CLIENT_IP'];
  } 
  else 
  {
   $ip = $_SERVER['REMOTE_ADDR'];
  }
 } 
 else 
 {
  if (getenv('HTTP_X_FORWARDED_FOR')) 
  {
   $ip = getenv('HTTP_X_FORWARDED_FOR');
  } 
  elseif (getenv('HTTP_CLIENT_IP')) 
  {
   $ip = getenv('HTTP_CLIENT_IP');
  } 
  else 
  {
   $ip = getenv('REMOTE_ADDR');
  }
 }
 return $ip;
}


// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
function tep_convert_linefeeds($from, $to, $string) 
{
 if ((PHP_VERSION < "4.0.5") && is_array($from)) 
 {
  return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
 } 
 else 
 {
  return str_replace($from, $to, $string);
 }
}

////
//Return 'true' or 'false' value to display boxes and files in index.php and column_left.php
function tep_admin_check_boxes($filename, $boxes='') 
{
 $row=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".$_SESSION['sess_adminid']."'","admin_groups_id");
 $login_groups_id=$row['admin_groups_id'];
 $is_boxes = 1;
 if ($boxes == 'sub_boxes') 
 {
  $is_boxes = 0;
 }
 if($row=getAnyTableWhereData(ADMIN_FILES_TABLE,"FIND_IN_SET( '" . tep_db_input($login_groups_id) . "', admin_groups_id) and admin_files_is_boxes = '" . tep_db_input($is_boxes) . "' and admin_files_name = '" . tep_db_input($filename) . "'","admin_files_id"))
 {
  return true;
 }
 else
 {
  return false;
 }
}
////
  function tep_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }
////
  function tep_remove($source) {
    global $messageStack, $tep_remove_error;

    if (isset($tep_remove_error)) $tep_remove_error = false;

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (is_writeable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (is_writeable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (is_writeable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }
//Check login and file access
function tep_admin_check_login() 
{
 global $messageStack;
 if(!isset($_SESSION['sess_adminid']) || !check_login('admin'))
 {
 	$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
  tep_redirect(FILENAME_INDEX);
 }
 else
 {
  $row=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".$_SESSION['sess_adminid']."'","admin_groups_id");
  $login_groups_id=$row['admin_groups_id'];
  $filename=basename(strtolower($_SERVER['PHP_SELF']));
  if($filename != FILENAME_ERROR && $filename != FILENAME_INDEX && $filename!=FILENAME_ADMIN1_ADMIN_FORBIDDEN && $filename != FILENAME_LOGOUT && $filename != FILENAME_ADMIN1_ACCOUNT && $filename !=FILENAME_ADMIN1_CONTROL_PANEL) 
  {
   if(!$row=getAnyTableWhereData(ADMIN_FILES_TABLE,"FIND_IN_SET( '" . $login_groups_id . "', admin_groups_id) and admin_files_name = '" . $filename . "'","admin_files_name"))
   {
    tep_redirect(FILENAME_ADMIN1_ADMIN_FORBIDDEN);
   }
  }
 }
}
////
// Creates a pull-down list of countries
function tep_get_country_list($name, $selected = '', $parameters = '') 
{
 $countries_array = array(array('id' => '', 'text' => COUNTRY_PULL_DOWN_DEFAULT));
 $countries = tep_get_countries();
 for ($i=0, $n=sizeof($countries); $i<$n; $i++) 
 {
  $countries_array[] = array('id' => $countries[$i]['id'], 'text' => $countries[$i]['text']);
 }
 return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
}
////
// Returns an array with countries
// TABLES: countries
function tep_get_countries($default = '') 
{
 $countries_array = array();
 if ($default) 
 {
  $countries_array[] = array('id' => '',
                             'text' => $default);
 }
 $countries_query = tep_db_query("select id, ".TEXT_LANGUAGE."country_name from " . COUNTRIES_TABLE . " order by ".TEXT_LANGUAGE."country_name");
 while ($countries = tep_db_fetch_array($countries_query)) 
 {
  $countries_array[] = array('id' => $countries['id'],
                             'text' => $countries[TEXT_LANGUAGE.'country_name']);
 }
 return $countries_array;
}

////
// return an array with country zones
function tep_get_country_zones($country_id) 
{
 $zones_array = array();
 $zones_query = tep_db_query("select zone_id, ".TEXT_LANGUAGE."zone_name from " . ZONES_TABLE . " where zone_country_id = '" . (int)$country_id . "' order by ".TEXT_LANGUAGE."zone_name");
 while ($zones = tep_db_fetch_array($zones_query)) 
 {
  $zones_array[] = array('id' => $zones[TEXT_LANGUAGE.'zone_name'],
                         'text' => $zones[TEXT_LANGUAGE.'zone_name']);
 }
 return $zones_array;
}


function tep_prepare_country_zones_pull_down($country_id = '') 
{
 // preset the width of the drop-down for Netscape
 $pre = '';
 if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) 
 {
  for ($i=0; $i<45; $i++) 
   $pre .= '&nbsp;';
 }
 $zones = tep_get_country_zones($country_id);
 if (sizeof($zones) > 0) 
 {
  $zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
  $zones = array_merge($zones_select, $zones);
 } 
 else 
 {
  $zones = array(array('id' => '', 'text' => TYPE_BELOW));
  // create dummy options for Netscape to preset the height of the drop-down
  if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) 
  {
   for ($i=0; $i<9; $i++) 
   {
    $zones[] = array('id' => '', 'text' => $pre);
   }
  }
 }
 return $zones;
}

////
////
//Return files stored in box that can be accessed by user
function tep_admin_files_boxes($filename, $sub_box_name, $parameters="") 
{
 $sub_boxes = '';
 $row=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".$_SESSION['sess_adminid']."'",'admin_groups_id');
 $dbquery = tep_db_query("select admin_files_name from " . ADMIN_FILES_TABLE . " where FIND_IN_SET( '" . $row['admin_groups_id'] . "', admin_groups_id) and admin_files_is_boxes = '0' and admin_files_name = '" . $filename . "'");
 if (tep_db_num_rows($dbquery)) 
 {
  $sub_boxes = '<a href="' . tep_href_link(PATH_TO_ADMIN.$filename.($parameters!=""?"?".$parameters:"")) . '">' . $sub_box_name . '</a>';
 }
 return $sub_boxes;
}
////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('TR_configuration_value', tep_get_countries(), $country_id);
  }
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('TR_configuration_value', false, 35, 5, $text);
  }
  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $zone_class_query = tep_db_query("select geo_zone_id, geo_zone_name from " . GEO_ZONES_TABLE . " order by geo_zone_name");
    while ($zone_class = tep_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
                                  'text' => $zone_class['geo_zone_name']);
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }
  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    global $languages_id;

    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));
    $statuses_query = tep_db_query("select orders_status_id, orders_status_name from " . ORDERS_STATUS_TABLE . " where language_id = '" . (int)$languages_id . "' order by orders_status_name");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      $statuses_array[] = array('id' => $statuses['orders_status_id'],
                                'text' => $statuses['orders_status_name']);
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }
  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TAX_CLASS_TABLE . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }
////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
  function tep_class_exists($class_name) {
    if (function_exists('class_exists')) {
      return class_exists($class_name);
    } else {
      return true;
    }
  }
  function tep_call_function($function, $parameter, $object = '') {
    if ($object == '') {
      return call_user_func($function, $parameter);
    } elseif (PHP_VERSION < 4) {
      return call_user_method($function, $object, $parameter);
    } else {
      return call_user_func(array($object, $function), $parameter);
    }
  }
  function tep_get_zone_class_title($zone_class_id) {
    if ($zone_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select geo_zone_name from " . GEO_ZONES_TABLE . " where geo_zone_id = '" . (int)$zone_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['geo_zone_name'];
    }
  }
  function tep_get_order_status_name($order_status_id, $language_id = '') {
    global $languages_id;

    if ($order_status_id < 1) return TEXT_DEFAULT;

    if (!is_numeric($language_id)) $language_id = $languages_id;

    $status_query = tep_db_query("select orders_status_name from " . ORDER_STATUS_TABLE . " where orders_status_id = '" . (int)$order_status_id . "' and language_id = '" . (int)$language_id . "'");
    $status = tep_db_fetch_array($status_query);

    return $status['orders_status_name'];
  }
  function quote_oanda_currency($code, $base = DEFAULT_CURRENCY) {
    $page = file('http://www.oanda.com/convert/fxdaily?value=1&redirected=1&exch=' . $code .  '&format=CSV&dest=Get+Table&sel_list=' . $base);

    $match = array();

    preg_match('/(.+),(\w{3}),([0-9.]+),([0-9.]+)/i', implode( '',$page), $match);

    if (sizeof($match) > 0) {
      return $match[3];
    } else {
      return false;
    }
  }

  function quote_xe_currency($to, $from = DEFAULT_CURRENCY) {
    $page = file('http://www.xe.net/ucc/convert.cgi?Amount=1&From=' . $from . '&To=' . $to);

    $match = array();

    preg_match('/[0-9.]+\s*' . $from . '\s*=\s*([0-9.]+)\s*' . $to . '/', implode('',$page), $match);

    if (sizeof($match) > 0) {
      return $match[1];
    } else {
      return false;
    }
  }
  function tep_count_modules($modules = '') {
    $count = 0;

    if (empty($modules)) return $count;

    $modules_array = explode(';', $modules);

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

      if (is_object($GLOBALS[$class])) {
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }

    return $count;
  }

  function tep_count_payment_modules() {
    return tep_count_modules(MODULE_PAYMENT_INSTALLED);
  }
////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $currencies;

    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $currencies;

    return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
  }
  function randomize($no_of_chars='7') 
  {
   $salt = "ABCDEFGHIJKLMNOPQRSTUVWXWZabchefghjkmnpqrstuvwxyz0123456789";
   srand((double)microtime()*1000000); 
   $i = 0;
   $pass='';
   while ($i <= $no_of_chars) 
   {
    $num = rand() % 33;
    $tmp = substr($salt, $num, 1);
    $pass.=$tmp;
    $i++;
   }
   return $pass;
  }
function get_drop_down_list($table_name,$parameters,$header="",$header_value="",$selected="")
{
 $string="";
 $level=1;
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='".$header_value."'>".stripslashes($header)."</option>";
 }
 $selected=explode(",",$selected);
 $query="select id,".TEXT_LANGUAGE."category_name from $table_name where sub_cat_id is NULL order by ".TEXT_LANGUAGE."category_name";
	//echo "<br>$query";//exit;
 $result=tep_db_query($query);
	$x=tep_db_num_rows($result);
	//echo $x;//exit;
 if($x > 0)
 {
  while($row = tep_db_fetch_array($result))
  {
   $cat_id=$row['id'];
   $string.="<option value='".htmlspecialchars($cat_id,ENT_QUOTES)."'";
   if(in_array($cat_id,$selected))
   {
    $string.=" selected";
   }
   $string.=">".stripslashes($row[TEXT_LANGUAGE.'category_name'])."</option>";  
   $query1="select id,".TEXT_LANGUAGE."category_name from $table_name where sub_cat_id='".$cat_id."'";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $x1=tep_db_num_rows($result1);
   //echo $x1;//exit;
   while($row1 = tep_db_fetch_array($result1))
   {
    $cat_id=$row1['id'];
    $string.="<option value='".htmlspecialchars($cat_id,ENT_QUOTES)."'";
    if(in_array($cat_id,$selected))
    {
     $string.=" selected";
    }
    $string.=">".draw_char($level).stripslashes($row1[TEXT_LANGUAGE.'category_name'])."</option>";
    //echo "<br>".$cat_id;//exit;
    $string.=repeat_drop_down_list($table_name,$cat_id,$selected,$level);
   }
  }
 	$string.="</select>";
 }
 @tep_db_free_result($result);
 @tep_db_free_result($result1);
	return $string;
}
function get_drop_down_list1($table_name,$parameters,$header="",$header_value="",$selected="")
{
 $string="";
 $level=1;
 $string.="<select ".$parameters.">";
 if($header!="")
 {
  $string.="<option value='".$header_value."'>".stripslashes($header)."</option>";
 }
 $selected=explode(",",$selected);
 $query="select id,".TEXT_LANGUAGE."category_name from $table_name where sub_cat_id =''";
	//echo "<br>$query";//exit;
 $result=tep_db_query($query);
	$x=tep_db_num_rows($result);
	//echo $x;//exit;
 if($x > 0)
 {
  while($row = tep_db_fetch_array($result))
  {
   $cat_id=$row['id'];
   $string.="<option value='".htmlspecialchars($cat_id,ENT_QUOTES)."'";
   if(in_array($cat_id,$selected))
   {
    $string.=" selected";
   }
   $string.=">".stripslashes($row[TEXT_LANGUAGE.'category_name'])."</option>";  
   $query1="select id,".TEXT_LANGUAGE."category_name from $table_name where sub_cat_id='".$cat_id."'";
   //echo "<br>$query1";//exit;
   $result1=tep_db_query($query1);
   $x1=tep_db_num_rows($result1);
   //echo $x1;//exit;
   while($row1 = tep_db_fetch_array($result1))
   {
    $cat_id=$row1['id'];
    $string.="<option value='".htmlspecialchars($cat_id,ENT_QUOTES)."'";
    if(in_array($cat_id,$selected))
    {
     $string.=" selected";
    }
    $string.=">".draw_char($level).stripslashes($row1[TEXT_LANGUAGE.'category_name'])."</option>";
    //echo "<br>".$cat_id;//exit;
    $string.=repeat_drop_down_list($table_name,$cat_id,$selected,$level);
   }
  }
 	$string.="</select>";
 
 @tep_db_free_result($result);
 @tep_db_free_result($result1);
	return $string;
 }}
function repeat_drop_down_list($table_name,$sub_cat_id,$selected,$level)
{
 //echo $level;
 $string='';
 $query="select id,sub_cat_id,".TEXT_LANGUAGE."category_name from $table_name where sub_cat_id='".$sub_cat_id."'";
 //echo "<br>$query";//exit;
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 //echo $x;//exit;
 while($row = tep_db_fetch_array($result))
 {
  $cat_id=$row['id'];
  //echo "<br>".$cat_id;
  $sub_cat_id=$row['sub_cat_id'];
  $string.="<option value='".htmlspecialchars($cat_id,ENT_QUOTES)."'";
  if(in_array($cat_id,$selected))
  {
   $string.=" selected";
  }
  $level++;
  $string.=">".draw_char($level).stripslashes($row[TEXT_LANGUAGE.'category_name'])."</option>";
  $string.=repeat_drop_down_list($table_name,$cat_id,$selected,$level--);
 }
 @tep_db_free_result($result);
	return $string;
}
function draw_char($level,$char='---')
{
	return str_repeat($char,$level);
}
////
////
// Return true if the category has subcategories
// TABLES: article categories
function tep_has_category_subcategories($category_id) 
{
 $child_category_query = tep_db_query("select count(*) as count from " . ARTICLE_CATEGORY_TABLE . " where sub_cat_id = '" . (int)$category_id . "'");
 $child_category = tep_db_fetch_array($child_category_query);
 if ($child_category['count'] > 0) 
 {
  return true;
 } 
 else 
 {
  return false;
 }
}
////

////
function tep_get_categories($table_name, $categories_array = '', $parent_id = '', $indent = '') 
{
 if (!is_array($categories_array)) 
  $categories_array = array();
 if($parent_id=='')
  $whereClause="sub_cat_id is NULL";
 else
  $whereClause="sub_cat_id = '" . $parent_id . "'";
 $categories_query = tep_db_query("select id, sub_cat_id, ".TEXT_LANGUAGE."category_name from $table_name where $whereClause order by  ".TEXT_LANGUAGE."category_name,priority");
 while ($categories = tep_db_fetch_array($categories_query)) 
 {
  $categories_array[] = array('id' => $categories['id'],
                              'text' => $indent . $categories[TEXT_LANGUAGE.'category_name']);

  if ($categories['id'] != $parent_id) 
  {
   $categories_array = tep_get_categories($table_name, $categories_array, $categories['id'], $indent . '--');
  }
 }
 return $categories_array;
}
function tep_get_diving_main_categories($table_name) 
{
 $whereClause="sub_cat_id is NULL";
 $categories_query = tep_db_query("select id from $table_name where $whereClause");
 while ($categories = tep_db_fetch_array($categories_query)) 
 {
  $sub_cat_id=$categories['id'];
  $sub_categories_query = tep_db_query("select id,category_name from $table_name where sub_cat_id='".$sub_cat_id."' order by priority, category_name");
  while ($sub_categories = tep_db_fetch_array($sub_categories_query)) 
  {
   $categories_array[] = array('id' => $sub_categories['id'],
                               'text' =>$sub_categories['category_name']);
  }
 }
 return $categories_array;
}
function js_popup($path_with_filename,$window_title=SITE_TITLE)
{
	$path_with_filename=HOST_NAME.$path_with_filename;
 if(trim($path_with_filename)=='') 
  return '';
 else
  return "javascript:popupimage('".$path_with_filename."','".str_replace(array("'","\""),array("\'","\'"),$window_title)."')";
}
function tep_get_caption($table=CONFIGURATION_TABLE,$id_field='id',$name_field='name',$where='',$order_by='',$default = '') 
{
 $caption_array = array();
 if ($default) 
 {
  $caption_array[] = array('id' => '',
                             'text' => $default);
 }
 $where=($where?"where $where":'');
 $order_by=($order_by?"order by $order_by":'');
 $temp_query="select $id_field,$name_field from $table $where $order_by";
 //echo $temp_query;
 $caption_query = tep_db_query($temp_query);
 
 while ($caption = tep_db_fetch_array($caption_query)) 
 {
  $caption_array[] = array('id' => $caption[$id_field],
                             'text' => $caption[$name_field]);
 }
 tep_db_free_result($caption_query);
 return $caption_array;
}
///////////////
function tep_get_category_type_list($name, $selected = '', $parameters = '') 
{
 $category_array = array(array('id' => '', 'text' => CATEGORY_PULL_DOWN_DEFAULT));
 $categories = tep_get_caption(JOB_CATEGORY_TABLE,'id',TEXT_LANGUAGE.'category_name','',TEXT_LANGUAGE.'category_name asc');
 for ($i=0, $n=sizeof($categories); $i<$n; $i++) 
 {
  $category_array[] = array('id' => $categories[$i]['id'], 'text' => $categories[$i]['text']);
 }
 return tep_draw_pull_down_menu($name, $category_array, $selected, $parameters);
}
///////////////
function tep_get_license_type_list($name, $selected = '', $parameters = '') 
{
 $category_array = array(array('id' => '', 'text' => LICENSE_PULL_DOWN_DEFAULT));
 $categories = tep_get_caption(LICENSES_TYPE_TABLE,'id','licenses_type_name','','id asc');
 for ($i=0, $n=sizeof($categories); $i<$n; $i++) 
 {
  $category_array[] = array('id' => $categories[$i]['id'], 'text' => $categories[$i]['text']);
 }
 return tep_draw_pull_down_menu($name, $category_array, $selected, $parameters);
}
///////////////
function tep_get_job_type_list($name, $selected = '', $parameters = '') 
{
 $category_array = array(array('id' => '', 'text' => JOB_TYPE_PULL_DOWN_DEFAULT));
 $categories = tep_get_caption(JOB_TYPE_TABLE,'id',TEXT_LANGUAGE.'type_name','','id asc');
 for ($i=0, $n=sizeof($categories); $i<$n; $i++) 
 {
  $category_array[] = array('id' => $categories[$i]['id'], 'text' => $categories[$i]['text']);
 }
 return tep_draw_pull_down_menu($name, $category_array, $selected, $parameters);
}
///////////////
///////////////
function check_parent_category($table_name,$id,$sub_category_id)
{
 $sub_category_id;
 $parent_id=$id;
 if($sub_category_id==$parent_id)
 return false;
 $category_array=array();
 $category_array1=tep_get_child_category($table_name,$parent_id);
 $category_array =array_merge($category_array,$category_array1); 
 $total_child=count($category_array1);
 $category_array1=implode(',',$category_array1);
 while($total_child)
 {
  $category_array1=tep_get_child_category($table_name,$category_array1);
  $category_array =array_merge($category_array,$category_array1); 
  $total_child=count($category_array1);
  $category_array1=implode(',',$category_array1);
 }
 if(in_array($sub_category_id,$category_array))
  return false;
 else
   return true;
}
function tep_get_child_category($table_name,$parent_id)
{
 $temp_query="select id  from $table_name where  sub_cat_id in (".$parent_id.")";
 $result = tep_db_query($temp_query);
 $category_array=array();
 while ($row = tep_db_fetch_array($result)) 
 {
  $category_array[]= $row['id'];
 }
 tep_db_free_result($result);
 return $category_array;
}
function valid_html_link($text)
{
 ######### tld #########
	$tlds = file(PATH_TO_MAIN_PHYSICAL . 'tld.txt');
	//while (list(,$line) = each($tlds)) 
	foreach ($tlds as $line) 
 {
		//echo $text;
  // Get rid of comments
  $words = explode('#', $line);
	 $tld.= $words[0];
 }
 //echo $tld;
 $tld_array=explode(' ',$tld);
 $check_element=array();
	for($i=0;$i<count($tld_array);$i++)
	{
  if($tld_array[$i]!='')
		{
			$check_element[]=$tld_array[$i];
		}
	}
 ######### tld #########


	$text_nl2br=nl2br($text);
	if(strstr($text_nl2br,'<br />'))
   $text= str_replace("<br />"," ",$text_nl2br);
	$text_array=explode(" ",$text);
 //echo $text;
	$link_count=0;
	for($j=0;$j<count($check_element);$j++)
	{
		for($k=0;$k<count($text_array);$k++)
		{
			if(strstr($text_array[$k],'.'.$check_element[$j]))
			{
						if(substr($text_array[$k],0,7)=="http://")
							$text1=str_replace($text_array[$k],"<a href='".str_replace('<br />','',$text_array[$k])."' target='_blank' >".$text_array[$k]."</a>",$text_array[$k]);
						elseif(substr($text_array[$k],0,8)=="https://")
							$text1=str_replace($text_array[$k],"<a href='".str_replace('<br />','',$text_array[$k])."' target='_blank' >".$text_array[$k]."</a>",$text_array[$k]);
						else
							$text1=str_replace($text_array[$k],"<a href='http://".str_replace('<br />','',$text_array[$k])."' target='_blank'>".$text_array[$k]."</a>",$text_array[$k]);
						$text= str_replace($text_array[$k],$text1,nl2br($text));
						$link_count ++ ;
			}
		}
	}
 //echo $text;
	if($link_count>0)
	{
		$text=($text);
	}
	else
	{
  $text=nl2br($text);
	}
 return $text;
}
function tep_new_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address,$attachment='',$attach_file_name='' )
{
 if (SEND_EMAILS != 'true') return false;
  if (EMAIL_TRANSPORT == 'smtp')
  {  
	 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'class-phpmailer.php');
	 $message = new PHPMailer;
	 $message->SMTPDebug = 0;                               
	 $message->isSMTP();            
	 $message->Host = EMAIL_SMTP_HOST;
	 $message->SMTPAuth = true;                          
	 $message->Username = EMAIL_SMTP_USER;  
	 $pass  =check_data1(EMAIL_SMTP_PASSWORD,"=","mail","pass");
	 $message->Password = $pass;                           
	 $message->SMTPSecure = 'tls';                           
	 //Set TCP port to connect to 
	 $message->Port = EMAIL_SMTP_PORT;                                   
	 $message->XMailer  = SITE_TITLE;
	 if(EMAIL_SMTP_HOST=='smtp.office365.com')
	 {
	  $message->From     = EMAIL_SMTP_USER;
	  $message->FromName = $from_email_name;
	 }
	 else
	 {
	  $message->From     = $from_email_address;
	  $message->FromName = $from_email_name;
	 }
	 if(tep_not_null($attachment) && tep_not_null($attach_file_name))
	 {
	  $message->addAttachment($attachment,$attach_file_name);
	 }
	 $message->addAddress($to_email_address, $to_name);
	 if (EMAIL_USE_HTML == 'true') 
	 {
	  $message->isHTML(true);
	  $message->Body = $email_text;
	 }
	 else
	 {
	  $text = strip_tags($email_text);
      $message->isHTML(false);
	  $message->AltBody   = $text;	
	 }
	 $message->Subject = $email_subject;
	 $message->Send(); 
  }
  else
  {
	  // Instantiate a new mail object
	  $message = new email(array('X-Mailer: '.SITE_TITLE.' Mailer'));
	  // Build the text version
	  $text = strip_tags($email_text);
	  if (EMAIL_USE_HTML == true) 
	  {
	   $message->add_html($email_text, $text);
	  } 
	  else 
	  {
	   $message->add_text($text);
	  }
	  if(tep_not_null($attachment) && tep_not_null($attach_file_name))
 	  {
	   $handle = fopen($attachment, "r");
	   $contents = fread($handle, filesize($attachment));
	   fclose($handle);
	   $message->add_attachment($contents,$attach_file_name);
	  }
	  // Send message
	  $message->build_message();
	  $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }
}
// Creates a pull-down list of cities
function get_city_dropdown_list($country_id, $parameters, $header = "", $header_value = "", $selected = "") {
  $string = "";
  $result = null; // Initialize result to null
  // Start the select element
  $string .= "<select " . $parameters . ">";

  // Check if country_id is null or empty
  if ((is_null($country_id) || $country_id === "") || (is_null($selected) || $selected === "")) {
      $string .= "<option value=''>" . htmlspecialchars($header, ENT_QUOTES) . "</option>";
  } else {
      // Query to get cities based on country_id
      $query = "SELECT city_id, city_name FROM cities WHERE city_zone_id = '" . intval($country_id) . "' ORDER BY city_name";
      $result = tep_db_query($query);

      if ($result && tep_db_num_rows($result) > 0) {
          while ($row = tep_db_fetch_array($result)) {
              $city_id = $row['city_id'];
              $city_name = stripslashes($row['city_name']);

              // Generate options
              $string .= "<option value='" . htmlspecialchars($city_name, ENT_QUOTES) . "'";
              if (is_string($selected)) {
              $selected = explode(',', $selected); // Convert the string into an array
              }
              if (in_array($city_name, $selected)) {
              $string .= " selected";
              }
              $string .= ">" . htmlspecialchars($city_name, ENT_QUOTES) . "</option>";
          }
      } else {
          $string .= "<option value=''>No cities available</option>";
      }
  }

  $string .= "</select>";

  if ($result) {
      @tep_db_free_result($result); // Free result if it was successfully created
  }

  return $string;
}

function get_sub_category_drop_down_list($table_name, $parameters, $header = "", $header_value = "", $selected = "")
{
    $string = "";
    $string .= "<select " . $parameters . ">";
    
    // Add a header option if provided
    if ($header != "") {
        $string .= "<option value='" . $header_value . "'>" . stripslashes($header) . "</option>";
    }
    
    // Convert the selected values to an array
    $selected = explode(",", $selected);
    
    // Query to get all sub-categories from the table
    $query = "SELECT id, " . TEXT_LANGUAGE . "sub_category_name 
              FROM $table_name 
              WHERE job_category_id IS NOT NULL 
              ORDER BY " . TEXT_LANGUAGE . "sub_category_name";
    
    $result = tep_db_query($query);
    $x = tep_db_num_rows($result);
    
    if ($x > 0) {
        // Loop through each sub-category
        while ($row = tep_db_fetch_array($result)) {
            $sub_cat_id = $row['id'];
            $string .= "<option value='" . htmlspecialchars($sub_cat_id, ENT_QUOTES) . "'";
            
            // Mark as selected if it matches the selected value(s)
            if (in_array($sub_cat_id, $selected)) {
                $string .= " selected";
            }
            $string .= ">" . stripslashes($row[TEXT_LANGUAGE . 'sub_category_name']) . "</option>";
        }
        $string .= "</select>";
    }
    
    // Free the result set
    @tep_db_free_result($result);
    
    return $string;
}

function tep_draw_file_upload_field(
  $name,
  $filename = '',
  $accept = '.html,.zip,.pdf',
  $button_text = 'Browse',
  $button_style = 'width: 130px;',
  $input_class = 'form-control'
) {
  // Create the field HTML
  $field = '<div class="file-upload">';
  $field .= '<input type="file" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" class="' . htmlspecialchars($input_class) . '" accept="' . htmlspecialchars($accept) . '">';
  $field .= '<button type="button" class="browse-btn" style="' . htmlspecialchars($button_style) . '">' . htmlspecialchars($button_text) . '</button>';

  $field .= '</div>';

  return $field;
}

function deleteDirectory($dir) {
  if (!file_exists($dir)) {
      return false;
  }

  if (!is_dir($dir)) {
      return unlink($dir);
  }

  foreach (scandir($dir) as $item) {
      if ($item === '.' || $item === '..') {
          continue;
      }
      $path = $dir . DIRECTORY_SEPARATOR . $item;
      if (is_dir($path)) {
          deleteDirectory($path);
      } else {
          unlink($path);
      }
  }
  return rmdir($dir);
}

?>