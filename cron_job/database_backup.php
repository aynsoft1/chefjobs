<?
/*
***********************************************************
**********# Name          : Shambhu Prasada Patnaik #******
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/08            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
*/
include_once("../include_files.php");

set_time_limit(0);
$backup_file = 'db_' . DB_DATABASE . '-' . date('YmdHis') . '.sql';
$fp = fopen(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file, 'w');
$schema = '# Database backup' . "\n" .
'# ' . HOST_NAME."\n" .
'#' . "\n" .
'# Database Backup For ' . SITE_OWNER . "\n" .
'# Copyright (c) ' . date('Y') . ' ' . SITE_OWNER . "\n" .
'#' . "\n" .
'# Database: ' . DB_DATABASE . "\n" .
'# Database Server: ' . DB_SERVER . "\n" .
'#' . "\n" .
'# Backup Date: ' . date("d/m/Y H:i:s") . "\n\n";
fputs($fp, $schema);
$tables_query = tep_db_query('show tables');
while ($tables = tep_db_fetch_array($tables_query)) 
{
 list(,$table) = each($tables);
 $schema = "\n".'drop table if exists ' . $table . ';' . "\n" .
           'create table ' . $table . ' (' . "\n";
 $table_list = array();
 $fields_query = tep_db_query("show fields from " . $table);
 while ($fields = tep_db_fetch_array($fields_query)) 
 {
  $table_list[] = $fields['Field'];
  $schema .= '  ' . $fields['Field'] . ' ' . $fields['Type'];
  if (strlen($fields['Default']) > 0) 
   $schema .= ' default \'' . $fields['Default'] . '\'';
  if ($fields['Null'] != 'YES') 
   $schema .= ' not null';
  if (isset($fields['Extra'])) 
   $schema .= ' ' . $fields['Extra'];
  $schema .= ',' . "\n";
 }
 $schema = preg_replace("/,\n$/", '', $schema);
 // add the keys
 $index = array();
 $keys_query = tep_db_query("show keys from " . $table);
 while ($keys = tep_db_fetch_array($keys_query)) 
 {
  $kname = $keys['Key_name'];
  if (!isset($index[$kname])) 
  {
   $index[$kname] = array('unique' => !$keys['Non_unique'],
                          'columns' => array());
  }
  $index[$kname]['columns'][] = $keys['Column_name'];
 }
 while (list($kname, $info) = each($index)) 
 {
  $schema .= ',' . "\n";
  $columns = implode($info['columns'], ', ');
  if ($kname == 'PRIMARY') 
  {
   $schema .= '  PRIMARY KEY (' . $columns . ')';
  } 
  elseif ($info['unique']) 
  {
   $schema .= '  UNIQUE ' . $kname . ' (' . $columns . ')';
  } 
  else 
  {
   $schema .= '  KEY ' . $kname . ' (' . $columns . ')';
  }
 }
 $schema .= "\n" . ')TYPE=MyISAM;' . "\n\n";
 fputs($fp, $schema);
 // dump the data
 $query_backup_count =(int) no_of_records($table,' 1',"*");
 $query_backup_count1=500;
 if($query_backup_count>$query_backup_count1)
  $query_backup_count =ceil($query_backup_count/$query_backup_count1);
 else
  $query_backup_count=1;
 $x1=0;
 $lower_limit=0;
 $upper_limit=$query_backup_count1;
 for($c=0;$c<$query_backup_count;$c++)
 {
  $rows_query = tep_db_query("select " . implode(',', $table_list) . " from " . $table."  limit $lower_limit ,$upper_limit");
  $lower_limit=$lower_limit+$query_backup_count1;
  while ($rows = tep_db_fetch_array($rows_query)) 
  {
   $schema = 'insert into ' . $table . ' (' . implode(', ', $table_list) . ') values (';
   reset($table_list);
   while (list(,$i) = each($table_list)) 
   {
    if (!isset($rows[$i])) 
    {
     $schema .= 'NULL, ';
    } 
    elseif (tep_not_null($rows[$i])) 
    {
     $row = addslashes($rows[$i]);
     $row = preg_replace("/\n#/", "\n".'\#', $row);
     $schema .= '\'' . $row . '\', ';
    } 
    else 
    {
     $schema .= '\'\', ';
    }
   }
   $schema = preg_replace('/, $/', '', $schema) . ');' . "\n";
   fputs($fp, $schema);
  }
  tep_db_free_result($rows_query);
 }
}
tep_db_free_result($tables_query);
fclose($fp);
?>