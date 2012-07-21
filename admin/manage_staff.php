<?php
	//                   [nyxIn/admin/galleries_customization.php]
	//
	//	This file deals with the management of the staff. Staff accounts are
	//	created, renamed and deleted here.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('manage_staff'))==true) {
?>
	<h2>Manage Staff</h2>
	<?php
		if($nyxIn_Admin_Action=="create_staff") {
			if(isset($_POST['staff_account_username'])&&($_POST['staff_account_username']!="")) {
				$staff_account_username = $nyxIn['db']->real_escape_string($_POST['staff_account_username']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['staff_account_password'])&&($_POST['staff_account_password']!="")) {
				$staff_account_password = $nyxIn['db']->real_escape_string($_POST['staff_account_password']);
				$staff_account_password = sha1($staff_account_password);
			} else {
				$fail = 1;
			}


			if(isset($_POST['staff_account_class_id'])&&($_POST['staff_account_class_id']!="")) {
				$staff_account_class_id = $nyxIn['db']->real_escape_string($_POST['staff_account_class_id']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."staff (class_id, username, password_hash) VALUES('$staff_account_class_id','$staff_account_username', '$staff_account_password')") or die($nyxIn['db']->error);
			}

		} else if($nyxIn_Admin_Action=="rename_staff") {
			if(isset($_POST['to_rename'])&&($_POST['to_rename']!="")) {
				$to_rename = $nyxIn['db']->real_escape_string($_POST['to_rename']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['rename_to'])&&($_POST['rename_to']!="")) {
				$rename_to = $nyxIn['db']->real_escape_string($_POST['rename_to']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET username='$rename_to' WHERE id='$to_rename'") or die($nyxIn['db']->error);
			}
		} else if($nyxIn_Admin_Action=="promote_demote_staff_member") {
			if(isset($_POST['to_promote_demote'])&&($_POST['to_promote_demote']!="")) {
				$to_promote_demote = $nyxIn['db']->real_escape_string($_POST['to_promote_demote']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['promote_demote_to'])&&($_POST['promote_demote_to']!="")) {
				$promote_demote_to = $nyxIn['db']->real_escape_string($_POST['promote_demote_to']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET class_id='$promote_demote_to' WHERE id='$to_promote_demote'") or die($nyxIn['db']->error);
			}

		} else if($nyxIn_Admin_Action=="change_password") {
			if(isset($_POST['to_change'])&&($_POST['to_change']!="")) {
				$to_change = $nyxIn['db']->real_escape_string($_POST['to_change']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['new_password'])&&($_POST['new_password']!="")) {
				$new_password = sha1($nyxIn['db']->real_escape_string($_POST['new_password']));
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET password_hash='$new_password' WHERE id='$to_change'") or die($nyxIn['db']->error);
			}
		} else if($nyxIn_Admin_Action=="delete_staff") {
			if(isset($_POST['to_delete'])&&($_POST['to_delete']!="")) {
				$to_delete = $nyxIn['db']->real_escape_string($_POST['to_delete']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET deleted_status='1' WHERE id='$to_delete'") or die($nyxIn['db']->error);
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Create Staff Account</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff&amp;nyxIn_Admin_Action=create_staff">
			<p>Username: <input type="text" name="staff_account_username"> Password: <input type="text" name="staff_account_password"> Class:
				<select name="staff_account_class_id">
					<?php
						$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE deleted_status='0' ORDER BY name") or die($nyxIn['db']->error);  
						while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
							echo "<option value='$row->id'>$row->name</option>";
						}
					?>
				</select>
			</p>
			<input type="submit" value="Create Staff"></p>
		</form>
		<h3>Rename Staff Account</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff&amp;nyxIn_Admin_Action=rename_staff">
			<p>Rename 
				<select name="to_rename">
					<?php
						$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT staff.id AS staff_id, staff.username AS staff_username, classes.name AS classes_name FROM ".$nyxIn['db_prefix']."staff AS staff, ".$nyxIn['db_prefix']."classes AS classes WHERE staff.class_id = classes.id AND staff.deleted_status='0' AND classes.deleted_status='0' ORDER BY classes.name") or die($nyxIn['db']->error);  
						while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
							echo "<option value='$row->staff_id'>$row->staff_username [$row->classes_name]</option>";
						}
					?>
				</select>
			to <input type="text" name="rename_to"> <input type="submit" value="Rename Staff"></p>
			<p>If you make any changes to the account you are logged into now, click Admin Panel in the footer.</p>
		</form>
		<h3>Promote/Demote Staff Account</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff&amp;nyxIn_Admin_Action=promote_demote_staff_member">
			<p>Promote/Demote 
				<select name="to_promote_demote">
					<?php
						$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT staff.id AS staff_id, staff.username AS staff_username, classes.name AS classes_name FROM ".$nyxIn['db_prefix']."staff AS staff, ".$nyxIn['db_prefix']."classes AS classes WHERE staff.class_id = classes.id AND staff.deleted_status='0' AND classes.deleted_status='0' ORDER BY classes.name") or die($nyxIn['db']->error);  
						while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
							echo "<option";
							if($row->staff_id==1) {
								echo " disabled='true'";
							} else {
								echo " value='$row->staff_id'";
							}
							echo ">$row->staff_username [$row->classes_name]</option>";
						}
					?>
				</select>
				to
				<select name="promote_demote_to">
					<?php
						$nyxQuery_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE deleted_status='0' ORDER BY name") or die($nyxIn['db']->error);
							while($row = $nyxQuery_SelectClassMetadata->fetch_object()) {
							?>
							<option value="<?php echo $row->id?>"><?php echo $row->name?></option>
							<?php
						}
					?>
				</select>
			<input type="submit" value="Promote/Demote Staff"></p>
			<p>If you make any changes to the account you are logged into now, click Admin Panel in the footer.</p>
		</form>
		<h3>Change Password</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff&amp;nyxIn_Admin_Action=change_password">
			<p>Change 
				<select name="to_change">
					<?php
						$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT staff.id AS staff_id, staff.username AS staff_username, classes.name AS classes_name FROM ".$nyxIn['db_prefix']."staff AS staff, ".$nyxIn['db_prefix']."classes AS classes WHERE staff.class_id = classes.id AND staff.deleted_status='0' AND classes.deleted_status='0' ORDER BY classes.name") or die($nyxIn['db']->error);  
						while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
							echo "<option";
							echo " value='$row->staff_id'";
							echo ">$row->staff_username [$row->classes_name]</option>";
						}
					?>
				</select>'s password to
				<input type="text" name="new_password"> <input type="submit" value="Change password"></p>
		</form>		
		<h3>Delete Staff Account</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff&amp;nyxIn_Admin_Action=delete_staff">
			<p>Delete 
				<select name="to_delete">
					<?php
						$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT staff.id AS staff_id, staff.username AS staff_username, classes.name AS classes_name FROM ".$nyxIn['db_prefix']."staff AS staff, ".$nyxIn['db_prefix']."classes AS classes WHERE staff.class_id = classes.id AND staff.deleted_status='0' AND classes.deleted_status='0' ORDER BY classes.name") or die($nyxIn['db']->error);  
						while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
							echo "<option";
							if($row->staff_id==1) {
								echo " disabled='true'";
							} else {
								echo " value='$row->staff_id'";
							}
							echo ">$row->staff_username [$row->classes_name]</option>";
						}
					?>
				</select>
				<input type="submit" value="Delete Staff"> </p>
		<p>If you make any changes to the account you are logged into now, click Admin Panel in the footer.</p>
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}