<?php
include_once 'header.php';
?> 

<header>    
    <!--CSS Libraries-->
    <link href="css/libraries/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/libraries/multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>

    <!--Javascript Libraries-->
    <script src="js/libraries/bootstrapvalidator.min.js" type="text/javascript"></script>   
    <script src="js/libraries/bootstrap-datetimepicker.js" type="text/javascript"></script>
    <script src="js/libraries/multiselect/bootstrap-multiselect.js" type="text/javascript"></script> 

    <!--Main JavaScript-->
    <script src="js/new_activity.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>  
</header>

<input type="hidden" name="technician" value="1">   

<div class="bs-callout bs-callout-success"> 
    <h4>New Activity</h4> 
    <p>You can use this page to start a new activity. If the activity is a new site build then the <b>Activity Type</b> MUST be a 'New Build' as the site does not yet exist. After you have completed a 'New Build' you can then start a new activity. Only 1 activity at a time can be running for a site.</p> 
    <p>Once you are ready just input the following:</p>
    <ul>
        <li><b>Site Code</b></li>
        <li><b>Site Name</b></li>
        <li><b>Country</b></li>
        <li><b>Site Type</b></li>
        <li><b>Site Region</b></li>
        <li><b>Activity Type</b></li>
        <li><b>Activity Type</b></li>
    </ul>
    <p>The website will then create the activity, corresponding checklist and update the <a href='teamCalender.php'>Team Calender</a> for you. For more information on each field click the <i class="glyphicon glyphicon-info-sign" id='example_info'></i> icon beside each field.</p>
</div>

<form class="well form-horizontal" action="" method="post"  id="activity_form">
    <fieldset> 
        <!-- Form Name -->
        <legend id="newSiteHeader">New Activity - Click on the icons to find out more about each field.</legend>

        <!-- Text input-->

        <div class="form-group">
            <label class="col-md-4 control-label" for="siteCode">Site Code</label>  
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span>
                    <input type="text" class="form-control" id="siteCode" name="siteCode" placeholder="Site Code"> 
                </div>
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label">Site Country</label>  
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span> 
                    <input type="text" name="siteCountry" class="form-control" id="siteCountry" placeholder="Site Country">
                </div>
            </div>
        </div> 

        <div class="form-group">
            <label class="col-md-4 control-label">Site County</label>  
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span> 
                    <input type="text" name="siteCounty" class="form-control" id="siteCounty" placeholder="Site County">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">Site Town</label>  
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span> 
                    <input type="text" name="siteTown" class="form-control" id="siteTown" placeholder="Site Town">
                </div>
            </div>
        </div>

        <div class="form-group date">
            <label class="col-md-4 control-label">Expected Go Live Date</label>  
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group dateInput">
                    <span class="input-group-addon dateInput"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span> 
                    <input type="text" name="expectedGoLiveDate" class="form-control" id="expectedGoLiveDate" placeholder="Expected Go Live Date" autocomplete="off">
                </div>
            </div>
        </div> 

        <!-- Select Basic -->     
        <div class="form-group"> 
            <label class="col-md-4 control-label">Site Type</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span>
                    <select name="siteType" class="form-control selectpicker" id="siteType">
                        <option value=" " disabled selected>Site Type</option> 
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group"> 
            <label class="col-md-4 control-label">Site Region</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span>
                    <select name="siteRegion" class="form-control selectpicker" id="siteRegion">
                        <option value=" " disabled selected>Site Region</option> 
                    </select>
                </div>
            </div>
        </div>  

        <div class="form-group"> 
            <label class="col-md-4 control-label">Activity Type</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span>
                    <select name="activityType" class="form-control selectpicker" id="activityType">
                        <option value=" " disabled selected>Activity Type</option> 
                    </select>
                </div>
            </div>
        </div>   

        <div class="form-group"> 
            <label class="col-md-4 control-label">Overnight Support</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign" data-toggle="modal" data-target="#siteCodeInfo"></i></span>
                    <select name="overnightSupport" class="form-control selectpicker" id="overnightSupport" multiple="multiple">
                        <!--                        <option value=" " disabled selected>Overnight Support</option> -->
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group"> 
            <label class="col-md-4 control-label">Cache Proxy Present?</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <label class="switch ">
                        <input type="checkbox" name='cacheProxy' state='off' value='Cache Proxy Ready' class="success optional">
                        <span class="slider round" tabindex="0"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group"> 
            <label class="col-md-4 control-label">NVR & LMC Present?</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <label class="switch ">
                        <input type="checkbox" name='NVR' state='off' value='DVR, Cameras, & Viewing Machine' class="success optional">
                        <span class="slider round" tabindex="0"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group"> 
            <label class="col-md-4 control-label">Email Machine Present?</label>
            <div class="col-md-4 selectContainer">
                <div class="input-group">
                    <label class="switch ">
                        <input type="checkbox" name='emailMachine' state='off' value='Email Machine' class="success optional">
                        <span class="slider round" tabindex="0"></span>
                    </label>
                </div>
            </div>
        </div> 

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4" class="greenButtonContainer">
                <button type="submit" class="btn btn-success" id='submit'>Create New Activity <span class="glyphicon glyphicon-send"></span></button>
            </div>
        </div>

    </fieldset>
</form>  
<br>
<div class="modal fade" id="siteCodeInfo" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalLabelSmall">Modal Title</h4>
            </div>

            <div class="modal-body">  
                <div id="modalIcon"></div>
                <div id="modalText"></div>
            </div>

        </div>
    </div>
</div>    
<?php
include_once 'footer.php';
?>  