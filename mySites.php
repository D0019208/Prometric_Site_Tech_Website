<?php
include_once 'header.php';
?> 
<!--Main Javascript-->
<script src="js/my_sites.js" type="text/javascript"></script>

<div class="bs-callout bs-callout-success"> 
    <h4>My Sites</h4> 
    <p>You can use this page to view all activities/sites you have taken part in and those still in progress. Hover over one of the entries to see the <b>Technician</b> responsible for the site, the <b>Expected Go Live Date</b>, the <b>Site Code</b> and the <b>FTS</b> technician/s. You can click on <b>Read More</b> to see the sites checklist or click on the technician name to see his/her profile.</p>
</div>

<!-- Text input-->
<div class="panel panel-default">
    <div class="panel-heading clearfix"> 
        <h3 class="panel-title">Filters</h3>
    </div>
    <div class="panel-body">
        <div class="form-group"> 
            <div class="col-md-4 inputGroupContainer filtersContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <select name="technician" class="form-control selectpicker" id="technician">
                        <option value="" disabled selected>Overnight Technician</option> 
                        <option value="All">All</option> 
                    </select>
                </div>
            </div> 

            <div class="col-md-4 inputGroupContainer filtersContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                    <select name="activityType" class="form-control selectpicker" id="activityType">
                        <option value="" disabled selected>Activity Type</option> 
                        <option value="All">All</option> 
                    </select>
                </div>
            </div>   

            <div class="col-md-4 inputGroupContainer filtersContainer">
                <div class="input-group dateInput">
                    <span class="input-group-addon dateInput"><i class="glyphicon glyphicon-lock"></i></span> 
                    <input type="text" name="siteCode" class="form-control" id="expectedGoLiveDate" placeholder="Site Code" autocomplete="off">
                </div>
            </div> 
        </div> 

        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4" class="greenButtonContainer" id="filterButtonContainer">
                <button type="submit" class="btn btn-success" id='filtersButtonSubmit'>Filter Sites Now!</button>
                <button type="submit" class="btn btn-success" id='filtersButtonReset'>Reset</button>
            </div> 
        </div>
    </div>
</div> 

<br>
<h1 class="display-4" style="text-align: center;">My Sites</h1> 
<div class='container-fluid' id="mySites"> </div>
<br>
<?php
include_once 'footer.php';
?> 