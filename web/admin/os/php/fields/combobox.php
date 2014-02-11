<?php
	function Ext_combobox ($id,$value) {
		$var ="new Ext.form.ComboBox({";
		$var.="id:'".$id."',";
        $var.=",fieldLabel: '".$value['label']."'";
        $var.=",store: new Ext.data.SimpleStore({";
        $var.="fields: ['value', 'text'],";
        $var.="data: ".json_encode($value['values']);
	    $var.="})";
	    $var.=",editable:false";
        $var.=",valueField: 'value'";
	    $var.=",displayField: 'text'";
        $var.=",typeAhead: true";
        $var.=",mode: 'local'";
        $var.=",forceSelection: true";
        $var.=",triggerAction: 'all'";
        $var.=",emptyText:'Select...'";
        $var.=",selectOnFocus:true";
	    $var.="})";
		return $var;
	}
	
	function Ext_combobox_beforesave($id) {
		return "";
	}
	function Ext_combobox_save($id) {
		return "\t\t".$id.":Ext.getCmp('".$id."').getValue()";
	}
	function Ext_combobox_init ($id,$value) {
		return "";
	}
?>