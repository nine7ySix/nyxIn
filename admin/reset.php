<?php
	//                   [nyxIn/admin/reset.php]
	//
	//	This file let's an Administrator reset the nyxIn Gallery with ease.
	//	This file is preferably used after the Administrator has familiarized
	//	with nyxIn's UI and is in the need to reset the installation in order
	//	to prepare for an actual Gallery. This file has proven itself useful
	//	during the development of nyxIn.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('reset'))) {
?>
	<h2>Reset</h2>
	<?php
		if($nyxIn_Admin_Action=="reset_images") {
			if(isset($_POST['password_check'])&&($_POST['password_check']!="")) {
				$password_check = sha1($_POST['password_check']);
				
				$nyxQuery_SelectStaff = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."staff WHERE username='".$_COOKIE['nyxIn_Admin']['username']."' AND password_hash='$password_check'") or die($nyxIn['db']->error);	
				if($nyxQuery_SelectStaff->num_rows==1) {
				} else {
					$fail = 1;
				}
			} else {
				$fail = 1;
			}
			
			if($fail==0) {
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."images") or die($nyxIn['db']->error);

				$uploadsFolderFilers = scandir("uploads");
				for($i=2;$i<count($uploadsFolderFilers);$i++) {
					unlink("uploads/$uploadsFolderFilers[$i]");
				}

				echo "<p style='background-color:#000;padding:10px;color:#F66;font-weight:bold;text-align:center;'>=== NYXIN RESET SUCCESSFUL ===</p>";
			}
		} else if($nyxIn_Admin_Action=="reset_images_and_galleries") {
			if(isset($_POST['password_check'])&&($_POST['password_check']!="")) {
				$password_check = sha1($_POST['password_check']);
				
				$nyxQuery_SelectStaff = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."staff WHERE username='".$_COOKIE['nyxIn_Admin']['username']."' AND password_hash='$password_check'") or die($nyxIn['db']->error);	
				if($nyxQuery_SelectStaff->num_rows==1) {
				} else {
					$fail = 1;
				}
			} else {
				$fail = 1;
			}
			
			if($fail==0) {
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."galleries") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."images") or die($nyxIn['db']->error);

				$uploadsFolderFilers = scandir("uploads");
				for($i=2;$i<count($uploadsFolderFilers);$i++) {
					unlink("uploads/$uploadsFolderFilers[$i]");
				}

				echo "<p style='background-color:#000;padding:10px;color:#F66;font-weight:bold;text-align:center;'>=== NYXIN RESET SUCCESSFUL ===</p>";
			}
		} else if ($nyxIn_Admin_Action=="reset_nyxIn") {
			if(isset($_POST['password_check'])&&($_POST['password_check']!="")) {
				$password_check = sha1($_POST['password_check']);
				
				$nyxQuery_SelectStaff = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."staff WHERE username='".$_COOKIE['nyxIn_Admin']['username']."' AND password_hash='$password_check'") or die($nyxIn['db']->error);	
				if($nyxQuery_SelectStaff->num_rows==1) {
				} else {
					$fail = 1;
				}
			} else {
				$fail = 1;
			}
			
			if($fail==0) {
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."galleries") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."images") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."staff") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."classes") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."permissions") or die($nyxIn['db']->error);
				$nyxIn['db']->query("TRUNCATE ".$nyxIn['db_prefix']."preferences") or die($nyxIn['db']->error);

				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."staff (class_id, username, password_hash) VALUES(1, 'Admin', '516b9783fca517eecbd1d064da2d165310b19759')") or die($nyxIn['db']->error);
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."permissions (name, shorthand) VALUES ('View Gallery During Maintenance', 'view_gallery_during_maintenace'), ('Upload Images', 'upload'), ('Manage Galleries', 'galleries_management'), ('Customize Galleries', 'gallery_customization'), ('Organize Galleries', 'gallery_organization'), ('Organize Sub-Galleries', 'subgallery_organization'), ('Move Images', 'move_images'), ('Delete Images', 'delete_images'), ('Moderate Images', 'moderate_images'), ('Manage Staff Classes', 'manage_staff_classes'), ('Manage Staff', 'manage_staff'), ('Preferences', 'preferences'), ('Reset nyxIn', 'reset')") or die($nyxIn['db']->error);

				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."preferences (preference, shorthand, description, accepted_values, value) VALUES ('Timestamp', 'timestamp', 'Unix timestamp of nyxIn installation.', 'Unix Timestamp', '".$nyxIn['preferences']['timestamp']."'), ('Maintenance Mode', 'maintenance_mode', 'Activate or Deacivate Maintenence Mode. During maintenance mode, only logged-in staff can browse the gallery.', '0 or 1', '1'), ('Number of Columns', 'cols', 'Number of columns of images and sub-galleries displayed.', 'Any integer', '5'), ('Moderated Image Only', 'display_moderated_only', 'Only staff-moderated images are displayed in the gallery.', '0 or 1', '1'), ('Thumbnail Length', 'thumbnail_length', 'The length in pixels of the square thumbnail image produced by nyxIn. Please refrain from changing this unless nyxIn has been image/gallery reset or if this is a fresh copy of nyxIn.', 'Any integer.', '450')") or die($nyxIn['db']->error);

				$jsonAdminPermissions = '{"1":"1","2":"1","3":"1","4":"1","5":"1","6":"1","7":"1","8":"1","9":"1","10":"1","11":"1","12":"1","13":"1"}';
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES ('Administrator', '".$nyxIn['db']->real_escape_string($jsonAdminPermissions)."')");

				$jsonBannedPermissions = '{"1":"0","2":"0","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0","13":"0"}';
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES ('Banned', '".$nyxIn['db']->real_escape_string($jsonBannedPermissions)."')");

				$jsonViewerPermissions = '{"1":"1","2":"0","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0","13":"0"}';
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES ('Viewer', '".$nyxIn['db']->real_escape_string($jsonViewerPermissions)."')");

				$jsonUploaderPermissions = '{"1":"1","2":"1","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0","13":"0"}';
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES ('Uploader', '".$nyxIn['db']->real_escape_string($jsonUploaderPermissions)."')");

				$jsonModeratorPermissions = '{"1":"1","2":"0","3":"0","4":"0","5":"1","6":"1","7":"1","8":"1","9":"1","10":"0","11":"0","12":"0","13":"0"}';
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."classes (name, permissions) VALUES ('Moderator', '".$nyxIn['db']->real_escape_string($jsonModeratorPermissions)."')");

				$uploadsFolderFilers = scandir("uploads");
				for($i=2;$i<count($uploadsFolderFilers);$i++) {
					unlink("uploads/$uploadsFolderFilers[$i]");
				}

				echo "<p style='background-color:#000;padding:10px;color:#F66;font-weight:bold;text-align:center;'>=== NYXIN RESET SUCCESSFUL ===</p>";
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Reset nyxIn Images</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=reset&amp;nyxIn_Admin_Action=reset_images">
			<p>Clicking the button below will cause nyxIn to truncate the <code>images</code> tables as well as delete all images off the server.</p>
			<p>This action <span style="color:#F00;font-weight:bold;">CANNOT</span> be undone.</p>
			<p>Password: <input type="text" name="password_check"> <input type="submit" value="Reset"></p>
		</form>
		<h3>Reset nyxIn Images and Galleries</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=reset&amp;nyxIn_Admin_Action=reset_images_and_galleries">
			<p>Clicking the button below will cause nyxIn to truncate the <code>gallery</code> and <code>images</code> tables as well as delete all images off the server.</p>
			<p>This action <span style="color:#F00;font-weight:bold;">CANNOT</span> be undone.</p>
			<p>Password: <input type="text" name="password_check"> <input type="submit" value="Reset"></p>
		</form>
		<h3>Reset nyxIn Installation</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=reset&amp;nyxIn_Admin_Action=reset_nyxIn">
			<p>Clicking the button below will reset nynIn's Images and Galleries (look above) as well as resetting the User/Class system and resetting all preference values to their default values.</p>
			<p>All staff and classes will be removed and a default username/password Admin/p account will be created.</p>
			<p>This action <span style="color:#F00;font-weight:bold;">CANNOT</span> be undone.</p>
			<p>Password: <input type="text" name="password_check"> <input type="submit" value="Reset"></p>
		</form>
	</div>
<?php

} else {
	require("admin/permission_error.php");
}