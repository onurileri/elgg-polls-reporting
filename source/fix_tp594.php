<?php
	set_time_limit(0);

	require_once(dirname(__FILE__) . "/engine/start.php");

	print('<h1>Fixing TP#594</h1>');
	
	// Get all polls
	$polls = get_entities('object', 'poll', 0, "", 9999999);
	
	foreach($polls as $poll)
	{
		if($poll->voting_type != 'stars5')	// This bug only affects stars polls 
			continue;
		
		print('<h2>Poll: ' . $poll->title . '</h2>');
		flush();
		
		$candidates = get_entities_from_metadata('parent_guid', $poll->guid, 'object', 'poll_candidate', 0, 9999999);

		foreach($candidates as $candidate)
		{
			if($candidate->votes_count > 0)
			{
				print('Candidate: ' . $candidate->title . '<br />');
				flush();
	
				$candidate->votes_score = $candidate->votes_total / $candidate->votes_count;
				
				// I don't think it needs this as the metadata gets saved directly, but it won't hurt!
				$candidate->save();
			}
		}
	}
	
	print('<h1>Done</h1>');
?>