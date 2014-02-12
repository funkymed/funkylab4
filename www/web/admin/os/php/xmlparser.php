<?php
	
	class XMLParser {
	    var $data;
	    var $vals;
	    var $collapse_dups;
	    var $index_numeric;
	    
	    function XMLParser($data_source, $data_source_type='raw', $collapse_dups=0, $index_numeric=0) {
	        $this->collapse_dups = $collapse_dups;
	        $this->index_numeric = $index_numeric;
	        $this->data = '';
	        if ($data_source_type == 'raw')
	            $this->data = $data_source;
	
	        elseif ($data_source_type == 'stream') {
	            while (!feof($data_source))  
	                $this->data .= fread($data_source, 1000);
	
	        } elseif (file_exists($data_source))
	            $this->data = implode('', file($data_source));
	
	        else {
		        if(is_file($data_source)){
		            $fp = fopen($data_source,'r');
		            while (!feof($fp))
		                $this->data .= fread($fp, 1000);
		            fclose($fp);
	            }
	        }
	    }
	
		function getJson($arr=null) {
			if (!is_array($arr)){
				$arr=$this->getTree();
			}
		    $parts = array();
		    $is_list = false;
		    $keys = array_keys($arr);
		    $max_length = count($arr)-1;
		    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {
		        $is_list = true;
		        for($i=0; $i<count($keys); $i++) {
		            if($i != $keys[$i]) { 
		                $is_list = false;
		                break;
		            }
		        }
		    }
		    foreach($arr as $key=>$value) {
		        if(is_array($value)) { 
		            if($is_list) $parts[] = $this->getJson($value);
		            else $parts[] = '"' . $key . '":' . $this->getJson($value);
		        } else {
		            $str = '';
		            if(!$is_list) $str = '"' . $key . '":';
		            if(is_numeric($value)) $str .= $value;
		            elseif($value === false) $str .= 'false';
		            elseif($value === true) $str .= 'true';
		            else $str .= '"' . addslashes($value) . '"';
		            $parts[] = $str;
		        }
		    }
		    $json = implode(',',$parts);
		    if($is_list) return '[' . $json . ']';
		    return '{' . $json . '}';
		} 
	    
	    function getTree() {
	        $parser = xml_parser_create('ISO-8859-1');
	        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	        xml_parse_into_struct($parser, $this->data, $vals, $index);
	        xml_parser_free($parser);
	
	        $i = -1;
	        return $this->getchildren($vals, $i);
	    }
	
	    function buildtag($thisvals, $vals, &$i, $type) { 
	        $tag = array();
	
	        if (isset($thisvals['attributes']))
	            $tag['ATTRIBUTES'] = $thisvals['attributes'];
	
	        if ($type === 'complete')
	            $tag['VALUE'] = isset($thisvals['value']) ? $thisvals['value'] : '';
	
	        else
	            $tag = array_merge($tag, $this->getchildren($vals, $i));
	
	        return $tag;
	    }
	
	    function getchildren($vals, &$i) { 
	        $children = array();
	
	        if ($i > -1 && isset($vals[$i]['value']))
	            $children['VALUE'] = $vals[$i]['value'];
	
	        while (++$i < count($vals)) {
	
	            $type = $vals[$i]['type'];
	
	            if ($type === 'cdata')
	                $children['VALUE'] .= $vals[$i]['value'];
	
	            elseif ($type === 'complete' || $type === 'open') {
	                $name = $vals[$i]['tag'];
	                $tag = $this->buildtag($vals[$i], $vals, $i, $type);
	                if ($this->index_numeric) {
	                    $tag['TAG'] = $name;
	                    $children[] = $tag;
	                } else
	                    $children[$name][] = $tag;
	            }
	
	            elseif ($type === 'close')
	                break;
	        }
	        if ($this->collapse_dups)
	            foreach($children as $key => $value)
	                if (is_array($value) && (count($value) == 1))
	                    $children[$key] = $value[0];
	        return $children;
	    } 
	}
	
?>