jQuery(document).ready(function($){


	// Initialize
	_initPasswordConfirm();
	_initDeleteConfirm();
	_initKMR();

	/**
	 * Define functions 
	 */
	
	// Settings page password fields
	function _initPasswordConfirm() {
	
		$("#have_account").change(function() {
			var have_account = $(this).is(':checked');
			
			if (have_account)
				$(".password-confirm").hide("fast");
			else
				$(".password-confirm").show("fast");
		});

	}

	// Confirm delete action
	function _initDeleteConfirm() {

		$(".kmr-delete").click(function(e) {
			var keyword = $(this).data("kw");
			var report 	= $(this).data("report");
			
			if ( keyword )
				var confirm_message = "Are you sure you want to delete keyword '"+keyword+"'?";

			if ( report )
				var confirm_message = "Are you sure you want to delete reports for '"+report+"'?";

			if ( !confirm(confirm_message) ){
				e.preventDefault();
			}
		});
	}

	// KMR actions
	function _initKMR() {
		
		// Keywords form submit
		$("#add_keywords_form").submit( function( event ) {

			event.preventDefault();

			var url 		= $("#kmr_url").val();
			var keywords 	= $("#kmr_keywords").val().replace(/\r?\n|\r/g, ",");
			
			valid = checkInput(url, keywords);

			if ( !valid )
				return;

			$('#add_url_modal').modal('hide');

			var data = {
				'kmr_action': 'check_price',
				'reason': 'add_keywords',
				'url': url,
				'keywords': keywords,
			}

			checkPrice(data);
		});

		// URL form submit
		$("#add_url_form").bind( "submit", function( event ) {

			event.preventDefault();

			var url 		= $("#kmr_url").val();
			var keywords 	= $("#kmr_keywords").val().replace(/\r?\n|\r/g, ",");
			
			valid = checkInput(url, keywords);

			if ( !valid )
				return;

			$('#add_url_modal').modal('hide');

			var data = {
				'kmr_action': 'check_price',
				'reason': 'add_url',
				'url': url,
				'keywords': keywords,
			}

			checkPrice(data);
		});

		// Update keywords via AJAX if rank is empty
		updateKeywords();

	}

	function checkPrice( data ) {

		// Add action to recognize in WP
		data.action = 'kmr_callback';

		// Send POST
		$.post(ajaxurl, data, function(response) {

			response = JSON.parse(response);
			
			var new_data = {
				'kmr_action': 'add_keywords',
				'url': data.url,
				'keywords': data.keywords,
			}

			var confirm_button_text = "Confirm";

			if ( response.upgrade_url != "" )
				confirm_button_text += " Upgrade";

			// If adding keywords send AJAX
			if ( data.reason == 'add_keywords' ) {
				$.confirm({
					title: "Add Keywords for \"" + new_data.url + "\"",	
					confirmButtonClass: "button button-primary",
					confirmButton: confirm_button_text,
					text: response.message,
					confirm: function() {
						if ( response.upgrade_url != "" )
							window.location.href=response.upgrade_url;
						else
							addKeywords(new_data);
					},
					cancel: function() {
						// nothing to do
					}
				});
			}
			// If adding URL submit form
			if ( data.reason == 'add_url' ) {
				$.confirm({
					title: "Add Report for \"" + new_data.url + "\"",	
					confirmButtonClass: "button button-primary",
					confirmButton: confirm_button_text,
					text: response.message,
					confirm: function() {
						if ( response.upgrade_url != "" )
							window.location.href=response.upgrade_url;
						else {
							// unbind initial event
							$("#add_url_form").unbind("submit");
							// submit form will redirect the report page
							$("#add_url_form").submit();
						}
					},
					cancel: function() {
						// nothing to do
					}
				});
			}

		});
	}

	/**
	 * Send AJAX request to add keywords
	 * 
	 */

	function addKeywords( data ) {

		// Add action to recognize in WP
		data.action = 'kmr_callback';

		// Send POST
		$.post(ajaxurl, data, function(response) {
			
			$("#kmr_report").html(response);
			// Need to update
			updateKeywords();
		});
	}

	/**
	 * Search keywords without rank and send AJAX request to update those
	 * 
	 */

	function updateKeywords() {

		$(".report-list .update-keyword").each(function(){
			
			var keyword_id 		= $(this).data("id");
			var url_id 			= $(this).data("urlid");
			var update_action 	= $(this).data("action");

			var data = {
				'action': 'kmr_callback',
				'kmr_action': 'get_keyword',
				'keyword_id': keyword_id,
				'url_id': url_id,
				'update_action': update_action
			}
			// Send POST
			$.post(ajaxurl, data, function(response) {
	
				$("#keyword_" + keyword_id).html(response);
			});
		});
	}

	/**
	 * Check if URL and Keywrods not empty
	 */

	function checkInput( url, keywords ) {

		var message = '';

		if (!keywords)
			message += "You didn't type any keywords to add.\r\n"; 
		if (!url)
			message += "You didn't specify a URL."; 

		if ( message.length != 0 ){
			alert(message);
			return false;
		}

		return true;
	}

});