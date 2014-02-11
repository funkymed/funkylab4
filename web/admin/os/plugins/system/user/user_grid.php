var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('winGridUser');
if(!currentwin){
	

	
	var storeUser = new Ext.data.GroupingStore({
		id:'storeUser',
		proxy:new Ext.data.HttpProxy({
			url:'os/plugins/system/user/user_datalist.php'
		}),
		reader:new Ext.data.JsonReader({
			totalProperty:'data.total',
			root:'data.results'
		}, Ext.data.Record.create([
			{name:'id'}, 
			{name:'login'}, 
			{name:'nom'}, 
			{name:'prenom'}, 
			{name:'langue'}, 
			{name:'admin'}, 
			{name:'datemodif'},
			{name:'datecreation'},
			{name:'dateconnexion'},
			{name:'pays_name'}
		])),
		sortInfo:{field:'id', direction:'DESC'},
		remoteSort:true
		//,groupField:'pays_name'		
	});
	var gridUser = new Ext.grid.GridPanel({
		id:'gridUser',
		ds:storeUser,
		columns:[
			{dataIndex:'id', 			header:'Id',width:25,align:'center',sortable: true}, 
			{dataIndex:'admin', 		header:'Type',sortable: true,align:'center',renderer:function(e){
            	switch(e)
                {
                	case 'sadmin':return 'Super admin';break;
                    case 'admin':return 'Admin';break;
                    case 'redacteur':return 'Redacteur';break;
                    case 'moderateur':return 'Moderateur';break;                   
                    case 'user':return 'Utilisateur';break;
                    case 'videos':return 'Videos';break;
                    default :return "";
				}
			}},
			{dataIndex:'langue', 		header:'Langue',sortable: true,align:'center',renderer:function(e){
				
				return '<div class="'+e+'" style="width:16px;height:10px;background:no-repeat;">&nbsp;</div>';
				
			}},
			{dataIndex:'pays_name', 	header:'Pays',sortable: true,align:'center',hidden:true},
			{dataIndex:'login', 		header:'Identifiant',sortable: true}, 
			{dataIndex:'nom', 			header:'Nom',sortable: true},
			{dataIndex:'prenom', 		header:'Pr&eacute;nom',sortable: true}, 
			{dataIndex:'datecreation', 	header:'Cr&eacute;er le',sortable: true,align:'center'},
			{dataIndex:'datemodif', 	header:'Editer le',sortable: true,align:'center'},
			{dataIndex:'dateconnexion', header:'Derni&egrave;re connexion',sortable: true,align:'center'}
		],
		loadMask:true,
		closable:true,
		frame: true,
		sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
		tbar:[{
	        text:'Ajouter',
	        iconCls:'admin-add',
	    	handler: function(){
				Ext.chewingCom.LoadAndExecJS('os/plugins/system/user/user_edit.php');
		    }
		}, '-',{
	        text:'Supprimer',
	        iconCls:'admin-remove',
	        id:'removeSearchBtn',
	    	handler: function(){
				var selectionModel = Ext.getCmp('gridUser').getSelectionModel();
				var e = selectionModel.getSelected(); 
				if(e){
					Ext.Msg.confirm('Confirm', 'Do you really want to delete ? :', function(btn){
					    if (btn == 'yes'){
					        Ext.Ajax.request({
					        	url:'os/plugins/system/user/user_action.php',
								params:{id:e.data.id,action:'remove'},
								success:function(e){
									Ext.getCmp('gridUser').store.reload();
								}
							});
					    }
					});
				}else{
					 Ext.chewingCom.alertItemSelect();
				}
		    }
		},{
	        text:'Modifier',
	        iconCls:'admin-edit',
	        id:'removeSearchBtn',
	    	handler: function(){
				var selectionModel = Ext.getCmp('gridUser').getSelectionModel();
				var e = selectionModel.getSelected(); 
				if(e){
					Ext.chewingCom.LoadAndExecJS('os/plugins/system/user/user_edit.php?id='+e.data.id);
				}else{
					 Ext.chewingCom.alertItemSelect();
				}
		    }
		}],
		bbar: new Ext.PagingToolbar({
			store:storeUser,
			pageSize:15,
			displayInfo: true
		}),
		view: new Ext.grid.GroupingView({
            forceFit:true,
            autoFill:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Users" : "User"]})',
            enableGroupingMenu :false
        }),
		width:'auto',
		height:'auto',
		
		autoScroll:true
	});
	
	gridUser.on('rowdblclick',function(){
		var selectionModel = this.getSelectionModel();
		var e = selectionModel.getSelected();
		Ext.chewingCom.LoadAndExecJS('os/plugins/system/user/user_edit.php?id='+e.data.id);
		
	});	
	
	storeUser.load({params:{start:0, limit:15}});

	desktop.createWindow({
		title:'G&eacute;rer les utilisateurs',
		id:'winGridUser',
		width:800,
		height:400,
		iconCls:'admin-module',
	    layout:'fit',
	    border:true,
		animCollapse:true,
		constrainHeader:true,
		items:gridUser
	}).show();

}else{
	currentwin.show();
	currentwin.toFront();
}