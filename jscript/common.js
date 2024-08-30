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
		else if ((iobjLength-(iTmp+1)<2)||(iobjLength-(iTmp+1)>3)  )
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
		///////////// Only for this site starts ////////
		var countIndustry,kk,abc;
		countIndustry=0;
		kk=0;
		abc="no";
  if(objFrm[i].type=='checkbox')
  {
   while(objFrm[i].name.substring(0,17)=="post_job_category")
   {
    if(objFrm[i].checked==true)
     countIndustry++;
    i++;
    abc="yes";
   }
  }
		if(abc=="yes")
		{
			if(countIndustry==0)
			{
				alert("Please Select job category interested.");
				return false;
				break;
			}
			/*else if(countIndustry>5)
			{
				alert("Sorry : You have allowed to select upto 5 positions interested.");
				return false;
				break;
			}*/
		}
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
    if((objFrm[i].name=='TR_end_month' || objFrm[i].name=='TR_end_year' )&& (objFrm.name=='work_history'  && (document.work_history.current.checked ==true)))
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
   if(objFrm[i].type=='file' && objFrm[i].value!="")
   {
    for(var fi=0; fi < objFrm[i].value.length;fi++)
				{
					if(objFrm[i].value.charAt(fi)=="'")
					{
      alert("(') character is not allowed. Please rename file and try again.");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
    }
    if(objFrm[i].name=='my_resume')
    {
     validformFile = /(.doc|.Doc|.DOc|.DoC|.dOc|.DOC|.pdf|.Pdf|.PDf|.PdF|.pDf|.PDF|.txt|.Txt|.TXt|.TxT|.tXt|.TXT|.docx|.dOcx|.doCx|.docX|.Docx|.DOcx|.DoCx|DocX|.dOCx|.dOcX|.doCX|.DOCx|.DoCX|.dOCX|.DOcX|.DOCX)$/;
     if(!validformFile.test(objFrm[i].value))
     {
      alert("Only txt/doc/pdf/docx file is supported. Please try again");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
    }
    else if(objFrm[i].name=='my_resume1')
    {
     validformFile = /(.doc|.Doc|.DOc|.DoC|.dOc|.DOC|.pdf|.Pdf|.PDf|.PdF|.pDf|.PDF|.txt|.Txt|.TXt|.TxT|.tXt|.TXT|.docx|.dOcx|.doCx|.docX|.Docx|.DOcx|.DoCx|DocX|.dOCx|.dOcX|.doCX|.DOCx|.DoCX|.dOCX|.DOcX|.DOCX)$/;
     if(!validformFile.test(objFrm[i].value))
     {
      alert("Only txt/doc/pdf/docx file is supported. Please try again");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
    }
    else if(objFrm[i].name=='my_photo')
    {
     validformFile = /(.gif|.Gif|.GIf|.GiF|.gIf|.GIF|.jpg|.Jpg|.JPg|.JpG|.jPg|.JPG|.png|.Png|.PNg|.PnG|.pNg|.PNG)$/;
     if(!validformFile.test(objFrm[i].value))
     {
      alert("Only gif/jpg/png file is supported. Please try again");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
    }
    else if(objFrm[i].name=='TR_portfolio')
    {
     validformFile = /(.gif|.Gif|.GIf|.GiF|.gIf|.GIF|.jpg|.Jpg|.JPg|.JpG|.jPg|.JPG|.png|.Png|.PNg|.PnG|.pNg|.PNG)$/;
     if(!validformFile.test(objFrm[i].value))
     {
      alert("Only gif/jpg/png file is supported. Please try again");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
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
   
			if((objDataTypeHolder=="TR_" || objDataTypeHolder=="IR_" || 
				objDataTypeHolder=="MR_"  )&& (objFrm[i].value=='') && ! ((objFrm[i].name=='TR_end_month' || objFrm[i].name=='TR_end_year') && (objFrm.name=='work_history'  && document.work_history.current.checked==true)))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
  
				alert("Please enter "+ sChangedName +".");
    if(objFrm[i].type=='textarea' && ( objFrm[i].name=='TR_description' || 
       objFrm[i].name=='TR_content' || objFrm[i].name=='TR_message'))
    {
				objFrm[i].focus();
				objFrm[i].select();
    }   
    else
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
			if((objDataTypeHolder=="IR_" || objDataTypeHolder=="MR_" )&& 
				(isNaN(objFrm[i].value)))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter numeric "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}
			if((objDataTypeHolder=="IR_" || objDataTypeHolder=="MR_" )&& 
				(objFrm[i].value < 0))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter valid "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}

			if((objDataTypeHolder=="IN_" || objDataTypeHolder=="MN_" )&& 
				(isNaN(objFrm[i].value) && objFrm[i].value!='' ))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter numeric "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}
			if((objDataTypeHolder=="IN_" || objDataTypeHolder=="MN_" )&& 
				(objFrm[i].value<0 && objFrm[i].value!=''))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter valid "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}
			if((objDataTypeHolder=="IR_" || objDataTypeHolder=="IN_" ) && 
				(objFrm[i].value.indexOf(".")!=-1))
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter valid "+ sChangedName +".");
				objFrm[i].focus();
				objFrm[i].select();
				return false;
				break;
			}
			if((objDataTypeHolder=="IN_" ) && ((objFrm[i].value.indexOf("e")!=-1) ||(objFrm[i].value.indexOf("E")!=-1)) )
			{
				sChangedName = objFrm[i].name.substring(3);
				sChangedName = getFormattedmsg(sChangedName)
				alert("Please enter Numeric "+ sChangedName +".");
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
			//ValidateNumber(objName)
			if((objDataTypeHolder=="NR_"))
			{
				if(!ValidateNumber(objFrm[i].value))
				{
					objFrm[i].focus();
					return false;
					break;
				}
			}
			if(objDataTypeHolder=="PHR")
			{
				var val=objFrm[i].value;
				if (val!="")
				{
					for(var j=0; j < val.length;j++)
					{
						if((val.charAt(j)!=' ')&& !((val.charAt(j)>=0)&&(val.charAt(j)<=9)))
						{
							sChangedName = objFrm[i].name.substring(4);
							sChangedName = getFormattedmsg(sChangedName)
							alert("Please enter valid "+ sChangedName +".");
							objFrm[i].focus();
							objFrm[i].select();
							return false;
							break;
						}
					}
				}
				else
				{
					sChangedName = objFrm[i].name.substring(4);
					sChangedName = getFormattedmsg(sChangedName)
					alert("Please enter "+ sChangedName +".");
					objFrm[i].focus();
					objFrm[i].select();
					return false;
					break;
				}
			}
			//ValidateNumber(objName)
			if((objDataTypeHolder=="NR_"))
			{
				if(!ValidateNumber(objFrm[i].value))
				{
					objFrm[i].focus();
					return false;
					break;
				}
				if(parseFloat(objFrm[i].value)<=0)
				{
					objFrm[i].focus();
					alert('Price should be greater then 0');
					return false;
				}
			}
			if(objDataTypeHolder=="PHN")
			{
				var val=objFrm[i].value;
				if (val!="")
				{
					for(var j=0; j < val.length;j++)
					{
						if((val.charAt(j)!=' ')&& !((val.charAt(j)>=0)&&(val.charAt(j)<=9)))
						{
							sChangedName = objFrm[i].name.substring(4);
							sChangedName = getFormattedmsg(sChangedName)
							alert("Please enter valid "+ sChangedName +".");
							objFrm[i].focus();
							objFrm[i].select();
							return false;
							break;
						}
					}
				}
			}
		}
	}
	return true;
}

function FormatDate(d)
{
	var dd,mm;
	var l;
	l=d.indexOf("/");
	dd=d.substring(0,l);
	d=d.substring(l+1);
	l=d.indexOf("/");
	mm=d.substring(0,l);
	yy=d.substring(l+1);

	if (parseInt(dd) < 10)
			dd="0" + dd;
	if (parseInt(mm) < 10)
			mm="0" + mm;
	d= dd + "/" + mm + "/" + yy
	return d;
}

function ValidateNumber(objName)
{

	var h;
	var x;

	h=objName.length;
	x = objName;
	if (h==0)
	{
		alert("Price Can be numeric only");
		return false;
	}
	for( i=0;i<h;i++)
	{
		z = x.substring(i,i+1);
		if ( z=="'"||z=='"' || (z >= "a" && z <= "z" ) || (z >= "A" && z <= "Z") )
		{
			alert("Price Can be numeric only");
			return false;
		}
	}
	jj=x.indexOf(".");
	if (jj != "-1")
	{
		hh=x.substring(jj);
		ll=hh.length;
		if (ll > 3)
		{
			alert("Price Can have upto 2 decimal places");
			return false;
		}
	}
	x = objName;
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
function validatedate(val)
{
	h=val.length;
	if(h<10)
	{
		return false;
	}
	else
	{
		return true;
	}
}