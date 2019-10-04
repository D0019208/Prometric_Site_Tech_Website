/* global w2alert, w2ui, w2confirm, w2popup, Swal */

var documents;
var config;
var selected_document_id;
var current_directory;
var current_file_name;
var form_to_display;

var config_form = {
    layout: {
        name: 'layout',
        padding: 4,
        panels: [
            {type: 'main', minSize: 300}
        ]
    },
    form: {
        header: 'Edit Record',
        name: 'form',
        fields: [
            {name: 'recid', type: 'text', html: {caption: 'ID', attr: 'size="10" readonly'}},
            {name: 'fname', type: 'text', required: true, html: {caption: 'First Name', attr: 'size="40" maxlength="40"'}},
            {name: 'lname', type: 'text', required: true, html: {caption: 'Last Name', attr: 'size="40" maxlength="40"'}},
            {name: 'email', type: 'email', html: {caption: 'Email', attr: 'size="30"'}},
            {name: 'sdate', type: 'date', html: {caption: 'Date', attr: 'size="10"'}}
        ],
        actions: {
            Reset: function () {
                this.clear();
            },
            Save: function () {
                var errors = this.validate();
                if (errors.length > 0)
                    return;
                if (this.recid === 0) {
                    w2ui.grid.add($.extend(true, {recid: w2ui.grid.records.length + 1}, this.record));
                    w2ui.grid.selectNone();
                    this.clear();
                } else {
                    w2ui.grid.set(this.recid, this.record);
                    w2ui.grid.selectNone();
                    this.clear();
                }
            }
        }
    }
};

function create_form(header, name, fields, access_code)
{
    $().w2form({
        header: header,
        name: name,
        fields: fields,
        actions: {
            Save: function () {
                var errors = this.validate();
                if (errors.length > 0)
                    return;

                //let records = w2ui[grid_name].records;
                // let records_length = w2ui[grid_name].records.length;
                let duplicate_found = false;

                if (access_code === "upload_document")
                {
                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'none'});

                    if (this.recid === 0) {
                        let new_document = this;
                        let new_document_name = this.record.uploadDocument[0].name.replace('.pdf', '');
                        let new_category = this.record.documentCategory.id;

//                        console.log(new_document);
//                        console.log(documents);
//                        console.log(new_document_name);
//                        console.log(new_category);
//                        console.log(new_document.record);

                        for (let i = 0; i < documents.length; i++)
                        {
                            if (documents[i].id === new_category)
                            {
                                if (documents[i].nodes !== null)
                                {
                                    for (let j = 0; j < documents[i].nodes.length; j++)
                                    {
                                        if (documents[i].nodes[j].text === new_document_name)
                                        {
                                            duplicate_found = true;
                                            w2alert('The document, "' + new_document_name + '.pdf" already exists under the category of "' + this.record.documentCategory.text + '". Please try again.').ok(new_document.clear());
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        if (!duplicate_found)
                        {
//                            this.record.recid = records[records_length - 1].recid + 1;


                            let pdfData = new FormData();
                            pdfData.append('pdf', new_document.record.uploadDocument[0].file);
                            pdfData.append('pdf_name_extension', new_document.record.uploadDocument[0].name);
                            pdfData.append('pdf_name_short', new_document_name);
                            pdfData.append('categoryID', new_document.record.documentCategory.categoryID);
                            pdfData.append('directory', new_document.record.documentCategory.text);
                            pdfData.append('technician', sessionStorage.getItem('userName'));

                            $.ajax({
                                type: 'POST',
                                url: "backend/upload/upload_new_document.php",
                                processData: false,
                                contentType: false,
                                data: pdfData,
                                success: function (data) {
                                    //console.log(data);

                                    if (data === '1')
                                    {
                                        new_document.clear();
                                        location.reload();
                                    } else
                                    {
                                        w2alert(data)
                                                .ok(function () {
                                                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                                    new_document.clear();
                                                });
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxErrorW2ui(jqXHR, textStatus, errorThrown);
                                }
                            });
                        } else {
                            $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                            new_document.clear();
                        }
                    } else
                    {
                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                    }
                } else if (access_code === "add_category")
                {
                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'none'});

                    if (this.recid === 0) {
                        let new_category = this;
                        let new_category_name = this.record.new_category;
                        //console.log(this);

                        for (let i = 0; i < documents.length; i++)
                        {
                            if (new_category_name === documents[i].text)
                            {
                                duplicate_found = true;
                                w2alert('The category, "' + new_category_name + '" already exists. Please try again.').ok(new_category.clear());
                                break;
                            }
                        }

                        if (!duplicate_found)
                        {
                            $.ajax({
                                type: 'POST',
                                url: "backend/add_delete_category.php",
                                dataType: 'text',
                                data: {category: new_category_name, access_code: "add_category"},
                                success: function (data) {
                                    if (data === '1')
                                    {
                                        new_category.clear();
                                        location.reload();
                                    } else
                                    {
                                        w2alert(data)
                                                .ok(function () {
                                                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                                    new_category.clear();
                                                });
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                    onAjaxErrorW2ui(jqXHR, textStatus, errorThrown);
                                }
                            });
                        } else {
                            $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                            new_category.clear();
                        }
                    } else
                    {
                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                    }
                } else if (access_code === "delete_category")
                {
                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'none'});

                    if (this.recid === 0) {
                        let category_to_delete = this;
                        let category_to_delete_name = category_to_delete.record.category_to_delete.text;
                        let category_to_delete_id = category_to_delete.record.category_to_delete.categoryID;

                        let alert_options = {
                            msg: 'Are you sure you want to delete the ' + category_to_delete_name + ' directory? This cannot be undone!', 
                            btn_yes: {
                                text: 'Yes', // text for yes button (or yes_text)
                                class: 'w2ui-btn w2ui-btn-red', // class for yes button (or yes_class)
                                style: '', // style for yes button (or yes_style)
                                callBack: null     // callBack for yes button (or yes_callBack)
                            },
                            btn_no: {
                                text: 'No', // text for no button (or no_text)
                                class: '', // class for no button (or no_class)
                                style: '', // style for no button (or no_style)
                                callBack: null     // callBack for no button (or no_callBack)
                            },
                            callBack: null     // common callBack
                        };

                        w2confirm(alert_options)
                                .yes(function () {
                                    $.ajax({
                                        type: 'POST',
                                        url: "backend/add_delete_category.php",
                                        dataType: 'text',
                                        data: {category: category_to_delete_name, category_id: category_to_delete_id, access_code: "delete_category"},
                                        success: function (data) {
                                            if (data === '1')
                                            {
                                                category_to_delete.clear();
                                                location.reload();
                                            } else
                                            {
                                                w2alert(data)
                                                        .ok(function () {
                                                            $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                                            category_to_delete.clear();
                                                        });
                                            }

                                        },
                                        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                                            onAjaxErrorW2ui(jqXHR, textStatus, errorThrown);
                                        }
                                    });
                                })
                                .no(function () {
                                    $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                                });
                    } else
                    {
                        $(".w2ui-form .w2ui-buttons").css({'pointer-events': 'all'});
                    }
                }
            },
            Cancel: function (event) {
                let new_document = this;
                new_document.clear();
                w2popup.close();
            }
        },
        onRender: function (event) {
            event.onComplete = function () {
                console.log(this);

                let button = this.box.children[0].children[4].children[0].outerHTML;

                if (access_code === "upload_document")
                {
                    this.box.children[0].children[4].children[0].className = "w2ui-btn w2ui-btn-green";
                    this.box.children[0].children[4].children[0].innerText = "Upload";
                } else if (access_code === "add_category")
                {
                    this.box.children[0].children[4].children[0].className = "w2ui-btn w2ui-btn-green";
                    this.box.children[0].children[4].children[0].innerText = "Add";
                } else if (access_code === "delete_category")
                {
                    this.box.children[0].children[4].children[0].className = "w2ui-btn w2ui-btn-red";
                    this.box.children[0].children[4].children[0].innerText = "Delete";
                }
//                this.box.children[0].children[4].children[0].outerHTML = button.replace('class="w2ui-btn w2ui-btn-blue"', 'class="w2ui-btn w2ui-btn-red"');
//                this.box.children[0].children[4].children[0].outerHTML = button.replace('Save', 'Delete');
            };

        }
    });
}
$(function () {
    // initialization in memory
    $().w2layout(config_form.layout);
    $().w2form(config_form.form);
});

function openPopup() {
    w2popup.open({
        title: 'Documents',
        width: 600,
        height: 370,
        showMax: true,
        body: '<div id="main" style="position: absolute; left: 5px; top: 5px; right: 5px; bottom: 5px;"></div>',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #main').w2render('layout');
                w2ui.layout.content('main', w2ui[form_to_display]);
            };
        },
        onToggle: function (event) {
            event.onComplete = function () {
                w2ui.layout.resize();
            };
        }
    });
}



function upload_document(input, url)
{
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            //console.log(input.files[0]);
            let pdf = input.files[0];

            let pdfData = new FormData();
            pdfData.append('pdf', pdf);
            pdfData.append('technician', sessionStorage.getItem("userName"));
            pdfData.append('document_id', selected_document_id);
            pdfData.append('current_directory', current_directory);
            pdfData.append('current_file_name', current_file_name);

            $.ajax({
                url: url,
                data: pdfData,
                type: "POST",
                processData: false,
                contentType: false,
                success: function (data) {
                    //console.log(data);

                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("<b>SQLSTATE</b>:"))
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
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // if error occured  
                    onAjaxError(jqXHR, textStatus, errorThrown);
                }
            });
        };
        reader.readAsDataURL(input.files[0]);
    } else
    {
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'text',
            data: {document_id: selected_document_id, technician: sessionStorage.getItem("userName"), current_directory: current_directory, current_file_name: current_file_name},
            success: function (data) {
                //console.log(data);
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
                    location.reload();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    }
}

$.ajax({
    type: 'POST',
    url: "backend/get_all_documents.php",
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

        documents = JSON.parse(data);

        config = {
            layout: {
                name: 'document_layout',
                padding: 0,
                panels: [
                    {type: 'left', size: 225, resizable: true, minSize: 225},
                    {type: 'main', overflow: 'hidden',
                        style: 'background-color: white; border: 1px solid silver; border-top: 0px; padding: 10px;',
                        tabs: {
                            active: 'introduction',
                            tabs: [{id: 'introduction', caption: 'Introduction Tab'}],
                            onClick: function (event) {
                                if (event.target === 'introduction')
                                {
                                    w2ui.document_layout.html('main', '<div><h1 style="text-align: center"><b>Documents Panel</b></h1><p style="font-size: 14px;">You can use the buttons below to edit the documents in a variety of ways. Click on one of the bellow buttons to perform the following actions: </p> <ul><li>Upload a new Document</li> <li>Add a new Folder</li> <li>Delete a Folder</li></ul><div style="text-align: center;"><button type="button" id="upload_new_document" class="btn btn-success">Upload Document</button><button style="margin-left: 5px" id="add_category" type="button" class="btn btn-info">Add Folder</button><button style="margin-left: 5px" type="button" id="delete_category" class="btn btn-danger">Delete Folder</button></div></div>');
                                } else
                                {
                                    for (let i = 0; i < w2ui.sidebar.nodes.length; i++)
                                    {
                                        for (let j = 0; j < w2ui.sidebar.nodes[i].nodes.length; j++)
                                        {
                                            if (w2ui.sidebar.nodes[i].nodes[j].id === parseInt(event.target))
                                            {
                                                w2ui.document_layout.html('main', '<object style="height: 95%; width: 100%;" data="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf"><embed src="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf" /></object><div style="text-align: center"><button type="button" style="margin-right: 5px;" id="document_upload" class="btn btn-info" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Update Document</button><button type="button" id="document_delete" class="btn btn-danger" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Delete Document</button><input id="uploader" type="file" accept="application/pdf"/></div>');
                                                selected_document_id = w2ui.sidebar.nodes[i].nodes[j].id;
                                                current_directory = w2ui.sidebar.nodes[i].text;
                                                current_file_name = w2ui.sidebar.nodes[i].nodes[j].text + ".pdf";
                                            }
                                        }
                                    }
                                }
                            },
                            onClose: function (event) {
                                this.click('introduction');
                            }
                        }
                    }
                ]
            },
            sidebar: {
                name: 'sidebar',
                nodes: documents,
                onClick: function (event) {
                    var tabs = w2ui.document_layout_main_tabs;
                    let column_clicked = false;

                    for (let i = 0; i < w2ui.sidebar.nodes.length; i++)
                    {
                        if (w2ui.sidebar.nodes[i].id === event.target)
                        {
                            column_clicked = true;
                        }
                    }

                    if (!column_clicked)
                    {
                        if (tabs.get(event.target)) {
                            tabs.select(event.target);

                            for (let i = 0; i < w2ui.sidebar.nodes.length; i++)
                            {
                                for (let j = 0; j < w2ui.sidebar.nodes[i].nodes.length; j++)
                                {
                                    if (w2ui.sidebar.nodes[i].nodes[j].id === parseInt(event.target))
                                    {
                                        w2ui.document_layout.html('main', '<object style="height: 95%; width: 100%;" data="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf"><embed src="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf" /></object><div style="text-align: center"><button type="button" style="margin-right: 5px;" id="document_upload" class="btn btn-info" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Update Document</button><button type="button" id="document_delete" class="btn btn-danger" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Delete Document</button><input id="uploader" type="file" accept="application/pdf"/></div>');
                                        selected_document_id = w2ui.sidebar.nodes[i].nodes[j].id;
                                        current_directory = w2ui.sidebar.nodes[i].text;
                                        current_file_name = w2ui.sidebar.nodes[i].nodes[j].text + ".pdf";
                                    }
                                }
                            }
                        } else {
                            tabs.add({id: event.target, caption: event.node.text, closable: true});
                            tabs.select(event.target);

                            for (let i = 0; i < w2ui.sidebar.nodes.length; i++)
                            {
                                for (let j = 0; j < w2ui.sidebar.nodes[i].nodes.length; j++)
                                {
                                    if (w2ui.sidebar.nodes[i].nodes[j].id === parseInt(event.target))
                                    {
                                        w2ui.document_layout.html('main', '<object style="height: 95%; width: 100%;" data="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf"><embed src="https://' + w2ui.sidebar.nodes[i].nodes[j].url + '" type="application/pdf" /></object><div style="text-align: center"><button type="button" style="margin-right: 5px;" id="document_upload" class="btn btn-info" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Update Document</button><button type="button" id="document_delete" class="btn btn-danger" data-id="' + w2ui.sidebar.nodes[i].nodes[j].id + '">Delete Document</button><input id="uploader" type="file" accept="application/pdf"/></div>');
                                        selected_document_id = w2ui.sidebar.nodes[i].nodes[j].id;
                                        current_directory = w2ui.sidebar.nodes[i].text;
                                        current_file_name = w2ui.sidebar.nodes[i].nodes[j].text + ".pdf";
                                    }
                                }
                            }

                        }
                    }
                }
            }
        };

        $(function () {
            // initialization 
            $('#documents').w2layout(config.layout);
            w2ui.document_layout.content('left', $().w2sidebar(config.sidebar));
            w2ui.document_layout.html('main', '<div><h1 style="text-align: center"><b>Documents Panel</b></h1><p style="font-size: 14px;">You can use the buttons below to edit the documents in a variety of ways. Click on one of the bellow buttons to perform the following actions: </p> <ul><li>Upload a new Document</li> <li>Add a new Folder</li> <li>Delete a Folder</li></ul><div style="text-align: center;"><button type="button" id="upload_new_document" class="btn btn-success">Upload Document</button><button style="margin-left: 5px" id="add_category" type="button" class="btn btn-info">Add Folder</button><button style="margin-left: 5px" type="button" id="delete_category" class="btn btn-danger">Delete Folder</button></div></div>');
            //console.log(w2ui.sidebar);
        });

        $("body").on('click', '#document_upload', function (event) {
            if (sessionStorage.getItem("userName") === null)
            {
                event.preventDefault();
                Swal.fire({
                    type: 'error',
                    title: 'Not allowed to edit',
                    text: 'Please login to edit documents.',
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
            } else {
                $('#uploader').trigger('click');
            }
        });

        $("body").on('click', '#document_delete', function (event) {
            if (sessionStorage.getItem("userName") === null)
            {
                event.preventDefault();
                Swal.fire({
                    type: 'error',
                    title: 'Not allowed to edit',
                    text: 'Please login to edit documents.',
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
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Delete Document?',
                    text: 'Are you sure you want to delete the ' + current_file_name + " document?",
//                footer: '<a href>Why do I have this issue?</a>',
                    showCancelButton: true,
                    confirmButtonColor: '#8bc33f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                }).then(function (result) {
                    if (result.value) {
                        upload_document(this, 'backend/delete/delete_document.php');
                    }
                });
            }
        });

        $("body").on('change', '#uploader', function () {
            upload_document(this, 'backend/upload/update_document.php');
        });

        $("body").on('click', '#upload_new_document', function (event) {
            if (sessionStorage.getItem("userName") === null)
            {
                event.preventDefault();
                Swal.fire({
                    type: 'error',
                    title: 'Not allowed to edit',
                    text: 'Please login to edit documents.',
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
            } else {
                if (typeof w2ui.upload_document_form === 'undefined')
                {
                    create_form("Upload Document", "upload_document_form", [{name: 'uploadDocument', type: 'file', required: true, html: {caption: 'Upload Document', attr: 'size="40" maxlength="40" placeholder="Drag and drop or click to add a file."', span: 10}, options: {max: 1,
                                onAdd: function (event)
                                {
                                    let name = event.file.name;
                                    if (!name.endsWith(".pdf"))
                                    {
                                        event.preventDefault();

                                        w2alert('You may only upload .pdf files. Please try again.');
                                    }
                                }}
                        }, {name: 'documentCategory', type: 'list', required: true, options: {items: documents}, html: {caption: 'Document Category', attr: 'size="40" maxlength="40" placeholder="Select a document category."', span: 10}}], "upload_document");
                }
                form_to_display = "upload_document_form";
                openPopup();
            }
        });

        $("body").on('click', '#add_category', function (event) {
            if (sessionStorage.getItem("userName") === null)
            {
                event.preventDefault();
                Swal.fire({
                    type: 'error',
                    title: 'Not allowed to edit',
                    text: 'Please login to edit documents.',
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
            } else {
                if (typeof w2ui.add_category_form === 'undefined')
                {
                    create_form("Add Category", "add_category_form", [{name: 'new_category', type: 'text', required: true, html: {caption: 'New Category', attr: 'size="40" maxlength="40" placeholder="New category name"', span: 10}}], "add_category");
                }
                form_to_display = "add_category_form";
                openPopup();
            }
        });

        $("body").on('click', '#delete_category', function (event) {
            if (sessionStorage.getItem("userName") === null)
            {
                event.preventDefault();
                Swal.fire({
                    type: 'error',
                    title: 'Not allowed to edit',
                    text: 'Please login to edit documents.',
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
            } else {
                if (typeof w2ui.delete_category_form === 'undefined')
                {
                    create_form("Delete Category", "delete_category_form", [{name: 'category_to_delete', type: 'list', required: true, options: {items: documents}, html: {caption: 'Category', attr: 'size="40" maxlength="40" placeholder="Category to delete"', span: 10}}], "delete_category");
                }
                form_to_display = "delete_category_form";
                openPopup();
            }
        });
    },
    error: function (jqXHR, textStatus, errorThrown) { // if error occured 
        onAjaxError(jqXHR, textStatus, errorThrown);
    }
});