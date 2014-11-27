/**
 * @package iMegaTeleport
 * @version 1.6
 *
 * Copyright 2013 iMega ltd (email: info@imega.ru)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
jQuery(document).ready(function () {
    var $progress = parseInt(jQuery('#iMegaExistProgress').val());
    var $typeMsg  = jQuery('#iMegaProgress').attr('class');

    if ($typeMsg != 'error' && (isNaN($progress) || $progress == 0)) {
        jQuery('#iMegaProgress').hide();
    }

    jQuery('#iMegaTeleportProgressBar').progressbar({
        value: parseInt($progress)
    });
    jQuery('#iMegaTeleportProgressBar > div').css('float', 'left');
    jQuery('<div>', {
        id: 'iMegaProgressDisplay',
        css: {'width': 40, 'float': 'left', 'lineHeight': '28px', 'fontSize': 22},
        text: $progress + '%'
    }).appendTo(jQuery('#iMegaTeleportProgressBar'));

    jQuery('.imegasets').on('click', 'input:checkbox', function () {
        var $data = {
            action: 'imegateleport-settings',
            param: jQuery(this).attr('name'),
            value: jQuery(this).prop('checked')
        };
        jQuery.post(ajaxurl, $data);
    });
    jQuery('#imegaTeleportClose').css('cursor', 'pointer')
        .click(function () {
            jQuery('#iMegaInfo').slideUp();
            var $data = {
                action: 'imegateleport-settings',
                param: 'postinstall',
                value: 'false'
            };
            jQuery.post(ajaxurl, $data);
        });
    jQuery('#imegaGOGO').css('cursor', 'pointer')
        .click(function () {
            jQuery('#iMegaProgress').slideDown();
            var data = {
                action: 'imegagogo'
            };
            jQuery.post(ajaxurl, data, function (response) {
                iMegaTeleportGetProgress();
            });
        });

    if ($progress >= 1) {
        iMegaTeleportGetProgress();
    }
});

function iMegaTeleportGetProgress() {
    var data = {
        action: 'imega_teleport'
    };
    clearInterval(iMegaProgress);
    jQuery.post(ajaxurl, data, function (response) {
        iMegaProgress = setInterval(function () {
            iMegaTeleportGetProgress();
        }, 500);
        var $value = response.progress * 1;
        if (typeof response.error !== 'undefined') {
            clearInterval(iMegaProgress);
            jQuery('#iMegaExistProgress').val(0);
            jQuery('#iMegaProgress').attr('class', 'error');
            jQuery('#iMegaTeleportProgressMsg').html(response.error);
            var $value = 0;
        }

        if ($value >= 100 || response.complete == 1) {
            clearInterval(iMegaProgress);
            jQuery('#iMegaProgress').slideUp();
            jQuery('#iMegaExistProgress').val(0);
            jQuery('#iMegaTeleportProgressBar').progressbar({ value: 0 });
            jQuery('#iMegaProgressDisplay').text('0%');
        }
        iMegaAnimateProgress($value);
    }, 'json');
}

function iMegaAnimateProgress($value) {
    var $progress = jQuery('#iMegaTeleportProgressBar');
    var $diplay = jQuery('#iMegaProgressDisplay');
    var pVal = $progress.progressbar('option', 'value');

    if (pVal == $value) {
        return;
    }
    var pCnt = !isNaN(pVal) ? pVal : 1;
    animateProgress = setInterval(function () {
        if (pCnt > $value) {
            clearInterval(animateProgress);
            animateProgress = undefined;
        } else {
            $progress.progressbar({value: pCnt});
            $diplay.text((pCnt) + '%');
            pCnt = pCnt+1;
        }
    }, 10);
}