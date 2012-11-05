<?php
	//echo "DEBUG:: Just Before the Required...<BR>";
	require('db/config.php');
	//echo "DEBUG:: Just After the Required...<BR>";

	// Begining Debuging $_POST
	//echo "<PRE>";
	//print_r($_POST);
	//echo "</PRE>";
	// Finishing Debuging $_POST

	foreach ($_POST as $key => $value) 
	{
		$$key = $value;
	}

	//echo "DEBUG:: Connecting to DB... <BR>";
	// Connect to DB
	//echo "DEBUG:: MySQL Hostname: ". SQL_HOST . "; MySQL Username: " . SQL_USER . "; User pass: " . SQL_PASS . "!<BR>";

	$conn = mysql_connect(SQL_HOST, SQL_USER, '')
		or die('Could not connect to MySQL database. ' . mysql_error());

	//echo "DEBUG:: MySQL DB Name: " . SQL_DB . ".<BR>"; 
	
	//Select Movie DV
	mysql_select_db(SQL_DB, $conn);
	//echo "DEBUG:: " . SQL_DB . " database selected... <BR>";

	//echo "DEBUG:: Just Before Switch $action... <BR>";
	switch ($action) 
	{
		case "Create Character":
			// Insert info into ZIPCODE Table
			$sql = 	"INSERT IGNORE INTO char_zipcode (id, city, state) " .
					"VALUES ('$zip', '$city', '$state')";

			$result = mysql_query($sql)
				or die(mysql_error());
			
			// Insert info into LAIR table
			$sql = 	"INSERT INTO char_lair (id, zip_id, lair_addr) " .
					"VALUES (NULL, '$zip', '$address')";

			$result = mysql_query($sql)
				or die(mysql_error());

			if ($result) 
			{
				// Return Lair ID
				$lairid = mysql_insert_id($conn);
			}
			
			// CREATE MAIN CHARACTER -- Insert info into CHAR_MAIN table
			$sql = 	"INSERT INTO char_main (id,lair_id,alias,real_name,align) " .
					"VALUES (NULL, '$lairid', '$alias', '$name', '$align')";
			
			$result = mysql_query($sql)
				or die(mysql_error());
			if ($result) 
			{
				// Return Character ID
				$charid = mysql_insert_id($conn);
			}

			if ($powers != '') 
			{
				$val = "";
				foreach ($powers as $key => $id) 
					{
						$val[] = "('$charid', '$id')";
					}

				$values = implode(',', $val);
				$sql = 	"INSERT IGNORE INTO char_power_link (char_id, power_id) " .
						"VALUES $values";

				$result = mysql_query($sql)
					or die(mysql_error());
			}

			if ($enemies != '') 
			{
				$val = "";
				foreach ($enemies as $key => $id) 
				{
					$val[] = "('$charid', '$id')";
				}
				
				$values = implode(",", $val);
				
				if ($align = 'good') 
				{
					$cols = '(good_id, bad_id)';
				} else 
				{
					$cols = '(bad_id, good_id)';
				}

				$sql = 	"INSERT IGNORE INTO char_good_bad_link $cols " .
						"VALUES $values";
				
				$result = mysql_query($sql)
					or die(mysql_error());
			}
			
			$redirect = 'char_list.php';
			break;

		case "Delete Character":
			$sql = 	"DELETE FROM char_main, char_lair " .
					"USING char_main m, char_lair l " .
					"WHERE m.lair_id = l.id AND m.id = $cid";

			$result = mysql_query($sql)
				or die(mysql_error());

			$sql = "DELETE FROM char_power_link WHERE char_id = $cid";
			
			$result = mysql_query($sql)
				or die(mysql_error());

			$sql = 	"DELETE FROM char_good_bad_link " .
					"WHERE good_id = $cid OR bad_id = $cid";

			$result = mysql_query($sql)
			or die(mysql_error());

			$redirect = 'char_list.php';
			break;

		case "Update Character":
			$sql = 	"INSERT IGNORE INTO char_zipcode (id, city, state) " .
					"VALUES ('$zip', '$city', '$state')";

			//echo "DEBUG:: SQL Statement is: $sql ...<BR>";

			$result = mysql_query($sql)
				or die(mysql_error());

			$sql = 	"UPDATE char_lair l, char_main m " 						.
					"SET l.zip_id='$zip', l.lair_addr='$address', " 		.
					"alias='$alias', real_name='$name', align='$align' " 	.
					"WHERE m.id = $cid AND m.lair_id = l.id";

			//echo "DEBUG:: SQL Statement is: $sql ...<BR>";

			$result = mysql_query($sql)
				or die(mysql_error());

			$sql = "DELETE FROM char_power_link WHERE char_id = $cid";

			//echo "DEBUG:: SQL Statement is: $sql ...<BR>";

			$result = mysql_query($sql)
				or die(mysql_error());

			if ($powers != '') 
			{
				$val = "";
				foreach ($powers as $key => $id) 
				{
					$val[] = "('$cid', '$id')";
				}
				$values = implode(',', $val);
				
				$sql = 	"INSERT IGNORE INTO char_power_link (char_id, power_id) " .
						"VALUES $values";

				//echo "DEBUG:: SQL Statement is: $sql ...<BR>";

				$result = mysql_query($sql)
				or die(mysql_error());
			}

			$sql = 	"DELETE FROM char_good_bad_link " .
					"WHERE good_id = $cid OR bad_id = $cid";

			$result = mysql_query($sql)
				or die(mysql_error());

			if ($enemies != '') 
			{
				$val = "";
				foreach ($enemies as $key => $id) 
				{
					$val[] = "(‘$cid’, ‘$id’)";
				}

				$values = implode(',', $val);

				if ($align == 'good') 
				{
					$cols = '(good_id, bad_id)';
				} else 
				{
					$cols = '(bad_id, good_id)';
				}

				$sql = 	"INSERT IGNORE INTO char_good_bad_link $cols " .
						"VALUES $values";

				$result = mysql_query($sql) or die(mysql_error());
			}

			$redirect = 'char_list.php';
			
			break;

		case "Delete Powers":
			//echo "DEBUG:: Entering Deleting Powers... <BR>";
			if ($powers != '') 
			{
				//echo "DEBUG:: Powers = " . $powers . "<BR>";

				$powerlist = implode(",", $powers);
				//echo "DEBUG:: So the powerslist = " . $powerlist . "<BR>";				
				
				// Deleting the Character Power
				//echo "DEBUG:: Deleting from Power Table...<BR>";
				$sql = "DELETE FROM char_power WHERE id IN ($powerlist)";
				//echo "DEBUG:: The Delete Statement is: " . $sql . "...<BR>";

				//echo "DEBUG:: Just before deleting from Power Table...<BR>";
				$result = mysql_query($sql)
					or die(mysql_error());
				//echo "DEBUG:: Just After deleting from Power Table...<BR>";

				//echo "DEBUG:: Taking care of referential integrety with CHAR_POWER_LINK Table. <BR>";
				$sql = 	"DELETE FROM char_power_link " .
						"WHERE power_id IN ($powerlist)";
				//echo "DEBUG:: The Delete Statement is: " . $sql . "<BR>";

				//echo "DEBUG:: Just Before deletion for keeping referential integrety..<BR>";
				$result = mysql_query($sql)
					or die(mysql_error());
				//echo "DEBUG:: Just After... <BR>";
			}
			
			//$redirect = 'power_edit.php';
			
			break;

		case "Add Power":
			//echo "DEBUG:: Entering Add Power Case... <BR>";
			if ($newpower != '')
			{
				$sql = 	"INSERT IGNORE INTO char_power (id, power) " .
						"VALUES (NULL, '$newpower')";
				
				//echo "DEBUG:: The SQL Statement is: " . $sql . "...<BR>";
				$result = mysql_query($sql)
					or die(mysql_error());

				//Debug info Only
				$lastPowerid = mysql_insert_id($conn);
				//echo "DEBUG:: Data successfully inserted. <BR>";
				//echo "DEBUG:: Power_id inserted is: " . $lastPowerid . "<BR>";
			}

			$redirect = 'power_edit.php';
			break;

		default:
			$redirect = 'char_list.php';
	}

	header("Location: $redirect");
?>