var xmlHttp

function pagination(page,keyword,location1,word1,country,state,job_category,experience,job_post_day,search_zip_code,zip_code,radius,host)
{
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  }
var url=host+"ajax/job_search_result.php";
url = url+"?starting="+page;
url = url+"&keyword="+keyword;
url = url+"&location="+location1;
url = url+"&word1="+word1;
url = url+"&country="+country;
url = url+"&state="+state;
if(job_category!=null && job_category!='')
{
url = url+"&job_category[]="+job_category;
}
url = url+"&experience="+experience;
url = url+"&job_post_day="+job_post_day;
url = url+"&search_zip_code="+search_zip_code;
url = url+"&zip_code="+zip_code;
url = url+"&radius="+radius;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=stateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
} 

function stateChanged() 
{
 if(xmlHttp.readyState==1)
 {
  document.getElementById("page_contents").innerHTML = '<div class="spinner">&nbsp;</div>';
 }
 if (xmlHttp.readyState==4)
 { 
  document.getElementById("page_contents").innerHTML=xmlHttp.responseText;
 }
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}