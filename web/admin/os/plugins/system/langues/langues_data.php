<?php
	header("content-type: application/xml");
	
	require_once("../../../php/const.php");
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	if(!isset($_REQUEST['id'])){
		exit();
	}
	

	$sqlquery="SELECT * FROM cms_pays WHERE  pays_libelle='".$_REQUEST['id']."'";
	$sqlres=mysql_query($sqlquery);
	$row = mysql_fetch_object($sqlres);


?><message success="true">
	<pays>
<?php
	print "\t\t<action>update</action>\n";
	foreach($row as $key=>$value){ 	
		print "\t\t<".$key."><![CDATA[".$value."]]></".$key.">\n";
	}
?>
	</pays>
</message>