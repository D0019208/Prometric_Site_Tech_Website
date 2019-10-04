<?php
require_once ("header.php");
?>
<!--CSS Libraries-->
<link href="css/libraries/w2ui/w2ui.min.css" rel="stylesheet" type="text/css"/>

<!--JavaScript Libraries-->
<script src="js/libraries/chart.min.js" type="text/javascript"></script>
<script src="js/libraries/w2ui/w2ui.min.js" type="text/javascript"></script>

<!--Main JavaScript-->
<script src="js/administrator_console.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>
<link href="css/profile.css" rel="stylesheet" type="text/css"/>
<style>
    .w2ui-col-header {
        text-align: center;
    } 

    .w2ui-marker {
        text-decoration: underline;
    }

    .panel {
        position: relative;
        width: 100%;
        margin-bottom: 10px;
        padding: 10px 17px;
        display: inline-block;
        background: #fff;
        border: 1px solid #E6E9ED;
        -webkit-column-break-inside: avoid;
        -moz-column-break-inside: avoid;
        column-break-inside: avoid;
        opacity: 1;
        transition: all .2s ease;
    }

    .panel_title {
        border-bottom: 2px solid #E6E9ED;
        padding: 1px 5px 6px;
        margin-bottom: 10px;
        text-align: center;
    }

    .panel_content {
        padding: 0 5px 6px;
        position: relative;
        width: 100%;
        float: left;
        clear: both;
        margin-top: 5px;
        text-align: center;
        min-height: 270px;
    }

    @media (min-width: 1200px)
    {
        #monthly_statistics
        {
            padding-left: 0;
        }
        #grant_access 
        {
            float: right; 
            padding-right: 0; 
        } 
    }

    .statistics_container
    {
        min-height: 147px !important;
    }

    div.form-group {
        height: 50px;
    }
    
    #btn {
        padding: 17px 34px; 
        font-size: 17px;
    }
</style>

<div class="bs-callout bs-callout-success"> 
    <h4>Administrator Console</h4> 
    <p>Welcome to the <b>Administrator Console</b>. Here you can see statistical data relating to Activities Complete, Activities in Progress and Upcoming Activities. You can also Add, Delete or Update technicians and Checklist Tasks.</p>
</div>  
<h1 class="display-4" style="text-align: center;">Activities Chart</h1>
<div id="chart_container" style="position: relative; height:1000px; border: 2px solid #eee;">
    <canvas id="barChart"></canvas> 
</div>  
<br>
<div class="row"> 
    <div class="col-md-6 col-sm-6 col-xs-12" id="monthly_statistics">
        <div class="panel">
            <div class="panel_title">
                <h2 id="statistics_heading">Busy Technicians</h2> 
            </div>
            <div class="panel_content">
                <div id="busy_techs_grid" style="width: 100%;"></div>
            </div>
        </div>
    </div>


    <div class="col-md-6 col-sm-6 col-xs-12" id="grant_access">
        <div class="panel">
            <div class="panel_title">
                <h2>Free Technicians</h2>
            </div> 
            <div class="panel_content"> 
                <div id="free_techs_grid" style="width: 100%;"></div>
            </div>
        </div>
    </div> 
</div>

<div style="text-align: center; border: 2px solid #eee; padding-bottom: 20px;">
    <h2>Administrator Panel</h2>
    <button class="w2ui-btn" id="btn">Open Panel</button>
</div>
<br>
<script>
    $(function () {
        $('#free_techs_grid').w2grid({
            name: 'free_techs_grid',
            url: 'jsonGenerator/get_free_techs.php',
            header: 'Technicians - Free',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarReload: false,
                toolbarColumns: false,
            },
            searches: [
                {field: 'name', caption: 'Technician Name', type: 'text'}
            ],
            columns: [
                {field: 'name', caption: 'Name:', size: '50%'}
            ],
            fixedBody: false
        });

        $('#busy_techs_grid').w2grid({
            name: 'busy_techs_grid',
            url: 'jsonGenerator/get_busy_techs.php',
            header: 'Technicians - Busy',
            show: {
                header: true,
                toolbar: true,
                footer: true,
                toolbarReload: false,
                toolbarColumns: false,
            },
            searches: [
                {field: 'name', caption: 'Technician Name', type: 'text'}
            ],
            columns: [
                {field: 'name', caption: 'Technician Name:', size: '100%'}
            ],
            fixedBody: false
        });
    });
</script>
<?php
require_once ("footer.php");
?>