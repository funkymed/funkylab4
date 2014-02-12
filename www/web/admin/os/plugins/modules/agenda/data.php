<?php
	require_once("../../../php/const.php");
	
	if(!isset($_REQUEST['id_agenda'])){
		exit();
	}
	header("content-type: application/xml");
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	$sqlquery="SELECT agenda.*";
	
	$sqlquery .= ",DATE_FORMAT(agenda_debut,'%Y-%m-%d') as agenda_debut";
	$sqlquery .= ",DATE_FORMAT(agenda_debut,'%H') as agenda_debutH";
	$sqlquery .= ",DATE_FORMAT(agenda_debut,'%i') as agenda_debutM";
	
	$sqlquery .= ",DATE_FORMAT(agenda_fin,'%Y-%m-%d') as agenda_fin";
	$sqlquery .= ",DATE_FORMAT(agenda_fin,'%H') as agenda_finH";
	$sqlquery .= ",DATE_FORMAT(agenda_fin,'%i') as agenda_finM";
	
	
	$sqlquery .= " FROM agenda WHERE id_agenda=".$_REQUEST['id_agenda'];
	$sqlres=mysql_query($sqlquery);
	$row = mysql_fetch_object($sqlres);
	
?><message success="true">
	<agenda>
<?php
	print "\t\t<action>update</action>\n";
	foreach($row as $key=>$value){ 	
		print "\t\t<".$key."><![CDATA[".$value."]]></".$key.">\n";
	}
?>
	</agenda>
</message>

