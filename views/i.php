<?php
	//                   [nyxIn/views/i.php]
	//
	//	This file is the view for the image page.
	//

$nyxIn_Frm_ajaxed_id = $_POST['id'];

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
