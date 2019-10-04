<?php

require_once ("../../database/database.php");
require_once ("../functions.php");

$access_code = ltrim(rtrim(filter_input(INPUT_POST, "access_code", FILTER_SANITIZE_STRING)));

try {
    if ($access_code === "checklist") {
        $start_date = ltrim(rtrim(filter_input(INPUT_POST, "start_date", FILTER_SANITIZE_STRING)));
        $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));
        $checklist_id = ltrim(rtrim(filter_input(INPUT_POST, "checklist_id", FILTER_SANITIZE_NUMBER_INT)));

        $query = "UPDATE checklist SET createdOn = :createdOn, expectedGoLiveDate = :goLiveDate WHERE checklistID = :checklistID";
        $statement = $db->prepare($query);
        $statement->bindParam(":createdOn", $start_date, PDO::PARAM_STR);
        $statement->bindParam(":goLiveDate", $end_date, PDO::PARAM_STR);
        $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);

        $result = $statement->execute();

        echo $result;
    } else if ($access_code === "upcomingSiteEvents") {
        $start_date = ltrim(rtrim(filter_input(INPUT_POST, "start_date", FILTER_SANITIZE_STRING)));
        $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));
        $checklist_id = ltrim(rtrim(filter_input(INPUT_POST, "checklist_id", FILTER_SANITIZE_NUMBER_INT)));

        $datetime = new DateTime($start_date);

        $date = $datetime->format('Y-m-d');
        $time = $datetime->format('H:i:s');

        $query = "UPDATE upcomingSiteEvents SET time = :time, date = :date, expectedGoLiveDate = :goLiveDate WHERE upcomingSiteEventID = :checklistID";
        $statement = $db->prepare($query);
        $statement->bindParam(":time", $time, PDO::PARAM_STR);
        $statement->bindParam(":date", $date, PDO::PARAM_STR);
        $statement->bindParam(":goLiveDate", $end_date, PDO::PARAM_STR);
        $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);

        $result = $statement->execute();

        echo $result;
    } else if ($access_code === "other") {
        $start_date = ltrim(rtrim(filter_input(INPUT_POST, "start_date", FILTER_SANITIZE_STRING)));
        $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));
        $checklist_id = ltrim(rtrim(filter_input(INPUT_POST, "checklist_id", FILTER_SANITIZE_NUMBER_INT)));

        $query = "UPDATE other_events SET start = :start, end = :end WHERE event_id = :checklistID";
        $statement = $db->prepare($query);
        $statement->bindParam(":start", $start_date, PDO::PARAM_STR);
        $statement->bindParam(":end", $end_date, PDO::PARAM_STR);
        $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);

        $result = $statement->execute();

        echo $result;
    }
//    else if ($access_code === "documents")
//    {
//        $datetime = new DateTime($start_date);
//
//        $date = $datetime->format('Y-m-d');
//        $time = $datetime->format('H:i:s');
//        
//        $query = "UPDATE documentEvents SET time = :time, date = :date WHERE documentEventID = :checklistID";
//        $statement = $db->prepare($query);
//        $statement->bindParam(":time", $time, PDO::PARAM_STR);
//        $statement->bindParam(":date", $date, PDO::PARAM_STR); 
//        $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
//
//        $result = $statement->execute();
//
//        echo $result;
//    } 
//    else if ($access_code === "site")
//    {
//        $datetime = new DateTime($start_date);
//
//        $date = $datetime->format('Y-m-d');
//        $time = $datetime->format('H:i:s');
//        
//        $query = "UPDATE siteNews SET time = :time, date = :date WHERE siteNewsID = :checklistID";
//        $statement = $db->prepare($query);
//        $statement->bindParam(":time", $time, PDO::PARAM_STR);
//        $statement->bindParam(":date", $date, PDO::PARAM_STR); 
//        $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
//
//        $result = $statement->execute();
//
//        echo $result;
//    } 
    else if ($access_code === "team_calender_move") {
        $start_date = ltrim(rtrim(filter_input(INPUT_POST, "start_date", FILTER_SANITIZE_STRING)));
        $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));
        $checklist_id = ltrim(rtrim(filter_input(INPUT_POST, "checklist_id", FILTER_SANITIZE_NUMBER_INT)));
        $add_ids = ltrim(rtrim(filter_input(INPUT_POST, "add_id", FILTER_SANITIZE_STRING)));
        $delete_ids = ltrim(rtrim(filter_input(INPUT_POST, "delete_id", FILTER_SANITIZE_STRING)));
        $internal_access_code = ltrim(rtrim(filter_input(INPUT_POST, "internal_access_code", FILTER_SANITIZE_STRING)));
        $new_build = ltrim(rtrim(filter_input(INPUT_POST, "new_build", FILTER_SANITIZE_STRING)));
//        echo "Add ID: " . $add_ids . " Delete ID: " . $delete_ids . " Checklist ID: " . $checklist_id; //exit();

        if ($internal_access_code === "checklist") {
            $query = "UPDATE checklist INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET checklist.technician = CASE WHEN look_for.technicianFullName = checklist.technician THEN add_tech.technicianFullName ELSE checklist.technician END, checklist.overNightSupport = CASE WHEN look_for.technicianFullName = checklist.overNightSupport THEN add_tech.technicianFullName ELSE checklist.overNightSupport END WHERE checklist.checklistID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "UPDATE overnightSupport INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET overnightSupport.technician = CASE WHEN look_for.technicianFullName = overnightSupport.technician THEN add_tech.technicianFullName ELSE overnightSupport.technician END WHERE overnightSupport.checklistID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "UPDATE checklist_technician INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET checklist_technician.technician = CASE WHEN look_for.technicianFullName = checklist_technician.technician THEN add_tech.technicianFullName ELSE checklist_technician.technician END WHERE checklist_technician.checklistID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "UPDATE liveSiteEvents INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET liveSiteEvents.technician = CASE WHEN look_for.technicianFullName = liveSiteEvents.technician THEN add_tech.technicianFullName ELSE liveSiteEvents.technician END WHERE liveSiteEvents.checklistID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "UPDATE siteContact INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET siteContact.name = CASE WHEN look_for.technicianFullName = siteContact.name THEN add_tech.technicianFullName ELSE siteContact.name END WHERE siteContact.checklistID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "UPDATE technician SET activitiesInProgress = CASE WHEN technicianID = " . $delete_ids . " THEN (activitiesInProgress - 1) WHEN technicianID = " . $add_ids . " THEN (activitiesInProgress + 1) ELSE activitiesInProgress END";
//            echo $query; exit();
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->execute();


//            $query = "UPDATE technician INNER JOIN checklist ON checklist.checklistID = :checklist_id SET activitiesInProgress = CASE activitiesInProgress WHEN technicianID = :delete_id THEN (activitiesInProgress - 1) WHEN technicianID = :add_id THEN (activitiesInProgress + 1) ELSE activitiesInProgress END, activitiesComplete = CASE activitiesComplete WHEN technicianID = :delete_id1 AND checklist.status = 3 THEN (activitiesComplete - 1) WHEN technicianID = :add_id1 AND checklist.status = 3 THEN (activitiesComplete + 1) ELSE activitiesComplete END";
//            $statement = $db->prepare($query);
//            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
//            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);  
//            $statement->bindParam(":delete_id1", $delete_ids, PDO::PARAM_INT);
//            $statement->bindParam(":add_id1", $add_ids, PDO::PARAM_INT); 
//            $statement->bindParam(":checklist_id", $checklist_id, PDO::PARAM_INT); 
//            $statement->execute();
        } else if ($internal_access_code === "upcoming_site") {
            $query = "UPDATE upcomingSiteEvents INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET upcomingSiteEvents.technician = CASE WHEN look_for.technicianFullName = upcomingSiteEvents.technician THEN add_tech.technicianFullName ELSE upcomingSiteEvents.technician END WHERE upcomingSiteEvents.upcomingSiteEventID = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();
        } else if ($internal_access_code === "documentEvents") {
//            $query = "UPDATE other_events INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET other_events.technician = CASE WHEN look_for.technicianFullName = other_events.technician THEN add_tech.technicianFullName ELSE other_events.technician END WHERE other_events.event_id = :checklistID";
//            $statement = $db->prepare($query);
//            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
//            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
//            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
//            $statement->execute();
        } else if ($internal_access_code === "other") {
            $query = "UPDATE other_events INNER JOIN technician AS look_for ON look_for.technicianID = :delete_id INNER JOIN technician AS add_tech ON add_tech.technicianID = :add_id SET other_events.technician = CASE WHEN look_for.technicianFullName = other_events.technician THEN add_tech.technicianFullName ELSE other_events.technician END WHERE other_events.event_id = :checklistID";
            $statement = $db->prepare($query);
            $statement->bindParam(":delete_id", $delete_ids, PDO::PARAM_INT);
            $statement->bindParam(":add_id", $add_ids, PDO::PARAM_INT);
            $statement->bindParam(":checklistID", $checklist_id, PDO::PARAM_INT);
            $statement->execute();
        }
        //Next Step - Update EVERYTHING created by checklist/site. Check if new build, if new build update site stuff as well. Then same for overnight tech ;(
        //
        //Tables To Update: 
        //checklist_technician, liveSiteEvents, siteContact, overnightSupport
        //
        //if(upcoming_site) {
        //  upcomingSiteEvents
        //}
        //
        //if(new_build) 
        //{
        //  site, site_technician
        //}
    } else if ($access_code === "new_event") {
        $user_name = ltrim(rtrim(filter_input(INPUT_POST, "user_name", FILTER_SANITIZE_STRING)));
        $event_type = ltrim(rtrim(filter_input(INPUT_POST, "event_type", FILTER_SANITIZE_STRING)));
        $start_date = ltrim(rtrim(filter_input(INPUT_POST, "start_date", FILTER_SANITIZE_STRING)));
        $event = ltrim(rtrim(filter_input(INPUT_POST, "event", FILTER_SANITIZE_STRING)));
        $event = $user_name . " - " . $event;

        if ($event_type === "other") {
            $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));

            $allDayEvent = false;

            $start_date_object = new DateTime($start_date);
            $end_date_object = new DateTime($end_date);

            $days_difference = $start_date_object->diff($end_date_object)->format("%a");

            if ($days_difference >= 1) {
                $allDayEvent = true;
            }

            $query = "INSERT INTO other_events (event, start, end, technician, link, event_type, image, allDayEvent) VALUES (:event, :start, :end, :technician, '#', :event_type, 'images/SiteIcons/SiteProgress.png', '" . $allDayEvent . "')";
            $statement = $db->prepare($query);
            $statement->bindParam(":event", $event, PDO::PARAM_STR);
            $statement->bindParam(":start", $start_date, PDO::PARAM_STR);
            $statement->bindParam(":end", $end_date, PDO::PARAM_STR);
            $statement->bindParam(":technician", $user_name, PDO::PARAM_STR);
            $statement->bindParam(":event_type", $event_type, PDO::PARAM_STR);
            echo $statement->execute();
        } else if ($event_type === "upcoming_site") {
            $site_code = ltrim(rtrim(filter_input(INPUT_POST, "site_code", FILTER_SANITIZE_STRING)));
            $activity_type = ltrim(rtrim(filter_input(INPUT_POST, "activity_type", FILTER_SANITIZE_STRING)));
            $end_date = ltrim(rtrim(filter_input(INPUT_POST, "end_date", FILTER_SANITIZE_STRING)));
            $country = ltrim(rtrim(filter_input(INPUT_POST, "country", FILTER_SANITIZE_STRING)));
            $county = ltrim(rtrim(filter_input(INPUT_POST, "county", FILTER_SANITIZE_STRING)));
            $town = ltrim(rtrim(filter_input(INPUT_POST, "town", FILTER_SANITIZE_STRING)));
            $event = "Site " . $site_code . " - " . $activity_type;

            $allDayEvent = false;

            $start_date_object = new DateTime($start_date);
            $end_date_object = new DateTime($end_date);

            $days_difference = $start_date_object->diff($end_date_object)->format("%a");

            if ($days_difference >= 1) {
                $allDayEvent = true;
            }

            $image = "images/SiteIcons/SiteActivityType/" . $activity_type . ".png";
            $image = str_replace(' ', '', $image);

            $datetime = new DateTime($start_date);
            $date = $datetime->format('Y-m-d');
            $time = $datetime->format('H:i:s');

            $query = "INSERT INTO upcomingSiteEvents (siteCode, event, technician, date, time, expectedGoLiveDate, link, image, allDayEvent, event_type, activity_type, event_country, event_county, event_town) VALUES (:siteCode, :event, :technician, :date, :time, :expectedGoLiveDate, '#', '" . $image . "', '" . $allDayEvent . "', :event_type, :activity_type, :event_country, :event_county, :event_town)";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $site_code, PDO::PARAM_STR);
            $statement->bindParam(":event", $event, PDO::PARAM_STR);
            $statement->bindParam(":technician", $user_name, PDO::PARAM_STR);
            $statement->bindParam(":date", $date, PDO::PARAM_STR);
            $statement->bindParam(":time", $time, PDO::PARAM_STR);
            $statement->bindParam(":expectedGoLiveDate", $end_date, PDO::PARAM_STR);
            $statement->bindParam(":event_type", $event_type, PDO::PARAM_STR);
            $statement->bindParam(":activity_type", $activity_type, PDO::PARAM_STR);
            $statement->bindParam(":event_country", $country, PDO::PARAM_STR);
            $statement->bindParam(":event_county", $county, PDO::PARAM_STR);
            $statement->bindParam(":event_town", $town, PDO::PARAM_STR);

            echo $statement->execute();
        } else if ($event_type === "site") {
            $site_code = ltrim(rtrim(filter_input(INPUT_POST, "site_code", FILTER_SANITIZE_STRING)));

            //Check if site code exists then send error
            $check_if_site_exists_query = "SELECT siteCode FROM site WHERE siteCode = :siteCode";
            $check_if_site_exists_statement = $db->prepare($check_if_site_exists_query);
            $check_if_site_exists_statement->bindParam(":siteCode", $site_code, PDO::PARAM_STR);
            $check_if_site_exists_statement->execute();

            $check_if_site_exists_results = $check_if_site_exists_statement->fetchAll(PDO::FETCH_ASSOC);

            if (count($check_if_site_exists_results) > 0) {

                $datetime = new DateTime($start_date);
                $date = $datetime->format('Y-m-d');
                $time = $datetime->format('H:i:s');

                $image = "images/SiteIcons/ModalIcons/ActivityType.png";
                $event = "Site " . $site_code . " - " . $event;

                $query = "INSERT INTO siteNews (siteCode, event, technician, date, time, link, image, allDayEvent, event_type) VALUES (:siteCode, :event, :technician, :date, :time, '#', '" . $image . "', 'false', :event_type)";
                $statement = $db->prepare($query);
                $statement->bindParam(":siteCode", $site_code, PDO::PARAM_STR);
                $statement->bindParam(":event", $event, PDO::PARAM_STR);
                $statement->bindParam(":technician", $user_name, PDO::PARAM_STR);
                $statement->bindParam(":date", $date, PDO::PARAM_STR);
                $statement->bindParam(":time", $time, PDO::PARAM_STR);
                $statement->bindParam(":event_type", $event_type, PDO::PARAM_STR);

                echo $statement->execute();
            } else {
                echo "Site Code does not exist!";
                exit();
            }
        }
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}