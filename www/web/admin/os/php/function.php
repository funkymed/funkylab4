<?php
function CleanAndCreateArchive($strContent, $strId, $strRootPth)
{
	//$ Initialisation
	$strNlPath	= $strRootPth.'/newsletter/';
	$strIdPath	= $strRootPth.'/newsletter/archives/'.$strId.'/';
	$aFile			= array();
	
	$directoryImage = "image-".$strId."/";
	$rootImage	= "http://devel.sfr.com/fileadmin/mes_documents/newsletter/".$directoryImage;
	
	//$ Rechercher les informations externes
	$aExtType = array('src','background');
	for($iExt = 0; $iExt < count($aExtType); $iExt++)
	{
		preg_match_all("/ ".$aExtType[$iExt]."=\"(http[^\"]*)/", $strContent, $matches, PREG_SET_ORDER);
		foreach ($matches as $val)
		{
			$aTmp	=	split('\.',$val[1]);
			$strExtension = $aTmp[count($aTmp)-1];
			$strName = date('U').rand(1000,9999) . '.' . strtolower($strExtension);
			$aTmpPath = split('/newsletter/',$val[1]);
			$strFromFile = $strNlPath.$aTmpPath[1];

			if(is_file($strIdPath.$strName))unlink($strIdPath.$strName);
			copy($strFromFile, $strIdPath.$strName);
			$strContent=ereg_replace($val[0], ' '.$aExtType[$iExt].'="'.$strName, $strContent);
			$aFile[] = $strName;
		}
	}

	
	if(is_file($strIdPath."/index.html")){
		rename($strIdPath."/index.html",$strIdPath."/archive_index.html");
	}else{
		file_put_contents($strIdPath."/archive_index.html",$strContent);	
	}
	
		
	foreach($aFile as $file){
		$strContent = str_replace('="'.$file,'="'.$rootImage.$file,$strContent);
	}

	
	//$ Enregistrement de la page
	file_put_contents($strIdPath."/index.html",$strContent);
	
	
	//$ Nettoyer le répertoire
	if ($dh = opendir($strIdPath))
	{
		while (($file = readdir($dh)) !== false)
		{
			if(($file != '.') && ($file != '..') && ($file != 'index.html') && ($file != 'archive_index.html') && (!in_array($file,$aFile)))
			{
				if(is_file($strIdPath.$file))unlink($strIdPath.$file);
			}
		}
		closedir($dh);
	}

	
	//$ Creation du zip
	$zip = new ZipArchive();
	$strIdPath = ereg_replace('\/$','',$strIdPath);
	if ($zip->open($strIdPath.'.zip', ZIPARCHIVE::CREATE)!==TRUE)
	{
		throw new Exception("cannot créate <$strIdPath.zip>\n");
	}
	for($i = 0; $i < count($aFile); $i++)
	{
		$zip->addFile($strIdPath.'/'.$aFile[$i], $directoryImage.$aFile[$i]);
	}
	$zip->addFile($strIdPath.'/index.html', "index.html");
	$zip->close();
	
	if(is_file($strIdPath."/index.html"))unlink($strIdPath."/index.html");
	rename($strIdPath."/archive_index.html",$strIdPath."/index.html");
}
?>