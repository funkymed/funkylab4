<?php
	class ArrayToXML{
		
		function toXml($data, $rootNodeName = 'response', $xml=null){
			
			if ($xml == null){
        		$xml="<?xml version='1.0' encoding='utf-8'?>";
        		$xml.="<".$rootNodeName.">";
        		$footer=true;
			}
			
			// loop through the data passed in.
			foreach($data as $key => $value){
				// no numeric keys in our xml please!
				if (is_numeric($key) || is_int($key)){
					// make string key...
					$key = "unknownNode_". (string) $key;
					if(is_array($value)){
						foreach($value as $_key => $_value){
							$key = $_key;
							$value = $_value;
						}
					}
				}
				// replace anything not alpha numeric
				$key = preg_replace('/[^a-z_0-9]/i', '', $key);
				// if there is another array found recrusively call this function
				if (is_array($value)){
					
					$xml.="<".$key.">";
					$xml.=ArrayToXML::toXml($value, $key, $xml);
					$xml.="</".$key.">";
					
 				}else if (is_object($value)){
	 			
	 				
	 				$subnode=array();
	 				$xml.="<".$key;
					foreach($value as $k=>$v){
						if (is_array($v)){
							$subnode[$k]= $v;
						}else{
							$xml.=' '.$k.'="'.$v.'"';
						}
					}
					$xml.=">";
					foreach($subnode as $k=>$v){
						$xml.=ArrayToXML::toXml($v, $k, " " );
					}
					
					$xml.="</".$key.">";
					
				}else{
					$xml.="<".$key."><![CDATA[".$value."]]></".$key.">";
				}
			}
			// pass back as string. or simple xml object if you want!
			if(isset($footer)) $xml.="</".$rootNodeName.">";
			return $xml;
		}
	}
?>