<?php

	$english = array(
	
		/**
		 * Menu items and titles
		 */
		'polls_reporting:reporting' => "View the results",
		'polls_reporting:select_report' => "Please select a report:",
	
		'polls_reporting:report:title:top10' => "Top 10 highest scoring candidates",
		'polls_reporting:report:description:top10' => "Experimental report to demonstrate graphing capabilities",

		'polls_reporting:report:title:whohasvoted' => "Which users have voted in this poll",
		'polls_reporting:report:description:whohasvoted' => "Show which users have already voted in this poll",
	
		'polls_reporting:report:title:standings' => "Current standings",
		'polls_reporting:report:description:standings' => "Show how the candidates currently stack up against each other",
	
		'polls_reporting:report:title:candidate_votes' => "Candidate Votes",
		'polls_reporting:report:description:candidate_votes' => "View who has voted for a candidate, and how they have voted",
	
		'polls_reporting:report:title:candidate_trend' => "Candidate Trend",
		'polls_reporting:report:description:candidate_trend' => "Show the voting trend for a candidate over time",
	
		/**
		 * Reports
		 */
		'polls_reporting:report:top10:score' => "Average score: %.1f",
		'polls_reporting:report:top10:chart:title' => "Voting breakdown:",

		'polls_reporting:report:who:none' => "No users have yet voted in this poll.",
		'polls_reporting:report:who:intro' => "The following users have voted in this poll:",
		'polls_reporting:report:who:uservotes' => "%s has placed %s votes",
		'polls_reporting:report:who:uservotes:one' => "%s has placed 1 vote",
	
		'polls_reporting:report:standings:candidate_title' => 'Candidate',
	
		'polls_reporting:report:candidate_trend:xaxis:date' => 'Date',
		'polls_reporting:report:candidate_trend:xaxis:time' => 'Time',
		'polls_reporting:report:candidate_trend:yaxis' => 'Score',
		'polls_reporting:report:candidate_trend:candidate_title' => 'Trend for candidate %s',
		/**
		 * Status and error messages
		 */
	
		/**
		 * General
		 */
		'polls_reporting:barchart:title' => "Vote breakdown",
		'polls_reporting:barchart:alt' => "[1 star: %s, 2 stars: %s, 3 stars: %s, 4 stars: %s, 5 stars: %s]",
		'polls_reporting:barchart:xaxis' => "Stars",
		'polls_reporting:barchart:yaxis' => "Votes",
	
		/**
		 * River items
		 */
	
		'polls_reporting:download_csv' => 'Download CSV',
		'polls_reporting:view_report' => 'View Report',
	
		'polls_reporting:report_list_saved' => 'Your changes have been saved',
	
		'polls_reporting:edit_reports_access' => 'Edit who can access the reports',
		'save_changes' => 'Save changes',
	
		'polls_reporting:score' => 'Score',
		'polls_reporting:votes' => 'Votes',
	
		'polls_reporting:loading' => "Loading...",
		'polls_reporting:saving' => "Saving...",
		'polls_reporting:save:ok' => "Saved.",
		'polls_reporting:save:fail' => "An error occurred.",
	
		'polls_reporting:report:no_data' => "There is no data avilable to display this report",
	
		'polls_reporting:main_vote' => "Main Vote",
	
		'polls_reporting:show_category_votes' => 'Show category votes',
	
		'polls_reporting:timescale:max' => 'Max',
		'polls_reporting:timescale:1year' => '1 year',
		'polls_reporting:timescale:6months' => '6 months',
		'polls_reporting:timescale:1month' => '1 month',
		'polls_reporting:timescale:1week' => '1 week',
		'polls_reporting:timescale:1day' => '1 day',
	);


	add_translation("en",$english);
?>
