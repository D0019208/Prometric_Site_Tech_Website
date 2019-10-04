<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");

$json = json_decode($_POST["request"]);
$key = $json->key;
//$key = '71'; 

try {

    $checklistQuery = "SELECT siteCode, siteName, siteType, activityType, createdOn, expectedGoLiveDate,  technician, overNightSupport FROM checklist WHERE checklistID = :checklistID";
    $checklistStatement = $db->prepare($checklistQuery);
    $checklistStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $checklistStatement->execute();

    $checklistArray = $checklistStatement->fetchAll(PDO::FETCH_NUM);

    $overNightSupportQuery = "SELECT technician, siteCode FROM overnightSupport WHERE checklistID = :checklistID";
    $overNightSupportStatement = $db->prepare($overNightSupportQuery);
    $overNightSupportStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $overNightSupportStatement->execute();

    $overNightSupportArray = $overNightSupportStatement->fetchAll(PDO::FETCH_ASSOC);

    $categoriesQuery = "SELECT checklist_checklistCategories.ID, checklist_checklistCategories.categoryName, status.status, checklist_checklistCategories.expectedCompletionTime, checklist_checklistCategories.completedOn FROM checklist_checklistCategories, status WHERE status.statusID = checklist_checklistCategories.categoryStatus AND checklist_checklistCategories.checklistID = :checklistID ORDER BY checklist_checklistCategories.ID";
    //$categoriesQuery = "SELECT checklist_checklistCategories.categoryName, status.status FROM checklist_checklistCategories, status WHERE status.statusID = checklist_checklistCategories.categoryStatus AND checklist_checklistCategories.checklistID = :checklistID";
    $categoriesStatement = $db->prepare($categoriesQuery);
    $categoriesStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $categoriesStatement->execute();

    $categoriesArray = $categoriesStatement->fetchAll(PDO::FETCH_NUM);

    $optionalCategoriesQuery = "SELECT checklist_checklistOptionalCategories.ID, checklist_checklistOptionalCategories.optionalCategoryName, status.status, checklist_checklistOptionalCategories.expectedCompletionTime, checklist_checklistOptionalCategories.completedOn FROM checklist_checklistOptionalCategories, status WHERE status.statusID = checklist_checklistOptionalCategories.optionalCategoryStatus AND checklist_checklistOptionalCategories.checklistID = :checklistID";
    $optionalCategoriesStatement = $db->prepare($optionalCategoriesQuery);
    $optionalCategoriesStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $optionalCategoriesStatement->execute();

    $optionalCategoriesArray = $optionalCategoriesStatement->fetchAll(PDO::FETCH_NUM);

    $tableColumnQuery = "SELECT column_name,table_schema FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name='checklist' AND column_name !='checklistID' AND column_name !='allDayEvent' AND column_name !='status' AND column_name !='checklistHeader' AND column_name !='complete'";
    $tableColumnStatement = $db->prepare($tableColumnQuery);
    $tableColumnStatement->execute();

    $tableColumnsArray = $tableColumnStatement->fetchAll(PDO::FETCH_NUM);

    $all_optional_categories_query = "SELECT optionalCategoryName FROM checklistOptionalCategories";
    $all_optional_categories_statement = $db->prepare($all_optional_categories_query);
    $all_optional_categories_statement->execute();

    $all_optional_categories_result = $all_optional_categories_statement->fetchAll(PDO::FETCH_ASSOC);
    
    $insert_after_identifier_query = "SELECT categoryName FROM checklist_checklistCategories WHERE categories_identifier = 'Workstations - Ready' AND checklistID = :checklistID";
    $insert_after_identifier_statement = $db->prepare($insert_after_identifier_query);
    $insert_after_identifier_statement->bindParam(":checklistID", $key, PDO::PARAM_STR);
    $insert_after_identifier_statement->execute();

    $insert_after_identifier_result = $insert_after_identifier_statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo $exception->getMessage();
}

//JSON columns
//$jsonColumnsArray = array("recid", "siteDetailsColumn", "siteDetailsColumnValue", "categories", "progress", "expected", "completedOn", "completed");
$jsonColumnsArray = array("recid", "siteDetailsColumn", "siteDetailsColumnValue", "categories", "progress", "expected", "completedOn");
//Used for merging the optional categories with the mandatory categories
$index;
$val;
$newArr = $categoriesArray;

//Add the optional categories that exist here
$optionalCategories = array();
$categoriesToEnterAfter = array();
foreach ($all_optional_categories_result as $value) {
    array_push($optionalCategories, $value["optionalCategoryName"]);
}

$optionalCategoriesCount = count($optionalCategories);

//Add the category which you wish to add the optional category AFTER so e.g. "Cache Proxy Ready" will be added AFTER "Finalize Server Setup"
foreach ($insert_after_identifier_result as $value)
{
    for($i = 0; $i < $optionalCategoriesCount; $i++)
    {
        array_push($categoriesToEnterAfter, $value["categoryName"]);
    }
}  

//Build the JSON string
$jsonToSendBack = '{ "records" : [';
$jsonArray = [];
$cntr = 0;
//Check if this site has optional categories
if ($optionalCategoriesStatement->rowCount() > 0) {
//Add the optional categories
    for ($i = 0; $i < count($categoriesArray); $i++) {
        for ($j = 0; $j < count($optionalCategoriesArray); $j++) {
            for ($k = 0; $k < count($optionalCategories); $k++) {
                if ($categoriesArray[$i][1] == $categoriesToEnterAfter[$k] && $optionalCategoriesArray[$j][1] == $optionalCategories[$k]) {
                    $index = $i + 1;
                    $val = $optionalCategoriesArray[$j];
                    $newArr = insert($newArr, $index, $val);

//                    $cntr++;
//
//                    if ($cntr == 2) {
//                        print_r($newArr);
//                        exit();
//                    }
                }
            }
        }
    }

    $categoriesArray = $newArr;
}

$overNightSupport = "";
$count = 0;

//DO NOT, change the position of overnightSupport column in the database or this won't work.
foreach ($overNightSupportArray as $key => $value) {
    for ($i = 0; $i < count($checklistArray[0]); $i++) {
        if ($overNightSupportArray[$key]["siteCode"] == $checklistArray[0][$i]) {
            $overNightSupport .= $overNightSupportArray[$key]["technician"];

            if ($count !== count($overNightSupportArray) - 1) {
                $overNightSupport .= ", ";
            }
        }
    }

    $count++;
}
$checklistArray[0][count($checklistArray[0]) - 1] = $overNightSupport;

//$checklistArray = Array ( [0] => Array ( [0] => 1234 [1] => Drogheda [2] => Corporate [3] => New Build [4] => 2019-04-25 04:15:00 [5] => Nichita Postolachi [6] => Charlie Dowd ) );
//$categoriesArray = Array ( [0] => Array ( [0] => Server Base Setup [1] => Not Started ) [1] => Array ( [0] => Communications Backend Setup [1] => Not Started ) [2] => Array ( [0] => Server Inside Cabinet and Site Communicating [1] => Not Started ) [3] => Array ( [0] => Admin & Workstation Ready for Deployment [1] => Not Started ) [4] => Array ( [0] => Finalize Server Setup [1] => Not Started ) [5] => Array ( [0] => Endpoints & VM's [1] => Not Started ) [6] => Array ( [0] => Demo Testing [1] => Not Started ) [7] => Array ( [0] => Final Checks (Pre Go-Live) [1] => Not Started ) [8] => Array ( [0] => 10 Day Post Live Review of Site [1] => Not Started ) [9] => Array ( [0] => Contact [1] => Not Started ) )
//$optionalCategoriesArray = Array ( [0] => Array ( [0] => Cache Proxy Ready [1] => Not Started ) [1] => Array ( [0] => DVR, Cameras, & LMC Machine [1] => Not Started ) )
//$tableColumnsArray = Array ( [0] => Array ( [0] => siteCode [1] => d00192082_sitetechwebsite ) [1] => Array ( [0] => siteName [1] => d00192082_sitetechwebsite ) [2] => Array ( [0] => siteType [1] => d00192082_sitetechwebsite ) [3] => Array ( [0] => activityType [1] => d00192082_sitetechwebsite ) [4] => Array ( [0] => expectedGoLiveDate [1] => d00192082_sitetechwebsite ) [5] => Array ( [0] => technician [1] => d00192082_sitetechwebsite ) [6] => Array ( [0] => overNightSupport [1] => d00192082_sitetechwebsite ) )
$jsonColumnsCount = count($jsonColumnsArray);

for ($j = 0; $j < $jsonColumnsCount; $j++) {
    $key = $jsonColumnsArray[$j];
    $value = '';

    $jsonArray[$key] = $value;
}

//We use this to check how the JSON string looks like at a specific index
$debugger = 0;
//Keep count of the index (similar to debugger)
$counter = 0;

//Counters
$categoriesArrayCountLoop = count($categoriesArray);
$categoriesArrayCountIf = count($categoriesArray) - 1;
$checklistInsideArrayCount = count($checklistArray[0]);


$tableColumnNameArrayCount = 0;
$checklistArrayCount = 0;
//Loop through first array in categories array
$categoryArrayCount = 0;
//Loop through inner array in categories array
$categoryArrayInsideCount = 1;

$progressIndex = 0;


//If value/column set, we set this variable to true so as to not add it again until the JSON row is complete after which we start again
$columnNameUsed = false;
$checklistUsed = false;
$categoriesOuterUsed = false;
$progressUsed = false;
$expectedUsed = false;
$recIDSet = false;
//$categoriesCompleteUsed = false;
$completedOnUsed = false;

for ($i = 0; $i < $categoriesArrayCountLoop; $i++) {
    foreach ($jsonArray as $key => $value) {
        if ($recIDSet == false) {
            $jsonArray[$key] = $categoriesArray[$categoryArrayCount][0];
            $recIDSet = true;
        } else if ($columnNameUsed == false) {
            //Add siteDetailsColumn Value
            if (!empty($tableColumnsArray[$tableColumnNameArrayCount])) {
                $row = $tableColumnsArray[$tableColumnNameArrayCount][0];

                $capitalizeRow = preg_replace('/(?<!\ )[A-Z]/', ' $0', $row);
                $newRow = ucwords($capitalizeRow);
                $noFirstSpaceWord = ltrim($newRow);

                $jsonArray[$key] = "<div style='text-align: center;'>" . $noFirstSpaceWord . "</div>";
                $tableColumnNameArrayCount++;
                $columnNameUsed = true;
            } else {
                $jsonArray[$key] = "";
                $columnNameUsed = true;
            }
        } else if ($checklistUsed == false) {
            //Add siteDetailsColumnValue value 
            if ($checklistArrayCount < $checklistInsideArrayCount) {
                //Might need to potentially change that 0
                $jsonArray[$key] = "<div style='text-align: center;'>" . $checklistArray[0][$checklistArrayCount] . "</div>";
                $checklistArrayCount++;
                $checklistUsed = true;
            } else {
                $jsonArray[$key] = "";
                $checklistUsed = true;
            }
        } else if ($categoriesOuterUsed == false) {
            //Add categories value
            if ($categoryArrayCount < $categoriesArrayCountIf) {
                $jsonArray[$key] = "<div style='text-align: center;'>" . $categoriesArray[$categoryArrayCount][$categoryArrayInsideCount] . "</div>";

                $categoryArrayInsideCount++;

                $categoriesOuterUsed = true;
            } else {
                $jsonArray[$key] = "<div style='text-align: center;'>" . $categoriesArray[$categoryArrayCount][$categoryArrayInsideCount] . "</div>";
                $categoryArrayInsideCount++;

                $categoriesOuterUsed = true;
            }
        } else if ($progressUsed == false) {
            //Add progress value  
            $jsonArray[$key] = $categoriesArray[$categoryArrayCount][$categoryArrayInsideCount];
            $progressIndex = $categoryArrayInsideCount;
            $categoryArrayInsideCount++;
            $progressUsed = true;
        } else if ($expectedUsed == false) {
            //Add expected value
            $jsonArray[$key] = $categoriesArray[$categoryArrayCount][$categoryArrayInsideCount];
            $categoryArrayInsideCount++;
            $expectedUsed = true;
        } else if ($completedOnUsed == false) {
            //Add completed on value
            $jsonArray[$key] = $categoriesArray[$categoryArrayCount][$categoryArrayInsideCount];
            $completedOnUsed = true;

            //TEMPORARY, MOVE BACK TO BELOW ELSE IF STATEMENT WHEN DONE
            $categoryArrayCount++;
            $categoriesCompleteUsed = false;
            $categoryArrayInsideCount = 1;
        }
//        else if ($categoriesCompleteUsed == false) {
//            if ($categoriesArray[$categoryArrayCount][$progressIndex] === "Complete") {
//                $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" name="categoryComplete" checked></div>';
//            } else {
//                $jsonArray[$key] = '<div style="text-align: center;"><input type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" name="categoryComplete" unchecked></div>';
//            }
//            
//            $categoryArrayCount++;
//            $categoriesCompleteUsed = false;
//            $categoryArrayInsideCount = 1;
//        }

        if ($counter == count($jsonArray) - 1) {

            //If loop has ended (all columns filled) Reset
            $columnNameUsed = false;
            $checklistUsed = false;
            $categoriesOuterUsed = false;
            $progressUsed = false;
            $expectedUsed = false;
            $recIDSet = false;
            $completedOnUsed = false;
            //$categoriesCompleteUsed = false;

            $encodedJsonArray = json_encode($jsonArray);
            $jsonToSendBack .= $encodedJsonArray;

            if ($i < $categoriesArrayCountIf) {
                $jsonToSendBack .= ',';
            } else {
                $jsonToSendBack .= "]}";
            }
            $counter = 0;
        } else {

            $counter++;
        }

//        if ($debugger == 6) { 
//            print_r($jsonToSendBack);
//            
//            exit();
//        }
//
//        $debugger++;
    }
}

echo $jsonToSendBack;
?>