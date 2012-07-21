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
		echo "<h3>".$nyxInFormatTitlesImploded."</h3>";
		empty($nyxInFormatTitles);
	} else {
		while($row = $nyxIn_Query_SelectMetadataFrom_galleries->fetch_object()) {
			$nyxInFormatTitles[] = "<span class='ajaxed' onClick='nyxIn_Ajax_Views(\"g\", $row->id, \"\")'>".$row->name."</span>";
			nyxInFormatTitle($row->parent_id, $image);
		}
	}
}