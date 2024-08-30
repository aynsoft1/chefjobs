var sInvalidChars
sInvalidChars="1234567890";
var iTotalChecked=0;
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


/*-------------------------------------------------------------------------
        This sub routine checks for the mandatory fields, 
		their data types and maximum length
        also validates valid email entered or not
        Return : True/False
        Input : objFrm ( form object name)
--------------------------------------------------------------------------*/
function ValidateForm(objFrm)
{
	var iConventionPos;
	var sChangedName;
 for( var i =0; i< objFrm.length;i++)
 {
  ///////////// Only for this site ends ////////
  if(objFrm[i].type=='password')
  {
			objFrm[i].value = fnFixSpace(objFrm[i].value);
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
   if(objFrm[i].value!='' && objFrm[i].value.indexOf(" ")!=-1)
   {
    alert("Spaces are not allowed in password.");
    objFrm[i].select();
    return false;
    break;
   }
   if(objFrm[i].value!='' && objFrm[i].value.length<5)
   {
    alert(sChangedName + " must be atleast 5 characters.");
    objFrm[i].select();
    return false;
    break;
   }
   if(objFrm[i].value!='' && objFrm[i].value.length >5 && objFrm[i].value.length >15)
   {
    alert(sChangedName + " cannot be greater than 15 characters.");
    objFrm[i].select();
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