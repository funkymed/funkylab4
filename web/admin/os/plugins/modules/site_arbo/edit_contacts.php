<?php
	if(!isset($_REQUEST['id']))
		exit();
?>

<?php
	require_once("../../../php/const.php");
?>
var getContact = function (){
	return <?php
	$contacts = array();
	$res = mysql_query("SELECT contact_titre,contact_nom,contact_prenom,id_contact FROM contacts ORDER BY contact_titre,contact_nom,contact_prenom ASC");
	while($row = mysql_fetch_object($res)){
		$contacts[]=array($row->id_contact,$row->contact_titre.", ".$row->contact_nom." ".$row->contact_prenom);
	}
	print json_encode($contacts);
	?>
}

var contacts_edit_form= new Ext.form.FormPanel({
    id:'contactsform',
	frame:true,
    border:true,
	layout: 'fit',
	waitMsgTarget: true,
    reader : new Ext.data.XmlReader({
        record : 'page',
        success: '@success'
    }, [
    	 {name: 'action', 				mapping:'action',				type:'string'}
    	,{name: 'id_page', 				mapping:'id_page',				type:'string'}
		,{name: 'id_contact_fk', 		mapping:'id_contact_fk',		type:'string'}
    ]),
    items:[
    	 new Ext.form.Hidden({name : "action",value:"update"})
		,new Ext.form.Hidden({name : "id_page"})
		,new Ext.ux.Multiselect({
				name              :  'id_contact_fk',
				fieldLabel        :  'Selectionnez un ou plusieurs contacts',
				dataFields        :  ['code', 'desc'], 
				data              :  getContact(),
				valueField        :  'code',
				displayField      :  'desc',
				width             :  640,
				height            :  340,
				allowBlank        :  true
			})
	],
	buttons: [{
        text:'Enregistrer',
        handler:function(){
        
	        var objSave = Ext.getCmp('contactsform').getForm().getValues();

			Ext.chewingCom.progressbar=Ext.MessageBox.wait('En cours...','Sauvegarde', {});
			
	        Ext.Ajax.request({
	        	url:'admin/os/plugins/modules/site_arbo/action_contacts.php', 
				params:objSave,
				success: function(e) {
					Ext.chewingCom.progressbar.hide();
	        		Ext.getCmp('contactswin').close();
	        		
					Ext.example.msg('Info', 'Contacts sauvegard&eacute;s');
						
				}
			});
			
        }
	},{
        text: 'Annuler',
        handler:function(){
        	Ext.getCmp('contactswin').close();
        }
	}]
});


new Ext.Window({
	 title:'Contacts'
	,id:'contactswin'
	,modal:true
	,width:670
	,height:420
	,draggable:false
	,resizable:false
	,minimizable:false
	,maximizable:false
	,items:contacts_edit_form,
}).show();

Ext.getCmp('contactsform').load({url:'admin/os/plugins/modules/site_arbo/data_contacts.php?id='+<?php print $_REQUEST['id'];?>, waitMsg:'Chargement...'});