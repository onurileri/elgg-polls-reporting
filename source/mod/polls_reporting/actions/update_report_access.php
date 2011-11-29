<?php
  require_once(dirname(dirname(__FILE__)) . "/lib.php");

   // Load configuration
	global $CONFIG;
	
	gatekeeper();

	set_context('polls_reporting');

	$poll_guid = (int)get_input('poll_guid');
	$report_id = get_input('report_id');
	$new_access_id = get_input('access_id');

	$success = false;
	
	$report_mapper = PollsReporting_ReportMapper::getInstance();
	
	$report = $report_mapper->findByIdAndPollGuid($report_id, $poll_guid);
	
	if($report && $report->canEdit())
	{
		$report->setAccessId($new_access_id);	
	
		$success = $report->save();
	}
	
	echo json_encode($success);
	exit;
?>