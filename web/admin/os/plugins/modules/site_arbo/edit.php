<?php
	require_once("../../../php/const.php");
	
	if(!isset($_GET['id']))
		exit();
		
?>

	var desktop = MyDesktop.getDesktop();
	Ext.chewingCom.StartEditing();
	desktop.createWindow({
		 title:'edition de page (<?php print $_GET['id'];?>)'
		,frame:true
		,width:800
		,height:600
		,resizable:false
		,maximizable:false
		,minimizable:false
		,maximized:true
		,draggable:false
		,buttonAlign:'center'
		,closeAction:'close'
		,close:function(){ Ext.chewingCom.StopEditing(this); }
		,layout:'fit'
		,border:true
		,animCollapse:false
		,constrainHeader:true
		,html:'<iframe allowtransparency="false" backgroundcolor="white" marginwidth="0" marginheight="0" frameborder="no" border="0" src="../pages/<?php print $_GET['id'];?>-edition.html" width="100%" height="100%"></iframe>'
	}).show();
