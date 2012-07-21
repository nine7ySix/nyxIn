<?php
	//                   [nyxIn/admin/gallery_organization.php]
	//
	//	This file deals with organization and display of pictures in a gallery.
	//

// Anti-Exploit Check
if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
	die();
}

if(nyxInRequirePermissions(array('gallery_organization'))) {
?>
	<h2>Image Organization</h2>
	<?php
		$gallery_id = 0;

		if($nyxIn_Admin_Action=="select_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}
		} else if($nyxIn_Admin_Action=="organize_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}

			if(isset($_POST['organized_images'])&&($_POST['organized_images']!="")) {
				$organized_images = $_POST['organized_images'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($organized_images as $key => $image_id) {
					$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."images SET order_int='$key' WHERE id=$image_id") or die($nyxIn['db']->error);
				}
			}

		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Select a Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=gallery_organization&amp;nyxIn_Admin_Action=select_gallery">
			<?php nyxInGetGalleriesSelectBox("gallery_id",0,0,1,$gallery_id,1);?>
			<input type="submit" value="Organize">
		</form>
		<?php
		if($gallery_id==0) {
			$nyxIn_Var_galleries_name = "Home";
		} else {
			$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id='$gallery_id' AND deleted_status='0'") or die($nyxIn['db']->error);
			$nyxIn_Obj_galleries = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object();
			$nyxIn_Var_galleries_name = $nyxIn_Obj_galleries->name;
		}
		?>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=gallery_organization&amp;nyxIn_Admin_Action=organize_gallery">
			<h3>Organizing <u><?php echo $nyxIn_Var_galleries_name; ?></u> <input type="submit" value="Organize"></h3>
			<input type="hidden" name="gallery_id" value="<?php echo $gallery_id; ?>">
			<ul class="sortable grid">
				<?php
					if($nyxIn['preferences']['display_moderated_only']==0) {
						$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id='$gallery_id' AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
					} else if($nyxIn['preferences']['display_moderated_only']==1) {
						$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id='$gallery_id' AND moderation_status='1' AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
					}

					$numberOfImages = 0;
					while($row = $nyxIn_Query_SelectMetadataFrom_images->fetch_object()) {
						$filename = $row->safename."_thumb.".$row->fileextension;
					?>
						<li><input type="hidden" name="organized_images[]" value="<?php echo $row->id?>"><img width="100%" src="uploads/<?php echo $filename; ?>"></li>
					<?php
						$numberOfImages++;
					}
				?>
			</ul>
			<?php
				echo str_repeat("<div style='height:".($length_for_organize_page+pow($margin_for_organize_page, 2))."px';'></div>", ceil($numberOfImages/$nyxIn['preferences']['cols']));
			?>
			<script>
				$(function() {
					$('.sortable').sortable();
						$('.handles').sortable({
							handle: 'span'
						});
				});
			</script>
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}