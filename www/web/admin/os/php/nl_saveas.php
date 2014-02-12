<?php
require_once('function.php');


$strId					= $_REQUEST['id'];
$aTmp						= split('/newsletter/',$_SERVER['SCRIPT_FILENAME']);
$strPathRoot		= $aTmp[0];
$strPathId			= $strPathRoot.'/newsletter/archives/'.$strId;

//$ Récupération du contenu
$strContent	= file_get_contents($strPathId.'/index.html');
if(!is_file($strPathId.'.zip'))
{
	CleanAndCreateArchive($strContent, $strId, $strPathRoot);
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="newsletter_'.$strId.'.zip"');
header('Content-Transfer-Encoding: binary');
readfile($strPathId.'.zip');
?>