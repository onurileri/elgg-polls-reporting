<?php
  require_once(dirname(dirname(__FILE__)) . "/lib.php");

   // Load configuration
	global $CONFIG;
	
	gatekeeper();
	action_gatekeeper();

	set_context('polls_reporting');
	$poll_guid = (int)get_input('poll_guid');
	$report_id = get_input('report_id');
	
	$view_report = get_input('view_report');
	$download_csv = get_input('download_csv');

	$report_def_mapper = PollsReporting_ReportDefinitionMapper::getInstance();
	$report_def = $report_def_mapper->findById($report_id);
	
	// @todo Handle this better
	if(!$report_def)
		exit;
	
	$report = $report_def->getReportForPollGuid($poll_guid);
	
	// @todo Handle this better
	if(!$report)
		exit;
	
	/*
	 * Go through each of the report parameters and set up the report with them,
	 * triggering an error if they're required and not available
	 */
	$params = $report_def->getReportParameterDefinitions();
	
	$get_params = array();
	
	$valid = true;
	
	foreach($params as $param)
	{
		$value = get_input($param->getName());
		
		if($param->getRequired() && ($value == ''))
		{
			system_messages(elgg_echo($param->getTitle()) . ' is required');
			$valid = false;
		}
			
		$report->setParameter($param->getName(), $value);
	}
	
	if(!$valid)
	{
		forward('pg/polls/reports/' . rawurlencode($poll_guid));
	}
	else if($view_report)
	{
		forward($report->getUrl());
	}
	else if($download_csv)
	{
		forward($report->getUrl('csv'));
	}
	
	exit;
?>