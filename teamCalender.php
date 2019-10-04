<?php
include_once 'header.php';
?> 
<!--Calender CSS-->
<link href="css/libraries/fullcalendar/core.min.css" rel="stylesheet" type="text/css"/>
<link href="css/libraries/fullcalendar/daygrid.min.css" rel="stylesheet" type="text/css"/>
<link href="css/libraries/fullcalendar/timegrid.min.css" rel="stylesheet" type="text/css"/> 

<link href="css/libraries/fullcalendar/timeline.min.css" rel="stylesheet" type="text/css"/>
<link href="css/libraries/fullcalendar/resource-timeline.min.css" rel="stylesheet" type="text/css"/>

<!--Calender JavaScript-->
<script src="js/libraries/fullcalendar/core.min.js" type="text/javascript"></script>
<script src="js/libraries/fullcalendar/interaction.min.js" type="text/javascript"></script>
<script src="js/libraries/fullcalendar/daygrid.min.js" type="text/javascript"></script>
<script src="js/libraries/fullcalendar/timegrid.min.js" type="text/javascript"></script>

<script src="js/libraries/fullcalendar/timeline.min.js" type="text/javascript"></script>
<script src="js/libraries/fullcalendar/resource-common.min.js" type="text/javascript"></script>
<script src="js/libraries/fullcalendar/resource-timeline.min.js" type="text/javascript"></script>

<!--Main JavaScript-->
<script src="js/team_calender.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>

<link href="css/libraries/bootstrap-datetimepicker.min.css?random=<?php echo uniqid(); ?>" rel="stylesheet" type="text/css"/>
<script src="js/libraries/bootstrap-datetimepicker.js" type="text/javascript"></script>
<script src="js/libraries/bootstrapvalidator.min.js" type="text/javascript"></script>

<style>
    .fc-timeline-event {
        height: 30px !important;
    } 

    .fc-event {
        cursor: pointer;
    }

    .resource_active {
        cursor: pointer;
        text-decoration: underline;
    }

    .swal2-actions {
        z-index: 0;
    }

    i.form-control-feedback.glyphicon.glyphicon-ok {
        left: 217px;
    }

    i.form-control-feedback.glyphicon.glyphicon-remove {
        left: 217px;
    }

    #create_event_form > div.event_event > div > i {
        left: 225px;
    }
</style>  
<div class="bs-callout bs-callout-success"> 
    <h4>Team Calender</h4> 
    <p>This is the <b>Team Calender</b> here you can see what everyone on the team is up to. It is very similar to the event calender but it is more personalized, you can add activities, read the status of ongoing activities and much more!</p>
    <p>Here is a list of things you can do in the <b>Team Calender</b>:</p>
    <ul>
        <li><b><u>View events</u></b> in detail.</li>
        <li><b><u>Add new events</u></b> by clicking either <b>"Add Event"</b> OR by clicking a cell on your row.</li>
        <li>Move your events from one technician to another</li>
        <li>See what everyone is up to easily.</li>
        <li>Jump between <b><u>checklists</u></b> and <b><u>documents</u></b> by clicking the events.</li>
    </ul>
</div>

<div class='my-legend'>
    <div class='legend-title'>Event Color Map</div>
    <div class='legend-scale'>
        <ul class='legend-labels'>
            <li><span style='background:#5cb85c;'></span>Events Completed</li>
            <li><span style='background:#0f52ba;'></span>Events In Progress</li>
            <li><span style='background:#cccccc;'></span>Events Not Started</li>
        </ul>
    </div>
    <div class='legend-scale' style="float: right;">
        <ul class='legend-labels'>
            <li>Document Events<span style='background:#c6538c;'></span></li>
            <li>Upcoming Events<span style='background:#ff8080;'></span></li>
            <li>Other<span style='background:#ff99e6;'></span></li>
        </ul>
    </div>
    <div class='legend-source'></div>
</div>
<div class="loading"></div>
<div id='calendar'></div> 
<br>

<div class="modal fade" id="checklist_event" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="checklist_event_title"></h4>
            </div>

            <div class="modal-body" id='eventModal'>   
                <div class="form-group" style='text-align: center;'>
                    <p id='checklist_event_site_status'></p> 
                    <p id='checklist_event_site_code'></p>
                    <p id='checklist_event_country'></p>
                    <p id='checklist_event_county'></p>
                    <p id='checklist_event_town'></p> 
                    <p id='checklist_event_technicians'></p>
                    <p id='checklist_event_start_date'></p>
                    <p id='checklist_event_end_date'></p> 
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="go_to_checklist" style="background: #5cb85c; border: none; line-height: normal;">
                        Go to checklist
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="site_news_event" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="site_news_event_title"><b>Event Report</b></h4>
            </div>

            <div class="modal-body" id='eventModal'>   
                <div class="form-group" id="modal_news_event_container" style='text-align: center;'> 
                    <p id='site_news_event_event_description'></p> 
                    <p id='site_news_event_technician'></p>
                    <p id='site_news_event_start_date'></p>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="upcoming_site_event" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="upcoming_event_title"></h4>
            </div>

            <div class="modal-body" id='eventModal'>   
                <div class="form-group" style='text-align: center;'> 
                    <p id='upcoming_event_site_status'><i class='glyphicon glyphicon-wrench'></i><b> Site Status: </b>Upcoming</p> 
                    <p id='upcoming_event_site_code'></p>
                    <p id='upcoming_event_country'></p>
                    <p id='upcoming_event_county'></p>
                    <p id='upcoming_event_town'></p> 
                    <p id='upcoming_event_technicians'></p>
                    <p id='upcoming_event_start_date'></p>
                    <p id='upcoming_event_end_date'></p> 
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="documents_event" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true">
    <div class="modal-dialog modal-m">
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="site_news_event_title"><b>Event Report</b></h4>
            </div>

            <div class="modal-body" id='eventModal'>   
                <div class="form-group" id="modal_documents_event_container" style='text-align: center;'> 
                    <p id='document_event_description'></p> 
                    <p id='document_technician'></p>
                    <p id='document_start_date'></p>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
include_once 'footer.php';
?> 