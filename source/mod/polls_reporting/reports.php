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
	
	$header = elgg_view('polls/breadcrumbs', array('item' => $item, 'extra' => $trail_extra));
	
	$title = $item->title;

	$content = '';
	
	if ($report)
	{
		if(get_input('view') == 'csv')
		{
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment');
			header('Cache-Control: private, must-revalidate');
			header('Pragma: cache');
			
			if(elgg_view_exists('polls_reporting/reports/' . $report->getId() . '/poll_type_' . $item->voting_type)) {
				$view = 'polls_reporting/reports/' . $report->getId() . '/poll_type_' . $item->voting_type;
			} else {
				$view = 'polls_reporting/reports/' . $report->getId();
			}			
			
			echo elgg_view($view, array(
				'poll' => $item
			));
			exit;
		}
		else
		{
			if(elgg_view_exists('polls_reporting/reports/' . $report->getId() . '/poll_type_' . $item->voting_type)) {
				$view = 'polls_reporting/reports/' . $report->getId() . '/poll_type_' . $item->voting_type;
			} else {
				$view = 'polls_reporting/reports/' . $report->getId();
			}
			
			$content .= elgg_view($view, array('poll' => $item));
		}
	}
	else
	{
		$report_mapper = PollsReporting_ReportMapper::getInstance();
		$reports = $report_mapper->findAllByPollGuidForUserGuid($item->getGuid(), elgg_get_logged_in_user_guid());
		
		$content .= elgg_view('polls_reporting/select_report',
					array('poll' => $item, 'reports' => $reports));

		// If any of the reports are editable then add a button to change them to the header
		foreach ($reports as $report)
		{
			if($report->canEdit()) {
				elgg_register_menu_item('title', array(
					'name' => 'edit_button_top',
					'text' => elgg_echo('polls_reporting:edit_reports_access'),
					'link_class' => 'polls-reporting-edit-reports-button elgg-button elgg-button-action',
				));
				
				break;
			}
		}
	}
	
//	$buttons = elgg_view_menu('title', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
	
	$header .= elgg_view('page/layouts/content/header', array('title' => $title));
	
	$content = '<div id="polls_reporting_reportlist">' . $content . '</div>';

	$body .= elgg_view_layout('content', array(
		'content' => $content,
		'header' => $header,
		'filter' => '',
	));
	
	// Finally draw the page
	echo elgg_view_page($title, $body);	
?>
