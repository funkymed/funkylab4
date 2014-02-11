<?php

	function full_copy( $source, $target )
    {
        if ( is_dir( $source ) )
        {
            @mkdir( $target);
            chmod($target,0777);
            $d = dir( $source );
           
            while ( FALSE !== ( $entry = $d->read() ) )
            {
                if ( $entry == '.' || $entry == '..' )
                {
                    continue;
                }
               
                $Entry = $source . '/' . $entry;           
                if ( is_dir( $Entry ) )
                {
	                chmod($Entry,0777);
                    full_copy( $Entry, $target . '/' . $entry );
                    continue;
                }
                copy( $Entry, $target . '/' . $entry );
                
            }
           
            $d->close();
        }else
        {
            copy( $source, $target );
        }
    }
    
	function getMultiTab($table,$values,$idkey,$titlekey){
		
		$allDesc=array();
		
		if($values!=false){
			$sqlquery="SELECT ".$idkey.",agenda_type,".$titlekey." FROM ".$table." WHERE ".$idkey." NOT IN (".$values.") AND agenda_online=1 AND  agenda_state='published' AND agenda_fin>=now() ORDER BY agenda_type,".$titlekey;
		}else{
			$sqlquery="SELECT ".$idkey.",agenda_type,".$titlekey." FROM ".$table." WHERE agenda_online=1 AND  agenda_state='published' AND agenda_fin>=now() ORDER BY agenda_type,".$titlekey;
		}
		
	
		
		$Results = mysql_query( $sqlquery );
		while($row = mysql_fetch_object($Results)) {
			$allDesc[]=array($row->$idkey,$row->agenda_type." / ".$row->$titlekey);
		}	
		if(count($allDesc)>0){
			$descNotSelected=json_encode($allDesc);
		}else{
			$descNotSelected="[]";
		}
		
		if($values!=false){
			$allDesc=array();
			
			$v = explode(",",$values);
			foreach($v as $val){
				$sqlquery="SELECT ".$idkey.",agenda_type,".$titlekey." FROM ".$table." WHERE ".$idkey." = ".$val." ORDER BY agenda_type, ".$titlekey." LIMIT 1";
				$Results = mysql_query( $sqlquery );
				if($row = mysql_fetch_object($Results)){
					$allDesc[]=array($row->$idkey,$row->agenda_type." / ".$row->$titlekey);
				}
			}
			
			if(count($allDesc)>0){
				$descSelected=json_encode($allDesc);
			}else{
				$descSelected="[]";
			}
		}else{
			$descSelected="[]";
		}
		
		return array($descNotSelected,$descSelected);
	}
	
	function getList($table,$idkey,$titlekey){
		$allDesc=array();
		$sqlquery="SELECT ".$idkey.",".$titlekey." FROM ".$table." WHERE pays_libelle='".$_SESSION[sessionName]['user']['languecms']."' ORDER BY ".$titlekey;
		$Results = mysql_query( $sqlquery );
		while($row = mysql_fetch_array($Results)) {
			$allDesc[]=array($row[$idkey],$row[$titlekey]);
		}	
		if(count($allDesc)>0){
			$descNotSelected=json_encode($allDesc);
		}else{
			$descNotSelected="[]";
		}
		return $descNotSelected;
	}
	
	
 	function dircopy($src_dir, $dst_dir,$UploadDate=false, $verbose = false, $use_cached_dir_trees = false){  
        static $cached_src_dir;
        static $src_tree;
        static $dst_tree;
        $num = 0;

        if(($slash = substr($src_dir, -1)) == "\\" || $slash == "/") $src_dir = substr($src_dir, 0, strlen($src_dir) - 1);
        if(($slash = substr($dst_dir, -1)) == "\\" || $slash == "/") $dst_dir = substr($dst_dir, 0, strlen($dst_dir) - 1);
        if (!$use_cached_dir_trees || !isset($src_tree) || $cached_src_dir != $src_dir)
        {
            $src_tree = get_dir_tree($src_dir,true,$UploadDate);
            $cached_src_dir = $src_dir;
            $src_changed = true;
        }
        if (!$use_cached_dir_trees || !isset($dst_tree) || $src_changed)
            $dst_tree = get_dir_tree($dst_dir,true,$UploadDate);
        if (!is_dir($dst_dir)) mkdir($dst_dir, 0777, true);

          foreach ($src_tree as $file => $src_mtime)
        {
            if (!isset($dst_tree[$file]) && $src_mtime === false)
                mkdir("$dst_dir/$file");
            elseif (!isset($dst_tree[$file]) && $src_mtime || isset($dst_tree[$file]) && $src_mtime > $dst_tree[$file]) 
            {
                if (copy("$src_dir/$file", "$dst_dir/$file"))
                {
                    if($verbose) echo "Copied '$src_dir/$file' to '$dst_dir/$file'<br>\r\n";
                    touch("$dst_dir/$file", $src_mtime);
                    $num++;
                } else
                    echo "<font color='red'>File '$src_dir/$file' could not be copied!</font><br>\r\n";
            }      
        }
        return $num;
    }

    function get_dir_tree($dir, $root = true,$UploadDate)
    {
        static $tree;
        static $base_dir_length;
     
        if ($root)
        {
            $tree = array();
            $base_dir_length = strlen($dir) + 1;
        }

        if (is_file($dir))
        {
           if($UploadDate!=false)
            {
                   if(filemtime($dir)>strtotime($UploadDate))
                    $tree[substr($dir, $base_dir_length)] = date('Y-m-d H:i:s',filemtime($dir));   
            }
            else
                $tree[substr($dir, $base_dir_length)] = date('Y-m-d H:i:s',filemtime($dir));
        }
        elseif ((is_dir($dir) && substr($dir, -4) != ".svn") && $di = dir($dir) )
        {
            if (!$root) $tree[substr($dir, $base_dir_length)] = false;
            while (($file = $di->read()) !== false)
                if ($file != "." && $file != "..")
                    get_dir_tree("$dir/$file", false,$UploadDate);
            $di->close();
        }
        if ($root)
            return $tree;   
    }
?>