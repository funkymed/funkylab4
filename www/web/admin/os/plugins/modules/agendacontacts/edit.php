if(typeof Ext.chewingCom.getFormAgendacontact!='object'){
	
	
	Ext.chewingCom.getFormAgendacontact = {
		win:null,
		id:null,
		winId:'winformagendacontactedit',
		formId:'formEditagendacontact',
		iconCls:'user',
		title: 'Agenda contact',
		googlemapinit:false,
		form:null,
		load:function(id){
			this.id=id;
			Ext.getCmp(this.formId).load({url:'os/plugins/modules/agendacontacts/data.php?id='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		initGooglemap:function(){
			var v = Ext.chewingCom.getFormAgendacontact.form.getForm().getValues();
			
	    	if(GoogleMap)
	    		GoogleMap.clearOverlays();
	    	initializeGoogleMap();	
	    	
	    	var centerPoint = new GLatLng(v.contact_lat,v.contact_long);
	    	(function(){
		    	if(this.id){
			    	GoogleMap.setCenter(centerPoint,15);
		    	}else{
			    	GoogleMap.setCenter(centerPoint,5);
		    	}
		    	
			    setMarkPos(centerPoint);
		    }).defer(100);
		},
		afterLoad:function(form,action){
	    	
		},		
		getForm:function(){
			
			this.form_identity = new Ext.Panel({
			    frame:false,
			    border:false,
			    title:'Identit&eacute;e',
				layout: 'border',
			    items: [
			    	{
			    		region:'center',
			    		layout: 'form',
			    		frame:true,
			    		//labelWidth:70,
			    		items:[
					    	 new Ext.form.Hidden({name : "action",value:"add"})
				    		,new Ext.form.Hidden({name : "id_contact"})
							,new Ext.form.FieldSet({
								title: 'Identit&eacute;e',
								autoHeight:true,
								items:[
									 new Ext.form.TextField({fieldLabel	 : "Titre",	name : "contact_titre",	anchor:'100%'})
									,new Ext.form.TextField({fieldLabel	 : "Nom",	name : "contact_nom",	anchor:'100%'})
									,new Ext.form.TextField({fieldLabel  : "Prenom",name : "contact_prenom",anchor:'100%'})
								]
							}),
							new Ext.form.FieldSet({
								title: 'Adresse',
								autoHeight:true,
								items:[
									 new Ext.form.TextField({fieldLabel	 : "Adresse",		name : "contact_adresse",	anchor:'100%'})
									,new Ext.form.ComboBox({fieldLabel:'Code postal',name:'contact_cp',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
										,width:80,maxLength:5
										,triggerClass: 'x-form-search-trigger'
									    ,store:new Ext.data.Store({
										    proxy: new Ext.data.ScriptTagProxy({
										        url: "os/plugins/modules/agendacontacts/getvalues.php?field=contact_cp" 
										    }),
										   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
									    })
									})
									,new Ext.form.ComboBox({fieldLabel:'Ville',name:'contact_ville',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
										,anchor:'100%'
										,triggerClass: 'x-form-search-trigger'
									    ,store:new Ext.data.Store({
										    proxy: new Ext.data.ScriptTagProxy({
										        url: "os/plugins/modules/agendacontacts/getvalues.php?field=contact_ville" 
										    }),
										   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
									    })
									})
									,new Ext.form.ComboBox({fieldLabel:'Quartier',name:'contact_quartier',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
										,anchor:'100%'
										,triggerClass: 'x-form-search-trigger'
									    ,store:new Ext.data.Store({
										    proxy: new Ext.data.ScriptTagProxy({
										        url: "os/plugins/modules/agendacontacts/getvalues.php?field=contact_quartier" 
										    }),
										   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
									    })
									})
									
								]
							})
						]
					},{
			    		region:'east',
			    		width:400,
			    		layout: 'form',
			    		frame:true,
			    		items:[
			    			new Ext.form.FieldSet({
								title: 'Contact',
								autoHeight:true,
								items:[
									 new Ext.form.TextField({fieldLabel	 : "Telephone",		name : "contact_tel",	anchor:'100%'})
									,new Ext.form.TextField({fieldLabel	 : "Fax",			name : "contact_fax",	anchor:'100%'})
									,new Ext.form.TextField({fieldLabel  : "Email",			name : "contact_email",	anchor:'100%'})
									,new Ext.form.TextField({fieldLabel	 : "Url",			name : "contact_url",	anchor:'100%',value:'http://'})
									,new Ext.form.TextArea({fieldLabel	 : "Info pratique",	name : "contact_infopratique",	anchor:'100%',height:230})
								]
							})
			    		]
			    	}
			    ]
			});
			
			
			this.form_googlemap = new Ext.Panel({
			    frame:true,
			    title:'Geolocalisation',
				layout: 'border',
				border:false,
			    items: [
					{
						region:'center',
						frame:true,
						border:true,
						html:'<div id="googleMapEdit" style="position:relative;top:0px;left:0px;display:block;width:460px;height:360px;"></div>'
					},{
						region:'east',
						layout: 'form',
						frame:true,
						border:false,
						labelWidth:70,
						width:290,
						items:[
							 new Ext.form.TextField({fieldLabel	 : "Lattitude",		name : "contact_lat",	id:'lattitude',anchor:'100%',value:'46.9502622421856'})
							,new Ext.form.TextField({fieldLabel	 : "Longitude",		name : "contact_long",	id:'longitude',anchor:'100%',value:'2.6806640625'})
							,new Ext.form.TriggerField({fieldLabel:'geolocaliser',anchor:'100%',triggerClass: 'x-form-search-trigger',
						    	onTriggerClick: function() {
							    	showAddress(this.getValue());
						    	}
					    	})
						]
					}
			    ]
			});
	    	
			this.form_googlemap.on('show',function(){
				if(Ext.chewingCom.getFormAgendacontact.googlemapinit==false){
					Ext.chewingCom.getFormAgendacontact.googlemapinit=true;
					Ext.chewingCom.getFormAgendacontact.initGooglemap();
				}
			});
			
			this.form = new Ext.form.FormPanel({
			    labelWidth:70,
			    frame:true,
			    id:this.formId,
				layout: 'form',
				border:false,
				autoScroll:true,
				waitMsgTarget: true,
		        reader : new Ext.data.XmlReader({
		            record : 'contact',
		            success: '@success'
		        }, [
		        	 {name: 'action', 				mapping:'action',				type:'string'}
		        	,{name: 'id_contact', 			mapping:'id_contact',			type:'string'}
					,{name: 'contact_titre', 		mapping:'contact_titre',		type:'string'}
					,{name: 'contact_nom', 			mapping:'contact_nom',			type:'string'}
					,{name: 'contact_prenom', 		mapping:'contact_prenom',		type:'string'}
					,{name: 'contact_adresse', 		mapping:'contact_adresse',		type:'string'}
					,{name: 'contact_cp', 			mapping:'contact_cp',			type:'string'}
					,{name: 'contact_ville', 		mapping:'contact_ville',		type:'string'}
					,{name: 'contact_quartier', 	mapping:'contact_quartier',		type:'string'}
					,{name: 'contact_tel', 			mapping:'contact_tel',			type:'string'}
					,{name: 'contact_fax', 			mapping:'contact_fax',			type:'string'}
					,{name: 'contact_email', 		mapping:'contact_email',		type:'string'}
					,{name: 'contact_url', 			mapping:'contact_url',			type:'string'}
					,{name: 'contact_infopratique', mapping:'contact_infopratique',	type:'string'}
					,{name: 'contact_lat', 			mapping:'contact_lat',			type:'string'}
					,{name: 'contact_long', 		mapping:'contact_long',			type:'string'}
		        ]),
			    items: {
			        xtype:'tabpanel',
			        activeTab: 0,
			        height:415,
			        id:'editTab',
			        deferredRender:false,
			        defaults:{autoHeight:true, bodyStyle:'padding:10px'}, 
				    defaults:{bodyStyle:'padding:10px'}, 
					items:[
						 this.form_identity, this.form_googlemap
					]
				}
			});
			
			
			 this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getFormAgendacontact.afterLoad(form,action);
		            }
		        }
		    });
			return this.form;
		
		},
		
		//------------------------------------------------------------
		// Window
		//------------------------------------------------------------
		
		display:function(){
			this.id=null;
			Ext.chewingCom.StartEditing();
			if(Ext.getCmp('winformedit')){
				this.win.show();
			}else{
				this.win = MyDesktop.getDesktop().createWindow({
				    title: this.title,
				    id:this.winId,
				    iconCls:this.iconCls,
				   	width:800, 
				   	height:500 ,
				    resizable:false,
				    maximizable:false,
				    minimizable:false,
				    draggable:false,
				    buttonAlign:'center',
				    closeAction:'close',
				    close:function(){ Ext.chewingCom.StopEditing(this); },
				    modal:true,
				    layout:'fit',
				    items:this.getForm(),
				    buttons: [{
				        text:'Enregistrer',
				        handler:function(){
				        
					       var objSave = Ext.chewingCom.getFormAgendacontact.form.getForm().getValues();
					       
							Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
							
					        Ext.Ajax.request({
					        	url:'os/plugins/modules/agendacontacts/action.php', 
								params:objSave,
								success: function(e) {
									
			 						Ext.chewingCom.progressbar.hide();
			 						Ext.getCmp('gridagendacontacts').store.reload();
					        		Ext.chewingCom.getFormAgendacontact.closeWin();
			 						
								}
							});
				        }
		        	},{
				        text: 'Annuler',
				        handler:function(){
				        	Ext.chewingCom.getFormAgendacontact.closeWin();
				        }
					}]
				}).show();
			}
		}
	};
	
}
Ext.chewingCom.getFormAgendacontact.googlemapinit=false;
Ext.chewingCom.getFormAgendacontact.display();
<?php if(isset($_GET['id'])){ ?>
	Ext.chewingCom.getFormAgendacontact.load(<?php print $_GET['id']; ?>);	
<?php } ?>	