
hFileBrowserTxt={
	btnSelect:'S&eacute;lectionner',
	btnCancel:'Annuler',
	btnUpload:'Transf&eacute;rer',
	btnDelete:'Supprimer',
	btnPathParent:'R&eacute;pertoire parent',
	btnRefresh:'Rafraichir',
	Title:'S&eacute;lection de fichier',
	Filter:'Filtrer',
	Loading:'Chargement',
	DeleteTitle:'Supprimer un fichier',
	DeleteMsg:'&ecirc;tes vous s&ucirc;re de vuloir supprimer le fichier "###FILE###"?',
	DeleteFolderMsg:'Vous ne pouvez pas effacer le r&eacute;pertoire',
	AlertTitleError:'Erreur',
	NoFile:'Pas de fichier',
	UploadErrorTitle:'Erreur de transfert',
	UploadFailedTitle:'Le transfert a &eacute;chou&eacute;',
	MsgLoadingError:'Erreur de chargement.',
	TTipsName:'Nom',
	TTipsDimension:'Dimensions',
	TTipsSize:'Taille',
	UnitBytes:'octets',
	UnitKB:'Ko',
	UnitMB:'Mo',
	FILEINFO_ErrorUrl:'###URL### non trouv&eacute;',
	FILEINFO_ErrorFile:'###FILE### non trouv&eacute;',
	ReturnError:
	{
		DirNotExist:'###INFO1### n\'existe pas',
		DirWriteError:'Impossible d\'&eacute;crire ###INFO1###',
		NoFileUpload:'Aucun fichier transf&eacute;r&eacute;',
		InvalidUploadFile:'Fichier transf&eacute;r&eacute; non valide',
		FileExist:'###INFO1### existe d&eacute;j&agrave;',
		InvalidFile:'Fichier non valide (seulement ###INFO1###)',
		ImageIsTooLarge:'Image trop grande',
		UPLOAD_ERR_INI_SIZE:'Erreur: La taille du fichier d&eacute;passe la taille d&eacute;fini par la directive "upload_max_filesize" (###INFO1###) fu php.ini.',
		UPLOAD_ERR_FORM_SIZE:'Erreur: La taille du fichier d&eacute;passe la taille d&eacute;fini par la directive "MAX_FILE_SIZE"',
		UPLOAD_ERR_PARTIAL:'Erreur: Le fichier transf&eacute;r&eacute; est incomplet',
		UPLOAD_ERR_NO_FILE:'Erreur: Aucun fichier transf&eacute;r&eacute;',
		UPLOAD_ERR_NO_TMP_DIR:'Erreur: R&eacute;pertiore temporaire non trouv&eacute;',
		UPLOAD_ERR_CANT_WRITE:'Erreur: Impossible d\'&eacute;crire le fichier sur le disque',
		UPLOAD_OTHER_ERROR:'Erreur: (###INFO1### - fichier inconnu',
		UPLOAD_SUCCESS:'###INFO1### transf&eacute;r&eacute; avec succ&egrave;s'		
	}
}
Ext.ux.FileBrowser = function(config) {


  // PRIVATE

  // cache data by image name for easy lookup
  var lookup = {};
  
//   var rootpath = config.rootpath ? config.rootpath : "directory/";
//   var parentpath = rootpath;
//   var currentpath = rootpath;
  
	var rootpath 		= config.rootpath ? config.rootpath : "directory/";
	var parentpath 		= (Ext.chewingCom.Browserlastparentpath!=undefined ) 	? Ext.chewingCom.Browserlastparentpath 	: rootpath;
	var currentpath 	= (Ext.chewingCom.Browserlastpath!=undefined ) 			? Ext.chewingCom.Browserlastpath 		: rootpath;  
	
  var extension=config.extension;
  var adminfront=config.adminfront ? true : false;
  
  // currently selected image data
  var data;

  // turn indicator on to indicate image list is loading
  var indicatorOn = function() {
	  
	Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Chargement', {});

    if (Ext.getCmp('img-browser-view')) {
      Ext.getCmp('img-browser-view').getTopToolbar().items.map.indicator.disable();
  	}
  };

  // turn indicator off
  var indicatorOff = function() {
	Ext.chewingCom.progressbar.hide();
    if (Ext.getCmp('img-browser-view')) {
      Ext.getCmp('img-browser-view').getTopToolbar().items.map.indicator.enable();
  	}
  };
  
	var TraiteError=function(c)
	{
		var h=hFileBrowserTxt.ReturnError;
		var a=String(c).split('###');
		var t=a[0];
		eval('var txt=h.'+t);
		if(!txt)
		{
			return c;
		}
		else
		{
			c=txt;
			for(var i=1;i<a.length;i++)
			{
				c=String(c).replace('###INFO'+i+'###',a[i]);
			}
			return c;
		}
	}

  // format loaded image data
	var formatData = function(data)
	{
		data.label = (data.name.length > 15) ? data.name.substr(0, 12) + '...' : data.name;
		var size=data.size;
		if(size<1024)size=size+' '+hFileBrowserTxt.UnitBytes;
		else if(size < 1048576)size=(Math.round(((size * 100) / 1024)) / 100)+' '+hFileBrowserTxt.UnitKB;
		else size=(Math.round(((size * 100) / 1048576)) / 100)+' '+hFileBrowserTxt.UnitMB;

		if(data.picture==true)
		{
			data.title = hFileBrowserTxt.TTipsName+": " + data.name +
				"<br>"+hFileBrowserTxt.TTipsDimension+": " + data.width + " x " + data.height +
				"<br>"+hFileBrowserTxt.TTipsSize+": " + size;
		}
		else if(data.type=='file')
		{
			data.title = hFileBrowserTxt.TTipsName+": " + data.name +
				"<br>"+hFileBrowserTxt.TTipsSize+": " + size;
		}
		else
		{
			data.title = hFileBrowserTxt.TTipsName+": " + data.name
		}
		if (data.width > data.height)
		{
			if (data.width < 80)
			{
				data.thumbwidth = data.width;
				data.thumbheight = data.height;
			}
			else
			{
				data.thumbwidth = 80;
				data.thumbheight = 80 / data.width * data.height;
			}
		}
		else
		{
			if (data.height < 80) 
			{
				data.thumbwidth = data.width;
				data.thumbheight = data.height;
			}
			else
			{
				data.thumbwidth = 80 / data.height * data.width;
				data.thumbheight = 80;
			}
		}
		data.thumbleft = (Math.round((80 - data.thumbwidth) / 2)) + "px";
		data.thumbtop = (Math.round((80 - data.thumbheight) / 2)) + "px";
		data.thumbwidth = Math.round(data.thumbwidth) + "px";
		data.thumbheight = Math.round(data.thumbheight) + "px";
		lookup[data.name] = data;
		return data;
	};

  // create the image upload form
  var form = Ext.getBody().createChild({
    tag: 'form',
    cls: 'x-hidden'
  });

  // called if image was uploaded successfully
  var uploadSuccess = function(response, options) {
    indicatorOff();
    response = Ext.util.JSON.decode(response.responseText);
	if (response.success == 'true') {
	  this.reset();
	} else {
		Ext.MessageBox.alert(hFileBrowserTxt.UploadErrorTitle, TraiteError(response.message));
	}
  };

  // called if image was not uploaded successfully
  var uploadFailure = function(response, options) {
    indicatorOff();
		Ext.MessageBox.alert(hFileBrowserTxt.UploadFailedTitle, TraiteError(response.responseText));
  };

  // upload a new image file
  var uploadFile = function(record) {
    
    //Ext.MessageBox.alert("Upload Disabled",
    //  "Uploading of files has been disabled as this is only a demo environment.");
    
     indicatorOn();
     
     record.appendTo(form);
     
     var _u = (Ext.chewingCom.frontAdmin) ? "path="+currentpath+"&extension="+extension+"&r=1" : "path="+currentpath+"&extension="+extension;
     
     Ext.Ajax.request({
       method: 'post',
       params: _u,
       url: this.uploadURL,
       isUpload: true,
       form: form,
       success: uploadSuccess,
       failure: uploadFailure,
       scope: this
     });
  };
  
	var refreshBrowser = function() {
		view.getEl().dom.parentNode.scrollTop = 0;
		store.reload();
		Ext.getCmp('filter').reset();
	};
	
	var setcurrentpath=function(path){
		Ext.chewingCom.saveBrowserPath(path,parentpath);
		
		store.url 				= (Ext.chewingCom.frontAdmin) ? config.listURL+"?path="+path+"&r=1" : config.listURL+"?path="+path;
		store.proxy.conn.url	= (Ext.chewingCom.frontAdmin) ? config.listURL+"?path="+path+'&extension='+extension+"&r=1" : config.listURL+"?path="+path+'&extension='+extension;
		refreshBrowser();
		
	}
  var parentdir=function(){
	currentpath=parentpath;
	
	var parentpath_tmp = currentpath.split("/");
	parentpath="";
	for(xx=0;xx<parentpath.length-1;xx++){
		parentpath+=parentpath_tmp[xx]+"/";
	}
	parentpath = parentpath.length < rootpath.length ? rootpath : parentpath;
	setcurrentpath(currentpath);
  }

  // delete an image file
  var deleteImage = function(doDelete) {
    
    //Ext.MessageBox.alert("Delete Disabled",
    //  "Deleting of files has been disabled as this is only a demo environment.");

	indicatorOn();
	if (doDelete === "yes") {
	  Ext.Ajax.request({
	    method: 'post',
	    url: this.deleteURL,
	    params: "path="+currentpath+"&image=" + data.name,
	    success: function(response) {
	      	indicatorOff();
			this.reset();
	    },
	    scope: this
	  });
	}
  };

  // confirm if ok to delete image
	var confirmDelete = function() {
	  	if(data){
		  	if(data.type=="file"){
				Ext.MessageBox.confirm(hFileBrowserTxt.DeleteTitle,
	  				String(hFileBrowserTxt.DeleteMsg).replace('###FILE###',data.name), deleteImage, this);
			}else{
				Ext.MessageBox.alert(hFileBrowserTxt.AlertTitleError,hFileBrowserTxt.DeleteFolderMsg);
			}
		}
	};
  
  // create template for image thumbnails
	var thumbTemplate = new Ext.XTemplate(
		'<tpl for=".">',
			'<div class="thumb-wrap" id="{name}">',
				'<div class="thumb"><img src="{icon}" ext:qtip="{title}" style="top:{thumbtop}; left:{thumbleft}; width:{thumbwidth}; height:{thumbheight};"></div>',
				'<span>{label}</span>',
		  '</div>',
		'</tpl>'
	);
	thumbTemplate.compile();

	
	var _u = (Ext.chewingCom.frontAdmin) ? config.listURL+"?path="+currentpath+"&extension="+extension+"&r=1" : config.listURL+"?path="+currentpath+"&extension="+extension;
	
  // create json store for loading image data
	var store = new Ext.data.JsonStore({
	    url: _u,
	    id:'store-listimage',
	    root: 'images',
	    fields: [
	      'name',
	      {name: 'width', type: 'float'},
	      {name: 'height', type: 'float'},
	      'type',
		  'picture',
	      {name: 'size', type: 'float'},
	      'url',
		  'icon'
	    ],
	    sortInfo:{field:'type', direction:'ASC'},
		listeners: {
			'beforeload': {fn: indicatorOn, scope: this},
	      	'load': {fn: indicatorOff, scope: this},
	      	'loadexception': {fn: indicatorOff, scope: this}
		}
	});
	store.load();
  
  // called when image selection is changed
	var selectionChanged = function() {
    	var selNode = view.getSelectedNodes();
		if (selNode && selNode.length > 0) {
			selNode = selNode[0];
			if(Ext.getCmp('select-btn'))
				Ext.getCmp('select-btn').enable();
			if(Ext.getCmp('delete-btn'))
				Ext.getCmp('delete-btn').enable();
     		data = lookup[selNode.id];
		} else {
			if(Ext.getCmp('select-btn'))
	    		Ext.getCmp('select-btn').disable();
	    	if(Ext.getCmp('delete-btn'))
				Ext.getCmp('delete-btn').disable();
		}
	};
	
  // perform callback to parent function
	var doCallback = function() {
		selectionChanged();
		if(data){
			if(data.type=="file"){
				this.hide(this.animateTarget, function() {
		      		if (this.callback) {
						this.callback(data);
					}
				});
			}else{
				parentpath = currentpath;
				currentpath=currentpath+data.name+"/";
				setcurrentpath(currentpath);
			}
		}
  };

  // image load exception
	var onLoadException = function(v,o) {
    view.getEl().update('<div style="padding:10px;">'+hFileBrowserTxt.MsgLoadingError+'</div>');
	};
	
  // create Ext.DataView to display thumbnails
  var view = new Ext.DataView({
		tpl: thumbTemplate,
		singleSelect: true,
		overClass: 'x-view-over',
		itemSelector: 'div.thumb-wrap',
		emptyText : '<div style="padding:10px;">'+hFileBrowserTxt.NoFile+'</div>',
		store: store,
		listeners: {
			'selectionchange': {fn: selectionChanged, scope: this, buffer: 100},
			'dblclick': {fn: doCallback, scope: this},
			'loadexception': {fn: onLoadException, scope: this},
			'beforeselect': {fn: function(view) {
        return view.store.getRange().length > 0;
	    }}
		},
		prepareData: formatData.createDelegate(this)
	});

  // create filter to easily search images
	var filterView = function() {
		var filter = Ext.getCmp('filter');
		view.store.filter('name', filter.getValue());
	};

  // apply additional config values
  Ext.applyIf(config, {
  	title: hFileBrowserTxt.Title,
  	layout: 'fit',
		minWidth: 514,
		minHeight: 323,
		modal: true,
		closeAction: 'destroy',
		border: false,
		items: [{
			id: 'img-browser-view',
			autoScroll: true,
			items: view,
      		tbar: [hFileBrowserTxt.Filter+':', ' ',
			      {
			      	xtype: 'textfield',
			      	id: 'filter',
			      	selectOnFocus: true,
			      	width: 100,
			      	listeners: {
			      		'render': {fn: function() {
			  		    	Ext.getCmp('filter').getEl().on('keyup', function() {
			  		    	filterView();
			          	}, this, {buffer:500});
			      		}, scope: this}
			        }
			      }, ' ', '-', {
			        xtype: 'fileuploadbutton',
			        id: 'add',
			        iconCls: 'add-file',
			        text: hFileBrowserTxt.btnUpload,
			        handler: uploadFile.createDelegate(this),
			        scope: this
			      }, {
			        id:'delete-btn',
			        iconCls:'delete-file',
			        text:hFileBrowserTxt.btnDelete,
			        handler: confirmDelete,
			        scope: this
			      },'-',
			      {
			        id:'refresh-store',
			        iconCls: 'parentdir',
			        text:hFileBrowserTxt.btnPathParent,
			        handler:parentdir.createDelegate(this),
			        scope: this
			      },'-',
			      {
			        id:'refresh-store',
			        iconCls: 'browseimagerefresh',
			        text:hFileBrowserTxt.btnRefresh,
			        handler:refreshBrowser.createDelegate(this),
			        scope: this
			      }, 
      			'->', 
      			{
					xtype: 'tbindicator',
					id: 'indicator'
			    }, ' '
			]
		}],
		buttons: [{
			id: 'select-btn',
			text: hFileBrowserTxt.btnSelect,
			handler: doCallback,
			scope: this
		}, {
			text: hFileBrowserTxt.btnCancel,
			handler: function() {
			  this.destroy();
			},
			scope: this
		}],
		keys: {
			key: 27, // Esc key
			handler: function() {
			  this.destroy();
			},
			scope: this
		}
	});
  
  // call Ext.Window constructor passing config
	Ext.ux.FileBrowser.superclass.constructor.call(this, config);

  // refresh the image list
	this.reset = function() {
		view.getEl().dom.parentNode.scrollTop = 0;
		store.reload();
		Ext.getCmp('filter').reset();
	};
}

// Ext.ux.FileBrowser
// extension of Ext.Window
Ext.extend(Ext.ux.FileBrowser, Ext.Window, {
  // overrides Ext.Window.show
  show: function(animateTarget, cb, scope) {

    // reset view if previously used
    if (this.rendered) {
      this.reset();
    }
    
    // call Ext.Window.show
    Ext.ux.FileBrowser.superclass.show.call(this, animateTarget, cb, scope);
  }
});

