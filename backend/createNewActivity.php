<?php

require_once ("../database/database.php");
require_once ("functions.php");
require_once ("class_lib.php");

$siteCode = ltrim(rtrim(filter_input(INPUT_POST, "siteCode", FILTER_SANITIZE_STRING)));
if(empty($siteCode))
{
    echo "Ooops, the Site Code seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$timezone = ltrim(rtrim(filter_input(INPUT_POST, "timezone", FILTER_SANITIZE_STRING)));
if(empty($timezone))
{
    echo "Ooops, the timezone seems to have either got lost in transaction!";
    exit();
}

$activityType = ltrim(rtrim(filter_input(INPUT_POST, "activityType", FILTER_SANITIZE_STRING)));
if(empty($activityType))
{
    echo "Ooops, the Activity Type seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
if(empty($technician))
{
    echo "Ooops, the Technician name seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$expectedGoLiveDate = ltrim(rtrim(filter_input(INPUT_POST, "goLiveDate", FILTER_SANITIZE_STRING)));
if(empty($expectedGoLiveDate))
{
    echo "Ooops, the Expected Go Live Date seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$siteType = ltrim(rtrim(filter_input(INPUT_POST, "siteType", FILTER_SANITIZE_STRING)));
if(empty($technician))
{
    echo "Ooops, the Technician seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

if(isset($_POST["overnightSupport"]))
{
    $overNightSupport = $_POST["overnightSupport"];
}
else 
{
    echo "Ooops, the Overnight Support Technician name seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit(); 
} 

$optionalFieldSelected = ltrim(rtrim(filter_input(INPUT_POST, "optionalFieldSelected", FILTER_VALIDATE_BOOLEAN)));
//////////////////////////////////
if ($optionalFieldSelected == true) {
    $optionalFields = $_POST["optionalValues"];
} else {
    $optionalFields = "";
}

if (!isset($overNightSupport)) {
    $overNightSupport = "";
}

date_default_timezone_set($timezone);
//////////////////////////////////

if ($activityType == "New Build") {
    //Check if the site entered by user is complete to begin Activity 
    checkIfSiteExists($db, $siteCode, $activityType);

    $siteCountry = ltrim(rtrim(filter_input(INPUT_POST, "siteCountry", FILTER_SANITIZE_STRING)));
    $siteCounty = ltrim(rtrim(filter_input(INPUT_POST, "siteCounty", FILTER_SANITIZE_STRING)));
    $siteTown = ltrim(rtrim(filter_input(INPUT_POST, "siteTown", FILTER_SANITIZE_STRING)));
    $goLiveDate = ltrim(rtrim(filter_input(INPUT_POST, "goLiveDate", FILTER_SANITIZE_STRING)));
    $siteRegion = ltrim(rtrim(filter_input(INPUT_POST, "siteRegion", FILTER_SANITIZE_STRING)));
    
    check_if_site_upcoming($db, $siteCode, $siteCountry);
    
    $checklistHeader = $siteCode . ' ' . $siteTown . ' - ' . $activityType;

    //Create two new objects, one for the site and one for the checklist
    $ActivitySite = new Site($siteCode, $siteTown, $siteCountry, $siteCounty, $siteTown, $siteType, $siteRegion, $activityType, $technician, $overNightSupport, $expectedGoLiveDate);
    $ActivityChecklist = new Checklist($siteCode, $siteTown, $siteCountry, $siteCounty, $siteTown, $siteType, $siteRegion, $activityType, $technician, $overNightSupport, $expectedGoLiveDate, $checklistHeader, $optionalFields);

    //Column Names (Might be able to get these from database so more automatic) Right now if database changes, these need to be changed too.
    $siteCategoryIds = ["siteCode", "siteRegion", "status", "siteType", "siteName", "siteCountry", "siteCounty", "siteTown", "siteAddress", "siteTech"];
    //Column Names with the values from the client. Again, it might be possible to get the column values from the database and update it accordingly. 
    $siteInsertValues = Array(["siteCode" => $siteCode, "siteRegion" => $siteRegion, "status" => "1", "siteType" => $siteType, "siteName" => $siteTown, "siteCountry" => $siteCountry, "siteCounty" => $siteCounty, "siteTown" => $siteTown, "siteAddress" => "dadsa", "siteTech" => $technician, "allDayEvent" => true]);

    //Create all records required for the site
    $siteResponse = $ActivitySite->createSite($db, $siteInsertValues, $siteCategoryIds, $ActivitySite);

    //$siteResponse = "Success";

    if ($siteResponse !== "Success") {
        echo $siteResponse;
    } else {
        
        $createdOn = date('Y/m/d h:i:s', time());
        //Column Names (Might be able to get these from database so more automatic) Right now if database changes, these need to be changed too.
        $checklistCategoryIds = ["siteCode", "siteName", "checklistHeader", "expectedGoLiveDate", "createdOn", "activityType", "siteType", "technician", "overNightSupport", "complete", "status", "allDayEvent"];
        //Column Names with the values from the client. Again, it might be possible to get the column values from the database and update it accordingly. 
        $checklistInsertValues = Array(["siteCode" => $siteCode, "siteName" => $siteTown, "checklistHeader" => $checklistHeader, "expectedGoLiveDate" => $expectedGoLiveDate, "createdOn" => $createdOn, "activityType" => $activityType, "siteType" => $siteType, "technician" => $technician, "overNightSupport" => $overNightSupport[0], "complete"=>"false", "status" => 1, "allDayEvent" => true]);
        //Create all records required for the checklist 
        $checklistResponse = $ActivityChecklist->createChecklist($db, $ActivityChecklist, $checklistInsertValues, $checklistCategoryIds, $optionalFieldSelected, $overNightSupport);

        if ($checklistResponse !== "Success") {
            echo $checklistResponse;
            exit();
        } else {

            $event = "Site " . $siteCode . " - " . $activityType;
            $image = "images/SiteIcons/SiteActivityType/" . $activityType . ".png";
            $image = str_replace(' ', '', $image);
            $link = "site.php?checklistID=" . $ActivityChecklist->getChecklistID();
            
            $liveSiteEventResponse = createLiveSiteEvent($db, $event, $technician, $image, $siteCode, $link, $ActivityChecklist->getChecklistID());
            echo $liveSiteEventResponse;
            exit();
        }
    }
} else {
    $siteCountry = ltrim(rtrim(filter_input(INPUT_POST, "siteCountry", FILTER_SANITIZE_STRING)));
    $siteTown = ltrim(rtrim(filter_input(INPUT_POST, "siteTown", FILTER_SANITIZE_STRING)));
    //Check if the site entered by user is complete to begin Activity
    checkIfActivityExists($db, $siteCode, $activityType);
    check_if_site_upcoming($db, $siteCode, $siteCountry);
    //Get all the information from the existing site needed to create a checklist for the Activity
    $siteDataActivity = getSiteDataActivity($db, $siteCode);

    //Loop through the result and fill in the Checklist Object
    foreach ($siteDataActivity as $row) {
        $checklistHeader = $siteCode . ' ' . $row["siteTown"] . ' - ' . $activityType;
        $ActivityChecklist = new Checklist($siteCode, $row["siteTown"], $row["siteCountry"], $row["siteCounty"], $row["siteCountry"], $row["siteType"], $row["region"], $activityType, $technician, $overNightSupport, $expectedGoLiveDate, $checklistHeader, $optionalFields);
    }

    //Values and Columns for creating the checklist, might get from database in future.
    $createdOn = date('Y/m/d h:i:s', time());
    $checklistCategoryIds = ["siteCode", "siteName", "checklistHeader", "expectedGoLiveDate", "createdOn", "activityType", "siteType", "technician", "overNightSupport", "complete", "status", "allDayEvent"];
    $checklistInsertValues = Array(["siteCode" => $siteCode, "siteName" => $siteTown, "checklistHeader" => $checklistHeader, "expectedGoLiveDate" => $expectedGoLiveDate, "createdOn" => $createdOn, "activityType" => $activityType, "siteType" => $siteType, "technician" => $technician, "overNightSupport" => $overNightSupport[0], "complete"=>"false", "status" => 1, "allDayEvent" => true]);

    //Create Activity Checklist
    $siteResponse = $ActivityChecklist->createChecklist($db, $ActivityChecklist, $checklistInsertValues, $checklistCategoryIds, $optionalFieldSelected, $overNightSupport);

    if ($siteResponse !== "Success") {
        echo $siteResponse;
        exit();
    } else {
        try {
            //Add the technician who is going to be working on the Activity/Site to the database
            $activityTechnicianQuery = "INSERT INTO site_technician (siteCode, technician) VALUES (:siteCode, :technician)";
            $activityTechnicianStatement = $db->prepare($activityTechnicianQuery);
            $activityTechnicianStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $activityTechnicianStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
            $activityTechnicianStatement->execute();

            //Add activity type to the database
            $query = "INSERT INTO site_siteActivityType (siteCode, activityType) VALUES (:siteCode, :activityType)";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $statement->bindParam(":activityType", $activityType, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    $event = "Site " . $siteCode . " - " . $ActivityChecklist->getActivityType();
    $image = "images/SiteIcons/SiteActivityType/" . $activityType . ".png";
    $image = str_replace(' ', '', $image);
    $link = "site.php?checklistID=" . $ActivityChecklist->getChecklistID();
    
    $liveSiteEventResponse = createLiveSiteEvent($db, $event, $technician, $image, $siteCode, $link, $ActivityChecklist->getChecklistID());

    echo $liveSiteEventResponse;
}
    