<?php

$strDBName=$_REQUEST['db'];

echo "Database name is: '" . $strDBName . "'. <BR>";

$link = mysql_connect("localhost", "root", "")
	or die("Could not connect: " . mysql_error());
	
$strCreateDB_SQL = "CREATE DATABASE IF NOT EXISTS ". $strDBName;

echo "CREATE DATABASE statement is: '". $strCreateDB_SQL . "'.<BR><BR>";

$result = mysql_query($strCreateDB_SQL, $link);

//var_dump($result);

?>
