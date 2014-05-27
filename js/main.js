$(function () {

    function resetActiveField() {
        $('td, .entry').each(function () {
            $(this).removeClass('activeField');
        })
    }

    function updateEditedField() {
        var span = $('#editDone');
        var activeField = $('.activeField');
        var value = span.siblings().val();
        if(activeField.hasClass('editColor')){
            value = span.siblings('select').find('option:selected').text();
            activeField.css('color',value);
        }
        activeField.html(value);
        activeField.removeClass('activeField');
        span.parent().hide();
    }

    var body = $('body');
    var flash = $('#flash');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('#addWorkerToggle').on('click', function () {
        $('#addWorkers').slideToggle();
    });

    $('#addAdminToggle').on('click', function () {
        $('#addAdmin').slideToggle();
    });

    $('#dateRangeToggle').on('click', function () {
        $('#dateRange').slideToggle();
    });

    $('#addEntryToggle').on('click', function () {
        $('#addEntry').slideToggle();
    });

    $('#addExceptionToggle').on('click', function () {
        $('#addException').slideToggle();
    });

    $('td').on('mouseover mouseout', function () {
        var cols = $('colgroup');
        var i = $(this).prevAll('td').length - 1;
        if (body.hasClass('schedulePage') || body.hasClass('changelogPage')) i++;
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

    $(document).on('submit', '#addWorkerForm, #addAdminForm, #addEntryForm, #addExceptionForm', function (e) {
        if (confirm("Are you sure you want to submit the form? All unsaved changes will be lost!")) {
            var form = $(this);
            var parent = form.parent();
            parent.toggleLoadingImage();
            form.ajaxSubmit({
                target: '#' + form.attr('id'),
                type: "post",
                success: function (responseText) {
                    console.log(responseText);
                    if (responseText.redirect) {
                        location.reload();
                    }
                    $('.datetimeInput').datetimepicker({
                    });
                    parent.toggleLoadingImage();
                }
            });
        }
        return false;
    });

    $('#editPanel').find('.datetimeInput').each(function () {
        $(this).datetimepicker({
            datepicker: false,
            format: 'H:i'
        });
    });


    $('form .datetimeInput').datetimepicker({
    });

    $(document).on('dblclick', '.editableTable td', function () {
        var td = $(this);
        if (!td.hasClass('activeField') && !td.hasClass('delete')) {
            resetActiveField();
            td.addClass('activeField');
            var content = td.text();
            var editPanel = $('#editPanel');
            var editInput = editPanel.children('.editInput');
            var position = td.position();
            var width = td.outerWidth();
            editPanel.css('width', width - 1);
            editPanel.css('margin-left', position.left).show();
            editPanel.css('top', position.top + td.outerHeight());
            if (td.hasClass('editDatetime')) {
                if (content != '') {
                    var splitDate = content.split('-');
                    var date = new Date(splitDate[2], splitDate[1] - 1, splitDate[0])
                } else {
                    var date = new Date();
                }
                var input = $('<input type="text" id="mydate" value="' + content + '"></input>');
                editInput.replaceWith(input);
                editPanel.css('width', width - 1);
                input.css('width', width - 10).focus();
                editPanel.children('input').datetimepicker({
                    timepicker: false,
                    format: 'd-m-Y',
                    formatDate: 'd-m-Y'
                });
            } else if (td.hasClass('editColor')) {
                var input = $('.colorSelect');
                input.find('option').filter(function () { return $(this).html() == content; }).attr('selected','selected');
                editInput.replaceWith(input);
                input.css('width', width - 10).focus();
            } else {
                input = $('<textarea>' + content + '</textarea>')
                editInput.replaceWith(input);
                input.css('width', width - 10).focus();
            }
        }
    });

    $(window).keypress(function (e) {
        if (e.keyCode == 13) {
            if ($('#editPanel').is(':visible')) {
                updateEditedField();
            }
        }
    });

    $(document).on('click', '#editDone', function () {
        updateEditedField();
    });

    $('#saveChanges').on('click', function () {
        var table = $('.editableTable');
        var entities = [];
        var id = "";
        if (table.length != 0) {
            id = table.attr('id');
            var separator = id.length - 1; // we subtract one because of the plural
            table.find('tbody tr').each(function () {
                var tr = $(this);
                var entity = {};
                tr.children('td').each(function () {
                    var td = $(this);
                    if (!td.hasClass('delete')) {
                        var field = td.attr('class').split(' ')[0].substr(separator);
                        if (td.text == "") {
                            entity[field] = null;
                        } else {
                            entity[field] = td.text();
                        }
                    }
                });
                entities.push(entity);
            });
        } else {
            table = $('#scheduleTable');
            id = "schedule";
            table.find('tbody .entry').each(function () {
                var entity = {};
                var entry = $(this);
                var entryId = entry.children('.entryId').html();
                var entryValue = $.trim(entry.children('.entryValue').html());
                if (!(entryValue == "-" && entryId.length == 0) && entry.children('.workerName').length == 0 && entry.children('.workerPosition').length == 0) {
                    entity["entryId"] = entryId.length != 0 ? entryId : 0;
                    entity["workerId"] = entry.children('.workerId').html();
                    entity["exception"] = entry.children('.exception').html();
                    entity["startTime"] = entry.children('.startDate').html() + " " + entry.children('.startTime').html();
                    entity["endTime"] = entry.children('.endDate').html() + " " + entry.children('.endTime').html();
                    entities.push(entity);
                }
            });
        }
        console.log(entities);
        $.ajax({
            url: baseUrl + '/' + id + '/save',
            type: "POST",
            data: {
                "entities": entities
            }
        }).success(function (data) {
            if (data.success == 1) {
                location.reload();
            }
            addMessage(data.message);
        }).error(function () {
            addMessage('Something with wrong, please try again.');
        });
    });

    if (body.hasClass('schedulePage')) {
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

        $('.entry').on('mouseenter mouseleave', function () {
            var entry = $(this);
            if (entry.children('.entryValue').text() != '')
                $(this).toggleClass('highlighted');
        });

        $('.entryValue').each(function () {
            var span = $(this);
            if (span.text() != '')
                span.parent().on('hover', function () {
                    $(this).css('background', '#aaa');
                });
        });

        $(document).on('dblclick', '#scheduleTable .entry', function (e) {
            var entry = $(this);
            if (!entry.hasClass('activeField') && !entry.hasClass('delete')) {
                resetActiveField();
                var td = entry.parent();
                entry.addClass('activeField');
                var editPanel = $('#editPanel');
                editPanel.find('input, select').val('');
                var position = td.position();
                var width = entry.outerWidth() < 100 ? 200 : entry.outerWidth();
                editPanel.css({
                    'width': width - 1,
                    'margin-left': position.left,
                    'top': position.top + td.outerHeight() + 85
                }).show();
                if (entry.children('.entryValue').html() != '') {
                    editPanel.find('input[name="entry[startTime]"]').val(entry.children('.startTime').text());
                    editPanel.find('input[name="entry[endTime]"]').val(entry.children('.endTime').text());
                }
                $('#editPanel input, #editPanel select').css("width", width - 10);
            }
        });

        $(document).on('click', '#formEditDone, #formEditRemove', function () {
            var span = $(this);
            var editPanel = span.parent();
            var activeField = $('.activeField');
            activeField.removeClass('activeField');
            var exception = editPanel.find('select[name="entry[exception]"] option:selected').text();
            var startTime = editPanel.find('input[name="entry[startTime]"]').val();
            var endTime = editPanel.find('input[name="entry[endTime]"]').val();
            console.log(exception);
            if (span.attr('id') == 'formEditDone') {
                activeField.find('.exception').html(exception);
                activeField.find('.startTime').html(startTime);
                activeField.find('.endTime').html(endTime);
                if (exception != "None") {
                    activeField.children('.entryValue').html(exception);
                } else {
                    activeField.children('.entryValue').html(startTime + ' - ' + endTime);
                }
            } else {
                activeField.find('.exception').html("");
                activeField.find('.startTime').html("");
                activeField.find('.startDate').html("");
                activeField.find('.endDate').html("");
                activeField.find('.endTime').html("");
                activeField.children('.entryValue').html(" - ");
            }
            editPanel.hide();
        });
    }

    $('td.delete').on('click', function () {
            if (confirm("Are you sure about this action?")) {
                var table = $('.editableTable');
                var td = $(this);
                var url = table.attr('id');
                var id = td.siblings(':first');
                $.ajax({
                    url: baseUrl + '/' + url + '/remove',
                    type: "POST",
                    data: {
                        "id": id.html()
                    }
                }).success(function (data) {
                    if (data.success == 1) {
                        td.parent().detach();
                    }
                    addMessage(data.message);
                }).error(function () {
                    addMessage('Something with wrong, please try again.');
                });
            }
        }
    );

    $('#resetChanges').on('click', function () {
        location.reload();
    });
});