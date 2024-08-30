 function rowclickEffect(object)
 { 
  if (object.className == 'inactive_menu_tab' || object.className == 'inactive_menu_over' )
  {
   var tab_menu=document.getElementById("tab_menu");
   var menu_ele = tab_menu.getElementsByTagName("*");
   var n = menu_ele.length;
   for (var i = 0; i < n; i++) 
   {
    var elm = menu_ele[i];
    if(elm.id.match(/\bpage_\d\b/i))
    {
     var name = elm.id;
     name=name.replace('_',''); 
     if(object.id ==elm.id)
     {
      object.className = 'active_menu_tab';
      var obj1=document.getElementById(name+'_content');
      if(obj1)
      obj1.style.display = 'block';  

     }
     else
     {
      elm.className = 'inactive_menu_tab';
      var obj1=document.getElementById(name+'_content');
      if(obj1)
      obj1.style.display = 'none';  
     }
    }
   }   
  }
 }
 function defaultTab()  
 {
  var tab_menu=document.getElementById("tab_menu");
  var menu_ele = tab_menu.getElementsByTagName("*");
  var n = menu_ele.length;
  for (var i = 0; i < n; i++) 
  {
   var elm = menu_ele[i];
   if(elm.id.match(/\bpage_\d\b/i))
   {
    var name = elm.id;
    name=name.replace('_',''); 
    if(elm.className=='active_menu_tab'  ||  elm.className=='active_menu_over')
    {
     var obj1=document.getElementById(name+'_content');
     if(obj1)
     obj1.style.display = 'block'; 
    }
    else
    {
     var obj1=document.getElementById(name+'_content');
     if(obj1)
     obj1.style.display = 'none';  
    }
   }
  }   
 }