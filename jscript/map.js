var map;
var Markers=Array();
var infoWindow;
var drawCircle=null;
function setMarkers(lat, lng,title,content_id)
{
 Markers.push([lat,lng,title,content_id]);
}
function addMarkers()
{
  markerLen=Markers.length;
  for (var i = 0; i <markerLen; i++)
  {
   var latLng = new google.maps.LatLng(Markers[i][0],Markers[i][1]);
   createMarker(map, latLng,Markers[i][2],Markers[i][3]);
  }
}
 function createMarker(map, position, marker_title,content_id)
  {
     var marker = new google.maps.Marker({position: position,title: ''+marker_title+'',map: map});
     
     google.maps.event.addListener(marker, 'click', function() 
      {
      var myHtml ;
      if(obj_con=document.getElementById(content_id))
       myHtml = obj_con.innerHTML;
      else
        myHtml = '';
       infoWindow.setContent(myHtml);
       infoWindow.open(map, marker);
     });

     google.maps.event.addListener(marker, 'click', function() 
      {
       if(drawCircle)
       {
        drawCircle.setMap(null);
       }
       drawCircle =new google.maps.Circle(
       {
        center:position,
        map: map,
        fillOpacity:0.1,
        fillColor:'#FF0000',
        strokeColor:'#FF9933',
        radius:1000
        }
       );
     
     });
  }