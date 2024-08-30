
function checkall()
{
	//Purpose	: This function is used to check all the checkboxes  
	for (var i=0;i <document.page.elements.length;i++)
	{
		var e = document.page.elements[i];
		if (e.type == "checkbox" && e.disabled!=true)
		{
			e.checked = true;
		}
	}
}
function uncheckall()
{
	//Purpose	: This function is used to check all the checkboxes  
	for (var i=0;i < document.page.elements.length;i++)
	{
		var e = document.page.elements[i];
		if (e.type == "checkbox")
		{
			e.checked = false;
		}
	}
}
function gotopage(pagename,higher,field,order,strings)
{
	pageno=document.page.page.options[document.page.page.selectedIndex].value;
	lower=(pageno-1)*higher;
	window.location=pagename+"?lower="+lower+"&higher="+higher+"&field="+field+"&order="+order+"&"+strings;
}
function goRemove(pagename,idname,deletename,id)
{
	if(confirm('Do you really want to delete this Information ?'))
	{
		window.location=pagename+"?"+idname+"="+id+"&data_delete="+deletename;
	}
	else
		return false;
}
function goRemoveSelected(pagename,idname,deletename)
{
	var id;
	id='';
	for (var i=0;i < page.elements.length;i++)
	{
		var e = page.elements[i];
		if (e.type == "checkbox")
		{
			if((e.checked) == true)
			{
				id=id+','+e.name;
			}
		}
	}
	if(id!='')
	{
		if(confirm('Do you really want to delete these Information ?'))
		{
			id=id.substring(1);
			//alert(id);
			window.location=pagename+"?"+idname+"="+id+"&data_delete="+deletename;
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('Please check atleast one checkbox and then click on delete.');
	}
}
function encode_delete(pagename)
{
  if(confirm('Do you really want to delete this Information ?'))
  {
		 window.location=pagename;
  }
  else
		 return false;
}
function encode_delete1(pagename,deletestring)
{
 if(confirm('Do you really want to delete this Information ?'))
 {
  window.location=pagename+"?query_strings="+deletestring;
 }
 else
  return false;
}
function encode_delete2(pagename,deletestring)
{
	var id;
	id='';
	for (var i=0;i < page.elements.length;i++)
	{
		var e = page.elements[i];
		if (e.type == "checkbox")
		{
			if((e.checked) == true)
			{
				id=id+','+e.name;
			}
		}
	}
	if(id!='')
	{
		if(confirm('Do you really want to delete these Information ?'))
		{
			id=id.substring(1);
			//alert(id);
			window.location=pagename+"?jobids="+id+"&query_strings="+deletestring;;
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('Please check atleast one checkbox and then click on delete.');
	}
}

////////////////////// Page Navigation ////////////
function submit1(lower)
{
	document.page.lower.value=lower;
	document.page.submit();
}
function submit2(higher)
{
	pageno=document.page.page.options[document.page.page.selectedIndex].value;	
	lower=(pageno-1)*higher;
	document.page.lower.value=lower;
	document.page.higher.value=higher;
	document.page.submit();
}
/////////////////////

/////////////////// Check Selected in list_of_jobs.php page //////
function check_selected()
{
	$a=false;
	for($i=0;$i<document.page1.length;$i++)
	{
		if(page1[$i].options[page1[$i].selectedIndex].value!='')
		{
			$a=true;
			break;
		}
	}
	if($a==true)
		return true;
	else
	{
		alert("You have not chosen re_advertise vacancy.");
		return false;
	}
}
////////////
function SetFocus() {
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") &&
           (field.elements[i].type != "hidden") &&
           (field.elements[i].type != "reset") &&
           (field.elements[i].type != "submit") ) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") ||
             (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();

        break;
      }
    }
  }
}
function rowOverEffect(object) {
  if (object.className == 'dataTableRow1') object.className = 'dataTableRowOver1';
  if (object.className == 'dataTableRow2') object.className = 'dataTableRowOver2';
  if (object.className == 'dataTableRow3') object.className = 'dataTableRowOver3';
  if (object.className == 'dataTableRow4') object.className = 'dataTableRowOver4';
  if (object.className == 'dataTableRightRow') object.className = 'dataTableRightRowOver';
  if (object.className == 'dataTableRightRow1') object.className = 'dataTableRightRowOver1';
  if (object.className == 'menuBoxHeading') object.className = 'menuBoxHeadingOver';
}

function rowOutEffect(object) {
  if (object.className == 'dataTableRowOver1') object.className = 'dataTableRow1';
  if (object.className == 'dataTableRowOver2') object.className = 'dataTableRow2';
  if (object.className == 'dataTableRowOver3') object.className = 'dataTableRow3';
  if (object.className == 'dataTableRowOver4') object.className = 'dataTableRow4';
  if (object.className == 'dataTableRightRowOver') object.className = 'dataTableRightRow';
  if (object.className == 'dataTableRightRowOver1') object.className = 'dataTableRightRow1';
  if (object.className == 'menuBoxHeadingOver') object.className = 'menuBoxHeading';
}

/////////////
function submitform(lower,form_name)
{
	eval("document."+form_name+".lower.value=lower");
	eval("document."+form_name+".submit()");
}
function submitform1(higher,form_name,select_name)
{
	pageno=select_name.options[select_name.selectedIndex].value;	
	var lower=(pageno-1)*higher;
	eval("document."+form_name+".lower.value=lower");
	eval("document."+form_name+".submit()");
}

function submit_thispage(sort_val,lower_val)
{
 document.page.sort.value=sort_val;
 document.page.lower.value=lower_val;
 document.page.submit();
}
function submit_thispage1(form_name,lower_name,sort_name,sort_val,lower_val)
{
	eval("document."+form_name+"."+sort_name+".value=sort_val");
	eval("document."+form_name+"."+lower_name+".value=lower_val");
	eval("document."+form_name+".submit()");
}
function submitform2(higher,form_name,select_name,lower_name)
{
	pageno=select_name.options[select_name.selectedIndex].value;	
	lower=(pageno-1)*higher;
	eval("document."+form_name+"."+lower_name+".value=lower");
	eval("document."+form_name+".submit()");
}

function submitform3(lower,form_name,lower_name)
{
	eval("document."+form_name+"."+lower_name+".value=lower");
	eval("document."+form_name+".submit()");
}


function checkGroups(obj) {
  var subgroupID,i;
  subgroupID = eval("this.defineForm.subgroups_"+parseFloat((obj.id).substring(7)));
  if(subgroupID!=undefined){
   if (subgroupID.length > 0) {
     for (i=0; i<subgroupID.length; i++) {
       if (obj.checked == true) { subgroupID[i].checked = true; }
       else { subgroupID[i].checked = false; }
     }
   } else {
     if (obj.checked == true) { subgroupID.checked = true; }
     else { subgroupID.checked = false; }
   }
  }
}

function checkSub(obj) {
  var groupID,subgroupID,i,num=0;
  groupID = eval("this.defineForm.groups_"+parseFloat((obj.id).substring(10)));
  subgroupID = eval("this.defineForm."+(obj.id));
      
  if (subgroupID.length > 0) {    
    for (i=0; i < subgroupID.length; i++) {
      if (subgroupID[i].checked == true) num++;
    }
  } else {
    if (subgroupID.checked == true) num++;
  }
  if (num>0) { groupID.checked = true; }
  else { groupID.checked = false; }
}

////////////
////////////
////////////
/////Pop up Image Code
//////////////////////////////////////////////////
PositionX = 100;
PositionY = 100;

// Set these value approximately 20 pixels greater than the
// size of the largest image to be used (needed for Netscape)

defaultWidth  = 500;
defaultHeight = 500;

// Set autoclose true to have the window close automatically
// Set autoclose false to allow multiple popup windows

var AutoClose = true;

// Do not edit below this line...
// ================================
if (parseInt(navigator.appVersion.charAt(0))>=4){
var isNN=(navigator.appName=="Netscape")?1:0;
var isIE=(navigator.appName.indexOf("Microsoft")!=-1)?1:0;}
var optNN='scrollbars=no,width='+defaultWidth+',height='+defaultHeight+',left='+PositionX+',top='+PositionY;
var optIE='scrollbars=no,width=100,height=100,left='+PositionX+',top='+PositionY;
function popupimage(imageURL,imageTitle){
if (isNN){imgWin=window.open('about:blank','',optNN);}
if (isIE){imgWin=window.open('about:blank','',optIE);}
with (imgWin.document){
writeln('<html><head><title>Loading...</title><style>body{margin:0px;}</style>');writeln('<sc'+'ript>');
writeln('var isNN,isIE;');writeln('if (parseInt(navigator.appVersion.charAt(0))>=4){');
writeln('isNN=(navigator.appName=="Netscape")?1:0;');writeln('isIE=(navigator.appName.indexOf("Microsoft")!=-1)?1:0;}');
writeln('function reSizeToImage(){');writeln('if (isIE){');writeln('window.resizeTo(100,100);');
writeln('width=113-(document.body.clientWidth-document.images[0].width);');
writeln('height=100-(document.body.clientHeight-document.images[0].height);');
writeln('window.resizeTo(width,height);}');writeln('if (isNN){');       
writeln('window.innerWidth=document.images["photo"].width;');writeln('window.innerHeight=document.images["photo"].height;}}');
writeln('function doTitle(){document.title="'+imageTitle+'";}');writeln('</sc'+'ript>');
if (!AutoClose) 
	writeln('</head><body bgcolor=000000 scroll="no" onload="reSizeToImage();doTitle();self.focus()">')
else 
	writeln('</head><body bgcolor=000000 scroll="no" onload="reSizeToImage();doTitle();self.focus()" onblur="self.close()">');
writeln('<img name="photo" src='+imageURL+' alt="'+imageTitle+'" style="display:block"></body></html>');
close();		
}}