<?php
	require('config.php');

	foreach ($_POST as $key => $value) 
	{
		$$key = $value;
	}

	// Connect to DB
	$conn = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die('Could not connect to MySQL database. ' . mysql_error());

	// Select Movie DV
	mysql_select_db(SQL_DB, $conn);


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

			if ($powers != “”) 
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
			
			$redirect = ‘charlist.php’;
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

			$redirect = 'charlist.php';
			break;

		case "Update Character":
		$sql = 	"INSERT IGNORE INTO char_zipcode (id, city, state) " .
				"VALUES ('$zip', '$city', '$state')";

		$result = mysql_query($sql)
			or die(mysql_error());

		$sql = 	"UPDATE char_lair l, char_main m " 						.
				"SET l.zip_id='$zip', l.lair_addr='$address', " 		.
				"alias='$alias', real_name='$name', align='$align' " 	.
				"WHERE m.id = $cid AND m.lair_id = l.id";

		$result = mysql_query($sql)
			or die(mysql_error());

		$sql = "DELETE FROM char_power_link WHERE char_id = $cid";

		$result = mysql_query($sql)
			or die(mysql_error());

		if ($powers != "") 
		{
			$val = "";
			foreach ($powers as $key => $id) 
			{
				$val[] = "('$cid', '$id')";
			}
			$values = implode(‘,’, $val);
			
			$sql = 	"INSERT IGNORE INTO char_power_link (char_id, power_id) " .
					"VALUES $values";

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

		$redirect = 'charlist.php';
		
		break;

		case "Delete Powers":
			if ($powers != "") 
			{
				$powerlist = implode(",", $powers);
				$sql = "DELETE FROM char_power WHERE id IN ($powerlist)";
			
				$result = mysql_query($sql)
					or die(mysql_error());
			
			$sql = 	"DELETE FROM char_power_link " .
					"WHERE power_id IN ($powerlist)";
			
			$result = mysql_query($sql)
				or die(mysql_error());
			}
			
			$redirect = 'poweredit.php';
			
			break;

		case "Add Power":
			if ($newpower != '' 
			{
				$sql = 	"INSERT IGNORE INTO char_power (id, power) " .
						"VALUES (NULL, ‘$newpower’)";
			
				$result = mysql_query($sql)
					or die(mysql_error());
			}

			$redirect = 'poweredit.php';
			break;

		default:
			$redirect = 'charlist.php';
	}

	header("Location: $redirect");
?>