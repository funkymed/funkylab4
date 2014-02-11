<?php
	require_once("../../../php/const.php");
?>

	var langue_dest = 	{
		id:'langue_dest',
		align:'right',
		tooltip:'Select a language',
	    xtype:'iconcombo',
	    editable:false,
	    fieldLabel:'Langue',
	    store: new Ext.data.SimpleStore({
	        fields: ['countryCode', 'countryName', 'countryFlag'],
	        data: <?php print $allLangueItems;?>
	    }),
	    valueField: 'countryCode',
	    displayField: 'countryName',
	    iconClsField: 'countryFlag',
	    triggerAction: 'all',
	    mode: 'local',
	    emptyText:'Destination'
	};
	
Ext.chewingCom.StartEditing();	
	
MyDesktop.getDesktop().createWindow({
	    title: "Clone a language",
	    id:'wincopielangue',
	   	width:300, 
	   	height:90,
	    modal:true,
	    resizable:false,
	    draggable:false,
	    buttonAlign:'center',
	    closeAction:'close',
	    close:function(){ Ext.chewingCom.StopEditing(this); },
	    layout:'fit',
	    items:langue_dest,
	    buttons: [{
		    text: 'Save',
	        iconCls:'add',
	        handler:function(){
		        var source_langue = '<?php print $_GET['langue'];?>';
		        if(Ext.getCmp('langue_dest').getValue()==''){
			        Ext.MessageBox.show({title:'Alert',msg:"Select a destination",buttons: Ext.MessageBox.OK,icon: 'ext-mb-warning'});
			        return false;
	        	}else if(source_langue==Ext.getCmp('langue_dest').getValue()){
			        Ext.MessageBox.show({title:'Alert',msg:"You can't copie a langue on itself",buttons: Ext.MessageBox.OK,icon: 'ext-mb-warning'});
			        return false;
		        }else{
					var objSave={ destination:Ext.getCmp('langue_dest').getValue(), source:source_langue };
					
					progressbar=Ext.MessageBox.wait('Please wait...','Saving', {});
			        Ext.Ajax.request({
			        	url:'os/plugins/system/langues/copie.php', 
						params:objSave,
						success: function(e) {
							progressbar.hide();
		        			Ext.getCmp('wincopielangue').close();
						}
					});
				}
	        }
		},{
	        text: 'Cancel',
	        iconCls:'remove',
	        handler:function(){
		       Ext.getCmp('wincopielangue').close();
	        }
		}]
	}).show();