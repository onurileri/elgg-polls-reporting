<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';
	
		$poll = $vars['poll'];
		
		// get voting data
		$query = "select u.name, count(*) as votes " .
			"from {$CONFIG->dbprefix}annotations as a " .
			"join {$CONFIG->dbprefix}metastrings as n1 on a.name_id = n1.id " .
			"join {$CONFIG->dbprefix}users_entity as u on a.owner_guid = u.guid " .
			"join {$CONFIG->dbprefix}metadata as md on a.entity_guid = md.entity_guid " .
			"join {$CONFIG->dbprefix}metastrings as n2 on md.name_id = n2.id " .
			"join {$CONFIG->dbprefix}metastrings as v2 on md.value_id = v2.id " .
			"where (n1.string = 'poll_stars5' or n1.string = 'poll_thumbs' ".
			"or n1.string = 'poll_custom_options') " .
			"and n2.string = 'parent_guid' and v2.string = '" . $poll->getGUID() . "' " .
			"group by u.name";

		$header = array(
			elgg_echo('user'),
			elgg_echo('polls_reporting:votes'),
		);
		
		echo PollsReporting_StringUtils::arrayToCsvLine($header);
						
		$rows = get_data($query);
		
		if($rows)
		{
			foreach($rows as $row)
			{
				$data = array(
					$row->name,
					$row->votes,
				);
				
				echo PollsReporting_StringUtils::arrayToCsvLine($data);
			}
		}
	}
?>
