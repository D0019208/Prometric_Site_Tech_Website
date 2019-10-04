<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key;
$tabName = $json->tab;
$categoriesObject = $json->category;

$categories = json_decode(json_encode($categoriesObject), True);
$primaryCategory = $categories[0];
$secondaryCategory = '%' . $categories[1] . '%';

try {
    $s001TasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName = :primaryCategory AND checklistID = :checklistID";
    $s001TasksStatement = $db->prepare($s001TasksQuery);
    $s001TasksStatement->bindParam(":primaryCategory", $primaryCategory, PDO::PARAM_STR);
    $s001TasksStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $s001TasksStatement->execute();

    $primaryCategoryTasks = $s001TasksStatement->fetchAll(PDO::FETCH_NUM);

    $cabinetTasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName LIKE :secondaryCategory AND checklistID = :checklistID";
    $cabinetTasksStatement = $db->prepare($cabinetTasksQuery);
    $cabinetTasksStatement->bindParam(":secondaryCategory", $secondaryCategory, PDO::PARAM_STR);
    $cabinetTasksStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $cabinetTasksStatement->execute();

    $secondaryCategoryTasks = $cabinetTasksStatement->fetchAll(PDO::FETCH_NUM);



    $s001TasksCompleteQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName = :primaryCategory AND checklistID = :checklistID AND taskCompleted = 'true'";
    $s001TasksCompleteStatement = $db->prepare($s001TasksCompleteQuery);
    $s001TasksCompleteStatement->bindParam(":primaryCategory", $primaryCategory, PDO::PARAM_STR);
    $s001TasksCompleteStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $s001TasksCompleteStatement->execute();

    $primaryCategoryCompleteResult = $s001TasksCompleteStatement->fetchAll(PDO::FETCH_NUM);
    $primaryCategoryCompleteCount = $primaryCategoryCompleteResult[0][0];


    $cabinetTasksCompleteQuery = "SELECT COUNT(*) FROM checklist_checklistTasks WHERE categoryName LIKE :secondaryCategory AND checklistID = :checklistID AND taskCompleted = 'true'";
    $cabinetTasksCompleteStatement = $db->prepare($cabinetTasksCompleteQuery);
    $cabinetTasksCompleteStatement->bindParam(":secondaryCategory", $secondaryCategory, PDO::PARAM_STR);
    $cabinetTasksCompleteStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $cabinetTasksCompleteStatement->execute();

    $secondaryCategoryCompleteResult = $cabinetTasksCompleteStatement->fetchAll(PDO::FETCH_NUM);
    $secondaryCategoryCompleteCount = $secondaryCategoryCompleteResult[0][0];


    $s001PreBuildTasksQuery = "SELECT ID, taskName, taskCompleted FROM checklist_checklistTasks WHERE categoryName LIKE '%S001 Pre Build%' AND checklistID = :checklistID";
    $s001PreBuildTasksStatement = $db->prepare($s001PreBuildTasksQuery);
    $s001PreBuildTasksStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $s001PreBuildTasksStatement->execute();

    $s001PreBuildTasks = $s001PreBuildTasksStatement->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}


$jsonArray = [];
//JSON columns
$jsonColumnsArray = array("recid", "ITQuotes", "ITQuotesComplete", "S001Migration", "S001MigrationComplete", "XenServerInstallation", "XenServerInstallationComplete", "S001BuildConfiguration", "S001BuildConfigurationComplete");
//Add JSON columns
for ($j = 0; $j < count($jsonColumnsArray); $j++) {
    $key = $jsonColumnsArray[$j];
    $value = '';

    $jsonArray[$key] = $value;
}

//Build the JSON string
$jsonToSendBack = '{ "records" : [';
$primaryTasksCount = count($primaryCategoryTasks);
$secondaryCategoryTasksCount = count($secondaryCategoryTasks);
$iterator;
$s001PreBuildCount = count($s001PreBuildTasks);
$counter = 0;
$insideTaskArray = 0;
$taskArrayCount = 0;
$debugger = 0;
$taskInsideArrayCount = 0;

$s001PreBuildTasksCounter = 0;

if ($primaryTasksCount > $secondaryCategoryTasksCount) {
    $iterator = $primaryTasksCount;
    $taskInsideArrayCount = count($primaryCategoryTasks[0]);
} else {
    $iterator = $secondaryCategoryTasksCount;
    $taskInsideArrayCount = count($secondaryCategoryTasks[0]);
}



$recIDSet = false;
$itQuotesSet = false;
$itQuotesProgressSet = false;
$s001MigrationSet = false;
$s001MigrationProgressSet = false;
$cabinetInstallSet = false;
$cabinetInstallProgressSet = false;
$s001BuildSet = false;
$s001BuilProgressSet = false;

$itQuotesCompleted = false;
$s001MigrationCompleted = false;

$itQuotesStatus;
$s001MigrationStatus;
$cabinetStatus; 
$s001BuildStatus;  

//Cabinet Summary
if($secondaryCategoryCompleteCount === $secondaryCategoryTasksCount)
{
    $cabinetStatus = "Complete";
}
else if($secondaryCategoryCompleteCount >= 1)
{
    $cabinetStatus = "In Progress";
}
else
{
    $cabinetStatus = "Not Started";
}

//S001 Build Summary
if($primaryCategoryCompleteCount === $primaryTasksCount)
{
    $s001BuildStatus = "Complete";
}
else if($primaryCategoryCompleteCount >= 1)
{
    $s001BuildStatus = "In Progress";
}
else
{
    $s001BuildStatus = "Not Started";
}

for ($i = 0; $i < $iterator; $i++) {
    foreach ($jsonArray as $key => $value) {
        if ($recIDSet == false) {
            $jsonArray[$key] = $i;

            $recIDSet = true;
        } else if ($itQuotesSet == false) {
            //Add siteDetailsColumn Value
            if (isset($s001PreBuildTasks[$s001PreBuildTasksCounter])) {
                $jsonArray[$key] = $s001PreBuildTasks[$s001PreBuildTasksCounter][1];
                $itQuotesSet = true;

//                $insideTaskArray++;
            } else {
                $jsonArray[$key] = "";
                $itQuotesSet = true;
            }
        } else if ($itQuotesProgressSet == false) {
            //Add siteDetailsColumnValue value 
            if (isset($s001PreBuildTasks[$s001PreBuildTasksCounter])) {

                if ($s001PreBuildTasks[$s001PreBuildTasksCounter][2] === "true") {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" recID="' . $s001PreBuildTasks[$s001PreBuildTasksCounter][0] . '" checked name="categoryComplete" onclick="toggleCheckbox(this, ' . $i . ')"></div>';

                    $itQuotesStatus = "Complete";
                } else {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" recID="' . $s001PreBuildTasks[$s001PreBuildTasksCounter][0] . '" unchecked name="categoryComplete" onclick="toggleCheckbox(this, ' . $i . ')"></div>';
                    $itQuotesStatus = "Not Started";
                }

                $itQuotesProgressSet = true;
                $s001PreBuildTasksCounter++;
                //$insideTaskArray++;
            } else {
                $jsonArray[$key] = "";
                $itQuotesProgressSet = true;
            }
        } else if ($s001MigrationSet == false) {
            //Add categories value
            if (isset($s001PreBuildTasks[$s001PreBuildTasksCounter])) {
                $jsonArray[$key] = $s001PreBuildTasks[$s001PreBuildTasksCounter][1];
                $s001MigrationSet = true;

//                $insideTaskArray++;
            } else {
                $jsonArray[$key] = "";
                $s001MigrationSet = true;
            }

            $s001MigrationSet = true;
        } else if ($s001MigrationProgressSet == false) {
            //Add progress value  
            if (isset($s001PreBuildTasks[$s001PreBuildTasksCounter])) {
                if ($s001PreBuildTasks[$s001PreBuildTasksCounter][2] === "true") {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" checked recID="' . $s001PreBuildTasks[$s001PreBuildTasksCounter][0] . '" onclick="toggleCheckbox(this, ' . $i . ')"></div>';
                    
                   $s001MigrationStatus = "Complete";
                } else {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" unchecked recID="' . $s001PreBuildTasks[$s001PreBuildTasksCounter][0] . '" onclick="toggleCheckbox(this, ' . $i . ')"></div>';
                
                     $s001MigrationStatus = "Not Started";
                }


                $s001PreBuildTasksCounter++;
            } else {
                $jsonArray[$key] = "";
            }

            $s001MigrationProgressSet = true;
        } else if ($cabinetInstallSet == false) {
            //Add expected value
            if (isset($secondaryCategoryTasks[$i])) {
                $jsonArray[$key] = $secondaryCategoryTasks[$i][1];

                $cabinetInstallSet = true;
            } else {
                $jsonArray[$key] = "";

                $cabinetInstallSet = true;
            }
        } else if ($cabinetInstallProgressSet == false) {
            if (isset($secondaryCategoryTasks[$i])) {
                if ($secondaryCategoryTasks[$i][2] === "true") {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="toggleCheckbox(this, ' . $i . ')" name="categoryComplete" recID="' . $secondaryCategoryTasks[$i][0] . '" checked></div>';
                } else {
                    $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="toggleCheckbox(this, ' . $i . ')" name="categoryComplete" recID="' . $secondaryCategoryTasks[$i][0] . '" unchecked></div>';
                }
            } else {
                $jsonArray[$key] = "";
            }

            $cabinetInstallProgressSet = true;
        } else if ($s001BuildSet == false) {
            if (isset($primaryCategoryTasks[$i])) {
                $jsonArray[$key] = $primaryCategoryTasks[$i][1];
            } else {
                $jsonArray[$key] = "";
            }

            $s001BuildSet = true;
        } else if ($s001BuilProgressSet == false) {
            if ($primaryCategoryTasks[$i][2] === "true") {
                $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" name="categoryComplete" recID="' . $primaryCategoryTasks[$i][0] . '" checked></div>';
            } else {
                $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="transform: scale(1.2); margin-left: 1px;" name="categoryComplete" recID="' . $primaryCategoryTasks[$i][0] . '" unchecked></div>';
            }

            $s001BuilProgressSet = true;
        }

        if ($counter == count($jsonArray) - 1) {

            //If loop has ended (all columns filled) Reset
            $recIDSet = false;
            $itQuotesSet = false;
            $itQuotesProgressSet = false;
            $s001MigrationSet = false;
            $s001MigrationProgressSet = false;
            $cabinetInstallSet = false;
            $cabinetInstallProgressSet = false;
            $s001BuildSet = false;
            $s001BuilProgressSet = false;

            $encodedJsonArray = json_encode($jsonArray);
            $jsonToSendBack .= $encodedJsonArray;

            if ($i < $iterator - 1) {
                $jsonToSendBack .= ',';
            } else {
                $jsonToSendBack .= ",{w2ui: {summary: true},recid: 'S-1', ITQuotes: 'S001 Pre Build Complete', ITQuotesComplete: '<div style=\"text-align: center;\"><span>" . $itQuotesStatus . "</span></div>', S001Migration: 'S001 Pre Build Complete', S001MigrationComplete: '<div style=\"text-align: center;\"><span>" . $s001MigrationStatus . "</span></div>', XenServerInstallation: 'Cabinet Install Status', XenServerInstallationComplete: '<div style=\"text-align: center;\"><span>" . $cabinetStatus . "</span></div>', S001BuildConfiguration: 'S001 Configuration Status', S001BuildConfigurationComplete: '<div style=\"text-align: center;\"><span>" . $s001BuildStatus . "</span></div>'}]}";
            }
            $counter = 0;
        } else {

            $counter++;
        }

//        if ($debugger == 16) {
//            print_r($jsonToSendBack . "]}");
//            //print_r($primaryCategoryTasks);
//            //print_r($s001PreBuildTasks);
//            //print_r($secondaryCategoryTasks);            
//            exit();
//        }
//        $debugger++;
    }
}


echo $jsonToSendBack;
