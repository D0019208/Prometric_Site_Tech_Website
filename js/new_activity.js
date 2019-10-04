/* global Intl, Swal */

$(document).ready(function () {
    $('#activity_form').bootstrapValidator({
// To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            siteCode: {
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
            siteCountry: {
                validators: {
                    stringLength: {
                        min: 4,
                        message: "A Country cannot have less than 4 letters!"
                    },
                    notEmpty: {
                        message: 'Please enter the Country in which the Site is to be located!'
                    }
                }
            },
            siteCounty: {
                validators: {
                    stringLength: {
                        min: 2,
                        message: "A County cannot have less than 2 letters!"
                    },
                    notEmpty: {
                        message: 'Please enter the County in which the Site is to be located!'
                    }
                }
            },
            siteTown: {
                validators: {
                    stringLength: {
                        min: 2,
                        message: "A Town cannot have less than 2 letters!"
                    },
                    notEmpty: {
                        message: 'Please enter the Town in which the Site is to be located!'
                    }
                }
            },
            expectedGoLiveDate: {
                validators: {
                    date: {
                        format: 'YYYY/MM/DD hh:mm',
                        message: 'The value is not a valid date'
                    },
                    notEmpty: {
                        message: 'The Expected Go Live Date cannot be empty!'
                    }
                }
            },
            siteType: {
                validators: {
                    notEmpty: {
                        message: 'Please select the Site Type!'
                    }
                }
            },
            siteRegion: {
                validators: {
                    notEmpty: {
                        message: 'Please select the Region in which the Site is to be located!'
                    }
                }
            },
            activityType: {
                validators: {
                    notEmpty: {
                        message: 'Please select the Activity Type!'
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
//==============================================================================//
//==============================Create New Site=================================//
//==============================================================================//
    //NEED TO DOCUMENT
    //Using Global variable page, we check to see what page we are on to see if we need to perform an AJAX request to not waste resources 
    create_activity_types("new_build");

    $("#submit").click(function () {
        var optionalFieldsArray = [];
        var optionalFieldSelected = false;
        var siteCode = $("input[name=siteCode]").val();
        var siteTown = $("input[name=siteTown]").val();
        var siteType = $("select[name=siteType]").val();
        var siteActivity = $("select[name=activityType]").val();
        $('.optional').each(function (i, obj)
        {
            if ($(this).attr('state') === "on")
            {
                optionalFieldSelected = true;
                optionalFieldsArray.push({"optionalField": $(this).val(), "optionalState": $(this).attr('state')});
            }
        });
        var validator = $('#activity_form').data('bootstrapValidator');
        validator.validate();
        if (validator.isValid())
        {
            let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $.ajax({
                type: 'POST',
                url: "backend/createNewActivity.php",
                dataType: 'text',
                data: {siteCode: siteCode, timezone: timezone, technician: sessionStorage.getItem("userName"), siteCountry: $("input[name=siteCountry]").val(), siteCounty: $("input[name=siteCounty]").val(), siteTown: siteTown, goLiveDate: $("input[name=expectedGoLiveDate]").val(), siteType: siteType, siteRegion: $("select[name=siteRegion]").val(), activityType: $("select[name=activityType]").val(), overnightSupport: $("#overnightSupport").val(), optionalValues: optionalFieldsArray, optionalFieldSelected: optionalFieldSelected},
                beforeSend: function () {
                    $("#submit").html('Loading <div id="loading"></div>');
                    $("#submit").prop('disabled', true);
                },
                success: function (data) {
                    //console.log(data);
                    $("#submit").html("Create New Site <span class='glyphicon glyphicon-send'></span>");
                    $("#submit").prop('disabled', false);
                    //console.log(data);

                    if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:"))
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

                    if (data === "Success")
                    {
                        Swal.fire({
                            type: 'success',
                            title: 'Activity Creation Complete',
                            text: 'The Activity "' + siteCode + ' ' + siteTown + ' - ' + siteActivity + '" has been successfully created! Please press "Okay" to continue back to the home page.',
                            //footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Okay'
                        }).then(function (result) {
                            if (result.value) {
                                window.location.replace("index.php");
                            }
                        });
                    } else
                    {
                        Swal.fire({
                            type: 'error',
                            title: 'Activity Creation Failed',
                            text: data,
                            //footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Try Again?'
                        }).then(function (result) {
                            if (result.value) {
                                $("#submit").trigger("click");
                            }
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                    $("#submit").html("Create New Site <span class='glyphicon glyphicon-send'></span>");
                    $("#submit").prop('disabled', false);

                    onAjaxError(jqXHR, textStatus, errorThrown);
                }
            });
        } else
        {
            Swal.fire({
                type: 'error',
                title: 'Validation Error',
                text: 'Please make sure to fill in all the required fields marked in red before continuing!'
                        // footer: '<a href>Why do I have this issue?</a>'
            });
        }
    });
    $('#siteCodeInfo').on('shown.bs.modal', function () {
        $("#modalIcon").css("height", $("#modalText").height());
    });
    //Load help info
    $(".glyphicon-info-sign").click(function () {
        var clickedText = $(this).parent().parent().parent().parent().find('label').text();

        if (clickedText === "Site Code")
        {
            $("#modalLabelSmall").text("Site Code");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the code of the site, each site has its own unique code used to identify it. E.G. 9750 is the practice lab in Prometric Building in Dundalk.</p>");
        } else if (clickedText === "Site Country")
        {
            $("#modalLabelSmall").text("Site Country");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the country in which the site will be located, pretty self explanatory. E.G. Ireland.</p>");
        } else if (clickedText === "Site County")
        {
            $("#modalLabelSmall").text("Site County");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the county in which the site will be located, pretty self explanatory. E.G. Louth.</p>");
        } else if (clickedText === "Site Town")
        {
            $("#modalLabelSmall").text("Site Town");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the town in which the site will be located, pretty self explanatory. E.G. Dundalk.</p>");
        } else if (clickedText === "Expected Go Live Date")
        {
            $("#modalLabelSmall").text("Expected Go Live Date");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the expected date and time on which the activity will be complete. E.G. 23/04/2019 11:01</p>");
        } else if (clickedText === "Site Type")
        {
            $("#modalLabelSmall").text("Site Type");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the type of site that will be built. An example of a site type would <b>Corporate.</b> For a full list of site types, click the selection menu.</p>");
        } else if (clickedText === "Site Region")
        {
            $("#modalLabelSmall").text("Site Region");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the region in which the site will be built. An example of a region in which Prometric builds sites would be <b>EMEA (<u>E</u>urope <u>M</u>iddle <u>E</u>ast <u>A</u>frica).</b> For a full list of regions, click the selection menu.</p>");
        } else if (clickedText === "Activity Type")
        {
            $("#modalLabelSmall").text("Site Activity");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/" + clickedText.replace(/\s/g, '') + ".png");
            $("#modalText").html("<p>This is the activity in which you will be engaging, a site can either be a <b>New Build, Rebuild or Closure.</b> For a full list of site types, click the selection menu.</p>");
        } else if (clickedText.includes("Overnight Support"))
        {
            $("#modalLabelSmall").text("Overnight Support");
            $("#modalIcon").css("background-image", "url(images/SiteIcons/ModalIcons/overnightSupport.png");
            $("#modalText").html("<p>This is the name of the Technician who will contiue working on this Activity when you leave the office to go home.</p>");
        }
    });
    $(function () {
        var nowDate = new Date();
        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
        $('#expectedGoLiveDate').datetimepicker({
            minDate: today,
            format: 'YYYY-MM-DD HH:mm'
        }).on("dp.change", function (e) {
            $('#activity_form').bootstrapValidator('updateStatus', "expectedGoLiveDate", 'VALID');
        });
    });

    $('input.success[type="checkbox"]').click(function () {
        if ($(this).attr("state") === "on")
        {
            $(this).attr("state", "off");
        } else {
            $(this).attr("state", "on");
        }
    }); 
});