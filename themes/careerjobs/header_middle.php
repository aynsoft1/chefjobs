<?

//////////////////// SLIDER CODING ENDS /////////////////////////////

if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_INDEX)
{
/*-----------------------SEARCH CODE---------------------------------------------------------*/
$job_search_form=tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search');
$key=tep_draw_input_field('keyword','','class="form-control form-control-lg form-home"  placeholder="'.INFO_KEYWORD.'"',false);
$selectId = 'country-select'; // ID for the select element
$locat = '<label for="country-select" class="sr-only">Select your country:</label>' . LIST_TABLE(
    COUNTRIES_TABLE,
    TEXT_LANGUAGE . "country_name",
    "priority",
    "id='$selectId' name='country' class='form-select form-select-lg form-home'",
    ALL_LOCATION,
    "",
    DEFAULT_COUNTRY_ID
);
$experience_1=experience_drop_down('name="experience" class="form-control form-control-lg form-home"', INFO_EXPERIENCE, '', $experience);
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>"Alle Kategorien"));
$selectId = 'industry-sector-select'; // ID for the select element
$industry_sector ='<label for="industry-sector-select" class="sr-only">Select your industry sector:</label>' . tep_draw_pull_down_menu(
    'job_category[]', 
    $cat_array, 
    '', 
    'id="' . $selectId . '" class="form-select form-select-lg form-home"'
);


$button= '<button type="submit" class="btn btn-lg btn-warning btn-find m-w-100 px-4">'.SEARCH_BUTTON.' </button>';
/********************************  SEARCH CODE ENDS********************************************* */

//////////////////// SLIDER CODING STARTS ///////////////////
$now=date("Y-m-d H:i:s");
$queryslider = "select s.id,s.slider_title,s.slider_description,s.slider_image,s.slider_link from ".SLIDER_TABLE." as s  where s.inserted <='$now' order by rand() limit 0,3";
//echo "<br>$queryslider";//exit;
$result_slider1=tep_db_query($queryslider);
$x=tep_db_num_rows($result_slider1);
$count=1;
$slider1='';
while($sliders = tep_db_fetch_array($result_slider1))
{
 $sliderid=$sliders['id'];
//$slider_link=$sliders['slider_link'];
$class=($count=='1'?'carousel-item active':'carousel-item');
  $slider_image='';

$slider1.='<div class="'.$class.'">
	<img src="'.PATH_TO_SLIDER_IMAGE.$sliders["slider_image"].'" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
      <h5>'.$sliders['slider_title'].'</h5>
      </div>
</div>
';
$count++;
}

 define('HEADER_MIDDLE_HTML','

<div class="container">
<div class="">
<div class="col-md-12 slider-bg-theme1 mb-5">
<div class="row">
<div class="col-md-8 mx-auto text-center">
<h1 class="text-white home-heading">'.FIND_JOB_THAT_FITS.'</h1>
<div class="text-white mb-5" style="font-size: 1.25rem;">'.CONNECTING_THE_BEST_JOBS.'</div>
<div class="car">
	<div class="card-bod">
  '.$job_search_form.'
<div class="row g-3">

  <div class="col flex-auto">
    '.$key.'
  </div>
  <div class="col flex-auto">
    '.$locat.'
  </div>
  <div class="col flex-auto">
    '.$industry_sector.'
  </div>
  <div class="col-auto flex-auto">
  '.$button.'
  </div>
  </div>

</div>
<div class="mt-3"><a href="'.getPermalink(FILENAME_JOB_SEARCH).'" class="text-white advance-link" target="_blank">'.ADVANCED_SEARCH.'</a></div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- End Sarch Block -->

 ');
}
else
{
 define('HEADER_MIDDLE_HTML','');
}
?>