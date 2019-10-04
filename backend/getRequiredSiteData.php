<?php

require_once ("../database/database.php");
require_once ("functions.php");
//Allows only specific piece of code to execute
$accessCode = ltrim(rtrim(filter_input(INPUT_POST, "accessCode", FILTER_SANITIZE_STRING)));
//$accessCode = "mySites";

if ($accessCode == "createSite") {
    $technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));

    try {
        //Create one big query using UNION ALL to increase performance by not having to create 3 seperate queries.
        $siteTypeSiteRegionQuery = "SELECT siteTypeID, siteType, 'siteType' as source FROM siteType UNION ALL SELECT regionID AS regionID, region, 'region'  FROM region UNION ALL SELECT activityID AS activityID, activityType, 'activityType' FROM siteActivityType UNION ALL SELECT technicianID, technicianFullName, 'technician' FROM technician";
        $siteTypeSiteRegionStatement = $db->prepare($siteTypeSiteRegionQuery);
        $siteTypeSiteRegionStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $siteTypeSiteRegionStatement->execute();

        $siteTypeSiteRegionSiteActivityResult = $siteTypeSiteRegionStatement->fetchAll(PDO::FETCH_ASSOC);

        $optionalCategoryNamesQuery = "SELECT optionalCategoryName FROM checklistOptionalCategories";
        $optionalCategoryNamesStatement = $db->prepare($optionalCategoryNamesQuery);
        $optionalCategoryNamesStatement->execute();

        $optionalCategoryNamesResult = $optionalCategoryNamesStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
//    $siteCategoryNameQuery = "SELECT activityID, activityType, 'activityType' as source FROM siteActivityType";
//    $siteCategoryNameStatement = $db->prepare($siteCategoryNameQuery);
//    $siteCategoryNameeStatement->execute();
//
//    $siteCategoryNameResult = $siteCategoryNameStatement->fetchAll(PDO::FETCH_ASSOC);
//    
//    $result = array_merge($siteTypeSiteRegionSiteActivityResult, $siteCategoryNameResult);

    echo json_encode(array_merge(array($siteTypeSiteRegionSiteActivityResult), array($optionalCategoryNamesResult)));
} else if ($accessCode == "headerAndFooter") {
    try {
        $technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
        if (!empty($technician)) {
            $activities_complete_query = "SELECT workingSince, technicianFullName FROM technician WHERE technicianFullName = '" . $technician . "'";
            $statement = $db->prepare($activities_complete_query);
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $workingSince = new DateTime($result[0]["workingSince"]);
            $today = new DateTime('2020-09-05');
            //$today = new DateTime(date("Y-m-d"));

            $difference = $today->diff($workingSince);

            //print_r($difference);

            $date = date("Y-m-d");
            $time = date("H:i:s");

            if ($difference->y >= 1 && $difference->m == 0 && $difference->d == 0) {
                $anniversary_suffix;

                if ($difference->y == 1) {
                    $anniversary_suffix = "year";
                } else {
                    $anniversary_suffix = "years";
                }

                $event = $result[0]["technicianFullName"] . " celebrates " . $difference->y . " " . $anniversary_suffix . " at Prometric! Happy anniversary " . explode(' ', $result[0]["technicianFullName"])[0] . " from Nichita!";

                $check_event_query = "SELECT event FROM other_events WHERE event = '" . $event . "'";
                $check_event_statement = $db->prepare($check_event_query);
                $check_event_statement->execute();

                $check_event_result = $check_event_statement->fetchAll(PDO::FETCH_ASSOC);

                if (count($check_event_result) == 0) {
                    $query = "INSERT INTO other_events (event, start, technician, link, event_type, image, allDayEvent) VALUES ('" . $event . "', '" . $date . " " . $time . "', '" . $result[0]["technicianFullName"] . "', '#', 'other', 'images/SiteIcons/ModalIcons/ActivityType.png', 0)";
                    $statement = $db->prepare($query);
                    $statement->execute();
                }
            }
        }

        //Create one big query using UNION ALL to increase performance by not having to create 3 seperate queries.
        $headerFooterQuery = "(SELECT 'upcomingSiteEvents' as source, 'none' as event_type, technician, event, image, link, DATE_FORMAT(date, '%d-%m-%Y') as date, time FROM upcomingSiteEvents ORDER BY Month(date) DESC, date DESC, time DESC LIMIT 7) UNION ALL (SELECT 'liveSiteEvents' as source, 'none' as event_type, technician, event, image, link, DATE_FORMAT(date, '%d-%m-%Y'), time FROM liveSiteEvents WHERE status != 3 ORDER BY Month(date) DESC, date DESC, time DESC LIMIT 7) UNION ALL (SELECT 'siteNews' as source, 'none' as event_type, technician, event, image, link, DATE_FORMAT(date, '%d-%m-%Y'), time FROM siteNews ORDER BY Month(date) DESC, date DESC, time DESC LIMIT 7) UNION ALL (SELECT 'documentEvents' as source, 'none' as event_type, technician, event, image, link, DATE_FORMAT(date, '%d-%m-%Y'), time FROM documentEvents ORDER BY Month(date) DESC, date DESC, time DESC LIMIT 7) UNION ALL (SELECT 'documentEvents' as source, event_type, technician, event, image, link, start as 'date', end as 'time' FROM other_events ORDER BY start DESC LIMIT 7)";
        $headerFooterStatement = $db->prepare($headerFooterQuery);
        $headerFooterStatement->execute();

        $headerFooterResult = $headerFooterStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
//    $siteCategoryNameQuery = "SELECT activityID, activityType, 'activityType' as source FROM siteActivityType";
//    $siteCategoryNameStatement = $db->prepare($siteCategoryNameQuery);
//    $siteCategoryNameeStatement->execute();
//
//    $siteCategoryNameResult = $siteCategoryNameStatement->fetchAll(PDO::FETCH_ASSOC);
//    
//    $result = array_merge($siteTypeSiteRegionSiteActivityResult, $siteCategoryNameResult); 


    array_multisort(array_map('strtotime', array_column($headerFooterResult, 'date')), SORT_DESC, array_map('strtotime', array_column($headerFooterResult, 'time')), SORT_DESC, $headerFooterResult);

    echo json_encode($headerFooterResult);
} else if ($accessCode == "mySites") {
    //$technician = "Nichita Postolachi";
    $technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));

    try {
        $mySitesQuery = "SELECT DISTINCT checklist.siteCode, checklist.technician, status.status, region.region, checklist.createdOn, checklist.checklistID, site.siteCountry, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport FROM checklist INNER JOIN site ON checklist.siteCode=site.siteCode INNER JOIN status ON status.statusID = checklist.status INNER JOIN region ON region.regionID = site.siteRegion INNER JOIN overnightSupport ON checklist.checklistID = overnightSupport.checklistID WHERE checklist.technician = :technician OR overnightSupport.technician = :overnight_technician ORDER BY checklist.createdOn DESC";
        $mySitesStatement = $db->prepare($mySitesQuery);
        $mySitesStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $mySitesStatement->bindParam(":overnight_technician", $technician, PDO::PARAM_STR);
        $mySitesStatement->execute();

        $mySitesStatementResult = $mySitesStatement->fetchAll(PDO::FETCH_ASSOC);

        $mySitesQuery1 = "SELECT siteCode, checklistID, technician FROM overnightSupport";
        $mySitesStatement1 = $db->prepare($mySitesQuery1);
        $mySitesStatement1->execute();

        $mySitesStatementResult1 = $mySitesStatement1->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    foreach ($mySitesStatementResult as $key => $field) {
        $tempSupport = $mySitesStatementResult[$key]["overNightSupport"];
        $mySitesStatementResult[$key]["overNightSupport"] = array();

        foreach ($mySitesStatementResult1 as $key1 => $field1) {
            if ($mySitesStatementResult[$key]["siteCode"] == $mySitesStatementResult1[$key1]["siteCode"] && $mySitesStatementResult[$key]["checklistID"] == $mySitesStatementResult1[$key1]["checklistID"]) {
                array_push($mySitesStatementResult[$key]["overNightSupport"], $mySitesStatementResult1[$key1]["technician"]);
            }
        }
    }

    echo json_encode($mySitesStatementResult);
} else if ($accessCode == "loadFilters") {
    try {
        //Create one big query using UNION ALL to increase performance by not having to create 3 seperate queries.
        $siteTypeSiteRegionQuery = "SELECT regionID AS regionID, region, 'region' AS source  FROM region UNION ALL SELECT activityID AS activityID, activityType, 'activityType' FROM siteActivityType UNION ALL SELECT technicianID, technicianFullName, 'technician' FROM technician UNION ALL SELECT statusID, status, 'status' FROM status UNION ALL SELECT siteTypeID, siteType, 'siteType' FROM siteType";
        $siteTypeSiteRegionStatement = $db->prepare($siteTypeSiteRegionQuery);
        $siteTypeSiteRegionStatement->execute();

        $siteTypeSiteRegionSiteActivityResult = $siteTypeSiteRegionStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    echo json_encode($siteTypeSiteRegionSiteActivityResult);
} else if ($accessCode == "mySitesFilter") {
    $technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));

    $filtersArray = array_map('array_filter', $_POST["filtersArray"]);
    $filtersArray = array_filter($filtersArray);

    $newFiltersArray = [];
    $count = 0;

    for ($i = 0; $i < count($filtersArray); $i++) {
        if (count($filtersArray[$i]) === 3) {
            $newFiltersArray[$count] = $filtersArray[$i];
            $count++;
        }
    }

    if (count($newFiltersArray) < 1) {
        $whereClause = "";
    } else {
        $whereClause = "WHERE ";
    }


    $filter_array_count = count($newFiltersArray);
    for ($i = 0; $i < $filter_array_count; $i++) {
        $whereClause .= $newFiltersArray[$i][2] . "." . $newFiltersArray[$i][0];

        if ($newFiltersArray[$i][1] === "All") {
            $whereClause .= " LIKE ";
            $whereClause .= "'%'";
        } else {
            if ($newFiltersArray[$i][0] === "siteCode") {
                $whereClause .= " = ";
                $whereClause .= "'" . $newFiltersArray[$i][1] . "'";
            } else {
                $whereClause .= " = ";
                $whereClause .= "'" . $newFiltersArray[$i][1] . "'";
            }
        }

        if ($i < count($newFiltersArray) - 1) {
            $whereClause .= " AND ";
        }
    }

    try {
        //Create one big query using UNION ALL to increase performance by not having to create 3 seperate queries.
        $siteTypeSiteRegionQuery = "SELECT DISTINCT checklist.checklistID, checklist.siteCode, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport, checklist.technician, checklist.siteType, checklist.createdOn, region.region, status.status, site.siteCountry FROM checklist INNER JOIN site AS site ON site.siteCode = checklist.siteCode INNER JOIN region AS region ON site.siteRegion = region.regionID INNER JOIN status ON checklist.status = status.statusID INNER JOIN overnightSupport ON overnightSupport.checklistID = checklist.checklistID " . $whereClause . "  ORDER BY checklist.createdOn DESC";
        //echo $siteTypeSiteRegionQuery;
        //exit();
        $siteTypeSiteRegionStatement = $db->prepare($siteTypeSiteRegionQuery);
        $siteTypeSiteRegionStatement->execute();

        $siteTypeSiteRegionSiteActivityResult = $siteTypeSiteRegionStatement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($siteTypeSiteRegionSiteActivityResult as $key => $value) {
            $overNightSupportAppend = "";
            $checklistID = $value["checklistID"];

            $tempQuery = "SELECT technician FROM overnightSupport WHERE checklistID = '" . $checklistID . "'";
            $tempStatement = $db->prepare($tempQuery);
            $tempStatement->execute();

            $response = $tempStatement->fetchAll(PDO::FETCH_ASSOC);


            $tempCount = count($response);

            for ($i = 0; $i < $tempCount; $i++) {
                $overNightSupportAppend .= $response[$i]["technician"];

                if ($i < $tempCount - 1) {
                    $overNightSupportAppend .= ", ";
                }
            }

            $siteTypeSiteRegionSiteActivityResult[$key]["overNightSupport"] = $overNightSupportAppend;
        }

        echo json_encode($siteTypeSiteRegionSiteActivityResult);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
} else if ($accessCode == "allSitesFilter") {
    $filtersArray = array_map('array_filter', $_POST["filtersArray"]);
    $filtersArray = array_filter($filtersArray);

    $newFiltersArray = [];
    $count = 0;

    for ($i = 0; $i < count($filtersArray); $i++) {
        if (count($filtersArray[$i]) === 3) {
            $newFiltersArray[$count] = $filtersArray[$i];
            $count++;
        }
    }

    if (count($newFiltersArray) < 1) {
        $whereClause = "";
        $overnightWhereClause = "";
    } else {
        $whereClause = "WHERE ";
        $overnightWhereClause = "WHERE ";
    }

    $overnight_set = false;
    $filter_array_count = count($newFiltersArray);
    for ($i = 0; $i < $filter_array_count; $i++) {

        if ($newFiltersArray[$i][0] === "technician") {
            $overnightWhereClause .= "overnightSupport." . $newFiltersArray[$i][0];
            $overnight_set = true;
        } else {
            $overnightWhereClause .= $newFiltersArray[$i][2] . "." . $newFiltersArray[$i][0];
        }

        if ($newFiltersArray[$i][1] === 'All') {
            $overnight_set = false;
        }

        $whereClause .= $newFiltersArray[$i][2] . "." . $newFiltersArray[$i][0];

        if ($newFiltersArray[$i][1] === "All") {
            $whereClause .= " LIKE ";
            $whereClause .= "'%'";

            $overnightWhereClause .= " LIKE ";
            $overnightWhereClause .= "'%'";
        } else {
            if ($newFiltersArray[$i][0] === "siteCode") {
                $whereClause .= " = ";
                $whereClause .= "'" . $newFiltersArray[$i][1] . "'";

                $overnightWhereClause .= " = ";
                $overnightWhereClause .= "'" . $newFiltersArray[$i][1] . "'";
            } else {
                $whereClause .= " = ";
                $whereClause .= "'" . $newFiltersArray[$i][1] . "'";

                $overnightWhereClause .= " = ";
                $overnightWhereClause .= "'" . $newFiltersArray[$i][1] . "'";
            }
        }

        if ($i < count($newFiltersArray) - 1) {
            $whereClause .= " AND ";
            $overnightWhereClause .= " AND ";
        }
    }

    try {
        //Create one big query using UNION ALL to increase performance by not having to create 3 seperate queries.
        $siteTypeSiteRegionQuery = "SELECT DISTINCT checklist.checklistID, checklist.status, checklist.siteCode, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport, checklist.technician, checklist.siteType, checklist.createdOn, region.region, status.status, site.siteCountry FROM checklist INNER JOIN site AS site ON site.siteCode = checklist.siteCode INNER JOIN region AS region ON site.siteRegion = region.regionID INNER JOIN status ON checklist.status = status.statusID INNER JOIN overnightSupport ON overnightSupport.checklistID = checklist.checklistID " . $whereClause . "  ORDER BY checklist.createdOn DESC";
//        echo $siteTypeSiteRegionQuery;
//        exit();
        $siteTypeSiteRegionStatement = $db->prepare($siteTypeSiteRegionQuery);
        $siteTypeSiteRegionStatement->execute();

        $siteTypeSiteRegionSiteActivityResult = $siteTypeSiteRegionStatement->fetchAll(PDO::FETCH_ASSOC);

        if ($overnight_set) {
            $siteTypeSiteRegionQueryOvernight = "SELECT DISTINCT checklist.checklistID, checklist.status, checklist.siteCode, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport, checklist.technician, checklist.siteType, checklist.createdOn, region.region, status.status, site.siteCountry FROM checklist INNER JOIN site AS site ON site.siteCode = checklist.siteCode INNER JOIN region AS region ON site.siteRegion = region.regionID INNER JOIN status ON checklist.status = status.statusID INNER JOIN overnightSupport ON overnightSupport.checklistID = checklist.checklistID " . $overnightWhereClause . "  ORDER BY checklist.createdOn DESC";
            //echo $siteTypeSiteRegionQueryOvernight;
            //exit();
            $siteTypeSiteRegionStatementOvernight = $db->prepare($siteTypeSiteRegionQueryOvernight);
            $siteTypeSiteRegionStatementOvernight->execute();

            $siteTypeSiteRegionSiteActivityResultOvernight = $siteTypeSiteRegionStatementOvernight->fetchAll(PDO::FETCH_ASSOC);
        }

        $loopArray;
        if (empty($siteTypeSiteRegionSiteActivityResult)) {
            if (empty($siteTypeSiteRegionSiteActivityResultOvernight)) {
                $loopArray = $siteTypeSiteRegionSiteActivityResult;
                append_overnight_technicians($db, $siteTypeSiteRegionSiteActivityResult, true, $siteTypeSiteRegionSiteActivityResult, $siteTypeSiteRegionSiteActivityResultOvernight);
                append_overnight_technicians($db, $siteTypeSiteRegionSiteActivityResult, false, $siteTypeSiteRegionSiteActivityResult, $siteTypeSiteRegionSiteActivityResultOvernight);
            } else {
                $loopArray = $siteTypeSiteRegionSiteActivityResultOvernight;
                append_overnight_technicians($db, $siteTypeSiteRegionSiteActivityResultOvernight, true, $siteTypeSiteRegionSiteActivityResult, $siteTypeSiteRegionSiteActivityResultOvernight);
            }
        } 
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    //print_r($siteTypeSiteRegionSiteActivityResultOvernight); 
    //print_r($siteTypeSiteRegionSiteActivityResult); exit();

    if ($overnight_set) {
        $mergedArray = array_merge($siteTypeSiteRegionSiteActivityResult, $siteTypeSiteRegionSiteActivityResultOvernight);
        $mergedCount = count($mergedArray);
        $temp = "";

        for ($i = 0; $i < $mergedCount; $i++) {
            if (isset($mergedArray[$i]["checklistID"])) {
                if ($temp === $mergedArray[$i]["checklistID"]) {
                    $temp = $mergedArray[$i]["checklistID"];
                    unset($mergedArray[$i]);
                } else {
                    $temp = $mergedArray[$i]["checklistID"];
                }
            }
        }

        echo json_encode($mergedArray);
    } else {
        echo json_encode($siteTypeSiteRegionSiteActivityResult);
    }
} else if ($accessCode == "initialAllSiteDisplay") {
    try {
        $allSitesQuery = "SELECT checklist.siteCode, status.status, region.region, checklist.technician, checklist.createdOn, checklist.checklistID, site.siteCountry, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport FROM checklist INNER JOIN site ON checklist.siteCode=site.siteCode INNER JOIN status ON status.statusID = checklist.status INNER JOIN region ON region.regionID = site.siteRegion ORDER BY checklist.createdOn DESC";
        $allSitesStatement = $db->prepare($allSitesQuery);
        $allSitesStatement->execute();

        $allSitesStatementResult = $allSitesStatement->fetchAll(PDO::FETCH_ASSOC);

        $allSitesQuery1 = "SELECT siteCode, checklistID, technician FROM overnightSupport";
        $allSitesStatement1 = $db->prepare($allSitesQuery1);
        $allSitesStatement1->execute();

        $allSitesStatementResult1 = $allSitesStatement1->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    foreach ($allSitesStatementResult as $key => $field) {
        $tempSupport = $allSitesStatementResult[$key]["overNightSupport"];
        $allSitesStatementResult[$key]["overNightSupport"] = array();

        foreach ($allSitesStatementResult1 as $key1 => $field1) {
            if ($allSitesStatementResult[$key]["siteCode"] == $allSitesStatementResult1[$key1]["siteCode"] && $allSitesStatementResult[$key]["checklistID"] == $allSitesStatementResult1[$key1]["checklistID"]) {

                array_push($allSitesStatementResult[$key]["overNightSupport"], $allSitesStatementResult1[$key1]["technician"]);
            }
        }
    }

    echo json_encode($allSitesStatementResult);
} else if ($accessCode == "checklistData") {
    $checklistID = ltrim(rtrim(filter_input(INPUT_POST, "checklistID", FILTER_SANITIZE_STRING)));

    try {
        $allSitesQuery = "SELECT checklist.siteCode, checklist.checklistHeader, checklist.complete, checklist.siteType, checklist.status, status.status, region.region, checklist.technician, checklist.createdOn, site.siteCountry, checklist.siteName, checklist.activityType, checklist.expectedGoLiveDate, checklist.overNightSupport FROM checklist INNER JOIN site ON checklist.siteCode=site.siteCode INNER JOIN status ON status.statusID = checklist.status INNER JOIN region ON region.regionID = site.siteRegion WHERE checklistID = :checklistID";
        $allSitesStatement = $db->prepare($allSitesQuery);
        $allSitesStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
        $allSitesStatement->execute();

        $allSitesStatementResult = $allSitesStatement->fetchAll(PDO::FETCH_ASSOC);

        $allSitesQuery1 = "SELECT siteCode, checklistID, technician FROM overnightSupport WHERE checklistID = :checklistID";
        $allSitesStatement1 = $db->prepare($allSitesQuery1);
        $allSitesStatement1->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
        $allSitesStatement1->execute();

        $allSitesStatementResult1 = $allSitesStatement1->fetchAll(PDO::FETCH_ASSOC);

        //Categories Identifiers
        $categories_query = "SELECT DISTINCT checklist_checklistCategories.categoryName AS 'categoryName', checklist_checklistCategories.categories_identifier AS 'identifier' FROM checklist_checklistCategories WHERE checklist_checklistCategories.checklistID = :checklistID";
        $categories_statement = $db->prepare($categories_query);
        $categories_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_INT);
        $categories_statement->execute();

        $categories_result = $categories_statement->fetchAll(PDO::FETCH_ASSOC);

        //Optional Categories Identifiers
        $optional_categories_query = "SELECT DISTINCT checklist_checklistOptionalCategories.optionalCategoryName AS 'categoryName', checklist_checklistOptionalCategories.optional_categories_identifier AS 'identifier' FROM checklist_checklistOptionalCategories WHERE checklist_checklistOptionalCategories.checklistID = :checklistID";
        $optional_categories_statement = $db->prepare($optional_categories_query);
        $optional_categories_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_INT);
        $optional_categories_statement->execute();

        $optional_categories_result = $optional_categories_statement->fetchAll(PDO::FETCH_ASSOC);

        //Tabs Identifier
        $tabs_query = "SELECT DISTINCT checklist_checklistTabs.tabName AS 'tabName', checklist_checklistTabs.tabs_identifier AS 'tab_identifier' FROM checklist_checklistTabs WHERE checklist_checklistTabs.checklistID = :checklistID";
        $tabs_statement = $db->prepare($tabs_query);
        $tabs_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_INT);
        $tabs_statement->execute();

        $tabs_result = $tabs_statement->fetchAll(PDO::FETCH_ASSOC);

        //Optional Tabs Identifier
        $optional_tabs_query = "SELECT DISTINCT optionalTabName AS 'tabName', optional_tabs_identifier AS 'tab_identifier' FROM checklist_checklistOptionalTabs WHERE checklistID = :checklistID";
        $optional_tabs_statement = $db->prepare($optional_tabs_query);
        $optional_tabs_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_INT);
        $optional_tabs_statement->execute();

        $optional_tabs_result = $optional_tabs_statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    foreach ($allSitesStatementResult as $key => $field) {
        $tempSupport = $allSitesStatementResult[$key]["overNightSupport"];
        $allSitesStatementResult[$key]["overNightSupport"] = array();

        foreach ($allSitesStatementResult1 as $key1 => $field1) {
            array_push($allSitesStatementResult[$key]["overNightSupport"], $allSitesStatementResult1[$key1]["technician"]);
        }
    }

    echo json_encode(array_merge($allSitesStatementResult, $categories_result, $optional_categories_result, $tabs_result, $optional_tabs_result));
} else if ($accessCode == "profile") {
    $userName = ltrim(rtrim(filter_input(INPUT_POST, "userName", FILTER_SANITIZE_STRING)));
    $timeZone = ltrim(rtrim(filter_input(INPUT_POST, "timezone", FILTER_SANITIZE_STRING)));

    $timeZoneObject = new DateTimeZone($timeZone);
    $now = new DateTime("now", $timeZoneObject);
    $now_formatted = $now->format('Y-m-d');

    //print_r($now_formatted); exit();

    try {
        $profileQuery = "SELECT title, avatar, workingSince, activitiesComplete, activitiesInProgress, documentsUpdated, documentsCreated, documentsDeleted FROM technician WHERE technicianFullName = :name";
        $profileStatement = $db->prepare($profileQuery);
        $profileStatement->bindParam(":name", $userName, PDO::PARAM_STR);
        $profileStatement->execute();

        $profileStatementResult = $profileStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    echo json_encode(array_merge($profileStatementResult, array(array("time" => $now_formatted))));
} else if ($accessCode === "profile_activities_data") {
    $userName = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
    $timezone = ltrim(rtrim(filter_input(INPUT_POST, "timezone", FILTER_SANITIZE_STRING)));
    $activities_count_array = [$userName => array("activities_complete" => 0, "activities_in_progress" => 0, "upcoming_activities" => 0)];

    date_default_timezone_set($timezone);

    try {
        //First Query (Completed Sites)
        $activities_complete_query = "SELECT checklist.technician FROM checklist WHERE complete = 'true' AND checklist.technician = :technician AND month(checklist.expectedGoLiveDate) = month(curdate())";
        $activities_complete_statement = $db->prepare($activities_complete_query);
        $activities_complete_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $activities_complete_statement->execute();

        $activities_complete_result = $activities_complete_statement->fetchAll(PDO::FETCH_ASSOC);
        $activities_complete_count = count($activities_complete_result);

        //First Query (Completed Sites) - Overnight Support Seperate Query
        $activities_complete_overnight_query = "SELECT overnightSupport.technician AS 'overnightSupport' FROM overnightSupport INNER JOIN checklist ON checklist.checklistID = overnightSupport.checklistID WHERE checklist.complete = 'true' AND overnightSupport.technician = :technician AND month(checklist.expectedGoLiveDate) = month(curdate())";
        $activities_complete_overnight_statement = $db->prepare($activities_complete_overnight_query);
        $activities_complete_overnight_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $activities_complete_overnight_statement->execute();

        $activities_complete_overnight_result = $activities_complete_overnight_statement->fetchAll(PDO::FETCH_ASSOC);
        $activities_complete_overnight_count = count($activities_complete_overnight_result);

        //Second Query (In Progress Sites) 
        $activities_in_progress_query = "SELECT checklist.technician FROM liveSiteEvents INNER JOIN checklist AS checklist ON checklist.checklistID = liveSiteEvents.checklistID INNER JOIN technician as technician ON checklist.technician = technician.technicianFullName WHERE liveSiteEvents.technician = :technician AND month(liveSiteEvents.date) = month(curdate())";
        $activities_in_progress_statement = $db->prepare($activities_in_progress_query);
        $activities_in_progress_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $activities_in_progress_statement->execute();

        $activities_in_progress_result = $activities_in_progress_statement->fetchAll(PDO::FETCH_ASSOC);
        $activities_in_progress_count = count($activities_in_progress_result);

        //Second Query (In Progress Sites) - Overnight Support Seperate Query
        $activities_in_progress_overnight_query = "SELECT overnightSupport.technician AS 'overnightSupport' FROM overnightSupport INNER JOIN checklist AS checklist ON checklist.checklistID = overnightSupport.checklistID INNER JOIN liveSiteEvents ON liveSiteEvents.checklistID = checklist.checklistID WHERE overnightSupport.technician = :technician AND month(liveSiteEvents.date) = month(curdate())";
        $activities_in_progress_overnight_statement = $db->prepare($activities_in_progress_overnight_query);
        $activities_in_progress_overnight_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $activities_in_progress_overnight_statement->execute();

        $activities_in_progress_overnight_result = $activities_in_progress_overnight_statement->fetchAll(PDO::FETCH_ASSOC);
        $activities_in_progress_overnight_count = count($activities_in_progress_overnight_result);

        //Third Query (Upcoming Sites)
        $upcoming_activities_query = "SELECT technician FROM upcomingSiteEvents WHERE technician = :technician AND month(upcomingSiteEvents.date) = month(curdate())";
        $upcoming_activities_statement = $db->prepare($upcoming_activities_query);
        $upcoming_activities_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $upcoming_activities_statement->execute();

        $upcoming_activities_statement_result = $upcoming_activities_statement->fetchAll(PDO::FETCH_ASSOC);
        $upcoming_activities_statement_count = count($upcoming_activities_statement_result);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    }

    $main_looper_in_progress = $activities_in_progress_count + $activities_in_progress_overnight_count;

    for ($i = 0; $i < $main_looper_in_progress; $i++) {
        if (isset($activities_in_progress_result[$i]["technician"])) {
            if ($userName === $activities_in_progress_result[$i]["technician"]) {
                $activities_count_array[$userName]["activities_in_progress"] = $activities_count_array[$userName]["activities_in_progress"] + 1;
            } else {
                for ($j = 0; $j < $activities_in_progress_overnight_count; $j++) {
                    if ($userName === $activities_in_progress_overnight_result[$j]["overnightSupport"]) {
                        $activities_count_array[$userName]["activities_in_progress"] = $activities_count_array[$userName]["activities_in_progress"] + 1;
                    }
                }
            }
        } else {
            $activities_count_array[$userName]["activities_in_progress"] = $activities_count_array[$userName]["activities_in_progress"] + 1;
        }
    }

    $main_looper_complete = $activities_complete_count + $activities_complete_overnight_count;
    //echo $main_looper_complete . " " . $activities_complete_overnight_count . " | ";

    for ($i = 0; $i < $main_looper_complete; $i++) {
        if (isset($activities_complete_result[$i]["technician"])) {
            $activities_count_array[$userName]["activities_complete"] = $activities_count_array[$userName]["activities_complete"] + 1;
            //echo $activities_count_array[$userName]["activities_complete"] . " ";
        } else {
            $activities_count_array[$userName]["activities_complete"] = $activities_count_array[$userName]["activities_complete"] + 1;
            //echo $activities_count_array[$userName]["activities_complete"] . " ";
        }
    }

    //print_r($activities_count_array);
    //print_r($activities_complete_result);

    foreach ($activities_count_array as $key => $value) {
        for ($i = 0; $i < $upcoming_activities_statement_count; $i++) {
            if ($key === $upcoming_activities_statement_result[$i]["technician"]) {
                $activities_count_array[$key]["upcoming_activities"] = $activities_count_array[$key]["upcoming_activities"] + 1;
            }
        }
    }

    echo json_encode($activities_count_array);
} else if ($accessCode === "profile_activities_pie_data") {
    $userName = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING)));
    $activities_pie_array = [$userName => array("new_build" => 0, "relocation" => 0, "refurb" => 0, "limited" => 0, "event_kit" => 0, "closure" => 0, "rebuild" => 0, "emergency_server_build" => 0)];

    try {
        //$activities_pie_query = "SELECT DISTINCT activityType, checklist.checklistID FROM checklist INNER JOIN overnightSupport ON overnightSupport.technician = :inner_join_technician OR overnightSupport.checklistID = checklist.checklistID WHERE checklist.technician = :technician OR overnightSupport.technician = :overnight_technician AND checklist.complete = 'true'";
        $activities_pie_query = "SELECT DISTINCT activityType, checklist.checklistID FROM checklist INNER JOIN overnightSupport ON overnightSupport.technician = :inner_join_technician WHERE checklist.complete = 'true' AND checklist.technician = :technician OR overnightSupport.technician = :overnight_technician AND checklist.complete = 'true' AND checklist.checklistID = overnightSupport.checklistID";
        $activities_pie_statement = $db->prepare($activities_pie_query);
        $activities_pie_statement->bindParam(":inner_join_technician", $userName, PDO::PARAM_STR);
        $activities_pie_statement->bindParam(":technician", $userName, PDO::PARAM_STR);
        $activities_pie_statement->bindParam(":overnight_technician", $userName, PDO::PARAM_STR);
        $activities_pie_statement->execute();

        $activities_pie_result = $activities_pie_statement->fetchAll(PDO::FETCH_ASSOC);
        $activities_pie_result_count = count($activities_pie_result);

        foreach ($activities_pie_array[$userName] as $key => $value) {
            for ($i = 0; $i < $activities_pie_result_count; $i++) {
                if ($key === "new_build" && $activities_pie_result[$i]["activityType"] === "New Build") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "relocation" && $activities_pie_result[$i]["activityType"] === "Relocation") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "refurb" && $activities_pie_result[$i]["activityType"] === "Refurb") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "limited" && $activities_pie_result[$i]["activityType"] === "Limited") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "event_kit" && $activities_pie_result[$i]["activityType"] === "Event Kit") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "closure" && $activities_pie_result[$i]["activityType"] === "Closure") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "rebuild" && $activities_pie_result[$i]["activityType"] === "Rebuild") {
                    $activities_pie_array[$userName][$key] += 1;
                } else if ($key === "emergency_server_build" && $activities_pie_result[$i]["activityType"] === "Emergency Server Build") {
                    $activities_pie_array[$userName][$key] += 1;
                }
            }
        }

        echo json_encode($activities_pie_array);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    }
} else if ($accessCode === "identifiers") {
    $key = ltrim(rtrim(filter_input(INPUT_POST, "checklistID", FILTER_SANITIZE_NUMBER_INT)));

    try {
        //Categories
        $identifier_query_categories = "SELECT DISTINCT checklist_checklistCategories.categoryName FROM checklist_checklistCategories WHERE checklist_checklistCategories.checklistID = :key";
        $identifier_query_statement = $db->prepare($identifier_query_categories);
        $identifier_query_statement->bindParam(":key", $key, PDO::PARAM_INT);
        $identifier_query_statement->execute();

        $categories_result = $identifier_query_statement->fetchAll(PDO::FETCH_ASSOC);

        //Optional Categories
        $identifier_query_optional_categories = "SELECT DISTINCT checklist_checklistOptionalCategories.optionalCategoryName FROM checklist_checklistOptionalCategories WHERE checklist_checklistOptionalCategories.checklistID = :key";
        $identifier_query_statement = $db->prepare($identifier_query_optional_categories);
        $identifier_query_statement->bindParam(":key", $key, PDO::PARAM_INT);
        $identifier_query_statement->execute();

        $optional_categories_result = $identifier_query_statement->fetchAll(PDO::FETCH_ASSOC);

        //Tabs
        $identifier_query_tabs = "SELECT DISTINCT checklist_checklistTabs.tabName FROM checklist_checklistTabs WHERE checklist_checklistTabs.checklistID = :key";
        $identifier_query_statement = $db->prepare($identifier_query_tabs);
        $identifier_query_statement->bindParam(":key", $key, PDO::PARAM_INT);
        $identifier_query_statement->execute();

        $tabs_result = $identifier_query_statement->fetchAll(PDO::FETCH_ASSOC);

        //Optional Tabs
        $identifier_query_optional_tabs = "SELECT DISTINCT checklist_checklistOptionalTabs.optionalTabName FROM checklist_checklistOptionalTabs WHERE checklist_checklistOptionalTabs.checklistID = :key";
        $identifier_query_statement = $db->prepare($identifier_query_optional_tabs);
        $identifier_query_statement->bindParam(":key", $key, PDO::PARAM_INT);
        $identifier_query_statement->execute();

        $optional_tabs_result = $identifier_query_statement->fetchAll(PDO::FETCH_ASSOC);

        print_r($categories_result);
        print_r($optional_categories_result);
        print_r($tabs_result);
        print_r($optional_tabs_result);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    }
}