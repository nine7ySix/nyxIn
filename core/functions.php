<?php

function nyxInGetGalleriesSelectBox($name, $parent_id, $nestLevel, $showRoot, $default_selection, $admin) {
	//On the Admin Page, Ajax onChange is hidden.
	global $nyxIn;
	if($nestLevel==0) {
		?>
		<select name="<?php echo $name; ?>" <?php if($admin==0) {?>onChange="nyxIn_Ajax_Views('g', this.value, '')"<?php }?>>
		<?php	
	}
	if($showRoot==1) {
		echo "<option value='0'>Home</option>\n";
		$nestLevel++;
	}
	$query = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id='$parent_id' AND deleted_status='0' ORDER BY order_int, name") or die($nyxIn['db']->error);  
	while($row = $query->fetch_object()) {
		echo "<option value='".$row->id."'";
		if($row->id==$default_selection) {
			echo " selected='selected'";
		}
		echo ">".str_repeat("-", $nestLevel).$row->name."</option>\n";
		nyxInGetGalleriesSelectBox("", $row->id, $nestLevel+1, 0, $default_selection, $admin);
	}
	if($nestLevel==0||($showRoot==1&&$nestLevel==1)) {
		echo "</select>\n";
	}
}

$nyxInFormatTitles;
function nyxInFormatTitle($gallery_id, $image) {
	global $nyxIn;
	global $nyxInFormatTitles;

	empty($nyxInFormatTitles);
	
	$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT id, parent_id, name FROM ".$nyxIn['db_prefix']."galleries WHERE id='$gallery_id' AND deleted_status='0'") or die($nyxIn['db']->error);
	
	if($nyxIn_Query_SelectMetadataFrom_galleries->num_rows==0) {
		if($gallery_id==0) {
			$nyxInFormatTitles[] = "<span class='ajaxed' onClick='nyxIn_Ajax_Views(\"g\", 0, \"\")'>"."Home"."</span>";
		}
		$nyxInFormatTitles = array_reverse($nyxInFormatTitles);
		$nyxInFormatTitlesImploded = implode($nyxInFormatTitles, " &#x2756; ");
		if($image==1) {
			$nyxInFormatTitlesImploded .= " &#x2756; image";
		}
		echo "<h3>".$nyxInFormatTitlesImploded;
		echo "</h3>";
		empty($nyxInFormatTitles);
	} else {
		while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {
			$nyxInFormatTitles[] = "<span class='ajaxed' onClick='nyxIn_Ajax_Views(\"g\", $row->id, nyxIn_gallery_password)'>".$row->name."</span>";
			nyxInFormatTitle($row->parent_id, $image);
		}
	}
}

function nyxInVerifyPassword($gallery_id, $password) {
	global $nyxIn;

	if($gallery_id==0) {
		return true;
	} else {
		$nyxIn_Query_SelectMetadataFrom_gallery = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=".$gallery_id." AND deleted_status='0'") or die($nyxIn['db']->error);
		$nyxIn_Obj_gallery = $nyxIn_Query_SelectMetadataFrom_gallery->fetch_object();
		$nyxIn_Var_gallery_id = $nyxIn_Obj_gallery->id;
		$nyxIn_Var_gallery_parent_id = $nyxIn_Obj_gallery->parent_id;
		$nyxIn_Var_gallery_locked_status = $nyxIn_Obj_gallery->locked_status;
		$nyxIn_Var_gallery_password = $nyxIn_Obj_gallery->password;

		if($nyxIn_Var_gallery_locked_status==0) {
			nyxInVerifyPassword($nyxIn_Var_gallery_parent_id, $password);
		} else {
			if($password==$nyxIn_Var_gallery_password) {
				return true;
			} else {
				return false;
			}
		}
	}
}

function nyxIsFreeGallery($gallery_id) {
	global $nyxIn;

	if($gallery_id==0) {
		return true;
	} else {
		$nyxIn_Query_SelectMetadataFrom_galleries = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id=".$gallery_id." AND deleted_status='0'") or die($nyxIn['db']->error);
		$nyxIn_Obj_galleries = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object();

		$nyxIn_Var_galleries_parent_id = $nyxIn_Obj_galleries->parent_id;
		$nyxIn_Var_galleries_locked_status = $nyxIn_Obj_galleries->locked_status;

		if($nyxIn_Var_galleries_locked_status==1) {
			return false;
		} else {
			nyxIsFreeGallery($nyxIn_Var_galleries_parent_id);
		}
	}
}