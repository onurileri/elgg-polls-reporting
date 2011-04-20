<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");

	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';
	
	$max_bar_width = 300;
	
	
	$poll = $vars['poll'];
	
	$category = (int) get_input('category');

	$candidate_text = elgg_echo('polls_reporting:report:standings:candidate_title');
	$score_text = elgg_echo('polls_reporting:score') . ' (' . elgg_echo('polls_reporting:votes') . ')';
			
	$body .= <<<HTML
	<div class="horizontal-bar-chart">
	<table>
		<thead>
			<tr class="column-header">
				<th class="y-axis">{$candidate_text}</th>
				<th class="plot-area">{$score_text}</th>
			</tr>
		</thead>
		<tbody>
HTML;

		$poll_score_meta_names = array('votes_total', 'votes_total_category1', 'votes_total_category2', 'votes_total_category3', 'votes_total_category4');
		$poll_count_meta_names = array('votes_count', 'votes_count_category1', 'votes_count_category2', 'votes_count_category3', 'votes_count_category4');
		$poll_count_vote_types = array('poll_stars5', 'poll_stars5_category1', 'poll_stars5_category2', 'poll_stars5_category3', 'poll_stars5_category4');
		
		// search parameters
		$metadata_search_array = array("parent_guid" => $poll->getGUID());
		$sort_by1 = 'votes_score';
		$sort_by2 = 'votes_count';
		$reverse = true;
		$limit = 100000;

		$candidates = polls_get_entities_from_metadata_multi_order_by_metadata(
				$metadata_search_array, "object", "poll_candidate", 0,
				$limit, 0, $sort_by1, $sort_by2, $reverse);

		if($poll->voting_type == 'stars5')
			$max_score = 5;
		else
		{
			foreach($candidates as $candidate)
			{
				foreach($poll_score_meta_names as $poll_score_meta_name)
				{
					if($candidate->$poll_score_meta_name > $max_score)
						$max_score = $candidate->$poll_score_meta_name;
				}
			}
		}
		
		$count = count($candidates);
		$current = 0;
		
		foreach($candidates as $candidate)
		{
			++$current;
			
			$this_bar_width = round($candidate->$poll_score_meta_name * $max_bar_width / $max_score);
			$this_score = (int) $candidate->$poll_score_meta_name;
			$name = htmlentities($candidate->title, ENT_COMPAT, 'utf8');
			
			if($current % 2)
				$row_class = 'odd';
			else
				$row_class = 'even';
			
			if($current == 1)
				$row_class .= ' first';

			if($current == $count)
				$row_class .= ' last';
				
			$inner_table = '<table class="series-table">';
			
			foreach($poll_score_meta_names as $index => $poll_score_meta_name)
			{
				$this_votes = (int) $candidate->$poll_count_meta_names[$index];
				
				$raw_vote_type = $poll_count_vote_types[$index];
				
				if($this_votes == 0)
					continue;
				
				$extra_style = '';
				
				$matches = null;
				if(preg_match('/^poll_stars5_category(\d+)/', $raw_vote_type, $matches))
				{
					$series_class = 'series' . ($matches[1] + 1);
					$extra_style = 'display: none';
					$has_category_votes = true;
					$raw_score = $candidate->$poll_score_meta_name / $this_votes;
				}
				else
				{
					$series_class = 'series1';
				
					if($poll->voting_type == 'stars5')
						$raw_score = $candidate->$poll_score_meta_name / $this_votes;
					else
						$raw_score = $candidate->$poll_score_meta_name;
				}
				
				$this_score = round($raw_score, 1); 
				$this_bar_width = round($raw_score * $max_bar_width / $max_score);
			
				$inner_table .= '<tr style="' . $extra_style . '" class="' . $series_class . '">';
				
				$inner_table .= '<td><div class="bar" style="float:left; width: ' . $this_bar_width . 'px;"></div> <span class="score">' . $this_score . ' (' . $this_votes . ')</span></td>';
				
				$inner_table .= '</tr>';
			}
			
			$inner_table .= '</table>';
			
			$candidate_url = $candidate->getUrl();
			
			$body .= <<<HTML
		<tr class="{$row_class}">
			<td class="y-value"><a href="{$candidate_url}">{$name}</a></td>
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
/*		
			$body .= '<img src="' . $CONFIG->wwwroot . 'pg/polls_reporting/top_candidates_bar_chart/';
			$body .= $poll->guid;
			$body .= '?category=' . get_input('category');
			$body .= '" ';
//			$body .= 'alt="' . sprintf(elgg_echo("polls_reporting:barchart:alt"), $scores[0], $scores[1], $scores[2], $scores[3], $scores[4]) . '" ';
			$body .= 'title="' . elgg_echo("polls_reporting:barchart:title") . '"';
			$body .= ' />';

			$body .= '<img src="' . $CONFIG->wwwroot . 'pg/polls_reporting/top_candidates_vertical_bar_chart/';
			$body .= $poll->guid;
			$body .= '?category=' . get_input('category');
			$body .= '" ';
//			$body .= 'alt="' . sprintf(elgg_echo("polls_reporting:barchart:alt"), $scores[0], $scores[1], $scores[2], $scores[3], $scores[4]) . '" ';
			$body .= 'title="' . elgg_echo("polls_reporting:barchart:title") . '"';
			$body .= ' />';
*/			
			
		echo elgg_view('page/elements/body', array('body' => $body));
	}

?>
