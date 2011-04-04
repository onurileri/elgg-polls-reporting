<?php   
 /*
     Example1 : A simple line chart
 */
	$PCHART_DIR = dirname(__FILE__) . "/../../../../../pchart/";

	// Standard inclusions      
	include($PCHART_DIR . "pData.class");   
	include($PCHART_DIR . "pChart.class");   

	$scores = array($vars['bar1'], $vars['bar2'], $vars['bar3'], $vars['bar4'], $vars['bar5']);

	$max_score = max($scores);
	$limit = pow(10, ceil(log10($max_score)));

	if ($max_score <= $limit / 2)
		$limit = $limit / 2;

	// never go below 5, so our 5 'tick marks' are always integers
	$limit = max($limit, 5);

	// some sizes
	$canvas_x = 200;
	$canvas_y = 150;
	$border_outer = 5;
	$border_inner = 7;
	$border_outer_radius = 5;
	$border_inner_radius = 5;
	$outer_colour = array(100, 100, 100);
	$inner_colour = array(240, 240, 240);
	$margin_l = 50;
	$margin_r = 20;
	$margin_t = 20;
	$margin_b = 45;
	$graph_colour = array(255, 255, 255);
	$grid_colour = array(230, 230, 230);
	$bar_colour = array(0, 160, 160);

	// Dataset definition
	$DataSet = new pData;
	$DataSet->AddPoint(array(1, 2, 3, 4, 5), "stars");
	$DataSet->AddPoint($scores, "votes");
	$DataSet->AddSerie("votes");
	$DataSet->SetAbsciseLabelSerie("stars");
	$DataSet->SetXAxisName(elgg_echo("polls_reporting:barchart:xaxis"));
	$DataSet->SetYAxisName(elgg_echo("polls_reporting:barchart:yaxis"));

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
	$chart->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 0, TRUE);

	$chart->drawGrid(4, TRUE, $grid_colour[0], $grid_colour[1], $grid_colour[2], 50);

	$chart->setColorPalette(0, $bar_colour[0], $bar_colour[1], $bar_colour[2]);

	// Draw the graph
	$chart->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), TRUE);

	// output it
	$chart->Stroke();
?>
