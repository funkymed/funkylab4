<?php
	if(!isset($_REQUEST['id']))
		exit();
?>

var types_edit_form= new Ext.form.FormPanel({
    labelWidth:70,
    frame:true,
    id:'typesform',
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
		,{name: 'typeprincipal', 		mapping:'typeprincipal',		type:'string'}
		,{name: 'typesecondaire', 		mapping:'typesecondaire',		type:'string'}
    ]),
    items:[
    	 new Ext.form.Hidden({name : "action",value:"update"})
		,new Ext.form.Hidden({name : "id_page"})
		
		,new Ext.form.ComboBox({fieldLabel:'Type principal',name:'typeprincipal',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
			,width:520
			,triggerClass: 'x-form-search-trigger'
		    ,store:new Ext.data.Store({
			    proxy: new Ext.data.ScriptTagProxy({
			        url: "admin/os/plugins/modules/site_arbo/getvalues.php?field=typeprincipal" 
			    }),
			   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
		    })
		})
		,new Ext.form.ComboBox({fieldLabel:'Type secondaire',name:'typesecondaire',editable:true,selectOnFocus:true,mode:'remote',triggerAction: 'all',valueField:'value',displayField:'text'
			,width:520
			,triggerClass: 'x-form-search-trigger'
		    ,store:new Ext.data.Store({
			    proxy: new Ext.data.ScriptTagProxy({
			        url: "admin/os/plugins/modules/site_arbo/getvalues.php?field=typesecondaire" 
			    }),
			   reader:new Ext.data.JsonReader({root: 'topics',totalProperty: 'totalCount',id: 'text'}, [{name: 'value'},{name: 'text'}])
		    })
		})
		
	],
	buttons: [{
        text:'Enregistrer',
        handler:function(){
	        var objSave = Ext.getCmp('typesform').getForm().getValues();
			Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
	        Ext.Ajax.request({
	        	url:'admin/os/plugins/modules/site_arbo/action_types.php', 
				params:objSave,
				success: function(e) {
					Ext.chewingCom.progressbar.hide();
	        		Ext.getCmp('typeswin').close();
					Ext.example.msg('Info', 'types donn&eacute;es sauvegard&eacute;es');
				}
			});
        }
	},{
        text: 'Annuler',
        handler:function(){
        	Ext.getCmp('typeswin').close();
        }
	}]
});

new Ext.Window({
	 title:'typess'
	,id:'typeswin'
	,modal:true
	,width:640
	,draggable:false
	,resizable:false
	,minimizable:false
	,maximizable:false
	,items:types_edit_form,
}).show();

Ext.getCmp('typesform').load({url:'admin/os/plugins/modules/site_arbo/data_types.php?id='+<?php print $_REQUEST['id'];?>, waitMsg:'Chargement...'});