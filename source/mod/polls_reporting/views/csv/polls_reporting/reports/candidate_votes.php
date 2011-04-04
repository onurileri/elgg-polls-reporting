<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';
	
		$poll = $vars['poll'];
		
		$candidate_guid = (int) get_input('candidate_guid');

		$category_names = array('"poll_stars5"', '"poll_thumbs"');
		
		for($i = 1; $i < 5; ++$i)
		{
			$category_names[] = '"poll_stars5_category' . $i . '"';
		}
		
		// get voting data
		$query = "select u.guid, u.name, u.username, count(*) as votes, sum(v1.string) as score, " .
			"n1.string AS vote_type ".
			"from {$CONFIG->dbprefix}annotations as a " .
			"join {$CONFIG->dbprefix}metastrings as n1 on a.name_id = n1.id " .
			"join {$CONFIG->dbprefix}metastrings as v1 on a.value_id = v1.id " .
			"join {$CONFIG->dbprefix}users_entity as u on a.owner_guid = u.guid " .
			"where a.entity_guid = {$candidate_guid} ".
			"and n1.string IN (" . implode(',', $category_names) . ") " .
			"group by u.guid, u.name, n1.string " .
			"order by u.name, u.guid, n1.string;";
		
		$rows = get_data($query);

		$categories = array();
		
		for($i = 1; $i < 5; ++$i)
		{
			$metakey = 'category' . $i;
			$category_title = $poll->$metakey;
			
			if($category_title != '')
				$categories[$i] = $category_title;
		} 
		
		$header = array(
			elgg_echo('user'),
			elgg_echo('username'),
			elgg_echo('polls_reporting:votes'),
			elgg_echo('polls_reporting:score'),
		);
		
		foreach($categories as $category_id => $title)
		{
			$header[] = $title . ':' . elgg_echo('polls_reporting:votes');
			$header[] = $title . ':' . elgg_echo('polls_reporting:score');
		}
		
		echo PollsReporting_StringUtils::arrayToCsvLine($header);
		
		$count = $rows ? count($rows) : 0;	// Why does get_data not just return an empty array if there's no data?
		$main_row_counter = 0;
		
		$has_category_votes = false;
		
		for($current = 0; $current < $count;)	// The increment is deliberately left out
		{
			++$main_row_counter;
			
			$row = $rows[$current];
			
			$name = htmlentities($row->name, ENT_COMPAT, 'utf8');
			$username = rawurlencode($row->username);
			$guid = $row->guid;

			$category_data = array();

			while(isset($rows[$current]) && ($rows[$current]->guid == $guid))
			{
				$matches = null;			
				
				if(preg_match('/^poll_stars5_category(\d+)/', $rows[$current]->vote_type, $matches))
					$category_data[(int) $matches[1]] = $rows[$current];
				else
					$category_data[0] = $rows[$current];
				
				++$current;
			}
			
			ksort($category_data);
			
			$data = array();
			
			$data[] = $name;
			$data[] = $username;
			
			if(isset($category_data[0]) && ($category_data[0]->votes > 0))
			{
				$data[] = (int) $category_data[0]->votes;
				
				if($poll->voting_type == 'poll_stars5')
					$data[] = $category_data[0]->score / $category_data[0]->votes;
				else
					$data[] = (double) $category_data[0]->score;
			}
			else
			{
				$data[] = 0;
				$data[] = 0;
			}
		
			
			foreach($categories as $category_id => $category)
			{
				if(isset($category_data[$category_id]) && ($category_data[$category_id]->votes > 0))
				{
					$data[] = (int) $category_data[$category_id]->votes;
					
					if(($category_id > 0) || ($poll->voting_type == 'poll_stars5'))
						$data[] = $category_data[$category_id]->score / $category_data[$category_id]->votes;
					else
						$data[] = (double) $category_data[$category_id]->score;
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
