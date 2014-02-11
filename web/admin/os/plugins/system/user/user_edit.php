<?php
	require_once("../../../php/const.php");
	
	$sqlquery="SELECT * FROM cms_pays ORDER BY pays_name";
	$sqlres=mysql_query($sqlquery);
	$allLangueItems=array();
	while($row=mysql_fetch_array($sqlres)){ $allLangueItems[]=array($row['pays_libelle'],($row['pays_name']),$row['pays_class']); }
	$allLangueItems=json_encode($allLangueItems);
	
	print "Ext.chewingCom.getLangue=function(){\n";
	print "\t\treturn ".$allLangueItems.";\n";
	print "}\n\n";
	
?>

if(typeof Ext.chewingCom.getEditUser!='object ***'){
	Ext.chewingCom.getEditUser = {
		win:null,
		id:null,
		winId:'winformedit',
		formId:'formEditUser',
		iconCls:'user',
		loadedXml:false,
		title: 'G&eacute;rer les utilisateurs',
		form:null,
		polygones:null,
		load:function(id){
			this.id=id;
			Ext.getCmp(this.formId).load({url:'os/plugins/system/user/user_data.php?id='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(a,b,c){
			this.loadedXml=true;
			if(a.result.data.id_arbo_fk!=0){
				var node = this.tree.getNodeById("page-"+a.result.data.id_arbo_fk);
				if(node){
					node.ui.checkbox.checked=true;
					Ext.chewingCom.getEditUser.lastchecked=node;
				}
			}
		},
		getForm:function(){
			this.form = new Ext.form.FormPanel({
			    labelWidth: 130,
			    frame:true,
			    id:this.formId,
				layout: 'form',
				border:false,
				autoScroll:true,
				region:'center',
				waitMsgTarget: true,
		        reader : new Ext.data.XmlReader({
		            record : 'user',
		            success: '@success'
		        }, [
		        	 {name: 'action', 				mapping:'action',				type:'string'}
		        	,{name: 'id', 					mapping:'id',					type:'string'}
		            ,{name: 'admin', 				mapping:'admin',				type:'string'}
		            ,{name: 'nom', 					mapping:'nom',					type:'string'}
		            ,{name: 'prenom', 				mapping:'prenom',				type:'string'}
		            ,{name: 'login',	 			mapping:'login',				type:'string'}
		            ,{name: 'pass', 				mapping:'pass',					type:'string'}
		            ,{name: 'langue', 				mapping:'langue',				type:'string'}
		            ,{name: 'id_arbo_fk', 			mapping:'id_arbo_fk',			type:'string'}
		        ]),
			    items: [
		    		 new Ext.form.Hidden({name : "action",value:"add"})
		    		,new Ext.form.Hidden({name : "id"})
		    		,new Ext.form.Hidden({name : "id_arbo_fk",value:0})
                    ,new Ext.form.ComboBox({
						 id: 'admin'
				        ,name: 'admin'
				        ,fieldLabel	: "Type"
				        ,store: new Ext.data.SimpleStore({
					            fields: ['value', 'text'],
					            data: [<?php if($_SESSION[sessionName]['user']['admin']=='sadmin'){?>['sadmin','Super admin'],<?php }?>['admin','Admin'],['user','Utilisateur'],['redacteur','Redacteur'],['moderateur','Moderateur'],['videos','Videos']]
					    })
					    ,editable:false
				        ,valueField: 'value'
					    ,displayField: 'text'
				        ,typeAhead: true
				        ,mode: 'local'
				        ,forceSelection: true
				        ,triggerAction: 'all'
				        ,emptyText:'Selectionner'
				        ,selectOnFocus:true
				    })
					,new Ext.form.TextField({fieldLabel: "Nom",name: "nom",anchor:'95%'})
					,new Ext.form.TextField({fieldLabel: "Pr&eacute;nom",name: "prenom",anchor:'95%'})
					,new Ext.form.TextField({fieldLabel: "Identifiant",name: "login",anchor:'95%'})
					,new Ext.form.TextField({fieldLabel: "Mot de passe",name: "pass",anchor:'95%'})
					,{
					     xtype:'iconcombo'
					    ,id:'langueIcon-id'
					    ,editable:false
					    ,name:'langue'
					    ,fieldLabel:'Langue'
					    ,store: new Ext.data.SimpleStore({
					            fields: ['countryCode', 'countryName', 'countryFlag'],
					            data: []
					    })
					    ,valueField: 'countryCode'
					    ,displayField: 'countryName'
					    ,iconClsField: 'countryFlag'
                        ,typeAhead: true
                        ,mode: 'local'
                        ,triggerAction: 'all'
                        ,emptyText:'Selectionner'
                        ,selectOnFocus:true
                        ,forceSelection:true
					}
			    ]
			});
			
			 this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getEditUser.afterLoad(action);
		            }
		        }
		    });
			return this.form;
		},
		getTree:function(){
			Ext.chewingCom.getEditUser.xmlLoader = Ext.extend(Ext.ux.XmlTreeLoader, {
			    processAttributes : function(attr){
					attr.text=attr.titre;			    
				    attr.expanded=true;
				    attr.checked=false;
			    }
			});
			
			
			this.tree = new Ext.tree.TreePanel({
				 loadMask:true
				,region      : 'east'
			    ,autoScroll  : true
			    ,width       : 400
			    ,loader:new Ext.tree.TreeLoader({
					baseAttrs: {checked: false}
				})
				,collapsible      :false
			    ,animCollapse     :false
				,enableDD         :false
				,containerScroll  :true
				,rootVisible	  :false
			    ,root:new Ext.tree.AsyncTreeNode({
			         leaf		  :false
			        ,id			  :0
			        ,text		  :'Categories pour restriction'
			        ,checked	  :false
			        ,expanded	  :true
			    })
			    ,loader: new Ext.chewingCom.getEditUser.xmlLoader({
		            dataUrl:'os/plugins/modules/site_arbo/xml-tree-data.xml.php'
		        })
			});	
			/*
			this.tree.on('load',function(){
				if(Ext.chewingCom.getEditUser.loadedXml==true){
					console.debug(Ext.chewingCom.getEditUser.form.getForm().getValues());
				}
			});
			*/
			return this.tree;
		},
		
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
				   	height:450 ,
				    resizable:false,
				    draggable:false,
				    buttonAlign:'center',
				    closeAction:'close',
				    close:function(){ Ext.chewingCom.StopEditing(this); },
				    modal:true,
				    layout:'border',
				    items:[
				    	this.getForm(),
				    	this.getTree()
				    ],
				    buttons: [{
				        text:'Enregistrer',
				        handler:function(){
					        var objSave 		= Ext.chewingCom.getEditUser.form.getForm().getValues();
					        objSave.admin		= Ext.getCmp('admin').getValue();
					        objSave.langue 		= Ext.getCmp('langueIcon-id').getValue();
					        objSave.id_arbo_fk	= (Ext.chewingCom.getEditUser.tree.getChecked()[0]) ? Ext.chewingCom.getEditUser.tree.getChecked()[0].attributes.id_page : "0";
					        
							Ext.chewingCom.progressbar=Ext.MessageBox.wait('Veuillez-patienter...','Enregistrement', {});
					        Ext.Ajax.request({
					        	url:'os/plugins/system/user/user_action.php', 
								params:objSave,
								success: function(e) {
			 						Ext.chewingCom.progressbar.hide();
			 						if(Ext.getCmp('gridUser'))
										Ext.getCmp('gridUser').store.reload();
				        			Ext.chewingCom.getEditUser.closeWin();
								}
							});
				        }
		        	},{
				        text: 'Annuler',
				        handler:function(){
				        	Ext.chewingCom.getEditUser.closeWin();
				        }
					}]
				}).show();
				
				
				this.tree.addListener('checkchange', function (node, event){
					var s = node.ui.checkbox.checked;
					if(Ext.chewingCom.getEditUser.lastchecked && Ext.chewingCom.getEditUser.lastchecked.ui.checkbox){
						Ext.chewingCom.getEditUser.lastchecked.ui.checkbox.checked=false;
					}else if(Ext.chewingCom.getEditUser.tree.getChecked()[0] && Ext.chewingCom.getEditUser.tree.getChecked()[0].ui.checkbox){
						Ext.chewingCom.getEditUser.tree.getChecked()[0].ui.checkbox.checked=false;
					}
					node.ui.checkbox.checked=s;
					Ext.chewingCom.getEditUser.lastchecked=node;
				});
				
			}
		}
	};
}

Ext.chewingCom.getEditUser.display();

Ext.getCmp('langueIcon-id').store.loadData(Ext.chewingCom.getLangue());
<?php 
	if(isset($_GET['id'])){ 
		print 'Ext.chewingCom.getEditUser.load("'.$_GET['id'].'");';
	}
?>
	

	

			