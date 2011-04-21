<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	global $CONFIG;
	
	$poll = $vars['poll'];
	$reports = $vars['reports'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body .= '<ul class="elgg-list">';

		foreach ($reports as $report)
		{
			$body .= elgg_view('polls_reporting/reportlisting', array('report' => $report));
		}

		$body .= '</ul>';
		
		$body = elgg_view('page/elements/body', array('body' => $body));
		
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
