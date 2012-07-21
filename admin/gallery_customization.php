<?php
	//                   [nyxIn/admin/galleries_customization.php]
	//
	//	This file deals with the management of the Gallery as a whole, and
	//	not the individual galleries themselves. Galleries are created, 
	//	renamed, moved and deleted here.
	//


	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('gallery_customization'))) {
?>
	<h2>Gallery Customization</h2>
	<?php
		$gallery_id = 0;

		if($nyxIn_Admin_Action=="select_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}

		} else if ($nyxIn_Admin_Action=="customize_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}
			
			if(isset($_POST['gallery_name'])&&($_POST['gallery_name']!="")) {
				$gallery_name = $nyxIn['db']->real_escape_string($_POST['gallery_name']);
			} else {
				$fail = 1;
			}
			
			if(isset($_POST['gallery_thumbnail'])) {
				$gallery_thumbnail = $nyxIn['db']->real_escape_string($_POST['gallery_thumbnail']);
			} else {
				$fail = 1;
			}
			
			if(isset($_POST['gallery_description'])) {
				$gallery_description = $nyxIn['db']->real_escape_string($_POST['gallery_description']);
			} else {
				$fail = 1;
			}
			
			if(isset($_POST['gallery_locked_status'])) {
				$gallery_locked_status = 1;
			} else {
				$gallery_locked_status = 0;
			}
			
			if(isset($_POST['gallery_password'])) {
				$gallery_password = $nyxIn['db']->real_escape_string($_POST['gallery_password']);
			} else {
				$gallery_password = "";
			}
			
			if($fail==0) {
				$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET thumbnail='$gallery_thumbnail', name='$gallery_name', description='$gallery_description', locked_status='$gallery_locked_status', password='$gallery_password' WHERE id='$gallery_id'") or die($nyxIn['db']->error);
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Select a Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=gallery_customization&amp;nyxIn_Admin_Action=select_gallery">
			<?php nyxInGetGalleriesSelectBox("gallery_id",0,0,0,$gallery_id,1);?>
			<input type="submit" value="Customize">
		</form>
		<?php
		if($gallery_id!=0) {
			$nyxQuery_SelectGalleryMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id='$gallery_id' AND deleted_status='0'") or die($nyxIn['db']->error);
			$galleryObject = $nyxQuery_SelectGalleryMetadata->fetch_object();
			$gallery_name_old = $galleryObject->name;
			$gallery_thumbnail_old = $galleryObject->thumbnail;
			$gallery_description_old = $galleryObject->description;
			$gallery_locked_status_old = $galleryObject->locked_status;
			$gallery_password_old = $galleryObject->password;
			?>
			<h3>Customizing <u><?php echo $gallery_name_old ?></u></h3>
			<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=gallery_customization&amp;nyxIn_Admin_Action=customize_gallery">
				<input type="hidden" name="gallery_id" value="<?php echo $gallery_id; ?>">
				<p>Name: <input type="text" name="gallery_name" value="<?php echo $gallery_name_old; ?>"></p>
				<p>Thumbnail: <input type="text" name="gallery_thumbnail" value="<?php echo $gallery_thumbnail_old; ?>"></p>
				<p><input type="checkbox" name="gallery_locked_status" <?php if($gallery_locked_status_old==1){?>checked="checked"<?php }?> value="1">  Password Lock: <input type="text" name="gallery_password" value="<?php echo $gallery_password_old; ?>"></p>
				<p>Description:</p>
				<textarea name="gallery_description" cols="50" rows="5"><?php echo $gallery_description_old; ?></textarea>
				<br>
				<input type="submit" value="Customize Gallery">
			</form>
		<?php
		}
		?>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}