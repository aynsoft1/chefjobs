<?php
include_once("include_files.php");
$whereClause="";
if(isset($_SESSION['sess_recruiteruserid']))
 $whereClause.="  j.recruiter_user_id='".tep_db_input($_SESSION['sess_recruiteruserid'])."'";
else
 $whereClause.="  j.recruiter_id='".tep_db_input($_SESSION['sess_recruiterid'])."'";
 $no_of_day=30;
 $cur_date= @date("Y-m-d", mktime(0, 0, 0, date('m'),date('d'), date('Y')));
 $past_date= @date("Y-m-d", mktime(0, 0, 0, date('m'),date('d')-$no_of_day, date('Y')));
 $chart_array=array();
 
  
$date_array=array();
for($i=$no_of_day;$i>=0;$i--)
{
 $date_array[] =  date("Y-m-d", mktime(0, 0, 0, date('m'),date('d')-$i, date('Y')));
}
 
 		 $job_data_array=array();

 $sql ="SELECT  job_id,job_title  FROM ".JOB_TABLE."  as j where    ".$whereClause ."  and  j.deleted is NULL order by re_adv desc limit 0,10 ";
 $temp_result=tep_db_query($sql);
   	 

	if(tep_db_num_rows($temp_result) > 0)
	{
  	  while($row = tep_db_fetch_array($temp_result))
	  {
		 $job_id    = $row['job_id'];
		 $job_title = tep_db_output($row['job_title']);
		 $job_data_array[$job_title]=array();
		 ///////////////////////////////////////////
		  $sql_s ="SELECT  *  FROM ".JOB_STATISTICS_DAY_TABLE."  where     job_id ='".tep_db_input($job_id)."'  and  date between  '".tep_db_input($past_date)."' and '".tep_db_input($cur_date)."'   ";
         $temp_result_s=tep_db_query($sql_s);
	     if(tep_db_num_rows($temp_result_s) > 0)
	     {
  	      while($row_s = tep_db_fetch_array($temp_result_s))
		  {
		   $job_id       = $row_s['job_id'];
		   $date         = $row_s['date'];
		   $viewed       = $row_s['viewed'];
		   $clicked      = $row_s['clicked'];
		   $applications = $row_s['applications'];
		   $job_data_array[$job_title][$date]=array('viewed'=>$viewed);
  		  }
		 }
		 tep_db_free_result($temp_result_s);
		 ///////////////////////////////////////////
      }
	}
    
	  // 
 tep_db_free_result($temp_result);
 //print_r($job_data_array);
 if(tep_not_null($job_data_array))
 {
	$chart_data='["date","'.implode('","',array_keys($job_data_array)).'" ],'."\n";

	   foreach($date_array as $key=> $val)
	   {
	    $chart_data.='["'. $val .'",';
        foreach($job_data_array as $jkey=> $jval)
	    {
			 //print_r($job_data_array[$jkey][$val]['viewed']);die();
          if (isset( $job_data_array[$jkey][$val]['viewed']))
			{
			  $viewed= ($job_data_array[$jkey][$val]['viewed']);
           $chart_data.=$viewed. ' ,'; 
			}
         else
		  $chart_data.= '0 ,'; 
		}
		 $chart_data=substr($chart_data,0,-2);
		 $chart_data.='],'."\n";

	   }
	   	$chart_data=substr($chart_data,0,-2);

  

    
	//echo $chart_data;
	?>
<!doctype html>
<html lang="en">
 <head>
 <title>Chart</title>
 </head>
 <body>
 <div id="chart_div1" style="background-color:#fff!important;"></div>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", { packages: ["corechart"] });
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      <? echo $chart_data; ?> 
    ]);
     
    var options = {
      title: 'Report Latested Job View  ',
 	  height: 500,
      pointSize: 2,
      backgroundColor: 'f9f9f9'
    };   
   var chart = new google.visualization.AreaChart(document.getElementById('chart_div1'));
    chart.draw(data, options);
   }
</script>
</body>
</html>

	<?
    
 }
 
?>