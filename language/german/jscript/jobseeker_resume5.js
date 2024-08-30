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
     validformFile = /(.doc|.Doc|.DOc|.DoC|.dOc|.DOC|.pdf|.Pdf|.PDf|.PdF|.pDf|.PDF|.txt|.Txt|.TXt|.TxT|.tXt|.TXT|.DOCX)$/i;
     if(!validformFile.test(objFrm[i].value))
     {
      alert("Only txt/doc/pdf file is supported. Please try again");
      objFrm[i].focus();
      objFrm[i].select();
      return false;
      break;
     }
    }    
    else if(objFrm[i].name=='my_photo')
    {
     validformFile = /(.gif|.Gif|.GIf|.GiF|.gIf|.GIF|.jpg|.Jpg|.JPg|.JpG|.jPg|.JPG|.png|.Png|.PNg|.PnG|.pNg|.PNG|.jpeg|.JPEG|.Jpeg|.jpeG)$/;
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