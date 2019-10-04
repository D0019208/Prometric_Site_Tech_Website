<?php
include_once 'header.php';
?> 
<!--CSS Libraries-->
<link href="css/libraries/w2ui/w2ui.min.css" rel="stylesheet" type="text/css"/>

<!--JavaScript Libraries-->
<script src="js/libraries/chart.min.js" type="text/javascript"></script>
<script src="js/libraries/w2ui/w2ui.min.js" type="text/javascript"></script>

<script src="js/documents.js" type="text/javascript"></script>

<div class="bs-callout bs-callout-success"> 
    <h4>Documents</h4> 
    <p>Welcome to the <b>Documents</b> page. Here you can view, edit and delete all the Site Technology Documents. <b style="color: red;">***WARNING***</b> only a <b style="color: red;"><u>PDF</u></b> version of the file may be uploaded!</p>
    <p>Here is a list of things you can do in the <b>Documents Panel</b>:</p>
    <ul>
        <li><b><u>View documents</u></b> in tabs allowing to view many documents simultaneously.</li>
        <li><b><u>Upload new documents</u></b> to a desired folder.</li> 
        <li><b><u>Update documents</u></b> if they have been changed.</li> 
        <li><b><u>Delete folders</u></b> that are no longer needed. <b style="color: red;">***NOTE*** (Folder must be empty!)</b></li>
        <li><b><u>Delete documents</u></b> that are no longer needed.</li>
    </ul>
</div>  
<div id="documents" style="width: 100%; height: 650px;">></div>
<?php
include_once 'footer.php';
?> 