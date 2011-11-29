<?php
	global $CONFIG;
?>

#polls_reporting_reportlist .contentWrapper {
	zoom: 1;
}

#polls_reporting_reportlist h3 {
	margin: 0 0 10px 0;
}

#polls_reporting_reportlist .search_listing
{
	border: 2px solid #cccccc;
	margin: 0 0 5px 0;
	height: 100%;
}

#polls_reporting_reportlist ul {
	list-style-type: none;
	margin: 0;
	padding: 0;
}

#polls_reporting_reportlist .report-title {
	font-weight: bold;
}

#polls_reporting_reportlist .report-controls {
	float: right;
}

#polls_reporting_reportlist .report-edit {
	clear: both;
}

#polls_reporting_reportlist label {
	font-size: 1em;
}

#polls_reporting_reportlist .edit-reports-button {
	display: none;
}

#polls_reporting_reportlist .report-edit-status {
	font-weight: bold;
}

#polls_reporting_reportlist .report-edit-loading {
	background: white url('<?php echo $CONFIG->url; ?>mod/polls_reporting/images/autocomplete_indicator.gif') left center no-repeat;
	padding-left: 20px;
}

#polls_reporting_reportlist .report-edit-success {
	color: #009900;
}

#polls_reporting_reportlist .report-edit-failure {
	color: #990000;
}

#polls_reporting_reportlist .view-report-form {
	float: right;
	clear: right;
	margin-left: 10px;
}

#polls_reporting_reportlist .view-report-controls ul {
	list-style-type: none;
	text-align: right;
	margin: 0;
	padding: 0;
}

#polls_reporting_reportlist .view-report-controls ul li {
	display: inline;
	margin-left: 5px;
}

#polls_reporting_reportlist input.submit_button:disabled, #polls_reporting_reportlist input.disabled {
	background: #666666;
	border-color: #666666;
	cursor: auto;
}

#polls_reporting_reportlist .show-categories-button {
	float: left;
}


/* CHARTS */
.horizontal-bar-chart, .bar-chart-legend {
	background: #F0F0F0;
	padding: 10px;
	border-radius: 5px;
	-moz-border-radius: 5px;
	border: 2px groove #E2E2E2;
	clear: both;
}

.bar-chart-legend {
/*
	right: 10px;
	top: 20px;
	opacity: 0.8;
	position: absolute;
*/
	float: right;
	clear: none;
	margin-bottom: 10px;
}

.bar-chart-legend ul {
	color: #969696;
}

.bar-chart-legend li {
	color: #969696;
}

.bar-chart-legend .bar {
	float: left;
	clear: left;
	width: 50px;
	height: 15px;
	margin-right: 0.5em;
	margin-bottom: 2px;
}

.horizontal-bar-chart table {
	border-collapse: collapse;
}

.horizontal-bar-chart .bar-container {
	background: #ffffff url('<?php echo $CONFIG->url; ?>mod/polls_reporting/images/chart-bg.gif');
	border-right: 1px solid #E2E2E2;
	border-bottom: 1px dashed #E2E2E2;
	color: #969696;
	vertical-align: middle;
	padding: 5px 0;
	width: 450px;
}

.horizontal-bar-chart .last .bar-container {
	border-bottom: 1px solid #E2E2E2;
}

.horizontal-bar-chart .bar {
	float: left;
	height: 1em;
	background: #00A0A0;
	margin: 0.25em 0.5em 0.25em 0;
}

.horizontal-bar-chart .series1 .bar, .bar-chart-legend .series1 .bar {
	background: #00A0A0;	
}

.horizontal-bar-chart .series2 .bar, .bar-chart-legend .series2 .bar {
	background: #4F81BD;	
}

.horizontal-bar-chart .series3 .bar, .bar-chart-legend .series3 .bar {
	background: #C0504D;	
}

.horizontal-bar-chart .series4 .bar, .bar-chart-legend .series4 .bar {
	background: #9BBB59;	
}

.horizontal-bar-chart .series5 .bar, .bar-chart-legend .series5 .bar {
	background: #8064A2;	
}

.horizontal-bar-chart .series6 .bar, .bar-chart-legend .series6 .bar {
	background: #802080;	
}

.horizontal-bar-chart .score {
	margin-left: 0.25em;
	margin-right: 0.25em;
}

.horizontal-bar-chart thead th {
	color: #969696;
	text-align: center;
}

.horizontal-bar-chart thead .plot-area {
	border-bottom: 1px solid #969696;
}

.horizontal-bar-chart .y-value {
	border-right: 1px solid #969696;
	color: #969696;
	font-size: 0.8em;
	text-align: right;
	padding: 2px 5px 2px 2px;
	vertical-align: middle;
	border-bottom: 1px dashed #E2E2E2;
}

.horizontal-bar-chart .last .y-value {
	border-bottom: none;
}

.horizontal-bar-chart .series-table .y-value {
	border-bottom: none;
}

/* JQUERY AUTOCOMPLETE */
.ac_results {
	border-radius: 4px;
	-moz-border-radius: 4px;
	background:#FFFFFF none repeat scroll 0 0;
	border:1px solid #4690D6;
	overflow: hidden;
	z-index: 99999;
	padding: 5px;
}

.ac_results ul {
	width: 100%;
	list-style-position: outside;
	list-style: none;
	padding: 0;
	margin: 0;
}

.ac_results li {
	margin: 0px;
	padding: 2px 5px;
	cursor: default;
	display: block;
	/* 
	if width will be 100% horizontal scrollbar will apear 
	when scroll mode will be used
	*/
	/*width: 100%;*/
	font: menu;
	font-size: 12px;
	/* 
	it is very important, if line-height not setted or setted 
	in relative units scroll will be broken in firefox
	*/
	line-height: 16px;
	overflow: hidden;
	text-align: left;
}

.ac_loading {
	background-image: url('<?php echo $CONFIG->url; ?>mod/polls_reporting/images/autocomplete_indicator.gif') !important;
	background-position: right center !important;
	background-repeat: no-repeat !important;
}

.ac_odd {
	background-color: #eee;
}

.ac_over {
	background-color: #0A246A;
	color: white;
}

/* REPORT: WHO HAS VOTED */
#polls_reporting_reportlist .report-who-has-voted table {
	background: #F0F0F0;
	padding: 5px;
	border-radius: 5px;
	-moz-border-radius: 5px;
	border: 1px solid #666666;
	clear: both;
	width: 100%;
}

#polls_reporting_reportlist .report-who-has-voted table p {
	padding: 0;
	margin: 0;
}

#polls_reporting_reportlist .report-who-has-voted table tr.major {
	border-top: 1px solid #666666;
}

#polls_reporting_reportlist .report-who-has-voted table td {
	border-bottom: 1px dashed #666666;
}


#polls_reporting_reportlist .report-who-has-voted table th {
	font-weight: bold;
	border-bottom: 1px solid #666666;
	vertical-align: middle;
}

#polls_reporting_reportlist .report-who-has-voted table td, #polls_reporting_reportlist .report-who-has-voted table th {
	padding: 2px;
}

