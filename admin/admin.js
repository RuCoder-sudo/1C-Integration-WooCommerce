jQuery(function ($) {
	/* Tab switching */
	$('.wc1c-tab-btn').on('click', function () {
		var tabId = $(this).data('tab');
		$('.wc1c-tab-btn').removeClass('active');
		$('.wc1c-tab-content').removeClass('active');
		$(this).addClass('active');
		$('#' + tabId).addClass('active');
	});

	/* Copy to clipboard */
	$('.wc1c-copy-btn').on('click', function (e) {
		e.preventDefault();
		var text = $(this).data('copy');
		if (navigator.clipboard) {
			navigator.clipboard.writeText(text).then(function () {
				alert('Скопировано: ' + text);
			});
		} else {
			var el = document.createElement('textarea');
			el.value = text;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
			alert('Скопировано: ' + text);
		}
	});

	/* Auto-uppercase license key */
	$('#license_key').on('input', function () {
		var pos = this.selectionStart;
		$(this).val($(this).val().toUpperCase());
		this.setSelectionRange(pos, pos);
	});
});
