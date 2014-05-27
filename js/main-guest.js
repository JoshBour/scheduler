$(function () {

    var flash = $('#flash');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('#dateRangeToggle').on('click', function () {
        $('#dateRange').slideToggle();
    });

    $('#optionPanel').css('text-align','left');

    $('td').on('mouseover mouseout', function () {
        var cols = $('colgroup');
        var i = $(this).prevAll('td').length - 1;
        if ($('body').hasClass('schedulePage')) i++;
        console.log(i);
        $(this).parent().toggleClass('hover')
        $(cols[i]).toggleClass('hover');
    })

    $('table').on('mouseleave', function () {
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

    var table = $('#scheduleTable');
    var headersToDisable = [2];
    var headerCount = 2;
    var headers = table.find('thead th').each(function () {
        headersToDisable.push(headerCount++);
    });
    headersToDisable.pop();
    headersToDisable.pop();
    table.dataTable({
//            stateSave: true,
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': headersToDisable }
        ],
        "order": [
            [ 1, "desc" ]
        ],
        "pagingType": "full_numbers",
//            "sScrollY": "650px",
//        "autoWidth":true,
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        "bScrollCollapse": true
//        "bPaginate": false,
////        "bFilter": false,
//        "bInfo" : false
    });

});