<?php
	session_start();
	unset($_SESSION['filter']);
?>

var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('wingridagendacontacts');
if(!currentwin){

	var sotreagendacontacts = new Ext.data.GroupingStore({
		id:'sotreagendacontacts',
		proxy:new Ext.data.HttpProxy({
			url:'os/plugins/modules/agendacontacts/datalist.php'
		}),
		reader:new Ext.data.JsonReader({
			id: 'id',
			totalProperty:'data.total',
			root:'data.results'
		}, Ext.data.Record.create([
			{name:'id_contact'}, 
			{name:'contact_titre'}, 
			{name:'contact_nom'}, 
			{name:'contact_prenom'}, 
			{name:'contact_adresse'}, 
			{name:'contact_cp'}, 
			{name:'contact_ville'},
			{name:'contact_quartier'}, 
			{name:'contact_tel'},  
			{name:'contact_fax'}, 
			{name:'contact_email'}, 
			{name:'contact_url'}, 
			{name:'edit_creation'}, 
			{name:'edit_date'}, 
			{name:'useredit'}
		])),
		sortInfo:{field:'id_contact', direction:'DESC'},
		remoteSort:true
	});
	

	var filters = new Ext.ux.grid.GridFilters({
		filters:[
			 {type: 'numeric', 	dataIndex: 'id_contact'}
			,{type: 'string',  	dataIndex: 'contact_titre'}
			,{type: 'string',  	dataIndex: 'contact_nom'}
			,{type: 'string', 	dataIndex: 'contact_prenom'}
			,{type: 'string', 	dataIndex: 'contact_adresse'}
			,{type: 'numeric',  dataIndex: 'contact_cp'}
			,{type: 'string',  	dataIndex: 'contact_ville'}
			,{type: 'string',  	dataIndex: 'contact_quartier'}
			,{type: 'string',  	dataIndex: 'contact_tel'}
			,{type: 'string',  	dataIndex: 'contact_fax'}
			,{type: 'string',  	dataIndex: 'contact_url'}
			,{type: 'date',  	dataIndex: 'edit_date'}
			,{type: 'date',  	dataIndex: 'edit_creation'}
		]
	});
	
	
	var gridagendacontact = new Ext.grid.GridPanel({
		id:'gridagendacontacts',
		ds:sotreagendacontacts,
		cm:new Ext.grid.ColumnModel([
			{dataIndex:'id_contact', 		header:'id',width:25,sortable:true}, 
			{dataIndex:'contact_titre', 	header:'Titre',width:50,sortable:true}, 
			{dataIndex:'contact_nom', 		header:'Nom',sortable:true}, 
			{dataIndex:'contact_prenom', 	header:'Prenom',sortable:true}, 
			{dataIndex:'contact_adresse', 	header:'Adresse',sortable:true}, 
			{dataIndex:'contact_cp', 		header:'Code postal',sortable:true}, 
			{dataIndex:'contact_ville', 	header:'Ville',sortable:true},
			{dataIndex:'contact_quartier', 	header:'Quartier',sortable:true}, 
			{dataIndex:'contact_tel', 		header:'Telephone',sortable:true},
			{dataIndex:'contact_fax', 		header:'Fax',sortable:true}, 
			{dataIndex:'contact_email', 	header:'Email',sortable:true}, 
			{dataIndex:'contact_url', 		header:'Url',renderer:function(o){
				return '<a href="'+o+'" target="_blank">'+o+'</a>';
			},sortable:true}, 
			{dataIndex:'edit_creation', 	header:'Date ajout',sortable:true,hidden:true}, 
			{dataIndex:'edit_date', 		header:'Date modif',sortable:true,hidden:true}, 
			{dataIndex:'useredit', 		header:'Edit&eacute; par',hidden:true}
		]),
		loadMask:true,
		frame: true,
		sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
		plugins: [new Ext.ux.grid.DragSelector(),filters],
 		tbar:[
			{
				text:'Ajouter',
				iconCls:'add',
				handler:function(){
					Ext.chewingCom.LoadAndExecJS('os/plugins/modules/agendacontacts/edit.php?action=add');
				}
			},'-',{
		        text:'Editer',
		        iconCls:'edit',
		    	handler: function(){
			    	var s = Ext.getCmp('gridagendacontacts').getSelectionModel().getSelections();
					if(s && s[0]){
						Ext.chewingCom.LoadAndExecJS('os/plugins/modules/agendacontacts/edit.php?action=update&id='+s[0].data.id_contact);
					}
			    }
			}
			
			<?php if (in_array($_SESSION[sessionName]['user']['admin'],array('sadmin', 'admin'))) { ?>
			,'-',{
		        text:'Effacer',
		        tooltip:'Effacer le(s) contact(s) s&eacute;l&eacute;ctionn&eacute;(s)',
		        iconCls:'remove',
		    	handler: function(){
			    	var selectionModel = Ext.getCmp('gridagendacontacts').getSelectionModel().getSelections();
			    	var allId=new Array();
			    	Ext.each(selectionModel,function(a){
				    	allId.push(a.data.id_contact);
			    	});
					if(allId.length>0){
						allId = allId.join(",");	
						Ext.Msg.confirm('Confirmation', 'Voulez vous effacer cet email ?', function(btn){
						    if (btn == 'yes'){
						        Ext.Ajax.request({
						        	url:'os/plugins/modules/agendacontacts/action.php',
									params:{id:allId,action:'remove'},
									success:function(e){
										Ext.getCmp('gridagendacontacts').store.reload();
									}
								});
						    }
						});
					}else{
						 Ext.MessageBox.show({ title:'Alerte', msg:"Veuillez selectionner un contact", buttons: Ext.MessageBox.OK, icon: 'ext-mb-warning' });
					}
					
			    }
			}
			
			<?php } ?>
			,'->',{
		        text:'Exporter',
		        iconCls:'xls',
		        id:'removeSearchBtn',
		    	handler: function(){
					document.location.href="os/plugins/modules/agendacontacts/exportxml.php";
			    }
			}
 		],
		bbar: new Ext.PagingToolbar({
			store: sotreagendacontacts,
			pageSize: 15,
			displayInfo: true
		}),
		viewConfig: { forceFit:true },
		width:'auto',
		iconCls:'GridEdit',
		autoScroll:true
	});
	
	gridagendacontact.on('rowdblclick',function(){
		var s = Ext.getCmp('gridagendacontacts').getSelectionModel().getSelections();
		if(s && s[0]){
			Ext.chewingCom.LoadAndExecJS('os/plugins/modules/agendacontacts/edit.php?action=update&id='+s[0].data.id_contact);
		}
	});	
	
	sotreagendacontacts.load({params:{start:0, limit:15}});
	
	desktop.createWindow({
		title:'Agenda Contacts',
		id:'wingridagendacontacts',
		iconCls:'agendacontact',
		width:1024,
		height:600,
	    layout:'fit',
	    border:true,
		animCollapse:true,
		constrainHeader:true,
		items:gridagendacontact
	}).show();
	
	
}else{
	currentwin.toFront();
	currentwin.show();
}


	     	