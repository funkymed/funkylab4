Ext.namespace('Ext.chewingCom');
Ext.QuickTips.init();
Ext.chewingCom.progressbar;
Ext.chewingCom.frontAdmin				= false;
Ext.chewingCom.Browserrootpath			= "directory/";
Ext.chewingCom.Browserlastpath 			= null;
Ext.chewingCom.Browserlastparentpath 	= null;

Ext.chewingCom.saveBrowserPath=function(path,parent){
	Ext.chewingCom.Browserlastpath 			= path;
	Ext.chewingCom.Browserlastparentpath 	= parent;
}

Ext.chewingCom.dateFormat=function(d){
	if(d!="00/00/0000 12:00:00"){
		var ddate = new Date(d);
		return ddate.format('d/m/Y H:i:s');
	}else{
		return "";
	}
}
Ext.chewingCom.LoadAndExecJS=function(url){
	if (typeof CollectGarbage == 'function') { CollectGarbage(); }
    Ext.chewingCom.progressbar = Ext.Msg.show({
	   title: 'Chargement...',
	   msg:'Veuillez-patienter...',
	   width: 300,
	   modal:true,
	   draggable:false,
	   closable:false,
	   id:'loaderInfo',
	   progress:true
	});
	Ext.chewingCom.progressbar.updateProgress(0.5,'50%');
	Ext.Ajax.request({
    	url:url,
		success:function(e){
    		Ext.chewingCom.progressbar.updateProgress(1,'100%');
			try{
			    if(e.responseText==""){
				    Ext.chewingCom.showAlert("Error","Invalid content");
			    }else{
		            eval(e.responseText);
	            }
		    }catch(ex){
			    Ext.chewingCom.showAlert(ex.name,"Line : "+ex.number+"<br />Message : "+ex.message);
		    }
		    
		     Ext.chewingCom.progressbar.hide();
		}
	});
}

Ext.chewingCom.openIframe=function(titre,maximized,w,h,url){
	maximized = (maximized) ? maximized : false;
	w = (w) ? w : 640;
	h = (h) ? h : 480;

	var desktop = MyDesktop.getDesktop();
	desktop.createWindow({
		title:titre,
		width:w,
		height:h,
		maximized:maximized,
		iconCls: 'bogus',
	    layout:'fit',
	    border:true,
		animCollapse:false,
		constrainHeader:true,
		html:'<iframe marginwidth="0" marginheight="0" frameborder="no" border="0" src="'+url+'" width="100%" height="100%" style="background:white;"></iframe>'
	}).show();
}

Ext.chewingCom.showAlert=function(alertTitle,alertMsg,class_){
	var classbox = class_ ? class_ : 'ext-mb-error';
	Ext.MessageBox.show({
        width:300,
		title:alertTitle,
		msg:alertMsg,
		buttons: Ext.MessageBox.OK,
		icon: classbox
	});
}

Ext.chewingCom.focushtmleditor=function(textareaField){
	textareaField.suspendEvents();
	
	var htmleditor = new Ext.form.HtmlEditor({
		enableFont:true,
		enableFontSize:true,
		enableAlignments:true,
		enableColors:true,
		enableLists:true,
 		enableLinks:true,
		anchor:textareaField.anchor,
		height:textareaField.height+25,
		applyTo:textareaField.id,
		scope:this
	});
	
	if(htmleditor.getValue()==""){
		htmleditor.setValue(' ');
	}
	htmleditor.onFirstFocus();
}

Ext.chewingCom.focushtmleditorimage=function(textareaField){
	textareaField.suspendEvents();
	var htmleditor =  new Ext.ux.HTMLEditor({
 		enableFont:false,
		enableFontSize:true,
		enableAlignments:true,
		enableColors:true,
		enableLists:true,
 		enableLinks:true,
		anchor:textareaField.anchor,
		height:textareaField.height+25,
		plugins : new Ext.ux.HTMLEditorImage(),
		applyTo:textareaField.id
	});
	htmleditor.plugins = new Ext.ux.HTMLEditorImage();
	if(htmleditor.getValue()==""){
		htmleditor.setValue(' ');
	}
	htmleditor.onFirstFocus();
}

Ext.chewingCom.editWindowCrop=function(offset,editlittle){
    var i = document.createElement('img');
    i.src = 'images/'+Ext.get('ImageFile'+offset).getValue();
    if(Ext.getCmp("tailleimage"+offset) && Ext.getCmp("tailleimage"+offset).getValue()=="grande"){
	    var w = 270+24;
	    var h = 190+65;
    }else{
	    var w = 136+24;
	    var h = 97+65;
    }
	var p = new Ext.ux.PanPanel({id:'preview'+offset,frame: true,border: false,client: i});
	var editorImage1 = new Ext.Window({
		frame: true,
		title:"Recadrer l'image",
		bodyBorder :false,
		id:'ImageCropEdit'+offset,
        border: false,
	    modal:true,
	    resizable:false,
	    draggable:false,
	    closable:false,
	    buttonAlign:'center',
	    closeAction:'close',
	   	width:w, 
	   	height:h,
	    layout:'fit',
		items:[p],
	 	buttons: [{
		    text: 'Termin&eacute;',
	        handler:function(){
		        Ext.getCmp('ImageCropEdit'+offset).close();
	        }
        }]
	}).show();
}

Ext.chewingCom.updatePreview=function(item,v,w,h){
	if(v!=""){
		var vi = '<a href="../'+v+'" target="_blank"><img src="../'+v+'" width="'+w+'" height="'+h+'" /></a>';
		Ext.getDom(item).innerHTML=vi;
	}
}

Ext.chewingCom.btnImageBrowser=function(_label,_title,_value,w,h){
		
	var field_input	= new Ext.form.TriggerField({
		fieldLabel:_title,
		id:"id_"+_label,
		name:_label,
		anchor:'100%',
		value:_value,
		triggerClass: 'x-form-search-trigger',
    	onTriggerClick: function() {
			if(Ext.get('browser-image'))
				Ext.get('browser-image').destroy();
			var imagebrow = new Ext.ux.ImageBrowser({
				id:'browser-image',
				width: 620,
				height: 400,
				manager:MyDesktop.getDesktop().getManager(),
				rootpath:Ext.chewingCom.Browserrootpath,
				listURL: 'os/htmleditorimage/listimages.php',
				uploadURL: 'os/htmleditorimage/uploadimage.php',
				deleteURL: 'os/htmleditorimage/deleteimage.php',
				callback: function(data){
					if(data){
						if(data.type=="file"){
							var file = data.url.substr(3,data.url.length);
							Ext.getCmp("id_"+_label).setValue(file);
						}
					}
				}
			}).show();
    	}
	});
	field_input.on('valid',function(a){
		Ext.chewingCom.updatePreview(_label+'_preview',a.getValue(),w,h);
	});
	var preview	= {
		bodyStyle:'padding-left:135px;padding-top:10px;padding-bottom:10px;',
		html:'<div id="'+_label+'_preview" style="width:'+w+'px;height:'+h+'px;background:#c6d4e6;">&nbsp;</div>'
	};
	return {field:field_input,preview:preview};	
}

Ext.chewingCom.btnFileFront=function(_label,_title,_value,extension){
	var w=64;
	var h=64;
	var field_input	= new Ext.form.TriggerField({
		fieldLabel:_title,
		id:"id_"+_label,
		name:_label,
		anchor:'95%',
		value:_value,
		triggerClass: 'x-form-search-trigger',
    	onTriggerClick: function() {
			if(Ext.get('browser-file'))
				Ext.get('browser-file').destroy();
			var imagebrow = new Ext.ux.FileBrowser({
				id:'browser-file',
				width: 620,
				height: 400,
				manager:winManager,
				rootpath:Ext.chewingCom.Browserrootpath,
				extension:extension,
				listURL: 'admin/os/htmleditorimage/file_list.php',
				uploadURL: 'admin/os/htmleditorimage/file_upload.php',
				deleteURL: 'admin/os/htmleditorimage/file_delete.php',
				callback: function(data){
					if(data){
						if(data.type=="file"){
							var file = data.url.replace("../","");
							Ext.getCmp("id_"+_label).setValue(file);
						}
					}
				}
			}).show();
    	}
	});
	
	field_input.on('valid',function(a){
		(function(){
			
			Ext.chewingCom.updateFilePreview(_label+'_preview',a.getValue(),w,h,true);
		}).defer(300);
		
	});
	
	var preview	= {
		bodyStyle:'padding-left:135px;padding-top:0px;padding-bottom:0px;',
		html:'<div id="'+_label+'_preview" style="width:90%;height:'+h+'px">&nbsp;</div>'
	};
	return {field:field_input,preview:preview};	
}

Ext.chewingCom.btnFileBrowser=function(_label,_title,_value,extension){
	var w=60;
	var h=60;
	
	var field_input	= new Ext.form.TriggerField({
		fieldLabel:_title,
		id:"id_"+_label,
		name:_label,
		anchor:'90%',
		value:_value,
		triggerClass: 'x-form-search-trigger',
    	onTriggerClick: function() {
			if(Ext.get('browser-file'))
				Ext.get('browser-file').destroy();
			var imagebrow = new Ext.ux.FileBrowser({
				id:'browser-file',
				width: 620,
				height: 400,
				manager:MyDesktop.getDesktop().getManager(),
				rootpath:Ext.chewingCom.Browserrootpath,
				extension:extension,
				listURL: 'os/htmleditorimage/file_list.php',
				uploadURL: 'os/htmleditorimage/file_upload.php',
				deleteURL: 'os/htmleditorimage/file_delete.php',
				callback: function(data){
					if(data){
						if(data.type=="file"){
							var file = data.url.substr(3,data.url.length);
							Ext.getCmp("id_"+_label).setValue(file);
						}
					}
				}
			}).show();
    	}
	});
	
	field_input.on('valid',function(a){
		(function(){
			Ext.chewingCom.updateFilePreview(_label+'_preview',a.getValue(),w,h);
		}).defer(300);
		
	});
	
	var preview	= {
		bodyStyle:'padding-left:135px;padding-top:0px;padding-bottom:0px;',
		html:'<div id="'+_label+'_preview" style="width:90%;height:'+h+'px">&nbsp;</div>'
	};
	return {field:field_input,preview:preview};	
}


Ext.chewingCom.updateFilePreview=function(item,v,w,h,front){
	var rootpath = front ? "admin/" : "";
	if(v!="" && Ext.getDom(item)){
		
		var infoURL = (Ext.chewingCom.frontAdmin) ? rootpath+'os/htmleditorimage/file_info.php?r=1' : rootpath+'os/htmleditorimage/file_info.php';
		
		Ext.getDom(item).innerHTML='<img src="'+rootpath+'os/htmleditorimage/loading.gif">';//hFileBrowserTxt.Loading;
		Ext.getDom(item).style.display='block';
		Ext.Ajax.request({
			method: 'post',
			params: "file="+v,
			url: infoURL,
			success: function(response, options){
				response = Ext.util.JSON.decode(response.responseText);
				if (response.success == 'true'){
					size=response.size;
					if(size<1024)size=size+' '+hFileBrowserTxt.UnitBytes;
					else if(size < 1048576)size=(Math.round(((size * 100) / 1024)) / 100)+' '+hFileBrowserTxt.UnitKB;
					else size=(Math.round(((size * 100) / 1048576)) / 100)+' '+hFileBrowserTxt.UnitMB;
					
					var vi='';
					vi+='<table>';
					vi+='<tr>';
					vi+='<td><a href="'+response.link+'" target="_blank"><img src="'+response.url+'" width="'+response.w+'" height="'+response.h+'" /></a></td>';
					vi+='<td nowrap="nowrap" valign="top" style="padding:left:20px" >';
					if(response.picture==true){
						vi+='<strong><em>'+hFileBrowserTxt.TTipsDimension+' :</em></strong> '+response.rw+'x'+response.rh+'<br />';
					}
					vi+='<strong><em>'+hFileBrowserTxt.TTipsSize+' :</em></strong> '+size+'<br />';
					vi+='</td>';
					vi+='</tr>';
					vi+='</table>';
					Ext.getDom(item).innerHTML=vi;
					Ext.getDom(item).style.height='70px';
					Ext.getDom(item).style.display='block';
				}else{
					c=String(hFileBrowserTxt.FILEINFO_ErrorFile).replace('###FILE###',v);
					Ext.MessageBox.alert(hFileBrowserTxt.AlertTitleError, c);
					Ext.getDom(item).innerHTML='';
				}
  			},failure:  function(response, options){
				c=String(hFileBrowserTxt.FILEINFO_ErrorUrl).replace('###URL###',infoURL);
				Ext.MessageBox.alert(hFileBrowserTxt.AlertTitleError, c);
				Ext.getDom(item).innerHTML='';
			}			
		});
	}
}


Ext.chewingCom.allPanel = new Array();

Ext.chewingCom.addPanel = function(title,items,id){
	
	bAutoScroll=true;
	if(items.length>0)
	{
		if(items[0].getXType()=='tabpanel')
		{
			bAutoScroll=false;
		}
	}
	var panel = new Ext.form.FormPanel({
		id:id,
		title:title,
	    frame:true,
	    labelWidth: 130,
		layout: 'form',
		border:false,
		items:items,
		autoScroll:bAutoScroll
	});
	return panel;
}
	
Ext.chewingCom.StartEditing = function (){
	Ext.get('ux-taskbar').mask();
}

Ext.chewingCom.StopEditing=function(win){
	Ext.get('ux-taskbar').unmask();
	MyDesktop.getDesktop().taskbar.removeTaskButton(win.taskButton);
	win.destroy();
}

Ext.chewingCom.alertItemSelect=function(){
	Ext.MessageBox.show({ title:'Erreur', msg:"Vous devez s&eacute;lectionner une donn&eacute;e", buttons: Ext.MessageBox.OK, icon: 'ext-mb-warning' });
}

Ext.chewingCom.yesnoRowGridRender = function(o){
	return o==1 ? "<span style='color:black;'>yes</span>" : "<span style='color:#ccc;'>no</span>";
}

Ext.chewingCom.previewVideo=function(fileFlv,mode){
	
	if(mode && mode==true){
		Ext.chewingCom.StartEditing();
		MyDesktop.getDesktop().createWindow({
		    title: "Video",
		    id:"videopreview",
		    iconCls:'file-flv',
		   	width:528, 
		   	height:453,
		    resizable:false,
		    maximizable:false,
		    minimizable:false,
		    draggable:false,
		    buttonAlign:'center',
		    closeAction:'close',
		    close:function(){ Ext.chewingCom.StopEditing(this); },
		    modal:true,
		    layout:'fit',
		    items:{html:'<div id="previewVideoFlvItem"></div>'},
		     buttons: [{
			        text: 'Fermer',
			        handler:function(){
			        	Ext.getCmp('videopreview').close();
			        }
				}]
		}).show();
	}else{
		MyDesktop.getDesktop().createWindow({
		    title: "Video",
		    id:"videopreview",
		    iconCls:'file-flv',
		   	width:528, 
		   	height:453,
		    resizable:false,
		    maximizable:false,
		    minimizable:false,
		    draggable:false,
		    buttonAlign:'center',
		    modal:true,
		    layout:'fit',
		    items:{html:'<div id="previewVideoFlvItem"></div>'},
		     buttons: [{
			        text: 'Fermer',
			        handler:function(){
			        	Ext.getCmp('videopreview').close();
			        }
				}]
		}).show();

	}
	
	(function(){
		
		var s1 = new SWFObject("os/swf/flvplayer.swf","loader","512","384","9");
		
		s1.addParam("allowfullscreen ","true");
		s1.addVariable("autostart","1");
		s1.addVariable("file",fileFlv);
		s1.write("previewVideoFlvItem");

		
	}).defer(200);
	
}




Ext.chewingCom.editWindowCrop=function(obj,size){
	
	if(obj.getValue()=="")
		return false;
		
	
	var w=size.w;
	var h=size.h;
	Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
							
					        
	Ext.Ajax.request({
    	url:'os/php/prepare_for_crop.php?file='+obj.getValue(),
		success:function(e){
			Ext.chewingCom.progressbar.hide();
			var file = '../'+e.responseText;
			var editorImage1 = new Ext.Window({
				frame: true
				,title:"Recadrer l'image"
				,id:'ImageCropEdit'
				,bodyBorder :false
			    ,border: false
			    ,modal:true
			    ,resizable:false
			    ,draggable:false
			    ,closable:false
			    ,buttonAlign:'center'
			    ,closeAction:'close'
			   	,width:655
			   	,height:305
			    ,layout:'border'
				,items:[{
						 layout:'fit'
						,region:'center'
						,html:'<img width="320" height="240" id="cropimage" src="'+file+'?timestamp='+new Date().getTime()+'" />'
					},new Ext.form.FormPanel({
						region:'east',
						width:320,
						id:'coordcropimage',
						layout:'form',
					    labelWidth: 80,
					    frame:true,
						border:false,
						items:[
							{html:'<div id="previewCrop" style="text-align:center;"></div><br/><div style="text-align:center;">dimensions : '+w+'x'+h+'</div>'}
							,new Ext.form.Hidden({fieldLabel: "image",	name: "image",value:e.responseText})
							,new Ext.form.Hidden({fieldLabel: "x1",		name: "x1",			id: "cropx1"})
							,new Ext.form.Hidden({fieldLabel: "y1",		name: "y1",			id: "cropy1"})
							,new Ext.form.Hidden({fieldLabel: "x2",		name: "x2",			id: "cropx2"})
							,new Ext.form.Hidden({fieldLabel: "y2",		name: "y2",			id: "cropy2"})
							,new Ext.form.Hidden({fieldLabel: "width",	name: "width",		id: "cropwidth"})
							,new Ext.form.Hidden({fieldLabel: "height",	name: "height",		id: "cropheight"})
						]
				})]
			 	,buttons: [{
				    text: 'Valider',
			        handler:function(){
				        Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				        var objSave = Ext.getCmp('coordcropimage').getForm().getValues();
				        objSave.end_w = h;
						objSave.end_h = w;
				        Ext.Ajax.request({
					    	url:'os/php/do_the_crop.php',
					    	params:objSave,
							success:function(e){
								obj.setValue(e.responseText);
								Ext.chewingCom.progressbar.hide();
								Ext.getCmp('ImageCropEdit').close();
							}
						});
			        }
		        },{
				    text: 'Annuler',
			        handler:function(){
				         Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				         var objSave = Ext.getCmp('coordcropimage').getForm().getValues();
				         Ext.Ajax.request({
					    	url:'os/php/remove_tmp_crop.php',
					    	params:objSave,
							success:function(e){
								Ext.chewingCom.progressbar.hide();
								Ext.getCmp('ImageCropEdit').close();
							}
						});
			        }
		        }]
			}).show();
			
			(function(){
			    cropClass = new Cropper.ImgWithPreview( 'cropimage',{
						minWidth: w,
						minHeight: h,
						ratioDim: { x: w, y: h },
						displayOnInit: true,
						onEndCrop: function( coords, dimensions){
							if(Ext.getCmp( 'cropx1' )) Ext.getCmp( 'cropx1' ).setValue(coords.x1);
							if(Ext.getCmp( 'cropy1' )) Ext.getCmp( 'cropy1' ).setValue(coords.y1);
							if(Ext.getCmp( 'cropx2' )) Ext.getCmp( 'cropx2' ).setValue(coords.x2);
							if(Ext.getCmp( 'cropy2' )) Ext.getCmp( 'cropy2' ).setValue(coords.y2);
							if(Ext.getCmp( 'cropwidth' )) Ext.getCmp( 'cropwidth' ).setValue(dimensions.width);
							if(Ext.getCmp( 'cropheight' )) Ext.getCmp( 'cropheight' ).setValue(dimensions.height);
								
						},
						previewWrap: 'previewCrop'
				});
			}).defer(200);
  		
		}
	});

}
Ext.chewingCom.editor=function(id,format,online,config,sidebardisplay,childdisplay,infodisplay,liendisplay,agendadisplay,menufooterdisplay,actualitesdisplay,brotherdisplay,demarchedisplay){
	
	
	
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	Ext.chewingCom.frontAdmin = true;
	
	winManager = new Ext.WindowGroup();
	winManager.zseed=100;
	this.page_id=id;
	Ext.chewingCom.dragconfig = config;
	//---- ONLINE 
	var online = new Ext.form.Checkbox ({
		boxLabel	: "En ligne",
		id:'page_online',		
		name : "agenda_online",
		checked:online   			 
	});
	online.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'online',online:o.checked,id:id},
        	success:function(){
	        	document.location.reload(false);
        	}
		});
	});
	
	
	//---- SIDEBAR 
	var sidebar = new Ext.form.Checkbox ({
		boxLabel	: "Colonne",
		id:'sidebar',		
		name : "sidebar",
		checked:sidebardisplay   			 
	});
	sidebar.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'sidebar',sidebar:o.checked,id:id},
        	success:function(){
		       	document.location.reload(false);
        	}
		});
	});
	
	
	//---- CHILD 
	var childview = new Ext.form.Checkbox ({
		boxLabel	: "Enfants",
		id:'childview',		
		name : "childview",
		checked:childdisplay   			 
	});
	childview.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'childview',childview:o.checked,id:id},
        	success:function(){
		       	document.location.reload(false);
        	}
		});
	});
	
	//---- AGENDAS 
	var agendaview = new Ext.form.Checkbox ({
		boxLabel	: "Agenda",
		id:'agendaview',		
		name : "agendaview",
		checked:agendadisplay   			 
	});
	agendaview.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'agendaview',agendaview:o.checked,id:id},
        	success:function(){
		       	document.location.reload(false);
        	}
		});
	});
	
	//---- PRATIQUE 
	var infoview = new Ext.form.Checkbox ({
		boxLabel	: "Pratique",
		id:'infoview',		
		name : "infoview",
		checked:infodisplay   			 
	});
	infoview.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'infoview',infoview:o.checked,id:id},
        	success:function(){
		       	document.location.reload(false);
        	}
		});
	});		
	
	//---- DEMOCRATIE 
	var lienview = new Ext.form.Checkbox ({
		boxLabel	: "Democratie",
		id:'lienview',		
		name : "lienview",
		checked:liendisplay   			 
	});
	lienview.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'lienview',lienview:o.checked,id:id},
        	success:function(){
		       document.location.reload(false);
        	}
		});
	});	
	//---- DEMARCHES 
	var demarcheview = new Ext.form.Checkbox ({
		boxLabel	: "Demarches",
		id:'demarcheview',		
		name : "demarcheview",
		checked:demarchedisplay   			 
	});
	demarcheview.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'demarcheview',demarcheview:o.checked,id:id},
        	success:function(){
		       document.location.reload(false);
        	}
		});
	});	
	
	//---- Menu Footer 
	var menufooter = new Ext.form.Checkbox ({
		boxLabel	: "Menu Footer",
		id:'menufooter',		
		name : "menufooter",
		checked:menufooterdisplay
	});
	menufooter.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'menufooter',menufooter:o.checked,id:id},
        	success:function(){
		       document.location.reload(false);
        	}
		});
	});	
	
	//---- Actualites
	var actualites = new Ext.form.Checkbox ({
		boxLabel	: "Actualit&eacute;s",
		id:'actualites',		
		name : "actualites",
		checked:actualitesdisplay
	});
	actualites.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'actualites',actualites:o.checked,id:id},
        	success:function(){
		       document.location.reload(false);
        	}
		});
	});		
		
	//---- Brother
	var brother = new Ext.form.Checkbox ({
		boxLabel	: "Pages soeur",
		id:'brother',		
		name : "brother",
		checked:brotherdisplay
	});
	brother.on('check',function(o){
		Ext.Ajax.request({
        	url:"admin/os/plugins/modules/site_arbo/action.php", 
        	params:{action:'brother',brother:o.checked,id:id},
        	success:function(){
		       document.location.reload(false);
        	}
		});
	});	
	//---- FORMAT
	 
	var comboformat = new Ext.form.ComboBox({
		name:'Format',
		id:'formatpage',
		defaults:{bodyStyle:'padding:10px'}, 
		width:164,
		fieldLabel	: "Format de la page",
	    store:  new Ext.data.SimpleStore({
		    fields: ['text','image','value'],
			data : [
				 ['1 colonne','format_1c-100.gif','1c-100']
				,['2 colonnes 25-75','format-2c-25-75.gif','2c-25-75']
				,['2 colonnes 50-50','format-2c-50-50.gif','2c-50-50']
				,['2 colonnes 75-25','format-2c-75-25.gif','2c-75-25']
				,['3 colonnes 25-25-50','format-3c-25-25-50.gif','3c-25-25-50']
				,['3 colonnes 25-50-25','format-3c-25-50-25.gif','3c-25-50-25']
				,['3 colonnes 50-25-25','format-3c-50-25-25.gif','3c-50-25-25']
				,['3 colonnes 33-33-33','format-3c-33-33-33.gif','3c-33-33-33']
			]
		}),
		editable:false,
		tpl: new Ext.XTemplate(
		    '<tpl for="."><div class="search-item" style="font-size:10pt;font-weight:bold;">',
		        '<img src="admin/os/resources/pageformat/{image}" alt="{text}" width="64" height="64" align="absmiddle" style="margin-right:10px;"/>{text}',
		    '</div></tpl>'
		),
		valueField:'value',
	    displayField:'text',
	    mode: 'local',
	    triggerAction: 'all',
	    listWidth:250,
	    hideTrigger:false,
	    selectOnFocus:true,
	    itemSelector: 'div.search-item',
	    value:format,
	    onSelect: function(record){
		    var myliste = this;
		    myliste.collapse();
		    Ext.Msg.confirm('Confirmation', 'En modifiant le format vous perdrez vos reglage de position ?', function(btn){
			    if (btn == 'yes'){
				    myliste.setValue(record.data.value);
				    myliste.setRawValue(record.data.text);
				    Ext.Ajax.request({
			        	url:"admin/os/plugins/modules/site_arbo/action.php", 
			        	params:{action:'format',format:record.data.value,id:id},
			        	success:function(){
				        	//document.location.href='site.php?id='+id;
				        	document.location.reload();
			        	}
					});
				}
			});
	    }
	});
	
	var p = new Ext.Panel({
		frame:true,
		buttonAlign:'center',
		layout:'form',
		items:[
		{
            xtype: 'checkboxgroup',
            fieldLabel: 'Configuration',
            columns: 2,

            items: [
            	online,sidebar,childview,infoview,lienview,demarcheview,agendaview,menufooter,actualites,brother
			]
		},comboformat]
	});

	var _w = new Ext.Window({
		title:'Edition',
		resizable:false,
		minimizable:false,
		maximizable:false,
		closable:false,
		manager:winManager,
		width:320,
		x:10,
		y:10,
		//collapsible : true,
		stateId:'statefloatwindow3',
		anchorTo:window,
		buttonAlign:'center',
		stateEvents: ["move"],
		tools:[{
		    id:'refresh',
		    qtip: 'Recharger',
		    handler: function(event, toolEl, panel){
		        document.location.reload();
		    }
		}
		/*,{
		    id:'close',
		    qtip: 'Deconnexion',
		    handler: function(event, toolEl, panel){
		       document.location.href='admin/os/php/f_deconnexion.php';
		    }
		}*/
		],

		tbar:[
			{
				text:'Options',
				menu:[
				{
					text:'Metas',
					handler:function(){
						Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_meta.php?id='+id);
					}
				},'-',{
					text:'Types',
					handler:function(){
						Ext.example.msg('Info', 'Types');
						Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_types.php?id='+id);
					}
				},'-',{
					text:'Url Redirection',
					handler:function(){
						Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_urlredirect.php?id='+id);
					}
				}]
			},'-',{
				text:'Colonne de droite',
				menu:[
					{
						text:'Pratiques',
						handler:function(){
							Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_infopratique.php?id='+id);
						}
					},{
						text:'Democratie',
						handler:function(){
							Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_lieninterne.php?id='+id);
						}
					},{
						text:'Demarches',
						handler:function(){
							Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_demarches.php?id='+id);
						}
					}
				]
			},'-',{
				text:'Contacts',
				handler:function(){
					Ext.chewingCom.LoadAndExecJS('admin/os/plugins/modules/site_arbo/edit_contacts.php?id='+id);
				}
			}
				
			
		],
		items:p
	});
	
	//get( String name, Mixed defaultValue )
	_w.show()
	
	
	//p.render('adm-btns');
	
	Ext.chewingCom.initDropZone(config);
}

Ext.chewingCom.initDropZone = function(config) {
	
	this.config=config;
	Ext.chewingCom.dragContextMenu = new Ext.menu.Menu({ 
		items: [
			{
				text:'Editer',
				handler:function(a,b,c){
					
					var idv		= Ext.chewingCom.SelectedBox.id.replace('handle_','');
					var info 	= idv.split("_");
					var newObj 	= {field:info[0],type:info[1],default:""};
			
					
					var i = {
						div:idv+'_editorpanel',
						type:info[1],
						table:'site_page',
						field:info[0],
						id:Ext.chewingCom.page_id
					}
					Ext.chewingCom.frontEditor(i);
				}
			}
		]
	});
	
	for(x=0;x<this.config.length;x++){
		var tt = x+1;
		if(Ext.get('dd'+tt+'-ct')){
			Ext.get('dd'+tt+'-ct').addClass('dd-ct');
			var __div = Ext.query('/div','dd'+tt+'-ct');
			var c = this.config[x].items;
			for(y=0;y<c.length;y++){
				if(__div[y]){
					var i		= c[y];
					
					
					var e		= __div[y];
					
					e.id=i.field+"_"+i.type;
					
					var value = (e.innerHTML==undefined) ? "" : e.innerHTML;
					
					
					
					var __html 	= '<div id="'+e.id+'_editorpanel">'+e.innerHTML+'</div>';
					if(i.type=='video')
						__html+='<div id="'+e.id+'_videopreview">&nbsp;</div>';
						
					Ext.get(e).addClass("dd-item");
					
					e.innerHTML = "";
					
					var dh 		= Ext.DomHelper;
					
					
					
					dh.append(e,{id: "handle_"+e.id,tag: 'span',cls: 'handle',html:i.field+' / D&eacute;placer'});	//Add handler
					dh.append(e,{id: e.id+"_container",tag: 'div',html:__html});									//Panel HTML Container
						
					//var p = new Ext.Panel({html:__html,id: e.id+"_panel"});//Panel EXT Container
					//p.render(e.id+"_container");
					
					e.setStyle({height: "auto"});
					
					if(i.type=='video' && value!=""){
						Ext.chewingCom.previewFrontVideo({div:e.id+'_editorpanel',_new:value});
					}
				}
			}
			
			this['dragzone'+tt] = new Ext.ux.Sortable({
				id:'dragzone-'+tt,
				column:x,
				container : 'dd'+tt+'-ct',
				tagName:'div',
				className:"dd-item",
				handles : true,
				horizontal : true,
				dragGroups : ['group'],
				contextMenu : function(a,b,c){
					var info 	= b.id.split("_");
					if(info[1]!="actualites"){
						Ext.chewingCom.SelectedBox = b;
						Ext.chewingCom.dragContextMenu.show(b.id);
					}
				}
			});
			
			this['dragzone'+tt].on('endDrag',function(a,b,c){
				Ext.chewingCom.UpdateConfig();
			});
			
			this['dragzone'+tt].on('notifyDrop',function(a,b,c){
				//Ext.chewingCom.UpdateConfig();
				
			}); 			
			
		}
	}
}

Ext.chewingCom.UpdateConfig=function(){
	
	var newconfig = new Array();
	
	for(x=0;x<this.config.length;x++){
		newconfig[x]={items:new Array()};
		var __div = this['dragzone'+(x+1)].serialize();
		Ext.each(__div,function(e){
			var info = e.id.split("_");
			var newObj = {field:info[0],type:info[1],default:""};
			newconfig[x].items.push(newObj);
			var value = Ext.getDom(e.id+'_editorpanel').innerHTML;
			if(info[1]=='video' && value!=""){
				Ext.chewingCom.previewFrontVideo({div:e.id+'_editorpanel',_new:value});
			}
		});
	}
	
	this.config=newconfig;
	
	Ext.Ajax.request({
    	url:"admin/os/plugins/modules/site_arbo/action.php", 
    	params:{action:'config',config:Ext.util.JSON.encode(newconfig),id:this.page_id},
    	success:function(){
        	Ext.example.msg('Info', "Mise en page sauvegard&eacute;e");
    	}
	});
}

Ext.chewingCom.initEditor=function(obj){
	if(Ext.get(obj.div)){
		Ext.getDom(obj.div).style.background='#cccccc';
		Ext.get(obj.div).on({
			'contextmenu':function(){
				Ext.chewingCom.frontEditor(obj);
			}
			,'mouseover' : function(){
				Ext.getDom(obj.div).style.background='#efefef';
			}
			,'mouseout' : function(){
			   	Ext.getDom(obj.div).style.background='#cccccc';
		    }
    	});
	}
}

Ext.chewingCom.previewFrontVideo=function(obj){
	if(obj._new==""){
		
		Ext.getDom(obj.div.replace('_editorpanel','_videopreview')).innerHTML="";
		
	}else{
		var o = obj.div.replace('_editorpanel','_videopreview');
		var ratio = 1.3333333333333333333333333333333;
		
		if(Ext.get(o)){
			var w = Ext.get(o).getComputedWidth()-5;
			var h = Math.round(w/ratio)-5;
			
			var s2 = new SWFObject("skins/swf/flvplayer.swf",obj.div+"_player",w,h,"9");
			s2.addParam("allowfullscreen","true");
			s2.addParam("wmode","opaque");
			s2.addVariable("file","../../"+obj._new);
			s2.useExpressInstall("skins/swf/expressinstall.swf");
			s2.write(obj.div.replace('_editorpanel','_videopreview'));
		}
	}
}


// Ext.chewingCom.displayVideo=function(o,file){

// if(Ext.get(o)){
// 		var ratio 	= 1.3333333333333333333333333333333;
// 		var d 		= new Date();
// 		var w 		= Ext.get(o).getComputedWidth()-5;
// 		var h 		= Math.round(w/ratio)-5;
// 		
// 		var s2 = new SWFObject("skins/swf/flvplayer.swf","player_"+d.getTime(),w,h,"9");
// 		s2.addParam("allowfullscreen","true");
// 		s2.addParam("wmode","opaque");
// 		s2.addVariable("file","../../"+file);
// 		s2.useExpressInstall("skins/swf/expressinstall.swf");
// 		s2.write(o);
// 	}
// }
Ext.chewingCom.frontEditor=function(obj){
	

	var v = Ext.getDom(obj.div).innerHTML;
	obj.old=v;
	
	if(obj.type=="textfield"){
		var field		= new Ext.form.TextField({id:"editor_"+obj.div,hideLabel:true,anchor:'100%',value: v});
	}else if(obj.type=="textarea"){
		var field = new Ext.form.TextArea  ({
			id:"editor_"+obj.div,
			hideLabel:true,
			anchor:'100%',
			height:300,
			value: v
		});
	}else if(obj.type=="htmleditor"){
		
		var p = new Ext.ux.HTMLEditorImage();
		
		var field =  new Ext.ux.HTMLEditor({
			id:"editor_"+obj.div,
			hideLabel:true,
			anchor:'90%',
			height:400,
			plugins :p ,
			value:v
		});
		
		
	}else if(obj.type=="image"){
		var imageB1 			= Ext.chewingCom.btnFileFront("editor_"+obj.div,"Image",v,'jpg|gif|png');
		var file_image			= imageB1.field;
		var file_image_preview	= imageB1.preview;
		var field 				= [file_image,file_image_preview];
	}else if(obj.type=="video"){
		var videoB1 			= Ext.chewingCom.btnFileFront("editor_"+obj.div,"Video",v,'flv');
		var file_video			= videoB1.field;
		var file_video_preview	= videoB1.preview;
		var field 				= [file_video,file_video_preview];
	}else if(obj.type=="file"){
		var _B1 				= Ext.chewingCom.btnFileFront("editor_"+obj.div,"Fichier",v,'*');
		var file_				= _B1.field;
		var file__preview		= _B1.preview;
		var field 				= [file_,file__preview];
	}else if(obj.type=="bool"){
		if(v=='Y'){
			v1 = true;
			v2 = false;
		}else{
			v1 = false;
			v2 = true;
		}
		
		var field = {
			xtype: 'checkboxgroup',
			defaultType: 'radio',
			items: [
				{boxLabel: 'Oui', id: "editor_"+obj.div, name: "editor_"+obj.div,value:'Y',checked:v1},
				{boxLabel: 'Non', name: "editor_"+obj.div,value:'N',checked:v2}
			]
		};
	}else{
		return false;
	}
	
	try{
	
		new Ext.Window({
			title:'Edition'
			,id:'frontEditForm'
			,modal:true
			,width:640
			,draggable:false
			,resizable:false
			,manager:winManager
			,minimizable:false
			,maximizable:false
			,items:new Ext.form.FormPanel({
				frame:true,
				border:false,
				items:field
			})
			,buttonAlign:'center'
			,buttons: [
		     	{
			     	text:'Enregistrer',
			     	handler:function(){
				     	Ext.getDom(obj.div).edit=null;
				     	var newV = "&nbsp;";
				     	if(obj.type=="image" || obj.type=="video" || obj.type=="file"){
					     	if(Ext.getCmp("id_editor_"+obj.div)){
					     		obj._new = Ext.getCmp("id_editor_"+obj.div).getValue();
				     		}
				     		if(obj.type=="video"){
					     		obj._new = (obj._new.indexOf("directory/")==-1 ) ? "directory/"+obj._new : obj._new;
					     		Ext.chewingCom.previewFrontVideo(obj);
							}
							
				     	}else  if(Ext.getCmp("editor_"+obj.div) && obj.type=="date"){
							obj._new 		= Ext.getCmp("editor_"+obj.div).getValue()!="" ? Ext.getCmp("editor_"+obj.div).getValue().format('Y-m-d') : "";
							obj._newDate 	= Ext.getCmp("editor_"+obj.div).getValue()!="" ? Ext.getCmp("editor_"+obj.div).getValue().format('d/m/Y') : "";
						}else  if(Ext.getCmp("editor_"+obj.div) && obj.type=="bool"){
							obj._new = Ext.getCmp("editor_"+obj.div).getValue()==true ? 'Y' :'N';
						}else{
							
							//if(Ext.getCmp("editor_"+obj.div) && Ext.getCmp("editor_"+obj.div).getXType()=="htmleditor"){
							if(Ext.getCmp("editor_"+obj.div) &&  obj.type=="htmleditor"){
								Ext.getCmp("editor_"+obj.div).syncValue();
								//-----------------------------------------------
						     	// Patch pour target _blank
						     	//--
								var bd = Ext.getCmp("editor_"+obj.div).getEditorBody();
								/* Get the html from editor */
								var html = bd.innerHTML;
								/* Request all links */
								var tagsLink = bd.getElementsByTagName("a");
								for (var i = 0; i < tagsLink.length; i++) { 
								    var target = tagsLink[i].target;
								    //Si lien interne
								    if (tagsLink[i].href.indexOf('cotestade.myfriendtrip.com')!=-1 || tagsLink[i].href.indexOf('www.cotesatede.fr')!=-1 || tagsLink[i].href.indexOf('www.cotesatede.com')!=-1) {
									    tagsLink[i].target = '_top';
								    }else{
								        /* target is not set, do it */ 
								        tagsLink[i].target = '_blank';
								    }
								}
								
						     	//-----------------------------------------------
								
								Ext.getCmp("editor_"+obj.div).syncValue();
								obj._new = Ext.getCmp("editor_"+obj.div).getValue();
							}else if(Ext.getCmp("editor_"+obj.div)){
								obj._new = Ext.getCmp("editor_"+obj.div).getValue();
							}
						}
						
						if(obj._new)
							newV = obj._new;
				     	
				     	
				     	
				     	Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				        Ext.Ajax.request({
				        	url:'admin/os/php/module.php', 
							params:obj,
							success: function(e) {
								Ext.chewingCom.progressbar.hide();
								var returnValue = e.responseText;
								if(returnValue==1){
				 					Ext.getDom(obj.div).innerHTML=newV;
			 						Ext.example.msg('Info', "Sauvegarde de vos modifications.");
								}else{
									Ext.example.msg('Erreur Serveur', e.responseText);
								}
								Ext.getCmp('frontEditForm').close();
							}
						});
			     	}
			    },{
			     	text:'Annuler',
			     	handler:function(){
				     	Ext.example.msg('Info', "Annulation d'&eacute;dition");
				     	Ext.getCmp('frontEditForm').close();
			     	}
		     	}
		     ]
		}).show();
	}catch(e){
		//console.debug(e);
	}
	
}
