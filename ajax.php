<?php
	//                   [nyxIn/ajax.php]
	//
	//	This file is called by the use of Ajax to dynamically load the 
	//	galleries, images and stats page. The gallery portion of this file
	//	is reffered to as {g}, the image portion of this file is reffered to 
	//	as {i} and the stats portion of this file is reffered to as {s}.
	//	The views for {g}, {i} and {s} can be found in nyxIn/views/ folder
	//

	if(!isset($nyxIn['dir'])) {
		break;
	}

	if($nyxIn['preferences']['maintenance_mode']==0||isset($_COOKIE['nyxIn_Admin']['id'])) {
		if(isset($_COOKIE['nyxIn_Admin']['id'])) {
			?>
			<p class="staff_notification">
				<?php
				if($nyxIn['preferences']['maintenance_mode']==1) {
					?>
					Maintenance Mode is currently active.<br>
					<?php
				}
				?>
				Logged in as <u><?php echo $_COOKIE['nyxIn_Admin']['username']?></u>
			</p>
			<?php
		}

		if($_COOKIE['nyxIn_Admin']['view_gallery_during_maintenance']==true) {
			$type = $_POST['type'];

			if($type=="g") {
				require("views/g.php");
			} else if($type=="i") {
				require("views/i.php");
			} else if($type=="s") {
				require("views/s.php");
			}
		} else {
		// Maintenance Mode = On && Not enough permission
		?>
		<p>Viewing the Gallery during Maintenance Mode is not active for your user class.</p>
		<?php			
		}
		
	} else if($nyxIn['preferences']['maintenance_mode']==1){
		// Maintenance Mode = On
		?>
		<h3>Maintenance Mode is On</h3>
		<p>Please come back later.</p>
		<?php
	}
?>