<?php
	function Ext_file ($id,$value) {
		return array('image'.$id.'.field','image'.$id.'.preview');
	}
	function Ext_file_beforesave($id) {
		return "";
	}
	function Ext_file_save($id) {
		return "\t\t".$id.":Ext.getCmp('id_".$id."').getValue()";
	}
	function Ext_file_init ($id,$value) {
		$buffer =  'var image'.$id.' = Ext.chewingCom.btnFileBrowser("'.$id.'","'.$value['label'].'","'.$value['value'].'","*");';
		return $buffer;
	}
?>