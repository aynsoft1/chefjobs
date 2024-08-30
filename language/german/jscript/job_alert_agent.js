function fnRemoveSpaces(sFldval)
{
	var sTemp=sFldval;
	var sNewval=sTemp;
	//remove spaces from the front
	for(var i=0;i<sTemp.length;i++)
	{
		if(sTemp.charAt(i)!=" ")
			break;
		else
			sNewval = sTemp.substring(i+1);
	}
	return sNewval;
}
function fnFixSpace(sFldval)
{
	var sTemp=sFldval;
	var sReversedString="";
	var sTemp1;

	//remove spaces from the front
	sNewval = fnRemoveSpaces(sTemp);

	// reverse n remove spaces from the front
	for(var i=sNewval.length-1;i>=0;i--)
		sReversedString = sReversedString + sNewval.charAt(i);
	sTemp1 = fnRemoveSpaces(sReversedString);
	//reverse again
	sReversedString="";
	for(var i=sTemp1.length-1;i>=0;i--)
		sReversedString = sReversedString + sTemp1.charAt(i);
	sNewval = sReversedString;
	return sNewval;
}
function ckeck_application(user)
{
 var count;
 var user_type=user;
 var arrnew = new Array();
 count=0;
 new_string='';
 for( var i =0; i< document.page.length;i++)
 {
  if(document.page[i].type=='checkbox')
  {
   if(document.page[i].checked==true)
   {
    arrnew[count]=document.page[i].value;
    count++;
   }   
  }
 }
 if(count>0)
 {
  if(count>5)
  {
   alert('You cannot apply  to more than 5 jobs per page');
   return false;
  }
  else
  {
   if(user_type=='new')
    window.open('jobseeker_registration_step1.php?job_apply='+arrnew.join(','),'new');
   else 
   window.open('bulk_apply_now.php?query_string='+arrnew.join(','),'new');
   return true;
  }
 }
 else
 {
  alert('Please select at least one job');
  return false;
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
			if(objFrm[i].type=='text' || objFrm[i].type=='textarea' || 
				objFrm[i].type=='password')
			{
				objFrm[i].value = fnFixSpace(objFrm[i].value);
			}
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