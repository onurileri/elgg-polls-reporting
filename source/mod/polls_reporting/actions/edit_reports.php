<?php
  require_once(dirname(dirname(__FILE__)) . "/lib.php");

  // Load configuration
	global $CONFIG;
	
	gatekeeper();
	action_gatekeeper();

	set_context('polls_reporting');

	$poll_guid = (int)get_input('poll_guid');

	// Get all the available reports
	$report_definition_mapper = PollsReporting_ReportDefinitionMapper::getInstance();
	$report_definitions = $report_definition_mapper->findAll();

	foreach($report_definitions as $report_definition)
	{
		$new_access_id = get_input('report_access_id_' . $report_definition->getId());
		
		if($new_access_id != '')
		{
			$report = $report_definition->getReportForPollGuid($poll_guid);
			
			if(!$report || !$report->canEdit())
				continue;

			$report->setAccessId($new_access_id);
			$report->save();
		}
	}
	
	system_message(elgg_echo('polls_reporting:report_list_saved'));
	
	forward('pg/polls/reports/' . $poll_guid);
	exit;
?>