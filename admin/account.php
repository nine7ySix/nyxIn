<?php

	//                   [nyxIn/admin/account.php]
	//
	//	This file allows staff members to change their passwords and view
	//	other account details.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}
?>
	<h2>Account</h2>
	<?php
		if($nyxIn_Admin_Action=="update_account") {
			if(isset($_POST['new_password'])&&(trim($_POST['new_password']!=""))) {
				$new_password = sha1($nyxIn['db']->real_escape_string($_POST['new_password']));
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET password_hash='$new_password' WHERE id='".$_COOKIE['nyxIn_Admin']['id']."'") or die($nyxIn['db']->error);
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Account Details</h3>
		<h3>Update Account Information</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=account&amp;nyxIn_Admin_Action=update_account">
			<?php
				$nyxIn_Query_SelectMetadataFrom_staff = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."staff AS staff WHERE staff.id='".$_COOKIE['nyxIn_Admin']['id']."'") or die($nyxIn['db']->error);
				$nyxIn_Obj_staff = $nyxIn_Query_SelectMetadataFrom_staff->fetch_object();
			?>
			<table width="100%">
				<tr>
					<td width="30%"><p>New Password:</p></td>
					<td width="70%"><input type="text" name="new_password"></td>
				</tr>
			</table>
			<input type="submit" value="Update Account Details">
		</form>
	</div>