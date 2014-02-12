var files = [		
	// CSS
    "os/resources/css/ext-all.css",
	"os/resources/css/cms.css",
	// JS
 	"os/js/adapter/ext/ext-base.js",
    "os/js/ext-all.js"
];	    

for(xx=0;xx<files.length;xx++){
	var pourcentage = Math.round((xx/files.length)*100);
	document.write('<script type="text/javascript">document.getElementById("loading-msg").innerHTML = "Chargement '+pourcentage+'%";</script>');
	if(files[xx].indexOf(".css")!=-1){
		document.write('<link rel="stylesheet" type="text/css" href="'+files[xx]+'"/>');
	}else{
		document.write('<script type="text/javascript" src="'+files[xx]+'"></script>');	
	}
}	
document.write('<script type="text/javascript">document.getElementById("loading-msg").innerHTML = "Chargement 100%";</script>');	