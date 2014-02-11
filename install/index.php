<?php
	require_once('lib/pclzip.lib.php');
	$archive = new PclZip("install.zip");
	$newfolder="./";
	unlink("index.php");
	if ($archive->extract(PCLZIP_OPT_PATH, ''.$newfolder.'')) {
		unlink("install.zip");
		header("location:index.php");
	}
?> 