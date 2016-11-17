<?ph	// https://github.com/tazotodua/useful-php-scripts  
	// EXAMPLE:	IMPORT_TABLES("localhost","user","pass","db_name", "my_baseeee.sql"); //TABLES WILL BE OVERWRITTEN
				//optional: 6th parameter-> array('OLD_DOMAIN.com','NEW_DOMAIN.com') to adequatelly replace strings in DB!! ( MUST READ!!!! - https://goo.gl/2fZDQL ) 

function IMPORT_TABLES($host,$user,$pass,$dbname, $sql_file_OR_content,      $replacements=array('','') ){
	set_time_limit(3000);  if(
	$SQL_CONTENT = (strlen($sql_file_OR_content) > 300 ?  $sql_file_OR_content : file_get_contents($sql_file_OR_content)  );        if (function_exists('DOMAIN_or_STRING_modifier_in_DB')) { $SQL_CONTENT = DOMAIN_or_STRING_modifier_in_DB($replacements[0], $replacements[1], $SQL_CONTENT); }
	$allLines = explode("\n",$SQL_CONTENT); 
	$mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();} 
		$zzzzzz = $mysqli->query('SET foreign_key_checks = 0');	        preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n". $SQL_CONTENT, $target_tables); foreach ($target_tables[2] as $table){$mysqli->query('DROP TABLE IF EXISTS '.$table);}         $zzzzzz = $mysqli->query('SET foreign_key_checks = 1');    $mysqli->query("SET NAMES 'utf8'");	
	$templine = '';	// Temporary variable, used to store current query
	foreach ($allLines as $line)	{											// Loop through each line
		if (substr($line, 0, 2) != '--' && $line != '') {$templine .= $line; 	// (if it is not a comment..) Add this line to the current segment
			if (substr(trim($line), -1, 1) == ';') {		// If it has a semicolon at the end, it's the end of the query
				$mysqli->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');  $templine = ''; // set variable to empty, to start picking up the lines after ";"
			}
		}
	}	return 'Importing finished. Now, Delete the import file.';
}   //see also export.php 
?>
