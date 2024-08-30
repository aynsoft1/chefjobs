$(document).ready(function(){
 
	setJobdetaillink();
 if (typeof(FB) != 'undefined' && FB != null )
 FB.Canvas.setSize({width: 810, height:$("body").height()+10});
	$(document).ajaxComplete(function(){
    try{
								setJobdetaillink();
								setBacklink();
        FB.XFBML.parse(); 
        FB.Canvas.setSize({width: 810, height:$("body").height()+10});
    }catch(ex){}
});

$("form").submit(function()
	{
  $('#jobDetailResult').hide();
  $('#searchResult').show();

	 var obj =$("form");
  var url= this.action;
  var data = obj.serialize();
$.ajax({
type: "POST",
url: url,
data: data,
	beforeSend:function(){$('#searchResult').html('<div class="loader"></div>');},
success: function(response) {
                       $('#searchResult').html(response);
                }
});
return false;
});/*submit	*/


});
function showJobdetail(jobID)
	{
 var obj=	$('#jobDetailResult');
 	var jID = obj.attr('jId');
		if(jID==jobID)
		{
   obj.show();
   $('#searchResult').hide();
		} 
		else
		{

			obj.attr('jId',jobID);
   obj.html('<div class="loader"></div>');	
  $('#searchResult').hide();
	 	$.get('job_details.php', {'job_id':jobID}, function(data){ 
  obj.html(data); 		
  obj.show();


  });
	 }
	}
	function backSearch()
	{		
  $('#jobDetailResult').hide();
  $('#searchResult').show();
 }
	function setBacklink()
	{
		$( "#backSearch" ).click(function() 
  {
   backSearch();
  });
	}

function setJobdetaillink()
{
	$( ".job_search_title" ).click(function() 
 {
  var jobId= $(this).attr('id');
	 jobId =jobId.slice(2);
		showJobdetail(jobId);
 });
 

}
function defaultJobResult()
{

	var obj =$('#searchResult');
  obj.html('<div class="loader"></div>');	
 	$.get('job_search.php', {'format':'html'}, function(data){ 
  obj.html(data); 
  });
}
function pagination(page,keyword,location1)
{
	$.ajax({
type: "POST",
url: 'job_search.php',
data: { keyword: keyword,location:location1,page:page },
success: function(response){
                       $('#searchResult').html(response);
                }
});
}
