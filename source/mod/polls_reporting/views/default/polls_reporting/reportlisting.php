<?php
	global $CONFIG;

	$report = $vars['report'];
	$report_def = $report->getReportDefinition();
	
	$icon = '<img src="' . $CONFIG->url . 'mod/polls_reporting/images/report_small.gif" />';
	
	$title = htmlentities($report_def->getTitle(), ENT_COMPAT, 'utf8');
	$description = htmlentities($report_def->getDescription(), ENT_COMPAT, 'utf8');
	
	$info = '<div class="report-info"><div class="report-title">' . $title . '</div>';
	$info .= '<div class="report-description">' . $description . '</div></div>';
	
	$csv_url = $report->getUrl('csv');

	$view_form = '';
	
	if(elgg_view_exists('polls_reporting/reports/' . $report->getId() . '/view_form')) {
		$view_form .= elgg_view('polls_reporting/reports/' . $report->getId() . '/view_form', array(
			'report' => $report,
		));
	}
	
	$view_form .= '<div class="view-report-controls"><ul>';

	if($csv_url)
	{
		$view_form .= '<li>' . elgg_view('input/submit', array(
				'internalname' => 'download_csv',
				'value' => elgg_echo('polls_reporting:download_csv'),
			)) . '</li>';
	}

	$view_form .= '<li>' . elgg_view('input/submit', array(
			'internalname' => 'view_report',
			'value' => elgg_echo('polls_reporting:view_report'),
		)) . '</li>';

	$view_form .= '</ul></div>';
	
	$view_form .= elgg_view('input/hidden', array(
		'internalname' => 'report_id',
		'value' => $report->getId(),
	));
	$view_form .= elgg_view('input/hidden', array(
		'internalname' => 'poll_guid',
		'value' => $report->getPollGuid(),
	));
	
	$info = '<div><div class="view-report-form">' . elgg_view('input/form', array(
		'body' => $view_form,
		'action' => $CONFIG->url . 'action/polls_reporting/view_report',
		'method' => 'post',
	)) . '</div>' . $info;
		
	if($report->canEdit())
	{
		$id_attr = 'input_report_access_id_' . $report->getPollGuid() . '_' . $report->getId();
		
		$info .= '<div class="report-edit">'.
		 '<label for="' . $id_attr . '">' . elgg_echo('access') . '</label>'.
			elgg_view('polls_reporting/input/access', array(
				'internalid' => $id_attr,
				'internalname' => 'report_access_id_' . $report->getId(),
				'value' => $report->getAccessId(),
			)).
			'<span class="report-edit-status" id="report_edit_loading_animation_' . $report->getId() . '"></span>'.
			'</div>';
	}
	
	$info .= '<div class="clearfloat"></div></div>';
		
	echo '<li id="polls-reporting-report-listing-' . $report->getId() . '">' . elgg_view_listing($icon, $info) . '</li>';
?>