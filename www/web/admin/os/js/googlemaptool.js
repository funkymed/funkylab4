//-----------------------------------------------
// Googlemap
//---
    var marker;
    var Icon;
    var GoogleMap=null;
    var geocoder = null;
    var inputLongitude;
	var inputLattitude; 
    var inputRecherche;
    
    function initializeGoogleMap() {
      if (GBrowserIsCompatible()) {
        GoogleMap = new GMap2(Ext.getDom("googleMapEdit"));
        GoogleMap.addControl(new GMapTypeControl());
        GoogleMap.addControl(new GSmallMapControl());
	    GoogleMap.enableScrollWheelZoom();
	    geocoder = new GClientGeocoder();
      }
    }
    function setMarkPos(center){
        marker = new GMarker(center);
        GoogleMap.addOverlay(marker);
    }
    function getInfo(v){
       v=CleanLatLong(v);
       Ext.getCmp('lattitude').setValue(v[0]);
       Ext.getCmp('longitude').setValue(v[1]);
    }
    function CleanLatLong(v){
        v=String(v);
        v=v.split("(")[1];
        v=v.split(")")[0];
        v=v.split(",");
        return v;
    }
    function showAddress(address) {
      if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " aucun de résultat");
            } else {
              GoogleMap.setCenter(point, 15);
              setCenter();
            }
          }
        );
      }
    }
    function setCenter(){
         GoogleMap.clearOverlays();
         v=CleanLatLong(GoogleMap.getCenter());
         setMarkPos(new GLatLng(v[0],v[1]));
        getInfo(marker.getLatLng());
    }
