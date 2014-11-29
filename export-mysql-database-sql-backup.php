======================USAGE=====================
EXPORT_TABLES("localhost","user","pass","db_name");  //to IMPORT, see the import.php file

<?php
function EXPORT_TABLES($host,$user,$pass,$name,  $tables=false, $backup_name=false )
{
	$link = mysqli_connect($host,$user,$pass,$name); if (mysqli_connect_errno()){ echo "ConnecttError: " . mysqli_connect_error();} mysqli_select_db($link,$name);	mysqli_query($link,"SET NAMES 'utf8'");
	$queryTables = mysqli_query($link,'SHOW TABLES'); while($row = mysqli_fetch_row($queryTables)) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, explode(',',$tables)); }
	
	$content='';    //start cycle
	foreach($target_tables as $table){
		$result	= mysqli_query($link,'SELECT * FROM '.$table); $fields_amount = mysqli_num_fields($result);$rows_num=mysqli_num_rows($result); $row2= mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table)); 
		$content	.= "\n\n".$row2[1].";\n\n";
		for ($i = 0; $i < $fields_amount; $i++) {
			$st_counter= 0;
			while($row = mysqli_fetch_row($result))	{
					//when started (and every after 100 command cycle)
					if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
				$content .= "\n(";
				for($j=0; $j<$fields_amount; $j++)  {
					$row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
					if (isset($row[$j])) { $content .= '"'.$row[$j].'"' ; } else { $content .= '""'; }
					if ($j<($fields_amount-1)) { $content.= ','; }
				}
				$content .=")";
					//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
					if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		}$content .="\n\n\n";
	}

	//save file
	$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
	header('Content-Type: application/octet-stream');	header("Content-Transfer-Encoding: Binary"); header("Content-disposition: attachment; filename=\"".$backup_name."\""); echo $content; exit;
}
?>
