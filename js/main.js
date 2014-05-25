$(function () {

    function resetActiveField() {
        $('td, .entry').each(function () {
            $(this).removeClass('activeField');
        })
    }

    function updateEditedField() {
        var span = $('#editDone');
        var activeField = $('.activeField');
        activeField.html(span.siblings().val());
        activeField.removeClass('activeField');
        span.parent().hide();
    }

    var flash = $('#flash');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

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
        var form = $(this);
        var parent = form.parent();
        parent.toggleLoadingImage();
        form.ajaxSubmit({
            target: '#'+form.attr('id'),
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

    $(document).on('dblclick', '#scheduleTable .entry', function (e) {
        var entry = $(this);
        if (!entry.hasClass('activeField') && !entry.hasClass('delete')) {
            resetActiveField();
            entry.addClass('activeField');
            var editPanel = $('#editPanel');
            editPanel.find('input').val('');
            var position = entry.position();
            var width = entry.outerWidth() < 100 ? 100 : entry.outerWidth();
            editPanel.css({
                'width': width - 1,
                'margin-left': position.left,
                'top': position.top + entry.outerHeight()
            }).show();
            if (entry.children('.entryValue').html() != '') {
                editPanel.find('input[name="entry[startTime]"]').val(entry.children('.startTime').text());
                editPanel.find('input[name="entry[endTime]"]').val(entry.children('.endTime').text());
            }
            $('#editPanel input, #editPanel select').css("width", width - 10);
        }
    });

    $(document).on('dblclick', '.editableTable td', function () {
        var td = $(this);
        if (!td.hasClass('activeField') && !td.hasClass('delete')) {
            resetActiveField();
            td.addClass('activeField');
            var content = td.text();
            var editPanel = $('#editPanel');
            var textArea = editPanel.children('textarea');
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
                textArea.replaceWith(input);
                editPanel.css('width', width - 1);
                input.css('width', width - 10).focus();
                editPanel.children('input').datetimepicker({
                    timepicker: false,
                    format: 'd-m-Y',
                    formatDate: 'd-m-Y'
                });
            } else {
                if (textArea.length == 0) {
                    textArea = $('<textarea>' + content + '</textarea>')
                    editPanel.children('input').replaceWith(textArea)
                } else {
                    textArea.val(content);
                }
                textArea.css('width', width - 10).focus();
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

    $(document).on('click', '#formEditDone', function () {
        var span = $(this);
        var editPanel = span.parent();
        var activeField = $('.activeField');
        activeField.removeClass('activeField');
        var exception = editPanel.find('select[name="entry[exception]"] option:selected').text();
        var startTime = editPanel.find('input[name="entry[startTime]"]').val();
        var endTime = editPanel.find('input[name="entry[endTime]"]').val();
        if(startTime == "")
        activeField.find('.exception').html(exception);
        activeField.find('.startTime').html(startTime);
        activeField.find('.endTime').html(endTime);
        if (exception != "None") {
            activeField.children('.entryValue').html(exception);
        } else {
            activeField.children('.entryValue').html(startTime + ' - ' + endTime);
        }

        editPanel.hide();
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
                if ($.trim(entry.children('.entryValue').html()) != "-" && entry.children('.workerName').length == 0) {
                    var entryId = entry.children('.entryId');
                    entity["entryId"] = entryId.length != 0 ? entryId.text() : 0;
                    entity["workerId"] = entry.children('.workerId').text();
                    entity["exception"] = entry.children('.exception').text();
                    entity["startTime"] = entry.children('.startDate').text() + " " + entry.children('.startTime').text();
                    entity["endTime"] = entry.children('.endDate').text() + " " + entry.children('.endTime').text();
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
            console.log(data);
            if (data.success == 1) {
            }
            addMessage(data.message);
        }).error(function () {
            addMessage('Something with wrong, please try again.');
        });
    });

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