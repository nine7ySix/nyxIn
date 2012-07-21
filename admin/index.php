<?php
	//                   [nyxIn/admin/index.php]
	//
	//	This file is the main Admin Panel menu. Permissions are checked before
	//	any of the admin pages are linked to. 
	//

	// Anti-Exploit Check
	if(!isset($_COOKIE['nyxIn_Admin'])) {
		die();
	}
?>
<h2>Admin Panel</h2>
<div id="nyxIn_Admin_Content">
	<table width="100%">
		<tr>
			<td width="50%"><center><h3>Gallery</h3></center></td>
			<td width="50%"><center><h3>Administration</h3></center></td>
		</tr>
		<tr>
			<td valign="top">
				<ul>
					<?php
						if(nyxInRequirePermissions(array('upload'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=upload">Upload Pictures</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('galleries_management'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=galleries_management">Galleries Management</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('gallery_customization'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=gallery_customization">Gallery Customization</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('gallery_organization'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=gallery_organization">Gallery Organization</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('subgallery_organization'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=subgallery_organization">Sub-Gallery Organization</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('move_images'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=move_images">Move Images</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('delete_images'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=delete_images">Delete Images</a></p></li>
							<?php
						}
					?>
				</ul>
			</td>
			<td valign="top">
				<ul>
					<?php
						if(nyxInRequirePermissions(array('manage_staff_classes'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=manage_staff_classes">Manage Staff Classes</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('moderate_images'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=moderate_images">
								Moderate Images
								<?php
									$nyxIn_Query_NumberOfUnmoderated = $nyxIn['db']->query("SELECT COUNT(id) FROM ".$nyxIn['db_prefix']."images WHERE moderation_status='0' AND deleted_status='0'") or die($nyxIn['db']->error);
									$nyxIn_Assoc_NumberOfUnmoderated = $nyxIn_Query_NumberOfUnmoderated->fetch_assoc();
									$nyxIn_NumberOfUnmoderated = $nyxIn_Assoc_NumberOfUnmoderated['COUNT(id)'];
								?>
								(<b><?php echo $nyxIn_NumberOfUnmoderated; ?></b>)
							</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('manage_staff'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=manage_staff">Manage Staff</a></p></li>
							<?php
						}
					?>
					<?php
						if(nyxInRequirePermissions(array('preferences'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=preferences">Preferences</a></p></li>
							<?php
						}
					?>	
					<?php
						if(nyxInRequirePermissions(array('reset'))==true) {
							?>
							<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=reset">Reset nyxIn</a></p></li>
							<?php
						}
					?>		
					<hr>
						<li><p><a href="?admin=1&amp;nyxIn_Admin_Menu=account">Account</a></p></li>			
				</ul>			
			</td>
		</tr>
	</table>
</div>