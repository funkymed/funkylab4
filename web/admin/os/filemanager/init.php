var desktop = MyDesktop.getDesktop();
var currentwin = desktop.getWindow('filemanager');
if(!currentwin){
	var wintree = MyDesktop.getDesktop().createWindow({
		title:'G&eacute;rer les fichiers',
        iconCls:'drive',
		id:'filemanager',
		width:640,
		height:480,
	    layout:'fit',
	    border:true,
		animCollapse:false,
		constrainHeader:true,
		items:new Ext.ux.FileTreePanel({
			width:250
			,height:400
			,id:'ftp'
			,rootPath:'directory'
			,topMenu:true
			,autoScroll:true
			,enableProgress:false
		})
	});
	wintree.show();
}else{
	currentwin.show();
	currentwin.toFront();
}