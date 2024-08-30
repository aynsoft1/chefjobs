function jobsearch_pagination(page,keyword,location1,word1,country,state,job_category,experience,job_post_day,search_zip_code,zip_code,radius,host,map_view,skill)
{
var url=host+"ajax/job_search_result1.php";
url = url+"?starting="+page;
if(job_category!=null && job_category!='')
{
url = url+"&job_category[]="+job_category;
}
jQuery('#page_contents').html('<div class="spinner">&nbsp;</div>'); 
jQuery.ajax({
        url: url,
       data: {
            starting: page,
            keyword: keyword,
            location: location1,
			word1: word1,
			country: country,
			state: state,
			experience: experience,
			job_post_day: job_post_day,
			search_zip_code: search_zip_code,
			zip_code: zip_code,
			radius: radius,
			skill: skill,
 			map_view: map_view,
            },
        type: 'get',
         success: function(data){
           jQuery('#page_contents').html(data); 
		   if(map_view>=1)
				{
//jQuery("#page_contents").find("script").each(function(i) {               eval(jQuery(this).text());});
			initialize();
				}
        }
    });
} 