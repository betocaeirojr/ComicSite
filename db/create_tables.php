<?php
require('config.php');

$conn = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
	or die('Could not connect to MySQL database. ' . mysql_error());

mysql_select_db(SQL_DB,$conn);


// Table CHARACTER MAIN
$sql1 = "CREATE TABLE IF NOT EXISTS char_main 
		(
			id 			int(11) 				NOT NULL auto_increment,
			alias 		varchar(40) 			NOT NULL default '',
			real_name 	varchar(80) 			NOT NULL default '',
			lair_id 	int(11) 				NOT NULL default 0,
			align 		enum('good','evil') 	NOT NULL default 'good',
			PRIMARY KEY (id)
		)";

// Table CHARACTER POWER
$sql2 = "CREATE TABLE IF NOT EXISTS char_power 
		(
			id 		int(11)			NOT NULL auto_increment,
			power 	varchar(40) 	NOT NULL default '',
			PRIMARY KEY (id)
		)";

// RELATIONSHIP TABLE CHARACTER POWER LINK
$sql3 = "CREATE TABLE IF NOT EXISTS char_power_link 
		(
			char_id 	int(11) 	NOT NULL default 0,
			power_id 	int(11) 	NOT NULL default 0,
			PRIMARY KEY (char_id, power_id)
		)";

// TABLE CHARACTER LAIR (SECRET PLACE)
$sql4 = "CREATE TABLE IF NOT EXISTS char_lair 
		(
			id 			int(11) 		NOT NULL auto_increment,
			zip_id 		varchar(10) 	NOT NULL default '00000',
			lair_addr 	varchar(40) 	NOT NULL default '',
			PRIMARY KEY (id)
		)";

// TABLE CHARACTER ZIP CODE
$sql5 = "CREATE TABLE IF NOT EXISTS char_zipcode 
		(
			id 		varchar(10) 	NOT NULL default '',
			city 	varchar(40)		NOT NULL default '',
			state 	char(2) 		NOT NULL default '',
			PRIMARY KEY (id)
		)";


// TABLE RELATIONSHIP GOOD OR BAD
$sql6 = "CREATE TABLE IF NOT EXISTS char_good_bad_link 
		(
			good_id int(11) 	NOT NULL default 0,
			bad_id 	int(11) 	NOT NULL default 0,
			PRIMARY KEY (good_id,bad_id)
		)";

// Send SQLs do MySQL DBMS
mysql_query($sql1) or die(mysql_error());
mysql_query($sql2) or die(mysql_error());
mysql_query($sql3) or die(mysql_error());
mysql_query($sql4) or die(mysql_error());
mysql_query($sql5) or die(mysql_error());
mysql_query($sql6) or die(mysql_error());
echo "Done.";
?>