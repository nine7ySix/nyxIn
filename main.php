<?php
	//                   [nyxIn/main.php]
	//
	//	This file is the is the main Gallery view page for nyxIn, it is
	//	only loaded once when the require("nyxIn/index.php"); is called
	//	on the actual website. The gallery is dynamically loaded on 
	//	within the <div id="nyxIn_Ajax"></div>.
	//
?>
<div id="nyxIn">
	<div id="nyxIn_Ajax"></div>
	<?php require $nyxIn["dir"]."/assets/js/javascriptAssets.php" ?>
	<?php require $nyxIn["dir"]."/assets/ajax/ajaxMaster.php" ?>
	<div id="nyxIn_Footer">
		<span class="ajaxed" onClick="nyxIn_Ajax_Views('g', 0,'').focus()">Home</span> &#x2022; <span class="ajaxed" onClick="window.open('<?php echo $nyxIn['dir']?>/index.php?admin=1', '_blank', 'height=400, width=700')">Admin</span> &#x2022; <span class="ajaxed" onClick="nyxIn_Ajax_Views('s', 0,'')">Stats</span>
		<br>
		<a href="http://fuzzicode.com">nyxIn</a>
	</div>
</div>