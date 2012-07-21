<?php
	//                   [nyxIn/admin/failure_check.php]
	//
	//	This file is called through mostly all admin pages that have forms.
	//	A variable $fail is set on nyxIn/admin.php to 0 and whenever there
	//	is a failure during the validation of the inputs on one of the admin
	//	pages, $fail is set to have a value of 1. This file is called before
	//	any forms on the admin pages.
	//	

	if($fail==1) {
		?>
		<p class="failure">Your request has failed.<br>There was something wrong with your inputs.</p>
		<?php
	}