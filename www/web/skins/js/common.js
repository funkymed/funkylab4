function swapinfo(obj,i){
	var b = $('infopratique-'+i);		
	if(b.visible()){
		//b.hide();
		Effect.BlindUp(b,{duration:.3});
		obj.innerHTML="[+]";
	}else{
		//b.show();
		Effect.BlindDown(b,{duration:.3});
		obj.innerHTML="[-]";
	}
}
function swapmap(obj,i){
	var b = $('map-'+i);		
	if(b.visible()){
		Effect.BlindUp(b,{duration:.3});
		obj.innerHTML="[+]";
	}else{
		Effect.BlindDown(b,{duration:.3});
		obj.innerHTML="[-]";
	}
}


function displayVideo(o,file){
	
		var ratio 	= 1.3333333333333333333333333333333;
		var d 		= new Date();
		var w 		= $(o).getWidth()-5;
		var h 		= Math.round(w/ratio)-5;
		
		var s2 = new SWFObject("skins/swf/flvplayer.swf","player_"+o.id,w,h,"9");
		s2.addParam("allowfullscreen","true");
		s2.addParam("wmode","opaque");
		s2.addVariable("file","../../"+file);
		s2.useExpressInstall("skins/swf/expressinstall.swf");
		s2.write(o);
}