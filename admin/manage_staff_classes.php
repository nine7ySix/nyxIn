<?php
	//                   [nyxIn/admin/manage_staff_classes.php]
	//
	//	This file deals with the management of the staff classes, mainly,
	//	permissions are set here.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('manage_staff_classes'))) {
?>
<h2>Manage Staff Classes</h2>
<?php
	$class_id = 0;
	if($nyxIn_Admin_Action=="create_class") {
		if(isset($_POST['permission_count'])&&($_POST['permission_count']!="")) {
			$permission_count = $_POST['permission_count'];
		} else {
			$fail = 1;
		}

		if(isset($_POST['class_name'])&&($_POST['class_name']!="")) {
			$class_name = $_POST['class_name'];
		} else {
			$fail = 1;
		}

		if($fail==0) {
			for($i=1;$i<=$permission_count;$i++) {
				$permissions[$i] = "0";
			}
			$permissions_json = json_encode($permissions);

			$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES('$class_name', '$permissions_json')") or die($nyxIn['db']->error);
		}

	} else if($nyxIn_Admin_Action=="delete_class") {
		if(isset($_POST['to_delete'])&&($_POST['to_delete']!="")) {
			$to_delete = $nyxIn['db']->real_escape_string($_POST['to_delete']);
		} else {
			$fail = 1;
		}

		if($fail==0) {
			$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."classes SET deleted_status='1' WHERE id='$to_delete'") or die($nyxIn['db']->error);
			$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."staff SET class_id='2' WHERE class_id='$to_delete'") or die($nyxIn['db']->error);
		}

	} else if($nyxIn_Admin_Action=="select_class") {
		if(isset($_POST['class_id'])&&($_POST['class_id']!="")) {
			$class_id = $_POST['class_id'];
		} else {
			$fail = 1;
		}

	} else if ($nyxIn_Admin_Action=="edit_class") {
		if(isset($_POST['class_id'])&&($_POST['class_id']!="")) {
			$class_id = $_POST['class_id'];
		} else {
			$fail = 1;
		}

		if(isset($_POST['permission_count'])&&($_POST['permission_count']!="")) {
			$permission_count = $_POST['permission_count'];
		} else {
			$fail = 1;
		}


		for($i=1;$i<=$permission_count;$i++) {
			if(isset($_POST['permissions'][$i])) {
				$permissions[$i] = $_POST['permissions'][$i];
			} else {
				if($class_id==1) {
					$permissions[$i] = "1";
				} else {
					$permissions[$i] = "0";
				}
			}
		}

		if($fail==0) {
			$permissions_json = json_encode($permissions);
			$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."classes SET permissions='$permissions_json' WHERE id='$class_id'") or die($nyxIn['db']->error);
		}
		nyxInReloadStaffPermissions();
	}
?>
<div id="nyxIn_Admin_Content">
	<?php require("admin/failure_check.php"); ?>
	<h3>Create a Class</h3>
	<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff_classes&amp;nyxIn_Admin_Action=create_class">
		<?php
			$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."permissions ORDER BY id") or die($nyxIn['db']->error);  
			$nyxIn_PermissionCount = $nyxIn_Query_SelectPermissionsMetadata->num_rows;
		?>
		<input type="hidden" name="permission_count" value="<?php echo $nyxIn_PermissionCount; ?>">
		<p>Name: <input type="text" name="class_name" value=""> <input type="submit" value="Create Class"></p>
	</form>
	<h3>Delete a Class</h3>
	<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff_classes&amp;nyxIn_Admin_Action=delete_class">
		<p>Delete
			<select name="to_delete">
			<?php
				$nyxQuery_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE deleted_status='0' ORDER BY name") or die($nyxIn['db']->error);
					while($row = $nyxQuery_SelectClassMetadata->fetch_object()) {
					?>
					<option <?php if($row->id!=1&&$row->id!=2){?>value="<?php echo $row->id; ?>"<? } else { ?>disabled="disabled"<?php } ?> <?php if($row->id==$class_id){?>selected="selected"<?php } ?>><?php echo $row->name?></option>
					<?php
				}
			?>
		</p>
		</select>
		<input type="submit" value="Delete Class">
	</form>
	<h3>Select a Class</h3>
	<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff_classes&amp;nyxIn_Admin_Action=select_class">
		<select name="class_id">
			<?php
				$nyxQuery_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE deleted_status='0' ORDER BY name") or die($nyxIn['db']->error);
					while($row = $nyxQuery_SelectClassMetadata->fetch_object()) {
					?>
					<option value="<?php echo $row->id?>" <?php if($row->id==$class_id){?>selected="selected"<?}?>><?php echo $row->name?></option>
					<?php
				}
			?>
		</select>
		<input type="submit" value="Edit Permissions">
	</form>
	<?php
	if($class_id!=0) {
		$nyxQuery_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE id=".$class_id." ORDER BY name") or die($nyxIn['db']->error);
		$classObject = $nyxQuery_SelectClassMetadata->fetch_object();
		$class_name = $classObject->name;
		$class_permission = $classObject->permissions;

		?>
		<h3>Editing Permissions for <u><?php echo $class_name ?></u></h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=manage_staff_classes&amp;nyxIn_Admin_Action=edit_class">
			<input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
			<?php
				$nyxIn_Query_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."classes WHERE id=".$class_id." ORDER BY name") or die($nyxIn['db']->error);  
				$nyxIn_User_Permissions_json = json_decode($nyxIn_Query_SelectClassMetadata->fetch_object()->permissions, true);

				$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."permissions ORDER BY id") or die($nyxIn['db']->error);  
				
				$permission_count = 0;
				while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
					echo "<p><input type='checkbox' name='permissions[".$row->id."]' value='1'";
					if($nyxIn_User_Permissions_json[$row->id]==1) {
						echo " checked='checked'";
					}
					if($class_id==1||$class_id==2) {
						echo " disabled='disabled'";
					}
					echo "> $row->name</p>";
					$permission_count++;
				}
			?>
			<input type="hidden" name="permission_count" value="<?php echo $permission_count; ?>">
			<input type="submit" value="Edit Permissions">
		</form>
	<?php
	}
	?>
</div>
<?php
} else {
	require("admin/permission_error.php");
}