/* global w2alert, Swal, moment */

//==============================================================================================================================================================================================\\
//=============================================================================Technician Class=================================================================================================\\
//==============================================================================================================================================================================================\\
function Technician(id, email, name, avatar, experience, workingSince, activitiesComplete, activitiesInProgress, documentsUpdated, documentsCreated, accessLevel) {
    this.id = id;
    this.email = email;
    this.name = name;
    this.avatar = avatar;
    this.experience = experience;
    this.workingSince = workingSince;
    this.activitiesComplete = activitiesComplete;
    this.activitiesInProgress = activitiesInProgress;
    this.documentsUpdated = documentsUpdated;
    this.documentsCreated = documentsCreated;
    this.accessLevel = accessLevel;
}
//==========================================================================\\
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Technician Getters<<<<<<<<<<<<<<<<<<<<<<<<<<<\\
//==========================================================================\\
Technician.prototype.getID = function () {
    return this.id;
};

Technician.prototype.getEmail = function () {
    return this.email;
};

Technician.prototype.getName = function () {
    return this.name;
};

Technician.prototype.getAvatar = function () {
    return this.avatar;
};

Technician.prototype.getExperience = function () {
    return this.experience;
};

Technician.prototype.getWorkingSince = function () {
    return this.workingSince;
};

Technician.prototype.getActivitiesComplete = function () {
    return this.activitiesComplete;
};

Technician.prototype.getActivitiesInProgress = function () {
    return this.activitiesInProgress;
};

Technician.prototype.getDocumentsUpdated = function () {
    return this.documentsCreated;
};

Technician.prototype.getDocumentsCreated = function () {
    return this.documentsCreated;
};

Technician.prototype.getAccessLevel = function () {
    return this.accessLevel;
};

//==========================================================================\\
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Technician Setters<<<<<<<<<<<<<<<<<<<<<<<<<<\\
//==========================================================================\\
Technician.prototype.setID = function (id) {
    this.id = id;
};

Technician.prototype.setEmail = function (email) {
    this.email = email;
};

Technician.prototype.setName = function (name) {
    this.name = name;
};

Technician.prototype.setAvatar = function (avatar) {
    this.avatar = avatar;
};

Technician.prototype.setExperience = function (experience) {
    this.experience = experience;
};

Technician.prototype.setWorkingSince = function (workingSince) {
    this.workingSince = workingSince;
};

Technician.prototype.setActivitiesComplete = function (activitiesComplete) {
    this.activitiesComplete = activitiesComplete;
};

Technician.prototype.setActivitiesInProgress = function (activitiesInProgress) {
    this.activitiesInProgress = activitiesInProgress;
};

Technician.prototype.setDocumentsUpdated = function (documentsCreated) {
    this.documentsCreated = documentsCreated;
};

Technician.prototype.setDocumentsCreated = function (documentsCreated) {
    this.documentsCreated = documentsCreated;
};

Technician.prototype.setAccessLevel = function (accessLevel) {
    this.accessLevel = accessLevel;
};

//==============================================================================================================================================================================================\\
//==============================================================================Checklist Class=================================================================================================\\
//==============================================================================================================================================================================================\\
function Checklist(checklistID, siteCode, siteName, siteCountry, siteType, activityType, expectedGoLiveDate, createdOn, technician, checklistHeader, complete, status, region, overnightSupport, category_identifier, tab_identifier) {
    this.checklistID = checklistID;
    this.siteCode = siteCode;
    this.siteName = siteName;
    this.siteCountry = siteCountry;
    this.siteType = siteType;
    this.activityType = activityType;
    this.expectedGoLiveDate = expectedGoLiveDate;
    this.createdOn = createdOn;
    this.technician = technician;
    this.checklistHeader = checklistHeader;
    this.complete = complete;
    this.status = status;
    this.region = region;
    this.overnightSupport = overnightSupport;
    this.category_identifier = category_identifier;
    this.tab_identifier = tab_identifier;
}

//==========================================================================\\
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Checklist Getters<<<<<<<<<<<<<<<<<<<<<<<<<<<\\
//==========================================================================\\
Checklist.prototype.getChecklistID = function () {
    return this.checklistID;
};

Checklist.prototype.getSiteCode = function () {
    return this.siteCode;
};

Checklist.prototype.getSiteName = function () {
    return this.siteName;
};

Checklist.prototype.getSiteCountry = function () {
    return this.siteCountry;
};

Checklist.prototype.getSiteType = function () {
    return this.siteType;
};

Checklist.prototype.getActivityType = function () {
    return this.activityType;
};

Checklist.prototype.getExpectedGoLiveDate = function () {
    return this.expectedGoLiveDate;
};

Checklist.prototype.getCreatedOn = function () {
    return this.createdOn;
};

Checklist.prototype.getTechnician = function () {
    return this.technician;
};

Checklist.prototype.getChecklistHeader = function () {
    return this.checklistHeader;
};

Checklist.prototype.getComplete = function () {
    return this.complete;
};

Checklist.prototype.getStatus = function () {
    return this.status;
};

Checklist.prototype.getRegion = function () {
    return this.region;
};

Checklist.prototype.getOvernightSupport = function () {
    return this.overnightSupport;
};

Checklist.prototype.getCategoryIdentifiers = function () {
    return this.identifier;
};

Checklist.prototype.getTabIdentifiers = function () {
    return this.tab_identifier;
};
//==========================================================================\\
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Checklist Setters<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\\
//==========================================================================\\
Checklist.prototype.setChecklistID = function (checklistID) {
    return this.checklistID = checklistID;
};

Checklist.prototype.setSiteCode = function (siteCode) {
    return this.siteCode = siteCode;
};

Checklist.prototype.setSiteName = function (siteName) {
    return this.siteName = siteName;
};

Checklist.prototype.setSiteCountry = function (siteCountry) {
    return this.siteCountry = siteCountry;
};

Checklist.prototype.setSiteType = function (siteType) {
    return this.siteType = siteType;
};

Checklist.prototype.setActivityType = function (activityType) {
    return this.activityType = activityType;
};

Checklist.prototype.setExpectedGoLiveDate = function (expectedGoLiveDate) {
    return this.expectedGoLiveDate = expectedGoLiveDate;
};

Checklist.prototype.setCreatedOn = function (createdOn) {
    return this.createdOn = createdOn;
};

Checklist.prototype.setTechnician = function (technician) {
    return this.technician = technician;
};

Checklist.prototype.setChecklistHeader = function (checklistHeader) {
    return this.checklistHeader = checklistHeader;
};

Checklist.prototype.setComplete = function (complete) {
    return this.complete = complete;
};

Checklist.prototype.setStatus = function (status) {
    return this.status = status;
};

Checklist.prototype.setRegion = function (region) {
    return this.region = region;
};

Checklist.prototype.setOvernightSupport = function (overnightSupport) {
    return this.overnightSupport = overnightSupport;
};

Checklist.prototype.setCategoryIdentifiers = function (identifier) {
    return this.identifier = identifier;
};

Checklist.prototype.setTabIdentifiers = function (tab_identifier) {
    return this.tab_identifier = tab_identifier;
};
//==============================================================================//
//=================================Functions====================================//
//==============================================================================// 
function sortNumber(a, b) {
    return a - b;
}

function setLoginContent() {
    $(".login").before("<li class='profileName'><a href='profile.php'><i class='glyphicon glyphicon-user'></i> Profile</a></li>");
    if (sessionStorage.getItem("accessLevel") === "2")
    {
        $(".profileName").after("<li class='admin'><a href='administratorConsole.php'><i class='glyphicon glyphicon-briefcase'></i> Admin</a></li>");
    }
    $(".login").html("<a href='#' id='logout'><i class='glyphicon glyphicon-off'></i> Logout</a>");
    $(".allSitesLogin").after("<li><a href='mySites.php'><i class='glyphicon glyphicon-edit'></i> My Sites</a></li>");
    $(".allSitesLogin").before("<li><a href='newActivity.php'><i class='glyphicon glyphicon-plus'></i> New Activity</a></li>");
    $("#loginToBegin").html("<a class='btn btn-primary' href='newActivity.php'><i class='glyphicon glyphicon-plus'></i> Start New Activity</a>");
    $("#avatar").attr('src', sessionStorage.getItem("avatar"));
}

function create_activity_types(page, resource_id) {

    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: "createSite", technician: sessionStorage.getItem("userName")},
//            beforeSend: function () {
//                // setting a timeout
//                $(placeholder).addClass('loading');
//            },
        success: function (data) {
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

            var json = JSON.parse(data);
            //console.log(json);

            if (page === "new_build")
            {

                //Append the data, since we used UNION ALL for the query we have to use the "source" field I included to differentiate between the 3 results
                for (var i = 0; i < json[0].length; i++) {
                    if (json[0][i].source === "siteType")
                    {
                        $("#siteType").append("<option value='" + json[0][i].siteType + "'>" + json[0][i].siteType + "</option>");
                    } else if (json[0][i].source === "activityType")
                    {
                        $("#activityType").append("<option value='" + json[0][i].siteType + "'>" + json[0][i].siteType + "</option>");
                    } else if (json[0][i].source === "technician") {
                        if (json[0][i].siteType !== sessionStorage.getItem("user_name"))
                        {
                            $("#overnightSupport").append("<option value='" + json[0][i].siteType + "'>" + json[0][i].siteType + "</option>");
                        }
                    } else
                    {
                        $("#siteRegion").append("<option value='" + json[0][i].siteTypeID + "'>" + json[0][i].siteType + "</option>");
                    }
                }

                let array_optional = ["cacheProxy", "NVR", "emailMachine"];

                for (let i = 0; i < json[1].length; i++)
                {
                    let selector = '[name="' + array_optional[i] + '"]';
                    $(selector).attr("value", json[1][i]["optionalCategoryName"]);
                }

                $('#overnightSupport').multiselect({numberDisplayed: 2, includeSelectAllOption: true, enableFiltering: true});
            } else
            {
                for (var i = 0; i < json[0].length; i++) {
                    if (json[0][i].source === "activityType")
                    {
                        $("#eventActivityType").append("<option value='" + json[0][i].siteType + "'>" + json[0][i].siteType + "</option>");
                    } else if (json[0][i].source === "technician") {
                        $("#admin_select_tech").append("<option value='" + json[0][i].siteType + "' id='" + json[0][i].siteTypeID + "'>" + json[0][i].siteType + "</option>");
                    }
                }

                $('#admin_select_tech *').each(function (key, value) {
                    if ($(this).attr("id") === resource_id) {
                        $('#create_event_form').bootstrapValidator('updateStatus', "admin_select_tech", 'VALID');
                        $('#admin_select_tech').val($(this).val());
                    }
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });
}



function isOdd(num) {
    return num % 2;
}

function login(email, password) {
    $.ajax({
        type: 'POST',
        url: "backend/loginTransaction.php",
        dataType: 'text',
        data: {email: email, password: password},
        beforeSend: function () {
            $("#login").html('Loading... <div id="loading"></div>');
            $("#login").prop('disabled', true);
        },
        success: function (data) {
            $("#login").html('Login');
            $("#login").prop('disabled', false);

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



            if (data === "Something went wrong and you could not be logged in. This could be either due to a wrong email or password. Please try again." || data === "Ooops, the password seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!" || data === "Ooops, the email seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!")
            {
                Swal.fire({
                    type: 'error',
                    title: 'Login Failed',
                    text: data,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Try Again'
                });
            } else
            {
                $('#modalLogin').modal('toggle');

                var json = JSON.parse(data);
                var technician;

                //console.log(json);

                for (var i = 0; i < json.length; i++)
                {
                    technician = new Technician(json[i].id, json[i].email, json[i].technicianFullName, json[i].avatar, json[i].experience, json[i].workingSice, json[i].activitiesComplete, json[i].activitiesInProgress, json[i].documentsUpdated, json[i].documentsCreated, json[i].accessLevel);
                }

                sessionStorage.setItem("userLogedIn", "true");
                sessionStorage.setItem("userName", technician.getName());
                sessionStorage.setItem("email", technician.getEmail());
                sessionStorage.setItem("accessLevel", technician.getAccessLevel());
                sessionStorage.setItem("avatar", technician.getAvatar());
                sessionStorage.setItem("user_id", technician.getID());

                setLoginContent();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            $("#login").html('Login');
            $("#login").prop('disabled', false);

            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });
}

function forgotPassword(email)
{
    $.ajax({
        type: 'POST',
        url: "backend/forgot_password_transaction.php",
        dataType: 'text',
        data: {email: email},
        beforeSend: function () {
            $("#forgotPassword").html('Loading... <div id="loading"></div>');
            $("#forgotPassword").prop('disabled', true);
        },
        success: function (data) {
            $("#forgotPassword").html('Reset Password');
            $("#forgotPassword").prop('disabled', false);
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


            if (data === "Please check your email for a link to reset your password and follow the instructions.")
            {
                Swal.fire({
                    type: 'success',
                    title: 'Password Reset email has been sent successfully!',
                    text: data,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then(function (result) {
                    if (result.value) {
                        $('#modalForgotPassword').modal('toggle');
                    }
                });
            } else
            {
                Swal.fire({
                    type: 'error',
                    title: 'Password Reset Failed!',
                    text: data,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Try Again?'
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            $("#forgotPassword").html('Reset Password');
            $("#forgotPassword").prop('disabled', false);

            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });
}

function onAjaxErrorW2ui(jqXHR, textStatus, errorThrown)
{
    if (jqXHR.status === 0) {
        w2alert('Error ' + jqXHR.status + ': ' + errorThrown + '. There seems to be a problem with your connection, please check your network connection and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else if (jqXHR.status === 404) {
        w2alert('Error ' + jqXHR.status + ': Page Not Found. There seems to be a problem with finding the requested web page, please check your network connection and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else if (jqXHR.status === 500) {
        w2alert('Error 500: Internal Server Error. An error has occured on the server, please wait a bit and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else if (textStatus === 'parsererror') {
        w2alert('Requested JSON parse failed. An error has occured when parsing your JSON content, please wait a bit and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else if (textStatus === 'timeout') {
        w2alert('Time out error. The system has timed out while processing your request, please wait a bit and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else if (textStatus === 'abort') {
        w2alert('Ajax request aborted. Something went wrong and the AJAX request has been aborted, please wait a bit, check your network connection and refresh the page to try again.')
                .ok(function () {
                    location.reload();
                });
    } else {
        w2alert('Uncaught Error. ' + jqXHR.responseText)
                .ok(function () {
                    location.reload();
                });
    }
}

function onAjaxError(jqXHR, textStatus, errorThrown)
{
    if (jqXHR.status === 0) {
        Swal.fire({
            type: 'error',
            title: 'Error ' + jqXHR.status + ': ' + errorThrown,
            text: 'There seems to be a problem with your connection, please check your network connection and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else if (jqXHR.status === 404) {
        Swal.fire({
            type: 'error',
            title: 'Error ' + jqXHR.status + ': Page Not Found',
            text: 'There seems to be a problem with finding the requested web page, please check your network connection and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else if (jqXHR.status === 500) {
        Swal.fire({
            type: 'error',
            title: 'Error 500: Internal Server Error',
            text: 'An error has occured on the server, please wait a bit and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else if (textStatus === 'parsererror') {
        Swal.fire({
            type: 'error',
            title: 'Requested JSON parse failed.',
            text: 'An error has occured when parsing your JSON content, please wait a bit and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else if (textStatus === 'timeout') {
        Swal.fire({
            type: 'error',
            title: 'Time out error.',
            text: 'The system has timed out while processing your request, please wait a bit and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else if (textStatus === 'abort') {

        Swal.fire({
            type: 'error',
            title: 'Ajax request aborted.',
            text: 'Something went wrong and the AJAX request has been aborted, please wait a bit, check your network connection and refresh the page to try again.',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Refresh Page'
        }).then(function (result) {
            if (result.value) {
                location.reload();
            }
        });
    } else {
        Swal.fire({
            type: 'error',
            title: 'Uncaught Error.',
            text: jqXHR.responseText,
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
}

function filterArray(arr, word) {
    var i = arr.length;
    //-- Loop through the array in reverse order since we are modifying the array.

    while (i--) {
        if (arr[i].innerHTML.indexOf(word) < 0) {
            //-- splice will remove the non-matching element
            arr.splice(i, 1);
        }
    }
}

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function detectMobile() {
    if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
            ) {
        return true;
    } else {
        return false;
    }
}

function getURLValue(name)
{
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results === null)
        return null;
    else
        return results[1];
}
function isEqual(value, other) {

    // Get the value type
    var type = Object.prototype.toString.call(value);

    // If the two objects are not the same type, return false
    if (type !== Object.prototype.toString.call(other))
        return false;

    // If items are not an object or array, return false
    if (['[object Array]', '[object Object]'].indexOf(type) < 0)
        return false;

    // Compare the length of the length of the two items
    var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
    var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
    if (valueLen !== otherLen)
        return false;

    // Compare two items
    var compare = function (item1, item2) {

        // Get the object type
        var itemType = Object.prototype.toString.call(item1);

        // If an object or array, compare recursively
        if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
            if (!isEqual(item1, item2))
                return false;
        }

        // Otherwise, do a simple comparison
        else {

            // If the two items are not the same type, return false
            if (itemType !== Object.prototype.toString.call(item2))
                return false;

            // Else if it's a function, convert to a string and compare
            // Otherwise, just compare
            if (itemType === '[object Function]') {
                if (item1.toString() !== item2.toString())
                    return false;
            } else {
                if (item1 !== item2)
                    return false;
            }

        }
    };

    // Compare properties
    if (type === '[object Array]') {
        for (var i = 0; i < valueLen; i++) {
            if (compare(value[i], other[i]) === false)
                return false;
        }
    } else {
        for (var key in value) {
            if (value.hasOwnProperty(key)) {
                if (compare(value[key], other[key]) === false)
                    return false;
            }
        }
    }

    // If nothing failed, return true
    return true;

}

function header_footer_events() {
    accessCode = "headerAndFooter";
    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: accessCode, technician: sessionStorage.getItem("userName")},
        success: function (data) {
            //console.log(JSON.parse(data));

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

            let json = JSON.parse(data);
            
            var liveEventFound = false;
            var upcomingActivitiesFound = false;
            var newsEventFound = false;
            var otherEventFound = false;
            var documentEventFound = false;

            console.log(json);
            //Append the data, since we used UNION ALL for the query we have to use the "source" field I included to differentiate between the 3 results
            for (var i = 0; i < json.length; i++) {
                if (json[i].source === "liveSiteEvents")
                {
                    if (!liveEventFound)
                    {
                        $("#liveEvents").html("");
                    }

                    $("#liveEvents").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='liveEventHeading'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='liveEventTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div> <div class='time' id='liveEventTimeAndDate'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].time).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i> " + json[i].date + "</div></div></div></div></a> <hr>");

                    liveEventFound = true;
                } else if (json[i].source === "upcomingSiteEvents")
                {
                    if (!upcomingActivitiesFound)
                    {
                        $("#upcomingEvents").html("");
                    }

                    $("#upcomingEvents").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='liveEventHeading'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='liveEventTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div> <div class='time' id='liveEventTimeAndDate'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].time).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i> " + json[i].date + "</div></div></div></div></a> <hr>");

                    upcomingActivitiesFound = true;
                } else if (json[i].source === "siteNews")
                {
                    if (!newsEventFound)
                    {
                        $("#siteNews").html("");
                    }

                    $("#siteNews").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='siteNewsHeading'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='siteNewsTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div><div class='time' id='siteNewsTimeAndDate'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].time).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i> " + json[i].date + "</div></div></div></div></a> <hr>");

                    newsEventFound = true;
                } else if (json[i].source === "documentEvents")
                {
                    if (!documentEventFound)
                    {
                        $("#otherEvents").html("");
                    }

                    if (json[i].event_type === 'other')
                    {
                        if (moment(json[i].time).format("DD-MM-YYYY") !== "Invalid date")
                        {
                            $("#otherEvents").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='otherEventsTitle'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='otherEventsTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].date).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(json[i].date).format("DD-MM-YYYY") + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].time).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(json[i].time).format("DD-MM-YYYY") + "</div></div></div></div></a> <hr>");
                        } else
                        {
                            $("#otherEvents").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='otherEventsTitle'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='otherEventsTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].date).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(json[i].date).format("DD-MM-YYYY") + "</div> </div></div></div></a> <hr>");
                        }
                    } else
                    {
                        $("#otherEvents").append("<a href='" + json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='otherEventsTitle'>" + json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='otherEventsTech'><i class='glyphicon glyphicon-user'></i> " + json[i].technician + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(json[i].time).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + json[i].date + "</div> </div></div></div></a> <hr>");
                    }
                    documentEventFound = true;
                }
            }

//            for (let i = 0; i < other_json.length; i++)
//            {
//                if (!otherEventFound)
//                {
//                    $("#no_document_events").remove();
//                }
//
//                if (moment(other_json[i].end).format("DD-MM-YYYY") !== "Invalid date")
//                {
//                    $("#otherEvents").append("<a href='" + other_json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + other_json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='otherEventsTitle'>" + other_json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='otherEventsTech'><i class='glyphicon glyphicon-user'></i> " + other_json[i].technician + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(other_json[i].start).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(other_json[i].start).format("DD-MM-YYYY") + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(other_json[i].end).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(other_json[i].end).format("DD-MM-YYYY") + "</div></div></div></div></a> <hr>");
//                } else
//                {
//                    $("#otherEvents").append("<a href='" + other_json[i].link + "'><div class='item d-flex align-items-center'><div class='image'><img src='" + other_json[i].image + "' alt='...' class='img-fluid'></div><div class='title'><strong id='otherEventsTitle'>" + other_json[i].event + "</strong><div class='d-flex newsDetailsContainer'><div class='comments' id='otherEventsTech'><i class='glyphicon glyphicon-user'></i> " + other_json[i].technician + "</div><div class='time' id='otherEventsDateAndTime'><i class='glyphicon glyphicon-time'></i> " + moment(other_json[i].start).format("HH:mm:ss") + " &nbsp; | &nbsp; <i class='glyphicon glyphicon-calendar'></i>  " + moment(other_json[i].start).format("DD-MM-YYYY") + "</div> </div></div></div></a> <hr>");
//                }
//
//                otherEventFound = true;
//            }

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });
}

$(document).ready(function () {
//==============================================================================//
//===================================General====================================//
//==============================================================================//
    var path = window.location.pathname;
    var page = path.split("/").pop();
    var accessCode;
    var technician;

    if (sessionStorage.getItem("userLogedIn") === "true")
    {
        setLoginContent();
    }

    $("li").removeClass("active");
    $('a[href^="' + page + '"]').parentsUntil($("li .dropdown")).addClass("active");
    $(document).on("click", ".close", function () {
        $(this).closest('.alert').hide();
    });

    $("#logout").click(function () {
        sessionStorage.clear();
        window.location.href = "backend/logoutTransaction.php";
    });
//==============================================================================//
//=================================Header/Footer================================//
//==============================================================================//
    header_footer_events();

//==============================================================================//
//===================================Technician=================================//
//==============================================================================//
    $("#login").click(function (e) {
        e.preventDefault();

        var email = $("input[name=email]").val();
        var password = $("input[name=password]").val();

        login(email, password);
    });

    $("#forgotPasswordLink").click(function () {
        $('#modalLogin').modal('toggle');
        $('#modalForgotPassword').modal('toggle');
    });

    $("#forgotPassword").click(function (e) {
        e.preventDefault();

        var email = $("input[name=forgotPasswordEmail]").val();
//        var newPassword = $("input[name=forgotPassword]").val();
//        var confirmNewPassword = $("input[name=confirmForgotPassword]").val();

        forgotPassword(email);
    });

    $("#backToLogin").click(function () {
        $('#modalForgotPassword').modal('toggle');
        $('#modalLogin').modal('toggle');
    });
});
//==============================================================================//
//==============================Miscellaneous===================================//
//==============================================================================//
if (!String.prototype.includes) {
    String.prototype.includes = function (search, start) {
        'use strict';
        if (typeof start !== 'number') {
            start = 0;
        }

        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}

if (!Array.prototype.indexOf)
{
    Array.prototype.indexOf = function (elt /*, from*/)
    {
        var len = this.length >>> 0;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
                ? Math.ceil(from)
                : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++)
        {
            if (from in this &&
                    this[from] === elt)
                return from;
        }
        return -1;
    };
}
