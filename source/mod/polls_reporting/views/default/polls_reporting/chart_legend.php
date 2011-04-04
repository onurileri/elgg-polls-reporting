<?php
	$poll = $vars['poll'];

	echo '<div class="bar-chart-legend"><ul>';
	
	for($i = 1; $i < 6; ++$i)
	{		
		if($i == 1)
			$legend_text = elgg_echo('polls_reporting:main_vote');
		else
		{
			$metakey = 'category' . ($i - 1);
			$legend_text = $poll->$metakey;
		}
		
		if($legend_text != '')
		{
			echo '<li class="series' . $i . '"><div class="bar"></div>';
			echo htmlentities($legend_text, ENT_COMPAT, 'utf8');
			echo '</li>';
		}
	}
	
	echo '</ul></div>';

?>