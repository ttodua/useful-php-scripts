*MYSQLI vs PDO:     http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059 (OR ttp://php.net/manual/en/mysqlinfo.api.choosing.php)
*MYSQLI commands:   http://www.pantz.org/software/mysql/mysqlcommands.html  [MYSQLI has 2 ways of execution- Object Oriented and Procedural (example: http://php.net/manual/en/mysqli.error.php )]
	

<?php
//=================================CONENCT TO MYSQL	
 	**********TYPICAL**********
	$MANUAL = new mysqli($host,$user,$pass,$DBname);

	**********FOR WODRPRESS**********	
	global $wpdb;  //(in Wordpress,before starting your SQL commands, you need to global that only once
  
  
  		
	=================== Example of Query execution :=======================
				**********TYPICAL QUERY**********
					$zzzzzz = $MANUAL->query($command);
				**********WODRPRESS QUERY**********
					$zzzzzz = $wpdb->query($command);
   
   
   
   
//================================= CREATE  DATABASE	=================================		
	$command="CREATE DATABASE my_database";
//================================= DELETE  DATABASE	=================================	
  	$command="DROP DATABASE my_database";
//================================= CREATE sample TABLE	=================================
	$command="CREATE TABLE IF NOT EXISTS `aa_my_table2` (
		`IDD` int(11) NOT NULL AUTO_INCREMENT,
		`userid` int(11) NOT NULL,
		`mycolumn1` varchar(150) NOT NULL,
		`mycolumn2` LONGTEXT NOT NULL DEFAULT '',
		`mycolumn3` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT '',
		`mytime` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY (`IDD`),
		UNIQUE KEY `IDD` (`IDD`)
	)   ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ;"; 
	//i.e......................................CHARSET=latin1 COLLATE=utf8_general_ci;
	//!!!!!!!!!!!!!!!!Check your database and make sure the whole database + tables + fields have the same charset!!!!!!!!!!!!!!!!!
	//p.s. If your Mysql doesnt support "InnoDB", then use "MyISAM"...    you can find out the InnoDB support with this automatic command:  $myType= ($wpdb->get_results("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'")[0]->SUPPORT) ? "InnoDB" : "MyISAM");
	
	
	//p.s. For Wordpress, there can be used this too: (more at http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table) :
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); dbDelta("CREATE TABLE..........");
 

	

//================================= DELETE  TABLES	=================================
			if ($result = $MANUAL->query("SHOW TABLES"))	{	
				$zzzzzz = $MANUAL->query('SET foreign_key_checks = 0');
				while($row = $zzzzzz->fetch_array()) {$MANUAL->query('DROP TABLE IF EXISTS '.$row[0]);}
				$zzzzzz = $MANUAL->query('SET foreign_key_checks = 1');
			}
//=================================INSERT=================================	
	$command="INSERT INTO my_tablee (Mycolumn_1, Mycolumn_2) VALUES ('aaaaaa', 'tttttttt')";
//=================================UPDATE=================================
	$command="UPDATE my_tablee SET Age=36 WHERE FirstName='Peter' AND LastName='Griffin'";
		//*****REPLACE existing values into columns****
		$command="UPDATE my_tablee set FIELD_NAME = replace( FIELD_NAME, 'what', 'by what' )";
//=================================DELETE=================================		
	$command="DELETE FROM my_tablee WHERE post_status = 'www'";
//=================================SELECT=================================
	**********DIRECT
	$zzzzzz = $MANUAL->query("SELECT `Mycolumn_1` from `my_tablee` WHERE Mycolumn_2 = 'excerpt' ");
	while ($row = $zzzzzz->fetch_array($zzzzzz)) 	{ 
		echo $row['Mycolumn_1'];
	}
	
	**********WODRPRESS
	$zzzzzz = $wpdb->get_results("SELECT `Mycolumn_1` from `my_tablee` WHERE Mycolumn_2 = 'excerpt' ");
	foreach ($zzzzzz as $row) {
		echo $row->Mycolumn_1;
	}
			**********WHEN NEED TO GET ONLY ONE RESULT**
			$wpdb->get_var("SELECT Mycolumn_1 FROM my_tablee WHERE post_type = 'smtnhnnng" );
//====================================================================================			

p.s. during the command execution, you can enable to show error reports(in case they happens):
	**********DIRECT
	  ->query(....);  if ($mysqli->error) die($mysqli->error);
	
	**********WODRPRESS
	  ->query(....);  if ($wpdb->last_error) die($wpdb->last_error);

p.s. for Wordpress, for secutiry, its better to use "PREPARE" function inside the query: $wpdb->query($wpdb->prepare("INSERT .....", null));     [ more at: http://codex.wordpress.org/Class_Reference/wpdb#Examples	]
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			

//===========================================================================================//
//================================= SOME LIVE EXAMPLES ======================================//
//===========================================================================================//
//1 ) UPDATE row(but if ROW doesnt exist, then Insert the new one)

		$value1	=stripslashes("my Market office");	$value2	=stripslashes("consultant");	 $useriid = 12;
			
		//////Method 1//////
		$MANUAL->query("UPDATE my_tablename SET	content1='$value1',content2='$value2'		WHERE userid = '$useriid'")
		or 
		$MANUAL->query("INSERT INTO my_tablename (content1, content2, userid) VALUES ('$value1', '$value2','$useriid')");
		
			
					//////Method 2 (only for  wordpress, sanitized)////// (check Updates:::: https://github.com/tazotodua/useful-php-scripts/blob/master/mysql-commands%20%28%2BWordpress%29.php )
					public function UPDATE_OR_INSERT($tablename, $NewArray, $WhereArray=array()){	  global $wpdb; 
						$array_for_check =   !empty($WhereArray) ? $WhereArray :  $NewArray;
						$arrayNames= array_keys($array_for_check);
						//convert array to STRING
						$o=''; $i=0; foreach ($array_for_check as $key=>$value){$i++; $o .= $key . " = ". (is_numeric($value) ? $value : "'".addslashes($value)."'"); if ($i != count($array_for_check)) { $o .=' AND ';}  }
						//check if already exists
						$CheckIfExists = $wpdb->get_var("SELECT ".$arrayNames[0]." FROM $tablename WHERE $o");
						return (  empty($CheckIfExists) ?         $wpdb->insert($tablename, array_merge($WhereArray, $NewArray))     :    $wpdb->update($tablename, $NewArray, $array_for_check)    );  
					}
					
					**EXECUTE**
					UPDATEE_OR_INSERTTT('myyy_tableee', 
							array('mycolumn_1'=> 'Hello World' ),
							array('mycolumn_5'=> 'Gonzales', 'mycolumn_6'=> 'France' ) );
																		  
																		  
																		  
																		  
																		  
																		  
																		  
																		  


//===========================================================================================//
//================================= Commands for VPS  ======================================//
//===========================================================================================//
																		  ::export::

::for export::
mysqldump -u USERNAME -p DBNAME [table1 table2] > "/var/www/example.sql"

::for import::
mysqldump -u USERNAME -p DBNAME [table1 table2] < "/var/www/example.sql"
