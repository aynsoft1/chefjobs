function SaveSelected(action)
{
 var check=false;
 for (var r=0;r<document.page.elements.length;r++)
 { 
  var e = page.elements[r];
  if((e.checked) == true)
  {
   check=true;
   break;
  }
 }
 if(check)
 {
  document.page.action1.value=action;
  document.page.submit();
 }
 else
 {
 	alert('Please check atleast one checkbox and then click on add Selected to Save.');
 }
}
function SaveSelected1(action)
{
 var check=false;
 for (var s=0;s<document.page1.elements.length;s++)
 { 
  var e = page1.elements[s];
  if((e.checked) == true)
  {
   check=true;
   break;
  }
 }
 if(check)
 {
  document.page1.action1.value=action;
  document.page1.submit();
 }
 else
 {
 	alert('Please check atleast one checkbox and then click on add Selected to Save.');
 }
}

function checkall1()
{
	//Purpose	: This function is used to check all the checkboxes  
	for (var i=0;i <document.page1.elements.length;i++)
	{
		var e = document.page1.elements[i];
		if (e.type == "checkbox" && e.disabled!=true)
		{
			e.checked = true;
		}
	}
}
function uncheckall1()
{
	//Purpose	: This function is used to check all the checkboxes  
	for (var i=0;i < document.page1.elements.length;i++)
	{
		var e = document.page1.elements[i];
		if (e.type == "checkbox")
		{
			e.checked = false;
		}
	}
}
function ValidateForm(objFrm)
{
	var iConventionPos;
	var sChangedName;
 for( var i =0; i< objFrm.length;i++)
 {
 ///////////// Only for this site ends ////////
		if(objFrm[i].type=='text' || objFrm[i].type=='textarea' || 
			objFrm[i].type=='select-one' || objFrm[i].type=='select-multiple' || 
			objFrm[i].type=='password' || objFrm[i].type=='file' || 
			objFrm[i].type=='radio')
		{
		
			var objDataTypeHolder = objFrm[i].name.substring(0,3);
			if(objDataTypeHolder=="TR_" && objFrm[i].value=='')
             {
			  sChangedName = objFrm[i].name.substring(3);
              sChangedName = getFormattedmsg(sChangedName)
	          alert("Please enter "+ sChangedName +".");
	          objFrm[i].focus();
              return false;
              break;
   }
		}
	}
	return true;
}
function getFormattedmsg(sVal)
{
	while(sVal.indexOf("_")!=-1)
	{
		sVal = sVal.replace("_", " ")
	}
	return sVal;
}