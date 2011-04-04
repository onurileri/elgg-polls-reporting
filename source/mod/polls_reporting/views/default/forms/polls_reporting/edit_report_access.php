<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	global $CONFIG;
	
	$poll = $vars['poll'];
	$reports = $vars['reports'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body .= '<p>';
		$body = elgg_echo('polls_reporting:select_report');
		$body .= '<p>';
		$body .= '<ul>';

		$any_report_is_editable = false;
		
		foreach ($reports as $report)
		{
			$body .= elgg_view('polls_reporting/reportlisting', array('report' => $report));
			
			if(!$any_report_is_editable && $report->canEdit()) {
				$any_report_is_editable = true;
			}
		}

		$body .= '</ul>';

		if($any_report_is_editable)
		{
			$body .= elgg_view('input/submit',array(
				'internalname' => 'submit_button',
				'value' => elgg_echo('save'),
			));
		}
		
		$body = elgg_view('page_elements/contentwrapper', array('body' => $body));
		
		$body .= elgg_view('input/hidden', array(
			'internalname' => 'poll_guid',
			'value' => $poll->getGUID(),
		));
		
		echo elgg_view('input/form', array(
			'action' => $CONFIG->url . 'action/polls_reporting/edit_reports',
			'method' => 'post',
			'body' => $body,
		));
	}

?>
