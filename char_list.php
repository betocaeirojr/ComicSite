<?php
	require('db/config.php');

	// Starting Debuging $_GET
	//echo "<PRE>";
	//print_r($_GET);
	//echo "</PRE>";
	// Starting Debuging $_GET

	if (isset($_GET['o']) && is_numeric($_GET['o'])) 
	{
		$ord = round(min(max($_GET['o'], 1), 3));
	} else 
	{
		$ord = 1;
	}
	$order = array(	
					1 => 'alias ASC', 
					2 => 'name ASC',
					3 => 'align ASC, alias ASC'
				   );

	// Connecting to DB
	//echo "DEBUG:: Connecting to DB... <BR>";
	$conn = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die('Could not connect to MySQL database. ' . mysql_error());

	// Selecting the DB to use	
	mysql_select_db(SQL_DB, $conn);

	//echo "DEBUG:: Preparing the SQL Statement... <BR>";
	$sql = 	"SELECT c.id, p.power " 	.
			"FROM char_main c " 		.
			"JOIN char_power p " 		.
			"JOIN char_power_link pk " 	.
			"ON c.id = pk.char_id AND p.id = pk.power_id";
	
	//echo "DEBUG:: The SQL Statement is: " . $sql . ".<BR>" ;

	//echo "DEBUG:: Sending SQL Statement to DB... <BR>";
	$result = mysql_query($sql)
		or die(mysql_error());

	//echo "DEBUG:: SQL Successfull... <BR>";

	if (mysql_num_rows($result) > 0) 
	{
		//echo "DEBUG:: Entering IF MySQL_NUM_ROW > 0... <BR>";
		while ($row = mysql_fetch_array($result)) 
		{
			//echo "DEBUG:: Entering While Row to iterate over Array... <BR>";
			$p[$row['id']][] = $row['power'];
		}
		foreach ($p as $key => $value) 
		{
			$powers[$key] = implode(", ", $value);
		}
	}

	// Preparing SQL Statement.
	//echo "DEBUG:: Preparing SQL Statement to take care of referential integrety... <BR>";
	$sql = 	"SELECT c.id, n.alias " 						.
			"FROM char_main c " 							.
			"JOIN char_good_bad_link gb " 					.
			"JOIN char_main n " 							.
			"ON (c.id = gb.good_id AND n.id = gb.bad_id) " 	.
			"OR (n.id = gb.good_id AND c.id = gb.bad_id)";

	//echo "DEBUG:: The SQL Statement is: " . $sql  .".<BR>";

	//echo "DEBUG:: Sending the SQL to the DBMS... <BR>";
	$result = mysql_query($sql)
		or die(mysql_error());

	//echo "DEBUG:: SQL Successfull...<BR>";
	if (mysql_num_rows($result) > 0) 
	{
		//echo "DEBUG:: Entering 2nd IF MYSQL NUM ROW > 0 <BR>";
		while ($row = mysql_fetch_array($result)) 
		{
			$e[$row['id']][] = $row['alias'];
		}
		foreach ($e as $key => $value) 
		{
			$enemies[$key] = implode(", ", $value);
		}
	}	
	$table = 	"<table><tr><td align=\"center\">No characters " .
				"currently exist.</td></tr></table>";
?>
<html>
	<head>
		<title>Comic Book Appreciation</title>
	</head>
<body>
	<img src="CBA_Tiny.gif" align="left" hspace="10">
	<h1>Comic Book<br>Appreciation</h1><br>
	<h3>Character Database</h3>
	<?php
		$sql = 	"SELECT id, alias, real_name AS name, align " .
				"FROM char_main ORDER BY ". $order[$ord];
		
		$result = mysql_query($sql)
			or die(mysql_error());

		if (mysql_num_rows($result) > 0) 
		{
			$table = "<table border=\"0\" cellpadding=\"5\">";
			$table .= "<tr bgcolor=\"#FFCCCC\"><th>";
			$table .= "<a href=\"" . $_SERVER['PHP_SELF'] . "?o=1\">Alias</a>";
			$table .= "</th><th><a href=\"" . $_SERVER['PHP_SELF'] . "?o=2\">";
			$table .= "Name</a></th><th><a href=\"" . $_SERVER['PHP_SELF'];
			$table .= "?o=3\">Alignment</a></th><th>Powers</th>";
			$table .= "<th>Enemies</th></tr>";
			
			// build each table row
			$bg = '';

			while ($row = mysql_fetch_array($result)) 
			{
				$bg = ($bg == 'F2F2FF' ? 'E2E2F2' : 'F2F2FF');
				
				$pow = ($powers[$row['id']]=='' ? 'none' : $powers[$row['id']]);
				if (!isset($enemies) || ($enemies[$row['id']]=='')) 
				{
					$ene = 'none';
				} else 
				{
					$ene = $enemies[$row['id']];
				}
				$table .= 	"<tr bgcolor=\"#" . $bg . "\">" 						.
							"<td><a href=\"char_edit.php?c=" . $row['id'] . "\">" 	.
							$row['alias']. "</a></td><td>" 							.
							$row['name'] . "</td><td align=\"center\">" 			.
							$row['align'] . "</td><td>" . $pow . "</td>" 			.
							"<td align=\"center\">" . $ene . "</td></tr>";
			}
		
			$table .= "</table>";
			
			$table = str_replace('evil', '<font color="red">evil</font>', $table);
			$table = str_replace('good', '<font color="darkgreen">good</font>', $table);
		}
	
		echo $table;
	?>
	
	<br/><a href="char_edit.php">New Character</a> &bull;
	<a href="power_edit.php">Edit Powers</a>
</body>
</html>