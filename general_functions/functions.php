<?

/*********************************************************

**********#	Name				  : Shambhu Prasad Patnaik		   #**********

**********#	Company			: Aynsoft							         #**********

**********#	Copyright (c) www.aynsoft.com 2004	#**********

*********************************************************/

////



function year_listing($startyear,$endyear,$parameters="",$header="",$header_value="",$selected="")

{

	$year_string="\n<select ".$parameters.">\n";

	if($header!="")

	{

		$year_string.="<option value='".$header_value."'>".tep_db_output($header)."</option>\n";

	}

 $selected=explode(",",$selected);

	for($i=$startyear;$i>=$endyear;$i--)

	{

  $year_string.="<option value='$i'";

  if(in_array($i,$selected))

  {

   $year_string.=" selected";

  }

  $year_string.=">".tep_db_output($i)."</option>\n";

	}

	$year_string.="</select>\n";

	return $year_string;

}

function no_of_records($table_name,$whereClause,$field_name="*")

{

	$query = "select count($field_name) as total from $table_name where $whereClause";

	//echo $query."<br>";

	$result=tep_db_query($query);

	$row=tep_db_fetch_array($result);

	$x=$row['total'];

	@tep_db_free_result($result);

	return $x;

}

//**************************************************************************************

//Function to get name(s) from a table**************************************************

//**************************************************************************************

function get_name_from_table($table_name,$select_name, $field_name_id,$field_name_ids)

{

	$name="";

 if($field_name_ids=='')

	 $query="select $select_name from $table_name where $field_name_id in ('')";

 else

 	$query="select $select_name from $table_name where $field_name_id in ($field_name_ids)";

	$result=tep_db_query($query);

 //echo "<br>$query"; //exit;

	//echo tep_db_num_rows($result);

	while($row=tep_db_fetch_array($result))

	{

		$name.=$row[$select_name].", ";

	}

	if(substr($name,-2,2)==", ")

	{

		$name=substr($name,0,-2);

	}

	tep_db_free_result($result);

	return $name;

}

////

// Alias function for Store configuration values in the Administration Tool

function tep_cfg_select_option($select_array, $key_value, $key = '')

{

 $string = '';

 for ($i=0, $n=sizeof($select_array); $i<$n; $i++)

 {

  $name = ((tep_not_null($key)) ? 'TR_configuration_value[' . $key . ']' : 'TR_configuration_value');

  $string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

  if ($key_value == $select_array[$i])

   $string .= ' checked';

  $string .= '> ' . $select_array[$i];

 }

 return $string;

}



////

// Alias function for module configuration keys

function tep_mod_select_option($select_array, $key_name, $key_value)

{

 reset($select_array);

 foreach ($select_array as $key =>$value)

 //while (list($key, $value) = each($select_array))

 {

  if (is_int($key))

   $key = $value;

  $string .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';

  if ($key_value == $key)

   $string .= ' CHECKED';

  $string .= '> ' . $value;

 }

 return $string;

}

///////////////////////////

function see_before_page_number1($sort_array,$field_name,$default_field,$order_name,$default_order,$lower,$default_lower,$higher,$default_higher)

{

 $field=(in_array($_POST['field'],$sort_array)?$_POST['field']:$default_field);

 $order=(in_array($_POST['order'],array('asc','desc'))?$_POST['order']:$default_order);

 if(!isset($_POST['lower']) && $_GET['lower']>0 )

  $lower=((int)$_GET['lower'] >=0 ?(int)$_GET['lower']:$default_lower);

 else

 $lower=((int)$_POST['lower'] >=0 ?(int)$_POST['lower']:$default_lower);



 if(!isset($_POST['higher']) && $_GET['higher']>0 )

  $higher=((int)$_GET['higher'] >0 ?(int)$_GET['higher']:$default_higher);

 else

  $higher=((int)$_POST['higher'] >0 ?(int)$_POST['higher']:$default_higher);



 return(array('order'=>$order,

              'field'=>$field,

              'lower'=>$lower,

              'higher'=>$higher));

}

function see_before_page_number123($sort_array,$field_name,$default_field,$order_name,$default_order,$lower,$default_lower,$higher,$default_higher)

{

 $field=(in_array($_POST['field'],$sort_array)?$_POST['field']:$default_field);

 $order=(in_array($_POST['order'],array('asc','desc'))?$_POST['order']:$default_order);

 $lower=((int)$_POST['lower'] >=0 ?(int)$_POST['lower']:$default_lower);

 $higher=((int)$_POST['higher'] >0 ?(int)$_POST['higher']:$default_higher);

 return(array('order'=>$order,

              'field'=>$field,

              'lower'=>$lower,

              'higher'=>$higher));

}

function see_before_page_number($sort_array,$field_name,$default_field,$order_name,$default_order,$lower,$default_lower,$higher,$default_higher)

{

 $field=(in_array($_POST['field'],$sort_array)?$_POST['field']:$default_field);

 $order=(in_array($_POST['order'],array('asc','desc'))?$_POST['order']:$default_order);

 $lower=((int)$lower >=0 ?(int)$lower:$default_lower);

 $higher=((int)$higher >0 ?(int)$higher:$default_higher);

 return(array('order'=>$order,

              'field'=>$field,

              'lower'=>$lower,

              'higher'=>$higher));

}



/// ...

function see_page_number()

{

 global $template,$hidden_fields,$lower,$x1,$total,$pno,$low,$higher,$totalpage;

	if($lower>=$x1)

	{

		$lower=0;

		if($pno==1)

		{

			$low=0;

		}

		else

			$low=($pno-2)*(int)$higher;

	}

	else

	{

		if($pno==1)

		{

			$low=0;

		}

		else

		$low=$lower-2*$higher;

	}

	if($totalpage>1)

	{

  $hidden_fields.=tep_draw_hidden_field('higher',$higher);

  $hidden_fields.=tep_draw_hidden_field('lower');

  $list_page="<select name='page[]' onchange=submitform1('$higher','page',this);>";

  for($i=1;$i<=$totalpage;$i++)

  {

   $list_page.="<option value='$i' ";

   if($i==$pno)

    $list_page.=" selected";

   $list_page.=">$i</option>";

  }

  $list_page.="</select>";

  $template->assign_vars(array("list_page"=>"Go to Page # ".$list_page));

  if($pno==1)

   $template->assign_vars(array("page"=>"Page ".$pno." of ".$totalpage." &nbsp;&nbsp;Prev | <a href=# onclick=submitform('$lower','page') class='other'>Next</a>"));

  else if($pno==$totalpage)

   $template->assign_vars(array("page"=>"Page ".$pno." of ".$totalpage." &nbsp;&nbsp;<a href=# onclick=submitform('$low','page') class='other'>Prev</a> | Next"));

  else

   $template->assign_vars(array("page"=>"Page ".$pno." of ".$totalpage." &nbsp;&nbsp;<a href=# onclick=submitform('$low','page') class='other'>Prev</a> | <a href=# onclick=submitform('$lower','page') class='other'>Next</a>"));

 }

 else

 {

  $hidden_fields.=tep_draw_hidden_field('lower');

 }

}

function see_page_number1($form_name='page',$lower_name='lower',$higher_name='higher',$lower1=null,$x2=null,$total_1=null,$pno_1=null,$higher_1=null,$totalpage_1=null)

{

 global $hidden_fields;

	if($lower_1>=$x2)

	{

		$lower_1=0;

		if($pno_1==1)

		{

			$low=0;

		}

		else

			$low=($pno_1-2)*$higher_1;

	}

	else

	{

		if($pno_1==1)

		{

			$low=0;

		}

		else

		$low=$lower_1-2*$higher_1;

	}

	if($totalpage_1>1 )

	{

  $hidden_fields.='<input type="hidden" name="'.$higher_name.'" value="'.$higher_1.'">';

  $hidden_fields.='<input type="hidden" name="'.$lower_name.'" >';

  $list_page="<select name='page[]' onchange=submitform2('$higher_1','".$form_name."',this,'$lower_name');>";

  for($i=1;$i<=$totalpage_1;$i++)

  {

   $list_page.="<option value='$i' ";

   if($i==$pno_1)

    $list_page.=" selected";

   $list_page.=">$i</option>";

  }

  $list_page.="</select>";

  $list_string='';

  $list_string['list_page']="Go to Page # ".$list_page;



  if($pno_1==1)

   $list_string['page']="Page ".$pno_1." of ".$totalpage_1." &nbsp;&nbsp;<b>Prev</b> | <a href=# onclick=submitform3('$lower1','$form_name','$lower_name') class='other'><b>Next</b></a>";

  else if($pno_1==$totalpage_1)

   $list_string['page']="Page ".$pno_1." of ".$totalpage_1." &nbsp;&nbsp;<a href=# onclick=submitform3('$low','$form_name','$lower_name') class='other'><b>Prev</b></a> | <b>Next</b>";

  else

   $list_string['page']="Page ".$pno_1." of ".$totalpage_1." &nbsp;&nbsp;<a href=# onclick=submitform3('$low','$form_name','$lower_name') class='other'><b>Prev</b></a> | <a href=# onclick=submitform3('$lower1','$form_name','$lower_name') class='other'><b>Next</b></a>";

 }

 else

 {

  $hidden_fields.='<input type="hidden" name="'.$lower_name.'" value="">';

  $list_string='';

 }

 return $list_string;

}

function see_page_number2()

{

 global $template,$hidden_fields,$lower,$x1,$total,$pno,$low,$higher,$totalpage,$map_view;



	if($totalpage>1)

	{

  $list_page="<select class='page_select' name='page[]' >";

  for($i=1;$i<=$totalpage;$i++)

  {

   $list_page.="<option value='$i' ";

   if($i==$pno)

    $list_page.=" selected";

   $list_page.=">$i</option>";

  }

  $list_page.="</select>";

		$list_string=array();

		$list_string['list_page']="Go to Page # ".$list_page;

  if($pno==1)

		$list_string['page']="Page ".$pno." of ".$totalpage." &nbsp;&nbsp;Prev | <a href='#' class='page_next'>Next</a>";

  else if($pno==$totalpage)

 	$list_string['page']="Page ".$pno." of ".$totalpage." &nbsp;&nbsp;<a href='#'  class='page_prev'>Prev</a> | Next";

  else

 	$list_string['page']="Page ".$pno." of ".$totalpage." &nbsp;&nbsp;<a href='#' class='page_prev'>Prev</a> | <a href=#  class='page_next'>Next</a>";

 }

  if($map_view==1)

 	$list_string['map_view_link']='<a class="map_view_link" >Grid View</a>';

		else

 	$list_string['map_view_link']='<a class="map_view_link" >Map View</a>';

 $hidden_fields.=tep_draw_hidden_field('lower');

 $hidden_fields.=tep_draw_hidden_field('show_page',$pno);

 $hidden_fields.=tep_draw_hidden_field('map_view',$map_view);

 return	$list_string;

}

function error_in_entry_data($error_string)

{

 if($error_string[0]=="")

  return $error_string[0];

 else

 {

  $error_string1="";

  $error_string2="";

  $error_string3="";

  $error_string1='<br><table border="0" width="99%" cellspacing="1" cellpadding="0" align="center">

  <tr>

   <td>

    <table border="0" cellspacing="2" cellpadding="2">';

    for($i=0;$i<count($error_string);$i++)

    {

     $error_string2.='

       <tr>

         <td colspan="100"><li>'.stripslashes($error_string[$i]).'</td>

       </tr>';

    }

    $error_string3='

      </table>

     </td>

    </tr>

   </table>';

  $error_string=$error_string1.$error_string2.$error_string3;

  return $error_string;

 }

}

function set_session_value($session_set_array)

{

 foreach ($session_set_array as $k =>$v)

 //while (list($k,$v)=each($session_set_array))

 {

  $_SESSION[$k]=$v;

  //echo $k." : ".$v."<br>";

 }

}

function unset_session_value($session_set_array)

{

 foreach ($session_set_array as $k =>$v)

 // while (list($k,$v)=each($session_set_array))

 {

  if(isset($_SESSION[$k]))

   unset($_SESSION[$k]);

  //echo $k." : ".$v."<br>";

 }

}

//////////////////////////////////////////////////////

function check_data($query_string,$connect="=",$pre_word="",$post_word="")

{

 //echo decode_string($query_string);

 $explode_string=explode($connect,decode_string($query_string));

 //print_r ($explode_string); exit;

 //echo sizeof($explode_string);

 if(sizeof($explode_string)==3 && $explode_string['0']==$pre_word && $explode_string['2']==$post_word)

 {

  return (int)$explode_string[1];

 }

 else

  return -1;

}

function check_data1($query_string,$connect="=",$pre_word="",$post_word="")

{

 //echo decode_string($query_string);

 $explode_string=explode($connect,decode_string($query_string));

 //print_r ($explode_string); exit;

 //echo sizeof($explode_string);

 if(sizeof($explode_string)==3 && $explode_string['0']==$pre_word && $explode_string['2']==$post_word)

 {

  return $explode_string[1];

 }

 else

  return -1;

}



//////////////////////////////////////////////

function calculate_experience($min_experience,$max_experience)

{



 if(!tep_not_null($max_experience))

 {

  return INFO_TEXT_ANY_EXPERIENCE;

 }

 if($min_experience=="0")

 {

  $minexperience="< ";

 }

 else

 {

  $minexperience=($min_experience >=12?(($min_experience/12)>1?($min_experience/12).INFO_TEXT_YEARS:($min_experience/12).INFO_TEXT_YEAR):($min_experience>1?$min_experience.INFO_TEXT_MONTHS:$min_experience.INFO_TEXT_MONTH));

 }

 if($min_experience==(int)$max_experience)

  $maxexperience=INFO_TEXT_PLUS;

 else

  $maxexperience=($max_experience >= 12?(($max_experience/12)>1?($max_experience/12).INFO_TEXT_YEARS1:($max_experience/12).INFO_TEXT_YEAR1):($max_experience>1?$max_experience.INFO_TEXT_MONTHS1:$max_experience.INFO_TEXT_MONTH1));

 if($min_experience=="0" && $maxexperience==INFO_TEXT_PLUS)

 {

  return $maxexperience;

 }

 else

 {

  return $minexperience.$maxexperience;

 }

}

  function tep_get_languages() {

    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . LANGUAGE_TABLE . " order by sort_order");

    while ($languages = tep_db_fetch_array($languages_query)) {

      $languages_array[] = array('id' => $languages['languages_id'],

                                 'name' => $languages['name'],

                                 'code' => $languages['code'],

                                 'image' => $languages['image'],

                                 'directory' => $languages['directory']);

    }



    return $languages_array;

  }

  function tep_get_orders_status_name($orders_status_id, $language_id = '') {

    global $languages_id;



    if (!$language_id) $language_id = $languages_id;

    $orders_status_query = tep_db_query("select orders_status_name from " . ORDER_STATUS_TABLE . " where orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");

    $orders_status = tep_db_fetch_array($orders_status_query);



    return $orders_status['orders_status_name'];

  }

function calculate_end_date($time_period,$time_period1,$start_date="")

{

 switch($time_period1)

 {

  case "Week":

   if($start_date!="")

    $end_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),substr($start_date,8,2)+($time_period*7)-1,substr($start_date,0,4)));

   else

    $end_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+($time_period*7)-1,date("Y")));

   break;

  case "Month":

   if($start_date!="")

    $end_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2)+$time_period,substr($start_date,8,2),substr($start_date,0,4)));

   else

    $end_date=date("Y-m-d",mktime(0,0,0,date("m")+$time_period,date("d")-1,date("Y")));

   break;

  case "Year":

   if($start_date!="")

    $end_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),substr($start_date,8,2),substr($start_date,0,4)+$time_period));

   else

    $end_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")+$time_period));

   break;

 }

 return $end_date;

}

////////////////////////////////////

function LIST_TABLE2($flag=0,$table_name=null,$where='',$field1_name=null,$field2_name='',$option_value=null,$order_by='',$addoption_value="",$addstart="" ,$addmiddle=" ",$addend="", $query="",$parameters='',$header="",$header_value="",$selected="",$footer="",$footer_value="")

{

	$string="";

 $selected=explode(",",$selected);

 if($flag==0)

 $string.="<select ".$parameters.">";

 if($header!="")

 {

  $string.="<option value='$header_value'";

  if(in_array($header_value,$selected))

  {

   $string.=" selected";

  }

  $string.=">".stripslashes($header)."</option>";

 }

  if($query=="")

 {

  if($order_by!="")

    $query = "select * from $table_name $where order by $order_by asc";

  else

    $query = "select * from $table_name $where order by $field1_name asc";

 }

	$result=tep_db_query($query);

	 //echo "<br>$query";//exit;

	$x=tep_db_num_rows($result);

	//echo $x;exit;

	while($row = tep_db_fetch_array($result))

	{

		$field1=$row[$field1_name];

		$field2=$row[$field2_name];



		$code=$addoption_value.$row[$option_value];

  $string.="<option value='$code'";

  if(in_array($code,$selected))

  {

   $string.=" selected";

  }

  $string.="> $addstart ".stripslashes($field1)."$addmiddle".stripslashes($field2)."$addend </option>";

 }

 if($footer!="")

 {

  $string.="<option value='$footer_value'";

  if(in_array($footer_value,$selected))

  {

   $string.=" selected";

  }

  $string.=">".stripslashes($footer)."</option>";

 }

	//$string.="</select>";

	@tep_db_free_result($result);

	return $string;

}

///////////////////////////////

 function LIST_SET_DATA($table_name=null,$where="",$field_name=null,$field_value=null,$order_by="",$parameters=null,$header="",$header_value="",$selected="",$addstart="",$addend="",$footer="",$footer_value="")

{

	$string="";

 $selected=explode(",",$selected);

 $string.="<select ".$parameters.">";

 if($header!="")

 {

  $string.="<option value='$header_value'";

  if(in_array($header_value,$selected))

  {

   $string.=" selected";

  }

  $string.=">".stripslashes($header)."</option>";

 }

  if($order_by!="")

    $query = "select ".$field_name.",".$field_value." from $table_name $where order by $order_by asc";

  else

    $query = "select ".$field_name.",".$field_value." from $table_name $where order by $field_name asc";
    // echo "<br>$query";
	$result=tep_db_query($query);

// echo "<br>$query";//exit;

	$x=tep_db_num_rows($result);

	//echo $x;exit;



	while($row = tep_db_fetch_array($result))

	{

		$c=$row[$field_name];

		$code=$row[$field_value];



   $string.="<option value='$code'";

	  if(in_array($code,$selected))

	  {

	   $string.=" selected";

	  }

    $string.=">".$addstart.stripslashes($c).$addend."</option>";

 }

 if($footer!="")

 {

  $string.="<option value='$footer_value'";

  if(in_array($footer_value,$selected))

  {

   $string.=" selected";

  }

  $string.=">".stripslashes($footer)."</option>";



 }

	$string.="</select>";

	@tep_db_free_result($result);

	return $string;

}

/////////////////////////////////////////////////

function TARGET_JOB_LOCATIONS($where="",$show_state=true,$add_country='',$order_by="",$parameters=null,$header="",$header_value="",$selected="",$footer="",$footer_name="")

{

	$string="";

 $table_name=COUNTRIES_TABLE;

 $add_country_array=explode(",",$add_country);

 $selected=explode(",",$selected);

 $string.="<select ".$parameters.">";

 if($header!="")

 {

  $string.="<option value='$header_value'";

  if(in_array($header_value,$selected))

  {

   $string.=" selected";

  }

  $string.=">".stripslashes($header)."</option>";

 }

 if($order_by!="")

   $query = "select * from $table_name $where order by $order_by asc";

 else

   $query = "select * from $table_name $where order by country_name asc";

	$result=tep_db_query($query);

 //echo "<br>$query";//exit;

	$x=tep_db_num_rows($result);

	//echo $x;exit;

	while($row = tep_db_fetch_array($result))

	{

  $c       =$row['country_name'];

  $state_id=0;

  $code=$row['id'];

  if($show_state)

  $code=$row['id']."_".(int)$state_id;

   $string.="<option value='$code'";

   if(in_array($code,$selected))

   {

     $string.=" selected";

   }

   $string.=">".stripslashes($c)."</option>";

   if($show_state &&(in_array($row['id'],$add_country_array)))

   {

    $state_results=tep_db_query("select * from ".ZONES_TABLE." where zone_country_id='".$row['id']."'");

    while($states=tep_db_fetch_array($state_results))

    {

     $code=$row['id']."_".(int)$states['zone_id'];

     $string.="<option value='".$code."'";

     if(in_array($code,$selected))

     {

      $string.=" selected";

     }

     $string.=">".$c."<b>-</b>".$states['zone_name']."</option>";

    }

    @tep_db_free_result($state_results);

   }

 }

 if($footer!="")

 {

  $string.="<option value='$footer_value'";

  if(in_array($footer_value,$selected))

  {

   $string.=" selected";

 }

 $string.=">".stripslashes($footer)."</option>";

 }

	$string.="</select>";

	@tep_db_free_result($result);

	return $string;

}

/////////////////////////////////////////////////



/////////////////////////////////////////////////////



function country_state($c_name='country',$c_d_value='Country',$s_name='state',$s_d_value='state',$s_value='zone_id',$value='')

{

 $value1=explode(',',$value);

 for($i=0;$i<count($value1);$i++)

 $value2.= '"'.trim($value1[$i]).'",';

 $value= substr($value2,0,-1);



 $result_country_id=tep_db_query("select distinct(zone_country_id) from ".ZONES_TABLE);

 $country_id='';

 while($row=tep_db_fetch_array($result_country_id))

 {

  $country_id.=$row['zone_country_id'].',';

 }

 $country_id=substr($country_id,0,-1);

 tep_db_free_result($result_country_id);

 if($country_id=='')

  $country_id=0;

 $result_country=tep_db_query("select * from ".COUNTRIES_TABLE." where id in ($country_id)");



 $result_country=tep_db_query("select * from ".COUNTRIES_TABLE);

 $ret_str='';

 $ret_str.='twoLevelCountryState.forValue("").addOptionsTextValue("'.$s_d_value.'","");';

 if($result_country&&(tep_db_num_rows($result_country)>0))

 {

  while($rowC=tep_db_fetch_array($result_country))

  {

   $temp=array();

   $qC="select * from ".ZONES_TABLE." where zone_country_id=".$rowC['id']." order by ".TEXT_LANGUAGE."zone_name asc";



   $result_state=tep_db_query($qC);



   if($result_state &&(tep_db_num_rows($result_country)>0)&&(tep_db_num_rows($result_state)>0))

   {

    $temp[]='"'.$s_d_value.'",""';

    while($rowZ=tep_db_fetch_array($result_state))

     $temp[].='"'.$rowZ[TEXT_LANGUAGE.'zone_name'].'","'.$rowZ[$s_value].'"';

    $state1=implode(',',$temp);

    $ret_str.='twoLevelCountryState.forValue("'.$rowC['id'].'").addOptionsTextValue('.$state1.');';

    $ret_str.='twoLevelCountryState.forValue("'.$rowC['id'].'").setDefaultOptions('.$value.');'."\n";

   }

   else

   {

    $state1='"'.$s_d_value.'",""';

    $ret_str.='twoLevelCountryState.forValue("'.$rowC['id'].'").addOptionsTextValue('.$state1.');';

   }

  }

 }

 tep_db_free_result($result_country);

 tep_db_free_result($result_state);

 return '<script language="javascript">var twoLevelCountryState = new optionList("'.$c_name.'","'.$s_name.'");'.$ret_str.'</script>';

}

//////////////////////////////////////////////////

 function row_positions($total_row=0,$parameters='',$header="",$header_value="",$selected="")

 {

  $string="";

  $selected=explode(",",$selected);

  if($flag==0)

  $string.="<select ".$parameters.">";

  if($header!="")

  {

   $string.="<option value='$header_value'";

   if(in_array($header_value,$selected))

   {

    $string.=" selected";

   }

   $string.=">".stripslashes($header)."</option>";

  }

  for($i=1;$i<=$total_row;$i++)

	 {

		 $code=$addoption_value.$i;

   $string.="<option value='$code'";

   if(in_array($code,$selected))

   {

    $string.=" selected";

   }

   $string.=">".stripslashes($i)."</option>";

  }

	  $string.="</select>";

	 return $string;

 }

 ///////////////////////////////

 //************************************************************

function year_month_list($year_parameter="",$start_year='',$end_year='',$year='',$month_parameter="",$month='',$required=false,$show_name=false,$change_order=false)

{

 $month_array=array("1"=>"Jan","2"=>"Feb","3"=>"Mar",

                    "4"=>"Apr","5"=>"May","6"=>"Jun","7"=>"Jul",

                    "8"=>"Aug","9"=>"Sept","10"=>"Oct",

                    "11"=>"Nov","12"=>"Dec");

 if($start_year=='')

 $start_year=date("Y");

 if($end_year=='')

 $end_year=date("Y")+2;



	$year_string="<div class='col'><select ".$year_parameter.">";

 $year_string.="<option value=''>Year</option>";



	for($i=$start_year;$i<=$end_year;$i++)

	{

  $year_string.="<option value='$i'";

  if($year==$i)

  {

   $year_string.=" selected";

  }

  $year_string.=">".stripslashes($i)."</option>";

	}

 $year_string.="</select></div>";

	$month_string="<div class='col'><select ".$month_parameter.">";

 $month_string.="<option value=''>Month</option>";



	for($i=1;$i<=12;$i++)

	{

  $month_string.="<option value='$i'";

  if($month==$i)

  {

   $month_string.=" selected";

  }

  $month_string.=">".$month_array[$i]."</option>";

	}

	$month_string.="</select></div>";

 if($required)

  //  $add1='&nbsp;<span class="inputRequirement">*</span>';

   $add1='';

 else

  $add1='';

  if($show_name and $change_order==false)

  //  $add1.=" [ year-month ]";

  $add1.=" ";

  elseif($show_name and $change_order==true)

  //  $add1.=" [ month-year]";

  $add1.=" ";

  else

   $add1.="";

  if($change_order=true)

  //  return $month_string." - ".$year_string.$add1;

   return $month_string."  ".$year_string.$add1;

  else

	  // return $year_string." - ".$month_string.$add1;

	  return $year_string."  ".$month_string.$add1;

}

//////////////////////////////////////////////////

function formate_date1($raw_date,$format='%d %b %Y  %I:%M %p')

{

 if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') )

  return false;

 $year = (int)substr($raw_date, 0, 4);

 $month = (int)substr($raw_date, 5, 2);

 $day = (int)substr($raw_date, 8, 2);

 $hour = (int)substr($raw_date, 11, 2);

 $minute = (int)substr($raw_date, 14, 2);

 $second = (int)substr($raw_date, 17, 2);

 return strftime($format, mktime($hour,$minute,$second,$month,$day,$year));



}

function formate_date($date='date("Y-m-d")',$format='d-M-Y')

{

// echo $date;//("Y-m-d") ; exit;

// return  @date("$format", mktime(0, 0, 0, substr($date,5,2),substr($date,8,2), substr($date,0,4)));

 $date_array=explode('-',$date);

 return  @date("$format", mktime(0, 0, 0, (int)$date_array[1],(int)$date_array[2], (int)$date_array[0]));

}

///////////////////////////////////////////////////

//*

function job_category_reduce($job_categorys)

{

 $job_categorys_array=explode(',',$job_categorys);

 for($i=0;$i<count($job_categorys_array);$i++)

 {

  $row=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id  ='".$job_categorys_array[$i]."'",'id,sub_cat_id');

  if($row1=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id ='".$row['sub_cat_id']."' and  sub_cat_id is NULL",'id'))

  {

   $query="select id from ".JOB_CATEGORY_TABLE." where sub_cat_id ='".$row['id']."'";

   //echo "<br>$query";exit;

   $result=tep_db_query($query);

   $x=tep_db_num_rows($result);

   //echo $x;//exit;

   if($x > 0)

   {

    $child_id=array();

    while($row1 = tep_db_fetch_array($result))

    {

     $child_id[]=$row1['id'];

     if (in_array($row1['id'], $job_categorys_array))

      $flag=1;

     else

     {

      //echo $row1['id'];

      $flag=0;

      break;

     }

    }//while

    @tep_db_free_result($result);

     if($flag==0)

     {

      $result = array_intersect($job_categorys_array,$child_id);

      if(count($result)>0)

      $job_categorys_array = array_diff($job_categorys_array,$row);

     }

    //else

    // $job_categorys_array = array_diff($job_categorys_array,$child_id);

   }

  }

 }//for

 $final_category=implode(',',$job_categorys_array);

  return $final_category;

}//*/

function remove_child_job_category($job_categorys)

{

 $job_categorys_array=explode(',',$job_categorys);

 $count=count($job_categorys_array);

 for($i=0;$i<$count;$i++)

 {

  $child=get_job_category_child((int)$job_categorys_array[$i]);

  $child_array=explode(',',substr($child,2));

  $job_categorys_array = array_diff($job_categorys_array,$child_array);

 }

 $final_category= implode(',',$job_categorys_array);

 return $final_category;

}

function get_search_job_category($job_category)

{

 $job_category_array=explode(',',$job_category);

 $total_category =count($job_category_array);

 $search_category=array();

 for ($i=0;$i<$total_category;$i++)

 {

  $row=getAnyTableWhereData(JOB_CATEGORY_TABLE," id ='".$job_category_array[$i]."'",'id,sub_cat_id');//parent

  if(!tep_not_null($row['sub_cat_id']))//parent

  {

   $search_category[]=get_job_category_child($job_category_array[$i]);

  }

  else

  {

   $search_category[]=$job_category_array[$i].','.$row['sub_cat_id'];

  }

 }

 $final_category=implode(',',$search_category);

 $search_category=explode(',',$final_category);

 $search_category=array_unique($search_category);

 $final_category=implode(',',$search_category);

 return $final_category;

}

//////////////////////////////////////////////////////////////////////

 function job_category_child($id)

 {

  $query="select id from ".JOB_CATEGORY_TABLE." where sub_cat_id ='".$id."'";

  //echo "<br>$query";exit;

  $result=tep_db_query($query);

  $x=tep_db_num_rows($result);

  //echo $x;//exit;

  $child_id[]=$id;

  if($x > 0)

  {

   while($row1 = tep_db_fetch_array($result))

   {

    $child_id[]=$row1['id'];

   }

  }

  return implode(',',$child_id);

 }



function get_job_category_child($job_category)

{



 /////// check paeent status ///////////////////////////

 //check root

 $job_category=(int)$job_category;

 $row=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id ='".$job_category."'",'id,sub_cat_id');

 if(is_null($row['sub_cat_id']))

 {

  $root_child_array=explode(',',job_category_child($job_category));//echo "root".$row['sub_cat_id'];

  //print_r($root_child_array);

  for($i=0;$i<count($root_child_array);$i++)

  {

   $child[]=job_category_child($root_child_array[$i]);

  }

  $child=implode(',',$child);

  $child=explode(',',$child);

  sort($child);

  $result = array_unique($child);//  print_r($result);

  return $child=implode(',',$result);



 }

 elseif ($row1=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id ='".$row['sub_cat_id']."' and  sub_cat_id is NULL",'id'))

  return job_category_child($job_category);//echo "paernt";

 else

   return  $job_category;//echo "child";

}

////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////

  function cal_work_experience($resume_id)

  {

   $query=" select start_year,start_month,end_year,end_month,still_work from ".JOBSEEKER_RESUME2_TABLE." where resume_id='".$resume_id."'";

   $result_query_list = tep_db_query($query);

   $list_row = tep_db_num_rows($result_query_list);

   $i=1;

   $experience=0;

   while ($row_work_experience = tep_db_fetch_array($result_query_list))

   {

    $start_year=$start_month=$end_year=$end_month=0;

    if($row_work_experience['start_month'] >'0' and $row_work_experience['start_year'] >'0')

    {

     $start_year          = @date("Y", mktime(0, 0, 0, $row_work_experience['start_month'],1, $row_work_experience['start_year']));

     $start_month         = @date("m", mktime(0, 0, 0, $row_work_experience['start_month'],1, $row_work_experience['start_year']));

    }

    if($row_work_experience['end_month'] >'0' and $row_work_experience['end_year'] >'0')

    {

     $end_year          = @date("Y", mktime(0, 0, 0, $row_work_experience['end_month'],1, $row_work_experience['end_year']));

     $end_month         = @date("m", mktime(0, 0, 0, $row_work_experience['end_month'],1, $row_work_experience['end_year']));

    }

    elseif($row_work_experience['still_work'] =='Yes')

    {

     $end_year          = @date("Y", mktime(0, 0, 0, date('m'),1, date('Y')));

     $end_month         =@date("m", mktime(0, 0, 0, date('m'),1, date('Y')));

    }



   // echo $start_year.'<br>'.$start_month.'<br>'.$end_year.'<br>'.$end_month;exit;

     $experience =$experience + cal_experience_month($start_year,$start_month,$end_year,$end_month);

   }//end while loop

   tep_db_free_result($result_query_list );

   return $experience;

  }



  function cal_experience_month($from_year,$from_month,$to_year,$to_month)

  {//echo ' from_year ='.$from_year.'  from_month ='.$from_month.' to_year='.$to_year.' to_month='. $to_month."    " ;



   if($from_year<=$to_year)

   {

    if($from_month <= $to_month  and  $from_year==$to_year)

    {

     $m = $to_month- $from_month ;

     return $m ;

    }

    elseif($from_year<$to_year)

    {

     $y= $to_year- $from_year;

     if($from_month <= $to_month)

     $m = $to_month - $from_month +12+(($y-1)*12);

     elseif($from_month > $to_month)

      $m = $to_month - $from_month +($y*12);

     return $m;

    }

    else

    return 0;

   }

   else

   return 0;

  }

  function set_work_experience($resume_id)

  {

   $resume_id=(int)$resume_id;

   $total_work_experience=cal_work_experience($resume_id);

   $query="select id, min_experience, max_experience from " . EXPERIENCE_TABLE ." order by min_experience";

   $result_query_list = tep_db_query($query);

   $list_row = tep_db_num_rows($result_query_list);

   $work_experince_set=false;

   while ($row = tep_db_fetch_array($result_query_list))

   {

    $min_experience= tep_db_output($row['min_experience']);

    $max_experience= tep_db_output($row['max_experience']);

    $experience_id = tep_db_output($row['id']);

    if($min_experience==$max_experience && $min_experience<=$total_work_experience)

    {

     tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set work_experince='".$experience_id."' where resume_id='".$resume_id."'");

     $work_experince_set=true;

     break;

    }

    elseif($min_experience<=$total_work_experience && $max_experience>=$total_work_experience)

    {

     tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set work_experince='".$experience_id."' where resume_id='".$resume_id."'");

     $work_experince_set=true;

     break;

    }

   }//end while loop

   if(!$work_experince_set)

    tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set work_experince=0 where resume_id='".$resume_id."'");

   tep_db_free_result($result_query_list );

  }

  function get_next_round($round)

  {

   $row1 =getAnyTableWhereData(SELECTION_ROUND_TABLE," id ='".$round."'");

   $query=" select *  from ".SELECTION_ROUND_TABLE." where value >'".$row1['value']."' order by value";

   $result_query_list = tep_db_query($query);

   $list_row = tep_db_num_rows($result_query_list);

   if($list_row > 0)

   {

    while ($row= tep_db_fetch_array($result_query_list))

    {

     $round=$row['id'];

     break;

    }//end while loop

    tep_db_free_result($result_query_list );

   }

   if($round <1)

    return 1;

   else

   return $round;

  }

  function get_current_round_status($application_id,$round='')

  {

   if($round=='')

   {

    $row2=getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' order by inserted desc,apt.id desc limit 0,1","apt.process_round");

    $round=$row2['process_round'];

   }

   $query=" select *  from ".SELECTION_ROUND_TABLE." order by value";

   $result_query_list = tep_db_query($query);

   $list_row = tep_db_num_rows($result_query_list);

   if($list_row > 0)

   {

    $round_status="";

    while ($row= tep_db_fetch_array($result_query_list))

    {

     $row1=getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' and process_round ='".$row['id']."' order by inserted desc,apt.id desc","ap.id,ap.application_status as cur_status");

     

     switch ($row['id']) {

      case '1':

        $text_color = "screening rounded-0";

        break;

      case '2':

        $text_color = "telephone rounded-0";

        break;

      case '3':

        $text_color = "interview rounded-0";

        break;

      case '4':

        $text_color = "skill-check rounded-0";

        break;



      case '5':

        $text_color = "background-check rounded-0";

        break;

      

      default:

        $text_color = "reference-check rounded-0";

        break;

     }





     $round_name= '<span class="badge '.$text_color.' me-2 mb-1">'.tep_db_output($row[TEXT_LANGUAGE.'round_name']);

     switch($row1['id'])

     {

      case 1:

       $round_status.=(($round== $row['id'])?''.$round_name.' > '.IMAGE_NEW.'':$round_name.' > '.IMAGE_NEW);

       break;

      case 2:

       $round_status.=(($round== $row['id'])?''.$round_name.' > '.IMAGE_PROCESS.'':$round_name.' > '.IMAGE_PROCESS);

       break;

      case 3:

       $round_status.=(($round== $row['id'])?''.$round_name.' > '.IMAGE_COMPLITE_SELECT.'':$round_name.' > '.IMAGE_COMPLITE_SELECT);

       break;

      case 4:

       $round_status.=(($round== $row['id'])?''.$round_name.' > '.IMAGE_COMPLITE_WAIT.'':$round_name.' > '.IMAGE_COMPLITE_WAIT);

       break;

      case 5:

       $round_status.=(($round== $row['id'])?''.$round_name.' > '.IMAGE_COMPLITE_REJECT.'':$round_name.' > '.IMAGE_COMPLITE_REJECT);

       break;

      default:

       //$round_status.=tep_image(PATH_TO_IMAGE.'pending.gif',IMAGE_NEW);

     }

     if(tep_not_null($row1['id']))

     $round_status.='</span>';

    }//end while loop

    tep_db_free_result($result_query_list );

    return $round_status;

   }

  }

  function get_current_round_name($application_id)

  {

    $row2=getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id) left join ".SELECTION_ROUND_TABLE." sr on(sr.id =apt.process_round)"," application_id='".$application_id."' order by inserted desc,apt.id desc limit 0,1","sr.".TEXT_LANGUAGE."round_name");

    return $row2[TEXT_LANGUAGE.'round_name'].' Round';

  }

  function get_status_current_round($application_id,$round='')

  {

   if(tep_not_null($round))

    $row1=getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' and  process_round='".$round."' order by inserted desc,apt.id desc limit 0,1","ap.id");

   else

    $row1=getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' order by inserted desc,apt.id desc limit 0,1","ap.id");

   if($row1['id'] ==3)

   {

    $status=tep_image(PATH_TO_IMAGE.'icon_lol.gif',IMAGE_SELECTED);

   }

   elseif($row1['id'] ==4)

   {

    $status=tep_image(PATH_TO_IMAGE.'surprised.gif',IMAGE_WAITING);

   }

   elseif($row1['id'] ==5)

   {

    $status=tep_image(PATH_TO_IMAGE.'reject.gif',IMAGE_REJECT);

   }

   elseif($row1['id'] ==2)

   {

    $status=tep_image(PATH_TO_IMAGE.'icon_arrow.gif',IMAGE_PROCESS);

   }

   else

   {

    $status=tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_NEW);

   }

   return $status;

  }

  function get_application_rating($application_id)

  {

   $total=0;

   $total_string='';

   $row =getAnyTableWhereData(APPLICANT_STATUS_TABLE." as apt left join ".APPLICATION_STATUS_TABLE." as ap on (apt.cur_status=ap.id)"," application_id='".$application_id."' order by inserted desc,apt.id desc","apt.process_round");

   $db_app_status_query_raw = "select  *  from ".SELECTION_ROUND_TABLE." as ap  where value <='".$row['process_round']."' order by value";

   //$db_app_status_query_raw ;

   $db_app_status_query = tep_db_query($db_app_status_query_raw );

   $db_app_status_num_row = tep_db_num_rows($db_app_status_query);

   if($db_app_status_num_row > 0)

   {

    $alternate=1;

    while ($application_status = tep_db_fetch_array($db_app_status_query))

    {

     //print_r($application_status);die();

     if($row_rating=getAnyTableWhereData(APPLICATION_RATING_TABLE," application_id='".$application_id."' and round_id ='".$application_status['id']."'","point"))

     {

      $total=$total+$row_rating['point'];

      $total_string.=$row_rating['point'];

     }

     else

     $total_string.='0';

     $total_string.="+";

    }

    tep_db_free_result($db_app_status_query);

    $total_string=substr($total_string,0,-1);

    $total_string.="=<span style='color:red'>".$total."</span>";

   }

   if($total==0)

    $total_string='';

   return $total_string;

  }

  function get_job_enquiry_code($job_id)

		{

   $end_limit  = $job_id%7500;

	  if($end_limit>7000)

   {

    $start_limit = 7000;

    $chr ='ZY0';//7000

   }

	  elseif($end_limit>6000)

   {

    $start_limit = 6000;

    $chr ='WC0';// 6000

   }

   elseif($end_limit>5000)

   {

    $start_limit = 5000;

    $chr ='SG0';// 5000

   }

   elseif($end_limit>4000)

   {

    $start_limit = 4000;

    $chr ='OK0';//4000

   }

   elseif($end_limit>3000)

   {

    $start_limit = 3000;

    $chr ='KO0';// 3000

   }

   elseif($end_limit>2000)

   {

    $start_limit = 2000;

    $chr ='GS0'; //2000

   }

   elseif($end_limit>1000)

   {

    $start_limit = 1000;

    $chr ='CW0';// 1000

   }

   elseif($end_limit>500)

   {

    $start_limit = 500;

    $chr ='AY0' ;// 500

   }

	  else

	  {

    $start_limit = 1;

    $chr ='A1' ;// 1

	  }

   for($i=$start_limit;$i<$end_limit;$i++)

	  {

 	  $chr++;

   }

	  if($job_id>7500)

   return date('ym').$chr;

	  else

   return $chr;

  }

		/*

  {

   $id=(int)$job_id;

   if($id>0)

    $a='A1';

   for($i=1;$i<$id;$i++)

   {

    if($i=='182779')

     $a='A0';

    $a++;

   }

   return $a;

  }*/

    function create_dir($dir_name)

  {

   if(is_dir(PATH_TO_MAIN_PHYSICAL.$dir_name))

   {

    return true;

   }

   else

   {

    if(mkdir(PATH_TO_MAIN_PHYSICAL.$dir_name,0755))

     return true;

    else

     return 0;

   }

  }

  function create_index_file($dir_name)

  {

   if(is_file(PATH_TO_MAIN_PHYSICAL.$dir_name.'/index.php'))

   {

    return true;

   }

   elseif(is_dir(PATH_TO_MAIN_PHYSICAL.$dir_name))

   {

     $d = dir(PATH_TO_MAIN_PHYSICAL.$dir_name);

     $handle = fopen(PATH_TO_MAIN_PHYSICAL.$dir_name.'/index.php', "w");

     fwrite($handle, "<?php \n header('location:../'); \n ?>");

     fclose($handle);

     $d->close();

     return true;

   }

   else

   {

    return 0;

   }

  }

  function check_directory($dir_name)

  {

   if(tep_not_null($dir_name))

   {

    if(create_dir($dir_name))

    {

     if(create_index_file($dir_name))

      return true;

     else

      return 0;

    }

    else

    return 0;

   }

   else

    return 0;

  }

  function get_file_directory($file_name,$file_limit=8)

  {

   if(tep_not_null($file_name))

   {

    return substr($file_name,0,$file_limit);

   }

   else

    return 0;

  }

////////////////////////////////////

function get_company_direct_limit($char='')

{

 $start_limit=0;

 // $query_category ="select substring( ucase( recruiter_company_name ),1,1 ) as first_char, count(*) as first_char_company from ".RECRUITER_TABLE." group by first_char order by recruiter_company_name";

 $query_category ="select substring( ucase( recruiter_company_name ),1,1 ) as first_char, count(*) as first_char_company from ".RECRUITER_TABLE." as r left join ".RECRUITER_LOGIN_TABLE." as rl on ( r.recruiter_id = rl.recruiter_id) where rl.recruiter_status='Yes' group by first_char order by recruiter_company_name";

 $result=tep_db_query($query_category);

 while($row = tep_db_fetch_array($result))

 {

  $first_char=$row["first_char"];

  $total=$row["first_char_company"];

  if(strtoupper($char)!=strtoupper($first_char))

  {

   $start_limit=$start_limit+$total;

  }

  else

  {

   break;

  }

 }

 tep_db_free_result($result);

 return   $start_limit;

}

////////////////

function banner_display($banner_location=1,$no_of_banners=1,$size=0,$class='')

{

 $size=(int)$size;

  if ($class)

 {

  $style =$class;

 }

 $class=$class;

	$today=date("Y-m-d",mktime(date("m"), date("d"), date("Y")));

 $whereClause_ads=" (location='".tep_db_input($banner_location)."' && duration_unlimited='T' and deleted='F')|| (location='$banner_location' && duration_unlimited='F' && deleted='F' && banner_duration_from <= '".$today."' and banner_duration_to >= '".$today."')";

 //$whereClause_ads=" (banner_location_name='".tep_db_input($banner_location)."' && duration_unlimited='T' and deleted='F')|| (banner_location_name='$banner_location' && duration_unlimited='F' && deleted='F' && banner_duration_from <= '".$today."' and banner_duration_to >= '".$today."')";

 $banner_query_raw="select * from " . BANNER_TABLE ." as bn LEFT JOIN ".BANNER_LOCATION_TABLE ." as bn_loc on (bn.location=bn_loc.banner_location_id) where $whereClause_ads order by rand() limit 0,$no_of_banners";

 $banner_query = tep_db_query($banner_query_raw);

 $banner_ad = array();

 while ($banner = tep_db_fetch_array($banner_query))

 {

		if($banner['banner_type']=='image')

		{

   if(is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_BANNER.$banner['src']))

   {

    if($size>0)

     $banner_ad[]= "<a href='".tep_href_link('adclicks.php','bID='.$banner['id'])."' target='_blank'>".tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_BANNER.$banner['src']."&size=".$size,tep_db_output($banner['alt']),'','',$style)."</a>";

    else

     $banner_ad[]= "<a href='".tep_href_link('adclicks.php','bID='.$banner['id'])."' target='_blank'><img src='".tep_href_link(PATH_TO_BANNER.$banner['src'])."' border='0' alt='".tep_db_output($banner['alt'])."' title='".tep_db_output($banner['alt'])."' ".$style."></a>";

    $adviews=$banner['adviews']+1;

    $sql_data_array['adviews']=$adviews;

    tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner['id']."'");

   }

  }

		elseif($banner['banner_type']=='script')

		{

   if(tep_not_null($banner['script']))

   {

    $banner_ad[]= stripslashes($banner['script']);

    $adviews=$banner['adviews']+1;

    $sql_data_array['adviews']=$adviews;

    tep_db_perform(BANNER_TABLE, $sql_data_array,'update',"id='".(int)$banner['id']."'");

   }

  }

	}

	tep_db_free_result($banner_query);

 return $banner_ad;

}

function get_category_name_with_parent($category_ids)

{

 $category_ids=$category_ids;

 $category_id1=explode(',',$category_ids);

 $category_name=array();

 $total_cat=count($category_id1);

 for($i=0;$i<$total_cat;$i++)

 {

	 $category_id=$category_id1[$i];

  if($row =getAnyTableWhereData(JOB_CATEGORY_TABLE,"id='".$category_id."'","id,sub_cat_id,".TEXT_LANGUAGE."category_name"))

  {

   if(!tep_not_null($row['sub_cat_id']))//parent

   {

    $category_name[]=$row[TEXT_LANGUAGE.'category_name'];

   }

   else

   {

    $row1 =getAnyTableWhereData(JOB_CATEGORY_TABLE,"id='".$row['sub_cat_id']."'",TEXT_LANGUAGE."category_name");

    $category_name[]=($row1[TEXT_LANGUAGE.'category_name']." -� ".$row[TEXT_LANGUAGE.'category_name']);

   }

  }

 }

 $category_name=implode(', ',$category_name);

	return $category_name;

}

///////////////////////////////////////////////////////////////////////////////////////

function year_month_experience_drop($year_parameter="",$year='',$month_parameter="",$month='',$required=false,$show_name=false,$change_order=false)

{

 $start_year=1;

 $end_year=30;

	$year_string="<div class='col'><select ".$year_parameter.">";

 $year_string.="<option value=''>Year</option>";



	for($i=$start_year;$i<=$end_year;$i++)

	{

  $year_string.="<option value='$i'";

  if($year==$i)

  {

   $year_string.=" selected";

  }

  $year_string.=">".stripslashes($i)."</option>";

	}

 $year_string.="</select></div>";

	$month_string="<div class='col'><select ".$month_parameter.">";

 $month_string.="<option value=''>Month</option>";



	for($i=1;$i<12;$i++)

	{

  $month_string.="<option value='$i'";

  if($month==$i)

  {

   $month_string.=" selected";

  }

  $month_string.=">".$i."</option>";

	}

	$month_string.="</select></div>";

 if($required)

  //  $add1='&nbsp;<span class="inputRequirement">*</span>';

   $add1='';

 else

  $add1='';

  if($show_name and $change_order==false)

  //  $add1.="[ year-month ]";

  $add1.=" ";

  elseif($show_name and $change_order==true)

  //  $add1.=" [ month-year]";

  $add1.=" ";

  else

   $add1.="";

  if($change_order==true)

  //  return $month_string." - ".$year_string.$add1;

   return $month_string."  ".$year_string.$add1;

  else

	  // return $year_string." - ".$month_string.$add1;

	  return $year_string."  ".$month_string.$add1;

}

//////////////////////////////////////////////////

function tag_key_check($key)

{

 $keyword=tep_db_prepare_input($key);

 $ip_address=tep_db_prepare_input(tep_get_ip_address());

 $today=date('Y-m-d');

 if(tep_not_null($keyword) && strlen($keyword)>=3 )

 {

  if(!$row_check=getAnyTableWhereData(TAG_STATISTICS_TABLE,"tag='".tep_db_input($keyword)."' and ip_address='".tep_db_input($ip_address)."' and date='".$today."'",'tag'))

  {

   $sql_data_array=array( 'tag'=>$keyword,

                          'ip_address'=>$ip_address,

                          'date'=>$today);

   tep_db_perform(TAG_STATISTICS_TABLE, $sql_data_array);

   if($row_check1=getAnyTableWhereData(TAGS_TABLE,"tag='".tep_db_input($keyword)."'",'tag'))

   {

    $temp_result=tep_db_query("update ".TAGS_TABLE." set point=point+1 where tag  = '".tep_db_input($keyword)."'");

   }

   else

   {

    $temp_result=tep_db_query("insert into ".TAGS_TABLE." (tag,point) values  ('".tep_db_input($keyword)."',1)");

   }

  }

 }

}

function get_tagCloud()

{

 include_once(PATH_TO_MAIN_PHYSICAL_CLASS .'wordcloud.php');

 include(PATH_TO_MAIN_PHYSICAL.'top_tags.txt');

 $cloud = new tagCloudWeight($randomWords);

 $tag_cloud1=array();

 $tag_cloud=array();

 if($cloud_word=$cloud->set_weight())

 {

  $total_word=count($cloud_word);

  shuffle($cloud_word);

  for($i=0;$i<$total_word;$i++)

  {

   if($cloud_word[$i]['value']>=99)

  			$tag_cloud[$i] = '<span class="size16">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=70)

  			$tag_cloud[$i] = '<span class="size16">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=60)

  			$tag_cloud[$i] = '<span class="size15">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=50)

  			$tag_cloud[$i] = '<span class="size14">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=40)

  			$tag_cloud[$i] = '<span class="size13">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=30)

  			$tag_cloud[$i] = '<span class="size12">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=20)

  			$tag_cloud[$i] = '<span class="size11">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=10)

  			$tag_cloud[$i] = '<span class="size10">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

   elseif($cloud_word[$i]['value']>=5)

  			$tag_cloud[$i] = '<span class="size9">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

  		else

  			$tag_cloud[$i] = '<span class="size8">'.tep_db_output($cloud_word[$i]['tag']).'</span> ';

    $tag_cloud1[]=array('tag'=>$cloud_word[$i]['tag'],'value'=>$tag_cloud[$i]);

  }

 }

 return $tag_cloud1;

}

///////////////////////////////////////////////

/*function encode_url($url)

{

 $url=tep_db_prepare_input($url);

 $url=str_replace(array("_"),array("__"),$url);

 $url=str_replace(array("&","/","\\","+","*","#"),array("_and1_","_fslash_","_bslash_","_plus_","_star_","_hess_"),$url);

 $url=str_replace(array(" "),array("_"),$url);

 return $url;

}

function decode_url($url)

{

 $url=tep_db_prepare_input($url);

 $url=str_replace(array("_and1_","_plus_","_fslash_","_fslash_","_star_","_hess_"),array("&","+","/","\\","*","#"),$url);

 $url=str_replace("_"," ",$url);

 return $url;

}*/

function encode_category($url)

{

 $url=tep_db_prepare_input($url);

$url = preg_replace('/[^a-zA-Z0-9\-]/', '-', $url);

$url = preg_replace('/^[\-]+/', '-', $url);

$url = preg_replace('/[\-]+$/', '-', $url);

$url = preg_replace('/[\-]{2,}/', '-', $url);

return $url;

}

function decode_category($url)

{

 $url=explode('-',$url);

 if($row_check_cat=getAnyTableWhereData(JOB_CATEGORY_TABLE,TEXT_LANGUAGE."category_name like'".tep_db_input($url['0'])."%".tep_db_input($url['1'])."%".tep_db_input($url['2'])."%".tep_db_input($url['3'])."%'",TEXT_LANGUAGE.'category_name'))

 return $row_check_cat[TEXT_LANGUAGE.'category_name'];

}

function tep_get_google_analytics_code()

{

 $string='';

 if(!(defined('MOGULE_GOOGLE_ANALYTICES') && defined('MOGULE_GOOGLE_ANALYTICES_UA_ID')))

  return $string;

  if(tep_not_null(MOGULE_GOOGLE_ANALYTICES_UA_ID) && MOGULE_GOOGLE_ANALYTICES_UA_ID !='UA-XXXXX-X' && MOGULE_GOOGLE_ANALYTICES=='enable')

  {

   /*$string ='<script type="text/javascript">

             var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");

             document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));

             </script>

             <script type="text/javascript">

              try{

              var pageTracker = _gat._getTracker("'.MOGULE_GOOGLE_ANALYTICES_UA_ID.'");

              pageTracker._trackPageview();

              }

              catch(err) {}

              </script>';

*/

$string ="<script async src='https://www.googletagmanager.com/gtag/js?id=".MOGULE_GOOGLE_ANALYTICES_UA_ID."'></script>

<script>

  window.dataLayer = window.dataLayer || [];

  function gtag(){dataLayer.push(arguments);}

  gtag('js', new Date());



  gtag('config', '".MOGULE_GOOGLE_ANALYTICES_UA_ID."');

</script>";

  }

  return $string;

}

function zone_radius($name='radius',$header="",$header_value="",$selected="",$required=true,$parameters='')

{

 $radius_array=array();

 if($header!='')

 $radius_array[]=array("id"=>$header_value,"text"=>$header);

 $radius_array[]=array("id"=>1,"text"=>'1 Mile');

 $radius_array[]=array("id"=>2,"text"=>'2 Miles');

 $radius_array[]=array("id"=>3,"text"=>'3 Miles');

 $radius_array[]=array("id"=>4,"text"=>'4 Miles');

 $radius_array[]=array("id"=>5,"text"=>'5 Miles');

 $radius_array[]=array("id"=>10,"text"=>'10 Miles');

 $radius_array[]=array("id"=>15,"text"=>'15 Miles');

 $radius_array[]=array("id"=>20,"text"=>'20 Miles');

 $radius_array[]=array("id"=>25,"text"=>'25 Miles');

 $radius_array[]=array("id"=>30,"text"=>'30 Miles');

 $radius_array[]=array("id"=>35,"text"=>'35 Miles');

 $radius_array[]=array("id"=>40,"text"=>'40 Miles');

 $radius_array[]=array("id"=>45,"text"=>'45 Miles');

 $radius_array[]=array("id"=>50,"text"=>'50 Miles');

 $radius_array[]=array("id"=>75,"text"=>'75 Miles');

 $radius_array[]=array("id"=>100,"text"=>'100 Miles');

 return tep_draw_pull_down_menu($name, $radius_array, $selected, $parameters,$required);

}



function encode_forum($url)

{

 $url=tep_db_prepare_input($url);

$url = preg_replace('/[^a-zA-Z0-9\-]/', '-', $url);

$url = preg_replace('/^[\-]+/', '-', $url);

$url = preg_replace('/[\-]+$/', '-', $url);

$url = preg_replace('/[\-]{2,}/', '-', $url);

return $url;

}

function decode_forum($url)

{

 $url=explode('-',$url);

 if($row_check_cat=getAnyTableWhereData(FORUM_TABLE,"title like'".tep_db_input($url['0'])."%".tep_db_input($url['1'])."%".tep_db_input($url['2'])."%".tep_db_input($url['3'])."%'",'title'))

 return $row_check_cat['title'];

}

function decode_country($url)

{

 $url=explode('-',$url);

 if($row_check_cat=getAnyTableWhereData(COUNTRIES_TABLE,"country_name like'".tep_db_input($url['0'])."%".tep_db_input($url['1'])."%".tep_db_input($url['2'])."%".tep_db_input($url['3'])."%'",'id,country_name,de_country_name'))

 return $row_check_cat;

}

function decode_state($url)

{

	if(tep_not_null($url))

	{

		$url=explode('-',$url);

  if($row_check_cat=getAnyTableWhereData(ZONES_TABLE,TEXT_LANGUAGE."zone_name like'".tep_db_input($url['0'])."%".tep_db_input($url['1'])."%".tep_db_input($url['2'])."%".tep_db_input($url['3'])."%'",'*'))

  return $row_check_cat;

	}

}

function set_twiter_status($status='',$url='')

{

 $status=tep_db_prepare_input($status);

 $url=tep_db_prepare_input($url);

 if($bitly_url=get_bitly_url($url))

  $url=tep_db_prepare_input($bitly_url);

 $url_length=strlen($url)+2;

 if($url_length<=140)

  $remain_char=140-$url_length;

 else

  $remain_char=strlen($status)+2;

 return substr($status,0,$remain_char).' '.$url;

}

function get_bitly_url($url)

{

 if($url=='')

 return false;

 if(MODULE_BITLY_STATUS!='enable')

 return false;

 $bitly_version = '2.0.1';

 $bitly_login   = MODULE_BITLY_USER_ID;

 $bitly_key     = check_data1(MODULE_BITLY_API_KEY,'#@#','api','key');

 $ch    = curl_init();

 curl_setopt($ch, CURLOPT_URL,'http://api.bit.ly/shorten?version='.urlencode($bitly_version).'&longUrl='.urlencode($url).'&login='.urlencode($bitly_login).'&apiKey='.urlencode($bitly_key).'&format=xml&history=1');

 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

 curl_setopt($ch, CURLOPT_TIMEOUT,60);

 $result=curl_exec($ch);

 $h_info=curl_getinfo($ch);

 curl_close($ch);

 if(preg_match("/<errorCode>(.*)<\/errorCode>/si",$result,$error))

 {

  if($error[1]!=0)

   return false;

  else

  {

   preg_match("/<shortUrl>(.*)<\/shortUrl>/si",$result,$shorturl);

   return $shorturl[1];

  }

 }

 return false;

}

function get_search_date($posted='',$s_year='',$s_month='',$s_date='',$e_year='',$e_month='',$e_date='')

{

 if(!tep_not_null($posted))

 return false;

 $date_array=array();

 switch($posted)

 {

  case '0':

   $search_from = date("Y-m-d H:i:s", mktime(0, 0,0, date("m"),date("d"), date("Y")));

   $search_to= date("Y-m-d H:i:s", mktime(59, 59,59, date("m"),date("d"), date("Y")));

   $date_array=array('search_from'=>$search_from,'search_to'=>$search_to);

   break ;

  case 1:

  case 7:

  case 14:

  case 21:

  case 30:

   $search_from = date("Y-m-d H:i:s", mktime(0, 0,0, date("m"),date("d"), date("Y")));

   $search_to= date("Y-m-d H:i:s", mktime(59, 59,59, date("m"),date("d")-$posted, date("Y")));

   $date_array=array('search_from'=>$search_from,'search_to'=>$search_to);

   break ;

  case 'custom':

    $start_date=$end_date='';

    if($s_month!='' && $s_date !='' && $s_year!='')

    {

     if(checkdate($s_month,$s_date,$s_year))

     $start_date  =$s_year.'-'.$s_month.'-'.$s_date;

    }

    if($e_month!='' && $e_date !='' && $e_year!='')

    {

     if(checkdate($e_month,$e_date,$e_year))

     $end_date  =$e_year.'-'.$e_month.'-'.$e_date;

    }

    if($start_date<=$end_date && $end_date!='' &&  $start_date!='')

    {

     $temp_date=$start_date;

     $start_date=$end_date;

     $end_date=$temp_date;

    }

    elseif($end_date=='')

     $end_date=$start_date;

    if($start_date!='')

    {

     $date_array=explode('-',$start_date);

     $search_from = date("Y-m-d H:i:s", mktime(59,59,59, $date_array[1],$date_array[2], $date_array[0]));

    }

    if($end_date!='')

    {

     $date_array=explode('-',$end_date);

     $search_to = date("Y-m-d H:i:s", mktime(0,0,0, $date_array[1],$date_array[2], $date_array[0]));

    }

    $date_array=array('search_from'=>$search_from,'search_to'=>$search_to);

   break ;

  }

 return $date_array;

}

function get_canonical_title($title='',$id=0,$section='article')

{

 $title = tep_db_prepare_input(strtolower($title));

 $id    = tep_db_prepare_input($id);

 $qnique_title=false;

 $new_title=get_display_filter_title($title);

 if($title=='')

  return false;

 switch($section)

 {

  case'article':

   if(tep_not_null($id))

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(ARTICLE_TABLE,"id!='".tep_db_input($id)."' && seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   else

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(ARTICLE_TABLE,"seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   return $new_title;

   break;

   case'category':

	if(tep_not_null($id))

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(JOB_CATEGORY_TABLE,"id!='".tep_db_input($id)."' && seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   else

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(JOB_CATEGORY_TABLE,"seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   case'company':

   if(tep_not_null($id))

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id!='".tep_db_input($id)."' && recruiter_company_seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   else

   {

    $i=2;

    while(!$qnique_title)

    {

     if($row_check_article_id=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_company_seo_name='".tep_db_input($new_title)."' "))

     {

      $new_title=get_display_filter_title($title.'-'.$i);

     }

     else

     {

      $qnique_title=true;

     }

     $i++;

    }

   }

   return $new_title;

   break;

  

   return $new_title;

	  break;

  

  default:

   return get_display_filter_title($title);

 }

}

function get_display_filter_title($title='')

{

 $title=tep_db_prepare_input(strtolower($title));

 $patterns=array('/[^a-zA-Z0-9\-]/','/^[\-]+/','/[\-]+$/','/[\-]{2,}/');

 $replacements=array('-','-','-','-');

 return preg_replace($patterns,$replacements,$title);



}

function get_display_link($id=0,$seo_name='')

{

 return get_display_filter_title($seo_name).'.html';

}

function autolink( $text='', $target='_blank', $nofollow=false )

{  // grab anything that looks like a URL...

  $urls  =  _autolink_find_URLS( $text );

  if( !empty($urls) ) // i.e. there were some URLS found in the text

  {

    array_walk( $urls, '_autolink_create_html_tags', array('target'=>$target, 'nofollow'=>$nofollow) );

    $text  =  strtr( $text, $urls );

  }

 $text = preg_replace("/href='www/", "href='http://www", $text);

 $text = preg_replace("/href=\"www/", "href=\"http://www", $text);

	return $text;

}



function _autolink_find_URLS( $text )

{

  // build the patterns

  $scheme         =       '(http:\/\/|https:\/\/)';

  $www            =       'www\.';

  $ip             =       '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';

  $subdomain      =       '[-a-z0-9_]+\.';

  $name           =       '[a-z][-a-z0-9]+\.';

  $tld            =       '[a-z]+(\.[a-z]{2,2})?';

  $the_rest       =       '\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1}';

  $pattern        =       "$scheme?(?(1)($ip|($subdomain)?$name$tld)|($www$name$tld))$the_rest";



  $pattern        =       '/'.$pattern.'/is';

  $c              =       preg_match_all( $pattern, $text, $m );

  unset( $text, $scheme, $www, $ip, $subdomain, $name, $tld, $the_rest, $pattern );

  if( $c )

  {

    return( array_flip($m[0]) );

  }

  return( array() );

}



function _autolink_create_html_tags( &$value, $key, $other=null )

{

  $target = $nofollow = null;

  if( is_array($other) )

  {

    $target      =  ( $other['target']   ? " target=\"$other[target]\"" : null );

    $nofollow    =  ( $other['nofollow'] ? ' rel="nofollow"'            : null );

  }

  $value = "<a href=\"$key\"$target$nofollow>$key</a>";

}



////////////////////////////////////////////////////////////////////////////////

////////////////functions to auto renew jobs////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////

function get_auto_renew_name($id ='0')

{

	$auto_renew=get_auto_renew_list();

	return  $auto_renew[$id];

}



function get_auto_renew_list()

{

	$auto_renew=array();

 $auto_renew['0']='None';

 $auto_renew['3']='3 Days';

 $auto_renew['7']='7 Days';

 $auto_renew['14']='14 Days';

 $auto_renew['21']='21 Days';

 $auto_renew['28']='28 Days';

	return  $auto_renew;

}



function list_job_auto_renew($parameters ,$header="",$header_value="",$selected="",$required=false)

{

	$auto_renew=get_auto_renew_list();

	$string="";

	$string.="<select ".$parameters.">";

	if($header!="")

	{

		$string.="<option value='".$header_value."'>$header</option>";

	}

	$selected=explode(",",$selected);

	foreach($auto_renew as $key => $value)

	{

  $fields= $value;

  $string.="<option value='$key'";

  if(in_array($key,$selected))

  {

   $string.=" selected";

  }

  $string.="> $fields  </option>";

 }

	$string.="</select>";

 if($required)

//  $string.='&nbsp; <span class="inputRequirement">*</span>';

 $string.='';

	return$string;

}

if (!function_exists('getLocationGeoAddress ')):

function getLocationGeoAddress ($parameter='')

{

 $google_maps_json_url = 'http://maps.googleapis.com/maps/api/geocode/json?key='.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'&'.$parameter;

 $address_array=array();

 if (function_exists('curl_init') )

 {

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  curl_setopt($ch, CURLOPT_URL,$google_maps_json_url);

  curl_setopt($ch, CURLOPT_TIMEOUT, 30);

  $data = curl_exec($ch);

  $error = curl_error($ch);

  curl_close($ch);

  if($data && $error =='')

  {

   $json = json_decode($data );

   if(isset($json->results[0]))

   $address_array = array(

					'longitude' => $json->results[0]->geometry->location->lng,

					'latitude' 	=> $json->results[0]->geometry->location->lat

	                );

  }

 }

 return  $address_array;

}

endif;

function get_user_opt($email_address='',$user='jobseeker')

{

 $todat=date("Y-m-d");

 $email_address=tep_db_prepare_input($email_address);

 if(!tep_not_null($email_address))

  return false;

 $user_opt= randomize();

 if($check_row = getAnyTableWhereData(USER_OPT_TABLE, " email_address='" . tep_db_input($email_address)."'", "date,opt"))

 {

   tep_db_query('update '.USER_OPT_TABLE ." set date='".$todat."', opt='" . tep_db_input($user_opt)."'  where email_address = '" . tep_db_input($email_address)."'") ;

 }

 else

 {

  $sql_data_array = array();

  $sql_data_array = array('email_address' => $email_address,

                       'date'  => $todat,

	                   'user'  =>$user,

                       'opt'   => $user_opt,

                       );

  tep_db_perform(USER_OPT_TABLE, $sql_data_array);

 }

 return $user_opt;

}

function check_user_opt($email_address='',$opt='')

{

 $todat=date("Y-m-d");

 $email_address=tep_db_prepare_input($email_address);

 $opt=tep_db_prepare_input($opt);

 if(!tep_not_null($email_address))

  return false;

 if(!tep_not_null($opt))

  return false;



 if($check_row = getAnyTableWhereData(USER_OPT_TABLE, " email_address='" . tep_db_input($email_address)."'  and opt='" . tep_db_input($opt)."' and date='".$todat."'", "date,opt,user"))

 {

  return $check_row['user'];

 }

 else

  return false;

}

function password_acknowledgement_mail($user_name,$email_address='')

{

 tep_db_query('delete from '.USER_OPT_TABLE ." where email_address='".$email_address."'") ;

 $template1 = new Template(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE);

if($_SESSION['language']=="german")

	 $template1->set_filenames(array('mail' => 'de_password_ack_template.htm'));

else

 $template1->set_filenames(array('mail' => 'password_ack_template.htm'));

 $template1->assign_vars(array(

    'USER'=>$user_name,

    'SITE_TITLE'=>SITE_TITLE,

  ));

 $email_text=stripslashes($template1->pparse1('mail'));

 tep_mail($user_name , $email_address, "Password changed successfully",$email_text, SITE_OWNER, EMAIL_FROM);

}



function send_reset_opt($parameter=array())

{

 $user_name     = $parameter['user_name'];

 $email_address = $parameter['email_address'];

 $user_opt      = $parameter['opt'];

 $reset_link    = $parameter['reset_link'];



 $template1 = new Template(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE);

if($_SESSION['language']=="german")

	 $template1->set_filenames(array('mail' => 'reset_password_template.htm'));

else

	 $template1->set_filenames(array('mail' => 'de_reset_password_template.htm'));



 $template1->assign_vars(array(

    'reset_password_url'=>$reset_link,

    'reset_password_code'=>$user_opt,

  ));

 $email_text=stripslashes($template1->pparse1('mail'));

 tep_mail($user_name , $email_address, "Account Recovery Code",$email_text, SITE_OWNER, EMAIL_FROM);

}





///



if (!function_exists('getSkillTagLink'))

{

 function getSkillTagLink($tags='')

 {

  $tags=tep_db_prepare_input($tags);

  $tag_array =explode(',',$tags);

  $string="";

  foreach ($tag_array as $tag)

  {

   $tag=trim($tag);

   $search_tag=str_replace(array(" ","/","\\"),array("+","_","_"),$tag);

   $string.=' <a href="'.getPermalink('skill',array('seo_name'=>$search_tag)).'">'.trim($tag).'</a>';

  }

  return $string;

 }

}

if (!function_exists('getSkillTagValueForSearch'))

{

 function getSkillTagValueForSearch($tags='')

 {

  $tags=tep_db_prepare_input($tags);

  $tag_array =explode(',',$tags);

  $string="";

  foreach ($tag_array as $tag)

  {

   $tag=trim($tag);

   $search_tag=str_replace(array(" ","/","\\"),array("+","_","_"),$tag);

   $createUniqueFormId = "tag-".$search_tag;

   $string.=tep_draw_form('search', FILENAME_JOB_SEARCH,'','post','class="d-inline skill-tags" id="'.$createUniqueFormId.'" ')

   .tep_draw_hidden_field('action','search')

   .tep_draw_hidden_field('skillTag',$search_tag).

   '<button type="submit" class="btn btn-light my-1 d-inline">'.trim($tag).'</button></form>';

  }

  return $string;

 }

}

if (!function_exists('insertSkillTag'))

{

 function insertSkillTag($tags='')

 {

  $tags=tep_db_prepare_input($tags);

  $tag_array =explode(',',$tags);

  foreach ($tag_array as $tag)

  {

   $tag=trim($tag);

   $sql_data_array=array('tag' =>$tag);

   if(!$row_chek=getAnyTableWhereData(SKILL_TAGS_TABLE,"tag='".tep_db_input($tag)."'",'id'))

   tep_db_perform(SKILL_TAGS_TABLE, $sql_data_array);

  }

 }

}

function  tep_site_magic_quotes()

{

 if(function_exists("get_magic_quotes_runtime"))

 {

  $magic_quotes = get_magic_quotes_runtime();

  if ($magic_quotes) {

		if (version_compare(PHP_VERSION, '5.3.0', '<')) {

			set_magic_quotes_runtime(false);

		} else {

			//Doesn't exist in PHP 5.4, but we don't need to check because

			//get_magic_quotes_runtime always returns false in 5.4+

			//so it will never get here

			ini_set('magic_quotes_runtime', false);

		}

	}

  }

}



////////////////functions to auto renew jobs////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////

function getSalaryRangeName($id ='0')

{

	$salary_range=getSalaryRangelist();

	return  $salary_range[$id];

}



function getCurrencyCode() {



  try {

    $default_curr = DEFAULT_CURRENCY;

    $column = "symbol_left,symbol_right";

    $table = CURRENCY_TABLE;



    $query = "select $column from $table where code='$default_curr'";

    $row_cur=tep_db_query($query);

    $result = tep_db_fetch_array($row_cur);



    if($result){

      if ($result['symbol_left']) {

        return $result['symbol_left'];

      } elseif ($result['symbol_right']) {

        return $result['symbol_right'];

      } else {

        return 'null';

      }

    }

  } catch (Exception $e) {

    return 'Exception '. $e;

  }

}



function getSalaryRangelist()

{

 $salary_range=array();

 $salary_range['1']= getCurrencyCode().' 1 - 50,000';

 $salary_range['2']= getCurrencyCode().' 50,000 - 75,000';

 $salary_range['3']= getCurrencyCode().' 75,000 - 100,000';

 $salary_range['4']= getCurrencyCode().' 100,000 - 125,000';

 $salary_range['5']= getCurrencyCode().' 125,000 +';

 $salary_range['6']= getCurrencyCode().' 0';



 return  $salary_range;

}



function listSalaryRange($parameters ,$header="",$header_value="",$selected="",$required=false)

{

	$salary=getSalaryRangelist();

	$string="";

	$string.="<select ".$parameters.">";

	if($header!="")

	{

		$string.="<option value='".$header_value."'>$header</option>";

	}

	$selected=explode(",",$selected);

	foreach($salary as $key => $value)

	{

  $fields= $value;

  $string.="<option value='$key'";

  if(in_array($key,$selected))

  {

   $string.=" selected";

  }

  $string.="> $fields  </option>";

 }

	$string.="</select>";

 if($required)

 $string.='&nbsp; <span class="inputRequirement">*</span>';

	return$string;

}

function getSalaryQuery($id ='0')

{

 $query='';

  switch($id)

  {

   case 1:

	 $query= '( j.job_salary > 0  and j.job_salary < 50000 )';

     break;

   case 2:

	$query ='( j.job_salary >= 50000  and j.job_salary < 75000 ) ';

	break;

   case 3:

	$query  ='( j.job_salary >= 75000  and j.job_salary < 100000 ) ';

	break;

   case 4:

	$query ='( j.job_salary >= 100000  and j.job_salary < 125000 ) ';

	break;

   case 5:

	$query ='( j.job_salary >= 125000) ';

	break;

   case 6:

	$query ='( j.job_salary = 0) ';

	break;   default :

		$query ='1';

   }

   return $query;



}

function getPermalink($section,$parameter=array())

{

 switch($section)

 {

  case 'job':

	  $ide      = $parameter['ide'];

	  $seo_name = $parameter['seo_name'];

	  $other_parameter = $parameter['other'];

	  return tep_href_link($ide.'/'.$seo_name,$other_parameter);

   break;

  case 'category':

 	  $seo_name = $parameter['seo_name'];

	  $other_parameter = $parameter['other'];

 	  return tep_href_link($seo_name.'-jobs/',$other_parameter);

   break;

   case 'sub_category':

    $seo_name = $parameter['seo_name'];

   $other_parameter = $parameter['other'];

    return tep_href_link($seo_name.'-jobs/',$other_parameter);

  break;


  case 'article':

 	  $ide = $parameter['ide'];

	  $seo_name = $parameter['seo_name'];

	  $other_parameter = $parameter['other'];

 	  return tep_href_link($seo_name.'/',$other_parameter);

   break;

  case 'skill':

 	  $seo_name = $parameter['seo_name'];

 	  return tep_href_link('jobskill/'.$seo_name.'-jobs');

   break;

  case 'company':

 	  $seo_name = $parameter['seo_name'];

 	  return tep_href_link('company/'.$seo_name.'/');

   break;

  case 'state':

 	  $continent_name = $parameter['continent_name'];

 	  $country_name   = $parameter['country_name'];

 	  $zone_name      = $parameter['zone_name'];

 	  return tep_href_link('company/'.$seo_name.'/');

   break;

  case 'article_category':

 	  $ide = $parameter['ide'];

	  $other_parameter = $parameter['other'];

 	  return tep_href_link('article_cat_'.$ide.'.html',$other_parameter);

   break;

   case 'forum_topics':

	  $ide      = $parameter['ide'];

	  $seo_name = $parameter['seo_name'];

	  $other_parameter = $parameter['other'];

	  return tep_href_link(PATH_TO_FORUM.$ide.'/'.$seo_name.'.html',$other_parameter);

    break;

  case 'topic_details':

	  $ide      = $parameter['ide'];

	  $seo_name = $parameter['seo_name'];

	  $other_parameter = $parameter['other'];

	  return tep_href_link(PATH_TO_FORUM.$seo_name.'_'.$ide.'.html',$other_parameter);

    break;

  case FILENAME_ABOUT_US:

	  return tep_href_link('about-us/');

    break;

  case 'article.php':

	  return tep_href_link('article/');

    break;

  case 'contact_us.php':

	  return tep_href_link('contact-us/');

    break;

  case FILENAME_LOGIN:

	 $other_parameter = $parameter['other'];

      return tep_href_link('login/',$other_parameter);

    break;

 case FILENAME_RECRUITER_LOGIN:

	 $other_parameter = $parameter['other'];

      return tep_href_link('recruiter-login/',$other_parameter);

    break;

  case FILENAME_JOBSEEKER_REGISTER1:

	 $parameter = $parameter['parameter'];

      return tep_href_link('jobseeker-register/',$parameter);

    break;

  case FILENAME_RECRUITER_REGISTRATION:

	 $parameter = $parameter['parameter'];

      return tep_href_link('recruiter-registation/',$parameter);

    break;

  case FILENAME_JOB_SEARCH:

	 $parameter = $parameter['parameter'];

      return tep_href_link('job-search/',$parameter);

    break;

  case PATH_TO_LMS.LMS_COURSES_FILENAME:

	  return tep_href_link(PATH_TO_LMS.'courses/');

    break;

  case PATH_TO_LMS.LMS_LIST_COURSES_FILENAME:

	  return tep_href_link(PATH_TO_LMS.'courses-list/');

    break;

  case FILENAME_JOBFAIR:

	  return tep_href_link('jobfair/');

    break;

  case 'site_map.php':

	  return tep_href_link('site-map/');

    break;

  case 'terms.php':

	  return tep_href_link('terms/');

    break;

  case 'faq.php':

	  return tep_href_link('faq/');

    break;

  case 'privacy.php':

	  return tep_href_link('privacy/');

    break;

  case 'job_search_by_location.php':

	  return tep_href_link('job-search-by-location/');

    break;

  case 'job_search_by_industry.php':

	  return tep_href_link('job-search-by-industry/');

    break;

  case 'job_search_by_skill.php':

	  return tep_href_link('job-search-by-skill/');

    break;

  case 'company_profile.php':

	  return tep_href_link('jobs-by-company/');

    break;

  default:

	 $parameters = $parameter['parameters'];

	  return tep_href_link($section,$parameters);

  }

}



function count_recruiter_jobseeker_reply($recruiter_id)

{

  $query = "SELECT COUNT(*) AS count

              FROM applicant_interaction AS ai

              LEFT JOIN application AS a ON (a.id = ai.application_id)

              LEFT OUTER JOIN jobseeker AS j ON (a.jobseeker_id = j.jobseeker_id)

              LEFT JOIN jobs AS jb ON (a.job_id = jb.job_id)

              LEFT JOIN recruiter AS r ON (jb.recruiter_id = r.recruiter_id)

              WHERE jb.recruiter_id = '$recruiter_id' 

              AND ai.receiver_mail_status = 'active' 

              AND sender_user = 'jobseeker'";



  $result = tep_db_query($query);



  // Fetching the result as an associative array

  $row = tep_db_fetch_array($result);



  // Returning the count

  return $row['count'];

}



function count_admin_responses_for_recruiter($recruiter_id) {

  $query = "SELECT COUNT(*) AS count

              FROM admin_employer_mails AS em

              LEFT JOIN recruiter AS r ON (em.receiver_id = r.recruiter_id)

              WHERE em.sender_id = 0 

              AND em.receiver_id = '$recruiter_id' 

              AND em.receiver_mail_status = 'active'";



  $result = tep_db_query($query);



  $row = tep_db_fetch_array($result);



  return $row['count'];

}



function tep_get_menu_link_list($default='')

{

 $link_array=array();

 $link_array[]=array('id'=>'','text'=>'');

 $link_array[]=array('id'=>'HOME','text'=>'Home');



 $link_array[]=array('id'=>'FILENAME_LOGIN','text'=>FILENAME_LOGIN);

 $link_array[]=array('id'=>'FILENAME_LOGOUT','text'=>FILENAME_LOGOUT);

 $link_array[]=array('id'=>'FILENAME_JOBSEEKER_REGISTER1','text'=>FILENAME_JOBSEEKER_REGISTER1);

 $link_array[]=array('id'=>'FILENAME_JOBSEEKER_CONTROL_PANEL','text'=>FILENAME_JOBSEEKER_CONTROL_PANEL);



 $link_array[]=array('id'=>'FILENAME_RECRUITER_LOGIN','text'=>FILENAME_RECRUITER_LOGIN);

 $link_array[]=array('id'=>'FILENAME_RECRUITER_REGISTRATION','text'=>FILENAME_RECRUITER_REGISTRATION);

 $link_array[]=array('id'=>'FILENAME_RECRUITER_CONTROL_PANEL','text'=>FILENAME_RECRUITER_CONTROL_PANEL);

 $link_array[]=array('id'=>'FILENAME_RECRUITER_POST_JOB','text'=>FILENAME_RECRUITER_POST_JOB);



 $link_array[]=array('id'=>'FILENAME_JOB_SEARCH','text'=>FILENAME_JOB_SEARCH);

 $link_array[]=array('id'=>'FILENAME_JOB_SEARCH_BY_INDUSTRY','text'=>FILENAME_JOB_SEARCH_BY_INDUSTRY);

 $link_array[]=array('id'=>'FILENAME_JOB_SEARCH_BY_LOCATION','text'=>FILENAME_JOB_SEARCH_BY_LOCATION);

 $link_array[]=array('id'=>'FILENAME_JOBSEEKER_COMPANY_PROFILE','text'=>FILENAME_JOBSEEKER_COMPANY_PROFILE);

 $link_array[]=array('id'=>'FILENAME_JOB_SEARCH_BY_SKILL','text'=>FILENAME_JOB_SEARCH_BY_SKILL);



 $link_array[]=array('id'=>'FILENAME_ABOUT_US','text'=>FILENAME_ABOUT_US);

 $link_array[]=array('id'=>'FILENAME_ARTICLE','text'=>FILENAME_ARTICLE);

 $link_array[]=array('id'=>'FILENAME_FAQ','text'=>FILENAME_FAQ);

 $link_array[]=array('id'=>'FILENAME_JOBFAIR','text'=>FILENAME_JOBFAIR);

 $link_array[]=array('id'=>'FILENAME_PRIVACY','text'=>FILENAME_PRIVACY);

 $link_array[]=array('id'=>'FILENAME_SITE_MAP','text'=>FILENAME_SITE_MAP);

 $link_array[]=array('id'=>'FILENAME_TERMS','text'=>FILENAME_TERMS);

 $link_array[]=array('id'=>'custom','text'=>'custom');

 return tep_draw_pull_down_menu('menu_link', $link_array, $default,'  class="form-control-sm" id="menu_link"');

}

function update_theme_menu()

{

  $str='<?php'."\n";

  $str.='$menu_list =array();'."\n";



  $query = "select * from " . THEME_MENU_TABLE . " as m   where menu_parent is null and status='active' order by  priority asc ";

  //echo $query; die();

   $result = tep_db_query($query);

    $num_row = tep_db_num_rows($result);

    if($num_row > 0)

    {

      $alternate=1;

      while ($row = tep_db_fetch_array($result))

      {

		   $sql = "select * from " . THEME_MENU_TABLE . " as m   where menu_parent ='".tep_db_input($row['id'])."' and status='active' order by  priority asc ";

           $result1 = tep_db_query($sql);

           $num_row1 = tep_db_num_rows($result1);

           $sub_menu_list =array();

		   if($num_row1>0)

		   {

 			 while ($data = tep_db_fetch_array($result1))

			 {

			  $sub_menu_list[] =array("text" =>tep_db_output($data['menu_title']),"text1" =>tep_db_output($data['menu_title']),"user_type" =>tep_db_output($data['user_type']),"link" =>stripslashes(get_menu_link($data['menu_link'])),"parameter" =>str_replace('"','\"',$data['menu_parameter']));

		     }

 		   }

           

			$str.='$menu_list[] =array("text" =>"'.tep_db_output($row['menu_title']).'","text1" =>"'.tep_db_output($row['menu_title1']).'","user_type" =>"'.tep_db_output($row['user_type']).'","link" =>"'.stripslashes(get_menu_link($row['menu_link'])).'","parameter" =>"'.str_replace('"','\"',$row['menu_parameter']).'","sub_menu" =>'.var_export($sub_menu_list,1) .');'."\n";



	  }

	  tep_db_free_result($result);

	}

	$str.='?>';

	     $handle = fopen(PATH_TO_MAIN_PHYSICAL.'theme_menu.php', "w");

     fwrite($handle,  stripslashes($str));

     fclose($handle);

}

function get_menu_link($link)

{

 if ($link=='HOME')

 {

  return getPermalink('');  

 }

 elseif(substr($link,0,2)=='##')

 {

	 return substr($link,2);

 }

 elseif(defined($link))

 {

	 return getPermalink(constant($link));

 }

 if (!tep_not_null($link))

 {

  return '';  

 }

 else

 return getPermalink($link);



}

function get_menu_list()

{

 include_once(PATH_TO_MAIN_PHYSICAL.'theme_menu.php');

 return $menu_list;

}

function tep_get_user_list($name='user_type',$default='')

{

 $user_type_array=array();

 $user_type_array[]=array('id'=>'','text'=>'');

 $user_type_array[]=array('id'=>'jobseeker','text'=>'jobseeker');

 $user_type_array[]=array('id'=>'disappear_after_jobseeker_login','text'=>'Disappear After Jobseeker Login');

 $user_type_array[]=array('id'=>'show_after_jobseeker_login','text'=>'Show After Jobseeker Login');

 $user_type_array[]=array('id'=>'recruiter','text'=>'recruiter');

 $user_type_array[]=array('id'=>'disappear_after_recruiter_login','text'=>'Disappear After Recruiter Login');

 $user_type_array[]=array('id'=>'show_after_recruiter_login','text'=>'Show After Recruiter Login');

 return tep_draw_pull_down_menu($name, $user_type_array, $default,'  class="form-control-sm" id="user_type"');

}

function is_show_menu($permission='')

{

   $is_show=true;

  switch ($permission)

  {



	  case 'disappear_after_jobseeker_login':

	    if(check_login("jobseeker") )

        $is_show=false;

	   break;

	  case 'show_after_jobseeker_login':

	    if(!check_login("jobseeker") )

        $is_show=false;

	   break;

	  case 'disappear_after_recruiter_login':

	    if(check_login("recruiter") )

        $is_show=false;

	   break;

	  case 'show_after_recruiter_login':

	    if(!check_login("recruiter") )

        $is_show=false;

	   break;

  }

  return $is_show;

}

// Function to extract and serve the HTML file
function displayZipContents($zipFilePath, $jobId = null) {
  // Generate a unique ID if $jobId is null
  $jobId = $jobId ?? uniqid('job_', true);
  if(check_login('admin')){
    $extractPath = '../temp_extract_' . $jobId . '/';
    }else{
      $extractPath = 'temp_extract_' . $jobId . '/';
    }


  // Clean up previous extracts if they exist
  if (is_dir($extractPath)) {
      array_map('unlink', glob("$extractPath/*.*"));
      rmdir($extractPath);
  }

  $zip = new ZipArchive;
  if ($zip->open($zipFilePath) === TRUE) {
      if (!is_dir($extractPath)) {
          mkdir($extractPath, 0777, true);
      }
      $zip->extractTo($extractPath);
      $zip->close();

      // Find the first HTML file in the extracted contents
      $htmlFiles = glob($extractPath . '*.html');
      if (!empty($htmlFiles)) {
          $htmlFile = $htmlFiles[0];
          $formattedPath = ltrim($extractPath, '/');
          if(check_login('admin')){
          return  '<iframe src="../' . $htmlFile . '" frameborder="0"></iframe> <input type="hidden" name="doc_folder" class="doc_folder" value="' . htmlspecialchars('temp_extract_' . $jobId) . '">';
          }else{
            return  '<iframe src="/' . $htmlFile . '" frameborder="0"></iframe> <input type="hidden" name="doc_folder" class="doc_folder" value="' . htmlspecialchars('temp_extract_' . $jobId) . '">';
          }
      } else {
          return 'No HTML file found in the ZIP.';
      }
  } else {
    return 'Failed to open ZIP file.';
  }
}

function deleteFolder($folderPath) {

  if (is_dir($folderPath)) {
      // Attempt to remove the folder
      if (rmdir($folderPath)) {
          echo 'Folder successfully removed.';
      } else {
          echo 'Failed to remove folder. Ensure the folder is empty.';
      }
  } else {
      echo 'Folder does not exist.';
  }
}

?>