========================= USAGE ======================//see the EXPORT.php too
IMPORT_TABLES("localhost","user","pass","db_name", "import.sql"); //TABLES ARE OVERWRITTEN

<?php
//https://github.com/tazotodua/useful-php-scripts
function IMPORT_TABLES($host,$user,$pass,$dbname,$sql_file){
	if (!file_exists($sql_file)) {die('Input the SQL filename correctly! <button onclick="window.history.back();">Click Back</button>');} $allLines = file($sql_file);
	$mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();} 
		$zzzzzz = $mysqli->query('SET foreign_key_checks = 0');	        preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n".file_get_contents($sql_file), $target_tables); foreach ($target_tables[2] as $table){$mysqli->query('DROP TABLE IF EXISTS '.$table);}         $zzzzzz = $mysqli->query('SET foreign_key_checks = 1');
	$mysqli->query("SET NAMES 'utf8'");							$templine = '';	// Temporary variable, used to store current query
	foreach ($allLines as $line)	{											// Loop through each line
		if (substr($line, 0, 2) != '--' && $line != '') {$templine .= $line; 	// (if it is not a comment..) Add this line to the current segment
			if (substr(trim($line), -1, 1) == ';') {		// If it has a semicolon at the end, it's the end of the query
				$mysqli->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');  $templine = '';// Reset temp variable to empty
			}
		}
	}	echo 'Importing finished. Now, Delete the import file.';
}
?>
