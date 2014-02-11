<?php
	if(!isset($_REQUEST['id']))
		exit();
?>

var urlredirect_edit_form= new Ext.form.FormPanel({
    labelWidth:70,
    frame:true,
    id:'urlredirectform',
	layout: 'form',
	border:false,
	autoScroll:true,
	waitMsgTarget: true,
    reader : new Ext.data.XmlReader({
        record : 'page',
        success: '@success'
    }, [
    	 {name: 'action', 				mapping:'action',				type:'string'}
    	,{name: 'id_page', 				mapping:'id_page',				type:'string'}
		,{name: 'urlredirect', 			mapping:'urlredirect',			type:'string'}
    ]),
    items:[
    	 new Ext.form.Hidden({name : "action",value:"update"})
		,new Ext.form.Hidden({name : "id_page"})
		,new Ext.form.TextField({fieldLabel	 : "Url",			name : "urlredirect",	anchor:'100%',value:'http://'})
	],
	buttons: [{
        text:'Enregistrer',
        handler:function(){
        
	        var objSave = Ext.getCmp('urlredirectform').getForm().getValues();

			Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
			
	        Ext.Ajax.request({
	        	url:'admin/os/plugins/modules/site_arbo/action_urlredirect.php', 
				params:objSave,
				success: function(e) {
					Ext.chewingCom.progressbar.hide();
	        		Ext.getCmp('urlredirectwin').close();
	        		
					Ext.example.msg('Info', 'Url de redirection sauvegard&eacute;e');
						
				}
			});
			
        }
	},{
        text: 'Annuler',
        handler:function(){
        	Ext.getCmp('urlredirectwin').close();
        }
	}]
});

new Ext.Window({
	 title:'urlredirects'
	,id:'urlredirectwin'
	,modal:true
	,width:640
	,draggable:false
	,resizable:false
	,minimizable:false
	,maximizable:false
	,items:urlredirect_edit_form,
}).show();

Ext.getCmp('urlredirectform').load({url:'admin/os/plugins/modules/site_arbo/data_urlredirect.php?id='+<?php print $_REQUEST['id'];?>, waitMsg:'Chargement...'});