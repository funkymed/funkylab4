<?php
	function Ext_selectlist ($id,$value) {
		$var = "{
			xtype:'multiselect',
			fieldLabel:'Colors',
			name:'colors',
			id:'colors',
			dataFields:['code', 'desc'], 
			data:".json_encode($value['values']).",
			valueField:'code',
			displayField:'desc',
			width:250,
			height:200,
			allowBlank:true,
			tbar:[
				{
					text:'clear',
					handler:function(){
						Ext.getCmp('".$id."').reset();
					}
				}
			]
		}";
		
		return $var;
	}
	
	function Ext_selectlist_beforesave($id) {
		return "";
	}
	function Ext_selectlist_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_selectlist_init ($id,$value) {
		return "";
	}
?>