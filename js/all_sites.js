$(document).ready(function () {
    var loadFilters = true;
    var accessCode = "loadFilters";
    if (loadFilters)
    {
        $.ajax({
            type: 'POST',
            url: "backend/getRequiredSiteData.php",
            dataType: 'text',
            data: {accessCode: accessCode},
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

                //Append the data, since we used UNION ALL for the query we have to use the "source" field I included to differentiate between the 3 results
                for (var i = 0; i < json.length; i++) {
                    if (json[i].source === "technician")
                    {
                        $("#technician").append("<option value='" + json[i].region + "'>" + json[i].region + "</option>");
                    } else if (json[i].source === "activityType")
                    {
                        $("#activityType").append("<option value='" + json[i].region + "'>" + json[i].region + "</option>");
                    } else if (json[i].source === "region") {
                        $("#region").append("<option value='" + json[i].region + "'>" + json[i].region + "</option>");
                    } else if (json[i].source === "status") {
                        $("#status").append("<option value='" + json[i].region + "'>" + json[i].region + "</option>");
                    } else if (json[i].source === "siteType") {
                        $("#siteType").append("<option value='" + json[i].region + "'>" + json[i].region + "</option>");
                    }
                }

                loadFilters = false;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    }

    $("#filtersButtonReset").click(function (e) {
        e.preventDefault();
        accessCode = "initialAllSiteDisplay";
        $.ajax({
            type: 'POST',
            url: "backend/getRequiredSiteData.php",
            dataType: 'text',
            data: {accessCode: accessCode},
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

                $("#allSites").html("");

                if (json.length === 0)
                {
                    $("#allSites").append('<h3 class="display-4" style="text-align: center;">No sites found that match your criteria. </h3><h3 class="display-4" style="text-align: center;"><a href="newActivity.php">Click here</a> to start a new activity!</h3>');
                }



                for (var i = 0; i < json.length; i++)
                {
                    var overnightSupport = "";

                    if (json[i].overNightSupport.constructor === Array)
                    {
                        for (var j = 0; j < json[i].overNightSupport.length; j++)
                        {
                            overnightSupport += "<a href='profile.php?technician=" + json[i].overNightSupport[j] + "'>" + json[i].overNightSupport[j] + "</a>";
                            if (json[i].overNightSupport.length > 1 && j !== json[i].overNightSupport.length - 1)
                            {
                                overnightSupport += ", ";
                            }
                        }
                    } else
                    {
                        overnightSupport = "<a href='profile.php?technician=" + json[i].overNightSupport + "'>" + json[i].overNightSupport + "</a>";
                    }

                    if (!isOdd(i))
                    {
                        if (json[i].status == "Not Started") {
                            $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code: <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                        } else if (json[i].status == "Complete")
                        {
                            $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                        } else
                        {
                            $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                        }
                    } else
                    {
                        if (json[i].status == "Not Started") {
                            $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                        } else if (json[i].status == "Complete")
                        {
                            $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                        } else
                        {
                            $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                        }
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });







    accessCode = "initialAllSiteDisplay";
    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: accessCode},
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

            $("#allSites").html("");

            if (json.length === 0)
            {
                $("#allSites").append('<h3 class="display-4" style="text-align: center;">No sites found that match your criteria. </h3><h3 class="display-4" style="text-align: center;"><a href="newActivity.php">Click here</a> to start a new activity!</h3>');
            }



            for (var i = 0; i < json.length; i++)
            {
                var overnightSupport = "";

                if (json[i].overNightSupport.constructor === Array)
                {
                    for (var j = 0; j < json[i].overNightSupport.length; j++)
                    {
                        overnightSupport += "<a href='profile.php?technician=" + json[i].overNightSupport[j] + "'>" + json[i].overNightSupport[j] + "</a>";
                        if (json[i].overNightSupport.length > 1 && j !== json[i].overNightSupport.length - 1)
                        {
                            overnightSupport += ", ";
                        }
                    }
                } else
                {
                    overnightSupport = "<a href='profile.php?technician=" + json[i].overNightSupport + "'>" + json[i].overNightSupport + "</a>";
                }

                if (!isOdd(i))
                {
                    if (json[i].status == "Not Started") {
                        $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code: <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                    } else if (json[i].status == "Complete")
                    {
                        $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                    } else
                    {
                        $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                    }
                } else
                {
                    if (json[i].status == "Not Started") {
                        $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                    } else if (json[i].status == "Complete")
                    {
                        $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                    } else
                    {
                        $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                    }
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });











    $("#filtersButtonSubmit").click(function (e) {
        e.preventDefault();


        $('.filters').each(function (i, obj)
        {
            if ($(this).attr('state') == "on")
            {
                optionalFieldSelected = true;
                optionalFieldsArray.push({"optionalField": $(this).val(), "optionalState": $(this).attr('state')});
            }
        });



        var technician = $("select[name=technician]").val();
        var activityType = $("select[name=activityType]").val();
        var siteCode = $("input[name=siteCode]").val();

        var filtersArray = [["technician", technician, "checklist"], ["activityType", activityType, "checklist"], ["siteCode", siteCode, "checklist"]];

        accessCode = "allSitesFilter";
        $.ajax({
            type: 'POST',
            url: "backend/getRequiredSiteData.php",
            dataType: 'text',
            data: {accessCode: accessCode, filtersArray: filtersArray},
            beforeSend: function () {
                $("#filtersButtonSubmit").html('Loading... <div id="loading"></div>');
                $("#filtersButtonSubmit").prop('disabled', true);
            },
            success: function (data) {
                $("#filtersButtonSubmit").html('Filter Sites Now!');
                $("#filtersButtonSubmit").prop('disabled', false);
                //console.log(JSON.parse(data));
                if (data === "Technician, Region, Status, Site Type, Site Code and Activity Type cannot all be empty!")
                {
                    Swal.fire({
                        type: 'error',
                        title: 'Site display failed!',
                        text: data,
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Try Again?'
                    });
                } else
                {
                    $("#allSites").html("");

                    var json = JSON.parse(data);
                    console.log(json);

                    if (json.length == 0)
                    {
                        $("#allSites").append('<h3 class="display-4" style="text-align: center;">No sites found that match your criteria. </h3><h3 class="display-4" style="text-align: center;"><a href="newActivity.php">Click here</a> to start a new activity!</h3>');
                    }

                    for (var i = 0; i < json.length; i++)
                    {
                        var overnightSupport = "";
                        var overnightSupportArray = json[i].overNightSupport.split(',');


                        for (var j = 0; j < overnightSupportArray.length; j++)
                        {
                            overnightSupport += "<a href='profile.php?technician=" + overnightSupportArray[j] + "'>" + overnightSupportArray[j] + "</a>";
                            if (j !== overnightSupportArray.length - 1)
                            {
                                overnightSupport += ", ";
                            }
                        }

                        if (!isOdd(i))
                        {
                            if (json[i].status == "Not Started") {
                                $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code: <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                            } else if (json[i].status == "Complete")
                            {
                                $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                            } else
                            {
                                $("#allSites").append("<div class='site-card'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                            }
                        } else
                        {
                            if (json[i].status == "Not Started") {
                                $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityEmergency'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='emergencyLink'>Read More</a></p></div></div>");
                            } else if (json[i].status == "Complete")
                            {
                                $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityNew'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "'>Read More</a></p></div></div>");
                            } else
                            {
                                $("#allSites").append("<div class='site-card alt'><div class='meta'><div class='photo' style='background-image: url(images/testCenter.png); background-size: contain; background-repeat: no-repeat;'></div><ul class='details'><li class='author'>Technician: <a href='profile.php?technician=" + json[i].technician + "'>" + json[i].technician + "</a></li><li class='date'>Expected Go Live Date: " + json[i].expectedGoLiveDate + "</li><li class='siteCode'>Site Code:  <a href='allSites.php?siteCode=" + json[i].siteCode + "'>" + json[i].siteCode + "</a></li><li class='overnightSupport'>FTS: " + overnightSupport + "</li><li class='region'>Region: " + json[i].region + "</li></ul></div><div class='description'><h1>Site " + json[i].siteCode + " - " + json[i].activityType + " (" + json[i].status + ")</h1><h2>" + json[i].siteCountry + " - " + json[i].siteName + "</h2><p><i class='activityProgress'></i>Click on the link below to continue working on this site and to view the checklist.</p><p class='read-more'><a href='site.php?checklistID=" + json[i].checklistID + "' class='progressLink'>Read More</a></p></div></div>");
                            }
                        }
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });
});