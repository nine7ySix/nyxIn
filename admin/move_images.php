<?php

	//                   [nyxIn/admin/move_images.php]
	//
	//	This file allows staff members to move images individually and in
	//	groups from one gallery to another.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('moderate_images'))) {
?>
	<h2>Move Images</h2>
	<?php
		$gallery_id = 0;

		if($nyxIn_Admin_Action=="select_gallery") {
			if(isset($_POST['gallery_id'])&&($_POST['gallery_id']!="")) {
				$gallery_id = $_POST['gallery_id'];
			} else {
				$fail = 1;
			}

			if($fail==0) {

			}
		} else if($nyxIn_Admin_Action=="move_images") {

			if(isset($_POST['move_to'])&&($_POST['move_to']!="")) {
				$move_to = $_POST['move_to'];
			} else {
				$fail = 1;
			}

			if(isset($_POST['to_move'])&&($_POST['to_move']!="")) {
				$to_move = $_POST['to_move'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($to_move as $image_id => $move_status) {
					if($move_status==1) {
						$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."images SET gallery_id='$move_to' WHERE id=$image_id") or die($nyxIn['db']->error);

						$nyxQuery_SelectGalleryMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=$move_to") or die($nyxIn['db']->error);
						$nyxInGallery = $nyxQuery_SelectGalleryMetadata->fetch_object();
						$nyxInGalleryThumbnailFile = $nyxInGallery->thumbnail;


						if(trim($nyxInGalleryThumbnailFile)=="") {
							$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE id='$image_id'") or die($nyxIn['db']->error);
							$nyxIn_Obj_images = $nyxIn_Query_SelectMetadataFrom_images->fetch_object();
							$nyxIn_Var_images_safename = $nyxIn_Obj_images->safename;
							$nyxIn_Var_images_fileextension = $nyxIn_Obj_images->fileextension;
							$nyxIn_Var_images_filename = $nyxIn_Var_images_safename."_thumb.".$nyxIn_Var_images_fileextension;

							$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."galleries SET thumbnail='$nyxIn_Var_images_filename' WHERE id=$move_to") or die($nyxIn['db']->error);
						}
					}
				}
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Select a Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=move_images&amp;nyxIn_Admin_Action=select_gallery">
			<?php nyxInGetGalleriesSelectBox("gallery_id",0,0,1,$gallery_id,1);?>
			<input type="submit" value="Organize">
			<p>You will get the choice to select the images you wish to move.</p>
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
		<h3>Organizing <u><?php echo $nyxIn_Var_galleries_name; ?></u></h3>
		<form name="moderate_images_form" method="post" action="?admin=1&amp;nyxIn_Admin_Menu=move_images&amp;nyxIn_Admin_Action=move_images">
			<script type="text/javascript">
				function toggleApproveReject(image_id) {
					var toggleVar = $('input.moderate_image_'+image_id).val();

					if(toggleVar==0) { // Leave Unmoderated
						$('input.moderate_image_'+image_id).val(1);
						$('td.moderate_image_'+image_id).css("background-color", "#00BD3D") ;
					} else if(toggleVar==1) { //Approve
						$('input.moderate_image_'+image_id).val(0);
						$('td.moderate_image_'+image_id).css("background-color", "#CCD9DD") ;
					}
				}
			</script>
			<p>Move selected images to
				<?php nyxInGetGalleriesSelectBox("move_to",0,0,1,$gallery_id,1);?>.
			</p>
			<?php
				if($nyxIn['preferences']['display_moderated_only']==0) {
					$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id='$gallery_id' AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
				} else if($nyxIn['preferences']['display_moderated_only']==1) {
					$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id='$gallery_id' AND moderation_status='1' AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
				}

				$cols=0;
				echo "<table width='100%'>";
					echo "<tr>";
					while($row = $nyxIn_Query_SelectMetadataFrom_images->fetch_object()) {
						$filename = $row->safename."_thumb.".$row->fileextension;
						echo "<td width='50%' valign='top' class='moderate_image moderate_image_".$row->id."'>";
						?>
							<img src='uploads/<?php echo $filename; ?>' width='100%' style='margin-bottom:5px;' onClick="toggleApproveReject(<?php echo $row->id; ?>)">
							<?php
							nyxIn_SubgalleryDumper($row->gallery_id, 1);
							echo "</div>";
							echo "<input type='hidden' class='moderate_image_".$row->id."' name='to_move[".$row->id."]' value='0'>";
						echo "</td>";
						$cols++;
						if($cols%2==0) {
							echo "</tr><tr>";
						}
					}
					echo str_repeat("<td width='50%' valign='top'></td>", 2-($cols+2)%2);			
					echo "</tr>";
				echo "</table>";
			?>
			<input type="submit" value="Move Images">
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}