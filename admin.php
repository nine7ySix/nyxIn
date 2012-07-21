<?php
	//                   [nyxIn/admin.php]
	//
	//	This file is a frame by it's own and is independent of other files,
	//	it is called directly from the nyxIn/index.php and no header
	//	information is called before the HTML of this fie, this allows for
	//	cookies to be set in order to track staff logins.
	//	
	
	if(isset($_GET['logout'])) {
		setcookie("nyxIn_Admin[id]", "", time()-1);
		setcookie("nyxIn_Admin[username]", "", time()-1);
		setcookie("nyxIn_Admin[class_id]", "", time()-1);
		setcookie("nyxIn_Admin[view_gallery_during_maintenace]", "", time()-1);

		if(isset($_GET['usernamechange'])) {
			header("Location: ?admin=1&usernamechange=1");
		} else {
			header("Location: ?admin=1");
		}
	}

	$expire=time()+60*60*24*30;
	if(isset($_GET['login'])) {
		$username = $_POST['username'];
		$password_hash = sha1($_POST['password']);
		$nyxQuery_SelectStaff = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."staff WHERE username='".$username."' AND password_hash='$password_hash'") or die($nyxIn['db']->error);	
		if($nyxQuery_SelectStaff->num_rows==1) {
			$nyxQuery_User = $nyxQuery_SelectStaff->fetch_object();

			setcookie("nyxIn_Admin[id]", $nyxQuery_User->id, $expire);
			setcookie("nyxIn_Admin[username]", $nyxQuery_User->username, $expire);
			setcookie("nyxIn_Admin[class_id]", $nyxQuery_User->class_id, $expire);
		}
		header("Location: ?admin=1");
	}

	if (isset($_COOKIE['nyxIn_Admin']['id'])) {
		//Check for username changes if logged in
		$nyxQuery_UsernameFromStaff = $nyxIn['db']->query("SELECT username FROM ".$nyxIn['db_prefix']."staff WHERE id='".$_COOKIE['nyxIn_Admin']['id']."'") or die($nyxIn['db']->error);	
		if($nyxQuery_UsernameFromStaff->fetch_object()->username!=$_COOKIE['nyxIn_Admin']['username']) {
			header("Location: ?admin=1&logout=1&usernamechange=1");
		}
	}

	if (isset($_COOKIE['nyxIn_Admin']['id'])) {
		/* RELOAD PERMISSIONS */
		nyxInReloadStaffPermissions();
		setcookie("nyxIn_Admin[view_gallery_during_maintenance]", $nyxIn_User_Permissions['view_gallery_during_maintenace'], $expire);
	}
?>
<html>
	<head>
		<title>nyxIn - Administration</title>
		<style type="text/css">
			* {
				margin:0;
				padding:0;
			}
		
			body {
				font-family:Arial;
				background-color:#262626;
			}
			
			#nyxIn_Admin_MainContainer {
				margin:0 auto 10px auto;
				width:600px;
			}
			
			#nyxIn_Admin_MainContainer h1 {
				margin-bottom:5px;
				color:#F8F8F8;
			}
			
			#nyxIn_Admin_ContentContainer {
				background-color:#FFF;
				padding:20px;
				border-radius:5px;
			}
			
			/* Admin Files CSS */
			
			#nyxIn_Admin_ContentContainer h2 {
				text-align:center;
				border-bottom:1px solid #000;
			}
			
			#nyxIn_Admin_ContentContainer p {
				margin:0 0 5px 0;
				font-size:10pt;
				line-height:20px;
			}

			#nyxIn_Admin_Content .failure {
				padding:10px;
				background-color:#000;
				color:#F66;
				font-size:20px;
				font-weight:bold;
			}
			
			#nyxIn_Admin_Content ul {
				margin:0 0 0 30px;
				list-style-type: square;
			}
			
			#nyxIn_Admin_Content ul li {
			}
			
			#nyxIn_Admin_Content ul li a {
				color:#4C4C4C;
				text-decoration:none;
			}

			#nyxIn_Admin_Content ul.sortable {
				margin:0;
			}

			#nyxIn_Admin_Content ul.sortable.grid li {
				<?php 
					$length_for_organize_page = 500/$nyxIn['preferences']['cols'];
					$margin_for_organize_page = 5;
				?>
				width:<?php echo $length_for_organize_page; ?>;
				height:<?php echo $length_for_organize_page; ?>;
				margin:0 5px 5px 0;
				float: left;
				text-align: center;
			}

			#nyxIn_Admin_Content {
				padding:10px;
			}
			
			#nyxIn_Admin_Content h3 {
				margin:10px 0 10px 0;
			}
			
			#nyxIn_Admin_Content table tr td.moderate_image {
				padding:10px;
				background-color:#CCD9DD;
			}

			#nyxIn_Admin_Footer {
				color: #E6E6E6;
				margin:5px 0 0 0;
				text-align:center;
			}
			
			#nyxIn_Admin_Footer a {
				color: #FFF;
				font-size:13px;
				text-decoration:none;
			}
			
			#nyxIn_Admin_Footer a:hover {
				text-decoration:underline;
			}
		</style>
		<script type='text/javascript' src='assets/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='assets/js/jquery.sortable.min.js'></script>
	</head>
	<body>
		<div id="nyxIn_Admin_MainContainer">
			<h1>nyxIn Administration</h1>
			<div id="nyxIn_Admin_ContentContainer">
				<?php
					if(isset($_GET['usernamechange'])) {
						?>
						<p class="usernamechange">Your username has appeared to have been changed, you have been logged out.</p>
						<?php
					}

					if (isset($_COOKIE['nyxIn_Admin']['id'])) {

						/* RELOAD PERMISSIONS */
						nyxInReloadStaffPermissions();
						$fail = 0; //If fail = 1, no queries will be run.

						if(isset($_GET['nyxIn_Admin_Menu'])) {
							$nyxIn_Admin_Menu = $_GET['nyxIn_Admin_Menu'];
						} else {
							$nyxIn_Admin_Menu = "index";
						}
						
						if(isset($_GET['nyxIn_Admin_Action'])) {
							$nyxIn_Admin_Action = $_GET['nyxIn_Admin_Action'];
						} else {
							$nyxIn_Admin_Action = "";
						}

						$nyxInAdminTools[] = "index";
						$nyxInAdminTools[] = "upload";
						$nyxInAdminTools[] = "galleries_management";
						$nyxInAdminTools[] = "gallery_customization";
						$nyxInAdminTools[] = "gallery_organization";
						$nyxInAdminTools[] = "subgallery_organization";
						$nyxInAdminTools[] = "move_images";
						$nyxInAdminTools[] = "delete_images";
						$nyxInAdminTools[] = "moderate_images";
						$nyxInAdminTools[] = "manage_staff_classes";
						$nyxInAdminTools[] = "manage_staff";
						$nyxInAdminTools[] = "preferences";
						$nyxInAdminTools[] = "reset";
						$nyxInAdminTools[] = "account";
						
						if(in_array($nyxIn_Admin_Menu, $nyxInAdminTools)) {
							require("admin/$nyxIn_Admin_Menu.php");
						}

					} else {
					  	?>
						<form name="nyxIn_Admin_LoginForm" method="post" action="?admin=1&amp;login=1">
							<table width="100%">
								<tr>
									<td width="30%"><p>Username:</p></td>
									<td width="70%"><input name="username" type="text"></td>
								</tr>
								<tr>
									<td><p>Password:</p></td>
									<td><input name="password" type="password"></td>
								</tr>
							</table>
							<input type="submit" value="Login">
						</form>
						<?php
					}
				?>
			</div>
			<div id="nyxIn_Admin_Footer">
				<?php
				if (isset($_COOKIE['nyxIn_Admin']['id'])) {
					?>
					<a href="?admin=1&amp;logout=1">Logout (<b><?php echo $_COOKIE['nyxIn_Admin']['username']?></b>)</a> &#x2022; <a href="?admin=1">Admin Panel</a> &#x2022; <a href="#=1" onClick="window.close()">Close</a>
					<?php
				} else {
					?>
					 <a href="#=1" onClick="window.close()">Close</a>
					<?php
				}
				?>
			</div>
		</div>
	</body>
</html>