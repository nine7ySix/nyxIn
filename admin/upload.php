<?php
	//                   [nyxIn/admin/upload.php]
	//
	//	This file deals with the forms for the uploading of images. The
	//	used was the File Uploader by Andrew Valums. His Github can
	//	be found here: https://github.com/valums and the page for the file
	//	uploader can be found on https://github.com/valums/file-uploader.
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin']['id'])) {
		die();
	}

if(nyxInRequirePermissions(array('upload'))) {
	?>
	<h2>Upload Pictures</h2>
	<script src="admin/upload_assets/fileuploader.js" type="text/javascript"></script>
	<link href="admin/upload_assets/fileuploader.css" rel="stylesheet" type="text/css">	
	<div id="nyxIn_Admin_Content">
		<script>        
		    function createUploader(gallery_id, gallery_name, hasSubGroups){            
		        var uploader = new qq.FileUploader({
					nyxInID: gallery_id,
					nyxInHeading: gallery_name,
					nyxhasSubGroups: hasSubGroups,
		            element: document.getElementById('nyxIn-upload_'+gallery_id),
		            action: 'uploadHandler.php?gallery_id='+gallery_id,
		            debug: true
		        });           
		    }
		</script>
		<p>You can drag-and-drop files onto the dropzones to upload.</p>
		<h3>Upload to Root</h3>
		<div id="nyxIn-upload_0">		
			<noscript>			
				<p>Please enable JavaScript to use file uploader.</p>
			</noscript>         
		</div>
		<br>
		<script>        
		    window.onload = createUploader(0, 'Root', 0);     
		</script>
		<?php
		if(isset($_GET['parent_id'])) {
			$parent_id = $_GET['parent_id'];
			$query = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE id='$parent_id' AND deleted_status='0'") or die($nyxIn['db']->error);
			$gallery_name = $query->fetch_object()->name;  
		} else {
			$parent_id = 0;
			$gallery_name = "Root";
		}	
		?>
		<br>
		<h3>Parent Gallery: <?php echo $gallery_name; ?></h3>	
		<?	
		$query = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id='$parent_id' AND deleted_status='0' ORDER BY order_int, name") or die($nyxIn['db']->error);
		while($row = $query->fetch_object()) {
			$gallery_id = $row->id;
			$gallery_name = $row->name;
			?>
			<div id="nyxIn-upload_<?php echo $gallery_id ?>">		
				<noscript>			
					<p>Please enable JavaScript to use file uploader.</p>
				</noscript> 
			</div>
			<br>
			<br>
			<?php
			$query_CheckForSubGroups = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."galleries WHERE parent_id='$gallery_id' AND deleted_status='0'") or die($nyxIn['db']->error);
			if($query_CheckForSubGroups->num_rows>0) {
				$hasSub = 1;
			} else {
				$hasSub = 0;
			}
			?>
			<script>        
			    window.onload = createUploader(<?php echo $gallery_id ?>, '<?php echo $gallery_name ?>', <?php echo $hasSub; ?>);     
			</script>    
			<?php
		}
		?>
		<br>
	</div>
<?php
} else {
	require("admin/permission_error.php");
}