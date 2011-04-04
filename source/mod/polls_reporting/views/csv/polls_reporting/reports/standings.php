<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';
	
		$poll = $vars['poll'];
		
		$poll_score_meta_names = array('votes_score', 'votes_total_category1', 'votes_total_category2', 'votes_total_category3', 'votes_total_category4');
		$poll_count_meta_names = array('votes_count', 'votes_count_category1', 'votes_count_category2', 'votes_count_category3', 'votes_count_category4');
		$poll_count_vote_types = array('poll_stars5', 'poll_stars5_category1', 'poll_stars5_category2', 'poll_stars5_category3', 'poll_stars5_category4');
				
		// search parameters
		$metadata_search_array = array("parent_guid" => $poll->getGUID());
		$sort_by1 = $poll_score_meta_name;
		$sort_by2 = $poll_count_meta_name;
		$reverse = true;
		$limit = 100000;

		$categories = array();
		
		for($i = 1; $i < 5; ++$i)
		{
			$metakey = 'category' . ($i - 1);
			$category_title = $poll->$metakey;
			
			if($category_title != '')
				$categories[$i] = $category_title;
		} 
		
		$candidates = polls_get_entities_from_metadata_multi_order_by_metadata(
				$metadata_search_array, "object", "poll_candidate", 0,
				$limit, 0, $sort_by1, $sort_by2, $reverse);

		$header = array(
			elgg_echo('polls_reporting:report:standings:candidate_title'),
			elgg_echo('polls_reporting:votes'),
			elgg_echo('polls_reporting:score'),
		);
		
		foreach($categories as $category_id => $title)
		{
			$header[] = $title . ':' . elgg_echo('polls_reporting:votes');
			$header[] = $title . ':' . elgg_echo('polls_reporting:score');
		}
		
		echo PollsReporting_StringUtils::arrayToCsvLine($header);
						
		foreach($candidates as $candidate)
		{
			$data = array();
			
			$data[] = $candidate->title;
			$data[] = (int) $candidate->$poll_count_meta_names[0];
			$data[] = (int) $candidate->$poll_score_meta_names[0];
			
			foreach($categories as $category_id => $title)
			{
				$votes = (int) $candidate->$poll_count_meta_names[$category_id];
				if($votes > 0)
				{
					$data[] = $votes;
					$data[] = (int) $candidate->$poll_score_meta_names[$category_id] / $votes;
				}
				else
				{
					$data[] = 0;
					$data[] = 0;
				}
			}
			
			echo PollsReporting_StringUtils::arrayToCsvLine($data);
		}
	}
?>
