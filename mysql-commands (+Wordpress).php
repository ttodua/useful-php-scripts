==========references ============
*MYSQLI vs PDO : 
---------- http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059
---------- http://php.net/manual/en/mysqlinfo.api.choosing.php


============================================
*mysqli commands(DIRECT): http://www.pantz.org/software/mysql/mysqlcommands.html
*mysqli commands(WORDPRESS) http://codex.wordpress.org/Class_Reference/wpdb#Examples 
	//Notice: (with wordpress,before starting commands, you need to use: global $wpdb;)
============================================


<?php
//=================================CONENCT TO MYSQL	

 	**********DIRECT	
	$CONNECTED = mysqli_connect($host,$user,$pass,$dbname); if (mysqli_connect_errno()){ echo "ConnecttError: " . mysqli_connect_error();} mysqli_select_db($CONNECTED,$name);

	**********WODRPRESS	
	global $wpdb;  (this object will be already connected..)
  
   
   
//================================= CREATE sample DATABASE		=================================		
	**********DIRECT	
	$zzzzzz = $CONNECTED->query('CREATE DATABASE my_database');

	**********WODRPRESS	
	$zzzzzz = $wpdb->query("CREATE DATABASE my_database" );


	
//================================= DELETE  DATABASE		=================================	
  	**********DIRECT	
	$zzzzzz = $CONNECTED->query('DROP DATABASE my_database');

	**********WODRPRESS	
	$zzzzzz = $wpdb->query("DROP DATABASE my_database" );
 
   
   OR
   
   
   
				//========= DELETE  all tables inside DATABASE		

	$zzzzzz = $CONNECTED->query('SET foreign_key_checks = 0');
	if ($result = $CONNECTED->query("SHOW TABLES"))
	{
		while($row = $result->fetch_array())
		{
			$mysql_query->query('DROP TABLE IF EXISTS '.$row[0]);
		}
	}

	$zzzzzz = $CONNECTED->query('SET foreign_key_checks = 1');
	$zzzzzz = $CONNECTED->close();
   
   
   
   
   
   
   
 //================================= CREATE sample TABLE		=================================	  
  	**********DIRECT	
	  //tags MEDIUMTEXT (1000) DEFAULT '' NOT NULL,
	  //extra1 VARCHAR(150) DEFAULT '' NOT NULL,
	  //time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  //name tinytext NOT NULL,
	  //text text NOT NULL,
	$create_table = "CREATE TABLE IF NOT EXISTS `wp_user_tasks_table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`userid` int(11) NOT NULL,
		`taskk` text CHARACTER SET utf8 NOT NULL,
		`datee` varchar(150) NOT NULL,
		`extra1` text CHARACTER SET utf8 NOT NULL,
		`extra2` text CHARACTER SET utf8 NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `id` (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ; ";

  
	$create_table = "CREATE TABLE IF NOT EXISTS `generated_urls` (
		`IDD` int(50) NOT NULL AUTO_INCREMENT,
		`soc_type` varchar(150) CHARACTER SET utf8 NOT NULL,
		`uniq_Identif` varchar(150) CHARACTER SET utf8 NOT NULL,
		`gener_typical` text CHARACTER SET utf8 NOT NULL,
		`gener_iphone` varchar(150) CHARACTER SET utf8 NOT NULL,
		`gener_andr` varchar(150) CHARACTER SET utf8 NOT NULL,
		`extra_column` varchar(400) CHARACTER SET utf8 NOT NULL,
		PRIMARY KEY (`IDD`),
		UNIQUE KEY `IDD` (`IDD`)
	)  ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";	
	
	///
			an
	Check your database and make sure the whole database + tables + fields have the same charset. This is a real table example, you can see the charset is set for the fields AND for the table. And of course the database was created with a charset too. 
		CREATE TABLE `politicas` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `Nombre` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

			an
		CHARACTER SET utf8 COLLATE utf8_bin; 
	///
		an
	ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;  
	
	**********WODRPRESS	   
	global $wpdb;
	$create_table = "CREATE TABLE IF NOT EXISTS 'TableName'.......";
	$execut=$wpdb->query($create_table) or die(mysql_error());

	//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//dbDelta( $sql );
	   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
//=================================INSERT	=================================	
	**********DIRECT	
	$zzzzzz = $CONNECTED->query("INSERT INTO sxva_table (PIRVELISVET, meoresvet) VALUES ('aaaaaa', 'tttttttt')");
	
	**********WODRPRESS	
	$zzzzzz = $wpdb->query("INSERT INTO sxva_table (PIRVELISVET, meoresvet) VALUES ('aaaaaa', 'tttttttt')" );


//=================================SELECT=================================
	**********DIRECT
	$zzzzzz = $CONNECTED->query("SELECT `PIRVELISVET` from `sxva_table` WHERE meoresvet = 'excerpt' ");
	while ($claad = $zzzzzz->fetch_array($zzzzzz)) 
	{ 
		echo $claad['PIRVELISVET'];
	}
	
	**********WODRPRESS
	$zzzzzz = $wpdb->get_results("SELECT `PIRVELISVET` from `sxva_table` WHERE meoresvet = 'excerpt' ");
	foreach ($zzzzzz as $claad) 
	{
		echo $each->PIRVELISVET;
	}

			**********WHEN NEED TO GET ONLY ONE RESULT**
			$wpdb->get_var("SELECT ID FROM tablename WHERE post_type = '$post_tip' " );
	

//=================================UPDATE=================================

		**********DIRECT
		$zzzzzz = $CONNECTED->query( "UPDATE tablename SET Age=36 WHERE FirstName='Peter' AND LastName='Griffin'");

		
		**********WORDPRESS
		$zzzzzz = $wpdb->query( "UPDATE tablename SET Age=36 WHERE FirstName='Peter' AND LastName='Griffin'") ;

		
//=================================REPLACE	=================================		
		$zzzzzz = $CONNECTED->query("UPDATE TABLE_NAME set FIELD_NAME = replace( FIELD_NAME, 'what', 'by what' )");
		;

		
//================================= DELETE	=================================		
		**********DIRECT
		$zzzzzz = $CONNECTED->query("DELETE FROM wp_posts WHERE post_status = 'www'");
		
		**********WORDPRESS
		$zzzzzz = $wpdb->query("DELETE FROM wp_posts WHERE post_status = 'www'");



		
		
		
		
		
		
		
		
		
		
		
		
		
		
		///update if not exists
		global $wpdb, $current_user;
		$tablename	= $wpdb->prefix."users_heirarchyy_table";
		$useriid	= $current_user->id;
		
		//if (!empty($_POST['prof_updtd']))
		//{
		
			$deprt	=stripslashes($_POST['spc_departmentt']);
			$possit	=stripslashes($_POST['spc_positionn']);	
			
			if (empty($wpdb->get_var("SELECT id FROM $tablename WHERE userid = '$useriid'"))) {
				$wpdb->insert($tablename, 
					array('departmentt'=> $deprt, 'positionn'=>$possit,'userid'=> $useriid,), 
					array('%s','%s','%s') 
				);
			}
			else 	{
				$wpdb->update($tablename, 
					array('departmentt'=>$deprt,'positionn'=>$possit),
					array('userid'=> $useriid)
				);
			}
		}
		

		
