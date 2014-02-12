<?php
	require_once("../../../php/const.php");
?>

if(typeof Ext.chewingCom.getFormLienInterne!='object ***'){
	
	
	Ext.chewingCom.getFormLienInterne = {
		win:null,
		id:null,
		winId:'winFormLienInterneedit',
		formId:'formEditLieninterne',
		iconCls:'lieninterne',
		title: 'Liens interne',
		loadedXml:false,
		selection:[],
		form:null,
		load:function(id){
			this.id=id;
			this.form.load({url:'admin/os/plugins/modules/site_arbo/data_lieninterne.php?id='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(a,b,c){
 			this.loadedXml=true;
 			if(a.result.data.lieninterne_ids!=0 && a.result.data.lieninterne_ids!=""){
	 			var ids = a.result.data.lieninterne_ids.split(',');
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
				Ext.chewingCom.getFormLienInterne.deSelectAll(o);
			});
		},
		
		
		getSelections:function(node,init){
			if(init==true){
				this.selection = new Array();
			}
			node.eachChild( function(o){
				if(o.ui.checkbox.checked==true){
					Ext.chewingCom.getFormLienInterne.selection.push(o.attributes.id_page);
				}
				Ext.chewingCom.getFormLienInterne.getSelections(o,false);
			});
			return this.selection;
		},
		getTree:function(){
			Ext.chewingCom.getFormLienInterne.xmlLoader = Ext.extend(Ext.ux.XmlTreeLoader, {
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
			    ,loader: new Ext.chewingCom.getFormLienInterne.xmlLoader({
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
		            ,{name: 'lieninterne_ids', 		mapping:'lieninterne_ids',		type:'string'}
		        ]),
			    items: [
			    	{
			    		region:'center',
			    		layout: 'form',
			    		frame:true,
			    		items:[
					    	 new Ext.form.Hidden({name : "action",value:"update"})
				    		,new Ext.form.Hidden({name : "id_page"})
				    		,new Ext.form.Hidden({name : "lieninterne_ids"})
						]
					}
			    ]
			});
			
			this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getFormLienInterne.afterLoad(action);
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
					    	Ext.chewingCom.getFormLienInterne.deSelectAll(Ext.chewingCom.getFormLienInterne.tree.getRootNode());
				    	}
				    }
			    ],
			    buttons: [{
			        text:'Enregistrer',
			        handler:function(){
				       var objSave 					= Ext.chewingCom.getFormLienInterne.form.getForm().getValues();
				       	objSave.lieninterne_ids 	= Ext.chewingCom.getFormLienInterne.getSelections(Ext.chewingCom.getFormLienInterne.tree.getRootNode(),true);
						objSave.lieninterne_ids 	= objSave.lieninterne_ids.join(",");
				       
						Ext.chewingCom.progressbar = Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				        Ext.Ajax.request({
				        	url:'admin/os/plugins/modules/site_arbo/action_lieninterne.php', 
							params:objSave,
							success: function(e) {
								Ext.chewingCom.progressbar.hide();
								Ext.getCmp(Ext.chewingCom.getFormLienInterne.winId).close();
		 						document.location.reload();
							}
						});
			        }
	        	},{
			        text: 'Annuler',
			        handler:function(){
			        	Ext.chewingCom.getFormLienInterne.closeWin();
			        }
				}]
			}).show();
			
			
			
		}
	};
}

Ext.chewingCom.getFormLienInterne.display();
<?php if(isset($_GET['id'])){ ?>
	Ext.chewingCom.getFormLienInterne.load(<?php print $_GET['id']; ?>);	
<?php } ?>	
