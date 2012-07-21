<?php

	//                   [nyxIn/admin/delete_images.php]
	//
	//	This file allows staff members to delete images individually and in
	//	groups from one gallery to another.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('moderate_images'))) {
?>
	<h2>Delete Images</h2>
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
		} else if($nyxIn_Admin_Action=="delete_images") {

			if(isset($_POST['to_delete'])&&($_POST['to_delete']!="")) {
				$to_delete = $_POST['to_delete'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($to_delete as $image_id => $delete_status) {
					if($delete_status==1) {
						$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."images SET deleted_status='1' WHERE id='$image_id'") or die($nyxIn['db']->error);
					}
				}
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Select a Gallery</h3>
		<form method="post" action="?admin=1&amp;nyxIn_Admin_Menu=move_images&amp;nyxIn_Admin_Action=select_gallery">
			<input type="submit" value="Organize">
			<p>You will get the choice to select the images you wish to delete.</p>
		</form>
				<?php
		if($gallery_id==0) {
			$nyxIn_Var_galleries_name = "Home";
		} else {
			$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries AS galleries WHERE galleries.id='$gallery_id' AND galleries.deleted_status='0'") or die($nyxIn['db']->error);
			$nyxIn_Obj_galleries = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object();
			$nyxIn_Var_galleries_name = $nyxIn_Obj_galleries->name;
		}
		?>
		<h3>Organizing <u><?php echo $nyxIn_Var_galleries_name; ?></u></h3>
		<form name="moderate_images_form" method="post" action="?admin=1&amp;nyxIn_Admin_Menu=delete_images&amp;nyxIn_Admin_Action=delete_images">
			<script type="text/javascript">
				function toggleApproveReject(image_id) {
					var toggleVar = $('input.moderate_image_'+image_id).val();

					if(toggleVar==0) { // Leave Unmoderated
						$('input.moderate_image_'+image_id).val(1);
						$('td.moderate_image_'+image_id).css("background-color", "#00BD3D") ;
					} else if(toggleVar==1) { //Approve
						$('input.moderate_image_'+image_id).val(0);
						$('td.moderate_image_'+image_id).css("background-color", "#FE493C") ;
					}
				}
			</script>
			<?php
				if($nyxIn['preferences']['display_moderated_only']==0) {
					$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images as images WHERE images.gallery_id='$gallery_id' AND images.deleted_status='0' ORDER BY images.order_int") or die($nyxIn['db']->error);
				} else if($nyxIn['preferences']['display_moderated_only']==1) {
					$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images as images WHERE images.gallery_id='$gallery_id' AND images.moderation_status='1' AND images.deleted_status='0' ORDER BY images.order_int") or die($nyxIn['db']->error);
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
							echo "<input type='hidden' class='moderate_image_".$row->id."' name='to_delete[".$row->id."]' value='0'>";
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
			<input type="submit" value="Delete Images">
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}