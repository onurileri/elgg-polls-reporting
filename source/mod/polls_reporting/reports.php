<?php

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	gatekeeper();
	
	global $CONFIG;

	$item_guid = get_input('item_guid');
	$report_id = get_input('report_id');
	
	$matches = null;
	if(preg_match('/^(.*)\.csv$/', $report_id, $matches))
	{
		$report_id = $matches[1];
		set_input('view', 'csv');
	}
	
	set_context('polls');
	
	$item = get_entity($item_guid);
	if (!$item) forward();

	$container_guid = $item->container_guid;

	if ($container_guid)
	{
		set_page_owner($container_guid);
	}
	else
	{
		set_page_owner($item->owner_guid);
	}

	$report = null;
	
	if($report_id)
	{
		$report_mapper = PollsReporting_ReportMapper::getInstance();
		$report = $report_mapper->findByIdAndPollGuid($report_id, $item_guid);
	}
	
	// Breadcrumbs
	$trail_extra = array(
		array('title' => elgg_echo('polls_reporting:reporting'), 'url' => $CONFIG->url . 'pg/polls/reports/' . $item->guid),
	);

	if ($report)
	{
		$trail_extra[] = array('title' => $report->getTitle(), 'url' => $CONFIG->url . 'pg/polls/reports/' . $item->guid . '/' . $report->getId());
	}
	
	$body = elgg_view('polls/breadcrumbs', array('item' => $item, 'extra' => $trail_extra));

	$title = $item->title;
	$body .= elgg_view_title($title);

	if ($report)
	{
		if(get_input('view') == 'csv')
		{
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment');
			header('Cache-Control: private, must-revalidate');
			header('Pragma: cache');
			echo elgg_view('polls_reporting/reports/' . $report->getId(), array(
				'poll' => $item
			));
			exit;
		}
		else
		{
			$body .= elgg_view('polls_reporting/reports/' . $report->getId(),
				array('poll' => $item));
		}
	}
	else
	{
		$report_mapper = PollsReporting_ReportMapper::getInstance();
		$reports = $report_mapper->findAllByPollGuidForUserGuid($item->getGuid(), get_loggedin_userid());
		
		$body .= elgg_view('polls_reporting/select_report',
					array('poll' => $item, 'reports' => $reports));
	}

	$body = '<div id="polls_reporting_reportlist">' . $body . '</div>';
	
	$body = elgg_view_layout('two_column_left_sidebar', '', $body, $sidebar);
	
	// Finally draw the page
	page_draw($title, $body);

?>
