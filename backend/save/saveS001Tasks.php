<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

//All data posted from grid
$json = json_decode($_POST["request"]);

//Assigning the data to variables
$checklistID = $json->key;
$changesObject = $json->changes;
$categoriesObject = $json->category;
$siteCode = $json->siteCode;

//User data
$technician = $json->technician;
$technician = ltrim(rtrim(filter_var($technician, FILTER_SANITIZE_STRING)));
$overnightSupport = $json->overnightSupport;
$email = $json->email; 

$timezone = $json->timezone; 

date_default_timezone_set($timezone);
$time = date('Y/m/d h:i:s', time());

//categories
$categories = json_decode(json_encode($categoriesObject), True);
$primaryCategory = $categories[0];
$secondaryCategory = '%' . $categories[1] . '%';

//Turn object to Array
// Array (  
//      [0] => Array ( [recid] => 8 [XenServerInstallationComplete] => 1 )
//  )
$changes = json_decode(json_encode($changesObject), True);

//Other variables
//This is the assoc array that will contain the structure of the checklist with the ID's of each induvidual task.
//Array ( 
//    [0] => Array ( [ITQuotesRecID] => 13828 [S001MigrationRecID] => 13829 [XenServerRecID] => 13832 [S001BuilRecID] => 13830 ) 
//    [1] => Array ( [ITQuotesRecID] => null [S001MigrationRecID] => null [XenServerRecID] => 13833 [S001BuilRecID] => 13831 )
// ....
//      )
$recIDArray = array();
$recIDInsideArray;
$iterator;
$done = false;

try {
    $s001TasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName = :primaryCategory AND checklistID = :checklistID";
    $s001TasksStatement = $db->prepare($s001TasksQuery);
    $s001TasksStatement->bindParam(":primaryCategory", $primaryCategory, PDO::PARAM_STR);
    $s001TasksStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $s001TasksStatement->execute();

    $primaryCategoryTasks = $s001TasksStatement->fetchAll(PDO::FETCH_NUM);

    $cabinetTasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName LIKE :secondaryCategory AND checklistID = :checklistID";
    $cabinetTasksStatement = $db->prepare($cabinetTasksQuery);
    $cabinetTasksStatement->bindParam(":secondaryCategory", $secondaryCategory, PDO::PARAM_STR);
    $cabinetTasksStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $cabinetTasksStatement->execute();

    $secondaryCategoryTasks = $cabinetTasksStatement->fetchAll(PDO::FETCH_NUM);

    $s001PreBuildTasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName LIKE '%S001 Pre Build%' AND checklistID = :checklistID";
    $s001PreBuildTasksStatement = $db->prepare($s001PreBuildTasksQuery);
    $s001PreBuildTasksStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $s001PreBuildTasksStatement->execute();

    $s001PreBuildTasks = $s001PreBuildTasksStatement->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}


$primaryTasksCount = count($primaryCategoryTasks);
$secondaryCategoryTasksCount = count($secondaryCategoryTasks);
$s001PreBuildTasksCount = count($s001PreBuildTasks);


if ($primaryTasksCount > $secondaryCategoryTasksCount) {
    $iterator = $primaryTasksCount;
} else {
    $iterator = $secondaryCategoryTasksCount;
}


for ($i = 0; $i < $iterator; $i++) {

    if (!$done) {
        $recIDInsideArray["ITQuotesRecID"] = $s001PreBuildTasks[0][0];
        $recIDInsideArray["S001MigrationRecID"] = $s001PreBuildTasks[1][0];

        $done = true;
    } else {
        $recIDInsideArray["ITQuotesRecID"] = "";
        $recIDInsideArray["S001MigrationRecID"] = "";
    }

    if (isset($secondaryCategoryTasks[$i])) {
        $recIDInsideArray["XenServerRecID"] = $secondaryCategoryTasks[$i][0];
    } else {
        $recIDInsideArray["XenServerRecID"] = "";
    }

    if (isset($primaryCategoryTasks[$i])) {
        $recIDInsideArray["S001BuilRecID"] = $primaryCategoryTasks[$i][0];
    } else {
        $recIDInsideArray["S001BuilRecID"] = "";
    }

    array_push($recIDArray, $recIDInsideArray);
}

$categoriesArray["ITQuotesRecID"] = "ITQuotesComplete";
$categoriesArray["S001MigrationRecID"] = "S001MigrationComplete";
$categoriesArray["XenServerRecID"] = "XenServerInstallationComplete";
$categoriesArray["S001BuilRecID"] = "S001BuildConfigurationComplete";

$changedCount = count($changes);

//Loop through the changes array
//$changes
// Array (  
//      [0](key) => Array ( [recid] => 8 [XenServerInstallationComplete] => 1 ) (value)
//  )
foreach ($changes as $key => &$value) {
    // Access the value of the changes array
    // $changes
    // Array ( 
    //    [recid]<--(innerChangesKey) => 8<--(innerChangesValue) [XenServerInstallationComplete] => 1 (boolean) 
    // )
    foreach ($changes[$key] as $innerChangesKey => $innerChangesValue) {
        if ($innerChangesKey !== "recid") {
            //  $categoriesArray
            //  Array ( 
            //      [ITQuotesRecID]<--(categoriesKey) => ITQuotesComplete<--(categoriesValue)    
            //      [S001MigrationRecID] => S001MigrationComplete
            //      [XenServerRecID] => XenServerInstallationComplete
            //      [S001BuilRecID] => S001BuildConfigurationComplete
            //  )
            // Here we loop through the categories array and we check to see what category the changed object belongs to
            // and if the task has been completed or not. If it has been completed we update the database and remove the
            // object as we do not need it anymore.
            //
            // If the task was NOT completed e.g. a completed task unchecked, we update the database and state that the task
            // has not been completed. Afterwards, we again remove the object as we do not need it anymore.
            foreach ($categoriesArray as $categoriesKey => $categoriesValue) {
                if ($innerChangesKey === $categoriesValue && $value[$categoriesValue] === true) {
                    $query = "UPDATE checklist_checklistTasks SET taskCompleted = 'true' WHERE ID = :ID";
                    $statement = $db->prepare($query);
                    $statement->bindParam(":ID", $recIDArray[$recid][$categoriesKey], PDO::PARAM_INT);
                    $statement->execute();

                    unset($changes[$key][$categoriesValue]);
                } else if ($innerChangesKey === $categoriesValue && $value[$categoriesValue] === false) {
                    $query = "UPDATE checklist_checklistTasks SET taskCompleted = 'false' WHERE ID = :ID";
                    $statement = $db->prepare($query);
                    $statement->bindParam(":ID", $recIDArray[$recid][$categoriesKey], PDO::PARAM_INT);
                    $statement->execute();

                    unset($changes[$key][$categoriesValue]);
                }
            }
        } else {
            $recid = $innerChangesValue;
        }
    }
}

$totalCompletedTasks = $primaryTasksCount + $secondaryCategoryTasksCount + $s001PreBuildTasksCount;
$secondaryCategoryStatus = 0;
$primaryCategoryStatus = 0;

try {
    $s001TasksCompleteQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName = :primaryCategory AND checklistID = :checklistID AND taskCompleted = 'true'";
    $s001TasksCompleteStatement = $db->prepare($s001TasksCompleteQuery);
    $s001TasksCompleteStatement->bindParam(":primaryCategory", $primaryCategory, PDO::PARAM_STR);
    $s001TasksCompleteStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $s001TasksCompleteStatement->execute();

    $primaryCategoryCompleteResult = $s001TasksCompleteStatement->fetchAll(PDO::FETCH_NUM);
    $primaryCategoryCompleteCount = $primaryCategoryCompleteResult[0][0];


    $cabinetTasksCompleteQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName LIKE :secondaryCategory AND checklistID = :checklistID AND taskCompleted = 'true'";
    $cabinetTasksCompleteStatement = $db->prepare($cabinetTasksCompleteQuery);
    $cabinetTasksCompleteStatement->bindParam(":secondaryCategory", $secondaryCategory, PDO::PARAM_STR);
    $cabinetTasksCompleteStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $cabinetTasksCompleteStatement->execute();

    $secondaryCategoryCompleteResult = $cabinetTasksCompleteStatement->fetchAll(PDO::FETCH_NUM);
    $secondaryCategoryCompleteCount = $secondaryCategoryCompleteResult[0][0];

    $s001PreBuildCompleteQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName LIKE '%S001 Pre Build%' AND checklistID = :checklistID AND taskCompleted = 'true'";
    $s001PreBuildCompleteStatement = $db->prepare($s001PreBuildCompleteQuery);
    $s001PreBuildCompleteStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $s001PreBuildCompleteStatement->execute();

    $s001PreBuildCompleteResult = $s001PreBuildCompleteStatement->fetchAll(PDO::FETCH_NUM);
    $s001PreBuildCompleteCount = $s001PreBuildCompleteResult[0][0];
} catch (Exception $ex) {
    echo $exception->getMessage();
    exit();
}

//echo "Primary Category: " . $primaryCategoryCompleteCount;
//echo "<br>S001 Pre Build: " . $s001PreBuildCompleteCount;
//echo "<br>Primary Task Count: " . $primaryTasksCount;
//echo "<br>Secondary Category Complete Count: " . $secondaryCategoryCompleteCount;
//echo "<br>Secondary Category Task Count: " . $secondaryCategoryTasksCount;

if ($primaryCategoryCompleteCount === $primaryTasksCount && $s001PreBuildCompleteCount >= 1 || $primaryCategoryCompleteCount === $primaryTasksCount && $secondaryCategoryCompleteCount === $secondaryCategoryTasksCount) {
    $primaryCategoryStatus = 3;
} else if ($primaryCategoryCompleteCount >= 1 || $primaryCategoryCompleteCount < $primaryTasksCount || $s001PreBuildCompleteCount >= 1 || $secondaryCategoryCompleteCount >= 1) {
    $primaryCategoryStatus = 2;
} else if ($primaryCategoryCompleteCount < 1 || $s001PreBuildCompleteCount === 0) {
    $primaryCategoryStatus = 1;
}

if ($secondaryCategoryCompleteCount === $secondaryCategoryTasksCount || $s001PreBuildCompleteCount >= 1) {
    $secondaryCategoryStatus = 3;
} else if ($secondaryCategoryCompleteCount >= 1 && $secondaryCategoryCompleteCount < $secondaryCategoryTasksCount) {
    $secondaryCategoryStatus = 2;
} else if ($secondaryCategoryCompleteCount < 1 || $s001PreBuildCompleteCount === 0) {
    $secondaryCategoryStatus = 1;
}

try {
    $updatePrimaryCategoryStatusQuery = "UPDATE checklist_checklistCategories SET categoryStatus = '" . $primaryCategoryStatus . "' WHERE categoryName = :primaryCategoryName AND checklistID = :checklistID";
    $updatePrimaryCategoryStatusStatement = $db->prepare($updatePrimaryCategoryStatusQuery);
    $updatePrimaryCategoryStatusStatement->bindParam(":primaryCategoryName", $primaryCategory, PDO::PARAM_STR);
    $updatePrimaryCategoryStatusStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $primaryResponse = $updatePrimaryCategoryStatusStatement->execute();

    $updateSecondaryCategoryStatusQuery = "UPDATE checklist_checklistCategories SET categoryStatus = '" . $secondaryCategoryStatus . "' WHERE categoryName = :secondaryCategoryName AND checklistID = :checklistID";
    $updateSecondaryCategoryStatusStatement = $db->prepare($updateSecondaryCategoryStatusQuery);
    $updateSecondaryCategoryStatusStatement->bindParam(":secondaryCategoryName", $categories[1], PDO::PARAM_STR);
    $updateSecondaryCategoryStatusStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $secondaryResponse = $updateSecondaryCategoryStatusStatement->execute();
} catch (Exception $ex) {
    echo $exception->getMessage();
    exit();
}

echo setStatus($db, $siteCode, $checklistID, $technician, $overnightSupport, $time, false, $checklistID);
