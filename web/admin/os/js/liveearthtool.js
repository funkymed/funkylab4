//-----------------------------------------------
// Live Earth
//---
Ext.chewingCom.liveEarth={
    marker:null,
    LiveMap:null,
    center:null,
    init:function (div,latlong) {
        this.LiveMap = new VEMap(div);
        
        if(latlong){
			this.LiveMap.LoadMap(new VELatLong(latlong[0], latlong[1]),16, VEMapStyle.Road, false, VEMapMode.Mode2D, true, 1);
			this.center = new VELatLong(latlong[0], latlong[1])
			this.marker = new VEShape(VEShapeType.Pushpin, this.center);
			this.LiveMap.AddShape(this.marker); 
			this.getInfo();
        }else{
	        this.LiveMap.LoadMap(new VELatLong(49.152969656170384, 5.449218749999985),5, VEMapStyle.Road, false, VEMapMode.Mode2D, true, 1);
        }
		
		//this.LiveMap.AttachEvent("onendzoom",Ext.chewingCom.liveEarth.getInfo);
		//this.LiveMap.AttachEvent("onmousemove",Ext.chewingCom.liveEarth.getInfo);
    },
    showAddress:function (address) {
      Ext.chewingCom.liveEarth.LiveMap.Find(null, address, null, null, null, null, null, null, null, null,Ext.chewingCom.liveEarth.displayWhere);
    },
	displayWhere:function (a,b,c){
		if(Ext.chewingCom.liveEarth.marker && Ext.chewingCom.liveEarth.marker!=null){
			Ext.chewingCom.liveEarth.LiveMap.DeleteShape(Ext.chewingCom.liveEarth.marker);
			Ext.chewingCom.liveEarth.marker=null
		}
		Ext.chewingCom.liveEarth.center	= (c && c!=null && c[0].LatLong) ? c[0].LatLong : a;
		Ext.chewingCom.liveEarth.LiveMap.SetCenterAndZoom(Ext.chewingCom.liveEarth.center, 16);
		Ext.chewingCom.liveEarth.marker = new VEShape(VEShapeType.Pushpin, Ext.chewingCom.liveEarth.center);
		Ext.chewingCom.liveEarth.LiveMap.AddShape(Ext.chewingCom.liveEarth.marker); 
		Ext.chewingCom.liveEarth.getInfo();
	},
    getInfo:function (){
		var center = Ext.chewingCom.liveEarth.LiveMap.GetCenter();
		Ext.getCmp('adresse_lat').setValue(center.Latitude);
		Ext.getCmp('adresse_long').setValue(center.Longitude);
		
	},
	setCenter:function(v){
		Ext.chewingCom.liveEarth.displayWhere(new VELatLong(v[0], v[1]));
	}
};


