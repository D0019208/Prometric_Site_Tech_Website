<?php

require_once ("../database/database.php");
require_once ("functions.php");

$start_get = ltrim(rtrim(filter_input(INPUT_GET, "start", FILTER_SANITIZE_STRING)));
$end_get = ltrim(rtrim(filter_input(INPUT_GET, "end", FILTER_SANITIZE_STRING)));

$start = date('Y-m-d', strtotime($start_get . ' -31 day'));
$end = date('Y-m-d', strtotime($end_get . ' +11 day'));

$checklist_id_to_query = "";

try {
    $eventQuery = "SELECT checklist.checklistID, checklist.siteCode, checklist.siteName, checklist.createdOn, checklist.expectedGoLiveDate, checklist.technician, checklist.checklistHeader, checklist.overNightSupport, checklist.allDayEvent, status.status, site.siteCountry, site.siteCounty, technician.technicianID FROM checklist INNER JOIN status ON checklist.status = status.statusID INNER JOIN site ON checklist.siteCode = site.siteCode INNER JOIN technician ON technician.technicianFullName = checklist.technician WHERE checklist.createdOn >= :start AND checklist.expectedGoLiveDate <= :end";
    $eventStatement = $db->prepare($eventQuery);
    $eventStatement->bindParam(":start", $start, PDO::PARAM_STR);
    $eventStatement->bindParam(":end", $end, PDO::PARAM_STR);
    $eventStatement->execute();

    $result = $eventStatement->fetchAll(PDO::FETCH_ASSOC);
    $resultCount = count($result);

    $count = 0;
    foreach ($result as $value) {
        $checklist_id_to_query .= $value["checklistID"];

        if ($count < $resultCount - 1) {
            $checklist_id_to_query .= ", ";
        }

        $count++;
    }

    //Add overnight support to checklist technicians
    if ($checklist_id_to_query !== "") {
        $overnightTechQuery = "SELECT checklistID, technician FROM overnightSupport WHERE checklistID IN  (" . $checklist_id_to_query . ")";
        $overnight_statement = $db->prepare($overnightTechQuery);
        $overnight_statement->execute();

        $overnight_support_techs = $overnight_statement->fetchAll(PDO::FETCH_ASSOC);
    }

    $siteNewsQuery = "SELECT siteNews.siteNewsID, siteNews.event, siteNews.siteCode, siteNews.technician, siteNews.date, siteNews.time, siteNews.allDayEvent, siteNews.event_type, siteNews.link, technician.technicianID FROM siteNews INNER JOIN technician ON siteNews.technician = technician.technicianFullName WHERE siteNews.date  >= :start";
    $siteNewsStatement = $db->prepare($siteNewsQuery);
    $siteNewsStatement->bindParam(":start", $start, PDO::PARAM_STR);
    $siteNewsStatement->execute();

    $siteNewsResult = $siteNewsStatement->fetchAll(PDO::FETCH_ASSOC);
    $siteNewsResultCount = count($siteNewsResult);

    $upcoming_activities_query = "SELECT upcomingSiteEvents.upcomingSiteEventID, upcomingSiteEvents.siteCode, upcomingSiteEvents.event, upcomingSiteEvents.technician, upcomingSiteEvents.date, upcomingSiteEvents.time, upcomingSiteEvents.expectedGoLiveDate, upcomingSiteEvents.allDayEvent, upcomingSiteEvents.event_country, upcomingSiteEvents.event_county, upcomingSiteEvents.event_town, upcomingSiteEvents.event_type, technician.technicianID FROM upcomingSiteEvents INNER JOIN technician ON technician.technicianFullName = upcomingSiteEvents.technician";
    $upcoming_activities_statement = $db->prepare($upcoming_activities_query);
    $upcoming_activities_statement->execute();

    $upcoming_activities_result = $upcoming_activities_statement->fetchAll(PDO::FETCH_ASSOC);
    $upcoming_activities_count = count($upcoming_activities_result);



    $document_events_query = "SELECT documentEvents.documentEventID, documentEvents.event, documentEvents.technician, documentEvents.date, documentEvents.time, documentEvents.link, documentEvents.allDayEvent, documentEvents.event_type, technician.technicianID FROM documentEvents INNER JOIN technician ON technician.technicianFullName = documentEvents.technician";
    $document_events_statement = $db->prepare($document_events_query);
    $document_events_statement->execute();

    $document_events_result = $document_events_statement->fetchAll(PDO::FETCH_ASSOC);
    $document_events_count = count($document_events_result);





    $other_events_query = "SELECT other_events.event_id, other_events.event, other_events.start, other_events.end, other_events.technician, other_events.link, other_events.event_type, other_events.allDayEvent, technician.technicianID FROM other_events INNER JOIN technician ON technician.technicianFullName = other_events.technician";
    $other_events_statement = $db->prepare($other_events_query);
    $other_events_statement->execute();

    $other_events_result = $other_events_statement->fetchAll(PDO::FETCH_ASSOC);
    $other_events_count = count($other_events_result);
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}
//=======================================================================================================\\
//===================================Create Events Based on Checklists===================================\\
//=======================================================================================================\\
$json_array = array();
$json_columns_array = array("id", "resourceIds", "editable", "resourceEditable", "site_code", "title", "start", "end", "real_start", "real_end", "technician", "color", "checklist_header", "allDay", "link", "event_type", "site_country", "site_county", "site_town", "status");
$json_columns_count = count($json_columns_array);
$json = "[";

for ($j = 0; $j < $json_columns_count; $j++) {
    $key = $json_columns_array[$j];
    $value = '';

    $json_array[$key] = $value;
}

$title_used = false;
$start_used = false;
$end_used = false;
$real_start = false;
$real_end = false;
$technician_used = false;
$id_used = false;
$resourceId_used = false;
$editable_used = false;
$resource_editable_used = false;
$site_code_used = false;
$color_used = false;
$checklist_header_used = false;
$all_day_used = false;
$url_used = false;
$event_type_used = false;
$site_country_used = false;
$site_county_used = false;
$site_town_used = false;
$status_used = false;

$looper = 0;
$count = 1;

foreach ($result as $valueResult) {
    foreach ($json_array as $key => $valueJson) {
        if (!$id_used) {
            $json_array[$key] = $valueResult["checklistID"];

            $id_used = true;
        } else if (!$resourceId_used) {
            $mini_query = "SELECT technician.technicianID FROM overnightSupport INNER JOIN technician ON overnightSupport.technician = technician.technicianFullName INNER JOIN checklist ON checklist.checklistID = overnightSupport.checklistID WHERE checklist.technician = '" . $valueResult["technician"] . "' AND checklist.checklistID = " . $valueResult["checklistID"] . " AND overnightSupport.checklistID = " . $valueResult["checklistID"];
//            "SELECT overnightSupport.technician FROM overnightSupport INNER JOIN checklist ON checklist.checklistID WHERE checklist.technician = '" . $valueResult["technician"] . "' AND checklist.checklistID = " . $valueResult["checklistID"] . " AND overnightSupport.checklistID = " . $valueResult["checklistID"];
            $mini_statement = $db->prepare($mini_query);
            $mini_statement->execute();

            $mini_result = $mini_statement->fetchAll(PDO::FETCH_ASSOC);
            $temp_array = array($valueResult["technicianID"]);

            foreach ($mini_result as $mini_value) {
                array_push($temp_array, $mini_value["technicianID"]);
            }

            $json_array[$key] = $temp_array;

            $resourceId_used = true;
        } else if (!$editable_used) {
            if ($valueResult["status"] === "Complete") {
                $json_array[$key] = false;
            } else {
                $json_array[$key] = true;
            }

            $editable_used = true;
        } else if (!$resource_editable_used) {
            if ($valueResult["status"] === "Complete") {
                $json_array[$key] = false;
            } else {
                $json_array[$key] = true;
            }

            $resource_editable_used = true;
        } else if (!$site_code_used) {
            $json_array[$key] = $valueResult["siteCode"];

            $site_code_used = true;
        } else if (!$title_used) {
            $json_array[$key] = "Site " . $valueResult["siteCode"] . " - " . $valueResult["status"];

            $title_used = true;
        } else if (!$start_used) {
            //$datetime = new DateTime($valueResult["createdOn"]);
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["createdOn"]));

            $start_used = true;
        } else if (!$end_used) {
            //$datetime = new DateTime($valueResult["expectedGoLiveDate"]);
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["expectedGoLiveDate"] . ' +1 day'));

            $end_used = true;
        } else if (!$real_start) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["createdOn"]));

            $real_start = true;
        } else if (!$real_end) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["expectedGoLiveDate"]));

            $real_end = true;
        } else if (!$technician_used) {
            $tech_string = "<a href='profile.php?technician=" . $valueResult["technician"] . "'>" . $valueResult["technician"] . "</a>";
            $overnight_tech_loop_counter = 0;
            $overnight_tech_count = count($overnight_support_techs);

            foreach ($overnight_support_techs as $techs) {
                if ($techs["checklistID"] === $valueResult["checklistID"]) {
                    if ($overnight_tech_loop_counter < $overnight_tech_count) {
                        $tech_string .= ", <a href='profile.php?technician=" . $techs["technician"] . "'>" . $techs["technician"] . "</a>";
                    }

                    $overnight_tech_loop_counter++;
                }
            }

            $json_array[$key] = $tech_string;

            $technician_used = true;
        } else if (!$color_used) {
            if ($valueResult["status"] == "Complete") {
                $json_array[$key] = "#5cb85c";
            } else if (strpos($valueResult["status"], 'In Progress') !== false) {
                $json_array[$key] = "#0f52ba";
            } else if (strpos($valueResult["status"], 'Not Started') !== false) {
                $json_array[$key] = "#cccccc";
            }

            $color_used = true;
        } else if (!$checklist_header_used) {
            $json_array[$key] = $valueResult["checklistHeader"];

            $checklist_header_used = true;
        } else if (!$all_day_used) {
            $json_array[$key] = $valueResult["allDayEvent"];
            $all_day_used = true;
        } else if (!$url_used) {
            $json_array[$key] = "site.php?checklistID=" . $valueResult["checklistID"];

            $url_used = true;
        } else if (!$event_type_used) {
            $json_array[$key] = "checklist";

            $event_type_used = true;
        } else if (!$site_country_used) {
            $json_array[$key] = $valueResult["siteCountry"];

            $site_country_used = true;
        } else if (!$site_county_used) {
            $json_array[$key] = $valueResult["siteCounty"];

            $site_county_used = true;
        } else if (!$site_town_used) {
            $json_array[$key] = $valueResult["siteName"];

            $site_town_used = true;
        } else if (!$status_used) {
            $json_array[$key] = $valueResult["status"];

            $status_used = true;
        }

        if ($looper == $json_columns_count - 1) {

            //If loop has ended (all columns filled) Reset
            $id_used = false;
            $editable_used = false;
            $resource_editable_used = false;
            $resourceId_used = false;
            $site_code_used = false;
            $title_used = false;
            $start_used = false;
            $end_used = false;
            $real_start = false;
            $real_end = false;
            $technician_used = false;
            $color_used = false;
            $checklist_header_used = false;
            $all_day_used = false;
            $url_used = false;
            $event_type_used = false;
            $site_country_used = false;
            $site_county_used = false;
            $site_town_used = false;
            $status_used = false;

            $encodedJsonArray = json_encode($json_array);
            $json .= $encodedJsonArray;

            if ($count < $resultCount) {
                $json .= ',';
            } else if ($siteNewsResultCount === 0 && $upcoming_activities_count === 0 && $document_events_count === 0 && $other_events_count === 0) {
                if ($count >= $resultCount) {
                    $json .= "]";

                    echo $json;
                    exit();
                }
            } else {
                $json .= ',';
            }
            $looper = 0;
        } else {

            $looper++;
        }
    }
    $count++;
}


//========================================================================================================\\
//===================================Create Events Based on News Events===================================\\
//========================================================================================================\\
$json_array = array();
$json_columns_array = array("id", "resourceIds", "resourceEditable", "editable", "site_code", "technician", "title", "event", "start", "real_start", "color", "allDay", "link", "event_type");
$json_columns_count = count($json_columns_array);

for ($j = 0; $j < $json_columns_count; $j++) {
    $key = $json_columns_array[$j];
    $value = '';

    $json_array[$key] = $value;
}

$title_used = false;
$siteNewsID_used = false;
$resourceId_used = false;
$editable_used = false;
$resource_editable_used = false;
$event_used = false;
$date_start_used = false;
$date_real_start = false;
$site_code_used = false;
$technician_used = false;
$color_used = false;
$all_day_event_used = false;
$link_used = false;
$event_type_used = false;

$looper = 0;
$count = 1;

foreach ($siteNewsResult as $valueResult) {
    foreach ($json_array as $key => $valueJson) {
        if (!$siteNewsID_used) {
            $json_array[$key] = $valueResult["siteNewsID"];

            $siteNewsID_used = true;
        } else if (!$resourceId_used) {
            $json_array[$key] = array($valueResult["technicianID"]);

            $resourceId_used = true;
        } else if (!$resource_editable_used) {
            $json_array[$key] = false;

            $resource_editable_used = true;
        } else if (!$editable_used) {
            $json_array[$key] = false;

            $editable_used = true;
        } else if (!$site_code_used) {
            $json_array[$key] = $valueResult["siteCode"];

            $site_code_used = true;
        } else if (!$technician_used) {
            $json_array[$key] = "<a href='profile.php?technician=" . $valueResult["technician"] . "'>" . $valueResult["technician"] . "</a>";

            $technician_used = true;
        } else if (!$title_used) {
            if ($valueResult["event_type"] === "site") {
                $json_array[$key] = $valueResult["event"];
            } else if ($valueResult["event_type"] === "other") {
                $json_array[$key] = $valueResult["technician"] . " has reached a milestone!";
            }

            $title_used = true;
        } else if (!$event_used) {
            $json_array[$key] = $valueResult["event"];
            $event_used = true;
        } else if (!$date_start_used) {
            $datetime = new DateTime($valueResult["date"] . " " . $valueResult["time"]);
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $date_start_used = true;
        } else if (!$date_real_start) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $date_real_start = true;
        } else if (!$color_used) {
            if (strpos($valueResult["event"], 'Finished') !== false) {
                $json_array[$key] = "#5cb85c";
            } else {
                $json_array[$key] = "#0f52ba";
            }

            if ($valueResult["event_type"] === "other" || $valueResult["event_type"] === "other") {
                $json_array[$key] = "#ff99e6";
            }

            $color_used = true;
        } else if (!$all_day_used) {
            $json_array[$key] = $valueResult["allDayEvent"];
            $all_day_used = true;
        } else if (!$link_used) {
            $json_array[$key] = $valueResult["link"];

            $link_used = true;
        } else if (!$event_type_used) {
            $json_array[$key] = $valueResult["event_type"];

            $event_type_used = true;
        }

        if ($looper == $json_columns_count - 1) {

            //If loop has ended (all columns filled) Reset
            $siteNewsID_used = false;
            $resourceId_used = false;
            $editable_used = false;
            $resource_editable_used = false;
            $site_code_used = false;
            $technician_used = false;
            $title_used = false;
            $event_used = false;
            $date_start_used = false;
            $date_real_start = false;
            $color_used = false;
            $all_day_used = false;
            $link_used = false;
            $event_type_used = false;

            $encodedJsonArray = json_encode($json_array);
            $json .= $encodedJsonArray;

            if ($count < $siteNewsResultCount) {
                $json .= ',';
            } else if ($upcoming_activities_count === 0 && $document_events_count === 0 && $other_events_count === 0) {
                if ($count >= $siteNewsResultCount) {
                    $json .= "]";

                    echo $json;
                    exit();
                }
            } else {
                $json .= ',';
            }
            $looper = 0;
        } else {

            $looper++;
        }
    }
    $count++;
}

//============================================================================================================\\
//===================================Create Events Based on Upcoming Events===================================\\
//============================================================================================================\\
$json_array = array();
$json_columns_array = array("id", "resourceIds", "site_code", "technician", "title", "event", "start", "end", "real_start", "real_end", "color", "allDay", "event_country", "event_county", "event_town", "event_type");
$json_columns_count = count($json_columns_array);

for ($j = 0; $j < $json_columns_count; $j++) {
    $key = $json_columns_array[$j];
    $value = '';

    $json_array[$key] = $value;
}

$id_used = false;
$resourceId_used = false;
$site_code_used = false;
$technician_used = false;
$title_used = false;
$event_used = false;
$date_start_used = false;
$date_end_used = false;
$real_start = false;
$real_end = false;
$color_used = false;
$all_day_used = false;
$event_type_used = false;
$event_country_used = false;
$event_county_used = false;
$event_town_used = false;

$looper = 0;
$count = 1;

foreach ($upcoming_activities_result as $valueResult) {
    foreach ($json_array as $key => $valueJson) {
        if (!$id_used) {
            $json_array[$key] = $valueResult["upcomingSiteEventID"];

            $id_used = true;
        } else if (!$resourceId_used) {
            $json_array[$key] = array($valueResult["technicianID"]);

            $resourceId_used = true;
        } else if (!$site_code_used) {
            $json_array[$key] = $valueResult["siteCode"];

            $site_code_used = true;
        } else if (!$technician_used) {
            $json_array[$key] = "<a href='profile.php?technician=" . $valueResult["technician"] . "'>" . $valueResult["technician"] . "</a>";

            $technician_used = true;
        } else if (!$title_used) {
            $json_array[$key] = $valueResult["event"];

            $title_used = true;
        } else if (!$event_used) {
            $json_array[$key] = $valueResult["event"];

            $event_used = true;
        } else if (!$date_start_used) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $date_start_used = true;
        } else if (!$date_end_used) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["expectedGoLiveDate"] . ' +1 day'));

            $date_end_used = true;
        } else if (!$real_start) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $real_start = true;
        } else if (!$real_end) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["expectedGoLiveDate"] . ' +1 day'));

            $real_end = true;
        } else if (!$color_used) {
            $json_array[$key] = "#ff8080";

            $color_used = true;
        } else if (!$all_day_used) {
            $json_array[$key] = $valueResult["allDayEvent"];

            $all_day_used = true;
        } else if (!$event_country_used) {
            $json_array[$key] = $valueResult["event_country"];

            $event_country_used = true;
        } else if (!$event_county_used) {
            $json_array[$key] = $valueResult["event_county"];

            $event_county_used = true;
        } else if (!$event_town_used) {
            $json_array[$key] = $valueResult["event_town"];

            $event_town_used = true;
        } else if (!$event_type_used) {
            $json_array[$key] = $valueResult["event_type"];

            $event_type_used = true;
        }


        if ($looper == $json_columns_count - 1) {

            //If loop has ended (all columns filled) Reset
            $id_used = false;
            $resourceId_used = false;
            $site_code_used = false;
            $technician_used = false;
            $title_used = false;
            $event_used = false;
            $date_start_used = false;
            $date_end_used = false;
            $real_end = false;
            $real_start = false;
            $color_used = false;
            $all_day_used = false;
            $event_country_used = false;
            $event_county_used = false;
            $event_town_used = false;

            $encodedJsonArray = json_encode($json_array);
            $json .= $encodedJsonArray;

            if ($count < $upcoming_activities_count) {
                $json .= ',';
            } else if ($document_events_count === 0 && $other_events_count === 0) {
                if ($count >= $upcoming_activities_count) {
                    $json .= "]";

                    echo $json;
                    exit();
                }
            } else {
                $json .= ',';
            }
            $looper = 0;
        } else {

            $looper++;
        }
    }
    $count++;
}

//============================================================================================================\\
//===================================Create Events Based on Document Events===================================\\
//============================================================================================================\\
$json_array = array();
$json_columns_array = array("id", "resourceIds", "resourceEditable", "title", "event", "editable", "technician", "start", "real_start", "color", "link", "allDay", "event_type");
$json_columns_count = count($json_columns_array);

for ($j = 0; $j < $json_columns_count; $j++) {
    $key = $json_columns_array[$j];
    $value = '';

    $json_array[$key] = $value;
}

$id_used = false;
$resourceId_used = false;
$resource_editable_used = false;
$title_used = false;
$event_used = false;
$editable_used = false;
$technician_used = false;
$date_start_used = false;
$real_start = false;
$color_used = false;
$link_used = false;
$all_day_used = false;
$event_type_used = false;

$looper = 0;
$count = 1;

foreach ($document_events_result as $valueResult) {
    foreach ($json_array as $key => $valueJson) {
        if (!$id_used) {
            $json_array[$key] = $valueResult["documentEventID"];

            $id_used = true;
        } else if (!$resourceId_used) {
            $json_array[$key] = array($valueResult["technicianID"]);

            $resourceId_used = true;
        } else if (!$resource_editable_used) {
            $json_array[$key] = false;

            $resource_editable_used = true;
        } else if (!$title_used) {
            $json_array[$key] = $valueResult["event"];

            $title_used = true;
        } else if (!$event_used) {
            $json_array[$key] = $valueResult["event"];

            $event_used = true;
        } else if (!$editable_used) {
            $json_array[$key] = false;

            $editable_used = true;
        } else if (!$technician_used) {
            $json_array[$key] = "<a href='profile.php?technician=" . $valueResult["technician"] . "'>" . $valueResult["technician"] . "</a>";

            $technician_used = true;
        } else if (!$date_start_used) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $date_start_used = true;
        } else if (!$real_start) {
            $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["date"] . " " . $valueResult["time"]));

            $real_start = true;
        } else if (!$color_used) {
            $json_array[$key] = "#c6538c";

            $color_used = true;
        } else if (!$link_used) {
            $json_array[$key] = $valueResult["link"];

            $link_used = true;
        } else if (!$all_day_used) {
            $json_array[$key] = $valueResult["allDayEvent"];

            $all_day_used = true;
        } else if (!$event_type_used) {
            $json_array[$key] = $valueResult["event_type"];

            $event_type_used = true;
        }


        if ($looper == $json_columns_count - 1) {

            //If loop has ended (all columns filled) Reset
            $id_used = false;
            $resourceId_used = false;
            $resource_editable_used = false;
            $technician_used = false;
            $title_used = false;
            $event_used = false;
            $editable_used = false;
            $date_start_used = false;
            $real_start = false;
            $color_used = false;
            $all_day_used = false;
            $link_used = false;

            $encodedJsonArray = json_encode($json_array);
            $json .= $encodedJsonArray;

            if ($count < $document_events_count) {
                $json .= ',';
            } else if ($other_events_count === 0) {
                if ($count >= $document_events_count) {
                    $json .= "]";

                    echo $json;
                    exit();
                }
            } else {
                $json .= ',';
            }
            $looper = 0;
        } else {

            $looper++;
        }
    }
    $count++;
}






//============================================================================================================\\
//====================================Create Events Based on Other Events=====================================\\
//============================================================================================================\\
$json_array = array();
$json_columns_array = array("id", "resourceIds", "resourceEditable", "title", "event", "editable", "technician", "start", "end", "real_start", "real_end", "color", "allDay", "event_type");
$json_columns_count = count($json_columns_array);

for ($j = 0; $j < $json_columns_count; $j++) {
    $key = $json_columns_array[$j];
    $value = '';

    $json_array[$key] = $value;
}

$id_used = false;
$resourceId_used = false;
$resource_editable_used = false;
$title_used = false;
$event_used = false;
$editable_used = false;
$technician_used = false;
$date_start_used = false;
$date_end_used = false;
$real_start = false;
$real_end = false;
$color_used = false;
$all_day_used = false;
$event_type_used = false;

$looper = 0;
$count = 1;

foreach ($other_events_result as $valueResult) {
    foreach ($json_array as $key => $valueJson) {
        if (!$id_used) {
            $json_array[$key] = $valueResult["event_id"];

            $id_used = true;
        } else if (!$resourceId_used) {
            $json_array[$key] = array($valueResult["technicianID"]);

            $resourceId_used = true;
        } else if (!$resource_editable_used) {
            if ($valueResult["end"] === null || $valueResult["end"] === "0000-00-00 00:00:00") {
                $json_array[$key] = false;
            } else {
                $json_array[$key] = true;
            }

            $resource_editable_used = true;
        } else if (!$title_used) {
            $json_array[$key] = $valueResult["event"];

            $title_used = true;
        } else if (!$event_used) {
            $json_array[$key] = $valueResult["event"];

            $event_used = true;
        } else if (!$editable_used) {
            if ($valueResult["end"] === null || $valueResult["end"] === "0000-00-00 00:00:00") {
                $json_array[$key] = false;
            } else {
                $json_array[$key] = true;
            }

            $editable_used = true;
        } else if (!$technician_used) {
            $json_array[$key] = "<a href='profile.php?technician=" . $valueResult["technician"] . "'>" . $valueResult["technician"] . "</a>";

            $technician_used = true;
        } else if (!$date_start_used) {
            $json_array[$key] = $valueResult["start"];

            $date_start_used = true;
        } else if (!$date_end_used) {
            if ($valueResult["end"] !== "0000-00-00 00:00:00") {
                //Changed this to fix bug where Other Events end date is todays date if problem, change back
                //$json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["end"] . ' +1 day'));
                if($valueResult["end"] === null) {
                    $json_array[$key] = $valueResult["start"];
                } else {
                    $json_array[$key] = date("Y-m-d\TH:i:s", strtotime($valueResult["end"] . ' +1 day'));
                } 
            } else {
                $json_array[$key] = $valueResult["end"];
            }


            $date_end_used = true;
        } else if (!$real_start) {
            $json_array[$key] = $valueResult["start"];

            $real_start = true;
        } else if (!$real_end) {
            $json_array[$key] = $valueResult["end"];

            $real_end = true;
        } else if (!$color_used) {
            $json_array[$key] = "#ff99e6";

            $color_used = true;
        } else if (!$all_day_used) {
            $json_array[$key] = $valueResult["allDayEvent"];

            $all_day_used = true;
        } else if (!$event_type_used) {
            $json_array[$key] = $valueResult["event_type"];

            $event_type_used = true;
        }


        if ($looper == $json_columns_count - 1) {

            //If loop has ended (all columns filled) Reset
            $id_used = false;
            $resourceId_used = false;
            $resource_editable_used = false;
            $title_used = false;
            $event_used = false;
            $editable_used = false;
            $technician_used = false;
            $date_start_used = false;
            $date_end_used = false;
            $real_start = false;
            $real_end = false;
            $color_used = false;
            $all_day_used = false;
            $event_type_used = false;

            $encodedJsonArray = json_encode($json_array);
            $json .= $encodedJsonArray;

            if ($count < $other_events_count) {
                $json .= ',';
            } else if ($count >= $other_events_count) {
                $json .= "]";

                echo $json;
                exit();
            }
            $looper = 0;
        } else {

            $looper++;
        }
    }
    $count++;
}