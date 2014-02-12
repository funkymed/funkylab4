<?php
	function Ext_htmleditor ($id,$value) {
		return "new Ext.form.TextArea ({id:'".$id."',fieldLabel: '".$value['label']."',width:500,height:150,value:\"".Ext_htmleditor_cleanText($value['value'])."\"})\n";
	}
	function Ext_htmleditor_beforesave($id) {
		return "if(Ext.getCmp('".$id."').getXType()!=\"textarea\") Ext.getCmp('".$id."').syncValue();\n"; 
	}
	function Ext_htmleditor_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_htmleditor_cleanText($str){
		$str = stripslashes($str);
		$str = str_replace('\\"','"',$str);
		$str = str_replace("\n","\\n",$str);
		$str = str_replace("\r","\\r",$str);
		$str = str_replace('"','\"',$str);
		return $str;
	}
	function Ext_htmleditor_init ($id,$value) {	
		$strParam=(isset($value['editoropt']))?"Ext.getCmp('".$id."').md_opteditor={".$value['editoropt']."};":"";
		return $strParam."Ext.getCmp('".$id."').on('focus',focushtmleditor);";
		//return "Ext.getCmp('".$id."').on('focus',focushtmleditorimage);";
	}
?>