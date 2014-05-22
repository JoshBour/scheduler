// adds a new message to the flash messenger
function addMessage(message) {
    var flash = $('#flash');
    if (flash.is(":visible")) {
        flash.html(message);
    } else {
        flash = $('<div />', {
            id: "flash",
            text: message
        }).prependTo('body');
    }
    flash.setRemoveTimeout(5000);
}

function daysBetween(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime()
    var date2_ms = date2.getTime()

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms)

    if(date2_ms < date1_ms){
        return false;
    }

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY)

}

// mini plugin that will hide an element according to the timout given
(function ($) {
    $.fn.setRemoveTimeout = function (milisecs) {
        var element = $(this);
        if (element.length > 0) {
            setTimeout(function () {
                $(element).slideUp().detach();
            }, milisecs);
        }
        return element;
    };
})(jQuery);

// plugin that will show or hide the loading image
(function ($) {
    $.fn.toggleLoadingImage = function () {
        var element = $(this);
        // we use .find for more secure results
        var loadingImg = element.children('.loadingImg');
        if (loadingImg.length > 0) {
            loadingImg.detach();
        } else {
            $('<div />', {'class': 'loadingImg'}).appendTo(element);
        }
    };
})(jQuery);

// "turn off" the lights and focus the specific element
(function ($) {
    $.fn.focusLight = function () {
        // set the default value for focusedDiv
        var element = $(this);
        //focusedDiv = typeof focusedDiv !== 'undefined' ? $(focusedDiv) : $(this);

        // add the shadow to the body
        $('<div />', {'id': 'shadow'}).appendTo('body');

        element.addClass('focused');

        return element;
    };
})(jQuery);

// "turn on" the lights
(function ($) {
    $.fn.unfocusLight = function () {
        var element = $(this);
        $('#shadow').detach();
        element.removeClass('focused');

        return element;
    };
})(jQuery);