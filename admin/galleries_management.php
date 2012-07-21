<?php
	//                   [nyxIn/admin/galleries_management.php]
	//
	//	This file deals with the management of the Gallery as a whole, and
	//	not the individual galleries themselves. Galleries are created, 
	//	renamed, moved and deleted here.
	//

// Anti-Exploit Check
if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
	die();
}

if(nyxInRequirePermissions(array('galleries_management'))) {
?>
	<h2>Galleries Management</h2>
	<?php
		if($nyxIn_Admin_Action=="create_gallery") {
			if(isset($_POST['parent_id'])&&($_POST['parent_id']!="")) {
				$parent_id = $nyxIn['db']->real_escape_string($_POST['parent_id']);
			} else {
				$fail = 1;
			}
			
			if(isset($_POST['galleries_name'])&&($_POST['galleries_name']!="")) {
				$galleries_name = $nyxIn['db']->real_escape_string($_POST['galleries_name']);
			} else {
				$fail = 1;
			}
			
			if($fail==0) {
				$nyxIn['db']->query("INSERT INTO ".$nyxIn['db_prefix']."galleries (parent_id, name, thumbnail) VALUES('$parent_id','$galleries_name', '')") or die($nyxIn['db']->error);
			}

		} else if($nyxIn_Admin_Action=="rename_gallery") {
			if(isset($_POST['new_name'])&&($_POST['new_name']!="")) {
				$new_name = $nyxIn['db']->real_escape_string($_POST['new_name']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['to_rename'])&&($_POST['to_rename']!="")) {
				$to_rename = $nyxIn['db']->real_escape_string($_POST['to_rename']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET name='$new_name' WHERE id='$to_rename'") or die($nyxIn['db']->error);
			}

		} else if($nyxIn_Admin_Action=="move_gallery") {
			if(isset($_POST['to_move'])&&($_POST['to_move']!="")) {
				$to_move = $nyxIn['db']->real_escape_string($_POST['to_move']);
			} else {
				$fail = 1;
			}

			if(isset($_POST['move_to'])&&($_POST['move_to']!="")) {
				$move_to = $nyxIn['db']->real_escape_string($_POST['move_to']);
			} else {
				$fail = 1;
			}

			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET parent_id='$move_to' WHERE id='$to_move'") or die($nyxIn['db']->error);
			}

		} else if($nyxIn_Admin_Action=="delete_gallery") {
			if(isset($_POST['to_delete'])&&($_POST['to_delete']!="")) {
				$to_delete = $nyxIn['db']->real_escape_string($_POST['to_delete']);
			} else {
				$fail = 1;
			}

			function nyxIn_Function_DeleteGallery($gallery_id) {
				global $nyxIn;

				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET deleted_status='1' WHERE id='$gallery_id'") or die($nyxIn['db']->error);

				$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id='$gallery_id'") or die($nyxIn['db']->error);
				while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {
					nyxIn_Function_DeleteGallery($row->id);
				}
			}

			if($fail==0) {
				nyxIn_Function_DeleteGallery($to_delete);
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Create Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=galleries_management&amp;nyxIn_Admin_Action=create_gallery">
			<p>Create <input type="text" name="galleries_name"> under <?php nyxInGetGalleriesSelectBox("parent_id",0,0,1,0,1);?> <input type="submit" value="Add Gallery"></p>
		</form>
		<h3>Rename Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=galleries_management&amp;nyxIn_Admin_Action=rename_gallery">
			<p>Rename <?php nyxInGetGalleriesSelectBox("to_rename",0,0,0,0,1);?> to <input type="text" name="new_name"> <input type="submit" value="Rename Gallery"></p>
		</form>
		<h3>Move Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=galleries_management&amp;nyxIn_Admin_Action=move_gallery">
			<p>Move <?php nyxInGetGalleriesSelectBox("to_move",0,0,0,0,1);?> under <?php nyxInGetGalleriesSelectBox("move_to",0,0,1,0,1);?><input type="submit" value="Move Gallery"></p>
		</form>
		<h3>Delete Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=galleries_management&amp;nyxIn_Admin_Action=delete_gallery">
			<p>Remove <?php nyxInGetGalleriesSelectBox("to_delete",0,0,0,0,1);?> <input type="submit" value="Delete Gallery"> </p>
			<p>All child galleries and images will be deleted.<p>
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}