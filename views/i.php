<?php
	//                   [nyxIn/views/i.php]
	//
	//	This file is the view for the image page.
	//

	$nyxIn_Frm_ajaxed_id = $_POST['id'];
	$nyxIn_Frm_password = $_POST['password'];

	$nyxIn_Query_Update_galleries = $nyxIn['db']->query("UPDATE  ".$nyxIn['db_prefix']."images SET views=views+1 WHERE id=".$nyxIn_Frm_ajaxed_id) or die($nyxIn['db']->error); //Update Views
	$nyxIn_Query_SelectMetadataFrom_images = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images WHERE id=".$nyxIn_Frm_ajaxed_id." AND deleted_status='0'") or die($nyxIn['db']->error);

	$nyxIn_Obj_images = $nyxIn_Query_SelectMetadataFrom_images->fetch_object();

	$nyxIn_Var_images_id = $nyxIn_Obj_images->id;
	$nyxIn_Var_images_gallery_id = $nyxIn_Obj_images->gallery_id;
	$nyxIn_Var_images_upload_timestamp = $nyxIn_Obj_images->upload_timestamp;
	$nyxIn_Var_images_views = $nyxIn_Obj_images->views;
	$nyxIn_Var_images_safename = $nyxIn_Obj_images->safename.".".$nyxIn_Obj_images->fileextension;
	$nyxIn_Var_images_filename = $nyxIn_Obj_images->filesize;

	$nyxIn_Var_images_name = $nyxIn_Obj_images->name;
	$nyxIn_Var_images_description = $nyxIn_Obj_images->description;


	$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=".$nyxIn_Var_images_gallery_id." AND deleted_status='0'") or die($nyxIn['db']->error);
	$nyxIn_Obj_galleries = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object();
	$nyxIn_Var_galleries_locked_status = $nyxIn_Obj_galleries->locked_status;

	if($nyxIn_Var_galleries_locked_status==1) {
		if(isset($_POST['password'])) {
			$nyxIn_Frm_gallery_password = $_POST['password'];
		} else {
			$nyxIn_Frm_gallery_password = 0;
		}

		if(nyxInVerifyPassword($nyxIn_Var_images_gallery_id, $nyxIn_Frm_gallery_password)==true) {
			$nyxIn_Var_has_access_to_gallery=1;
		} else {
			$nyxIn_Var_has_access_to_gallery=0;
		}

	} else {
		$nyxIn_Var_has_access_to_gallery=1;
	}

	if($nyxIn_Var_has_access_to_gallery==1) {
		?>
		<p>Quick Navigate: <?php nyxInGetGalleriesSelectBox("nyxIn-gallery_quickswitch",0,0,1,$nyxIn_Var_images_gallery_id,0); ?></p>
		<?php
			nyxInFormatTitle($nyxIn_Var_images_gallery_id, 1);

		echo "<img src='".$nyxIn['dir']."/uploads/$nyxIn_Var_images_safename' width='100%'>";
		echo "Views: $nyxIn_Var_images_views";
		echo "<br>";
		echo "Filesize: $nyxIn_Var_images_filename bytes";

		if(!$nyxIn_Var_images_description=="") {
			echo "<p class='galleryDescription'>$nyxIn_Var_images_description</p>";
		}	
	} else {
	}
