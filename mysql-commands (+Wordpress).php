========== MYSQLI vs PDO ============
------------- http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059
----------OR
------------- http://php.net/manual/en/mysqlinfo.api.choosing.php

p.s.(MYSQLI has two ways of execution- Object Oriented and Procedural: http://php.net/manual/en/mysqli.error.php )
==================================


============================================
*MYSQLI commands  :
	(DIRECT): http://www.pantz.org/software/mysql/mysqlcommands.html
	(WORDPRESS): http://codex.wordpress.org/Class_Reference/wpdb#Examples 	//(in Wordpress,before starting commands, you need to use: global $wpdb;)
============================================


<?php
//=================================CONENCT TO MYSQL	
 	**********DIRECT**********
	$CONNECTED = new mysqli($host,$user,$pass,$DBname);

	**********WODRPRESS**********	
	global $wpdb;  //(this object will be already connected..)
  
  
  		
	=================== NOW, for example,  you can execute the queries :=======================
	$command= "Create Database my_database";
	
				**********DIRECT**********
					$zzzzzz = $CONNECTED->query($command);
	
				**********WODRPRESS**********
					$zzzzzz = $wpdb->query($command);
   
   
   
   
//================================= CREATE  DATABASE	=================================		
	$command="CREATE DATABASE my_database";
//================================= DELETE  DATABASE	=================================	
  	$command="DROP DATABASE my_database";
//================================= CREATE sample TABLE	=================================
	$command="CREATE TABLE IF NOT EXISTS `aa_tasks_table2` (
		`IDD` int(11) NOT NULL AUTO_INCREMENT,
		`userid` int(11) NOT NULL,
		`mycolumn1` varchar(150) NOT NULL,
		`mycolumn2` LONGTEXT DEFAULT '' NOT NULL,
		`mycolumn3` LONGTEXT DEFAULT '' CHARACTER SET utf8 NOT NULL,
		`mytime` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  PRIMARY KEY (`IDD`),
	  UNIQUE KEY `IDD` (`IDD`)
	)  ENGINE=InnoDB  DEFAULT CHARSET=utf8		AUTO_INCREMENT=10; "; 
	//........................CHARSET=latin1	AUTO_INCREMENT=1;
	//!!!!!!!!!!!!!!!!Check your database and make sure the whole database + tables + fields have the same charset!!!!!!!!!!!!!!!!!

	p.s. For Wordpress, there may be needed:   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); dbDelta("CREATE TABLE..........");
	

//================================= DELETE  TABLES		=================================
			if ($result = $CONNECTED->query("SHOW TABLES"))	{	
				$zzzzzz = $CONNECTED->query('SET foreign_key_checks = 0');
				while($row = $zzzzzz->fetch_array()) {$CONNECTED->query('DROP TABLE IF EXISTS '.$row[0]);}
				$zzzzzz = $CONNECTED->query('SET foreign_key_checks = 1');
			}
			
   
//=================================INSERT	=================================	
	$command="INSERT INTO my_tablee (Mycolumn_1, Mycolumn_2) VALUES ('aaaaaa', 'tttttttt')";
//=================================UPDATE=================================
	$command="UPDATE my_tablee SET Age=36 WHERE FirstName='Peter' AND LastName='Griffin'";
		//*****REPLACE existing values into columns****
		$command="UPDATE my_tablee set FIELD_NAME = replace( FIELD_NAME, 'what', 'by what' )";
//================================= DELETE	=================================		
	$command="DELETE FROM my_tablee WHERE post_status = 'www'";
//=================================SELECT=================================
	**********DIRECT
	$zzzzzz = $CONNECTED->query("SELECT `Mycolumn_1` from `my_tablee` WHERE Mycolumn_2 = 'excerpt' ");
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

p.s. during the command execution, you can see errors like this (if there will be any errors):
	**********DIRECT
	  query(....) or die($mysqli->error);
	
	**********WODRPRESS
	  query(....) or die($wpdb->last_error);
					
			
			
			
			
			

//===========================================================================================//
//================================= SOME LIVE EXAMPLES ======================================//
//===========================================================================================//
//1 ) UPDATE row(but if ROW doesnt exist, then Insert the new one)

		$value1	=stripslashes("my Market office");	$value2	=stripslashes("consultant");	 $useriid = 12;
			
		//////Method 1//////
		$CONNECTED->query("UPDATE my_tablename SET	content1='$value1',content2='$value2'		WHERE userid = '$useriid'")
		or 
		$CONNECTED->query("INSERT INTO my_tablename (content1, content2, userid) VALUES ('$value1', '$value2','$useriid')");
		
			
						//////Method 2 (only for  wordpress, sanitized)//////
						$CheckIfExists = $wpdb->get_var("SELECT id FROM my_tablename WHERE userid = '$useriid'");
						if (!empty($CheckIfExists)) {
							$wpdb->update($tablename, 
								array('mycolumn_1'=>$value1, 'mycolumn_2'=>$value2),
								array('userid'=> $useriid)
							);
						}
						else 	{
							$wpdb->insert($tablename, 
								array('mycolumn_1'=>$value1, 'mycolumn_2'=>$value2,	'userid'=> $useriid,), 
								array('%s','%s','%s') 
							);
						}
		
