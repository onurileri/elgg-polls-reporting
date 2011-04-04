<?php
	global $CONFIG;
	
	$report = $vars['report'];
	?>
<div class="view-report-input">
	<label id="polls_reporting_candidate_trend_candidate_label" for="polls_reporting_candidate_trend_candidate"><?php echo elgg_echo('polls:candidate:title'); ?>
	<?php
		echo elgg_view('input/text', array(
			'internalname' => 'candidate',
			'class' => 'candidate_input',
			'value' => $vars['candidate'],
			'internalid' => 'polls_reporting_candidate_trend_candidate'
		));
	?>
	</label>
	<input type="hidden" name="candidate_guid" id="polls_reporting_candidate_trend_candidate_guid">
</div>
<script language="javascript" type="text/javascript">
	$('#polls_reporting_candidate_trend_candidate_label input').autocomplete(
		"<?php echo $CONFIG->url; ?>mod/polls_reporting/autocomplete_candidates.php?poll_guid=<?php echo $report->getPollGuid(); ?>",
		{ mustMatch: true, matchContains: true }
	).result(function(event, data, formatted) {
		$('#polls_reporting_candidate_trend_candidate_guid').val(data[1]);
		PollsReporting.setViewReportButtonsDisabled("<?php echo $report->getId(); ?>", (data[1] == ""));
	}).bind('change', function() {
		PollsReporting.setViewReportButtonsDisabled("<?php echo $report->getId(); ?>", true);
	});

	$(document).ready(function() {
		PollsReporting.setViewReportButtonsDisabled("<?php echo $report->getId(); ?>", true);
	});
</script>