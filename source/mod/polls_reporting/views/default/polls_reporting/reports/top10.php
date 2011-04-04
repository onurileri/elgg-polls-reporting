<?php

	require_once($CONFIG->pluginspath . "polls/lib.php");


	function get_vote_breakdown_chart($candidate_guid)
	{
		global $CONFIG;

		// get voting data

		$query = "select v.string as stars, count(*) as votes " .
			 "from {$CONFIG->dbprefix}annotations as ann " .
			 "join {$CONFIG->dbprefix}metastrings as n on ann.name_id = n.id " .
			 "join {$CONFIG->dbprefix}metastrings as v on ann.value_id = v.id " .
			 "where n.string = \"poll_stars5\" " .
			 "and ann.entity_guid = {$candidate_guid} " .
			 "group by v.string";

		$rows = get_data($query);

		$scores = array(0, 0, 0, 0, 0);

		if ($rows)
		{
			foreach ($rows as $row)
			{
				$scores[$row->stars - 1] = $row->votes;
			}
		}

		$chart = '<img src="' . $CONFIG->wwwroot . 'pg/polls_reporting/candidate_votes_bar_chart/';
		$chart .= $scores[0] . '/';
		$chart .= $scores[1] . '/';
		$chart .= $scores[2] . '/';
		$chart .= $scores[3] . '/';
		$chart .= $scores[4];
		$chart .= '" ';
		$chart .= 'alt="' . sprintf(elgg_echo("polls_reporting:barchart:alt"), $scores[0], $scores[1], $scores[2], $scores[3], $scores[4]) . '" ';
		$chart .= 'title="' . elgg_echo("polls_reporting:barchart:title") . '"';
		$chart .= '>';

		return $chart;
	}



	$poll = $vars['poll'];

	if ($poll && $poll->getSubtype() == "poll")
	{
		global $CONFIG;

		$body = '';

		// search parameters
		$metadata_search_array = array("parent_guid" => $poll->getGUID());
		$sort_by1 = "votes_score";
		$sort_by2 = "votes_count";
		$reverse = true;
		$limit = 10;

		$candidates = polls_get_entities_from_metadata_multi_order_by_metadata(
				$metadata_search_array, "object", "poll_candidate", 0,
				$limit, 0, $sort_by1, $sort_by2, $reverse);

		foreach ($candidates as $candidate)
		{
			$body .= '<p class="candidate_title">';
			$body .= "<b><a href=\"" . $candidate->getUrl() . "\">" . $candidate->title . "</a></b>";
			$body .= '</p>';

			$body .= '<p>';
			$body .= sprintf(elgg_echo('polls_reporting:report:top10:score'), $candidate->votes_score);

			$body .= '<p>';
			$body .= elgg_echo('polls_reporting:report:top10:chart:title');
			$body .= '<p>';
/*
echo '<pre>'; // This is for correct handling of newlines
ob_start();
var_dump($candidate->votes_total);
$a=ob_get_contents();
ob_end_clean();
echo htmlspecialchars($a,ENT_QUOTES); // Escape every HTML special chars (especially > and < )
echo '</pre>';

echo '<pre>'; // This is for correct handling of newlines
ob_start();
var_dump($candidate->votes_count);
$a=ob_get_contents();
ob_end_clean();
echo htmlspecialchars($a,ENT_QUOTES); // Escape every HTML special chars (especially > and < )
echo '</pre>';

$tmp = get_annotations_sum($candidate->getGUID(), "", "", "poll_stars5");


echo '<pre>'; // This is for correct handling of newlines
ob_start();
var_dump($tmp);
$a=ob_get_contents();
ob_end_clean();
echo htmlspecialchars($a,ENT_QUOTES); // Escape every HTML special chars (especially > and < )
echo '</pre>';
*/



			$body .= get_vote_breakdown_chart($candidate->getGUID());

		}

		echo elgg_view('page_elements/contentwrapper', array('body' => $body));
	}

?>
