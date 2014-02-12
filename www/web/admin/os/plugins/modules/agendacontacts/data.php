<?php
	require_once("../../../php/const.php");
	
	if(!isset($_REQUEST['id'])){
		exit();
	}
	header("content-type: application/xml");
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	$sqlquery="SELECT * FROM contacts WHERE id_contact=".$_REQUEST['id'];
	$sqlres=mysql_query($sqlquery);
	
?><message success="true">
	<contact>
<?php
	if($row = mysql_fetch_object($sqlres)){
		print "\t\t<action>update</action>\n";
		foreach($row as $key=>$value){ 	
			print "\t\t<".$key."><![CDATA[".$value."]]></".$key.">\n";
		}
	}
?>
	</contact>
</message>

