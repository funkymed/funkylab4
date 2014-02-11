<?php
	if(!isset($_REQUEST['id']))
		exit();
?>

var meta_edit_form= new Ext.form.FormPanel({
    labelWidth:70,
    frame:true,
    id:'metaform',
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
		,{name: 'meta_title', 			mapping:'meta_title',			type:'string'}
		,{name: 'meta_tags', 			mapping:'meta_tags',			type:'string'}
		,{name: 'meta_description', 	mapping:'meta_description',		type:'string'}
    ]),
    items:[
    	 new Ext.form.Hidden({name : "action",value:"update"})
		,new Ext.form.Hidden({name : "id_page"})
		,new Ext.form.TextField({fieldLabel	 : "Titre",			name : "meta_title",	anchor:'100%'})
		,new Ext.form.TextField({fieldLabel	 : "Tags",			name : "meta_tags",	anchor:'100%'})
		,new Ext.form.TextField({fieldLabel	 : "Description",	name : "meta_description",anchor:'100%'})
	],
	buttons: [{
        text:'Enregistrer',
        handler:function(){
	        var objSave = Ext.getCmp('metaform').getForm().getValues();
			Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
	        Ext.Ajax.request({
	        	url:'admin/os/plugins/modules/site_arbo/action_meta.php', 
				params:objSave,
				success: function(e) {
					Ext.chewingCom.progressbar.hide();
	        		Ext.getCmp('metawin').close();
					Ext.example.msg('Info', 'Meta donn&eacute;es sauvegard&eacute;es');
				}
			});
        }
	},{
        text: 'Annuler',
        handler:function(){
        	Ext.getCmp('metawin').close();
        }
	}]
});

new Ext.Window({
	 title:'Metas'
	,id:'metawin'
	,modal:true
	,width:640
	,draggable:false
	,resizable:false
	,minimizable:false
	,maximizable:false
	,items:meta_edit_form,
}).show();

Ext.getCmp('metaform').load({url:'admin/os/plugins/modules/site_arbo/data_meta.php?id='+<?php print $_REQUEST['id'];?>, waitMsg:'Chargement...'});