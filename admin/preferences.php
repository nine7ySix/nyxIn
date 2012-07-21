<?php
	//                   [nyxIn/admin/preferences.php]
	//
	//	This file deals with the main preferences of the Gallery. Preferably,
	//	only an Administrator should be allowed to access this file.
	//	Maintenance Mode, the Number of Columns and Moderated Image Only along
	//	with other incredibly important variables can be changed here.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('preferences'))) {
?>
	<h2>Preferences</h2>
	<?php
		if($nyxIn_Admin_Action=="update_preferences") {
			if(isset($_POST['preferences'])) {
				$preferences = $_POST['preferences'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($preferences as $preference_id => $value) {
					$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."preferences SET value='$value' WHERE id='$preference_id'") or die($nyxIn['db']->error);
				}
			}

		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=preferences&amp;nyxIn_Admin_Action=update_preferences">
			<table>
				<tr>
					<td><p><b>Preference</b></p></td>
					<td><p><b>Description</b></p></td>
					<td><p><b>Value</b></p></td>
					<td><p><b>Accepted Values</b></p></td>

				</tr>
				<tr>
					<td colspan="4"><hr></td>
				</tr>				
				<?php
					$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."preferences") or die($nyxIn['db']->error);  
					while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
						?>
						<tr>
							<td width='30%' valign='top'><p><b><?php echo $row->preference; ?></b></p></td>
							<td width='25%' valign='top'><p><?php echo $row->description; ?></p></td>
							<td width='20%' valign='top'><p><input type="text" name="preferences[<?php echo $row->id; ?>]" value="<?php echo $row->value; ?>"></p></td>
							<td width='25%' valign='top'><p><?php echo $row->accepted_values; ?></p></td>
						</tr>
						<tr>
							<td colspan="4"><hr></td>
						</tr>
						<?php
					}
				?>
			</table>
			<input type="submit" value="Update Preferences">
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}