<?php
	require_once("../../../php/const.php");
	
	if(!isset($_REQUEST['id']))
		exit();

	header("content-type: application/xml");
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	$sqlquery="SELECT id_page,id_contact_fk FROM site_page WHERE id_page=".$_REQUEST['id'];
	
	$sqlres=mysql_query($sqlquery);
	
	$row = mysql_fetch_object($sqlres);
	
?><message success="true">
	<page>
<?php
	print "\t\t<action>update</action>\n";
	foreach($row as $key=>$value){ 	
		print "\t\t<".$key."><![CDATA[".$value."]]></".$key.">\n";
	}
?>
	</page>
</message>

