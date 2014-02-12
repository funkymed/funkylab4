<?php 
	require_once("../../../php/const.php");
	
	$NomFichier="newsletter-contacts-".date("YmdHis").".xml";
	
	header("Content-Type: text/xml; charset=UTF-8;name=\"".$NomFichier."\"");
	header("Content-Disposition: attachment; filename=\"".$NomFichier."\"");

	$where = (isset($_SESSION['filter']) && isset($_SESSION['filter']['contacts'])) ? $_SESSION['filter']['contacts'] : '';

	print "<?xml version=\"1.0\"?>\n";
	print "<?mso-application progid=\"Excel.Sheet\"?>\n";
	
	$field_unwanted = array("id_contact","edit_creation","edit_user_fk","edit_date");
	
	$sqlquery="SELECT * FROM contacts ".$where." ORDER BY contact_nom,contact_prenom,contact_email ASC";
	$sqlres=mysql_query($sqlquery);
	$allResult=array();
	$libelles=false;
	$libeValues = array();
	$contents = array();
	while($row = mysql_fetch_object($sqlres)){
		$content=array();
		if($libelles==false){
			foreach($row as $key=>$value){ 
				if(!in_array($key,$field_unwanted)){
					$libeValues[]=$key; 
				}
			}
			$contents[]=$row;
			$libelles=true;
		}else{
			$contents[]=$row;
		}
	}

?><Workbook xmlns:msxsl="urn:schemas-microsoft-com:xslt"
	xmlns:user="urn:my-scripts"
	xmlns="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:x="urn:schemas-microsoft-com:office:excel"
	xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
	xmlns:html="http://www.w3.org/TR/REC-html40">
	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
			<Alignment ss:Vertical="Bottom"/>
			<Borders/>
			<Font/>
			<Interior/>
			<NumberFormat/>
			<Protection/>
		</Style>
		<Style ss:ID="s21" ss:Name="Hyperlink">
			<Font ss:Color="#000000" />
		</Style>
		<Style ss:ID="s23">
			<Font ss:Color="#555555" x:Family="Swiss" ss:Bold="1"/>
		</Style>
	</Styles>
	<Worksheet ss:Name="Names">
		<Table x:FullColumns="1" x:FullRows="1">
			<Row ss:Height="50">
			<?php
			  
			  foreach($libeValues as $lib){
				  $lib = str_replace("contact_","",$lib);
				  print "\t\t\t\t".'<Cell ss:StyleID="s23"><Data ss:Type="String">'.$lib.'</Data></Cell>'."\n";
			  }
			?>
			</Row>
			
			<?php 
				foreach($contents as $rows) { 
					print "\t\t\t<Row>\n";
					foreach($rows as $key=>$value) { 
						if(!in_array($key,$field_unwanted)){
							$value = ($value);
							$value = str_replace('"','',$value);
							print "\t\t\t\t".'<Cell ss:StyleID="s21"><Data ss:Type="String">'.$value.'</Data></Cell>'."\n";
						}
					}
					print "\t\t\t</Row>\n";
				} 
			?>
		</Table>
	</Worksheet>
</Workbook>