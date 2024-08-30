<?

function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link')
{
 global $$link;
 if (function_exists('mysql_connect')) :
 if (USE_PCONNECT == 'true')
 {
  $$link = @mysql_pconnect($server, $username, $password) or die("couldn't connect to database.");
 }
 else
 {
  $$link = @mysql_connect($server, $username, $password) or die("couldn't connect to database.");
 }
 if ($$link)
  @mysql_select_db($database) or die("couldn't connect to database.");
  @mysql_query($$link, 'set session sql_mode=""');
 else :
   if (USE_PCONNECT == 'true') {
      $server = 'p:' . $server;
    }
    $$link = mysqli_connect($server, $username, $password, $database);
    if ( !mysqli_connect_errno() ) {
      mysqli_set_charset($$link, 'utf8');
    }
    @mysqli_query($$link, 'set session sql_mode=""');
 endif;
  return $$link;
}
function tep_db_close($link = 'db_link')
{
 global $$link;
 if (function_exists('mysql_close'))
 return mysql_close($$link);
 else
    return mysqli_close($$link);
}
function tep_db_error($query, $errno, $error)
{
 global $messageStack;
 $error='<font color="#000000">'.$_SERVER['PHP_SELF'].'<br><b>' . $errno . ' - ' . $error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></b></font>';
// tep_mail("Shambhu Patnaik" , "shambhu@ejobsitesoftware.com", "jobsite Demo error database",$error , SITE_OWNER, ADMIN_EMAIL);
	$messageStack->add_session(ERROR_DATABASE, 'error');
 //tep_redirect(FILENAME_ERROR);
 die($error);
}
function tep_db_query($query, $link = 'db_link')
{
 global $$link;
 if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true'))
 {
  error_log("\n".tep_get_ip_address()." ".strftime(STORE_PARSE_DATE_TIME_FORMAT)." \n".'QUERY :' . $query . "\n", 3, STORE_DATABASE_QUERY_LOG);
 }

 if (function_exists('mysql_query')) :{

  $result = mysql_query($query, $$link) or tep_db_error($query, mysql_errno(), mysql_error());
  if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true'))
  {
   $result_error = mysql_error();
   error_log('RESULT: ' . $result . ' ' . $result_error . "\n", 3, STORE_DATABASE_QUERY_LOG);
  }
 }
 else :

   if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true'))
   {
    $result_error = mysql_error();
    error_log('RESULT: ' . $result . ' ' . $result_error . "\n", 3, STORE_DATABASE_QUERY_LOG);
   }
   $result = mysqli_query($$link, $query) or tep_db_error($query, mysqli_errno($$link), mysqli_error($$link));

 endif;
 return $result;
}

function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link')
{
 reset($data);
 if ($action == 'insert')
 {
  $query = 'insert into ' . $table . ' (';
 // while (list($columns, ) = each($data))
  foreach($data  as $columns => $value)
  {
   $query .= $columns . ', ';
  }
  $query = substr($query, 0, -2) . ') values (';
  reset($data);
  //while (list(, $value) = each($data))
  foreach($data  as $columns => $value)
  {
   switch ((string)$value)
   {
    case 'now()':
    $query .= 'now(), ';
    break;
    case 'null':
    $query .= 'null, ';
    break;
    default:
    $query .= '\'' . tep_db_input($value) . '\', ';
    break;
   }
  }
  $query = substr($query, 0, -2) . ')';
  //echo $query;exit;
 }
 elseif ($action == 'update')
 {
  $query = 'update ' . $table . ' set ';
 // while (list($columns, $value) = each($data))
   foreach($data  as $columns => $value)
  {
   switch ((string)$value)
   {
    case 'now()':
    $query .= $columns . ' = now(), ';
    break;
    case 'null':
    $query .= $columns .= ' = null, ';
    break;
    default:
    $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
    break;
   }
  }
  $query = substr($query, 0, -2) . ' where ' . $parameters;
 }
 //echo $query;exit;
 return tep_db_query($query, $link);
}
function tep_db_fetch_array($db_query)
{
 if(function_exists('mysql_fetch_array'))
  return mysql_fetch_array($db_query, MYSQL_ASSOC);
 else
  return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
}
function tep_db_num_rows($db_query)
{
 if(function_exists('mysql_num_rows'))
  return mysql_num_rows($db_query);
 else
  return mysqli_num_rows($db_query);
}
function tep_db_data_seek($db_query, $row_number)
{
 return mysql_data_seek($db_query, $row_number);
}
function tep_db_insert_id()
{
 return mysql_insert_id();
}
function tep_db_free_result($db_query)
{
 if(function_exists('mysql_free_result'))
  return mysql_free_result($db_query);
 else
  return mysqli_fetch_field($db_query);
}
function tep_db_fetch_fields($db_query)
{
 if(function_exists('mysql_fetch_field'))
  return mysql_fetch_field($db_query);
 else
  return mysqli_fetch_field($db_query);
}
function tep_db_output($string)
{
 return htmlspecialchars($string ,ENT_QUOTES,'ISO-8859-1');
}
function tep_db_input($string)
{
 return addslashes($string);
}
function tep_db_prepare_input($string)
{
 if (is_string($string))
 {
  return trim(tep_sanitize_string(stripslashes($string)));
 }
 elseif (is_array($string))
 {
  reset($string);
  //while (list($key, $value) = each($string))
  foreach($string  as $key => $value)

  {
   $string[$key] = tep_db_prepare_input($value);
  }
  return $string;
 }
 else
 {
  return $string;
 }
}
////////////////////
function tep_db_get_field($table,$field='',$whereclause='')
{
 $row=getAnyTableWhereData($table,$whereclause,$field);
 return $row["$field"];
}
function tep_db_num_fields($db_result)
{
 if(function_exists('mysql_num_fields'))
  return mysql_num_fields($db_result);
 else
  return mysqli_num_fields($db_result);
}
function tep_db_field_name($db_result,$no)
{
 if(function_exists('mysql_field_name'))
  return mysql_field_name($db_result,$no);
 else
  return mysqli_fetch_field_direct($db_result,$no)->name;
}
function tep_db_fetch_row($db_result)
{
 if(function_exists('mysql_fetch_row'))
  return mysql_fetch_row($db_result);
 else
  return mysqli_fetch_row($db_result);
}
?>