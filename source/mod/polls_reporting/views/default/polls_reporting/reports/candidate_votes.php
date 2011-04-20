<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';
	
		$max_bar_width = 300;
		
		$poll = $vars['poll'];
		
		$candidate_guid = (int) get_input('candidate_guid');

		$categories = array('"poll_stars5"', '"poll_thumbs"');
		
		for($i = 1; $i < 5; ++$i)
		{
			$categories[] = '"poll_stars5_category' . $i . '"';
		}
		
		// get voting data
		$query = "select u.guid, u.name, u.username, count(*) as votes, sum(v1.string) as score, " .
			"n1.string AS vote_type ".
			"from {$CONFIG->dbprefix}annotations as a " .
			"join {$CONFIG->dbprefix}metastrings as n1 on a.name_id = n1.id " .
			"join {$CONFIG->dbprefix}metastrings as v1 on a.value_id = v1.id " .
			"join {$CONFIG->dbprefix}users_entity as u on a.owner_guid = u.guid " .
			"where a.entity_guid = {$candidate_guid} ".
			"and n1.string IN (" . implode(',', $categories) . ") " .
			"group by u.guid, u.name, n1.string " .
			"order by u.name, u.guid, n1.string;";
		
		$rows = get_data($query);

		if($rows)
		{
			$user_text = elgg_echo('user');
			$vote_type_text = elgg_echo('polls_reporting:vote_type');
			$score_text = elgg_echo('polls_reporting:score') . ' (' . elgg_echo('polls_reporting:votes') . ')';
			
			$body .= <<<HTML
	<div class="horizontal-bar-chart">
	<table>
		<thead>
			<tr class="column-header">
				<th class="y-axis">{$user_text}</th>
				<th class="plot-area">{$score_text}</th>
			</tr>
		</thead>
		<tbody>
HTML;

			if($poll->voting_type == 'stars5')
				$max_score = 5;
			else
			{
				foreach($rows as $row)
				{
					if($row->score > $max_score)
						$max_score = $row->score;
				}
			}
			
			$count = count($rows);
			$main_row_counter = 0;
			
			$has_category_votes = false;
			
			for($current = 0; $current < $count;)	// The increment is deliberately left out
			{
				++$main_row_counter;
				
				$row = $rows[$current];
				
				$name = htmlentities($row->name, ENT_COMPAT, 'utf8');
				$username = rawurlencode($row->username);
				$guid = $row->guid;

				$categories = array();
				
				while(isset($rows[$current]) && ($rows[$current]->guid == $guid))
				{
					$categories[] = $rows[$current];
					++$current;
				}
				
				$category_count = count($categories);
				
				if($main_row_counter % 2)
					$row_class = 'odd';
				else
					$row_class = 'even';
				
				if($main_row_counter == 1)
					$row_class .= ' first';
	
				if(!isset($rows[$current]))
					$row_class .= ' last';
				
				$inner_table = '<table class="series-table">';
				
				$no_main_vote = true;
				
				foreach($categories as $category)
				{
					$this_bar_width = round($category->score * $max_bar_width / $max_score);
					$this_score = (int) $category->score;
					$this_votes = (int) $category->votes;

					$extra_style = '';
					
					$matches = null;
					if(preg_match('/^poll_stars5_category(\d+)/', $category->vote_type, $matches))
					{
						$series_class = 'series' . ($matches[1] + 1);
						$extra_style = 'display: none';
						$has_category_votes = true;
						$raw_score = $this_score / $this_votes;
					}
					else
					{
						$series_class = 'series1';
				
						if($poll->voting_type == 'poll_stars5')
							$raw_score = $this_score / $this_votes;
						else
							$raw_score = $this_score;
							
						if($this_votes > 0)
							$no_main_vote = false;
					}
						
					if($this_votes == 0)
						continue;
					
					$this_score = round($raw_score, 1); 
					$this_bar_width = round($raw_score * $max_bar_width / $max_score);
						
					$inner_table .= '<tr style="' . $extra_style . '" class="' . $series_class . '">';
						
					$inner_table .= '<td><div class="bar" style="float:left; width: ' . $this_bar_width . 'px;"></div> <span class="score">' . $this_score . ' (' . $this_votes . ')</span></td>';
					
					$inner_table .= '</tr>';
				}
				
				if($no_main_vote)
					$row_class .= ' no-main-vote';
				
				$inner_table .= '</table>';
				
				$user_url = $CONFIG->url . 'pg/profile/' . rawurlencode($username);
				
				$body .= <<<HTML
		<tr class="{$row_class}">
			<td class="y-value"><a href="{$user_url}">{$name}</a></td>
			<td class="bar-container">{$inner_table}</td>
		</tr>
HTML;
			}	
	
			$body .= '</tbody></table></div>';
			
			if($has_category_votes)
			{
				$body = elgg_view('input/button',array(
						'internalname' => 'show_categories_button_top',
						'value' => elgg_echo('polls_reporting:show_category_votes'),
						'class' => 'show-categories-button submit_button',
						'type' => 'button',
					)) .
					elgg_view('polls_reporting/chart_legend', array('poll' => $poll)) .
					$body;
			}
		}
		else
		{
			$body .= '<p>' . elgg_echo('polls_reporting:report:no_data') . '</p>';
		}	

		echo elgg_view('page/elements/body', array('body' => $body));
	}

?>
