<?php

	require_once(dirname(__FILE__) . "/lib.php");

	/**
	 * Initialise the plugin.
	 *
	 */
	function polls_reporting_init()
	{
		global $CONFIG;


		// Register a page handler, so we can have nice URLs
		register_page_handler('polls_reporting', 'polls_reporting_page_handler');

		// add a submenu to polls
		register_plugin_hook('polls:submenu', 'poll', 'polls_reporting_submenu_hook');

		// add items to polls page handler (necessary to get polls context sensitive menus)
		register_plugin_hook('polls:pagehandler', 'poll', 'polls_reporting_pagehandler_hook');

		// Register some actions
		register_action("polls_reporting/edit_reports", false, $CONFIG->pluginspath . "polls_reporting/actions/edit_reports.php");
		register_action("polls_reporting/update_report_access", false, $CONFIG->pluginspath . "polls_reporting/actions/update_report_access.php");
		register_action("polls_reporting/view_report", false, $CONFIG->pluginspath . "polls_reporting/actions/view_report.php");
		
		// Extend some views
		elgg_extend_view('css','polls_reporting/css');
		elgg_extend_view('js/initialise_elgg','js/jquery.autocomplete');
		elgg_extend_view('js/initialise_elgg','polls_reporting/js');
	}

	/**
	 * Page handler.
	 *
	 * @param array $page
	 */
	function polls_reporting_page_handler($page)
	{
		global $CONFIG;

		if (isset($page[0]))
		{
			// See what context we're using
			switch($page[0])
			{
				case "candidate_votes_bar_chart" :
					if (isset($page[1]) && isset($page[2]) && isset($page[3]) && isset($page[4]) && isset($page[5]))
					{
						echo elgg_view('polls_reporting/reports/top10/candidate_votes_bar_chart',
									array(
										'bar1' => $page[1],
										'bar2' => $page[2],
										'bar3' => $page[3],
										'bar4' => $page[4],
										'bar5' => $page[5]));
					}
					break;
					
				case "candidate_trend_chart" :
					if (isset($page[1]))
					{
						echo elgg_view('polls_reporting/reports/candidate_trend/trend_graph',
							array('candidate_guid' => $page[1])
						);
					}
					break;
			}
		}
		
	}


	function polls_reporting_submenu_hook($hook, $entity_type, $returnvalue, $params)
	{
		global $CONFIG;

		if ($entity_type = "poll" && isset($params['command']) && isset($params['item']))
		{
			$command = $params['command'];
			$item = $params['item'];

			$reports_mapper = PollsReporting_ReportMapper::getInstance();
			
			$reports = $reports_mapper->findAllByPollGuidForUserGuid($item->getGUID(), get_loggedin_userid());
			
			if(!empty($reports))
			{
				add_submenu_item(elgg_echo('polls_reporting:reporting'),
						"{$CONFIG->wwwroot}pg/polls/reports/{$item->getGUID()}", 'pollsactions');
			}
		}
	}


	function polls_reporting_pagehandler_hook($hook, $entity_type, $returnvalue, $params)
	{
		global $CONFIG;

		$page = $params['page_array'];

		if (isset($page[0]))
		{
			$command = $page[0];

			switch($command)
			{
				case "reports" :
					if (isset($page[1]))
						set_input('item_guid', $page[1]);

					if (isset($page[2]))
						set_input('report_id', $page[2]);
						
					set_input('other_params', array_slice($page, 3));

					include($CONFIG->pluginspath . "polls_reporting/reports.php");
					break;
			}
		}
	}


	// Make sure the initialisation function is called on initialisation
	register_elgg_event_handler('init', 'system', 'polls_reporting_init');
?>
