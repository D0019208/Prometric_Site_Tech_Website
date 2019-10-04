/* global moment, FullCalendar, sortNumber, Swal */

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var hour = moment();
    var technician_id = sessionStorage.getItem("user_id");

    var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        plugins: ['interaction', 'resourceTimeline'],
        timeZone: "local",
        header: {
            left: 'today prev,next addEventButton',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineMonth'
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
        defaultView: 'resourceTimelineDay',
        longPressDelay: 500,
        titleRangeSeparator: ' \u2013 ',
        scrollTime: hour.hours() + ":00:00",
        minTime: "06:00:00",
        maxTime: "22:00:00",
        displayEventTime: false,
        aspectRatio: 1.5,
        nowIndicator: true,
        loading: function (isLoading, view) {
            if (isLoading) {// isLoading gives boolean value
                $(".loading").css({"display": "block"});
            } else {
                $(".loading").css({"display": "none"});
            }
        },
        resourceRender: function (renderInfo) {

            let name = renderInfo.el.children[0].children[0].children[1];
            let profile_url = renderInfo.resource.extendedProps.avatar;

            $(renderInfo.el.children[0].children[0].children[0]).before('<img src="' + profile_url + '" width="40" height="40" onclick="window.location.href = \'profile.php?technician=' + renderInfo.resource.title + '\'" class="rounded-circle" style="cursor: pointer; border-radius: 50%">');

            $(name).hover(function () {
                $(name).addClass('resource_active');
            }, function () {
                $(name).removeClass('resource_active');
            });
            $(name).click(function () {
                window.location.href = "profile.php?technician=" + renderInfo.resource.title;
            });
        },
        eventAfterAllRender: function (info) {
            $('.fc-scroller').animate({scrollTop: '0px'}, 0);
        },
        eventResize: function (info) {
            //console.log(info);
            let resource_ids = info.event._def.resourceIds;
            let user_logged_in = technician_id === null ? false : true;
            let access_level = sessionStorage.getItem("accessLevel");
            let allow_edit = false;

            for (let i = 0; i < resource_ids.length; i++)
            {
                if (parseInt(resource_ids[i]) === parseInt(technician_id) || access_level === "2")
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
                    confirmButtonText: 'Change Date',
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
                                final_end_date = moment(document.getElementById('swal-input2').value).add(1, "days").format("YYYY-MM-DD HH:mm:ss");
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
                    text = "You are not authorized to edit someone elses tasks/events. Please contact your manager to change it if it is required."
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
        eventDrop: function (info) {
            //console.log(info);
            let resource_ids = info.oldEvent._def.resourceIds;
            let user_logged_in = technician_id === null ? false : true;
            let access_level = sessionStorage.getItem("accessLevel");
            let allow_edit = false;
            let user_has_task = false;

            let access_code;

            for (let i = 0; i < resource_ids.length; i++)
            {
//                console.log(resource_ids[i]);
//                console.log(technician_id);
                if (parseInt(resource_ids[i]) === parseInt(technician_id) || access_level === "2")
                {
                    allow_edit = true;
                }
            }

            for (let i = 0; i < info.oldEvent._def.resourceIds.length; i++)
            {
                if (info.newResource !== null)
                {
                    if (info.newResource.id === info.oldEvent._def.resourceIds[i])
                    {
                        user_has_task = true;
                    }
                }
            }

            if (info.event.extendedProps.event_type === "upcoming_site")
            {
                access_code = "upcoming_site";
            } else if (info.event.extendedProps.event_type === "checklist")
            {
                access_code = "checklist";
            } else if (info.event.extendedProps.event_type === "documents")
            {
                access_code = "documents";
            } else if (info.event.extendedProps.event_type === "other")
            {
                access_code = "other";
            }

//            console.log("Allowed to edit = " + allow_edit);
//            console.log("User has task = " + user_has_task);
//            console.log(info.oldEvent._def.resourceIds);
//            console.log(info.event._def.resourceIds);
//            console.log(isEqual(info.oldEvent._def.resourceIds, info.event._def.resourceIds));


            if (allow_edit && !user_has_task && !isEqual(info.oldEvent._def.resourceIds, info.event._def.resourceIds)) {
                let old_ids = info.oldEvent._def.resourceIds;
                let new_ids = info.event._def.resourceIds;
                let ids_add = [];
                let ids_remove = [];

                new_ids = new_ids.map(function (x) {
                    return parseInt(x, 10);
                });

                old_ids = old_ids.map(function (x) {
                    return parseInt(x, 10);
                });

                new_ids.sort(sortNumber);
                old_ids.sort(sortNumber);

//                console.log("Old ")
//                console.log(old_ids);
//                console.log("New ")
//                console.log(new_ids);

                for (let i = 0; i < new_ids.length; i++)
                {
                    if (new_ids.length !== old_ids.length)
                    {
                        info.revert();
                        Swal.fire({
                            type: 'error',
                            title: 'Duplicate Task',
                            html: "This technician is already working on <b><u>" + info.event.extendedProps.checklist_header + "</u></b>",
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Okay'
                        });
                    }
                }

                if (new_ids.length === old_ids.length) {

                    for (let i = 0; i < new_ids.length; i++)
                    {
                        if (old_ids[i] !== new_ids[i])
                        {
                            ids_remove.push(old_ids[i]);
                            ids_add.push(new_ids[i]);
                        }
                    }
                }

                let new_build = false;
                if (typeof info.event.extendedProps.checklist_header !== "undefined")
                {
                    if (info.event.extendedProps.checklist_header.indexOf("New Build") >= 0) {
                        new_build = true;
                    }
                } else
                {
                    if (info.event.extendedProps.event.indexOf("New Build") >= 0) {
                        new_build = true;
                    }
                }

//                console.log(ids_add[0]);
//                console.log(ids_remove[0]);


                $.ajax({
                    type: 'POST',
                    url: "backend/save/save_event.php",
                    dataType: 'text',
                    data: {delete_id: ids_remove[0], add_id: ids_add[0], checklist_id: info.event.id, access_code: "team_calender_move", internal_access_code: access_code, new_build: new_build},
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
                let text;

                if (!allow_edit)
                {
                    text = "You are not authorized to edit someone elses tasks/events. Please contact your manager to change it if it is required.";
                } else if (user_has_task || isEqual(info.oldEvent._def.resourceIds, info.event._def.resourceIds)) {
                    text = "The user to which you wish to move the task to is already assigned to this task.";
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
//
//            console.log(ids_add);
//            console.log(ids_remove);
        },
        dateClick: function (info) {
            //console.log(info);

            let date = moment();
            let info_date = moment(info.date);
            let selected_id = info.resource.id;

            if (technician_id === info.resource.id && info_date >= date.subtract(1, 'days') || sessionStorage.getItem("accessLevel") === '2' && info_date >= date.subtract(1, 'days'))
            {
                if ($("#eventActivityType").length <= 2)
                {
                    create_activity_types("other", selected_id);
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

                if (technician_id !== info.resource.id && sessionStorage.getItem("accessLevel") !== '2')
                {
                    error = "You cannot add an event for someone else!";
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
        eventClick: function (info) {
            //console.log(info);
            if (info.event.extendedProps.event_type === "checklist")
            {
                let now = moment();
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                let end_date = moment(info.event.end).subtract(1, "days");

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
            } else if (info.event.extendedProps.event_type === "site")
            {
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                $("#go_to_checklist_event").remove();

                if (info.event.extendedProps.site_code !== null && $("#site_news_event_site_code").length === 0)
                {
                    $("#modal_news_event_container").prepend("<p id='site_news_event_site_code'><i class='glyphicon glyphicon-lock'></i><b> Site Code: </b> " + info.event.extendedProps.site_code + "</p>");
                }

                $("#site_news_event_event_description").html("<i class='glyphicon glyphicon-list-alt'></i><b> Event: </b> " + info.event.extendedProps.event);
                $("#site_news_event_technician").html("<i class='glyphicon glyphicon-user'></i><b> Technician: </b> " + info.event.extendedProps.technician);
                $("#site_news_event_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Occured On: </b> " + start_date);

                if (info.event.extendedProps.event_type === "site")
                {
                    $("#modal_news_event_container").append("<button type='submit' class='btn btn-primary btn-block btn-lg' id='go_to_checklist_event' style='background: #5cb85c; border: none; line-height: normal;'>Go to checklist </button>");
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

                $("#modal_documents_event_container").append("<button type='submit' class='btn btn-primary btn-block btn-lg' id='go_to_document' style='background: #5cb85c; border: none; line-height: normal;'>Open Document </button>");

                $("#go_to_document").click(function () {
                    window.location.href = "#";
                });

                $('#documents_event').modal();
            } else if (info.event.extendedProps.event_type === "other")
            {
                let start_date = moment(info.event.start).format("DD-MM-YYYY");
                $("#go_to_checklist_event").remove();

                $("#site_news_event_event_description").html("<i class='glyphicon glyphicon-list-alt'></i><b> Event: </b> " + info.event.extendedProps.event);
                $("#site_news_event_technician").html("<i class='glyphicon glyphicon-user'></i><b> Technician: </b> " + info.event.extendedProps.technician);
                $("#site_news_event_start_date").html("<i class='glyphicon glyphicon-calendar'></i><b> Occured On: </b> " + start_date);

                $('#site_news_event').modal();
            }
        },
        views: {
            resourceTimelineDay: {
                buttonText: 'today',
                slotDuration: '00:15'},

            resourceTimelineWeek: {
                type: 'resourceTimeline',
                duration: {days: 7},
                buttonText: '7 days'}
        },
        resourceAreaWidth: '30%',
        editable: true,
        resourceLabelText: 'Technician',
        resources: 'backend/get_all_technicians.php',
        events: 'backend/get_all_events.php'
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
            let event_end_date = $("#eventEndDate").val();
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
                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("SQLSTATE") || data.includes("Site Code does not exist!"))
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

//event._def.resourceIds