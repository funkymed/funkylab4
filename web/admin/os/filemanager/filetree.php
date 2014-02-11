<?php
	$offset="../../../";


	if (!function_exists('json_encode')){
		require_once("json.php");
		function json_encode($a=false){
			if($a!=false){
				$json = new Services_JSON();
				return $json->encode($a);
			}
		}
	}
	
	function formatBytes($val, $digits = 3, $mode = "SI", $bB = "B"){ //$mode == "SI"|"IEC", $bB == "b"|"B"
	   $si = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
	   $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
	   switch(strtoupper($mode)) {
	       case "SI" : $factor = 1000; $symbols = $si; break;
	       case "IEC" : $factor = 1024; $symbols = $iec; break;
	       default : $factor = 1000; $symbols = $si; break;
	   }
	   switch($bB) {
	       case "b" : $val *= 8; break;
	       default : $bB = "B"; break;
	   }
	   for($i=0;$i<count($symbols)-1 && $val>=$factor;$i++)
	       $val /= $factor;
	   $p = strpos($val, ".");
	   if($p !== false && $p > $digits) $val = round($val);
	   elseif($p !== false) $val = round($val, $digits-$p);
	   return round($val, $digits) . " " . $symbols[$i] . $bB;
	}
	
	function EffacerAllFile($path){
	    if ($path[strlen($path)-1] != "/"){
	        $path .= "/";	
        }        
	    if (is_dir($path)){
	        $d = opendir($path);
	        while ($f = readdir($d)){
	            if ($f != "." && $f != ".."){
	               $rf = $path . $f;
	                if (is_dir($rf)){
	                    EffacerAllFile($rf);
                	}else{
	                    unlink($rf);
                    }
	            }
	        }	        
			closedir($d);	
			rmdir($path);		     
	    }
	}



//------------------------------------------------------
	if(isset($_POST['cmd'])){
		switch($_POST['cmd']){
			case 'newdir':
				if(mkdir($offset.$_POST['dir'])){
					chmod($offset.$_POST['dir'],0777);
					print '{"success":true}';
				}else{
					print '{"success":false,"error":"Impossible de creer le dossier '.$_POST['dir'].'"}';
				}
				break;
			case 'rename':
			
				if(rename($offset.$_POST['oldname'],$offset.$_POST['newname'])){
					print '{"success":true}';
				}else{
					print '{"success":false,"error":"Impossible de renommer le fichier '.$_POST['oldname'].' en '.$_POST['newname'].'"}';
				}
				break;
			case 'delete':
			
				if(is_file($offset.$_POST['file'])){
					if(unlink($offset.$_POST['file'])){
						print '{"success":true}';
					}else{
						print '{"success":false,"error":"Impossible d\'effacer le fichier '.$_POST['file'].'"}';
					}
				}else if (is_dir($offset.$_POST['file'])){
					EffacerAllFile($offset.$_POST['file']);
					print '{"success":true}';
				}
				break;
			case 'upload':
				$error=false;
 				foreach($_FILES as $key=>$value){
 				 	if(move_uploaded_file($value['tmp_name'],$offset.$_POST['path']."/".$value['name'])){
	 				 	print '{"success":true}';
	 				 	chmod($offset.$_POST['path']."/".$value['name'],0664);
 				 	}else{
	 					print '{"success":false,"error":{"'.$key.'":"Erreur de chargement"}}'; 	
 				 	}
			 	}
 			 	
				break;
			case 'zip':
// 				$dir_root="../../../../";
// 				$dirinclude="includes/php/filemanager/";
// 				require_once('pclzip.lib.php');
// 				$file=$_GET['file'];
// 				$dir=$dir_root.$_GET['dir'].'/';
// 				
// 				if (isset($_GET['dirname'])){
// 					$archive = new PclZip($dir.$_GET['dirname'].'.zip');
// 					$v_list = $archive->create($dir,
// 			                            PCLZIP_OPT_REMOVE_PATH, $dir);
// 				}else{
// 					$archive = new PclZip($dir.$file.'.zip');
// 					$v_list = $archive->create($dir.$file,
// 			                            PCLZIP_OPT_REMOVE_PATH, $dir);
// 				}
// 				if ($v_list == 0) {
// 					echo 'error';
// 					die("Error : ".$archive->errorInfo(true));
// 				}else{
// 					echo 'ok';
// 				}
				break;
			case 'unzip':
// 				$dir_root="../../../../";
// 				$dirinclude="includes/php/filemanager/";
// 				require_once('pclzip.lib.php');
// 				$file=$dir_root.$_GET['file'];	
// 				$fileNewDir=substr($file,0,strlen($file)-4);
// 				$archive = new PclZip(''.$file);
// 				$return =@mkdir ($fileNewDir, 0777);		
// 				if ($archive->extract(PCLZIP_OPT_PATH, $fileNewDir) == 0) {
// 					die("Error : ".$archive->errorInfo(true));
// 				}
				break;
			case 'get':
				if(isset($_POST['path'])){
					$path = $offset.$_POST['path'];
					$paths = array();
					$d = dir($path);
					while($f = $d->read()){
						if($f == '.' || $f == '..' || substr($f, 0, 1) == '.')continue;
						if(is_dir($path.'/'.$f)){
							$paths[] = array(
								'text'=>$f,
								'iconcls'=>'folder',
								'disabled'=>false,
								'leaf'=>false
							);
						}else{
							$size = formatBytes(filesize($path.'/'.$f), 2);
							$ext = strtolower(strrchr($f,"."));
							$ext = substr($ext,1,strlen($ext));
							
							if(strstr($f,"thumb_")==""){
							
								$paths[] = array(
									'text'=>$f, 
									'iconCls'=>'file-'.$ext,
									'disabled'=>false,
									'leaf'=>true,
									'qtip'=>$size,
									'fileurl'=>$_POST['path'].'/'.$f
								);
							}
						}
					}
					
					$d->close();
					print json_encode($paths);
				}else{
					print '{"success":false,"error":"Aucun argument"}';
				}
				break;
			case 'download':
				print $offset.$_POST['path'].'/'.$f;
				break;
			
		}
	}else{
		print '{"success":false,"error":"Aucun argument"}';
	}
	
?>