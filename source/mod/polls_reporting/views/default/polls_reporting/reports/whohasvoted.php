<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';

		$body .= '<p>';

		// get voting data
		$query = "select u.name, count(*) as votes " .
			"from {$CONFIG->dbprefix}annotations as a " .
			"join {$CONFIG->dbprefix}metastrings as n1 on a.name_id = n1.id " .
			"join {$CONFIG->dbprefix}users_entity as u on a.owner_guid = u.guid " .
			"join {$CONFIG->dbprefix}metadata as md on a.entity_guid = md.entity_guid " .
			"join {$CONFIG->dbprefix}metastrings as n2 on md.name_id = n2.id " .
			"join {$CONFIG->dbprefix}metastrings as v2 on md.value_id = v2.id " .
			"where (n1.string = 'poll_stars5' or n1.string = 'poll_thumbs') " .
			"and n2.string = 'parent_guid' and v2.string = '" . $poll->getGUID() . "' " .
			"group by u.name";

		$rows = get_data($query);

		if ($rows)
		{
			$body .= elgg_echo('polls_reporting:report:who:intro');

			$body .= '<table>';
			
			$body .= '<thead><tr>';
				$body .= '<th>' . elgg_echo('user') . '</th>';
				$body .= '<th>' . elgg_echo('polls_reporting:votes') . '</th>';
			$body .= '</tr></thead>';
			
			$body .= '<tbody>';

			foreach ($rows as $row)
			{
				$body .= '<tr>';

				$body .= '<td>' . $row->name . '</td>';
				$body .= '<td>' . $row->votes . '</td>';

				$body .= '</tr>';
			}

			$body .= '</tbody></table>';
		}
		else
		{
			$body .= elgg_echo('polls_reporting:report:who:none');
		}

		$body = '<div class="report-who-has-voted">' . $body . '</div>';
		
		echo elgg_view('page/elements/body', array('body' => $body));
	}

?>
