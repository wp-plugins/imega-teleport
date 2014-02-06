jQuery(document).ready(function() {
	var $progress = jQuery('#iMegaExistProgress').val();

	if ($progress == 0) {
		jQuery('#iMegaProgress').hide();
	}
	
	jQuery('#iMegaTeleportProgressBar').progressbar({
		value: parseInt($progress)
	});
	
	jQuery('.imegasets').on('click', 'input:checkbox', function() {
		var $data = { 
				action: 'imegateleport-settings',
				param: jQuery(this).attr('name'),
				value: jQuery(this).prop('checked')
		};
		jQuery.post(ajaxurl, $data);
	});
	jQuery('#imegaTeleportClose').css('cursor', 'pointer')
		.click(function() {
			jQuery('#iMegaInfo').slideUp();
			var $data = { 
					action: 'imegateleport-settings',
					param: 'postinstall',
					value: 'false'
			};
			jQuery.post(ajaxurl, $data);
		});
		
	jQuery('#imegaGOGO').css('cursor', 'pointer')
		.click(function() {
		jQuery('#iMegaProgress').slideDown();
		var data = {
			action: 'imegagogo'
		};
		jQuery.post(ajaxurl, data);
		iMegaProgress = setInterval(function(){
			iMegaTeleportGetProgress();
		},1000);
	});
	
	if ($progress >= 1) {
		iMegaProgress = setInterval(function(){
			iMegaTeleportGetProgress();
			},1000);
	}
});

function iMegaTeleportGetProgress() {
	var data = {
		action: 'imega_teleport'
	};
	jQuery.post(ajaxurl, data, function(response) {
		iMegaInteval = setInterval(
			function(){
				var $value = response*1;
				iMegaAnimateProgress($value);
				if ($value >= 100) {
					if (typeof iMegaInteval === "undefined") {
						clearInterval(iMegaProgress);
						jQuery('#iMegaProgress').slideUp();
						jQuery('#iMegaExistProgress').val(0);
						jQuery('#iMegaTeleportProgressBar').progressbar({
							value: 0
						});
					}
				}
			},10);
	});
}

function iMegaAnimateProgress($value, $animate) {
    var $progress = jQuery('#iMegaTeleportProgressBar');
	var pVal = $progress.progressbar('option', 'value');
    var pCnt = !isNaN(pVal) ? (pVal + 1) : 1;
	if (pCnt >= $value) {		
        clearInterval(iMegaInteval);
        iMegaInteval = undefined;
    } else {
    	$progress.progressbar({value: pCnt});
    }
}