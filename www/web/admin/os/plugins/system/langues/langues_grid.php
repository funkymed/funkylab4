<?php
	require_once("../../../php/xmlparser.php");
	function listOption($directory){
		$tab=array();
		$dir = opendir($directory);
		
		while ($f = readdir($dir)) {
				if(is_dir($directory.$f)) {
					if ($f!="." && $f!=".."){
					$tab[]= $f;	
				}
			}
	  	}
		closedir($dir);
		sort($tab);
		return $tab;
	}

?>

var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('wingridLangues');
if(!currentwin){
		
	var storeLangues = new Ext.data.GroupingStore({
		id:'storeLangues',
		proxy:new Ext.data.HttpProxy({
			url:'os/plugins/system/langues/langues_datalist.php'
		}),
		reader:new Ext.data.JsonReader({
			totalProperty:'data.total',
			root:'data.results'
		}, Ext.data.Record.create([
			{name:'pays_libelle'}, 
			{name:'pays_langue'}, 
			{name:'pays_name'}, 
			{name:'pays_class'}, 
			{name:'edit_date'}, 
			{name:'edit_creation'}, 
			{name:'edit_user_fk'},
			{name:'typo'}
		])),
		sortInfo:{field:'pays_libelle', direction:'DESC'},
		remoteSort:true
		//,groupField:'pays_langue'

	});
	
	var gridLangues = new Ext.grid.GridPanel({
		id:'gridLangues',
		ds:storeLangues,
		columns:[
			{dataIndex:'pays_libelle', 		header:'Id',width:35,align:'center',sortable: true}, 
			{dataIndex:'pays_class', 		header:'Flag',width:25,sortable: true,align:'center',renderer:function(e){
				return '<div class="'+e+'" style="width:16px;height:10px;background:no-repeat;">&nbsp;</div>';
			}},
			{dataIndex:'typo', 				header:'Typo',sortable: true,align:'center'}, 
			{dataIndex:'pays_langue', 		header:'Language',sortable: true,align:'center'}, 
			{dataIndex:'pays_name', 		header:'Country',sortable: true}, 
			{dataIndex:'edit_date', 		header:'Edit date',sortable: true,align:'center'},
			{dataIndex:'edit_creation', 	header:'Edit created',sortable: true,align:'center'},
			{dataIndex:'edit_user_fk', 		header:'Last edit by',sortable: true,align:'center'}
		],
		loadMask:true,
		closable:true,
		
		frame: true,
		sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
		
		tbar:[
			{
		        text:'Add',
		        iconCls:'translate-add',
		    	handler: function(){
					Ext.chewingCom.LoadAndExecJS('os/plugins/system/langues/langues_edit.php');
			    }
			}, {
		        text:'Delete',
		        iconCls:'translate-remove',
		        id:'removeSearchBtn',
		    	handler: function(){
					var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected(); 
					if(e){
						Ext.Msg.confirm('Confirm', 'Do you really want to delete ? :', function(btn){
						    if (btn == 'yes'){
						        Ext.Ajax.request({
						        	url:'os/plugins/system/langues/langues_action.php',
									params:{pays_libelle:e.data.pays_libelle,action:'remove'},
									success:function(e){
										Ext.getCmp('gridLangues').store.reload();
									}
								});
						    }
						});
					}else{
						Ext.chewingCom.alertItemSelect();
					}
			    }
			},
			{
		        text:'Edit',
		        iconCls:'translate-edit',
		        id:'removeSearchBtn',
		    	handler: function(){
					var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected(); 
					if(e){
						Ext.chewingCom.LoadAndExecJS('os/plugins/system/langues/langues_edit.php?id='+e.data.pays_libelle);
					}else{
						 Ext.chewingCom.alertItemSelect();
					}
			    }
			},'-',{
		        text:'Localisations',
		        iconCls:'translate-module',
		        menu:[
					<?php
						$dirModules	= listOption('../../modules/');
						
						$buffer = '';
						foreach($dirModules as $value) {
							if (is_file ('../../modules/'.$value.'/module.xml')) {
								$parser = new XMLParser ('../../modules/'.$value.'/module.xml', 'file', 1);
								$allConfigModules[$value] = $parser->getTree();
								
								if (isset ($allConfigModules[$value]['MODULE']['LOCALISATION']['VALUE']) && $allConfigModules[$value]['MODULE']['LOCALISATION']['VALUE'] == 1) {
								
									if (!empty ($buffer)) $buffer .= ',';
									
									$buffer .= '{
										        	text:"'.$allConfigModules[$value]['MODULE']['TITLE']['VALUE'].'",
										        	iconCls:"'.$allConfigModules[$value]['MODULE']['ICON']['VALUE'].'",
										        	handler: function(){
												    	var selectionModel = Ext.getCmp(\'gridLangues\').getSelectionModel();
														var e = selectionModel.getSelected();
														if(e){
															Ext.chewingCom.LoadAndExecJS(\'os/plugins/modules/'.$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE'].'/edit.php?type='.$allConfigModules[$value]['MODULE']['NAMESPACE']['VALUE'].'&langue=\'+e.data.pays_libelle);
														}else{
															 Ext.chewingCom.alertItemSelect();
														}
												    }
												}';
								}
							}
						}
						echo $buffer;
					?>
		        ]
		    	
			},{
		        text:'Export for Prod',
		        iconCls:'translate-export',
		    	handler: function(){
			    	var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected();
					if(e){
						Ext.Msg.confirm('Confirmation', 'You will replace everything in front,<br/>do you really want to do that ?', function(btn){
							if (btn == 'yes'){
								progressbar=Ext.MessageBox.wait('Please wait','Processing...', {});		
								 Ext.Ajax.request({
						        	url:'os/plugins/system/export/exportprod.php?langue='+e.data.pays_libelle,
									success:function(e){
										progressbar.hide();
										eval(e.responseText);
									}
								});
								
							}
						});
					}else{
						 Ext.chewingCom.alertItemSelect();
					}
			    }
			},{
		        text:'Preview',
		        iconCls:'translate-preview',
		    	handler: function(){
			    	var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected();
					if(e){
						window.open('../?mode=preview&langue='+e.data.pays_libelle);
					}else{
						 Ext.chewingCom.alertItemSelect();
					}
			    }
			},{
				text:'Clone a language',
				iconCls:'translate-clone',
				handler:function(){
					var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected();
					if(e){
						 Ext.chewingCom.LoadAndExecJS('os/plugins/system/langues/copie_edit.php?langue='+e.data.pays_libelle);
					}else{
						 Ext.chewingCom.alertItemSelect();
					}
				}
			},{
				text:'Export emails newslettere',
				iconCls:'user',
				handler:function(){
					var selectionModel = Ext.getCmp('gridLangues').getSelectionModel();
					var e = selectionModel.getSelected();
					if(e){
						 window.open("os/plugins/system/export_email/csv.php?langue="+e.data.pays_libelle);
					}else{
						 window.open("os/plugins/system/export_email/csv.php");
					}
				}
			}
			
			
			
			
			
		],
		
		
		
		bbar: new Ext.PagingToolbar({
			store:storeLangues,
			pageSize:50,
			displayInfo: true
		}),
		view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Languages" : "Language"]})',
            enableGroupingMenu :false
        }),

		width:'auto',
		height:'auto',
		
		autoScroll:true
	});
	
	gridLangues.on('rowdblclick',function(){
		var selectionModel = this.getSelectionModel();
		var e = selectionModel.getSelected();
		Ext.chewingCom.LoadAndExecJS('os/plugins/system/langues/langues_edit.php?id='+e.data.pays_libelle);
	});	
	
	storeLangues.load({params:{start:0, limit:50}});

	desktop.createWindow({
		title:'Language settings',
		id:'wingridLangues',
		width:800,
		height:400,
		iconCls:'translate-module',
	    layout:'fit',
	    border:true,
		animCollapse:true,
		constrainHeader:true,
		items:gridLangues
	}).show();
	
}else{
	currentwin.show();
	currentwin.toFront();
}