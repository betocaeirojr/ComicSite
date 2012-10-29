<?php
	require('db/config.php');
	
	$conn = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die("Could not connect to MySQL database. " . mysql_error());

	mysql_select_db(SQL_DB, $conn);
	$sql = "SELECT id, power FROM char_power ORDER BY power";

	$result = mysql_query($sql)
		or die(mysql_error());

	if (mysql_num_rows($result) > 0) 
	{
		while ($row = mysql_fetch_array($result)) 
		{
			$pwrlist[$row['id']] = $row['power'];
		}

		$numpwr = count($pwrlist);
		$thresh = 5;
		$maxcols = 3;

		$cols = min($maxcols, (ceil(count($pwrlist)/$thresh)));
		$percol = ceil(count($pwrlist)/$cols);
		$powerchk = '';
		
		$i = 0;
		foreach ($pwrlist as $id => $pwr) 
		{
			if (($i>0) && ($i%$percol == 0)) 
			{
				$powerchk .= "</td>\n<td valign=\"top\">";
			}

			$powerchk .= 	"<input type=\"checkbox\" name=\"powers[]\" " .
							"value=\"$id\"> $pwr<br>\n";
			$i++;
		}

		$delbutton = 	" <tr>
							<td colspan=\"$cols\" bgcolor=\"#CCCCFF\" align=\"center\">
								<input type=\"submit\" name=\"action\" value=\"Delete Powers\">
								<font size=\"2\" color=\"#990000\"><br><br>
								deleting will remove all associated powers<br>
								from characters as well -- select wisely</font>
							</td>
						</tr>";
	} else 
	{
		$powerchk = 	"<div style=\"text-align:center;width:300;
						font-family:Tahoma,Verdana,Arial\">No Powers entered...</div>";
		$delbutton = '';
		$cols = 1;
	}
?>
<html>
	<head>
		<title>Add/Delete Powers</title>
	</head>
	<body>
		<img src="CBA_Tiny.gif" align="left" hspace="10">
		<h1>Comic Book<br>Appreciation</h1><br>
		<h3>Editing Character Powers</h3>
		<form action="char_transact.php" method="post" name="theform">
			<table border="0" cellpadding="5">
				<tr bgcolor="#FFCCCC">
					<td valign="top"><?php echo $powerchk; ?></td>
				</tr>
					<?php echo $delbutton; ?>
				<tr>
					<td colspan="<?php echo $cols; ?>" bgcolor="#CCCCFF" align="center">
					<input type="text" name="newpower" value="" size=20>
					<input type="submit" name="action" value="Add Power">
					</td>
				</tr>
			</table>
		</form>
		<a href="charlist.php">Return to Home Page</a>
	</body>
</html>