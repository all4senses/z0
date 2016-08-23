if (Drupal.jsEnabled) {
	$(document).ready(function() {
		temp = this.getElementById('edit-uc-auto-sku-enable');		
		if (temp.checked) {
			$('#uc-auto-sku').show(0);
		} else {
			$('#uc-auto-sku').hide(0);
		}

		$('#edit-uc-auto-sku-enable').click(function() {
			if (this.checked) {
				$('#uc-auto-sku').show(0);
			} else {
				$('#uc-auto-sku').hide(0);
			}
		});
	});
}