$(function () {

    function resetActiveTd() {
        $('td').each(function () {
            $(this).removeClass('activeTd');
        })
    }

    function updateEditedField() {
        var span = $('#editDone');
        var activeTd = $('.activeTd');
        activeTd.html(span.siblings().val());
        activeTd.removeClass('activeTd');
        span.parent().hide();
    }

    var flash = $('#flash');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('#addWorkerToggle').on('click', function () {
        $('#addWorkers').slideToggle();
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

    $('form .datetimeInput').datetimepicker({
    });

    $('#dateRangeStart, #dateRangeEnd').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        formatDate: 'd-m-Y'
    });

    $(document).on('submit', '#addWorkerForm', function (e) {
        var form = $(this);
        var parent = form.parent();
        parent.toggleLoadingImage();
        form.ajaxSubmit({
            target: '#addWorkerForm',
            type: "post",
            success: function (responseText) {
                if (responseText.redirect) {
                    location.reload();
                }
                parent.toggleLoadingImage();
            }
        });
        return false;
    });

    $(document).on('submit', '#addEntryForm', function (e) {
        var form = $(this);
        var parent = form.parent();
        parent.toggleLoadingImage();
        form.ajaxSubmit({
            target: '#addEntryForm',
            type: "post",
            success: function (responseText) {
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

    $(document).on('submit', '#addExceptionForm', function (e) {
        var form = $(this);
        var parent = form.parent();
        parent.toggleLoadingImage();
        form.ajaxSubmit({
            target: '#addExceptionForm',
            type: "post",
            success: function (responseText) {
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

    $('#editPanel .datetimeInput').datetimepicker({
        datepicker: false,
        format: 'H:i'
    });

    $(document).on('dblclick', '#scheduleTable td', function (e) {
        var td = $(this);
        if (!td.hasClass('activeTd') && !td.hasClass('delete')) {
            resetActiveTd();
            if (td.html() != "") {
                td.addClass('activeTd');
                var editPanel = $('#editPanel');
                var position = td.position();
                var width = td.outerWidth();
                editPanel.css('width', width - 1);
                editPanel.css('margin-left', position.left).show();
                editPanel.css('top', position.top + td.outerHeight());
                editPanel.find('select[name="entry[worker]"] option').each(function () {
                    var option = $(this);
                    if (option.text() == td.children('.worker').text()) option.attr('selected', 'selected');
                });
                editPanel.find('input[name="entry[startTime]"]').val(td.children('.startTime').text());
                editPanel.find('input[name="entry[endTime]"]').val(td.children('.endTime').text());
                editPanel.find('input[name="entry[totalTime]"]').val(td.children('.totalTime').text());
                editPanel.find('input[name="entry[extraTime]"]').val(td.children('.extraTime').text());
                $('#editPanel input, #editPanel select').css("width", width - 10);
            }
        }
    });

    $(document).on('dblclick', '.editableTable td', function () {
        var td = $(this);
        if (!td.hasClass('activeTd') && !td.hasClass('delete')) {
            resetActiveTd();
            td.addClass('activeTd');
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
                input.css('width', width - 10);
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
                textArea.css('width', width - 10);
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
        var activeTd = $('.activeTd');
        activeTd.removeClass('activeTd');
        var exception = editPanel.find('select[name="entry[exception]"] option:selected').text();
        var startTime = editPanel.find('input[name="entry[startTime]"]').val();
        var endTime = editPanel.find('input[name="entry[endTime]"]').val();
        activeTd.find('.exception').html(exception);
        activeTd.find('.startTime').html(startTime);
        activeTd.find('.endTime').html(endTime);
        activeTd.find('.totalTime').html(editPanel.find('input[name="entry[totalTime]"]').val());
        activeTd.find('.extraTime').html(editPanel.find('input[name="entry[extraTime]"]').val());
        if (exception != "None") {
            activeTd.children('.entryValue').html(exception);
        } else {
            activeTd.children('.entryValue').html(startTime + ' - ' + endTime);
        }

        span.parent().hide();
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
            table.find('tbody td').each(function () {
                var entity = {};
                var td = $(this);
                if (td.text() != '') {
                    entity["entryId"] = td.children('.entryId').text();
                    if (entity["entryId"] != "") {
                        entity["surname"] = td.children('.worker').text();
                        entity["exception"] = td.children('.exception').text();
                        entity["startTime"] = td.children('.startDate').text() + " " + td.children('.startTime').text();
                        entity["endTime"] = td.children('.endDate').text() + " " + td.children('.endTime').text();
                        entity["totalTime"] = td.children('.totalTime').text();
                        entity["extraTime"] = td.children('.extraTime').text();
                        entities.push(entity);
                    }
                }
            });
            console.log(entities);
        }
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