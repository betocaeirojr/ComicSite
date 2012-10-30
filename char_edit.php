<?php
	require('db/config.php');

	if (!isset($_GET['c']) || $_GET['c'] == '' || !is_numeric($_GET['c'])) 
	{
		$char='0';
	} else 
	{
		$char = $_GET['c'];
	}

	$subtype = "Create";
	$subhead = "Please enter character data and click " .
				"'$subtype Character.'";
	$tablebg = '#EEEEFF';
	
	$conn = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die('Could not connect to MySQL database. ' . mysql_error());
	
	mysql_select_db(SQL_DB, $conn);
	
	$sql = "SELECT id, power FROM char_power";
	
	$result = mysql_query($sql)
		or die(mysql_error());
	
	if (mysql_num_rows($result) > 0) 
	{
		while ($row = mysql_fetch_array($result)) 
		{
			$pwrlist[$row['id']] = $row['power'];
		}
	}
	
	$sql = "SELECT id, alias FROM char_main WHERE id != $char";
	
	$result = mysql_query($sql)
		or die(mysql_error());
	
	if (mysql_num_rows($result) > 0) 
	{
		$row = mysql_fetch_array($result);
		$charlist[$row['id']] = $row['alias'];
	}

	if ($char != '0') 
	{
		$sql = 	"SELECT c.alias, c.real_name AS name, c.align, " 		.
				"l.lair_addr AS address, z.city, z.state, z.id AS zip " .
				"FROM char_main c, char_lair l, char_zipcode z " 		.
				"WHERE z.id = l.zip_id " 								.
				"AND c.lair_id = l.id " 								.
				"AND c.id = $char";
	
		$result = mysql_query($sql)
			or die(mysql_error());
	
		$ch = mysql_fetch_array($result);
	
		if (is_array($ch)) 
		{
			$subtype = "Update";
			$tablebg = '#EEFFEE';
			$subhead = 	"Edit data for <i>" . $ch['alias'] .
						"</i> and click ‘$subtype Character.'";
		
			$sql = 	"SELECT p.id " 				.
					"FROM char_main c " 		.
					"JOIN char_power p "	 	.
					"JOIN char_power_link pk " 	.
					"ON c.id = pk.char_id " 	.
					"AND p.id = pk.power_id " 	.
					"WHERE c.id = $char";
		
			$result = mysql_query($sql)
				or die(mysql_error());
		
			if (mysql_num_rows($result) > 0) 
			{
				while ($row = mysql_fetch_array($result)) 
				{
					$powers[$row['id']] = 'selected';
				}
			}
		
			// get list of character’s enemies
			$sql = 	"SELECT n.id " 									.
					"FROM char_main c " 							.
					"JOIN char_good_bad_link gb " 					.
					"JOIN char_main n " 							.
					"ON (c.id = gb.good_id AND n.id = gb.bad_id) " 	.
					"OR (n.id = gb.good_id AND c.id = gb.bad_id) " 	.
					"WHERE c.id = $char";
			
			$result = mysql_query($sql)
				or die(mysql_error());
			
			if (mysql_num_rows($result) > 0) 
			{
				while ($row = mysql_fetch_array($result)) 
				{
					$enemies[$row['id']] = 'selected';
				}
			}
		}
	}
?>
<html>
	<head>
		<title>Character Editor</title>
	</head>
	<body>
		<img src="CBA_Tiny.gif" align="left" hspace="10">
		<h1>Comic Book<br />Appreciation</h1><br />
		<h3><?php echo $subhead; ?></h3>
		<form action="char_transact.php" name="theform" method="post">
			<table border="0" cellpadding="15" bgcolor="<?php echo $tablebg; ?>">
				<tr>
					<td>Character Name:</td>
					<td><input type="text" name="alias" size="41" value="
							<?php if (isset($ch)) { echo $ch['alias']; } ?>">
					</td>
				</tr>
				<tr>
					<td>Real Name:</td>
					<td><input type="text" name="name" size="41" value="
							<?php if (isset($ch)) { echo $ch['name']; } ?>">
					</td>
				</tr>
				<tr>
					<td>Powers:<br><font size="2" color="#990000"> 
						(Ctrl-click to<br>select multiple<br>powers)</font>
					</td>
					<td>
						<select multiple name="powers[]" size="4">
						<?php
							foreach ($pwrlist as $key => $value) 
							{
								echo "<option value=\"$key\" ";
								if (isset($powers) && array_key_exists($key,$powers)) 
								{
									echo $powers[$key];
								}
								echo ">$value</option>\n";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Lair Location:<br><font size="2" color="#990000"> 
						(address,<br>city, state, zip)</font>
					</td>
					<td>
						<input type="text" name="address" size="41" value=
							"<?php if (isset($ch)) { echo $ch['address']; } ?>"><br>
						<input type="text" name="city" value=
							"<?php if (isset($ch)) { echo $ch['city']; } ?>">
						<input type="text" name="state" size="2" value=
							"<?php if (isset($ch)) { echo $ch['state']; } ?>">
						<input type="text" name="zip" size="10" value=
							"<?php if (isset($ch)) { echo $ch['zip']; } ?>">
					</td>
				</tr>
				<tr>
					<td>Alignment:</td>
					<td>
						<input type="radio" name="align" value="good"
							<?php if (isset($ch)) 
							{
								echo($ch['align']=='good' ? ' checked' : '');
							} ?>
						>good<br>
						<input type="radio" name="align" value="evil"
							<?php if (isset($ch)) 
							{
								echo($ch['align']=='evil' ? ' checked' : '');
							} ?>
							>evil
					</td>
				</tr>
				<?php if (isset($charlist) && is_array($charlist)) { ?>
				<tr>
					<td>Enemies:<br><font size="2" color="#990000">
						(Ctrl-click to<br>select multiple<br>enemies)</font>
					</td>
					<td>
						<select multiple name="enemies[]" size="4">
							<?php
								foreach ($charlist as $key => $value) 
								{
									echo "<option value=\"$key\" ";
									if (isset($enemies)) 
									{
										echo $enemies[$key];
									}
									echo ">$value</option>\n";
								}
							?>
						</select>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value=
							"<?php echo $subtype; ?> Character">
						<input type="reset">
							<?php if ($subtype == "Update") 
							{ ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="submit" name="action" value="Delete Character">
							<?php 
							} ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="cid" value="<?php echo $char; ?>">
		</form>
		<a href="char_list.php">Return to Home Page</a>
	</body>
</html>