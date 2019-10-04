<?php

include_once 'header.php';
?>   
<!--Checklist CSS-->
<link href="css/libraries/w2ui/w2ui.min.css" rel="stylesheet" type="text/css"/>
<!--Checklist JavaScript-->
<script src="js/libraries/w2ui/w2ui.min.js" type="text/javascript"></script>

<!--Main JavaScript-->
<script src="js/site.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>
<style>
    .w2ui-grid .w2ui-grid-header
    { 
        background-image: linear-gradient(#a4d95b, #7ab52a);
    }

    .w2ui-tabs .w2ui-tab.active
    {
        background-color: #a4d95b; 
    }

    .completed
    {
        background-color: #a4d95b
    }

    .inProgress
    {
        background-color: #80bfff;
    }

    input[type='checkbox']{
        transform: scale(1.2); 
        margin: 0;
    }
</style> 
<div class="bs-callout bs-callout-success"> 
    <h4 id="siteCallout"></h4>  
    <ul>
        <li><u>Overview Tab</u> - Fill in the "Expected" columns with the time which you think will take to finish each category.</li>
        <li><u>Contact Tab</u> - Fill in the empty "Name" column with the name of the Test Center Administrator (TCA) along with his/her phone number, the main technicians phone number and the phone number(s) of the Overnight Support Technician(s).</li>
        <li><u>Everything Else</u> - Once a task has been completed, click on the checkbox to mark it as "changed". Make sure to save any changes before closing the browser.</li>
    </ul>
</div> 
<div id="layout" style="width: 100%; height: 850px;"></div>
<?php
include_once 'footer.php';