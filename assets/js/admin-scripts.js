(function ($) {
	'use strict';

	jQuery(document).ready(function ($) {
		// Show/hide redirect URL field based on selected action after expired
		var actionAfterExpiredField = $('#action_after_expired');
		var redirectUrlField = $('#redirect_url_field');

		actionAfterExpiredField.on('change', function () {
			if (this.value === 'redirect') {
				redirectUrlField.show();
			} else {
				redirectUrlField.hide();
			}
		});

		// Initialize visibility of redirect URL field on page load
		if (actionAfterExpiredField.val() === 'redirect') {
			redirectUrlField.show();
		} else {
			redirectUrlField.hide();
		}
	});

})(jQuery);