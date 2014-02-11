<?php
	function Ext_textarea ($id,$value) {
		return "new Ext.form.TextArea ({id:'".$id."',fieldLabel: '".$value['label']."',width:500,height:150,value:\"".Ext_textarea_cleanText($value['value'])."\"})\n";
	}
	function Ext_textarea_beforesave($id) {
		return "";
	}
	function Ext_textarea_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_textarea_init ($id,$value) {
		return "";
	}
	function Ext_textarea_cleanText($str){
		$str = stripslashes($str);
		$str = str_replace('\\"','"',$str);
		$str = str_replace("\n","\\n",$str);
		$str = str_replace("\r","\\r",$str);
		$str = str_replace('"','\"',$str);
		return $str;
		
	}
?>