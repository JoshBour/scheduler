$(function () {

    var flash = $('#flash');
    var body = $('body');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('span[class$="Toggle"]').on('click',function(){
        var toggler = $(this).attr('class');
        var element = $('#'+toggler.substr(0,toggler.length-6));
        console.log(element);
        if (element.is(':visible')) {
            element.slideToggle("normal", function () {
                ofh = new $.fn.dataTable.FixedHeader(table);
            });
        } else {
            element.slideToggle();
            body.children(".fixedHeader").each(function () {
                $(this).remove();
            });
        }
    });

    $('#optionPanel').css('text-align','left');

    $('td').on('mouseover mouseout', function () {
        var cols = $('colgroup');
        var i = $(this).prevAll('td').length - 1;
        if ($('body').hasClass('schedulePage')) i++;
        $(this).parent().toggleClass('hover')
        $(cols[i]).toggleClass('hover');
    })

    $('table').on('mouseleave', function () {
        $('colgroup').removeClass('hover');
    });

    $('#dateRange').find('.button').on('click', function () {
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

    $('#dateRangeStart, #dateRangeEnd').datePicker();

    var table = $('.scheduleTable');
    var headersToDisable = [2];
    var headerCount = 2;
    var headers = table.find('thead th').each(function () {
        headersToDisable.push(headerCount++);
    });
    headersToDisable.pop();
    headersToDisable.pop();
    table = table.dataTable({
//            stateSave: true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': headersToDisable }
        ],
        "order": [
            [ 1, "desc" ]
        ],
        "pagingType": "full_numbers",
        "iDisplayLength": 50
    }).on('length.dt', function (e, settings, len) {
        body.children(".fixedHeader").each(function () {
            $(this).remove();
        });
        table.fnAdjustColumnSizing();
        ofh = new $.fn.dataTable.FixedHeader(table);
    });
    var ofh = new $.fn.dataTable.FixedHeader(table);

});