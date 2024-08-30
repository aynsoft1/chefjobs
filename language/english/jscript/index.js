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

function ValidateEMail(objName)
{
	var sobjValue;
	var iobjLength;
	sobjValue=objName;
	iobjLength=sobjValue.length;
	iFposition=sobjValue.indexOf("@");
	iSposition=sobjValue.indexOf(".");
	iTmp=sobjValue.lastIndexOf(".");
	iPosition=sobjValue.indexOf(",");
	iPos=sobjValue.indexOf(";");

	if (iobjLength!=0)
	{
		if ((iFposition == -1)||(iSposition == -1))
		{
			return false;
		}
		else if(sobjValue.charAt(0) == "@" || sobjValue.charAt(0)==".")
		{
			return false;
		}
		else if(sobjValue.charAt(iobjLength) == "@" ||
				sobjValue.charAt(iobjLength)==".")
		{
			return false;
		}
		else if((sobjValue.indexOf("@",(iFposition+1)))!=-1)
		{
			return false;
		}
		else if ((iobjLength-(iTmp+1)<2)||(iobjLength-(iTmp+1)>3))
		{
			return false;
		}
		else if ((iPosition!=-1) || (iPos!=-1))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
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
			if(objFrm[i].name.substring(0,5)=='TREF_' || 
				objFrm[i].name.substring(0,5)=='TNEF_')
			{
				objDataTypeHolder = objFrm[i].name.substring(0,5);
			}
			if(objFrm[i].type=='select-one' && objDataTypeHolder=="TR_")
   {
    var test;
    test='ok';
    if((objFrm[i].name=='TR_end_month' || objFrm[i].name=='TR_end_year' ))
    {
     test='aa';
    }
    else if(objFrm[i].options[objFrm[i].selectedIndex].value=='' && test!='aa')
    {
     sChangedName = objFrm[i].name.substring(3);
     sChangedName = getFormattedmsg(sChangedName)
     alert("Please select "+ sChangedName +".");
     objFrm[i].focus();
     return false;
     break;
    }
   }
			if(objFrm[i].type=='select-multiple' &&	objDataTypeHolder=="TR_")
   {
    if(objFrm[i].selectedIndex==-1)
    {
     sChangedName = objFrm[i].name.substring(3);
     lengg=sChangedName.length;
     sChangedName = sChangedName.substring(0,(lengg-2));
     sChangedName = getFormattedmsg(sChangedName)
     alert("Please select "+ sChangedName +".");
     objFrm[i].focus();
     return false;
     break;
    }
   }


   if(objFrm[i].type=='password' && objFrm[i].value!='' && 
				objFrm[i].value.indexOf(" ")!=-1)
			{
				alert("Spaces are not allowed in password.");
				objFrm[i].select();
				return false;
				break;
			}
			if(objFrm[i].type=='password' && objFrm[i].value!='' && 
				objFrm[i].value.length<5)
			{
				alert("Password must be atleast 5 characters.");
				objFrm[i].select();
				return false;
				break;
			}
			if(objFrm[i].type=='password' && objFrm[i].value!='' &&        
				objFrm[i].value.length >5 && objFrm[i].value.length >15)
			{
				alert("Password cannot be greater than 15 characters.");
				objFrm[i].select();
				return false;
				break;
			}
			if(objFrm[i].type=='password' && objFrm.length > (i+2) && 
				objFrm[i+1].type=='password' && 
				objFrm[i].value!='' && 
				objFrm[i+1].value!='' && 
				objFrm[i].value!=objFrm[i+1].value)
			{
				alert("Password & Confirm password does not match");
				objFrm[i+1].select();
				return false;
				break;
			}
   
			if((objDataTypeHolder=="TR_")&& (objFrm[i].value=='') && ! ((objFrm[i].name=='TR_end_month' || objFrm[i].name=='TR_end_year')))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
  
				alert("Please enter "+ sChangedName +".");
        {
				 objFrm[i].focus();
 				objFrm[i].select();
        }
				return false;
				break;
			}
	
			if(objDataTypeHolder=="TREF_" && objFrm[i].value=='')
			{
				sChangedName = objFrm[i].name.substring(5);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter "+ sChangedName +".");
				//alert("Please enter email.");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}
				if((objDataTypeHolder=="TREF_") || (objDataTypeHolder=="TNEF_" && 
				objFrm[i].value!='' ))
			{
				if(!ValidateEMail(objFrm[i].value))
				{
				sChangedName = objFrm[i].name.substring(5);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter valid "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
				}
			}
			if(objFrm[i].name=="TREF_confirm_email_address" &&  
				objFrm[i].value!='' && objFrm[i-1].value!=objFrm[i].value)
			{
				alert("Email address and Cofirm email address does not match.");
				objFrm[i].focus();
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