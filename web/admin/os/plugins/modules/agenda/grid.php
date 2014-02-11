<?php
	require_once("../../../php/const.php");
	ini_set("display_errors",1);
	error_reporting(E_ALL);
?>
var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('wingridagenda');
if(!currentwin){
	var storeagenda = new Ext.data.GroupingStore({
		id:'storeagenda',
		proxy:new Ext.data.HttpProxy({
			url:'os/plugins/modules/agenda/datalist.php'
		}),
		reader:new Ext.data.JsonReader({
			totalProperty:'data.total',
			root:'data.results'
		}, Ext.data.Record.create([
			 {name:'id_agenda'}
			,{name:'agenda_online'}
			,{name:'agenda_titre'}
			,{name:'agenda_type'}
			,{name:'agenda_state'}
			,{name:'agenda_debut'}
			,{name:'agenda_fin'}
			,{name:'agenda_tags'}
			,{name:'edit_date'}
			,{name:'edit_creation'}
			,{name:'useredit'}
		])),
		sortInfo:{field:'id_agenda', direction:'ASC'},
		remoteSort:true
	});
	
	var editagenda = function(){
		var selectionModel = Ext.getCmp('agendagrid').getSelectionModel();
		var e = selectionModel.getSelected();
		if(e) Ext.chewingCom.LoadAndExecJS('os/plugins/modules/agenda/edit.php?id_agenda='+e.data.id_agenda);
	}	

	var editagendaaction = function(action){
		var selectionModel = Ext.getCmp('agendagrid').getSelectionModel();
		var e = selectionModel.getSelected();
		if(e){
			Ext.Ajax.request({
	        	url:'os/plugins/modules/agenda/action.php',
				params:{id:e.data.id_agenda,action:action},
				success:function(e){
					Ext.getCmp('agendagrid').store.reload();
				}
			});
		}
	}	
	
	var filters = new Ext.ux.grid.GridFilters({
		filters:[
			 {type: 'numeric', 	dataIndex: 'id_agenda'}
			,{type: 'string',  	dataIndex: 'agenda_titre'}
			
			,{
				type: 'list',  
				dataIndex: 'agenda_state', 
				options: [['preview','brouillon'],['published','publi&eacute;'],['archived','archiv&eacute;	']],
				phpMode: true
			}
			,{type: 'boolean',  dataIndex: 'agenda_online'}
			,{type: 'string',  	dataIndex: 'agenda_type'}
			,{type: 'date',  	dataIndex: 'agenda_debut'}
			,{type: 'date', 	dataIndex: 'agenda_fin'}
			,{type: 'string', 	dataIndex: 'agenda_tags'}
			,{type: 'date',  	dataIndex: 'edit_date'}
			,{type: 'date',  	dataIndex: 'edit_creation'}
		]
	});	
	
	var gridagenda = new Ext.grid.GridPanel({
		id:'agendagrid',
		ds:storeagenda,
		plugins: [filters],
		columns:[
			 {dataIndex:'id_agenda', 			header:'Id',align:'center',width:40,sortable: true}
			,{dataIndex:'agenda_online',		header:'En ligne',align:'center',width:50,sortable: true,renderer:function(o){
				return (o==1) ? "oui" : "non";
			}}
			,{dataIndex:'agenda_titre', 		header:'Titre',align:'left',sortable: true}
			,{dataIndex:'agenda_type', 			header:'Type',align:'left',sortable: true}
			,{dataIndex:'agenda_state', 		header:'Etat',align:'left',sortable: true,renderer:function(o){
				if(o=='preview'){
					o='brouillon';
				}else if(o=='published'){
					o='publi&eacute;';
				}else{
					o='archiv&eacute;';
				}
				return o;
			}}
			,{dataIndex:'agenda_debut', 		header:'Date debut',align:'left',sortable: true}
			,{dataIndex:'agenda_fin',			header:'Date fin',align:'right',width:110,sortable: true}
			,{dataIndex:'agenda_tags',			header:'Tags',align:'right',width:110,sortable: true}
			,{dataIndex:'edit_creation', 		header:'Date creation',align:'right',width:110,sortable: true,hidden:true}
			,{dataIndex:'edit_date', 			header:'Date edition',align:'right',width:110,sortable: true,hidden:true}
			,{dataIndex:'useredit', 			header:'Moderateur',align:'right',width:100,sortable: false,hidden:true}
		],
		sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
		bbar: new Ext.PagingToolbar({
			store:storeagenda,
			pageSize:15,
			displayInfo: true
		}),
		viewConfig: { forceFit:true },
		enableColLock: false,
		loadMask: true,
		width:'auto',
		height:'auto',
		autoScroll:true,
		tbar:[
			{
		        text:'Ajouter',
		        iconCls:'add',
		    	handler: function(){
			    	Ext.chewingCom.LoadAndExecJS('os/plugins/modules/agenda/edit.php');
		    	}
			},'-',{
		        text:'Editer',
		        iconCls:'edit',
		    	handler: editagenda
			},'-',{
		        text:'Effacer',
		        iconCls:'remove',
		    	handler: function(){
			    	var selectionModel = Ext.getCmp('agendagrid').getSelectionModel();
					var e = selectionModel.getSelected();
					if(e){
						Ext.Msg.confirm('Confirmation', 'Voulez vous effacer cet evenement ?', function(btn){
						    if (btn == 'yes'){
						        Ext.Ajax.request({
						        	url:'os/plugins/modules/agenda/action.php',
									params:{id:e.data.id_agenda,action:'remove'},
									success:function(e){
										Ext.getCmp('agendagrid').store.reload();
									}
								});
						    }
						});
					}
			    }
			}
			<?php if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin', 'admin','moderateur'))){ ?>
			,'-',{
				text:'Etat',
				menu:[
					{
				        text:'Brouillon',
				    	handler: function(){
				    		editagendaaction('setpreview');
				    	}
					},{
				        text:'Publi&eacute;',
				    	handler: function(){
				    		editagendaaction('setpublished');
				    	}
					},{
				        text:'Archiv&eacute;',
				    	handler: function(){
				    		editagendaaction('setarchived');
				    	}
					}
				]
			}
			
			<?php } ?>
			,'-',{
		        text:'En ligne',
		    	menu: [
		    		{
		    			text:'oui',
		    			handler: function(){
				    		editagendaaction('setonline');
				    	}
				    },{
		    			text:'non',
		    			handler: function(){
				    		editagendaaction('setoffline');
				    	}
				    }
				]
			}
			<?php if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin', 'admin','moderateur','redacteur'))){ ?>
			,'-',{
	    		text:'Duppliquer',
	    		iconCls:'copy',
	    		handler: function(){
		    		editagendaaction('duplicate');
		    	}
		    }
		    <?php } ?> 
		]
	});		
	
	gridagenda.on('rowdblclick',editagenda);	
	
	storeagenda.load({params:{start:0, limit:15}});

	desktop.createWindow({
		title:'Agenda',
		id:'wingridagenda',
		width:800,
		height:400,
		iconCls:'agenda',
	    layout:'fit',
	    border:true,
		animCollapse:true,
		constrainHeader:true,
		items:gridagenda
	}).show();
	
	
}else{
	currentwin.show();
	currentwin.toFront();
}