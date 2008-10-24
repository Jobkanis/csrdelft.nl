<?php
/*
 * ledenkaart.php	| 	Jan Pieter Waagmeester (jieter@jpwaag.com)
 * 
 * 
 * googlemaps-probeerseltje.
 * 
 */
include('include.config.php');

if(!$lid->hasPermission('P_LEDEN_READ')){ header('location: '.CSR_ROOT); }
if(isset($_GET['xml'])){
	$sLedenQuery="
		SELECT 
			uid, voornaam, achternaam, tussenvoegsel, adres, postcode, woonplaats
		FROM
			lid
		WHERE
			status='S_LID' OR status='S_GASTLID' OR status='S_NOVIET' OR status='S_KRINGEL'
		ORDER BY achternaam, voornaam;";
	
	$rLeden=$db->query($sLedenQuery);
	header('content-type: text/xml');
	echo '<?xml version="1.0" encoding="utf-8"?><markers>'."\n";
	while($aLid=$db->next($rLeden)){
		if($aLid['adres']!=''){	
			echo '<marker address="'.$aLid['adres'].', '.$aLid['woonplaats'].'" html="'.$lid->getNaamLink($aLid['uid'], 'civitas', false, false, false).'"><![CDATA[ ';
			echo $lid->getNaamLink($aLid['uid'], 'civitas', false).'';
			echo ']]></marker>'."\n";
		}
	}
	echo '</markers>';
exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API Example</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAATQu5ACWkfGjbh95oIqCLYxRY812Ew6qILNIUSbDumxwZYKk2hBShiPLD96Ep_T-MwdtX--5T5PYf1A"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		var geocoder = new GClientGeocoder();
		var randomnumber=Math.floor(Math.random()*11111)
    	GDownloadUrl("/tools/ledenkaart.php?xml=true&random="+randomnumber, function(data, responseCode) {
    	var xml = GXml.parse(data);

	//store markers in markers array
    var markers = xml.documentElement.getElementsByTagName("marker");

	// create marker icon
	var icon = new GIcon();
	icon.image = "http://plaetjes.csrdelft.nl/layout/favicon.ico";
	icon.iconSize = new GSize(24, 23);
	icon.iconAnchor = new GPoint(0, 20);
	icon.infoWindowAnchor = new GPoint(5, 1);

	//loop over the markers array
    for (var i = 0; i < markers.length; i++) {
		var address = markers[i].getAttribute("address");
		var html = markers[i].getAttribute("html");
		showAddress(map,geocoder,address,html,icon);
    } //close for loop

	  }
	); //close GDownloadUrl

//Create marker and set up event window
function createMarker(point,html,icon){
  var marker = new GMarker(point);
  GEvent.addListener(marker, "click", function() {
     marker.openInfoWindowHtml(html);
  });
  return marker;
}

//showAddress
function showAddress(map,geocoder,address,html,icon) {
  geocoder.getLatLng(
    address,
    function(point) {
      if (!point) {
      //  alert(address + " niet gevonden");
      } else {
        var marker = createMarker(point,html+'<br/><br/>'+address,icon);
        map.addOverlay(marker);
		map.addControl(new GMapTypeControl());
      }
    }
  );
}


	 	
      }
map.setCenter(new GLatLng(	52.015,4.356667), 14);
    }
//
    //]]>
    </script>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="width: 700px; height: 600px"></div>
  </body>
</html>

