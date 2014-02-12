if(typeof Ext.chewingCom.getEditLangue!='object'){
	Ext.chewingCom.getEditLangue = {
		win:null,
		id:null,
		winId:'winformeditlanguage',
		formId:'formEditLanguage',
		iconCls:'translate-module',
		title: 'Language',
		form:null,
		load:function(id){
			this.id=id;
			Ext.getCmp(this.formId).load({url:'os/plugins/system/langues/langues_data.php?id='+this.id, waitMsg:'Loading...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(){
		},
		getForm:function(){
			
			var _file_video1 			= Ext.chewingCom.btnFileBrowser("file_video1","Intro video 1","",'flv|avi|wmv|mov');
			var file_video1				= _file_video1.field;
			var file_video1_preview		= _file_video1.preview;
			
			
			var _file_video2 			= Ext.chewingCom.btnFileBrowser("file_video2","Intro video 2","",'flv|avi|wmv|mov');
			var file_video2				= _file_video2.field;
			var file_video2_preview		= _file_video2.preview;
			
			
			this.form = new Ext.form.FormPanel({
			    labelWidth: 130,
			    frame:true,
			    id:this.formId,
				layout: 'form',
				border:false,
				autoScroll:true,
				waitMsgTarget: true,
		        reader : new Ext.data.XmlReader({
		            record : 'pays',
		            success: '@success'
		        }, [
		        	 {name: 'action', 				mapping:'action',				type:'string'}
		        	,{name: 'pays_libelle', 		mapping:'pays_libelle',			type:'string'}
		            ,{name: 'pays_langue', 			mapping:'pays_langue',			type:'string'}
		            ,{name: 'pays_name', 			mapping:'pays_name',			type:'string'}
		            ,{name: 'id', 					mapping:'pays_libelle',			type:'string'}
		            ,{name: 'file_video1', 			mapping:'file_video1',			type:'string'}
		            ,{name: 'file_video2', 			mapping:'file_video2',			type:'string'}
		            ,{name: 'modele1_online', 		mapping:'modele1_online',		type:'string'}
		            ,{name: 'modele2_online', 		mapping:'modele2_online',		type:'string'}
		            ,{name: 'typo', 				mapping:'typo',					type:'string'}
		            ,{name: 'colors', 				mapping:'colors',				type:'string'}
		            ,{name: 'pays_domaine', 		mapping:'pays_domaine',			type:'string'}
		            ,{name: 'pays_directory', 		mapping:'pays_directory',		type:'string'}
		            
		        ]),
			    items: [
		    		 new Ext.form.Hidden({name : "action",value:"add"})
		    		,new Ext.form.Hidden({name : "id"})
					,new Ext.form.TextField({fieldLabel: "Libelle (ex:uk_en)",anchor:'95%',name : "pays_libelle"})
					,new Ext.form.TextField({fieldLabel: "Language",name: "pays_langue",anchor:'95%'})
					,new Ext.form.TextField({fieldLabel: "Country",name: "pays_name",anchor:'95%'})
					
					,new Ext.form.TextField({fieldLabel: "Domain",name: "pays_domaine",anchor:'95%'})
					,new Ext.form.TextField({fieldLabel: "Directory",name: "pays_directory",anchor:'95%'})
					
					,new Ext.form.ComboBox({
						 id: 'typo-list-id'
				        ,name: 'typo'
				        ,fieldLabel	: "Typo used"
				        ,store: new Ext.data.SimpleStore({
					            fields: ['value', 'text'],
					            data: [['latin','Latin'],['cyrillique','Cyrillique'],['asiatique','Asiatique'],['autres','Autres']]
					    })
					    ,editable:false
				        ,valueField: 'value'
					    ,displayField: 'text'
				        ,typeAhead: true
				        ,mode: 'local'
				        ,forceSelection: true
				        ,triggerAction: 'all'
				        ,emptyText:'Select...'
				        ,selectOnFocus:true
				    })					
					,new Ext.form.Checkbox({id:"modele1_online",fieldLabel	: "Model R",name : "modele1_online"})
					,new Ext.form.Checkbox({id:"modele2_online",fieldLabel	: "Model J",name : "modele2_online"})
					,file_video1
					,file_video1_preview
					,file_video2
					,file_video2_preview
					,{
						xtype:"multiselect",
						fieldLabel:"Colors",
						name:"colors",
						id:'colors',
						dataFields:["code", "desc"], 
						data:[[0,'Ash Beige'],[1,'Black Pepper'],[2,'White'],[3,'Extreme Blue'],[4,'Opaline Blue'],[5,'Blue Roy'],[6,'Mocha Brown'],[7,'Gray Cassiopeia'],[8,'Gray Eclipse'],[9,'Platinum Gray'],[10,'Black Pearl'],[11,'Red Dyna'],[12,'Green Vetiver']],
						valueField:"code",
						displayField:"desc",
						width:300,
						height:120,
						allowBlank:true,
						tbar:[
							{
								text:"clear",
								handler:function(){
									Ext.getCmp("colors").reset();
								}
							}
						]
					}
					
			    ]
			});
			 this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getEditLangue.afterLoad();
		            }
		        }
		    });
			return this.form;
		},
		display:function(){
		
			this.id=null;
			Ext.chewingCom.StartEditing();
			if(Ext.getCmp('winformeditlanguage')){
				this.win.show();
			}else{
				this.win = MyDesktop.getDesktop().createWindow({
				    title: this.title,
				    id:this.winId,
				    iconCls:this.iconCls,
				   	width:800, 
				   	height:600 ,
				    resizable:false,
				    draggable:false,
				    buttonAlign:'center',
				    closeAction:'close',
				    close:function(){ Ext.chewingCom.StopEditing(this); },
				    modal:true,
				    layout:'fit',
					items: this.getForm(),
				    buttons: [{
				        text:'Save',
				        handler:function(){
					        var objSave 			= Ext.chewingCom.getEditLangue.form.getForm().getValues();
					        objSave.typo 			= Ext.getCmp("typo-list-id").getValue();
					        objSave.modele1_online	= Ext.getCmp('modele1_online').checked;
					        objSave.modele2_online	= Ext.getCmp('modele2_online').checked;
					        
							Ext.chewingCom.progressbar=Ext.MessageBox.wait('Please wait...','Saving', {});
					        Ext.Ajax.request({
					        	url:'os/plugins/system/langues/langues_action.php', 
								params:objSave,
								success: function(e) {
			 						Ext.chewingCom.progressbar.hide();
			 						if(Ext.getCmp('gridLangues'))
										Ext.getCmp('gridLangues').store.reload();
				        			Ext.chewingCom.getEditLangue.closeWin();
								}
							});
				        }
		        	},{
				        text: 'Cancel',
				        handler:function(){
				        	Ext.chewingCom.getEditLangue.closeWin();
				        }
					}]
				}).show();
			}
		}
		
	};
}

Ext.chewingCom.getEditLangue.display();

<?php 
	if(isset($_GET['id'])){ 
		print 'Ext.chewingCom.getEditLangue.load("'.$_GET['id'].'");';
	}
?>
	

	

			