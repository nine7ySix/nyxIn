<script type="text/javascript">

var nyxIn_gallery_password = "";

function nyxIn_Ajax_Views(type, id, password) {

	nyxIn_gallery_password = password;
	// type: { g = gallery, i = image, s = stats }
	$.ajax({
		type: "POST",
		url: "<?php echo $nyxIn['dir']; ?>/index.php?ajax=1",
		data: { type: type, id: id, password: password},
		dataType: "html"
	}).done(function(data) {
		var nyxInAjax = $('#nyxIn_Ajax');
            nyxInAjax.html(data);
	});
}

<?php
	if(isset($_POST['nyxInGalleryId'])) {
		$nyxInGalleryId = $_POST['nyxInGalleryId'];
	} else {
		$nyxInGalleryId = 0;
	}
	
?>

nyxIn_Ajax_Views('g', <?php echo $nyxInGalleryId; ?>,nyxIn_gallery_password);

</script>