<?php
	require_once("../../../php/const.php");
?>

if(typeof Ext.chewingCom.getFormInfoPratique!='object ***'){
	
	
	Ext.chewingCom.getFormInfoPratique = {
		win:null,
		id:null,
		winId:'winFormInfopratiqueedit',
		formId:'formEditInfopratique',
		iconCls:'infopratique',
		title: 'Infopratique',
		loadedXml:false,
		selection:[],
		form:null,
		load:function(id){
			this.id=id;
			this.form.load({url:'admin/os/plugins/modules/site_arbo/data_infopratique.php?id='+this.id, waitMsg:'Chargement...'});
		},
		closeWin:function(){
			Ext.getCmp(this.winId).close();
		},
		afterLoad:function(a,b,c){
 			this.loadedXml=true;
 			if(a.result.data.infopratique_ids!=0 && a.result.data.infopratique_ids!=""){
	 			var ids = a.result.data.infopratique_ids.split(',');
	 			var currentscope = this;
	 			setTimeout(function(){
		 			
		 			for(i=0;i<ids.length;i++){
			 			var node = currentscope.tree.getNodeById("page-"+ids[i]);
			 			if(node) node.ui.checkbox.checked=true;
		 			}
		 			
	 			},800);
 			}
		},	
		getSelections:function(node,init){
			if(init==true){
				this.selection = new Array();
			}
			node.eachChild( function(o){
				if(o.ui.checkbox.checked==true){
					Ext.chewingCom.getFormInfoPratique.selection.push(o.attributes.id_page);
				}
				Ext.chewingCom.getFormInfoPratique.getSelections(o,false);
			});
			return this.selection;
		},
		
		deSelectAll:function(node){
			node.eachChild( function(o){
				if(o.ui.checkbox.checked==true){
					o.ui.checkbox.checked=false;
				}
				Ext.chewingCom.getFormInfoPratique.deSelectAll(o);
			});
		},
		
		getTree:function(){
			Ext.chewingCom.getFormInfoPratique.xmlLoader = Ext.extend(Ext.ux.XmlTreeLoader, {
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
			    ,loader: new Ext.chewingCom.getFormInfoPratique.xmlLoader({
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
		            ,{name: 'infopratique_ids', 	mapping:'infopratique_ids',		type:'string'}
		        ]),
			    items: [
			    	{
			    		region:'center',
			    		layout: 'form',
			    		frame:true,
			    		items:[
					    	 new Ext.form.Hidden({name : "action",value:"update"})
				    		,new Ext.form.Hidden({name : "id_page"})
				    		,new Ext.form.Hidden({name : "infopratique_ids"})
						]
					}
			    ]
			});
			
			this.form.on({
			 	actioncomplete: function(form, action){
		            if(action.type == 'load'){
						Ext.chewingCom.getFormInfoPratique.afterLoad(action);
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
					    	Ext.chewingCom.getFormInfoPratique.deSelectAll(Ext.chewingCom.getFormInfoPratique.tree.getRootNode());
				    	}
				    }
			    ],
			    buttons: [{
			        text:'Enregistrer',
			        handler:function(){
				       var objSave 					= Ext.chewingCom.getFormInfoPratique.form.getForm().getValues();
				       	objSave.infopratique_ids 	= Ext.chewingCom.getFormInfoPratique.getSelections(Ext.chewingCom.getFormInfoPratique.tree.getRootNode(),true);
						objSave.infopratique_ids 	= objSave.infopratique_ids.join(",");
				       
						Ext.chewingCom.progressbar = Ext.MessageBox.wait('En cours...','Sauvegarde', {});
				        Ext.Ajax.request({
				        	url:'admin/os/plugins/modules/site_arbo/action_infopratique.php', 
							params:objSave,
							success: function(e) {
								Ext.chewingCom.progressbar.hide();
								Ext.getCmp(Ext.chewingCom.getFormInfoPratique.winId).close();
		 						document.location.reload();
							}
						});
			        }
	        	},{
			        text: 'Annuler',
			        handler:function(){
			        	Ext.chewingCom.getFormInfoPratique.closeWin();
			        }
				}]
			}).show();
			
			
			
		}
	};
}

Ext.chewingCom.getFormInfoPratique.display();
<?php if(isset($_GET['id'])){ ?>
	Ext.chewingCom.getFormInfoPratique.load(<?php print $_GET['id']; ?>);	
<?php } ?>	
