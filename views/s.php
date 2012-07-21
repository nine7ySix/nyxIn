<?php
	//                   [nyxIn/views/s.php]
	//
	//	This file is the view for the stats page.
	//

$nyxIn_Frm_ajaxed_id = $_POST['id'];
?>
<h3>Stats</h3>
<table width="100%">
	<tr>
		<td width="60%"><h4>Gallery Age</h4></td>
		<?php					
			$nyxIn_Var_date = new DateTime();
			$nyxIn_Var_date->setTimestamp($nyxIn['preferences']['timestamp']);
			$interval = $nyxIn_Var_date->diff(new DateTime('now'));
		?>
		<td width="40%"><?php echo $interval->format('%y years, %m months, %d days'); ?></td>
	</tr>
	<tr>
		<td width="60%"><h4>Gallery Birthday</h4></td>
		<td width="40%"><?php echo $nyxIn_Var_date->format('d F, Y'); ?></td>
	</tr>
	<tr>
		<td><h4>Total Image Views</h4></td>
		<?php
			$nyxIn_Query_TotalImageViews = $nyxIn['db']->query("SELECT SUM(views) FROM ".$nyxIn['db_prefix']."images WHERE deleted_status='0'") or die($nyxIn['db']->error);
			$nyxIn_Assoc_TotalImageViews = $nyxIn_Query_TotalImageViews->fetch_assoc();
			$nyxIn_TotalImageViews = $nyxIn_Assoc_TotalImageViews['SUM(views)']
		?>
		<td><?php echo $nyxIn_TotalImageViews; ?></td>
	</tr>
	<tr>
		<td><h4>Number of Images</h4></td>
		<?php
			if($nyxIn['preferences']['display_moderated_only']==0) {
				$nyxIn_Query_NumberOfImages = $nyxIn['db']->query("SELECT COUNT(id) FROM ".$nyxIn['db_prefix']."images WHERE deleted_status='0'") or die($nyxIn['db']->error);
			} else if($nyxIn['preferences']['display_moderated_only']==1) {
				$nyxIn_Query_NumberOfImages = $nyxIn['db']->query("SELECT COUNT(id) FROM ".$nyxIn['db_prefix']."images WHERE moderation_status=1 AND deleted_status='0'") or die($nyxIn['db']->error);
			}
			$nyxIn_Assoc_NumberOfImages = $nyxIn_Query_NumberOfImages->fetch_assoc();
			$nyxIn_NumberOfImages = $nyxIn_Assoc_NumberOfImages['COUNT(id)']
		?>
		<td><?php echo $nyxIn_NumberOfImages; ?></td>
	</tr>
	<tr>
		<td><h4>Number of Galleries + Sub Galleries</h4></td>
		<?php
			$nyxIn_Query_NumberOfGalleries = $nyxIn['db']->query("SELECT COUNT(id) FROM ".$nyxIn['db_prefix']."galleries WHERE deleted_status='0'") or die($nyxIn['db']->error);
			$nyxIn_Assoc_NumberOfGalleries = $nyxIn_Query_NumberOfGalleries->fetch_assoc();
			$nyxIn_NumberOfGalleries = $nyxIn_Assoc_NumberOfGalleries['COUNT(id)']
		?>
		<td><?php echo $nyxIn_NumberOfGalleries; ?></td>
	</tr>
</table>
<br>
<h4>Top 15 Most Viewed Images</h4>
<table width="100%">
	<tr>
		<?php
		if($nyxIn['preferences']['display_moderated_only']==0) {
			$nyxIn_Query_SelectTop15 = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images AS i,".$nyxIn['db_prefix']."galleries AS g WHERE i.deleted_status='0' AND i.gallery_id=g.id AND g.password_status='0' ORDER BY views DESC") or die($nyxIn['db']->error);
		} else if($nyxIn['preferences']['display_moderated_only']==1) {
			$nyxIn_Query_SelectTop15 = $nyxIn['db']->query("SELECT * FROM ".$nyxIn['db_prefix']."images AS i,".$nyxIn['db_prefix']."galleries AS g WHERE i.deleted_status='0' AND i.gallery_id=g.id AND g.locked_status='0' AND i.moderation_status=1 ORDER BY views DESC") or die($nyxIn['db']->error);
		}

		$nyxIn_ViewOrder = 0;
		while($row = $nyxIn_Query_SelectTop15->fetch_object()) {
			if(nyxIsFreeGallery($row->gallery_id)==true) {
				$views = $row->views;
				$safename = $row->safename."_thumb.".$row->fileextension;
				?>
				<td width="20%" valign="top">
					<p class="top15">#<?php echo $nyxIn_ViewOrder+1;?></p>
					<span class="ajaxed" onClick="nyxIn_Ajax_Views('i', <?php echo $row->id ?>,'')">
						<img src="<?php echo $nyxIn['dir']; ?>/uploads/<?php echo $safename; ?>" width="100%">
						<p class="top15">Views: <?php echo $views ?></p>
					</span>
				</td>
				<?php
				$nyxIn_ViewOrder++;
				
				if($nyxIn_ViewOrder%5==0) {
					echo "</tr><tr>";
				}

				if($nyxIn_ViewOrder>15) {
					break;
				}
			}
		}
		echo str_repeat("<td width='20%' valign='top'></td>", 5-(($nyxIn_ViewOrder-1)+5)%5);			
		?>
		</tr>
	</table>