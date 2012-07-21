<?php
	$nyxIn['db'] = new mysqli('localhost','root','alpine','test');
	$nyxIn['db_prefix'] = "nyxIn_";

	// Preferences are loaded in this file
	$nyxIn_Query_SelectPreferencesMetadata = $nyxIn['db']->query("SELECT p.* FROM ".$nyxIn['db_prefix']."preferences AS p") or die($nyxIn['db']->error);
		while($row = $nyxIn_Query_SelectPreferencesMetadata->fetch_object()) {
		$nyxIn['preferences'][$row->shorthand] = $row->value;
	}

	$nyxIn['preferences']['colsPercentage'] = 100/$nyxIn['preferences']['cols'];
