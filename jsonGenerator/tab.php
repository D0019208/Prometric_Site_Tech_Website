<?php

require_once ("../database/database.php");
require_once ("../backend/functions.php");

$checklistID = ltrim(rtrim(filter_input(INPUT_POST, "checklistID", FILTER_SANITIZE_STRING)));
//$checklistID = 60;

try {
    //MANDATORY tabs
    $tabsQuery = "SELECT ID, tabName, tabs_identifier FROM checklist_checklistTabs WHERE checklistID = :checklistID";
    $tabsStatement = $db->prepare($tabsQuery);
    $tabsStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $tabsStatement->execute();

    $tabsArray = $tabsStatement->fetchAll(PDO::FETCH_NUM); 
    //OPTIONAL Tabs  
    $optionalTabsQuery = "SELECT checklist_checklistOptionalTabs.ID, checklist_checklistOptionalTabs.optionalTabName, checklist_checklistOptionalTabs.optional_tabs_identifier, status.status FROM checklist_checklistOptionalTabs, status WHERE status.statusID = checklist_checklistOptionalTabs.optionalTabStatus AND checklistID = :checklistID";
    //echo $optionalTabsQuery; exit();
    $optionalTabsStatement = $db->prepare($optionalTabsQuery);
    $optionalTabsStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
    $optionalTabsStatement->execute();

    $optionalTabsArray = $optionalTabsStatement->fetchAll(PDO::FETCH_NUM);
    
    $all_optional_categories_query = "SELECT optionalTabName, optional_tabs_identifier FROM checklistOptionalTabs";
    $all_optional_categories_statement = $db->prepare($all_optional_categories_query);
    $all_optional_categories_statement->execute();
    
    $all_optional_categories_result = $all_optional_categories_statement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}

$jsonArray = [];
//JSON columns
$jsonColumnsArray = array("id", "caption");

//Add JSON columns
for ($j = 0; $j < count($jsonColumnsArray); $j++) {
    $key = $jsonColumnsArray[$j];
    $value = '';

    $jsonArray[$key] = $value;
}

if ($optionalTabsStatement->rowCount() > 0) {
    $cntr = 0;
    $index;
    $val;
    $newArr = $tabsArray;

    //Add the optional tabs that exist here
    $optionalTabs = array(); //= array("C001 Build", "NVR & LMC", "Email Machine");
    
    foreach($all_optional_categories_result as $value)
    {
        array_push($optionalTabs, array($value["optionalTabName"], $value["optional_tabs_identifier"])); 
    }
    
    //Add the category which you wish to add the optional category AFTER so e.g. "Cache Proxy Ready" will be added AFTER "Finalize Server Setup"
    //IMPORTANT: MUST CHANGE THIS!!!! OTHERWISE IF WRONG TAB NAME IS CHANGED WHOLE THING WILL CRASH!!!!! Perhaps check the identifiers then add them to this array
    $tabsToEnterAfter = array();
    $optional_tabs_count = count($optionalTabs);
    
    for($i = 0; $i < $optional_tabs_count; $i++)
    {
        if($i == 0)
        {
            array_push($tabsToEnterAfter, 'TCDDC & Xen Desktop Tab');
        }
        else
        {
            array_push($tabsToEnterAfter, 'Endpoints & VM Configurations Tab');
        }
    } 
   
//    echo "Optional Tabs| ";
//    print_r($optionalTabs);
//    echo " |optional tabs array| ";
    //print_r($optionalTabs);
//    echo " |tabs array| ";
//    print_r($tabsArray); 
    //exit();
    
    //Add the optional categories
    for ($i = 0; $i < count($tabsArray); $i++) {
        for ($j = 0; $j < count($optionalTabsArray); $j++) {
            for ($k = 0; $k < count($tabsToEnterAfter); $k++) {
                if ($tabsArray[$i][2] == $tabsToEnterAfter[$k] && $optionalTabsArray[$j][2] == $optionalTabs[$k][1]) {
                    $index = $i + 1;
                    $val = $optionalTabsArray[$j];
                    $newArr = insert($newArr, $index, $val);

                    $cntr++;

//                        if ($cntr == 1) {
//                            print_r($newArr);
//                            exit();
//                        }
                }
            }
        }
    }

    $tabsArray = $newArr;
}

//Build the JSON string
$jsonToSendBack = '[';
$idUsed = false;
$captionUsed = false;
$counter = 0;
$categoriesArrayCountIf = count($tabsArray) - 1;

for ($i = 0; $i < count($tabsArray); $i++) {
    foreach ($jsonArray as $key => $value) {
        if ($idUsed === false) {
            $jsonArray[$key] = $tabsArray[$i][2];
            $idUsed = true;
        } else if ($captionUsed === false) {

            $jsonArray[$key] = $tabsArray[$i][1];
            $captionUsed = true;
        }

        if ($counter == count($jsonArray) - 1) {

            //If loop has ended (all columns filled) Reset
            $captionUsed = false;
            $idUsed = false;

            $encodedJsonArray = json_encode($jsonArray);
            $jsonToSendBack .= $encodedJsonArray;

            if ($i < $categoriesArrayCountIf) {
                $jsonToSendBack .= ',';
            } else {
                $jsonToSendBack .= "]";
            }
            $counter = 0;
        } else {

            $counter++;
        }
    }
}

//print_r($optionalTabsArray); exit();
echo $jsonToSendBack;
