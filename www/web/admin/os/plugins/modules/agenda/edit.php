<?php
	require_once("../../../php/const.php");
?>
var getContact = function (){
	return <?php
	$contacts = array();
	$res = mysql_query("SELECT contact_titre,contact_nom,contact_prenom,id_contact FROM contacts ORDER BY contact_titre,contact_nom,contact_prenom ASC");
	while($row = mysql_fetch_object($res)){
		$contacts[]=array($row->id_contact,$row->contact_titre.", ".$row->contact_nom." ".$row->contact_prenom);
	}
	print json_encode($contacts);
	?>
}
if(typeof Ext.chewingCom.getFormAgenda!='object'){
	
	
	Ext.chewingCom.getFormAgenda = {
		win:null,
		id:null,
		winId:'winFormAgendaedit',
		formId:'formEditagenda',
		iconCls:'agenda',
		title: 'Agenda - Evenement',
		form:null,
		load:function(id){
			this.id=id;
			Ext.getCmp(this.formId).load({url:'os/plugins/modules/agenda/data.php?id_agenda='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(form,action){
	    	
		},		
		getForm:function(){
			
			var image 			= Ext.chewingCom.btnFileBrowser("agenda_thumb","Image","",'jpg,png,gif');
			var thumb			= image.field;
			var thumb_preview	= image.preview;
			
			var fichier 		= Ext.chewingCom.btnFileBrowser("agenda_file","Fichier","",'*');
			var file			= fichier.field;
			var file_preview	= fichier.preview;
			
			this.form_identity = new Ext.Panel({
			    frame:false,
			    border:false,
			    title:'Detail',
				layout: 'border',
			    items: [
			    	{
			    		region:'center',
			    		layout: 'form',
			    		frame:true,
			    		//labelWidth:70,
			    		items:[
					    	 new Ext.form.Hidden({name : "action",value:"add"})
				    		,new Ext.form.Hidden({name : "id_agenda"})
				    		,new Ext.form.Checkbox ({fieldLabel	: "En ligne",id:'agenda_online',		name : "agenda_online"	   			 })
							,new Ext.form.TextField({fieldLabel	 : "Titre",	name : "agenda_titre",	anchor:'100%'})
							
							,new Ext.form.DateField({fieldLabel	 : "Date de debut",	name : "agenda_debut",	format:'Y-m-d'})
							,new Ext.form.TextField({fieldLabel	 : "Heure de debut",	name : "agenda_debutH",	width:30,maxLength:2,value:'00'})
							,new Ext.form.TextField({fieldLabel	 : "Minute de debut",	name : "agenda_debutM",	width:30,maxLength:2,value:'00'})
							
							,new Ext.form.DateField({fieldLabel  : "Date de fin",	name : "agenda_fin",format:'Y-m-d'})
							,new Ext.form.TextField({fieldLabel	 : "Heure de fin",	name : "agenda_finH",	width:30,maxLength:2,value:'00'})
							,new Ext.form.TextField({fieldLabel	 : "Minute de fin",	name : "agenda_fintM",	width:30,maxLength:2,value:'00'})
						
							,new Ext.form.ComboBox({fieldLabel:'Type',name:'agenda_type',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
								,anchor:'100%'
								,triggerClass: 'x-form-search-trigger'
							    ,store:new Ext.data.Store({
								    proxy: new Ext.data.ScriptTagProxy({
								        url: "os/plugins/modules/agenda/getvalues.php?field=agenda_type" 
								    }),
								   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
							    })
							})
							,new Ext.form.TextField({fieldLabel	 : "Mots clef",		name : "agenda_tags",	anchor:'100%'})
							 
						]
					},{
			    		region:'east',
			    		width:400,
			    		layout: 'form',
			    		frame:true,
			    		items:[ 
							 new Ext.form.TextArea({fieldLabel	 : "Chapeau",		name : "agenda_chapeau",	anchor:'100%',height:130})
							 ,new Ext.form.TextField({fieldLabel	 : "Lien externe",	name : "agenda_linkext",	anchor:'100%',value:'http://'})
							 ,new Ext.form.TextField({fieldLabel	 : "Lien interne",	name : "agenda_linkint",	anchor:'100%',value:'http://'})
							
							,thumb
							,thumb_preview
							,new Ext.Button({
								style:'margin-left:130px;margin-bottom:10px;',
								text:'recadrer',
								handler:function(){
									Ext.chewingCom.editWindowCrop(Ext.getCmp('id_agenda_thumb'),{w:64,h:64});
								}
							})
							,file
							,file_preview
							
							
						 
			    		]
			    	}
			    ]
			});
			
			var editorhtml = new Ext.form.TextArea({fieldLabel	 : "Texte",		name : "agenda_texte",	anchor:'100%',height:430});
			
			editorhtml.on('focus',function(){
				Ext.chewingCom.focushtmleditorimage(this);
			});
			
			this.form_texte = new Ext.Panel({
			    frame:true,
			    border:true,
			    title:'Texte',
				layout: 'fit',
				labelWidth:130,
			    items: editorhtml
			});
			
			this.form_contacts = new Ext.Panel({
			    frame:true,
			    border:true,
			    title:'Contacts',
				layout: 'fit',
				labelWidth:130,
			    items: new Ext.ux.Multiselect({
					name              :  'id_contact_fk',
					fieldLabel        :  'Selectionnez un ou plusieurs contacts',
					dataFields        :  ['code', 'desc'], 
					data              :  getContact(),
					valueField        :  'code',
					displayField      :  'desc',
					width             :  740,
					height            :  440,
					allowBlank        :  true
				})
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
		            record : 'agenda',
		            success: '@success'
		        }, [
		        	 {name: 'action', 				mapping:'action',				type:'string'}
		        	,{name: 'id_agenda', 			mapping:'id_agenda',			type:'string'}
					,{name: 'agenda_titre', 		mapping:'agenda_titre',			type:'string'}
					,{name: 'agenda_texte', 		mapping:'agenda_texte',			type:'string'}
					,{name: 'agenda_type', 			mapping:'agenda_type',			type:'string'}
					,{name: 'agenda_debut', 		mapping:'agenda_debut',			type:'string'}
					,{name: 'agenda_debutH', 		mapping:'agenda_debutH',		type:'string'}
					,{name: 'agenda_debutM', 		mapping:'agenda_debutM',		type:'string'}
					,{name: 'agenda_fin', 			mapping:'agenda_fin',			type:'string'}
					,{name: 'agenda_finH', 			mapping:'agenda_finH',			type:'string'}
					,{name: 'agenda_finM', 			mapping:'agenda_finM',			type:'string'}
					,{name: 'agenda_thumb', 		mapping:'agenda_thumb',			type:'string'}
					,{name: 'agenda_chapeau', 		mapping:'agenda_chapeau',		type:'string'}
					,{name: 'agenda_file', 			mapping:'agenda_file',			type:'string'}
					,{name: 'agenda_linkext', 		mapping:'agenda_linkext',		type:'string'}
					,{name: 'agenda_linkint', 		mapping:'agenda_linkint',		type:'string'}
					,{name: 'agenda_tags', 			mapping:'agenda_tags',			type:'string'}
					,{name: 'agenda_online', 		mapping:'agenda_online',		type:'string'}
					,{name: 'id_contact_fk', 		mapping:'id_contact_fk',		type:'string'}
		        ]),
			    items: {
			        xtype:'tabpanel',
			        activeTab: 0,
			        height:515,
			        id:'editTab',
			        deferredRender:false,
			        defaults:{autoHeight:true, bodyStyle:'padding:10px'}, 
				    defaults:{bodyStyle:'padding:10px'}, 
					items:[
						 this.form_identity,this.form_texte,this.form_contacts
					]
				}
			});
			
			
			 this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getFormAgenda.afterLoad(form,action);
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
				   	height:600 ,
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
				        
					       var objSave = Ext.chewingCom.getFormAgenda.form.getForm().getValues();
					       objSave.agenda_online=Ext.getCmp('agenda_online').checked;
					       
							Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
							
					        Ext.Ajax.request({
					        	url:'os/plugins/modules/agenda/action.php', 
								params:objSave,
								success: function(e) {
									
			 						Ext.chewingCom.progressbar.hide();
			 						Ext.getCmp('agendagrid').store.reload();
					        		Ext.chewingCom.getFormAgenda.closeWin();
			 						
								}
							});
							
				        }
		        	},{
				        text: 'Annuler',
				        handler:function(){
				        	Ext.chewingCom.getFormAgenda.closeWin();
				        }
					}]
				}).show();
			}
		}
	};
	
}
Ext.chewingCom.getFormAgenda.googlemapinit=false;
Ext.chewingCom.getFormAgenda.display();
<?php if(isset($_GET['id_agenda'])){ ?>
	Ext.chewingCom.getFormAgenda.load(<?php print $_GET['id_agenda']; ?>);	
<?php } ?>	