<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	require_once(dirname(__FILE__) . "/lib.php");
	
	gatekeeper();
	
	global $CONFIG;

	$poll_guid = get_input('poll_guid');
	
	$candidates = polls_reporting_get_entities_from_metadata_multi_and_title_search_order_by_title(
		array('parent_guid' => $poll_guid),
		get_input('q'),
		"object", "poll_candidate", 0,
		100000
	);
	
	if($candidates)
	{
		foreach($candidates as $candidate) {
			echo $candidate->title . '|' . $candidate->guid . "\r\n";
		}
	}
?>