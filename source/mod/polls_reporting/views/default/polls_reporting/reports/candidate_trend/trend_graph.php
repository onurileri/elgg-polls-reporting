<?php
	// some sizes
	$canvas_x = 646;
	$canvas_y = 500;
	$border_outer = 5;
	$border_inner = 7;
	$border_outer_radius = 5;
	$border_inner_radius = 5;
	$outer_colour = array(100, 100, 100);
	$inner_colour = array(240, 240, 240);
	$margin_l = 50;
	$margin_r = 20;
	$margin_t = 20;
	$margin_b = 80;
	$graph_colour = array(255, 255, 255);
	$grid_colour = array(230, 230, 230);
	$bar_colour = array(0, 160, 160);
	
	global $CONFIG;
	
	require_once($CONFIG->pluginspath . "polls/lib.php");
	
	$PCHART_DIR = dirname(__FILE__) . "/../../../../../pchart/";
	
	// Standard inclusions
	include($PCHART_DIR . "pData.class");
	include($PCHART_DIR . "pChart.class");
	
	$candidate_guid = (int) $vars['candidate_guid'];
	$candidate = get_entity($candidate_guid);
	
	$poll = get_entity($candidate->parent_guid);
	
	$timescale = get_input('time');
	
	$x_format = 'd/m/Y';
	$x_label = elgg_echo('polls_reporting:report:candidate_trend:xaxis:date');

	// We default the end time to 23:59 yesterday (this is so that the date shows correctly on the scale)
	$end_time_parts = getdate();
	$end_time = mktime(0, 0, -1, $end_time_parts['mon'], $end_time_parts['mday'], $end_time_parts['year']);
	
	switch($timescale)
	{
		case '1year':
			$start_time = strtotime('1 year ago', $end_time);
			$resolution = '2 weeks';
			break;
			
		case '6months':
			$start_time = strtotime('6 months ago', $end_time);
			$resolution = '1 week';
			break;
			
		case '1month':
			$start_time = strtotime('1 month ago', $end_time);
			$resolution = '1 day';
			break;
			
		case '1week':
			$end_time = mktime(floor($end_time_parts['hours'] / 6) * 6, 0, 0, $end_time_parts['mon'], $end_time_parts['mday'], $end_time_parts['year']);
			$start_time = strtotime('1 week ago', $end_time);
			$resolution = '6 hours';
			$x_format = "jS @ H:i";
			$margin_b += 10;	// Increase the bottom margin to fit the larger labels
			break;
		
		case '1day':
			$end_time = mktime($end_time_parts['hours'], 0, 0, $end_time_parts['mon'], $end_time_parts['mday'], $end_time_parts['year']);
			$start_time = strtotime('1 day ago', $end_time);
			$resolution = '1 hour';
			$x_format = 'H:i';
			$x_label = elgg_echo('polls_reporting:report:candidate_trend:xaxis:time');
			break;
			
		default:
			$start_time_parts = getdate($poll->getTimeCreated());
			$start_time = mktime(0, 0, -1, $start_time_parts['mon'], $start_time_parts['mday'], $start_time_parts['year']);
			
			// This will keep increasing the scale until we get 35 or less segments
			// in the graph - more than this and the scale labels overlap each other
			
			$period = time() - $start_time;
			
			$end_time_to_1h = true;
			$approx_resolution = 3600;
			$resolution = '1 hour';
			$x_format = 'H:i';
			
			if(($period / $approx_resolution) > 35)
			{
				$approx_resolution *= 24;
				$resolution = '1 day';
				$x_format = 'd/m/Y';
				$end_time_to_1h = false;
				
				if(($period / $approx_resolution) > 35)
				{
					$approx_resolution *= 7;
					$resolution = '1 week';
					
					if(($period / $approx_resolution) > 30)
					{
						$approx_resolution *= ( 30 / 7 );
						$resolution = '1 month';
					}
				}
			}
			
			if($end_time_to_1h)
			{
				$end_time = mktime($end_time_parts['hours'], 0, 0, $end_time_parts['mon'], $end_time_parts['mday'], $end_time_parts['year']);
				$start_time = mktime($start_time_parts['hours'], 0, 0, $start_time_parts['mon'], $start_time_parts['mday'], $start_time_parts['year']);
			}
				
			break;
	}
	
	$body = '';
	
	$query = "SELECT * FROM {$CONFIG->dbprefix}river r".
		" WHERE r.object_guid = {$candidate_guid}".
		" AND r.posted >= {$start_time}".
		" ORDER BY r.posted DESC;";
	
	$raw_data = array();
	$time_data = array();
	
	// We will start these from the current values and go backwards - it's safer that way!
	$current_total = $candidate->votes_total;	// This keeps track of the current score
	$current_count = $candidate->votes_count;
	
	$max_score = 0;
	
	$last_user_votes = array();	// This keeps track of the last vote a user made
	
	$rows = get_data($query);
	
	$matches = null;
	
	$current_time_period = $end_time;
	
	foreach($rows as $row)
	{
		if($row->action_type == 'vote')
			$vote_score = 1;
		else if(preg_match('/^vote(\d)$/', $row->action_type, $matches))
			$vote_score = $matches[1];
		else if($row->action_type != 'create')	// We allow create to go through and pick it up later
			continue;
		
		if($row->posted <= $current_time_period)
		{
			while($current_time_period > $row->posted)
			{
				$time_data[$current_time_period] = array(
					"total" => $current_total,
					"count" => $current_count,
				);
				
				$current_time_period = strtotime($resolution . ' ago', $current_time_period);
			}
			
			if($row->action_type == 'create')
			{
				// null for these will mean it didn't exist
				$current_total = null;
				$current_count = null;
				break;
			}
			
			$raw_data[] = array(
				"timestamp" => $row->posted,
				"total" => $current_total,
				"count" => $current_count,
				"user_guid" => $row->subject_guid,
				"vote" => $vote_score,
			);
		}
		
		// If the user had already voted (they are changing their vote) then remove their old vote
		if(isset($last_user_votes[$row->subject_guid]))
			$current_total += $vote_score;
		else
			$current_count--;
			
		$current_total = max(0, $current_total - $vote_score);	// Just in case!
			
		$last_user_votes[$row->subject_guid] = $vote_score;
			
		if($current_total > $max_score)
			$max_score = $current_total;
	}

	while($current_time_period >= $start_time)
	{
		$time_data[$current_time_period] = array(
			"total" => $current_total,
			"count" => $current_count,
		);
		
		$current_time_period = strtotime($resolution . ' ago', $current_time_period);
	}
	
	ksort($time_data);
	
	if($poll->voting_type == 'stars5')
	{
		$max_score = 5;
		$stars_vote = true;
	}
	
	$limit = pow(10, ceil(log10($max_score)));
	
	if ($max_score <= $limit / 2)
	$limit = $limit / 2;
	
	// never go below 5, so our 5 'tick marks' are always integers
	$limit = max($limit, 5);
	
	$dataSet = new PData();
	
	foreach($time_data as $timestamp => $row)
	{
		$dataSet->AddPoint(date($x_format, $timestamp), "Serie1");
		
		if(is_null($row["count"]))
			$dataSet->AddPoint("", "Serie2");
		else if($stars_vote)
			$dataSet->AddPoint($row["total"] / $row["count"], "Serie2");
		else
			$dataSet->AddPoint($row["total"], "Serie2");
	}
	
	$dataSet->AddSerie("Serie2");
	$dataSet->SetAbsciseLabelSerie("Serie1");
//	$dataSet->SetXAxisFormat($x_format);
	$dataSet->SetXAxisName($x_label);
	$dataSet->SetYAxisName(elgg_echo("polls_reporting:report:candidate_trend:yaxis"));
	
	// Initialise the graph
	$chart = new pChart($canvas_x, $canvas_y);
	$chart->setFontProperties($PCHART_DIR . "Fonts/tahoma.ttf", 8);
	$chart->setGraphArea($margin_l, $margin_t, $canvas_x - $margin_r, $canvas_y - $margin_b);
	
	$chart->drawFilledRoundedRectangle(
		$border_inner, $border_inner,
		$canvas_x - $border_inner, $canvas_y - $border_inner,
		$border_inner_radius,
		$inner_colour[0], $inner_colour[1], $inner_colour[2]
	);
	
	$chart->drawRoundedRectangle(
		$border_outer, $border_outer,
		$canvas_x - $border_outer, $canvas_y - $border_outer,
		$border_outer_radius,
		$outer_colour[0], $outer_colour[1], $outer_colour[2]
	);
	
	$chart->drawGraphArea($graph_colour[0], $graph_colour[1], $graph_colour[2], TRUE);
	
	$chart->setFixedScale(0, $limit);
	$chart->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,45,2,TRUE);  
	
	$chart->drawGrid(4, TRUE, $grid_colour[0], $grid_colour[1], $grid_colour[2], 50);
	
	$chart->setColorPalette(0, $bar_colour[0], $bar_colour[1], $bar_colour[2]);
	
	// Draw the graph
	$chart->drawLineGraph($dataSet->GetData(),$dataSet->GetDataDescription());
//	$chart->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255); 
	
	// output it
	$chart->Stroke();
?>