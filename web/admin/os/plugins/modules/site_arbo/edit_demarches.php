<?php
	require_once("../../../php/const.php");
?>

if(typeof Ext.chewingCom.getFormDemarches!='object ***'){
	
	
	Ext.chewingCom.getFormDemarches = {
		win:null,
		id:null,
		winId:'winFormDemarchesedit',
		formId:'formEditLieninterne',
		iconCls:'demarches',
		title: 'Liens interne',
		loadedXml:false,
		selection:[],
		form:null,
		load:function(id){
			this.id=id;
			this.form.load({url:'admin/os/plugins/modules/site_arbo/data_demarches.php?id='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(a,b,c){
 			this.loadedXml=true;
 			
 			if(a.result.data.demarches_ids!=0 && a.result.data.demarches_ids!=""){
	 			var ids = a.result.data.demarches_ids.split(',');
	 			var currentscope = this;
	 			setTimeout(function(){
		 			
		 			for(i=0;i<ids.length;i++){
			 			var node = currentscope.tree.getNodeById("page-"+ids[i]);
			 			if(node) node.ui.checkbox.checked=true;
		 			}
		 			
	 			},800);
 			}
		},	
		
		deSelectAll:function(node){
			node.eachChild( function(o){
				if(o.ui.checkbox.checked==true){
					o.ui.checkbox.checked=false;
				}
				Ext.chewingCom.getFormDemarches.deSelectAll(o);
			});
		},
		
		
		getSelections:function(node,init){
			if(init==true){
				this.selection = new Array();
			}
			node.eachChild( function(o){
				if(o.ui.checkbox.checked==true){
					Ext.chewingCom.getFormDemarches.selection.push(o.attributes.id_page);
				}
				Ext.chewingCom.getFormDemarches.getSelections(o,false);
			});
			return this.selection;
		},
		getTree:function(){
			Ext.chewingCom.getFormDemarches.xmlLoader = Ext.extend(Ext.ux.XmlTreeLoader, {
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
			    ,loader: new Ext.chewingCom.getFormDemarches.xmlLoader({
		            dataUrl:'admin/os/plugins/modules/site_arbo/xml-tree-data.xml.php'
		        })
			});	
			
			return this.tree;
		},
		
		getForm:function(){
			this.form = new Ext.form.FormPanel({
			    frame:false,
			    border:false,
				layout: 'fit',
				reader : new Ext.data.XmlReader({
		            record : 'page',
		            success: '@success'
		        }, [
		        	 {name: 'action', 				mapping:'action',				type:'string'}
		        	,{name: 'id_page', 				mapping:'id_page',				type:'string'}
		            ,{name: 'demarches_ids', 		mapping:'demarches_ids',		type:'string'}
		        ]),
			    items: [
			    	{
			    		region:'center',
			    		layout: 'form',
			    		frame:true,
			    		items:[
					    	 new Ext.form.Hidden({name : "action",value:"update"})
				    		,new Ext.form.Hidden({name : "id_page"})
				    		,new Ext.form.Hidden({name : "demarches_ids"})
						]
					}
			    ]
			});
			
			this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getFormDemarches.afterLoad(action);
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
			this.win = new Ext.Window({
			    title: this.title,
			    id:this.winId,
			   	width:400, 
			   	height:600 ,
			    resizable:false,
			    maximizable:false,
			    minimizable:false,
			    draggable:false,
			    buttonAlign:'center',
			    modal:true,
			    layout:'fit',
			    items:[this.getTree(),this.getForm()],
			    tbar:[
			    	'->',
			    	{
				    	text:'Tout d&eacute;-s&eacute;l&eacute;ctionner',
				    	handler:function(){
					    	Ext.chewingCom.getFormDemarches.deSelectAll(Ext.chewingCom.getFormDemarches.tree.getRootNode());
				    	}
				    }
			    ],
			    buttons: [{
			        text:'Enregistrer',
			        handler:function(){
				       var objSave 				= Ext.chewingCom.getFormDemarches.form.getForm().getValues();
				       	objSave.demarches_ids 	= Ext.chewingCom.getFormDemarches.getSelections(Ext.chewingCom.getFormDemarches.tree.getRootNode(),true);
						objSave.demarches_ids 	= objSave.demarches_ids.join(",");
						
						Ext.chewingCom.progressbar = Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				        Ext.Ajax.request({
				        	url:'admin/os/plugins/modules/site_arbo/action_demarches.php', 
							params:objSave,
							success: function(e) {
								Ext.chewingCom.progressbar.hide();
								Ext.getCmp(Ext.chewingCom.getFormDemarches.winId).close();
		 						document.location.reload();
							}
						});
			        }
	        	},{
			        text: 'Annuler',
			        handler:function(){
			        	Ext.chewingCom.getFormDemarches.closeWin();
			        }
				}]
			}).show();
			
			
			
		}
	};
}

Ext.chewingCom.getFormDemarches.display();
<?php if(isset($_GET['id'])){ ?>
	Ext.chewingCom.getFormDemarches.load(<?php print $_GET['id']; ?>);	
<?php } ?>	
