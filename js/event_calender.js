/* global moment, Swal, Intl */

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['interaction', 'dayGrid', 'timeGrid'],
        timeZone: 'UTC',
        defaultView: 'dayGridMonth',
        header: {
            left: 'prev,next today addEventButton',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        customButtons: {
            addEventButton: {
                text: 'Add event',
                click: function () {
                    if (sessionStorage.getItem("userName") !== null)
                    {
                        if ($("#eventActivityType option").length === 1)
                        {
                            create_activity_types("other");
                        }

                        if (sessionStorage.getItem("accessLevel") === '2')
                        {
                            $("#admin_select").css({display: 'block'});
                        }

                        $("#modal_add_event").modal('toggle');

                        var nowDate = new Date(); 
                        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), nowDate.getHours(), nowDate.getMinutes(), 0, 0);
                        let now = moment(today).format("YYYY-MM-DD HH:mm"); 

                        $('#eventStartDate').datetimepicker({
                            minDate: today,
                            format: 'YYYY-MM-DD HH:mm'
                        });

                        $('#eventStartDate').val(now);

                        $('#eventEndDate').datetimepicker({
                            minDate: today,
                            format: 'YYYY-MM-DD HH:mm'
                        });

                        $('#eventEndDate').val(now);

                        $('#create_event_form').bootstrapValidator({
                            feedbackIcons: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                event_type: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Please select what type of Event you wish to create.'
                                        }
                                    }
                                },
                                admin_select_tech: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Please select a Technician to assign an event to.'
                                        }
                                    }
                                },
                                eventSiteCode: {
                                    validators: {
                                        stringLength: {
                                            //DOCUMENT THIS!!!
                                            min: 4,
                                            message: "The Site Code must be at least 4 digits long!"
                                        },
                                        notEmpty: {
                                            message: 'Please enter the Site Code!'
                                        }
                                    }
                                },
                                eventActivityType: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Please select the Activity Type!'
                                        }
                                    }
                                },
                                eventSiteCountry: {
                                    validators: {
                                        stringLength: {
                                            min: 4,
                                            message: "A Country cannot have less than 4 letters!"
                                        },
                                        notEmpty: {
                                            message: 'Please enter the Country in which the event will take place!'
                                        }
                                    }
                                },
                                eventSiteCounty: {
                                    validators: {
                                        stringLength: {
                                            min: 2,
                                            message: "A County cannot have less than 2 letters!"
                                        },
                                        notEmpty: {
                                            message: 'Please enter the County in which the event will take place!'
                                        }
                                    }
                                },
                                eventSiteTown: {
                                    validators: {
                                        stringLength: {
                                            min: 2,
                                            message: "A Town cannot have less than 2 letters!"
                                        },
                                        notEmpty: {
                                            message: 'Please enter the Town in which the event will take place!'
                                        }
                                    }
                                },
                                eventStartDate: {
                                    validators: {
                                        date: {
                                            format: 'YYYY/MM/DD hh:mm',
                                            message: 'The value is not a valid date'
                                        },
                                        notEmpty: {
                                            message: 'The Event Start Date cannot be empty!'
                                        }
                                    }
                                },
                                eventEndDate: {
                                    validators: {
                                        date: {
                                            format: 'YYYY/MM/DD hh:mm',
                                            message: 'The value is not a valid date'
                                        },
                                        notEmpty: {
                                            message: 'The Event End Date cannot be empty!'
                                        }
                                    }
                                },
                                event_event_description: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Event Description cannot be empty!'
                                        }
                                    }
                                }
                            }
                        }).on('success.form.bv', function (e) {
                            $('#success_message').slideDown({opacity: "show"}, "slow"); // Do something ...
                            $('#activity_form').data('bootstrapValidator').resetForm();
                            // Prevent form submission
                            e.preventDefault();
                            // Get the form instance
                            var $form = $(e.target);
                            // Get the BootstrapValidator instance
                            var bv = $form.data('bootstrapValidator');
                            // Use Ajax to submit form data
                            $.post($form.attr('action'), $form.serialize(), function (result) {
                                //console.log(result);
                            }, 'json');
                        });

                        $('#eventStartDate').datetimepicker({
                            minDate: today,
                            format: 'YYYY-MM-DD HH:mm'
                        }).on("dp.change", function (e) {
                            $('#create_event_form').bootstrapValidator('updateStatus', "eventStartDate", 'VALID');
                        });
                    } else
                    {
                        Swal.fire({
                            type: 'error',
                            title: 'Not allowed to edit',
                            text: 'You must be logged in to add an event! Please login and try again.',
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function (result) {
                            if (result.value) {
                                $('#modalLogin').modal('toggle');
                            }
                        });
                    }
                }
            }
        },
        editable: true,
        displayEventTime: false,
        eventSources: [
            {
                url: 'backend/get_all_events.php',
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            }
        ],
        titleRangeSeparator: ' \u2013 ',
        loading: function (isLoading, view) {
            if (isLoading) {// isLoading gives boolean value
                $(".loading").css({"display": "block"});
            } else {
                $(".loading").css({"display": "none"});
            }
        },
        eventTimeFormat: {
            hour: "2-digit",
            minute: "2-digit",
            meridiem: false,
            hour12: false
        },
        dateClick: function (info) {
            //console.log(info);

            let date = moment();
            let info_date = moment(info.date);

            if (sessionStorage.getItem("userName") !== null && info_date >= date.subtract(1, 'days') || sessionStorage.getItem("accessLevel") === '2' && info_date >= date.subtract(1, 'days'))
            {
                if ($("#eventActivityType option").length === 1)
                {
                    create_activity_types("other");
                }

                if (sessionStorage.getItem("accessLevel") === '2')
                {
                    $("#admin_select").css({display: 'block'});
                }

                $("#modal_add_event").modal('toggle');

                var nowDate = new Date();
                let now = moment(info.date).format("YYYY-MM-DD HH:mm");

                var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

                $('#eventStartDate').datetimepicker({
                    minDate: today,
                    format: 'YYYY-MM-DD HH:mm'
                });

                $('#eventStartDate').val(now);

                $('#eventEndDate').datetimepicker({
                    minDate: today,
                    format: 'YYYY-MM-DD HH:mm'
                });

                $('#eventEndDate').val(now);

                $('#create_event_form').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        event_type: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select what type of Event you wish to create.'
                                }
                            }
                        },
                        admin_select_tech: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select a Technician to assign an event to.'
                                }
                            }
                        },
                        eventSiteCode: {
                            validators: {
                                stringLength: {
                                    //DOCUMENT THIS!!!
                                    min: 4,
                                    message: "The Site Code must be at least 4 digits long!"
                                },
                                notEmpty: {
                                    message: 'Please enter the Site Code!'
                                }
                            }
                        },
                        eventActivityType: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select the Activity Type!'
                                }
                            }
                        },
                        eventSiteCountry: {
                            validators: {
                                stringLength: {
                                    min: 4,
                                    message: "A Country cannot have less than 4 letters!"
                                },
                                notEmpty: {
                                    message: 'Please enter the Country in which the event will take place!'
                                }
                            }
                        },
                        eventSiteCounty: {
                            validators: {
                                stringLength: {
                                    min: 2,
                                    message: "A County cannot have less than 2 letters!"
                                },
                                notEmpty: {
                                    message: 'Please enter the County in which the event will take place!'
                                }
                            }
                        },
                        eventSiteTown: {
                            validators: {
                                stringLength: {
                                    min: 2,
                                    message: "A Town cannot have less than 2 letters!"
                                },
                                notEmpty: {
                                    message: 'Please enter the Town in which the event will take place!'
                                }
                            }
                        },
                        eventStartDate: {
                            validators: {
                                date: {
                                    format: 'YYYY/MM/DD hh:mm',
                                    message: 'The value is not a valid date'
                                },
                                notEmpty: {
                                    message: 'The Event Start Date cannot be empty!'
                                }
                            }
                        },
                        eventEndDate: {
                            validators: {
                                date: {
                                    format: 'YYYY/MM/DD hh:mm',
                                    message: 'The value is not a valid date'
                                },
                                notEmpty: {
                                    message: 'The Event End Date cannot be empty!'
                                }
                            }
                        },
                        event_event_description: {
                            validators: {
                                notEmpty: {
                                    message: 'The Event Description cannot be empty!'
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {
                    $('#success_message').slideDown({opacity: "show"}, "slow"); // Do something ...
                    $('#activity_form').data('bootstrapValidator').resetForm();
                    // Prevent form submission
                    e.preventDefault();
                    // Get the form instance
                    var $form = $(e.target);
                    // Get the BootstrapValidator instance
                    var bv = $form.data('bootstrapValidator');
                    // Use Ajax to submit form data
                    $.post($form.attr('action'), $form.serialize(), function (result) {
                        //console.log(result);
                    }, 'json');
                });

                $('#eventStartDate').datetimepicker({
                    minDate: today,
                    format: 'YYYY-MM-DD HH:mm'
                }).on("dp.change", function (e) {
                    $('#create_event_form').bootstrapValidator('updateStatus', "eventStartDate", 'VALID');
                });

//                        var dateStr = prompt('Enter a date in YYYY-MM-DD format');
//                        var date = new Date(dateStr + 'T00:00:00'); // will be in local time
//
//                        if (!isNaN(date.valueOf())) { // valid?
//                            calendar.addEvent({
//                                title: 'dynamic event',
//                                start: date,
//                                allDay: true
//                            });
//                            alert('Great. Now, update your database...');
//                        } else {
//                            alert('Invalid date.');
//                        }
            } else
            {
                let error;
                let button;

                if (sessionStorage.getItem("userName") === null)
                {
                    error = "You must be logged in to add an event! Please login and try again.";
                    button = "Login";
                } else
                {
                    error = "You cannot add an event for a date in the past!";
                    button = "Try Again";
                }

                Swal.fire({
                    type: 'error',
                    title: 'Not Allowed to edit',
                    text: error,
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: button
                }).then(function (result) {
                    if (result.value && sessionStorage.getItem("userName") === null) {
                        $('#modalLogin').modal('toggle');
                    }
                });
            }








//            alert('Clicked on: ' + info.dateStr);
//            alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
//            alert('Current view: ' + info.view.type);
//            // change the day's background color just for fun
//            info.dayEl.style.backgroundColor = 'red';
        },
        eventDrop: function (info) {
            //console.log(info);
            let resource_ids = info.oldEvent.extendedProps.resourceIds;
            let user_logged_in = sessionStorage.getItem("user_id") === null ? false : true;
            let user_id = sessionStorage.getItem("user_id");
            let access_level = sessionStorage.getItem("accessLevel");
            let allow_edit = false;

            let access_code;

            for (let i = 0; i < resource_ids.length; i++)
            {
                if (parseInt(resource_ids[i]) === parseInt(user_id) || access_level === "2")
                {
                    allow_edit = true;
                }
            }

            if (info.event.extendedProps.event_type === "upcoming_site")
            {
                access_code = "upcoming_site";
            } else if (info.event.extendedProps.event_type === "checklist")
            {
                access_code = "checklist";
            }

            if (allow_edit) {
                let start_date;
                let end_date;
                let html;
                let event_header;

                let selector;

                if (info.event.extendedProps.event_type === "upcoming_site")
                {
                    start_date = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");
                    end_date = moment(info.event.end).subtract(1, "days").format("YYYY-MM-DD HH:mm:ss");

                    event_header = info.event.extendedProps.event;
                    html = '<p style="margin: 0; font-size: 1.125em; font-weight: 300;">Do you want to move the activity <b>\'<u>' + event_header + '</u>\'</b> to the dates shown below?</p> <label style="font-size: 15px; margin-top: 10px;" for="start_date"><u>Start Date</u>:</label><input id="move_event_start_date" class="swal2-input" name="start_date" value="' + start_date + '"> <label style="font-size: 15px;" for="end_date"><u>End Date</u>:</label><br/><input id="move_event_end_date" class="swal2-input" name="end_date" style="margin-top: 0;" value="' + end_date + '">';
                    selector = "#move_event_end_date";
                } else if (info.event.extendedProps.event_type === "checklist")
                {
                    start_date = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");
                    end_date = moment(info.event.end).subtract(1, "days").format("YYYY-MM-DD HH:mm:ss");

                    //console.log(info.event.end);

                    event_header = info.event.extendedProps.checklist_header;
                    html = '<p style="margin: 0; font-size: 1.125em; font-weight: 300;">Do you want to move the activity <b>\'<u>' + event_header + '</u>\'</b> to the dates shown below?</p> <label style="font-size: 15px; margin-top: 10px;" for="start_date"><u>Start Date</u>:</label><input id="move_event_start_date" class="swal2-input" name="start_date" value="' + start_date + '"> <label style="font-size: 15px;" for="end_date"><u>End Date</u>:</label><br/><input id="move_event_end_date" class="swal2-input" name="end_date" style="margin-top: 0;" value="' + end_date + '">';
                    selector = "#move_event_end_date";
                } else if (info.event.extendedProps.event_type === "other") {
                    console.log("hh")
                    console.log(info);
                    start_date = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");
                    end_date = moment(info.event.end).format("YYYY-MM-DD HH:mm:ss");

                    event_header = info.event.extendedProps.event;
                    html = '<p style="margin: 0; font-size: 1.125em; font-weight: 300;">Do you want to move the activity <b>\'<u>' + event_header + '</u>\'</b> to the dates shown below?</p> <label style="font-size: 15px; margin-top: 10px;" for="start_date"><u>Start Date</u>:</label><input id="move_event_start_date" class="swal2-input" name="start_date" value="' + start_date + '"> <label style="font-size: 15px;" for="end_date"><u>End Date</u>:</label><br/><input id="move_event_end_date" class="swal2-input" name="end_date" style="margin-top: 0;" value="' + end_date + '">';
                    selector = "#move_event_end_date";
                } else {
                    start_date = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");

                    event_header = info.event.extendedProps.event;
                    html = '<p style="margin: 0; font-size: 1.125em; font-weight: 300;">Do you want to move the activity <b>\'<u>' + event_header + '</u>\'</b> to the date shown below?</p> <label style="font-size: 15px; margin-top: 10px;" for="start_date"><u>Occured On</u>:</label><input id="move_event_start_date" class="swal2-input" name="start_date" value="' + start_date + '">';
                    selector = "#move_event_start_date";
                }

                Swal.fire({
                    title: 'Move Event',
                    type: 'warning',
                    onOpen: function () {
                        $(selector).blur();

                        if (typeof $(selector) !== "undefined" || typeof $(selector) !== null)
                        {
                            $(selector).datetimepicker({
                                format: 'YYYY-MM-DD HH:mm'
                            });

                            if ($("#move_event_start_date").length)
                            {
                                $("#move_event_start_date").blur();
                                $("#move_event_start_date").datetimepicker({
                                    format: 'YYYY-MM-DD HH:mm'
                                });
                            }
                        }
                    },
                    html: html,
                    focusConfirm: false,
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Change Date',
//                preConfirm: function () {
//                    return [
//                        document.getElementById('swal-input1').value,
//                    ]
//                }
                }).then(function (result) {
                    if (result.value) {
                        let validate_start_date = moment(document.getElementById('move_event_start_date').value);
                        let validate_end_date;
                        let end_date_validated;

                        if (document.getElementById('move_event_end_date') !== null)
                        {
                            validate_end_date = moment(document.getElementById('move_event_end_date').value);
                        }

                        if (typeof validate_end_date === "undefined")
                        {
                            validate_end_date = true;
                        } else
                        {
                            validate_end_date = validate_end_date.isValid();
                        }

                        if (validate_start_date.isValid() && validate_end_date)
                        {
                            let final_start_date = moment(document.getElementById('move_event_start_date').value).format("YYYY-MM-DD HH:mm:ss");
                            let final_end_date;

                            var access_code;

                            if (info.event.extendedProps.event_type === "upcoming_site")
                            {
                                access_code = "upcomingSiteEvents";
                                final_end_date = moment(document.getElementById('move_event_end_date').value).format("YYYY-MM-DD HH:mm:ss");

                            } else if (info.event.extendedProps.event_type === "checklist")
                            {
                                access_code = "checklist";
                                final_end_date = moment(document.getElementById('move_event_end_date').value).format("YYYY-MM-DD HH:mm:ss");
                            } else if (info.event.extendedProps.event_type === "other")
                            {
                                access_code = "other";
                                final_end_date = moment(document.getElementById('move_event_end_date').value).subtract(1, "days").format("YYYY-MM-DD HH:mm:ss");
                            }
//                            else if (info.event.extendedProps.event_type === "documents")
//                            {
//                                access_code = "documents";
//                            } else if (info.event.extendedProps.event_type === "site" || info.event.extendedProps.event_type === "other")
//                            {
//                                access_code = "site";
//                            }

                            $.ajax({
                                type: 'POST',
                                url: "backend/save/save_event.php",
                                dataType: 'text',
                                data: {start_date: final_start_date, end_date: final_end_date, checklist_id: info.event.id, access_code: access_code},
                                success: function (data) {
                                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("SQLSTATE"))
                                    {
                                        Swal.fire({
                                            type: 'error',
                                            title: 'Uncaught Error.',
                                            text: data,
//                footer: '<a href>Why do I have this issue?</a>',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Refresh Page'
                                        }).then(function (result) {
                                            if (result.value) {
                                                location.reload();
                                            }
                                        });
                                    } else
                                    {
                                        calendar.refetchEvents();
                                    }
                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxError(jqXHR, textStatus, errorThrown);
                                }
                            });
                        } else
                        {
                            info.revert();
                        }
                    } else
                    {
                        info.revert();
                    }
                });
            } else
            {
                let text;

                if (user_logged_in)
                {
                    text = "You are not authorized to edit someone elses tasks/events. Please contact your manager to change it if it is required.";
                } else
                {
                    text = "You must be logged in to edit! Please login and try again."
                }

                info.revert();

                Swal.fire({
                    type: 'error',
                    title: 'Not Allowed to edit',
                    text: text,
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: user_logged_in ? 'Logout' : 'Login'
                }).then(function (result) {
                    if (result.value) {
                        if (!user_logged_in)
                        {
                            $('#modalLogin').modal('toggle');
                        } else
                        {
                            sessionStorage.clear();
                            window.location.href = "backend/logoutTransaction.php";
                        }
                    }
                });

            }


        },
        eventResize: function (info) {
            //console.log(info);
            let resource_ids = info.event.extendedProps.resourceIds;
            let user_logged_in = sessionStorage.getItem("user_id") === null ? false : true;
            let user_id = sessionStorage.getItem("user_id");
            let access_level = sessionStorage.getItem("accessLevel");
            let allow_edit = false;

            for (let i = 0; i < resource_ids.length; i++)
            {
                if (parseInt(resource_ids[i]) === parseInt(user_id) || access_level === "2")
                {
                    allow_edit = true;
                }
            }

            if (allow_edit) {
                let start_date = moment(info.event.extendedProps.real_start).format("YYYY-MM-DD HH:mm:ss");
                let end_date = moment(info.event.end).subtract(1, "days").format("YYYY-MM-DD HH:mm:ss");

                let event_header;

                if (info.event.extendedProps.event_type === "upcoming_site")
                {
                    event_header = info.event.extendedProps.event;
                } else if (info.event.extendedProps.event_type === "checklist")
                {
                    event_header = info.event.extendedProps.checklist_header;
                }

                Swal.fire({
                    title: 'Change Due Date',
                    type: 'warning',
                    onOpen: function () {
                        $('#swal-input2').blur();

                        var nowDate = new Date();
                        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

                        $('#swal-input2').datetimepicker({
                            minDate: today,
                            format: 'YYYY-MM-DD HH:mm'
                        });

                        $('#swal-input2').val(end_date);
                    },
                    html: '<p style="margin: 0; font-size: 1.125em; font-weight: 300;">Do you want to change the Expected Finish Date of the activity <b>\'<u>' + event_header + '</u>\'</b> to the value below?</p> <label style="font-size: 15px; margin-top: 15px;" for="event_resize_start_date"><u>Start Date</u>:</label><input id="swal-input1" class="swal2-input" name="event_resize_start_date" disabled value="' + start_date + '"> <label style="font-size: 15px;" for="event_resize_end_date"><u>End Date</u>:</label><input id="swal-input2" class="swal2-input" name="event_resize_end_date" value="' + end_date + '">',
                    focusConfirm: false,
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Change Date'
//                preConfirm: function () {
//                    return [
//                        document.getElementById('swal-input1').value,
//                    ]
//                }
                }).then(function (result) {
                    if (result.value) {
                        let validate_start_date = moment(document.getElementById('swal-input1').value);
                        let validate_end_date = moment(document.getElementById('swal-input2').value);

                        if (validate_start_date.isValid() && validate_end_date.isValid())
                        {
                            let final_start_date = moment(document.getElementById('swal-input1').value).format("YYYY-MM-DD HH:mm:ss");
                            let final_end_date;

                            var access_code;

                            if (info.event.extendedProps.event_type === "upcoming_site")
                            {
                                access_code = "upcomingSiteEvents";
                                final_end_date = moment(document.getElementById('swal-input2').value).format("YYYY-MM-DD HH:mm:ss");

                            } else if (info.event.extendedProps.event_type === "checklist")
                            {
                                access_code = "checklist";
                                final_end_date = moment(document.getElementById('swal-input2').value).format("YYYY-MM-DD HH:mm:ss");
                            } else if (info.event.extendedProps.event_type === "other")
                            {
                                access_code = "other";
                                final_end_date = moment(document.getElementById('swal-input2').value).add(1, "days").format("YYYY-MM-DD HH:mm:ss");
                            }

                            $.ajax({
                                type: 'POST',
                                url: "backend/save/save_event.php",
                                dataType: 'text',
                                data: {start_date: final_start_date, end_date: final_end_date, checklist_id: info.event.id, access_code: access_code},
                                success: function (data) {
                                    //console.log(data)
                                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("SQLSTATE"))
                                    {
                                        Swal.fire({
                                            type: 'error',
                                            title: 'Uncaught Error.',
                                            text: data,
//                footer: '<a href>Why do I have this issue?</a>',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Refresh Page'
                                        }).then(function (result) {
                                            if (result.value) {
                                                location.reload();
                                            }
                                        });
                                    } else
                                    {
                                        calendar.refetchEvents();
                                    }
                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxError(jqXHR, textStatus, errorThrown);
                                }
                            });
                        } else
                        {
                            alert("dsas")
                            info.revert();
                        }
                    } else
                    {
                        info.revert();
                    }
                });
            } else
            {
                let text;

                if (user_logged_in)
                {
                    text = "You are not authorized to edit someone elses tasks/events. Please contact your manager to change it if it is required.";
                } else
                {
                    text = "You must be logged in to edit! Please login and try again.";
                }

                info.revert();

                Swal.fire({
                    type: 'error',
                    title: 'Not Allowed to edit',
                    text: text,
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: user_logged_in ? 'Logout' : 'Login'
                }).then(function (result) {
                    if (result.value) {
                        if (!user_logged_in)
                        {
                            $('#modalLogin').modal('toggle');
                        } else
                        {
                            sessionStorage.clear();
                            window.location.href = "backend/logoutTransaction.php";
                        }
                    }
                });

            }
        },
        longPressDelay: 500,
        eventClick: function (info) {
            //console.log(info);
            if (info.event.extendedProps.event_type === "checklist")
            {
                let now = moment();
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                let end_date;

                if (info.event.end === null)
                {
                    end_date = moment(info.event.start);
                } else
                {
                    end_date = moment(info.event.end).subtract(1, "days");
                }
                $("#checklist_event_title").html("<b>Site Build Status: </b> <u>" + info.event.extendedProps.checklist_header + "</u>");

                $("#checklist_event_site_status").html("<i class='glyphicon glyphicon-wrench'></i><b> Activity Status: </b> " + info.event.extendedProps.status);
                $("#checklist_event_site_code").html("<i class='glyphicon glyphicon-lock'></i><b> Site Code: </b> " + info.event.extendedProps.site_code);
                $("#checklist_event_country").html("<i class='glyphicon glyphicon-globe'></i><b> Site Country: </b> " + info.event.extendedProps.site_country);
                $("#checklist_event_county").html("<i class='glyphicon glyphicon-map-marker'></i><b> Site County: </b> " + info.event.extendedProps.site_county);
                $("#checklist_event_town").html("<i class='glyphicon glyphicon-map-marker'></i><b> Site Town: </b> " + info.event.extendedProps.site_town);
                $("#checklist_event_technicians").html("<i class='glyphicon glyphicon-user'></i><b> Technician(s): </b> " + info.event.extendedProps.technician);
                $("#checklist_event_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Started On: </b> " + start_date);

                if (now >= end_date)
                {
                    $("#checklist_event_end_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Finished On: </b> " + end_date.format("DD-MM-YYYY"));
                } else
                {
                    $("#checklist_event_end_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Expected Finish Date: </b> " + end_date.format("DD-MM-YYYY"));
                }

                $("#go_to_checklist").click(function () {
                    window.location.href = info.event.extendedProps.link;
                });

                $('#checklist_event').modal();
            } else if (info.event.extendedProps.event_type === "site" || info.event.extendedProps.event_type === "other")
            {
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                $("#site_news_event_end_date").remove();

                $("#go_to_checklist_event").remove();

                if (info.event.extendedProps.site_code !== null && $("#site_news_event_site_code").length === 0)
                {
                    $("#modal_news_event_container").prepend("<p id='site_news_event_site_code'><i class='glyphicon glyphicon-lock'></i><b> Site Code: </b> " + info.event.extendedProps.site_code + "</p>");
                }

                $("#site_news_event_event_description").html("<i class='glyphicon glyphicon-list-alt'></i><b> Event: </b> " + info.event.extendedProps.event);
                $("#site_news_event_technician").html("<i class='glyphicon glyphicon-user'></i><b> Technician: </b> " + info.event.extendedProps.technician);
                $("#site_news_event_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Occured On: </b> " + start_date);


                if (info.event.extendedProps.real_end !== "0000-00-00 00:00:00" && info.event.extendedProps.real_end !== null && info.event.extendedProps.event_type === "other")
                {
                    $("#site_news_event_start_date").after("<p id='site_news_event_end_date'><i class='glyphicon glyphicon-calendar'></i><b> Ends On: </b> " + moment(info.event.extendedProps.real_end).format("DD-MM-YYYY") + "</p>");
                } else if (info.event.extendedProps.real_end !== "0000-00-00 00:00:00" && info.event.extendedProps.real_end !== null)
                {
                    $("#site_news_event_start_date").after("<p id='site_news_event_end_date'><i class='glyphicon glyphicon-calendar'></i><b> Ends On: </b> " + moment(info.event.extendedProps.real_end).subtract(1, "days").format("DD-MM-YYYY") + "</p>");
                }
                //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

                if (info.event.extendedProps.event_type === "site")
                {
                    $("#modal_news_event_container").append("<button type='submit' class='btn btn-primary btn-block btn-lg' id='go_to_checklist_event' style='background: #5cb85c; border: none; line-height: normal;'>Go to checklist </button>");
                } else
                {
                    $("#site_news_event_site_code").remove();
                }

                $("#go_to_checklist_event").click(function () {
                    window.location.href = info.event.extendedProps.link;
                });

                $('#site_news_event').modal();
            } else if (info.event.extendedProps.event_type === "upcoming_site") {
                let now = moment();
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                let end_date = moment(info.event.end).subtract(1, "days");

                $("#upcoming_event_title").html("<b>Site Build Status: </b> <u>" + info.event.extendedProps.event + "</u>");

                $("#upcoming_event_site_code").html("<i class='glyphicon glyphicon-lock'></i><b> Site Code: </b> " + info.event.extendedProps.site_code);
                $("#upcoming_event_country").html("<i class='glyphicon glyphicon-globe'></i><b> Site Country: </b> " + info.event.extendedProps.event_country);
                $("#upcoming_event_county").html("<i class='glyphicon glyphicon-map-marker'></i><b> Site County: </b> " + info.event.extendedProps.event_county);
                $("#upcoming_event_town").html("<i class='glyphicon glyphicon-map-marker'></i><b> Site Town: </b> " + info.event.extendedProps.event_town);
                $("#upcoming_event_technicians").html("<i class='glyphicon glyphicon-user'></i><b> Technician(s): </b> " + info.event.extendedProps.technician);
                $("#upcoming_event_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Will Start On: </b> " + start_date);
                $("#upcoming_event_end_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Expected Finish Date: </b> " + end_date.format("DD-MM-YYYY"));

                $('#upcoming_site_event').modal();
            } else if (info.event.extendedProps.event_type === "documents") {
                let start_date = moment(info.event.start).format("DD-MM-YYYY");

                $("#go_to_document").remove();

                $("#document_event_description").html("<i class='glyphicon glyphicon-list-alt'></i><b> Event: </b> " + info.event.extendedProps.event);
                $("#document_technician").html("<i class='glyphicon glyphicon-user'></i><b> Technician: </b> " + info.event.extendedProps.technician);
                $("#document_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Occured On: </b> " + start_date);
                
                if (info.event.extendedProps.event.search("has deleted the") === -1)
                {
                    $("#modal_documents_event_container").append("<button type='submit' class='btn btn-primary btn-block btn-lg' id='go_to_document' onclick='window.location.href = \"" + info.event.extendedProps.link + "\"' style='background: #5cb85c; border: none; line-height: normal;'>Open Document </button>");
                }
                
                $("#go_to_document").click(function () {
                    window.location.href = "#";
                });

                $('#documents_event').modal();
            }
        }
        // 8. Then, enter a date for defaultDate that best displays your events.
        //
        // defaultDate: 'XXXX-XX-XX'
    });

    calendar.render();

    $("#event_type").on("change", function () {
        if ($("#event_type").val() === "upcoming_site")
        {
            var nowDate = new Date();
            let today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

            $("#event_description").html("<p><b><u>Upcoming Site Build:</u></b> Choose this option if you have an upcoming site build. Fill in all the fields.</p>");
            $(".event_event").css({display: 'none'});
            $(".event_site_code, .event_activity_type, .event_start_date, .event_end_date").css({display: 'block'});

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventSiteCode', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventActivityType', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventStartDate', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventEndDate', true);

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'event_event_description', false);

            $('#eventStartDate').datetimepicker({
                minDate: today,
                format: 'YYYY-MM-DD HH:mm'
            }).on("dp.change", function (e) {
                $('#create_event_form').bootstrapValidator('updateStatus', "eventStartDate", 'VALID');
            });

            if (sessionStorage.getItem("accessLevel") !== '2')
            {
                $('#create_event_form').bootstrapValidator('enableFieldValidators', 'admin_select_tech', false);
            }
        } else if ($("#event_type").val() === "site")
        {
            $("#event_description").html("<p><b><u>Site Related:</u></b> This is for any site build, input a Site Code for an existing Site and describe the event. <b><u>NOTE:</u></b> Upcoming Site Codes don't work.</p>");
            $(".event_site_code, .event_start_date, .event_event").css({display: 'block'});
            $(".event_end_date, .event_activity_type").css({display: 'none'});

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventSiteCode', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventStartDate', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'event_event_description', true);

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventStartDate', false);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventActivityType', false);

            if (sessionStorage.getItem("accessLevel") !== '2')
            {
                $('#create_event_form').bootstrapValidator('enableFieldValidators', 'admin_select_tech', false);
            }
        } else if ($("#event_type").val() === "other")
        {
            var nowDate = new Date();
            let today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

            $("#event_description").html("<b><u>Other:</u></b> This is for any other task that does not fit into any of the previous categories such as holidays, small notes, etc.</p>");
            $(".event_start_date, .event_end_date, .event_event").css({display: 'block'});
            $(".event_site_code, .event_activity_type").css({display: 'none'});

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventStartDate', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventEndDate', true);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'event_event_description', true);

            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventSiteCode', false);
            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'eventActivityType', false);

            $('#eventEndDate').datetimepicker({
                minDate: today,
                format: 'YYYY-MM-DD HH:mm'
            }).on("dp.change", function (e) {
                $('#create_event_form').bootstrapValidator('updateStatus', "eventEndDate", 'VALID');
            });

            if (sessionStorage.getItem("accessLevel") !== '2')
            {
                $('#create_event_form').bootstrapValidator('enableFieldValidators', 'admin_select_tech', false);
            }
        }
    });

    $("#create_event").click(function (e) {
        e.preventDefault();

        let validator = $('#create_event_form').data('bootstrapValidator');
        validator.validate();
        if (validator.isValid())
        {
            let user_name = sessionStorage.getItem("userName");

            let event_type = $("#event_type").val();
            let activity_type = $("#eventActivityType").val();
            let site_code = $("#eventSiteCode").val();
            let country = $("#eventSiteCountry").val();
            let county = $("#eventSiteCounty").val();
            let town = $("#eventSiteTown").val();
            let event_start_date = $("#eventStartDate").val();
            let event_end_date = moment($("#eventEndDate").val()).subtract(1, "days").format("YYYY-MM-DD HH:mm:ss");
            let event = $("#event_event_description").val();

            if (sessionStorage.getItem('accessLevel') === '2')
            {
                user_name = $("#admin_select_tech").val();
            }

            $.ajax({
                type: 'POST',
                url: "backend/save/save_event.php",
                dataType: 'text',
                data: {user_name: user_name, activity_type: activity_type, country: country, county: county, town: town, start_date: event_start_date, end_date: event_end_date, event_type: event_type, event: event, access_code: "new_event", site_code: site_code},
                success: function (data) {
                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("SQLSTATE"))
                    {
                        Swal.fire({
                            type: 'error',
                            title: 'Uncaught Error.',
                            text: data,
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Refresh Page'
                        }).then(function (result) {
                            if (result.value) {
                                location.reload();
                            }
                        });
                    } else
                    {
                        header_footer_events();
                        calendar.refetchEvents();
                        $("#modal_add_event").modal('toggle');

                        $('#create_event_form').data('bootstrapValidator').resetForm();
                        $('#create_event_form *').filter(':input').each(function (key, value) {
                            if ($(this).is("input")) {
                                $(this).val("");
                            } else if ($(this).is("select")) {
                                $(this).val('default');
                            } else if ($(this).is("textarea")) {
                                $(this).val("");
                            }
                        });
                        $(".event_site_code, .event_activity_type, .event_start_date, .event_end_date, .event_event").css({display: 'none'});

                        if (sessionStorage.getItem("accessLevel") === '2')
                        {
                            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'admin_select_tech', true);
                        } else
                        {
                            $('#create_event_form').bootstrapValidator('enableFieldValidators', 'admin_select_tech', false);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // if error occured  
                    onAjaxError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    });

    $('#eventStartDate, #eventEndDate').keydown(function (e) {
        var key = e.keyCode || e.charCode;
        if (key === 8 || key === 46) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});