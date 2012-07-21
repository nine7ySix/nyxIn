<?php

	//                   [nyxIn/admin/moderate_images.php]
	//
	//	This file allows staff members to moderate the uploaded images. Most
	//	of this file is powered by Javascript. This file is important if the
	//	perference [Moderated Image Only] is set to 1.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('moderate_images'))) {
?>
	<h2>Moderate Images</h2>
	<?php
		$gallery_id = 0;

		if($nyxIn_Admin_Action=="moderate") {
			if(isset($_POST['moderate_image'])&&($_POST['moderate_image']!="")) {
				$moderate_images = $_POST['moderate_image'];
			} else {
				$fail = 1;
			}

			if($fail==0) {
				foreach($moderate_images as $image_id => $moderate_status) {
					if($moderate_status==0) {
					} else if($moderate_status==1) {
						$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."images SET moderation_status='1' WHERE id='$image_id'") or die($nyxIn['db']->error);
					} else if($moderate_status==2) {
						$nyxIn['db']->query("UPDATE ".$nyxIn['db_prefix']."images SET deleted_status='1' WHERE id='$image_id'") or die($nyxIn['db']->error);
					}
				}
			}
		}
	?>
	<div id="nyxIn_Admin_Content">
		<?php require("admin/failure_check.php"); ?>
		<h3>Unmoderated Images</h3>
		<form name="moderate_images_form" method="post" action="?admin=1&amp;nyxIn_Admin_Menu=moderate_images&amp;nyxIn_Admin_Action=moderate">
			<p><span style="padding:5px;background-color:#CCD9DD;color:#000">Gray Background:</span> Leave Unmoderated.</p>
			<p><span style="padding:5px;background-color:#00BD3D;color:#FFF">Green Background:</span> Approve.</p>
			<p><span style="padding:5px;background-color:#FE493C;color:#FFF">Red Background:</span> Reject & Delete.</p>
			<script type="text/javascript">
				function toggleApproveReject(image_id) {
					var toggleVar = $('input.moderate_image_'+image_id).val();

					if(toggleVar==0) { // Leave Unmoderated
						$('input.moderate_image_'+image_id).val(1);
						$('td.moderate_image_'+image_id).css("background-color", "#00BD3D") ;
					} else if(toggleVar==1) { //Approve
						$('input.moderate_image_'+image_id).val(2);
						$('td.moderate_image_'+image_id).css("background-color", "#FE493C") ;
					} else if(toggleVar==2) { //Reject
						$('input.moderate_image_'+image_id).val(0);
						$('td.moderate_image_'+image_id).css("background-color", "#CCD9DD") ;
					}
				}
			</script>
			<?php
				$nyxQuery_SelectClassMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE moderation_status='0' AND deleted_status='0'") or die($nyxIn['db']->error);
				$cols=0;
				echo "<table width='100%'>";
					echo "<tr>";
					while($row = $nyxQuery_SelectClassMetadata->fetch_object()) {
						$filename = $row->safename."_thumb.".$row->fileextension;
						echo "<td width='50%' valign='top' class='moderate_image moderate_image_".$row->id."'>";
						?>
							<img src='uploads/<?php echo $filename; ?>' width='100%' style='margin-bottom:5px;' onClick="toggleApproveReject(<?php echo $row->id; ?>)">
							<?php
							nyxIn_SubgalleryDumper($row->gallery_id, 1);
							echo "</div>";
							echo "<input type='hidden' class='moderate_image_".$row->id."' name='moderate_image[".$row->id."]' value='0'>";
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
			<input type="submit" value="Moderate">
		</form>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}