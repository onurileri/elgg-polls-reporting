<?php
	global $CONFIG;
?>
// Polls Reporting
$(document).ready( function() {
	// Bind the event to the select box
	$('#polls_reporting_reportlist .report-edit').hide();
	
	$('a.polls-reporting-edit-reports-button').show();
	
	$('a.polls-reporting-edit-reports-button').bind('click', function(event) {
		event.preventDefault();
		$('#polls_reporting_reportlist .report-edit').slideDown();
		$('a.polls-reporting-edit-reports-button').fadeOut();
	});
	
	$('#polls_reporting_reportlist .report-edit :input').bind('change', function(evt) {
		var el = $(evt.target);
		
		var matches = el.attr('id').match(/^input_report_access_id_(\d+)_(.+)$/);
		
		if(!matches)
			return true;
			
		var poll_guid = matches[1];
		var report_id = matches[2];
		
		PollsReporting.setReportAjaxStatusLoading(report_id);
		
		var params = {
			"report_id": report_id,
			"poll_guid": poll_guid,
			"access_id": el.val()
		};
		
		// Ajax request
		elgg.action("polls_reporting/update_report_access", {
			data: params,
			dataType: "json",
			complete: function() {
				$('#report_edit_loading_animation_' + report_id).removeClass(".report-edit-loading");
			},
			success: function(data) {
				if(data) {
					PollsReporting.setReportAjaxStatus(report_id, "<?php echo addslashes(elgg_echo('polls_reporting:save:ok')); ?>");
				} else {
					PollsReporting.setReportAjaxStatus(report_id, "<?php echo addslashes(elgg_echo('polls_reporting:save:fail')); ?>", "failure");
				}
			},
			error: function() {
				PollsReporting.setReportAjaxStatus(report_id, "<?php echo addslashes(elgg_echo('polls_reporting:save:fail')); ?>", "failure");
			},
			type: "post"
		});
	});
	
	// Cache the loading image - this is used in a few places
	var img = new Image();
	img.src = "<?php echo $CONFIG->url; ?>mod/polls_reporting/images/autocomplete_indicator.gif";
	
	// This is the show categories button
	$('.show-categories-button').bind('click', function() {
		$('.horizontal-bar-chart tr.series2').toggle();
		$('.horizontal-bar-chart tr.series3').toggle();
		$('.horizontal-bar-chart tr.series4').toggle();
		$('.horizontal-bar-chart tr.series5').toggle();
		$('.horizontal-bar-chart tr.no-main-vote').toggle();
		$('.bar-chart-legend').slideToggle('normal');
	});
	
	$('.bar-chart-legend').hide();
	
	$('.horizontal-bar-chart tr.no-main-vote').hide();
});

var PollsReporting = {
	setElementDisabled : function(element, disabled) {
		if(disabled) {
			$(element)
				.attr("disabled", "disabled")
				.addClass("disabled");
		} else {
			$(element)
				.removeAttr("disabled")
				.removeClass("disabled");			
		}
	},
	
	setViewReportButtonsDisabled : function(reportId, disabled) {
		PollsReporting.setElementDisabled('#polls-reporting-report-listing-' + reportId + ' :submit', disabled);
	},
	
	setReportAjaxStatusLoading: function(reportId) {
		$('#report_edit_loading_animation_' + reportId)
			.addClass("report-edit-loading")
			.removeClass("report-edit-success")
			.removeClass("report-edit-failure")
			.text("<?php echo addslashes(elgg_echo('polls_reporting:saving')); ?>")
			.show();
	},

	setReportAjaxStatus: function(reportId, statusText, status) {
		if(!status)
			status = "success";
			
		$('#report_edit_loading_animation_' + reportId)
			.removeClass("report-edit-loading")
			.removeClass("report-edit-success")
			.removeClass("report-edit-failure")
			.addClass("report-edit-" + status)
			.text(statusText)
			.show();
			
		window.setTimeout( function() {
			PollsReporting.clearReportAjaxStatus(reportId);
		}, 5000);
	},
	
	clearReportAjaxStatus: function(reportId) {
		$('#report_edit_loading_animation_' + reportId)
			.fadeOut('fast');
	}

};