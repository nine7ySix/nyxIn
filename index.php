<?php
	//                   [nyxIn/index.php]
	//
	//	This file is the controller for nyxIn, everything links here,
    //	including the Admin Panel, the Ajax and the main Gallery it routes
	//	the files to the actual files that do the actions. It checks for
	//	specific parameters in the URL. The only time no parameters are
	//	passed is when it is called from the actual website.
	//

	//	Possibly the most important variable that it deservers it's own
	//	comment. It's just the name of the folder the nyxIn installation
	//	is in.
	$nyxIn['dir'] = "nyxIn";

	if(isset($_GET['admin'])) {
		require("core/variables.php");
		require("core/functions.php");
		require("core/functions_admin.php");
		require("admin.php");
	} else if(isset($_GET['ajax'])){
		require("core/variables.php");
		require("core/functions.php");
		require("ajax.php");
	} else {		
		require($nyxIn['dir']."/core/variables.php");
		require($nyxIn['dir']."/core/functions.php");
		require($nyxIn['dir']."/main.php");
	}
?>