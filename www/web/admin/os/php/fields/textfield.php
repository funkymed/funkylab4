<?php
	function Ext_textfield ($id,$value) {
		return "new Ext.form.TextField ({id:'".$id."',fieldLabel: '".$value['label']."',width:500,value:\"".Ext_textfield_cleanText($value['value'])."\"})\n";
	}
	function Ext_textfield_beforesave($id) {
		return "";
	}
	function Ext_textfield_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_textfield_init ($id,$value) {
		return "";
	}
	function Ext_textfield_cleanText($str){
		$str = stripslashes($str);
		$str = str_replace('\\"','"',$str);
		$str = str_replace('"','\"',$str);
		return $str;
	}
?>