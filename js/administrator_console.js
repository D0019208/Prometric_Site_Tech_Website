/* global w2ui */

$(document).ready(function () {
    var technicians = [];
    var dropdown_technicians = [];
    //var dropdown_technicians = [{id: 1, text: "Charlie Dowd"}, {id: 2, text: "Chelvan Tamilchelvan"}, {id: 3, text: "Debra Clark"}, {id: 4, text: "Divy Mohan Shorey"}, {id: 5, text: "George Carswell"}, {id: 6, text: "Jason Pietsch"}, {id: 7, text: "Johan Hoevenberg"}, {id: 8, text: "Nichita Postolachi"}, {id: 9, text: "Simon Scowcroft"}, {id: 10, text: "Tiffany Rantin"}];
    var activities_complete_color = [];
    var activities_in_progress_color = [];
    var upcoming_activities_color = [];
    var activities_complete_border_color = [];
    var activities_in_progress_border_color = [];
    var upcoming_activities_border_color = [];
    var activities_complete_data = [];
    var activities_in_progress_data = [];
    var upcoming_activities_data = [];

    var optional_category_names = [];
    var category_names = [];
    var optional_tab_names = [];
    var tab_names = [];

    var site_information = ["Activities Complete", "Activities In Progress", "Upcoming Activities"];
    var canvas = $('#barChart').get(0).getContext('2d');
    var grid_initialized = false;

    function get_activities_data(grid_name, display_grid) {
        $.ajax({
            type: 'POST',
            url: "backend/get_activities_chart_data.php",
            dataType: 'text',
            data: {technicians: JSON.stringify(technicians)},
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
                }

                //console.log(data);

                activities_complete_data = [];
                activities_in_progress_data = [];
                upcoming_activities_data = [];

                optional_category_names = [];
                category_names = [];
                optional_tab_names = [];
                tab_names = [];

                let json = JSON.parse(data);
                let json_activities_data = json[0];
                let json_optional_category_names = json[1];
                let json_category_names = json[2];
                let json_optional_tab_names = json[3];
                let json_tab_names = json[4];

                //console.log(json);
                for (let key in json_activities_data)
                {
                    activities_in_progress_data.push(json_activities_data[key].activities_in_progress);
                    activities_complete_data.push(json_activities_data[key].activities_complete);
                    upcoming_activities_data.push(json_activities_data[key].upcoming_activities);
                }


                for (let i = 0; i < json_optional_category_names.length; i++)
                {
                    optional_category_names.push(json_optional_category_names[i][0]);
                }

                for (let i = 0; i < json_category_names.length; i++)
                {
                    category_names.push(json_category_names[i][0]);
                }

                for (let i = 0; i < json_optional_tab_names.length; i++)
                {
                    optional_tab_names.push(json_optional_tab_names[i][0]);
                }

                for (let i = 0; i < json_tab_names.length; i++)
                {
                    tab_names.push(json_tab_names[i][0]);
                }

                if (!display_grid)
                {
                    initializeSiteBarChart();
                    if (!grid_initialized)
                    {
                        initialize_grids();
                    }
                } else
                {
                    w2ui[grid_name].reload();
                    w2ui.layout.content('main', w2ui[grid_name]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    }




    function createGenericGrid(name, loadURL, saveURL, deleteURL, header, data, allow_delete, allow_save, allow_add)
    {
        let data_array = data.select_data;
        $().w2grid({
            name: name,
            url: {
                get: loadURL,
                save: saveURL,
                remove: deleteURL
            },
            header: header,
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarAdd: allow_add,
                toolbarDelete: allow_delete,
                toolbarSave: allow_save
            },
            multiSearch: true,
            searches: [],
            columns: data.columns,
            onDelete: function (event) {
                if (event.force)
                {
                    w2ui[name].postData['table_name'] = data.table_name;
                    w2ui[name].postData['where_clause'] = data_array[0];

                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            postData: {
                data: data
            },
            onError: function (event) {
                console.log(event);
            }
        });
    }

    function create_form(header, name, fields, table, access_code, grid_name, ajax_access_code)
    {
        $().w2form({
            header: header,
            name: name,
            fields: fields,
            actions: {
                Save: function () {
                    var errors = this.validate();
                    if (errors.length > 0)
                    {
                        return;
                    }

                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'none'});

                    if (this.recid == 0) {
                        let records = w2ui[grid_name].records;
                        let records_length = w2ui[grid_name].records.length;
                        let duplicate_found = false;

                        let new_record = this;

                        for (let i = 0; i < records_length; i++)
                        {
                            if (access_code === "add_category")
                            {
                                if (records[i].categoryName === this.record.categoryName)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Category Name you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm(alert_options).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_tasks") {
                                if (records[i].taskName === this.record.taskName)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Task Name you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm('The Task Name you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.').yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_tabs")
                            {
                                if (records[i].tabName === this.record.tabName && records[i].tabs_identifier === this.record.tabs_identifier)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Tab Name OR Tab Identifier you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };

                                    w2confirm(alert_options).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_optional_categories")
                            {
                                if (records[i].optionalCategoryName === this.record.optionalCategoryName && records[i].optional_categories_identifier === this.record.optional_categories_identifier)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Optional Category Name OR Optional Category Identifier you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm(alert_options).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_optional_tasks") {
                                if (records[i].optionalTaskName === this.record.optionalTaskName)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Optional Task Name you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm(alert_options).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_optional_tabs")
                            {
                                if (records[i].optionalTabName === this.record.optionalTabName && records[i].optional_tabs_identifier === this.record.optional_tabs_identifier)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Optional Tab Name OR Optional Tab Identifier you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm(alert_options).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            } else if (access_code === "add_activity_type")
                            {
                                if (records[i].activityType === this.record.activityType)
                                {
                                    duplicate_found = true;
                                    let alert_options = {
                                        msg: 'The Activity Name you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                    w2confirm(duplicate_found).yes(function () {
                                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                        return;
                                    }).no(function () {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui[grid_name]);
                                    });

                                    break;
                                }
                            }
                        }

                        if (!duplicate_found)
                        {
                            this.record.recid = records[records_length - 1].recid + 1;

                            $.ajax({
                                type: 'POST',
                                url: "backend/save/new_admin_template.php",
                                dataType: 'text',
                                data: {record: this.record, table_name: table, access_code: ajax_access_code},
                                success: function (data) {
                                    //console.log(data);

                                    if (data === '1')
                                    {
                                        new_record.clear();
                                        get_activities_data(grid_name, true);
                                    } else
                                    {
                                        w2alert(data)
                                                .ok(function () {
                                                    new_record.clear();
                                                    w2ui.layout.content('main', w2ui[grid_name]);
                                                });
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxErrorW2ui(jqXHR, textStatus, errorThrown);
                                }
                            });
                        }

                    } else {
                        w2ui.grid_name.set(this.recid, this.record);
                        w2ui.grid_name.selectNone();

                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                    }
                },
                Cancel: function (event) {
                    //console.log(w2ui[grid_name]);
                    w2ui.layout.content('main', w2ui[grid_name]);
                }
            },
            onRender: function (event) {
                event.onComplete = function () {
                    this.box.children[0].children[4].children[0].className = "w2ui-btn w2ui-btn-green";
                    this.box.children[0].children[4].children[0].innerText = "Add";
                };

            }
        });
    }

    function initialize_grids() {
        grid_initialized = true;
        $().w2grid({
            name: 'checklist_tasks',
            url: {
                get: 'jsonGenerator/get_admin_data.php',
                save: 'backend/save/save_admin_data.php',
                remove: 'backend/delete/delete_admin_records.php'
            },
            header: 'Tasks Panel',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarAdd: true,
                toolbarDelete: true,
                toolbarSave: true
            },
            multiSearch: true,
            searches: [
                {field: 'taskID', caption: 'Task ID', type: 'int'},
                {field: 'categoryName', caption: 'Category Name', type: 'text'},
                {field: 'tabName', caption: 'Tab Name', type: 'text'},
                {field: 'taskName', caption: 'Task Name', type: 'text'}
            ],
            columns: [
                {
                    field: 'recid',
                    caption: 'Task ID',
                    size: '55px',
                    sortable: false,
                    resizable: true,
                    attr: 'align="center"',
                    info: true
                },
                {
                    field: 'taskName',
                    caption: 'Task Name',
                    size: '330px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'categoryName',
                    caption: 'Category Name',
                    size: '410px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'tabName',
                    caption: 'Tab Name',
                    size: '220px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                }
            ],
//        onClick: function (event) {
//            console.log(event);
//            console.log(w2ui["checklist_tasks"])
//        },
            postData: {
                data: {table_name: "checklistTasks", access_code: "admin_save_tasks_tabs", select_data: ["taskID", "categoryName", "tabName", "taskName"]}
            },
            onReload: function (event) {
                w2ui['checklist_tasks'].load('jsonGenerator/get_admin_data.php');
            },
            onStateRestore: function (event) {
                w2ui['checklist_tasks'].load('jsonGenerator/get_admin_data.php');
            },
            onSave: function (event) {
                //Get length of the records array
                let records_length = w2ui["checklist_tasks"].records.length;
                let array_to_send = [];
                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                for (let i = 0; i < records_length; i++)
                {
                    if (typeof w2ui["checklist_tasks"].records[i].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["checklist_tasks"].records[i].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["checklist_tasks"].records[i].w2ui.changes["tabName"] !== 'undefined')
                            {
                                array_to_send.push({tabName: w2ui["checklist_tasks"].records[i]["tabName"], recid: w2ui["checklist_tasks"].records[i].recid});
                            }
                        }
                    }
                }

                w2ui["checklist_tasks"].postData['original_tasks'] = array_to_send;

                event.onComplete = function () {
                    //location.reload();
                };
            },
            onDelete: function (event) {
                if (event.force)
                {
                    w2ui["checklist_tasks"].postData['table_name'] = "checklistTasks";
                    w2ui["checklist_tasks"].postData['where_clause'] = "taskID";
                }
            },
            onError: function (event) {
                console.log(event);
            }
        });

        $().w2grid({
            name: 'checklist_optional_tasks',
            url: {
                get: 'jsonGenerator/get_admin_data.php',
                save: 'backend/save/save_admin_data.php',
                remove: 'backend/delete/delete_admin_records.php'
            },
            header: 'Tasks Panel',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarAdd: true,
                toolbarDelete: true,
                toolbarSave: true
            },
            searches: [
                {field: 'optionalTaskID', caption: 'Optional Task ID', type: 'int'},
                {field: 'optionalCategoryName', caption: 'Optional Category Name', type: 'text'},
                {field: 'optionalTabName', caption: 'Optional Tab Name', type: 'text'},
                {field: 'optionalTaskName', caption: 'Optional Task Name', type: 'text'}
            ],
            columns: [
                {
                    field: 'recid',
                    caption: 'Optional Task ID',
                    size: '107px',
                    sortable: false,
                    resizable: true,
                    attr: 'align="center"',
                    info: true
                },
                {
                    field: 'optionalTaskName',
                    caption: 'Optional Task Name',
                    size: '284px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'optionalCategoryName',
                    caption: 'Optional Category Name',
                    size: '211px',
                    sortable: false,
                    resizable: true,
                    attr: 'align="center"'
                },
                {
                    field: 'optionalTabName',
                    caption: 'Optional Tab Name',
                    size: '119px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                }
            ],
            onSave: function (event) {
                //Get length of the records array
                let records_length = w2ui["checklist_optional_tasks"].records.length;
                let array_to_send = [];
                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                for (let i = 0; i < records_length; i++)
                {
                    if (typeof w2ui["checklist_optional_tasks"].records[i].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["checklist_optional_tasks"].records[i].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["checklist_optional_tasks"].records[i].w2ui.changes["optionalTabName"] !== 'undefined')
                            {
                                array_to_send.push({optionalTabName: w2ui["checklist_optional_tasks"].records[i]["optionalTabName"], recid: w2ui["checklist_optional_tasks"].records[i].recid});
                            }
                        }
                    }
                }

                w2ui["checklist_optional_tasks"].postData['original_tasks'] = array_to_send;

                event.onComplete = function () {
                    //location.reload();
                };
            },
            onDelete: function (event) {
                if (event.force)
                {
                    w2ui["checklist_optional_tasks"].postData['table_name'] = "checklistOptionalTasks";
                    w2ui["checklist_optional_tasks"].postData['where_clause'] = "optionalTaskID";
                }
            },
            postData: {
                data: {table_name: "checklistOptionalTasks", access_code: "admin_save_optional_tasks_tabs", select_data: ["optionalTaskID", "optionalCategoryName", "optionalTabName", "optionalTaskName"]}
            },
            onError: function (event) {
                console.log(event);
            }
        });

        $().w2grid({
            name: 'technician',
            url: {
                get: 'jsonGenerator/get_admin_data.php',
                save: 'backend/save/save_admin_data.php',
                remove: 'backend/delete/delete_admin_records.php'
            },
            header: 'Technician Panel',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarAdd: true,
                toolbarDelete: true,
                toolbarSave: true
            },
            searches: [
                {field: 'recid', caption: 'Technician ID', type: 'int'},
                {field: 'email', caption: 'Technician Email', type: 'text'},
                {field: 'technicianFullName', caption: 'Technician Name', type: 'text'},
                {field: 'activitiesComplete', caption: 'Activities - Complete', type: 'int'},
                {field: 'activitiesInProgress', caption: 'Activities - In Progress', type: 'int'},
                {field: 'documentsCreated', caption: 'Documents - Created', type: 'int'},
                {field: 'documentsUpdated', caption: 'Documents - Updated', type: 'int'},
                {field: 'documentsDeleted', caption: 'Documents - Deleted', type: 'int'},
                {field: 'accessLevel', caption: 'Access Level', type: 'int'}
            ],
            multiSearch: true,
            columns: [
                {
                    field: 'recid',
                    caption: 'Technician ID',
                    size: '106px',
                    sortable: false,
                    resizable: true,
                    attr: 'align="center"',
                    hidden: true
                },
                {
                    field: 'email',
                    caption: 'Email',
                    size: '255px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"',
                    info: true
                },
                {
                    field: 'technicianFullName',
                    caption: 'Name',
                    size: '170px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'title',
                    caption: 'Title',
                    size: '150px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'avatar',
                    caption: 'Profile Picture',
                    size: '520px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'password',
                    caption: 'Password',
                    size: '170px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'text'},
                    attr: 'align="center"'
                },
                {
                    field: 'workingSince',
                    caption: 'Working Since',
                    size: '100px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'},
                    attr: 'align="center"'
                },
                {
                    field: 'activitiesComplete',
                    caption: 'Activities - Complete',
                    size: '140px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                },
                {
                    field: 'activitiesInProgress',
                    caption: 'Activities - In Progress',
                    size: '150px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                },
                {
                    field: 'documentsCreated',
                    caption: 'Documents - Created',
                    size: '140px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                },
                {
                    field: 'documentsUpdated',
                    caption: 'Documents - Updated',
                    size: '140px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                },
                {
                    field: 'documentsDeleted',
                    caption: 'Documents - Deleted',
                    size: '140px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                },
                {
                    field: 'accessLevel',
                    caption: 'Access Level',
                    size: '130px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'int'},
                    attr: 'align="center"'
                }
            ],
            onAdd: function (event) {
                w2ui.layout.content('main', w2ui.add_technician);
            },
            onSave: function (event) {
                //Get length of the records array
                let records_length = w2ui["technician"].records.length;
                let array_to_send = [];
                let technician_new_name = sessionStorage.getItem("userName");
                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                for (let i = 0; i < records_length; i++)
                {
                    if (typeof w2ui["technician"].records[i].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["technician"].records[i].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["technician"].records[i].w2ui.changes["technicianFullName"] !== 'undefined')
                            {
                                w2ui["technician"].postData['access_code'] = 'admin_save_tech';
                                array_to_send.push({technician_name: w2ui["technician"].records[i]["technicianFullName"], recid: w2ui["technician"].records[i].recid});

                                if (sessionStorage.getItem("userName") === w2ui["technician"].records[i]["technicianFullName"])
                                {
                                    sessionStorage.setItem("userName", w2ui["technician"].records[i].w2ui.changes["technicianFullName"]);
                                }
                            }
                        }
                    }
                }

                w2ui["technician"].postData['original_technician'] = array_to_send;

                event.onComplete = function () {
                    location.reload();
                };
            },
            onDelete: function (event) {
                if (event.force)
                {
                    w2ui["technician"].postData['table_name'] = "technician";
                    w2ui["technician"].postData['where_clause'] = "technicianID";
                }


            },
//        onClick: function (event) {
//            console.log(event);
//            console.log(w2ui["checklist_tasks"])
//        },
            postData: {
                data: {table_name: "technician", access_code: "normal", select_data: ["technicianID", "email", "technicianFullName", "title", "avatar", "password", "workingSince", "activitiesComplete", "activitiesInProgress", "documentsCreated", "documentsUpdated", "documentsDeleted", "accessLevel"]}
            },
            onError: function (event) {
                console.log(event);
            }
        });
    }

    $.ajax({
        type: 'POST',
        url: "backend/get_all_technicians.php",
        dataType: 'text',
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
            }


            let json = JSON.parse(data);
            let json_length = json.length;
            //console.log(json);
            for (let i = 0; i < json_length; i++)
            {
                dropdown_technicians.push({id: i + 1, text: json[i].title});

                technicians.push(json[i].title);
                activities_complete_color.push('rgba(75, 192, 192, 0.2)');
                activities_in_progress_color.push('rgba(54, 162, 235, 0.2)');
                upcoming_activities_color.push('rgba(255, 99, 132, 0.2)');
                activities_complete_border_color.push('rgba(75, 192, 192, 1)');
                activities_in_progress_border_color.push('rgba(54, 162, 235, 1)');
                upcoming_activities_border_color.push('rgba(255,99,132,1)');
            }

            get_activities_data();
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });

    function initializeSiteBarChart() {
        var myBarChart = new Chart(canvas, {
            type: 'horizontalBar',
            data: {
                labels: technicians,
                datasets: [
                    {
                        label: "Activities Complete",
                        data: activities_complete_data,
                        backgroundColor: activities_complete_color,
                        borderColor: activities_complete_border_color,
                        borderWidth: 1
                    },
                    {
                        label: "Activities In Progress",
                        data: activities_in_progress_data,
                        backgroundColor: activities_in_progress_color,
                        borderColor: activities_in_progress_border_color,
                        borderWidth: 1
                    },
                    {
                        label: "Activities Upcoming",
                        data: upcoming_activities_data,
                        backgroundColor: upcoming_activities_color,
                        borderColor: upcoming_activities_border_color,
                        borderWidth: 1
                    }


                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 0,
                        right: 15,
                        top: 10,
                        bottom: 5
                    }
                },
                scales: {
                    xAxes: [{
                            ticks: {
                                maxRotation: 90,
                                minRotation: 80,
                                precision: 0
                            }
                        }],
                    yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                }
            }
        });
    }

    var style = 'background-color: #F5F6F7; border: 1px solid silver; padding: 3px';
    //Administrator Panel
    var config = {
        layout: {
            name: 'layout',
            padding: 0,
            panels: [
                //{type: 'top', size: 32, content: '<div style="padding: 7px;">Top Panel</div>', style: 'border-bottom: 1px solid silver;'},
                {type: 'left', size: 220, resizable: true, minSize: 35},
                {type: 'main', minSize: 650}
            ]
        },
        sidebar: {
            name: 'sidebar',
            //flatButton: true,
            nodes: [
                {id: 'Checklist_Data', text: 'Checklist Data', icon: 'glyphicon glyphicon-list-alt', expanded: true, count: 8, nodes: [
                        {id: 'checklist', text: 'Checklists', icon: 'glyphicon glyphicon-list-alt'},
                        {id: 'checklist_categories_data', text: 'Categories Data', icon: 'glyphicon glyphicon-th-large', expanded: true, count: 2, nodes: [
                                {id: 'checklist_categories', text: 'Categories', icon: 'glyphicon glyphicon-tasks', selected: true},
                                {id: 'checklist_optional_categories', text: 'Optional Categories', icon: 'glyphicon glyphicon-tasks'}
                            ]},
                        {id: 'checklist_tasks_data', text: 'Tasks Data', icon: 'glyphicon glyphicon-th-large', count: 2, nodes: [
                                {id: 'checklist_tasks', text: 'Tasks', icon: 'glyphicon glyphicon-check'},
                                {id: 'checklist_optional_tasks', text: 'Optional Tasks', icon: 'glyphicon glyphicon-check'}
                            ]},
                        {id: 'checklist_tabs_data', text: 'Tabs Data', icon: 'glyphicon glyphicon-th-large', count: 2, nodes: [
                                {id: 'checklist_tabs', text: 'Tabs', icon: 'glyphicon glyphicon-align-justify'},
                                {id: 'checklist_optional_tabs', text: 'Optional Tabs', icon: 'glyphicon glyphicon-align-justify'}
                            ]},
                    ]},
                {id: 'Technician_Data', text: 'Technician Data', icon: 'glyphicon glyphicon-user', expanded: false, count: 1, nodes: [
                        {id: 'technician', text: 'Technicians', icon: 'glyphicon glyphicon-user'}
                    ]},
                {id: 'Site', text: 'Site Data', icon: 'glyphicon glyphicon-globe', expanded: false, count: 3, nodes: [
                        {id: 'all_sites', text: 'Sites', icon: 'glyphicon glyphicon-globe'},
                        {id: 'activity_type', text: 'Activity Type', icon: 'glyphicon glyphicon-book'},
                        {id: 'status', text: 'Status', icon: 'glyphicon glyphicon-flag'}
                    ]},
                {id: 'Event', text: 'Event Data', icon: 'glyphicon glyphicon-calendar', expanded: false, count: 3, nodes: [
                        {id: 'upcoming_sites', text: 'Upcoming Site Events', icon: 'glyphicon glyphicon-hourglass'},
                        {id: 'document_events', text: 'Document Events', icon: 'glyphicon glyphicon-file'},
                        {id: 'other_events', text: 'Other Events', icon: 'glyphicon glyphicon-option-horizontal'}
                    ]}
            ],
//            onFlat: function (event) {
//                $('#layout_layout_panel_left').css('width', (event.goFlat ? '35px' : '200px'));
//                w2ui.checklist_categories.resize();
//            },
            onClick: function (event) {
                switch (event.target) {
                    case 'checklist_categories':
                        w2ui.layout.content('main', w2ui.checklist_categories);
                        break;
                    case 'checklist_tasks':
                        w2ui.layout.content('main', w2ui.checklist_tasks);
                        if (typeof w2ui.checklist_tasks_form === "undefined")
                        {
                            create_form("Add Task", "checklist_tasks_form", [{name: 'taskName', type: 'text', required: true, html: {caption: 'Task Name', attr: 'size="40" maxlength="40" placeholder="Task Name"', span: 10}}, {name: 'categoryName', type: 'list', required: true, options: {items: category_names}, html: {caption: 'Category Name', attr: 'size="40" maxlength="40" placeholder="Category to which the task will belong to."', span: 10}}, {name: 'tabName', type: 'list', required: true, options: {items: tab_names}, html: {caption: 'Tab Name', attr: 'size="40" maxlength="40" placeholder="Tab to which the task will belong to."', span: 10}}], "checklistTasks", "add_tasks", "checklist_tasks", "2 count");

                            w2ui.checklist_tasks.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_tasks_form);
                            });
                        }

                        break;
                    case 'checklist_tabs':
                        if (typeof w2ui.checklist_tabs === "undefined")
                        {
                            createGenericGrid("checklist_tabs", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_admin_records.php', "Tab Panel", {table_name: "checklistTabs", access_code: "normal", select_data: ["tabID", "tabName", "tabs_identifier"], columns: [{field: 'recid', caption: 'Tab ID', size: '34%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'tabName', caption: 'Tab Name', size: '34%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'tabs_identifier', caption: 'Tab Identifier', size: '34%', sortable: false, resizable: true, attr: 'align="center"'}]}, true, true, true);
                            w2ui['checklist_tabs'].searches.push({field: 'tabID', caption: 'Tab ID', type: 'int'});
                            w2ui['checklist_tabs'].searches.push({field: 'tabName', caption: 'Tab Name', type: 'text'});
                            w2ui['checklist_tabs'].searches.push({field: 'tabs_identifier', caption: 'Tab Identifier', type: 'text'});

                            w2ui.checklist_tabs.on('save', function (event) {
                                //Get length of the records array
                                let records_length = w2ui["checklist_tabs"].records.length;
                                let array_to_send = [];
                                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                                for (let i = 0; i < records_length; i++)
                                {
                                    if (typeof w2ui["checklist_tabs"].records[i].w2ui !== 'undefined')
                                    {
                                        if (typeof w2ui["checklist_tabs"].records[i].w2ui.changes !== 'undefined')
                                        {
                                            if (typeof w2ui["checklist_tabs"].records[i].w2ui.changes["tabName"] !== 'undefined')
                                            {
                                                array_to_send.push({tabName: w2ui["checklist_tabs"].records[i]["tabName"], recid: w2ui["checklist_tabs"].records[i].tabID});
                                            }
                                        }
                                    }
                                }

                                w2ui["checklist_tabs"].postData['original_tab_names'] = array_to_send;
                            });

                            create_form("Add Tab", "checklist_tabs_form", [{name: 'tabName', type: 'text', required: true, html: {caption: 'Tab Name', attr: 'size="40" maxlength="40" placeholder="Tab Name"', span: 10}}, {name: 'tabs_identifier', type: 'text', required: true, html: {caption: 'Tab Identifier', attr: 'size="40" maxlength="40" placeholder="Tab Identifier"', span: 10}}], "checklistTabs", "add_tabs", "checklist_tabs", "1 count");

                            w2ui.checklist_tabs.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_tabs_form);
                            });
                        }

                        w2ui.layout.content('main', w2ui.checklist_tabs);
                        break;
                    case 'checklist_optional_categories':
                        if (typeof w2ui.checklist_optional_categories === "undefined")
                        {
                            createGenericGrid("checklist_optional_categories", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_admin_records.php', "Optional Category Panel", {table_name: "checklistOptionalCategories", access_code: "admin_save_optional_categories", select_data: ["optionalCategoryID", "optionalCategoryName", "optional_categories_identifier"], columns: [{field: 'recid', caption: 'Optional Category ID', size: '34%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'optionalCategoryName', caption: 'Optional Category Name', size: '34%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'optional_categories_identifier', caption: 'Optional Category Identifier', size: '34%', sortable: false, resizable: true, attr: 'align="center"'}]}, true, true, true);
                            w2ui['checklist_optional_categories'].searches.push({field: 'optionalCategoryID', caption: 'Optional Category ID', type: 'int'});
                            w2ui['checklist_optional_categories'].searches.push({field: 'optionalCategoryName', caption: 'Optional Category Name', type: 'text'});
                            w2ui['checklist_optional_categories'].searches.push({field: 'optional_categories_identifier', caption: 'Optional Category Identifier', type: 'text'});

                            w2ui.checklist_optional_categories.on('save', function (event) {
                                //Get length of the records array
                                let records_length = w2ui["checklist_optional_categories"].records.length;
                                let array_to_send = [];
                                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                                for (let i = 0; i < records_length; i++)
                                {
                                    if (typeof w2ui["checklist_optional_categories"].records[i].w2ui !== 'undefined')
                                    {
                                        if (typeof w2ui["checklist_optional_categories"].records[i].w2ui.changes !== 'undefined')
                                        {
                                            if (typeof w2ui["checklist_optional_categories"].records[i].w2ui.changes["optionalCategoryName"] !== 'undefined')
                                            {
                                                array_to_send.push({optionalCategoryName: w2ui["checklist_optional_categories"].records[i]["optionalCategoryName"], recid: w2ui["checklist_optional_categories"].records[i].optionalCategoryID});
                                            }
                                        }
                                    }
                                }

                                w2ui["checklist_optional_categories"].postData['original_category_names'] = array_to_send;
                            });

                            create_form("Add Optional Category", "checklist_optional_categories_form", [{name: 'optionalCategoryName', type: 'text', required: true, html: {caption: 'Optional Category Name', attr: 'size="40" maxlength="40" placeholder="Optional Category Name"', span: 10}}, {name: 'optional_categories_identifier', type: 'text', required: true, html: {caption: 'Optional Category Identifier', attr: 'size="40" maxlength="40" placeholder="Optional Category Identifier"', span: 10}}], "checklistOptionalCategories", "add_optional_categories", "checklist_optional_categories", "1 count");

                            w2ui.checklist_optional_categories.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_optional_categories_form);
                            });
                        }

                        w2ui.layout.content('main', w2ui.checklist_optional_categories);
                        break;
                    case 'checklist_optional_tasks':
                        w2ui.layout.content('main', w2ui.checklist_optional_tasks);
                        if (typeof w2ui.checklist_optional_tasks_form === "undefined")
                        {
                            create_form("Add Optional Task", "checklist_optional_tasks_form", [{name: 'optionalTaskName', type: 'text', required: true, html: {caption: 'Optional Task Name', attr: 'size="40" maxlength="40" placeholder="Optional Task Name"', span: 10}}, {name: 'optionalCategoryName', type: 'list', required: true, options: {items: optional_category_names}, html: {caption: 'Optional Category Name', attr: 'size="40" maxlength="40" placeholder="Category to which the task will belong to."', span: 10}}, {name: 'optionalTabName', type: 'list', required: true, options: {items: optional_tab_names}, html: {caption: 'Optional Tab Name', attr: 'size="40" maxlength="40" placeholder="Tab to which the task will belong to."', span: 10}}], "checklistOptionalTasks", "add_optional_tasks", "checklist_optional_tasks", "2 count");

                            w2ui.checklist_optional_tasks.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_optional_tasks_form);
                            });
                        }

                        break;
                    case 'checklist_optional_tabs':
                        if (typeof w2ui.checklist_optional_tabs === "undefined")
                        {
                            createGenericGrid("checklist_optional_tabs", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_admin_records.php', "Optional Tab Panel", {table_name: "checklistOptionalTabs", access_code: "normal", select_data: ["optionalTabID", "optionalTabName", "optional_tabs_identifier"], columns: [{field: 'recid', caption: 'Optional Tab ID', size: '34%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'optionalTabName', caption: 'Optional Tab Name', size: '34%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'optional_tabs_identifier', caption: 'Optional Tab Identifier', size: '34%', sortable: false, resizable: true, attr: 'align="center"'}]}, true, true, true);
                            w2ui['checklist_optional_tabs'].searches.push({field: 'optionalTabID', caption: 'Optional Tab ID', type: 'int'});
                            w2ui['checklist_optional_tabs'].searches.push({field: 'optionalTabName', caption: 'Optional Tab Name', type: 'text'});
                            w2ui['checklist_optional_tabs'].searches.push({field: 'optional_tabs_identifier', caption: 'Optional Tab Identifier', type: 'text'});

                            create_form("Add Optional Tab", "checklist_optional_tabs_form", [{name: 'optionalTabName', type: 'text', required: true, html: {caption: 'Optional Tab Name', attr: 'size="40" maxlength="40" placeholder="Optional Tab Name"', span: 10}}, {name: 'optionalCategoryName', type: 'list', required: true, options: {items: optional_category_names}, html: {caption: 'Optional Category Name', attr: 'size="40" maxlength="40" placeholder="Optional Category Name"', span: 10}}, {name: 'optional_tabs_identifier', type: 'text', required: true, html: {caption: 'Optional Tab Identifier', attr: 'size="40" maxlength="40" placeholder="Optional Tab Identifier"', span: 10}}], "checklistOptionalTabs", "add_optional_tabs", "checklist_optional_tabs", "2 count");

                            w2ui.checklist_optional_tabs.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_optional_tabs_form);
                            });
                        }

                        w2ui.layout.content('main', w2ui.checklist_optional_tabs);
                        break;
                    case 'technician':
                        w2ui['technician'].load('jsonGenerator/get_admin_data.php');
                        w2ui.layout.content('main', w2ui.technician);
                        break;
                    case 'status':
                        if (typeof w2ui.status === "undefined")
                        {
                            createGenericGrid("status", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_admin_records.php', "Status Panel", {table_name: "status", access_code: "normal", select_data: ["statusID", "status"], columns: [{field: 'recid', caption: 'Status ID', size: '34%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'status', caption: 'Status', size: '34%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}]}, false, false, false);
                            w2ui['status'].searches.push({field: 'statusID', caption: 'Status ID', type: 'int'});
                            w2ui['status'].searches.push({field: 'status', caption: 'Status', type: 'text'});
                        }

                        w2ui['status'].load('jsonGenerator/get_admin_data.php');
                        w2ui.layout.content('main', w2ui.status);
                        break;
                    case 'activity_type':
                        if (typeof w2ui.siteActivityType === "undefined")
                        {
                            createGenericGrid("siteActivityType", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_admin_records.php', "Activity Type Panel", {table_name: "siteActivityType", access_code: "normal", select_data: ["activityID", "activityType"], columns: [{field: 'recid', caption: 'Activity ID', size: '34%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'activityType', caption: 'Activity', size: '34%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}]}, true, true, true);
                            w2ui['siteActivityType'].searches.push({field: 'activityID', caption: 'Activity ID', type: 'int'});
                            w2ui['siteActivityType'].searches.push({field: 'activityType', caption: 'Activity', type: 'text'});

                            create_form("Add Activity Type", "checklist_activity_type_form", [{name: 'activityType', type: 'text', required: true, html: {caption: 'Activity Type', attr: 'size="40" maxlength="40" placeholder="Activity Type"', span: 10}}], "siteActivityType", "add_activity_type", "siteActivityType", "1 count");

                            w2ui.siteActivityType.on('add', function (event) {
                                w2ui.layout.content('main', w2ui.checklist_activity_type_form);
                            });
                        }

                        w2ui['siteActivityType'].load('jsonGenerator/get_admin_data.php');
                        w2ui.layout.content('main', w2ui.siteActivityType);
                        break;
                    case 'all_sites':
                        if (typeof w2ui.all_sites === "undefined")
                        {
                            createGenericGrid("all_sites", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_site.php', "Site Panel", {table_name: "site", access_code: "normal", select_data: ["siteCode", "siteCountry", "siteCounty", "siteTown", "siteTech", "siteType", "completedOn"], columns: [{field: 'recid', caption: 'Site Code', size: '90px', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'siteCountry', caption: 'Site Country', size: '140px', sortable: false, editable: false, resizable: true, attr: 'align="center"'}, {field: 'siteCounty', caption: 'Site County', size: '120px', sortable: false, editable: false, resizable: true, attr: 'align="center"'}, {field: 'siteTown', caption: 'Site Town', size: '120px', sortable: false, editable: false, resizable: true, attr: 'align="center"'}, {field: 'siteTech', caption: 'Site Technician', size: '150px', sortable: false, editable: false, resizable: true, attr: 'align="center"'}, {field: 'siteType', caption: 'Site Type', size: '90px', sortable: false, resizable: true, attr: 'align="center"'}, {field: 'completedOn', caption: 'Completed On', size: '140px', sortable: false, editable: false, resizable: true, attr: 'align="center"'}]}, true, false, false);
                            w2ui['all_sites'].searches.push({field: 'siteCode', caption: 'Site Code', type: 'int'});
                            w2ui['all_sites'].searches.push({field: 'siteCountry', caption: 'Site Country', type: 'text'});
                            w2ui['all_sites'].searches.push({field: 'siteCounty', caption: 'Site County', type: 'text'});
                            w2ui['all_sites'].searches.push({field: 'siteTown', caption: 'Site Town', type: 'text'});
                            w2ui['all_sites'].searches.push({field: 'siteTech', caption: 'Site Technician', type: 'text'});
                            w2ui['all_sites'].searches.push({field: 'siteType', caption: 'Site Type', type: 'text'});
                            w2ui['all_sites'].searches.push({field: 'completedOn', caption: 'Completed On', type: 'date'});
                        }

                        w2ui.layout.content('main', w2ui.all_sites);
                        break;
                    case 'checklist':
                        if (typeof w2ui.checklist === "undefined")
                        {
                            createGenericGrid("checklist", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_checklist.php', "Checklist Panel", {table_name: "checklist", access_code: "normal", select_data: ["checklistID", "siteCode", "checklistHeader", "technician"], columns: [{field: 'recid', caption: 'Checklist ID', size: '25%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'siteCode', caption: 'Site Code', size: '25%', sortable: false, editable: false, resizable: true, attr: 'align="center"'}, {field: 'checklistHeader', caption: 'Checklist Description', size: '25%', sortable: false, resizable: true, attr: 'align="center"'}, {field: 'technician', caption: 'Technician', size: '25%', sortable: false, resizable: true, attr: 'align="center"'}]}, true, false, false);
                            w2ui['checklist'].searches.push({field: 'checklistID', caption: 'Checklist ID', type: 'int'});
                            w2ui['checklist'].searches.push({field: 'siteCode', caption: 'Site Code', type: 'text'});
                            w2ui['checklist'].searches.push({field: 'checklistHeader', caption: 'Checklist Description', type: 'text'});
                            w2ui['checklist'].searches.push({field: 'technician', caption: 'Technician', type: 'text'});
                        }

                        w2ui.layout.content('main', w2ui.checklist);
                        break;
                    case 'upcoming_sites':
                        if (typeof w2ui.upcoming_sites === "undefined")
                        {
                            createGenericGrid("upcoming_sites", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_event.php', "Upcoming Site Events Panel", {table_name: "upcomingSiteEvents", access_code: "normal", select_data: ["upcomingSiteEventID", "siteCode", "event", "technician", "event_country", "event_county", "event_town", "date", "time", "expectedGoLiveDate"], columns: [{field: 'recid', caption: 'Document Event ID', size: '123px', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'siteCode', caption: 'Site Code', size: '80px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'event', caption: 'Event', size: '170px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'technician', caption: 'Technician', size: '150px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'event_country', caption: 'Event Country', size: '120px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'event_county', caption: 'Event County', size: '120px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'event_town', caption: 'Technician', size: '120px', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'date', caption: 'Date Created', size: '90px', sortable: false, editable: {type: 'date', format: 'yyyy-mm-dd'}, resizable: true, attr: 'align="center"'}, {field: 'time', caption: 'Time Created', size: '90px', sortable: false, editable: {type: 'time', format: 'h24'}, resizable: true, attr: 'align="center"'}, {field: 'expectedGoLiveDate', caption: 'Expected Go Live Date', size: '140px', sortable: false, editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'}, resizable: true, attr: 'align="center"'}]}, true, true, false);
                            w2ui['upcoming_sites'].searches.push({field: 'upcomingSiteEventID', caption: 'Upcoming Site Event ID', type: 'int'});
                            w2ui['upcoming_sites'].searches.push({field: 'siteCode', caption: 'Site Code', type: 'text'});
                            w2ui['upcoming_sites'].searches.push({field: 'event', caption: 'Event', type: 'text'});
                            w2ui['upcoming_sites'].searches.push({field: 'technician', caption: 'Technician', type: 'text'});
                            w2ui['upcoming_sites'].searches.push({field: 'event_country', caption: 'Event Country', type: 'int'});
                            w2ui['upcoming_sites'].searches.push({field: 'event_county', caption: 'Event County', type: 'text'});
                            w2ui['upcoming_sites'].searches.push({field: 'event_town', caption: 'Event Town', type: 'text'});
                            w2ui['upcoming_sites'].searches.push({field: 'date', caption: 'Date Created', type: 'date'});
                            w2ui['upcoming_sites'].searches.push({field: 'time', caption: 'Time Created', type: 'time'});
                            w2ui['upcoming_sites'].searches.push({field: 'expectedGoLiveDate', caption: 'Expected Go Live Date', type: 'datetime'});
                        }

                        w2ui.layout.content('main', w2ui.upcoming_sites);
                        break;
                    case 'document_events':
                        if (typeof w2ui.document_events === "undefined")
                        {
                            createGenericGrid("document_events", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_event.php', "Other Events Panel", {table_name: "documentEvents", access_code: "normal", select_data: ["documentEventID", "event", "date", "time", "technician"], columns: [{field: 'recid', caption: 'Document Event ID', size: '20%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'event', caption: 'Event', size: '20%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'date', caption: 'Date Created', size: '20%', sortable: false, editable: {type: 'date', format: 'yyyy/mm/dd'}, resizable: true, attr: 'align="center"'}, {field: 'time', caption: 'Time Created', size: '20%', sortable: false, editable: {type: 'time', format: 'h24'}, resizable: true, attr: 'align="center"'}, {field: 'technician', caption: 'Technician', size: '20%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}]}, true, false, false);
                            w2ui['document_events'].searches.push({field: 'documentEventID', caption: 'Document Event ID', type: 'int'});
                            w2ui['document_events'].searches.push({field: 'event', caption: 'Event', type: 'text'});
                            w2ui['document_events'].searches.push({field: 'date', caption: 'Date Created', type: 'date'});
                            w2ui['document_events'].searches.push({field: 'time', caption: 'Time Created', type: 'time'});
                            w2ui['document_events'].searches.push({field: 'technician', caption: 'Technician', type: 'text'});
                        }

                        w2ui.layout.content('main', w2ui.document_events);
                        break;
                    case 'other_events':
                        if (typeof w2ui.other_events === "undefined")
                        {
                            createGenericGrid("other_events", "jsonGenerator/get_admin_data.php", "backend/save/save_admin_data.php", 'backend/delete/delete_event.php', "Other Events Panel", {table_name: "other_events", access_code: "normal", select_data: ["event_id", "event", "start", "end", "technician"], columns: [{field: 'recid', caption: 'Event ID', size: '20%', sortable: false, resizable: true, attr: 'align="center"', info: true}, {field: 'event', caption: 'Event', size: '20%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}, {field: 'start', caption: 'Date Started', size: '20%', sortable: false, editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'}, resizable: true, attr: 'align="center"'}, {field: 'end', caption: 'Date Ended', size: '20%', sortable: false, editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'}, resizable: true, attr: 'align="center"'}, {field: 'technician', caption: 'Technician', size: '20%', sortable: false, editable: {type: 'text'}, resizable: true, attr: 'align="center"'}]}, true, false, false);
                            w2ui['other_events'].searches.push({field: 'event_id', caption: 'Event ID', type: 'int'});
                            w2ui['other_events'].searches.push({field: 'event', caption: 'Event', type: 'text'});
                            w2ui['other_events'].searches.push({field: 'start', caption: 'Date Started', type: 'datetime'});
                            w2ui['other_events'].searches.push({field: 'end', caption: 'Date Ended', type: 'datetime'});
                            w2ui['other_events'].searches.push({field: 'technician', caption: 'Technician', type: 'text'});
                        }

                        w2ui.layout.content('main', w2ui.other_events);
                        break;
                }
            }
        },
        form: {
            header: 'Add Technician',
            name: 'add_technician',
            fields: [
                {name: 'technicianFullName', type: 'text', required: true, html: {caption: 'Technician Name', attr: 'size="40" maxlength="40" padding-top="10px" placeholder="Technician Full Name"', span: 10}},
                {name: 'email', type: 'email', required: true, html: {caption: 'Email', attr: 'size="40" placeholder="Technicians Prometric email"', span: 10}},
                {name: 'title', type: 'text', required: true, html: {caption: 'Title', attr: 'size="40" maxlength="40" placeholder="Technician Title (e.g. Site Technician)"', span: 10}},
                {name: 'workingSince', type: 'date', required: true, options: {format: 'yyyy-mm-dd'}, html: {caption: 'Start Date', attr: 'size="40" placeholder="Date when technician began working"', span: 10}}
            ],
            actions: {
                Save: function () {
                    var errors = this.validate();
                    if (errors.length > 0)
                    {
                        return;
                    }
                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'none'});

                    if (this.recid === 0) {
                        let records = w2ui.technician.records;
                        let records_length = w2ui.technician.records.length;
                        let duplicate_found = false;

                        let new_record = this;

                        for (let i = 0; i < records_length; i++)
                        {
                            if (records[i].technicianFullName === this.record.technicianFullName || records[i].email === this.record.email)
                            {
                                duplicate_found = true;
                                let alert_options = {
                                        msg: 'The Technician Name and Email you have chosen already exist, click "Try Again" to input different values or "Cancel" to go back to the grid.',
                                        btn_yes: {
                                            text: 'Try Again', // text for yes button (or yes_text)
                                            class: 'w2ui-btn w2ui-btn-green', // class for yes button (or yes_class)
                                            style: '', // style for yes button (or yes_style)
                                            callBack: null     // callBack for yes button (or yes_callBack)
                                        },
                                        btn_no: {
                                            text: 'Cancel', // text for no button (or no_text)
                                            class: 'w2ui-btn w2ui-btn-red', // class for no button (or no_class)
                                            style: '', // style for no button (or no_style)
                                            callBack: null     // callBack for no button (or no_callBack)
                                        },
                                        callBack: null     // common callBack
                                    };
                                    
                                w2confirm(alert_options).yes(function () {
                                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                    return;
                                }).no(function () {
                                    new_record.clear();
                                    w2ui.layout.content('main', w2ui.technician);
                                });

                                break;
                            }
                        }

                        if (!duplicate_found)
                        {
                            this.record.recid = records[records_length - 1].recid + 1;

                            $.ajax({
                                type: 'POST',
                                url: "backend/save/new_admin_records.php",
                                dataType: 'text',
                                data: {record: this.record, table_name: "technician"},
                                success: function (data) {
                                    //console.log(data);

                                    if (data === '1')
                                    {
                                        new_record.clear();
                                        w2ui.layout.content('main', w2ui.technician);
                                    } else
                                    {
                                        w2alert(data)
                                                .ok(function () {
                                                    new_record.clear();
                                                    w2ui.layout.content('main', w2ui.technician);
                                                });
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxErrorW2ui(jqXHR, textStatus, errorThrown);
                                }
                            });
                        } else
                        {
                            $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                        }
                    } else {
                        w2ui.technician.set(this.recid, this.record);
                        w2ui.technician.selectNone();
                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                    }
                },
                Cancel: function () {
                    w2ui.layout.content('main', w2ui.technician);
                }

            }
        },
        grid: {
            name: 'checklist_categories',
            url: {
                get: 'jsonGenerator/get_admin_data.php',
                save: 'backend/save/save_admin_data.php',
                remove: 'backend/delete/delete_admin_records.php'
            },
            style: 'border: 0px; border-left: 1px solid silver',
            header: 'Category Panel',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarAdd: true,
                toolbarDelete: true,
                toolbarSave: true
            },
            searches: [
                {field: 'recid', caption: 'Category ID', type: 'int'},
                {field: 'categoryName', caption: 'Category Name', type: 'text'},
                {field: 'categories_identifier', caption: 'Category Identifier', type: 'text'}
            ],
            columns: [
                {field: 'recid', caption: 'Category ID', size: '50%', attr: 'align="center"', info: true},
                {field: 'categoryName', editable: {type: 'text'}, caption: 'Category Name', size: '50%', attr: 'align="center"'},
                {field: 'categories_identifier', caption: 'Category Identifier', size: '50%', attr: 'align="center"'}
            ],
            postData: {
                data: {table_name: "checklistCategories", access_code: "normal", select_data: ["categoryID", "categoryName", "categories_identifier"]}
            },
            onAdd: function (event) {
                if (typeof w2ui.checklist_categories_form === "undefined")
                {
                    create_form("Add Category", "checklist_categories_form", [{name: 'categoryName', type: 'text', required: true, html: {caption: 'Category Name', attr: 'size="40" maxlength="40" placeholder="Category Name"', span: 10}}, {name: 'categories_identifier', type: 'text', required: true, html: {caption: 'Category Identifier', attr: 'size="40" maxlength="40" placeholder="Category Identifier"', span: 10}}], "checklistCategories", "add_category", "checklist_categories", "1 count");
                }

                event.onComplete = function () {
                    w2ui.layout.content('main', w2ui.checklist_categories_form);
                };
            },
            onSave: function (event) {
                //Get length of the records array
                let records_length = w2ui["checklist_categories"].records.length;
                let array_to_send = [];
                //Loop through the records array checking if a record has been changed and if it has add the original name of the technician to an array along with the recid to be used to update the database
                for (let i = 0; i < records_length; i++)
                {
                    if (typeof w2ui["checklist_categories"].records[i].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["checklist_categories"].records[i].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["checklist_categories"].records[i].w2ui.changes["categoryName"] !== 'undefined')
                            {
                                w2ui["checklist_categories"].postData['access_code'] = 'admin_save_categories';
                                array_to_send.push({categoryName: w2ui["checklist_categories"].records[i]["categoryName"], recid: w2ui["checklist_categories"].records[i].recid});
                            }
                        }
                    }
                }

                w2ui["checklist_categories"].postData['original_categoy_names'] = array_to_send;

//                event.onComplete = function () {
//                    location.reload();
//                };
            },
            onDelete: function (event) {
                if (event.force)
                {
                    w2ui["checklist_categories"].postData['table_name'] = "checklistCategories";
                    w2ui["checklist_categories"].postData['where_clause'] = "categoryID";
                }
            },
            onError: function (event) {
                console.log(event);
            }
        }
    };
    $(function () {
        // initialization in memory
        $().w2layout(config.layout);
        $().w2sidebar(config.sidebar);
        $().w2grid(config.grid);
        $().w2form(config.form);
    });
    function openPopup() {
        w2popup.open({
            title: 'Administrator Panel',
            width: 920,
            height: 600,
            showMax: true,
            body: '<div id="main" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px;"></div>',
            onOpen: function (event) {
                event.onComplete = function () {
                    w2ui['sidebar'].select('checklist_categories');
                    $('#w2ui-popup #main').w2render('layout');
                    w2ui.layout.content('left', w2ui.sidebar);
                    w2ui.layout.content('main', w2ui.checklist_categories);
                };
            },
            onToggle: function (event) {
                event.onComplete = function () {
                    w2ui.layout.resize();
                };
            }
        });
    }

    $("#btn").click(function () {
        openPopup();
    });
});