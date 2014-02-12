   
   document.oncontextmenu = function(){return false;};
   
	var files = [		
		// CSS
	
	    "admin/os/resources/css/ext-all.css",
	
	    "admin/os/filemanager/css/icons.css",
		//"admin/os/filemanager/css/webpage.css",
//		"admin/os/filemanager/css/filetree.css",
		"admin/os/filemanager/css/filetype.css",
		"admin/os/filemanager/css/famflag.css",
		"admin/os/filemanager/css/Ext.ux.IconCombo.css",
		
		"admin/os/resources/css/galeries.css",
		"admin/os/resources/css/column-tree.css",
		"admin/os/resources/style.css",
		
	    "admin/os/htmleditorimage/loadingindicator.css",
	    "admin/os/htmleditorimage/imagebrowser.css",
	    "admin/os/htmleditorimage/statictextfield.css",
			"admin/os/resources/css/cms.css",
		// JS
		
		"admin/os/js/adapter/ext/prototype/ext-prototype-adapter.js",
		"admin/os/js/cropper/cropper.js",
		
	    "admin/os/js/ext-all.js",
	 	
	    "admin/os/js/ColumnNodeUI.js",
	    "admin/os/js/ItemSelector.js",
	    "admin/os/js/MultiSelect.js",
	    "admin/os/js/DDView.js",
	    "admin/os/js/data-view-plugins.js",
	    "admin/os/js/Ext.ux.galeries.js",
	    "admin/os/js/StartMenu.js",
	    "admin/os/js/TaskBar.js",
	    "admin/os/js/Desktop.js",
	    "admin/os/js/App.js",
	    "admin/os/js/Module.js",
	    
	    "admin/os/htmleditorimage/htmleditor.js",
	    "admin/os/htmleditorimage/fileuploadbutton.js",
	    "admin/os/htmleditorimage/loadingindicator.js",
	    "admin/os/htmleditorimage/imagebrowser.js",
	    "admin/os/htmleditorimage/htmleditorimage.css",
	    "admin/os/htmleditorimage/statictextfield.js",
	    "admin/os/htmleditorimage/htmleditorimage.js",
		"admin/os/htmleditorimage/filebrowser.js",
	    
		"admin/os/filemanager/js/Ext.ux.form.BrowseButton.js",
		"admin/os/filemanager/js/Ext.ux.FileUploader.js",
		"admin/os/filemanager/js/Ext.ux.UploadPanel.js",
		"admin/os/filemanager/js/Ext.ux.FileTreeMenu.js",
		"admin/os/filemanager/js/Ext.ux.FileTreePanel.js",
		"admin/os/filemanager/js/Ext.ux.ThemeCombo.js",
		"admin/os/filemanager/js/Ext.ux.IconCombo.js",
		"admin/os/filemanager/js/Ext.ux.LangSelectCombo.js",
		"admin/os/filemanager/locale/fr_FR.js",
		"admin/os/js/GridDragSelector.js",
		"admin/os/js/ux/imagecrop.js",
		"admin/os/js/locale/ext-lang-fr.js",
		
		"admin/os/js/ux/menu/EditableItem.js",
		"admin/os/js/ux/menu/RangeMenu.js",
		
		"admin/os/js/ux/grid/GridFilters.js",
		
		"admin/os/js/ux/grid/filter/Filter.js",
		"admin/os/js/ux/grid/filter/BooleanFilter.js",
		"admin/os/js/ux/grid/filter/StringFilter.js",
		"admin/os/js/ux/grid/filter/DateFilter.js",
		"admin/os/js/ux/grid/filter/ListFilter.js",
		"admin/os/js/ux/grid/filter/NumericFilter.js",		
		"admin/os/js/XmlTreeLoader.js",
		"admin/os/js/Ext.example.msg.js",
		"admin/os/js/Ext.chewingcom.js",
		"admin/os/js/sortable0.6.js"
	
	];	  
	
	for(xx=0;xx<files.length;xx++){
		var pourcentage = Math.round((xx/files.length)*100);
		
		document.write('<script type="text/javascript">if(document.getElementById("loading-msg"))document.getElementById("loading-msg").innerHTML = "Chargement '+pourcentage+'%";</script>');
		if(files[xx].indexOf(".css")!=-1){
			document.write('<link rel="stylesheet" type="text/css" href="'+files[xx]+'"/>');
		}else{
			document.write('<script type="text/javascript" src="'+files[xx]+'"></script>');	
		}
	}		
	document.write('<script type="text/javascript">if(document.getElementById("loading-msg"))document.getElementById("loading-msg").innerHTML = "Chargement 100%";</script>');
	
