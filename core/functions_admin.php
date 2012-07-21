<?php

	function nyxInGetPermissionsSelectBox($name) {
		global $nyxIn;
		echo "<select name='$name'>";
		$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."permissions ORDER BY name") or die($nyxIn['db']->error);  
		while($row = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()) {
			echo "<option value='$row->id'>$row->name</option>";
		}
		echo "</select>";
	}

	$nyxIn_User_Permissions;
	function nyxInReloadStaffPermissions() {
		global $nyxIn;
		global $nyxIn_User_Permissions;

		empty($nyxInFormatTitles);
		$nyxIn_Query_SelectPermissionsFromUser = $nyxIn['db']->query("SELECT permissions FROM ".$nyxIn['db_prefix']."classes WHERE id=".$_COOKIE['nyxIn_Admin']['class_id']) or die($nyxIn['db']->error);  
		$nyxIn_User_Permissions_json = json_decode($nyxIn_Query_SelectPermissionsFromUser->fetch_object()->permissions);

		foreach($nyxIn_User_Permissions_json as $key => $value) {
			$permission_id = $key;
			$nyxIn_Query_SelectPermissionsMetadata = $nyxIn['db']->query("SELECT shorthand FROM ".$nyxIn['db_prefix']."permissions WHERE id=$permission_id") or die($nyxIn['db']->error);  
			$nyxIn_Permissions_shorthand = $nyxIn_Query_SelectPermissionsMetadata->fetch_object()->shorthand;

			$nyxIn_User_Permissions[$nyxIn_Permissions_shorthand] = $value;
		}
	}
		
	function nyxInRequirePermissions($permissions_required) {
		global $nyxIn_User_Permissions;
		$enoughPermissions = true;
		foreach($permissions_required as $permission) {
			if((!isset($nyxIn_User_Permissions[$permission]))||$nyxIn_User_Permissions[$permission]==0) {
				$enoughPermissions = false;
			}
		}

		if($enoughPermissions==false) {
			return false;
		} else {
			return true;
		}
	}

	$nyxIn_Var_SubgalleryDumper;
	function nyxIn_SubgalleryDumper($gallery_id, $init) {
		global $nyxIn;
		global $nyxIn_Var_SubgalleryDumper;
		
		if($init==1) {
			$nyxIn_Var_SubgalleryDumper = "";
		}

		$nyxIn_Query_SelectGalleryMetadata = $nyxIn['db']->query("SELECT id, parent_id, name FROM ".$nyxIn['db_prefix']."galleries WHERE id=".$gallery_id) or die($nyxIn['db']->error);
		
		if($nyxIn_Query_SelectGalleryMetadata->num_rows==0) {
			if($gallery_id==0) {
				$nyxIn_Var_SubgalleryDumper[] = "<span class='ajaxed' onClick='nyxIn_Ajax_Views(\"g\", 0, \"\")'>"."Home"."</span>";
			}
			$nyxIn_Var_SubgalleryDumper = array_reverse($nyxIn_Var_SubgalleryDumper);
			$nyxIn_Var_SubgalleryDumperImploded = implode($nyxIn_Var_SubgalleryDumper, " &#x2756; ");
			echo "<p>".$nyxIn_Var_SubgalleryDumperImploded."</p>";
		} else {
			while($row = $nyxIn_Query_SelectGalleryMetadata->fetch_object()) {
				$nyxIn_Var_SubgalleryDumper[] = $row->name;
				nyxIn_SubgalleryDumper($row->parent_id, 0);
			}
		}
	}