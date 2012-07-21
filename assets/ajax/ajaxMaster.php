<script type="text/javascript">
function nyxIn_Ajax_Views(type, id, password) {
	// type: { g = gallery, i = image, s = stats }
	$.ajax({
		type: "POST",
		url: "<?php echo $nyxIn['dir']; ?>/index.php?ajax=1",
		data: { type: type, id: id, nyxInGalleryPasswordGet: password},
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

nyxIn_Ajax_Views('g', <?php echo $nyxInGalleryId; ?>,<?php if(isset($_POST['nyxInGalleryPasswordGet'])){ echo "'".$_POST['nyxInGalleryPasswordGet']."'"; } else {echo '\'\''; }?>)

</script>