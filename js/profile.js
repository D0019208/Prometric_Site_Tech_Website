/* global Intl, moment, Swal */

$(document).ready(function () {
    var userName;
    var guest = false;
    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    $("#statistics_heading").text("Activity Statistics for " + moment().format("MMMM"));

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

    if (getURLValue("technician") !== null)
    {
        userName = decodeURIComponent(getURLValue("technician"));
        guest = true;
    } else
    {
        userName = sessionStorage.getItem("userName");
        guest = false;
    }

    function makeBlob(dataURL)
    {
        var BASE64_MARKER = ';base64,';
        if (dataURL.indexOf(BASE64_MARKER) === -1) {
            var parts = dataURL.split(',');
            var contentType = parts[0].split(':')[1];
            var raw = decodeURIComponent(parts[1]);
            return new Blob([raw], {type: contentType});
        }
        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;

        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], {type: contentType});
    }

    function setimage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);

                //console.log(input.files[0]);

                var image = input.files[0];

                var imageData = new FormData();
                imageData.append('image', image);
                imageData.append('userName', userName);

                $.ajax({
                    url: 'backend/upload/uploadImage.php',
                    data: imageData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        //console.log(data);

                        if (data.includes("<b>Fatal error</b>:") || data.includes("<b>Warning</b>:") || data.includes("<b>SQLSTATE</b>:") || data === "Error connecting to FTP client.")
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
                            sessionStorage.setItem("avatar", data);
                            location.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // if error occured  
                        onAjaxError(jqXHR, textStatus, errorThrown);
                    }
                }); 
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").click(function (event) {
        if (guest)
        {
            event.preventDefault();
        }
    });

    $("#imageUpload").change(function (event) {
        if (!guest)
        {
            setimage(this);
        }
    });

    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: "profile", userName: userName, timezone: timezone},
        success: function (data) {
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
                var json = JSON.parse(data);

                let start = moment(json[0].workingSince, 'YYYY-MM-DD'); //Pick any format
                let end = moment(json[1].time, 'YYYY-MM-DD'); //right now (or define an end date yourself)
                let weekdayCounter = 0;

                while (start <= end) {
                    if (start.format('ddd') !== 'Sat' && start.format('ddd') !== 'Sun') {
                        weekdayCounter++; //add 1 to your counter if its not a weekend day
                    }
                    start = moment(start, 'YYYY-MM-DD').add(1, 'days'); //increment by one day
                }


                $("#technicianName").text(userName + " - " + json[0].title);
                $("#imagePreview").css({"background-image": "url(" + json[0].avatar + ")"});
                $("#activitiesInProgress").text(json[0].activitiesInProgress);
                $("#activitiesCompleted").text(json[0].activitiesComplete);
                $("#documentsUploaded").text(json[0].documentsCreated);
                $("#documentsUpdated").text(json[0].documentsUpdated);
                $("#yearsExperience").text(weekdayCounter);
                $("#documentsDeleted").text(json[0].documentsDeleted);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured  
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });

    var activities_complete_data = [];
    var activities_in_progress_data = [];
    var upcoming_activities_data = [];

    var canvas_activities = $('#activitiesBarChart').get(0).getContext('2d');

    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: "profile_activities_data", technician: userName, timezone: timezone},
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
            }

            let json = JSON.parse(data);

            for (let key in json)
            {
                activities_in_progress_data.push(json[key].activities_in_progress);
                activities_complete_data.push(json[key].activities_complete);
                upcoming_activities_data.push(json[key].upcoming_activities);
            }

            initializeSiteBarChart();
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });

    function initializeSiteBarChart() {
        var myBarChart = new Chart(canvas_activities, {
            type: 'bar',
            data: {
                labels: [userName],
                datasets: [
                    {
                        label: "Activities Complete",
                        data: activities_complete_data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: "Activities In Progress",
                        data: activities_in_progress_data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: "Activities Upcoming",
                        data: upcoming_activities_data,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                            ticks: {
                                maxRotation: 90,
                                minRotation: 80
                            }
                        }],
                    yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }]
                }
            }
        });
    }

    var new_build_data = [];
    var relocation_data = [];
    var refurb_data = [];
    var limited_data = [];
    var event_kit_data = [];
    var closure_data = [];
    var rebuild_data = [];
    var emergency_server_build_data = [];

    var pie_data = [];

    var canvas_documents = $('#barChartDocuments').get(0).getContext('2d');

    $.ajax({
        type: 'POST',
        url: "backend/getRequiredSiteData.php",
        dataType: 'text',
        data: {accessCode: "profile_activities_pie_data", technician: userName},
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

            if (data.length <= 2)
            {
                Swal.fire({
                    type: 'error',
                    title: 'No data sent from server',
                    text: 'The client has not recieved a response from the server, please refresh the page and try again.',
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
            //console.log(json);

            for (let key in json)
            {
                pie_data.push(json[key].new_build);
                pie_data.push(json[key].relocation);
                pie_data.push(json[key].refurb);
                pie_data.push(json[key].limited);
                pie_data.push(json[key].event_kit);
                pie_data.push(json[key].closure);
                pie_data.push(json[key].rebuild);
                pie_data.push(json[key].emergency_server_build);
            }
            //console.log(pie_data);
            initializeActivityPieChart();
        },
        error: function (jqXHR, textStatus, errorThrown) { // if error occured 
            onAjaxError(jqXHR, textStatus, errorThrown);
        }
    });

    function initializeActivityPieChart() {
        var myBarChart = new Chart(canvas_documents, {
            type: 'pie',
            data: {
                datasets: [{
                        data: pie_data,
                        //data: [21, 43, 93, 23, 74, 25, 94, 78],
                        backgroundColor: [
                            'rgb(139, 195, 63)',
                            'rgb(255, 51, 255)',
                            'rgb(179, 102, 255)',
                            'pink',
                            'rgb(0, 153, 153)',
                            'black',
                            'rgb(15, 82, 186)',
                            'rgb(255, 51, 51)'

                        ],
                        label: 'Dataset 1'
                    }],
                labels: [
                    'New Build',
                    'Relocation',
                    'Refurb',
                    'Limited',
                    'Event Kit',
                    'Closure',
                    'Rebuild',
                    'Emergency Server Build'
                ]
            },
            options: {
                title: {
                    display: true,
                    fontSize: 20,
                    text: 'Activities Complete by Type'
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});