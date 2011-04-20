<?php
	global $CONFIG;

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$candidate_guid = (int) get_input('candidate_guid');
	$candidate = get_entity($candidate_guid);
	$poll_guid = $candidate->parent_guid;
	$poll = get_entity($poll_guid);
	
	$timescales = array('max');
	
	$poll_start = $poll->getTimeCreated();
	
	if($poll_start <= strtotime('6 months ago'))
		$timescales[] = '1year';
		
	if($poll_start <= strtotime('1 month ago'))
		$timescales[] = '6months';
	
	if($poll_start <= strtotime('1 week ago'))
		$timescales[] = '1month';
	
	$timescales[] = '1week';
		
	$time = get_input('time', 'max');
	
	$body = '<div class="polls-reporting-tabbedreport">';
	
	$body .= '<h3>' . htmlentities(sprintf(elgg_echo('polls_reporting:report:candidate_trend:candidate_title'), $candidate->title), ENT_COMPAT, 'utf-8') . '</h3>';
	
	$body .= '<ul id="elgg_horizontal_tabbed_nav">';
	
	foreach($timescales as $timescale)
	{
		if($time == $timescale)
			$class = ' class="selected"';
		else
			$class = '';
		
		$body .= '<li' . $class . '><a href="' . $CONFIG->url . '/pg/polls/reports/' . $poll_guid . '/candidate_trend?candidate_guid=' . $candidate->guid . '&time=' . $timescale . '">'
			. elgg_echo('polls_reporting:timescale:' . $timescale) . '</a></li>';
	}

	$body .= '</ul>';
	
	$body .= '<img src="' . $CONFIG->url . 'pg/polls_reporting/candidate_trend_chart/' . $candidate_guid .'?time=' . rawurlencode($time) . '" />';

	$body .= '</div>';
	
	echo elgg_view('page/elements/body', array('body' => $body));
?>
