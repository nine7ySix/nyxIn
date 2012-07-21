<?php
	$javascriptAssets[] = "jquery-1.7.2.min";
	$javascriptAssets[] = "jquery.easing.min";
	$javascriptAssets[] = "jquery.sortable.min";

	foreach($javascriptAssets as $javascriptAsset) {
		echo "\n<script type='text/javascript' src='$nyxIn[dir]/assets/js/$javascriptAsset.js'></script>";
	}
	echo "\n";
?>