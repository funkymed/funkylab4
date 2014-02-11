var files = [		
	// CSS
    "os/resources/css/ext-all.css",

    "os/filemanager/css/icons.css",
	"os/filemanager/css/webpage.css",
	"os/filemanager/css/filetree.css",
	"os/filemanager/css/filetype.css",
	"os/filemanager/css/famflag.css",
	"os/filemanager/css/Ext.ux.IconCombo.css",
	
	"os/resources/css/galeries.css",
	"os/resources/css/desktop.css",
	"os/resources/css/column-tree.css",
	"os/resources/css/cms.css",
	"os/resources/style.css",
	
    "os/htmleditorimage/loadingindicator.css",
    "os/htmleditorimage/imagebrowser.css",
    "os/htmleditorimage/statictextfield.css",
	
	// JS
	
	
	
 	"os/js/cropper/prototype.js",
	"os/js/cropper/scriptaculous.js",
	
	"os/js/adapter/ext/prototype/ext-prototype-adapter.js",
	"os/js/cropper/cropper.js",
	
    "os/js/ext-all.js",
 	
    "os/js/ColumnNodeUI.js",
    "os/js/ItemSelector.js",
    "os/js/MultiSelect.js",
    "os/js/DDView.js",
    "os/js/data-view-plugins.js",
    "os/js/Ext.ux.galeries.js",
    "os/js/StartMenu.js",
    "os/js/TaskBar.js",
    "os/js/Desktop.js",
    "os/js/App.js",
    "os/js/Module.js",
    
    "os/htmleditorimage/htmleditor.js",
    "os/htmleditorimage/fileuploadbutton.js",
    "os/htmleditorimage/loadingindicator.js",
    "os/htmleditorimage/imagebrowser.js",
    "os/htmleditorimage/htmleditorimage.css",
    "os/htmleditorimage/statictextfield.js",
    "os/htmleditorimage/htmleditorimage.js",
	"os/htmleditorimage/filebrowser.js",
    
	"os/filemanager/js/Ext.ux.form.BrowseButton.js",
	"os/filemanager/js/Ext.ux.FileUploader.js",
	"os/filemanager/js/Ext.ux.UploadPanel.js",
	"os/filemanager/js/Ext.ux.FileTreeMenu.js",
	"os/filemanager/js/Ext.ux.FileTreePanel.js",
	"os/filemanager/js/Ext.ux.ThemeCombo.js",
	"os/filemanager/js/Ext.ux.IconCombo.js",
	"os/filemanager/js/Ext.ux.LangSelectCombo.js",
	"os/filemanager/locale/fr_FR.js",
	"os/js/GridDragSelector.js",
	"os/js/ux/imagecrop.js",
	"os/js/locale/ext-lang-fr.js",
	
	"os/js/ux/menu/EditableItem.js",
	"os/js/ux/menu/RangeMenu.js",
	
	"os/js/ux/grid/GridFilters.js",
	
	"os/js/ux/grid/filter/Filter.js",
	"os/js/ux/grid/filter/BooleanFilter.js",
	"os/js/ux/grid/filter/StringFilter.js",
	"os/js/ux/grid/filter/DateFilter.js",
	"os/js/ux/grid/filter/ListFilter.js",
	"os/js/ux/grid/filter/NumericFilter.js",		
	"os/js/XmlTreeLoader.js",
	"os/js/Ext.chewingcom.js",
	"os/js/swfobject.js",
	"os/js/googlemaptool.js",
	


	
	//localhost
	//"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAABfCfVJ6YRX7801uApvUPDRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQqZ_yjkObcrvKdsRejp4BMyiBJSQ"
	//Preprood
	//"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAABfCfVJ6YRX7801uApvUPDRQzcXcFQfD8Q_WGQjvEGItOz7b1_xSdIaVT3yEP2BqnEP_HVRHokLnoow"
	//Prod
	//"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAABfCfVJ6YRX7801uApvUPDRSozprusKgX_-CmD3KFbYU8LLydfxR3qZSvvpci8h2bMdBypl1b7YGr0A"
	"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAABfCfVJ6YRX7801uApvUPDRSfgGLHYrTR4ik3j6lirXE8H1lZgxThhyF4NzGP8qc8hg9kpxAj12oKcg"
	
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