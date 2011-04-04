<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	global $CONFIG;
	
	$poll = $vars['poll'];
	$reports = $vars['reports'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

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
			$body = '<div class="reports-controls"><ul><li>' .
				elgg_view('input/button',array(
					'internalname' => 'edit_button_top',
					'value' => elgg_echo('polls_reporting:edit_reports_access'),
					'class' => 'edit-reports-button submit_button',
					'type' => 'button',
				)) . 
				'</li></ul></div><div class="clearfloat"></div>' . $body;
/*
			$body .= '<div class="reports-controls"><ul><li>' .
				elgg_view('input/button',array(
					'internalname' => 'edit_button_bottom',
					'value' => elgg_echo('polls_reporting:edit_reports_access'),
					'class' => 'edit-reports-button submit_button',
					'type' => 'button',
				)) . 
				'</li></ul></div><div class="clearfloat"></div>';
*/
		}
		
		$body = elgg_view('page_elements/contentwrapper', array('body' => $body));
		
		$body .= elgg_view('input/hidden', array(
			'internalname' => 'poll_guid',
			'value' => $poll->getGUID(),
		));

		echo $body;
		/*
		echo elgg_view('input/form', array(
			'action' => $CONFIG->url . 'action/polls_reporting/edit_reports',
			'method' => 'post',
			'body' => $body,
		));
		*/
	}

?>
