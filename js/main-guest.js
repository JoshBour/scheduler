$(function () {

    var flash = $('#flash');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('#dateRangeToggle').on('click', function () {
        $('#dateRange').slideToggle();
    });

    $('td').on('mouseover mouseout',function(){
        var cols = $('colgroup');
        var i = $(this).prevAll('td').length-1;
        if($('body').hasClass('schedulePage')) i++;
        console.log(i);
        $(this).parent().toggleClass('hover')
        $(cols[i]).toggleClass('hover');
    })

    $('table').on('mouseleave',function(){
        $('colgroup').removeClass('hover');
    });

    $('#dateRange .button').on('click', function () {
        var start = $('#dateRangeStart').val();
        var end = $('#dateRangeEnd').val();
        if (start == '' || end == '') {
            return null;
        } else {
            var splitStart = start.split('-');
            var splitEnd = end.split('-');
            var startDate = new Date(splitStart[2], splitStart[1], splitStart[0]);
            var endDate = new Date(splitEnd[2], splitEnd[1], splitEnd[0]);
            var daysDiff = daysBetween(startDate, endDate);
            if (!daysDiff || daysDiff > 31) {
                return null;
            }
            window.location.href = baseUrl + '/schedule/from/' + start + '/to/' + end;
        }
    });

    $('#dateRangeStart, #dateRangeEnd').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        formatDate: 'd-m-Y'
    });

});