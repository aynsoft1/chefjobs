function validate_change_password(objFrm)
{
 for( var i =0; i< objFrm.length;i++)
 {
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
 if(objFrm.TR_new_password.value!="" && objFrm.TR_confirm_password.value!="" && ( objFrm.TR_confirm_password.value!=objFrm.TR_new_password.value))
 {
  alert("Password and confirm password does not match.");
  objFrm.TR_confirm_password.select();
  return false;
 }
}