<?php
	//                   [nyxIn/admin/subgalleries_organization.php]
	//
	//	This file deals with organization and display of subgalleries in a gallery.
	//

// Anti-Exploit Check
if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
	die();
}

if(nyxInRequirePermissions(array('subgallery_organization'))) {
?>
	<h2>Sub Gallery Organization</h2>
	<?php
		$gallery_id = 0;

		if($nyxIn_Admin_Action=="select_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}
		} else if($nyxIn_Admin_Action=="organize_subgalleries") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}

			if(isset($_POST['organized_subgalleries'])&&($_POST['organized_subgalleries']!="")) {
				$organized_subgalleries = $_POST['organized_subgalleries'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($organized_subgalleries as $key => $subgallery_id) {
					$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET order_int='$key' WHERE id='$subgallery_id'") or die($nyxIn['db']->error);
				}
			}

		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Select a Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=subgallery_organization&amp;nyxIn_Admin_Action=select_gallery">
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
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=subgallery_organization&amp;nyxIn_Admin_Action=organize_subgalleries">
			<h3>Organizing <u><?php echo $nyxIn_Var_galleries_name; ?></u> <input type="submit" value="Organize"></h3>
			<input type="hidden" name="gallery_id" value="<?php echo $gallery_id; ?>">
			<ul class="sortable grid">
				<?php
					$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id='$gallery_id' AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);

					$numberOfImages = 0;
					while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {
						$filename = $row->thumbnail;
						if(trim($filename)=="") {
							$filename = "assets/images/empty.png";
						} else {
							$filename = "uploads/$row->thumbnail";
						}

					?>
						<li><input type="hidden" name="organized_subgalleries[]" value="<?php echo $row->id?>"><img width="100%" src="<?php echo $filename; ?>"><br><p><?php echo $row->name; ?></p></li>
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