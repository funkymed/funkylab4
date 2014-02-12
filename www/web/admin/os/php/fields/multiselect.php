<?php
	function Ext_multiselect ($id,$value) {
		
		$allContentSelected		= array();
		$allContentUnselected	= array();
		
		if($value['value']!=""){
			$allIds=explode(",",$value['value']);	
			foreach($value['values'] as $_value){
				if(!in_array($_value[0],$allIds))
					$allContentUnselected[]=$_value;
			}
			foreach($allIds as $v){
				foreach($value['values'] as $_value){
					if($_value[0]==$v){
						$allContentSelected[]=$_value;
					}
				}
			}
		}else{
			$allContentUnselected=$value['values'];
		}
		
		$var ="{
				layout: 'fit',
				bodyStyle: 'padding-left:20px;',
				items:[{
					bodyStyle: 'margin-bottom:20px;',
					xtype:'itemselector',
					id:'".$id."',
					hideLabel:true,
					dataFields:['Id', 'Libelle'],
					fromData:".json_encode($allContentUnselected).",
					toData:".json_encode($allContentSelected).",
					msWidth:300,
					msHeight:220,
					width:600,
					height:220,
					valueField:'Id',
					displayField:'Libelle',
					fromLegend:'from',
					toLegend:'to'
				}]
			}";
		
		
		
		return $var;
	}
	
	function Ext_multiselect_beforesave($id) {
		return "";
	}
	function Ext_multiselect_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_multiselect_init ($id,$value) {
		return "";
	}
?>