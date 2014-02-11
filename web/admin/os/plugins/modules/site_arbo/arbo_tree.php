<?php
	require_once("../../../php/const.php");
	$strPath='arbo';
	$langueModule = isset($_SESSION[sessionName]['user'][$strPath]['langue']) ? $_SESSION[sessionName]['user'][$strPath]['langue'] :  $_SESSION[sessionName]['user']['languecms'];
	
	if($_SESSION[sessionName]['user']['admin']!='admin' && $_SESSION[sessionName]['user']['admin']!='sadmin'){
		$id = $_SESSION[sessionName]['user']['id_arbo_fk'];
		print "var rootArboId = '".$id."';\n";
		
		$res = mysql_query("SELECT titre FROM site_page WHERE id_page=".$id." LIMIT 1");
		$row = mysql_fetch_object($res);
		print "var rootArboText = '".$row->titre."';\n";
	}else{
		print "var rootArboId = '0';\n";
		print "var rootArboText = 'Racine';\n";
	}	
	


?>

var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('wingrid<?php print $strPath;?>');
if(!currentwin){
	
	//-----------------------------------------------------------------------------------
	// MENU EDITION
	//-----------------------------------------------------------------------------------

	var menueditarbo = [
		{
			text:'Ajouter',iconCls:'add',
			handler:function(e){
				var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
				if(o){
					
					var params = o.attributes;
					params.action="addnode";
					Ext.Ajax.request({
			        	url:"os/plugins/modules/site_arbo/action.php", 
			        	params:params,
						success: function(e) {
							Ext.chewingCom.afterloadarbo=e.responseText;
							Ext.getCmp('tree-panel-arbo').root.reload();
						}
					});
					
				}else{
					 Ext.chewingCom.alertItemSelect();
				}
	        }
		},'-',{
			text:'Effacer',iconCls:'remove',
			handler:function(e){
				var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
				if(o){
				
					Ext.Msg.confirm('Confirmation', 'Voulez vous vraiment effacer ?', function(btn){
					    if (btn == 'yes'){
					        var params = o.attributes;
							params.action="deletenode";
							Ext.Ajax.request({
					        	url:"os/plugins/modules/site_arbo/action.php", 
					        	params:params,
								success: function(e) {
			                        o.remove();
								}
							});
					    }
					});
					
				}else{
					 Ext.chewingCom.alertItemSelect();
				}
	        }
		},'-',{
			text:'Editer',iconCls:'edit',
			handler:function(e){
				var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
				if(o){
					//Ext.chewingCom.LoadAndExecJS('os/plugins/modules/site_arbo/edit.php?id='+o.attributes.id_page);
					Ext.getDom('editframe').src='../pages/'+o.attributes.id_page+'-edition.html';
				}else{
					 Ext.chewingCom.alertItemSelect();
				}
	        }
		},'-',{
			text:'Options',
			menu:[
				{
					text:'Renommer',
					iconCls:'rename',
					handler:function(e){
						var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
						if(o){
							treeEditor.triggerEdit(o);
						}
					}
				},'-',{
					text:'Duppliquer',
					iconCls:'copy',
					handler:function(e){
						var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
						if(o){
					        var params = o.attributes;
							params.action="copynode";
							Ext.Ajax.request({
					        	url:"os/plugins/modules/site_arbo/action.php", 
					        	params:params,
								success: function(e) {
			                        Ext.getCmp('tree-panel-arbo').root.reload();
								}
							});
						}else{
							 Ext.chewingCom.alertItemSelect();
						}
			        }
				}
			]
		}
	];	
	var ctxMenuarbo = new Ext.menu.Menu({ items: menueditarbo});

	var contextEditarbo = function (node, e){ 
		if(node.attributes.tagName!='modele'){
			node.select();
			ctxMenuarbo.show(node.ui.getAnchor()); 
		}
	}

	//-----------------------------------------------------------------------------------
	// XMLTREELOADER
	//-----------------------------------------------------------------------------------
	
	Ext.app.Contentarbo = Ext.extend(Ext.ux.XmlTreeLoader, {
	    processAttributes : function(attr){
		    attr.draggable=false;
		    attr.text=attr.titre;
		    if(attr.tagName=="tableau"){
			    attr.text=attr.name;
			}else{
				attr.draggable=true;
		    }
		    attr.expanded = (attr.leaf == false && attr.open == true) ? true : false;
	    } 
	});
	
	//-----------------------------------------------------------------------------------
	// WINDOW
	//-----------------------------------------------------------------------------------
	Ext.chewingCom.afterloadarbo=null;
	var treearbo = new Ext.tree.TreePanel({
		
		tbar:menueditarbo,
        xtype: 'treepanel',
        id: 'tree-panel-arbo',
        margins: '2 2 0 2',
        autoScroll:true,
        rootVisible: true,
        enableDD:true,
        region:'west',
        width:300,
        root: new Ext.tree.AsyncTreeNode({
	      id:'page-'+rootArboId,
	      hasChildren:true,
	      draggable: false,
	      id_page:rootArboId,
	      text: rootArboText, 
	      expanded:true
	    }),
        loader: new Ext.app.Contentarbo({
            dataUrl:'os/plugins/modules/site_arbo/xml-tree-data.xml.php'
        }),
        listeners: {
            'render': function(tp){
                tp.getSelectionModel().on('selectionchange', function(tree, node){
                   //console.debug(node);
                })
            }
        },bbar:['->',{
			iconCls:'x-tbar-loading',
			text:'Recharger',
			handler:function(){
				Ext.getCmp('tree-panel-arbo').root.reload();
			}
		}]
    });

	treearbo.on('contextmenu', contextEditarbo);
	treearbo.on('load', function(){
		if(Ext.chewingCom.afterloadarbo!=null){
			(function(){
				Ext.getCmp('tree-panel-arbo').getNodeById(Ext.chewingCom.afterloadarbo).select();
				Ext.chewingCom.afterloadarbo=null;
			}).defer(300);
		}
	});    
    
	var treeEditor = new Ext.tree.TreeEditor(treearbo, {
		 id:'treeEditor',
		 allowBlank:false,
		 cancelOnEsc:true,
		 completeOnEnter:true,
		 ignoreNoChange:true,
		 selectOnFocus:true,
         blankText:'Text is required'
	});
	
    treeEditor.on('complete',function(a,b,c){
	    var nodeEdit = a.editNode.attributes;
	    if(nodeEdit.tagName=="tableau"){
		    return false;
		}else{    
		    Ext.Ajax.request({
	        	url:'os/plugins/modules/site_arbo/action.php',
				params:{id:nodeEdit.id,newname:b,action:'rename'}
			});
		}
    });

    treearbo.on('dblclick',function(o){
	    //var o = Ext.getCmp('tree-panel-arbo').getSelectionModel().selNode;
		if(o){
			//Ext.chewingCom.LoadAndExecJS('os/plugins/modules/site_arbo/edit.php?id='+o.attributes.id_page);
			Ext.getDom('editframe').src='../pages/'+o.attributes.id_page+'-edition.html';
		}else{
			 Ext.chewingCom.alertItemSelect();
		}
    });
    
	//-----------------------------------------------------------------------------------
	// DRAG N DROP TREE
	//-----------------------------------------------------------------------------------
	
	treearbo.on('beforenodedrop',function(o){
		var target 		= o.target.attributes;
		var node 		= o.dropNode.attributes;
		var parentNode 	= o.target.parentNode ? o.target.parentNode.attributes : false;
		
		var objParent   = null;
		var parent = null;
		
	 	if(o.point=="append"){
		 	parent=target.id;
		 	objParent=o.target;
	 	}else if(o.point=="below"){
		 	parent=parentNode.id;
		 	objParent=o.target.parentNode;
	 	}else if(o.point=="above" && parentNode){
		 	parent=parentNode.id;
		 	objParent=o.target.parentNode;
	 	}else{
		 	return false;
	 	}
		if(node){
			if(node.id_page==0){
				return false;
			}
	 	}else{
		 	return false;
	 	}
	});	
	
	function saveExpandNodeState(node,state){
		if(node.id.indexOf('page')!=-1 && node.id_page>0){
			Ext.Ajax.request({
	        	url:"os/plugins/modules/site_arbo/action.php",
				params:{
					 action:'saveexpand'
					,expanded:state
					,node:Ext.util.JSON.encode(node)
				}
			});		
		}
	}
	
	treearbo.on('expandnode',function(o){
 		var node 		= o.attributes;
 		saveExpandNodeState(node,true);
	});
	
	treearbo.on('collapsenode',function(o){
 		var node 		= o.attributes;
 		saveExpandNodeState(node,false);
	});
	
	treearbo.on('nodedrop',function(o){
		
		Ext.getCmp('tree-panel-arbo').getSelectionModel().clearSelections();
		
		var target 		= o.target.attributes;
		var node 		= o.dropNode.attributes;
		var parentNode 	=  o.target.parentNode ? o.target.parentNode.attributes : false;
		
		var objParent   = null;
		var parent = null;
		
	 	if(o.point=="append"){
		 	parent=target.id;
		 	objParent=o.target;
	 	}else if(o.point=="below" && parentNode){
		 	parent=parentNode.id;
		 	objParent=o.target.parentNode;
	 	}else if(o.point=="above"){
		 	parent=parentNode.id;
		 	objParent=o.target.parentNode;
	 	}else{
		 	return false;
	 	}		
	 	
		// changement de node detecté
		//if(o.dropNode.parentNode.attributes.id==objParent.attributes.id){}
		
		var neworder=new Array();
		
		for(x=0;x<objParent.childNodes.length;x++){
			var n = objParent.childNodes[x].attributes;
			v = n.id.split("-")[1]; 
			neworder.push(v);
		}
		Ext.Ajax.request({
        	url:"os/plugins/modules/site_arbo/action.php",
			params:{
					action:'savenodeorder'
					,nodeparent:Ext.util.JSON.encode(objParent.attributes)
					,node:Ext.util.JSON.encode(node)
					,'neworder[]':neworder
			},
			success:function(e){
				o.dropNode.select();
			}
		});		
	});	
	
	desktop.createWindow({
		title:"Pages du site",
		id:'wingrid<?php print $strPath;?>',
		width:800,
		height:600,
        maximized:true,
		iconCls:'icon-grid',
	    layout: 'border',
	    border:true,
		animCollapse:true,
		constrainHeader:true,
		
		items:[
			treearbo,
			new Ext.Panel({
				region:'center',
				html:'<iframe id="editframe" allowtransparency="false" backgroundcolor="white" marginwidth="0" marginheight="0" frameborder="no" border="0" src="os/plugins/modules/site_arbo/nopage.html" width="100%" height="100%"></iframe>'
			})
		]
	}).show();

}else{
	currentwin.show();
	currentwin.toFront();
}