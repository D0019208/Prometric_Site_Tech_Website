<?php
include_once 'header.php';
?>    
<!--Chart JavaScript-->
<script src="js/libraries/chart.min.js" type="text/javascript"></script>

<!--Main JavaScript-->
<script src="js/profile.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>
<!--Main CSS-->
<link href="css/profile.css" rel="stylesheet" type="text/css"/>

<div class="bs-callout bs-callout-success"> 
    <h4>Profile</h4> 
    <p>This is your <b>Profile</b>, here you can change your avatar which everyone can see. You can also check out some useful statistics such as how many activities you currently have in progress, how many activities you have completed overall, how many documents you have uploaded, updated, deleted and how many days you have worked in Prometric (holidays included). You can also see a bar chart with all the activities you have completed, started or upcoming for the current month and a pie chart showing how many of each type of activity you have completed.</p>
</div>
<div class="avatar-upload">
    <div class="avatar-edit">
        <input type='file' name='image' id="imageUpload" accept=".png, .jpg, .jpeg" />
        <label for="imageUpload" data-toggle="tooltip" title="Click to change image"></label>
    </div>
    <div class="avatar-preview"> 
        <div id="imagePreview">
        </div>
    </div>
</div>

<h3 id='technicianName'></h3>

<div class="row tile_count">
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-edit"></i></div>
        <div class="count_top">Activities in Progress</div>
        <div class="count green" id='activitiesInProgress'>NaN</div> 
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-check"></i></div>
        <div class="count_top">Activities Completed</div>
        <div class="count green" id='activitiesCompleted'>NaN</div> 
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-cloud-upload"></i></div>
        <div class="count_top">Uploaded Documents</div>
        <div class="count green" id='documentsUploaded'>NaN</div> 
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-cloud-download"></i></div>
        <div class="count_top">Updated Documents</div>
        <div class="count green" id='documentsUpdated'>NaN</div> 
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-trash"></i></div>
        <div class="count_top">Documents Deleted</div>
        <div class="count green" id='documentsDeleted'>NaN</div> 
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count statistics_container">
        <div class='statsIcon'><i class="glyphicon glyphicon-calendar"></i></div>
        <div class="count_top">Days in Prometric</div>
        <div class="count green" id='yearsExperience'>99999</div> 
    </div> 
</div> 
<br>
<div class="row">




    <div class="col-md-6 col-sm-6 col-xs-12" id="monthly_statistics">
        <div class="panel">
            <div class="panel_title">
                <h2 id="statistics_heading">Monthly Statistics</h2> 
            </div>
            <div class="panel_content">
                <div id="chart_container_activities" style="position: relative; height:500px;">
                    <canvas id="activitiesBarChart"></canvas> 
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-6 col-sm-6 col-xs-12" id="grant_access">
        <div class="panel">
            <div class="panel_title">
                <h2>Activity Types Statistics</h2>
            </div> 
            <div class="panel_content"> 
                <div id="chart_container_documents" style="position: relative; height:500px;">
                    <canvas id="barChartDocuments"></canvas> 
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once 'footer.php';
?> 