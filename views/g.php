<?php
	//                   [nyxIn/views/g.php]
	//
	//	This file is the view for the gallery page.
	//

	$nyxIn_Frm_ajaxed_id = $_POST['id'];
?>
<p>Quick Navigate: <?php nyxInGetGalleriesSelectBox("nyxIn-gallery_quickswitch",0,0,1,$nyxIn_Frm_ajaxed_id,0); ?></p>
<?php
if(!$nyxIn_Frm_ajaxed_id==0) {
	$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=".$nyxIn_Frm_ajaxed_id." AND deleted_status='0'") or die($nyxIn['db']->error);
	$nyxIn_Obj_galleries = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object();
	$nyxIn_Var_galleries_id = $nyxIn_Obj_galleries->id;
	$nyxIn_Var_galleries_description = $nyxIn_Obj_galleries->description;
	$nyxIn_Var_galleries_locked_status = $nyxIn_Obj_galleries->locked_status;
	$nyxIn_Var_galleries_password = $nyxIn_Obj_galleries->password;

	nyxInFormatTitle($nyxIn_Var_galleries_id, 0);
	if($nyxIn_Var_galleries_locked_status==1) {
		if(isset($_POST['password'])) {
			$nyxIn_Frm_gallery_password = $_POST['password'];
		} else {
			$nyxIn_Frm_gallery_password = 0;
		}

		if(nyxInVerifyPassword($nyxIn_Var_galleries_id, $nyxIn_Frm_gallery_password)==true) {
			$nyxIn_Var_has_access_to_gallery=1;
		} else {
			$nyxIn_Var_has_access_to_gallery=0;
		}
	} else {
		$nyxIn_Var_has_access_to_gallery=1;
	}
	
	if(!$nyxIn_Var_galleries_description=="") {
		echo "<p class='galleryDescription'>$nyxIn_Var_galleries_description</p>";
	}
} else {
	echo "<h3>Home</h3>";
	$nyxIn_Var_has_access_to_gallery=1;
}

if($nyxIn_Var_has_access_to_gallery==1) {
	$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id=".$nyxIn_Frm_ajaxed_id." AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);	
	if(!$nyxIn_Query_SelectMetadataFrom_galleries->num_rows==0) {
		if(!$nyxIn_Frm_ajaxed_id==0) {
			echo "<h4>Sub Galleries</h4>";
		}
		$nyxIn_Var_columns_count=0;
		echo "<table width='100%'>";
			echo "<tr>";
			while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {

				//Handle Image Thumbnails
				if($row->locked_status==1) {
					//If locked
					$nyxIn_gallery_thumbnailurl = $nyxIn['dir']."/assets/images/locked.png";
				} else {
					$nyxIn_gallery_thumbnail = $row->thumbnail;
					if(trim($nyxIn_gallery_thumbnail)=="") {
						$nyxIn_gallery_thumbnailurl = $nyxIn['dir']."/assets/images/empty.png";
					} else {
						$nyxIn_gallery_thumbnailurl = $nyxIn['dir']."/uploads/$row->thumbnail";
					}
				}

				echo "<td width='".$nyxIn['preferences']['colsPercentage']."%' valign='top'>";
					echo "<span class='ajaxed subgallery' onClick=\"nyxIn_Ajax_Views('g', ".$row->id.",nyxIn_gallery_password)\">";
						echo "<img src='".$nyxIn_gallery_thumbnailurl."' width='100%'>";
						echo "<br>";
						echo $row->name;
					echo "</span>";
				echo "</td>";

				$nyxIn_Var_columns_count++;
				if($nyxIn_Var_columns_count%$nyxIn['preferences']['cols']==0) {
					echo "</tr><tr>";
				}
			}

			echo str_repeat("<td width='".$nyxIn['preferences']['colsPercentage']."%' valign='top'></td>", $nyxIn['preferences']['cols']-($nyxIn_Var_columns_count+$nyxIn['preferences']['cols'])%$nyxIn['preferences']['cols']);			
			echo "</tr>";
		echo "</table>";
	}			

	if($nyxIn['preferences']['display_moderated_only']==0) {
		$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id=".$nyxIn_Frm_ajaxed_id." AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
	} else if($nyxIn['preferences']['display_moderated_only']==1) {
		$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE gallery_id=".$nyxIn_Frm_ajaxed_id." AND moderation_status=1 AND deleted_status='0' ORDER BY order_int") or die($nyxIn['db']->error);
	}
	if(!$nyxIn_Query_SelectMetadataFrom_galleries->num_rows==0) {
		echo "<h4>Images</h4>";
		$nyxIn_Var_columns_count=0;
		echo "<table width='100%'>";
			echo "<tr>";
			while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {
				$nyxIn_Var_galleries_filename = $row->safename."_thumb.".$row->fileextension;
				echo "<td width='".$nyxIn['preferences']['colsPercentage']."%' valign='top'>";
					echo "<span class='ajaxed' onClick=\"nyxIn_Ajax_Views('i', ".$row->id.",nyxIn_gallery_password)\">";
						echo "<img src='".$nyxIn['dir']."/uploads/$nyxIn_Var_galleries_filename' width='100%'>";
					echo "</span>";
				echo "</td>";
				$nyxIn_Var_columns_count++;
				if($nyxIn_Var_columns_count%$nyxIn['preferences']['cols']==0) {
					echo "</tr><tr>";
				}
			}
			echo str_repeat("<td width='".$nyxIn['preferences']['colsPercentage']."%' valign='top'></td>", $nyxIn['preferences']['cols']-($nyxIn_Var_columns_count+$nyxIn['preferences']['cols'])%$nyxIn['preferences']['cols']);			
			echo "</tr>";
		echo "</table>";
	}
} else {
	?>
		<p>This gallery is locked with a password.</p>
		<input type="text" name="nyxIn_Frm_gallery_password" class="nyxIn_Frm_gallery_password"> <input type="button" value="Authenticate" onClick="nyxIn_Ajax_Views('g', <?php echo $nyxIn_Var_galleries_id ?>, $('input.nyxIn_Frm_gallery_password').val())">
	<?php
}