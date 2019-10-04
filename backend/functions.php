<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function insertRecords($db, $genericSelectIDsArray, $genericTable, $genericInsertValuesArray, $genericInsertTable, $ActivityChecklist = "") {
    //Get count of all elements to search in DB for
    $genericCountSelect = count($genericSelectIDsArray);
    //Start creating our query
    $genericSelectQuery = "SELECT ";
    $insertCategories = "";
    $insertCategoriesCount = 0;

    //Get results of our query
    $genericResult = selectStatement($db, $genericCountSelect, $genericSelectQuery, $insertCategories, $insertCategoriesCount, $genericSelectIDsArray, $genericTable, $ActivityChecklist);

    $genericCountResult = count($genericResult);
    $genericCountInsert = count($genericInsertValuesArray);

    $preparedStatement = buildInsertStatement($genericResult, $genericInsertValuesArray, $genericCountResult, $genericCountInsert, $insertCategoriesCount, $insertCategories);

    //Insert
    try {
        $query = "INSERT INTO " . $genericInsertTable . "(" . $insertCategories . ") VALUES "; //Prequery
        $qPart = array_fill(0, count($genericResult), $preparedStatement);
        $query .= implode(",", $qPart);

        $stmt = $db->prepare($query);
        $i = 1;

        foreach ($genericResult as $item) { //bind the values one by one   
            for ($j = 0; $j < $insertCategoriesCount; $j++) {
                $stmt->bindValue($i++, $item[array_keys($item)[$j]]);
            }
        }

        $stmt->execute(); //execute
        return "Success";
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function selectStatement(&$db, &$genericCountSelect, &$genericSelectQuery, &$insertCategories, &$insertCategoriesCount, $genericSelectIDsArray, $genericTable, $ActivityChecklist) {
    if ($ActivityChecklist !== "") {
        $optionalFields = $ActivityChecklist->getOptionalFields();
//        $cacheProxy = $ActivityChecklist->getCacheProxy();
//        $cacheProxyState = $ActivityChecklist->getCacheProxyState();
//        $nvr = $ActivityChecklist->getNVR();
//        $nvrState = $ActivityChecklist->getNVRState(); 
    }

    //Loop through all fields we need to search for
    for ($i = 0; $i < $genericCountSelect; $i++) {
        $genericSelectQuery .= $genericSelectIDsArray[$i];
        $insertCategories .= $genericSelectIDsArray[$i];

        $insertCategoriesCount++;

        if ($genericCountSelect > 1 && $i !== $genericCountSelect - 1) {
            $genericSelectQuery .= ", ";
            $insertCategories .= ", ";
        }
    }
    $insertCategories .= ", ";

    //Finish building query
    if ($ActivityChecklist !== "") {
        $genericSelectQuery = multipleORStatements($genericSelectQuery, $optionalFields, $genericTable);
    } else {
        $genericSelectQuery .= " FROM " . $genericTable;
    }

    try {
        $genericStatement = $db->prepare($genericSelectQuery);
        $genericStatement->execute();

        return $genericStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

//ADD OPTIONAL FIELDS HERE
function multipleORStatements($genericSelectQuery, $optionalFields, $genericTable) {
    $count = count($optionalFields);
    $itterator = 0;

    $genericSelectQuery .= " FROM " . $genericTable . " WHERE ";
    foreach ($optionalFields as $value) {
        if ($value["optionalState"] == "on") {
            $genericSelectQuery .= "optionalCategoryName = '" . $value["optionalField"] . "'";

            if ($count > 1 && $itterator < $count - 1) {
                $genericSelectQuery .= " OR ";
            }
        }
        $itterator++;
    }

    return $genericSelectQuery;
}

function buildInsertStatement(&$genericResult, $genericInsertValuesArray, $genericCountResult, &$genericCountInsert, &$insertCategoriesCount, &$insertCategories) {
    //Our goal is to make a prepared statement placeholder, start here (?, ?, ?)
    $preparedStatement = "(";

    //Add our other categories to the results
    for ($j = 0; $j < $genericCountResult; $j++) {
        foreach ($genericInsertValuesArray as $key => $value) {
            $genericResult[$j] += [$key => $value];
        }
    }

    $count = 0;
    //Build our insert query along with adding the final category that we will be using to insert into
    foreach ($genericInsertValuesArray as $key => $value) {
        $insertCategories .= $key;

        if ($genericCountInsert > 1 && $count !== $genericCountInsert - 1) {
            $insertCategories .= ", ";
        }
        $count++;
        $insertCategoriesCount++;
    }

    //Continue creating prepared statement string
    for ($i = 0; $i < $insertCategoriesCount; $i++) {
        $preparedStatement .= "?";

        if ($insertCategoriesCount > 1 && $i !== $insertCategoriesCount - 1) {
            $preparedStatement .= ",";
        }
    }

    return $preparedStatement .= ")";
}

function createLiveSiteEvent($db, $event, $technician, $image, $siteCode, $link, $checklistID) {
    $date = date("Y:m:d");
    $time = date("H:i:s");
    try {
        $liveEventQuery = "INSERT INTO liveSiteEvents (siteCode, checklistID, event, technician, date, time, image, link, status) VALUES (:siteCode, '" . $checklistID . "', '" . $event . "', :technician, '" . $date . "', '" . $time . "', '" . $image . "', '" . $link . "', '1')";
        $liveEventStatement = $db->prepare($liveEventQuery);
        $liveEventStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_INT);
        $liveEventStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $liveEventStatement->execute();

        return "Success";
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function getSiteDataDisplay($db, $siteCode) {
    //WIP\\
    $getSiteDataQuery = "SELECT site.siteCode, site.siteRegion, site.siteType, site.siteCountry, site.siteCounty, site.siteTown, site.activityType, site.technician  FROM liveSiteEvents WHERE siteCode = ':siteCode' AND complete = 'true'";
    $getSiteDataStatement = $db->prepare($getSiteDataQuery);
    $getSiteDataStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
    $getSiteDataStatement->execute();
}

function getSiteDataActivity($db, $siteCode) {
    try {
        $getSiteDataActivityQuery = "SELECT site.siteCountry, site.siteCounty, site.siteTown, site.siteType, region.region FROM site, region WHERE region.regionID = site.siteRegion AND siteCode = :siteCode";
        $getSiteDataActivityStatement = $db->prepare($getSiteDataActivityQuery);
        $getSiteDataActivityStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
        $getSiteDataActivityStatement->execute();

        return $getSiteDataActivityStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

//Check database if the activity created already exists or hasn't yet been completed
function checkIfActivityExists($db, $siteCode, $activityType) {
    try {
        //UNION ALL these two queries
        $existingSiteQuery1 = "SELECT siteCode FROM site WHERE siteCode = :siteCode AND status = '3'";
        $existingSiteStatement1 = $db->prepare($existingSiteQuery1);
        $existingSiteStatement1->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
        $existingSiteStatement1->execute();

        $resultCount1 = $existingSiteStatement1->rowCount();

        //$existingSiteQuery2 = "SELECT activityType FROM checklist WHERE siteCode = :siteCode AND complete = 'false' AND status != '3' AND activityType = :activityType";
        $existingSiteQuery2 = "SELECT activityType FROM checklist WHERE siteCode = :siteCode AND status != '3' AND activityType = :activityType";
        $existingSiteStatement2 = $db->prepare($existingSiteQuery2);
        $existingSiteStatement2->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
        $existingSiteStatement2->bindParam(":activityType", $activityType, PDO::PARAM_STR);
        $existingSiteStatement2->execute();

        $resultCount2 = $existingSiteStatement2->rowCount();
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    //If the site is not complete or doesn't exist, throw error
    if ($resultCount1 !== 1) {
        echo 'The Site you wish to perform the following activity on: "' . $activityType . '" has still not been completed or doesnt yet exist.';
        exit();
    } else if ($resultCount2 >= 1) { //If the activity already exists, error.
        echo 'There is already a Site with the Site Code: "' . $siteCode . '" with the following activity: "' . $activityType . '" in progress at the moment, delete the Activity or input a different Site to continue.';
        exit();
    }
}

//Check database if site to be created already exists
function checkIfSiteExists($db, $siteCode, $activityType) {
    try {
        //UNION ALL these two queries
        $existingSiteQuery1 = "SELECT siteCode FROM site WHERE siteCode = :siteCode";
        $existingSiteStatement1 = $db->prepare($existingSiteQuery1);
        $existingSiteStatement1->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
        $existingSiteStatement1->execute();

        $resultCount1 = $existingSiteStatement1->rowCount();
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    //If the site is not complete or doesn't exist, throw error
    if ($resultCount1 >= 1) { //If the activity already exists, error.
        echo 'Site "' . $siteCode . '" already exists, input a different site code to continue.';
        exit();
    }
}

//Login
function loginTechnician($db, $email, $password) {
    try {
        $loginQuery = "SELECT technicianID AS 'id',email, technicianFullName, avatar, workingSince, activitiesComplete, activitiesInProgress, documentsUpdated, documentsCreated, accessLevel FROM technician WHERE email = :email AND BINARY password = :password";
        $loginStatement = $db->prepare($loginQuery);
        $loginStatement->bindParam(":email", $email, PDO::PARAM_STR);
        $loginStatement->bindParam(":password", $password, PDO::PARAM_STR);
        $loginStatement->execute();

        $loginResultCount = $loginStatement->rowCount();
        $loginResult = $loginStatement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    if ($loginResultCount < 1) { //If the activity already exists, error.
        echo 'Something went wrong and you could not be logged in. This could be either due to a wrong email or password. Please try again.';
        exit();
    }

    return $loginResult;
}

//Send email
function sendEmail($email, $subject, $body, $successResponse) {
    require "phpmailer/PHPMailer.php";
    require "phpmailer/SMTP.php";

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'Smtp.sendgrid.net';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'D00192082';                 // SMTP username
        $mail->Password = '3820065Np2!';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        //Recipients
        $mail->setFrom("D00192082@student.dkit.ie", "Shane Dollard");
        $mail->addAddress($email);
        //$mail->addAddress('nikito888@gmail.com', $name[0]); 
        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;

        $response = $mail->send();

        if ($response) {
            return $successResponse;
        } else {
            return "The email has failed to send, please wait a bit and try again.";
        }
    } catch (Exception $ex) {
        return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}

//Insert into array by index
function insert($jsonColumnsArray, $index, $val) {
    $size = count($jsonColumnsArray); //because I am going to use this more than one time
    if (!is_int($index) || $index < 0 || $index > $size) {
        return -1;
    } else {
        $temp = array_slice($jsonColumnsArray, 0, $index);
        $temp[] = $val;
        return array_merge($temp, array_slice($jsonColumnsArray, $index, $size));
    }
}

function createSiteNewsEvent($db, $event, $technician, $image, $link, $siteCode, $allDayEvent, $event_type, $checklistID) {
    $date = date("Y:m:d");
    $time = date("H:i:s");

    try {
        $liveEventQuery = "INSERT INTO siteNews (checklistID, siteCode, event, technician, date, time, image, link, allDayEvent, event_type) VALUES (:checklistID, :siteCode, '" . $event . "', :technician, '" . $date . "', '" . $time . "', '" . $image . "', '" . $link . "', '" . $allDayEvent . "', '" . $event_type . "')";
        $liveEventStatement = $db->prepare($liveEventQuery);
        $liveEventStatement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
        $liveEventStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
        $liveEventStatement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $liveEventStatement->execute();

        return "Success";
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function checkIfSiteNewsExists($db, $checklistID, $event) {
    try {
        $query = "SELECT COUNT(*) FROM siteNews WHERE checklistID = :checklistID AND event = :event";
        $statement = $db->prepare($query);
        $statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
        $statement->bindParam(":event", $event, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_NUM);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }

    if ($result[0][0] > 0) {
        return false;
    } else {
        return true;
    }
}

function setStatus($db, $siteCode, $key, $technician, $overnightSupport, $time, $allDayEvent, $checklistID) {
    $categoriesPercent = calculatePercentComplete($db, $key, "checklist_checklistOptionalCategories", "checklist_checklistCategories", "optionalCategoryStatus", "categoryStatus", "3", false);
    $tasksPercent = calculatePercentComplete($db, $key, "checklist_checklistOptionalTasks", "checklist_checklistTasks", "taskCompleted", "taskCompleted", "'true'", true);

    try {
        if ($categoriesPercent === 100) {
            $select_activity_query = "SELECT activityType FROM checklist WHERE checklistID = :checklistID";
            $select_activity_statement = $db->prepare($select_activity_query);
            $select_activity_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
            $select_activity_statement->execute();

            $activity_result = $select_activity_statement->fetchAll(PDO::FETCH_ASSOC);

            $event = $siteCode . " " . $activity_result[0]["activityType"] . " - " . "Finished";
            $image = "images/SiteIcons/SiteActivityType/NewBuild.png";
            $link = "site.php?checklistID=" . $key;

            setChecklistStatus($db, $key, 3, "status");
            setChecklistStatus($db, $key, 'true', "complete");

            if (checkIfSiteNewsExists($db, $checklistID, $event)) {
                createSiteNewsEvent($db, $event, $technician, $image, $link, $siteCode, $allDayEvent, "site", $checklistID);

                $query = "DELETE FROM liveSiteEvents WHERE checklistID = :checklistID";
                $statement = $db->prepare($query);
                $statement->bindParam(":checklistID", $key, PDO::PARAM_STR);
                $statement->execute();

                $sanitizedTechnicianArray = array_map('strip_tags', $overnightSupport);
                $technicianCount = count($sanitizedTechnicianArray);

                $andStatement = "";

                for ($i = 0; $i < $technicianCount; $i++) {
                    $andStatement .= "'" . $sanitizedTechnicianArray[$i] . "', ";
                }

                $updateTechnicianQuery = "UPDATE technician SET activitiesInProgress = activitiesInProgress - 1, activitiesComplete = activitiesComplete + 1 WHERE technicianFullName IN(" . $andStatement . "'" . $technician . "'" . ")";
                $updateTechnicianStatement = $db->prepare($updateTechnicianQuery);
                $updateTechnicianStatement->execute(); 

                $activities_complete_query = "SELECT activitiesComplete, technicianFullName FROM technician WHERE technicianFullName IN(" . $andStatement . "'" . $technician . "'" . ")";
                $statement = $db->prepare($activities_complete_query);
                $statement->execute();

                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result_count = count($result);

                $date = date("Y-m-d");
                $time = date("H:i:s");

                for ($i = 0; $i < $result_count; $i++) {
                    if ($result[$i]["activitiesComplete"] % 10 == 0 && $result[$i]["activitiesComplete"] !== 0) {
                        $event = $result[$i]["technicianFullName"] . " has completed " . $result[$i]["activitiesComplete"] . " site activities! Congratulations " . explode(' ', $result[$i]["technicianFullName"])[0] . "!";

                        $query = "INSERT INTO other_events (event, start, technician, link, event_type, image, allDayEvent) VALUES ('" . $event . "', '" . $date . " " . $time . "', '" . $result[$i]["technicianFullName"] . "', '#', 'other', 'images/SiteIcons/ModalIcons/ActivityType.png', 0)";
                        $statement = $db->prepare($query);
                        $statement->execute();
                    }
                }
            }
        } else if ($tasksPercent >= 75 && $tasksPercent < 100) {
            $select_activity_query = "SELECT activityType FROM checklist WHERE checklistID = :checklistID";
            $select_activity_statement = $db->prepare($select_activity_query);
            $select_activity_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
            $select_activity_statement->execute();

            $activity_result = $select_activity_statement->fetchAll(PDO::FETCH_ASSOC);

            $event = $siteCode . " " . $activity_result[0]["activityType"] . " - " . "75% Done";
            $image = "images/SiteIcons/SiteProgress.png";
            $link = "site.php?checklistID=" . $key;

            setChecklistStatus($db, $key, 2, "status");

            if (checkIfSiteNewsExists($db, $checklistID, $event)) {
                createSiteNewsEvent($db, $event, $technician, $image, $link, $siteCode, false, "site", $checklistID);
            }
        } else if ($tasksPercent >= 50 && $tasksPercent < 75) {
            $select_activity_query = "SELECT activityType FROM checklist WHERE checklistID = :checklistID";
            $select_activity_statement = $db->prepare($select_activity_query);
            $select_activity_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
            $select_activity_statement->execute();

            $activity_result = $select_activity_statement->fetchAll(PDO::FETCH_ASSOC);

            $event = $siteCode . " " . $activity_result[0]["activityType"] . " - " . "50% Done";
            $image = "images/SiteIcons/SiteProgress.png";
            $link = "site.php?checklistID=" . $key;

            setChecklistStatus($db, $key, 2, "status");

            if (checkIfSiteNewsExists($db, $checklistID, $event)) {
                createSiteNewsEvent($db, $event, $technician, $image, $link, $siteCode, false, "site", $checklistID);
            }
        } else if ($tasksPercent >= 25 && $tasksPercent < 50) {
            $select_activity_query = "SELECT activityType FROM checklist WHERE checklistID = :checklistID";
            $select_activity_statement = $db->prepare($select_activity_query);
            $select_activity_statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
            $select_activity_statement->execute();

            $activity_result = $select_activity_statement->fetchAll(PDO::FETCH_ASSOC);

            $event = $siteCode . " " . $activity_result[0]["activityType"] . " - " . "25% Done";
            $image = "images/SiteIcons/SiteProgress.png";
            $link = "site.php?checklistID=" . $key;

            setChecklistStatus($db, $key, 2, "status");

            if (checkIfSiteNewsExists($db, $checklistID, $event)) {
                createSiteNewsEvent($db, $event, $technician, $image, $link, $siteCode, false, "site", $checklistID);
            }
        } else if ($tasksPercent > 0 && $tasksPercent < 25) {
            setChecklistStatus($db, $key, 2, "status");
        } else if ($tasksPercent === 0) {
            setChecklistStatus($db, $key, 1, "status");
        } 

        if ($categoriesPercent == 100) 
        {
            //Update site table
            $updateSiteQuery = "UPDATE site SET completedOn = :date, status = 3 WHERE siteCode = :siteCode";
            $updateSiteStatement = $db->prepare($updateSiteQuery);
            $updateSiteStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $updateSiteStatement->bindParam(":date", $time, PDO::PARAM_STR);
            $updateSiteStatement->execute();
        } else if ($tasksPercent > 0 && $categoriesPercent < 100) {
            //Update site table
            $updateSiteQuery = "UPDATE site SET status = 2 WHERE siteCode = :siteCode";
            $updateSiteStatement = $db->prepare($updateSiteQuery);
            $updateSiteStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $updateSiteStatement->execute();
        } else {
            $updateSiteQuery = "UPDATE site SET status = 1 WHERE siteCode = :siteCode";
            $updateSiteStatement = $db->prepare($updateSiteQuery);
            $updateSiteStatement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $updateSiteStatement->execute();
        }

        return $tasksPercent;
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function setChecklistStatus($db, $key, $categoryStatus, $column) {
    try {
        $updateCategoryStatusQuery = "UPDATE checklist SET " . $column . " = '" . $categoryStatus . "' WHERE checklistID = :checklistID";
        $updateCategoryStatusStatement = $db->prepare($updateCategoryStatusQuery);
        $updateCategoryStatusStatement->bindParam(":checklistID", $key, PDO::PARAM_STR);

        $updateCategoryStatusStatement->execute();
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function calculatePercentComplete($db, $key, $tableName1, $tableName2, $condition1, $condition2, $value, $use_contact_tasks) {
    try {
        if ($use_contact_tasks) {
            $contact_tasks_query = "SELECT COUNT(*) FROM siteContact WHERE checklistID = :checklistID AND taskCompleted = 'true'";
            $contact_tasks_statement = $db->prepare($contact_tasks_query);
            $contact_tasks_statement->bindParam(":checklistID", $key, PDO::PARAM_STR);
            $contact_tasks_statement->execute();

            $contact_tasks_result = $contact_tasks_statement->fetchAll(PDO::FETCH_NUM);
        }

        $queryCount1 = "SELECT COUNT(*),(SELECT COUNT(*) FROM " . $tableName1 . " WHERE checklistID = :checklistIDOptional) FROM " . $tableName2 . " WHERE checklistID = :checklistID";
        $statementCount1 = $db->prepare($queryCount1);
        $statementCount1->bindParam(":checklistID", $key, PDO::PARAM_STR);
        $statementCount1->bindParam(":checklistIDOptional", $key, PDO::PARAM_STR);
        $statementCount1->execute();

        $result1 = $statementCount1->fetchAll(PDO::FETCH_NUM);
        $result1Count = count($result1[0]);
        $allCategoriesCount = 0;

        for ($i = 0; $i < $result1Count; $i++) {
            $allCategoriesCount += (int) $result1[0][$i];
        }

        $queryCount2 = "SELECT COUNT(*),(SELECT COUNT(*) FROM " . $tableName1 . " WHERE checklistID = :checklistIDOptional AND " . $condition1 . " = " . $value . " ) FROM " . $tableName2 . " WHERE checklistID = :checklistID AND " . $condition2 . " = " . $value . " ";
        $statementCount2 = $db->prepare($queryCount2);
        $statementCount2->bindParam(":checklistID", $key, PDO::PARAM_STR);
        $statementCount2->bindParam(":checklistIDOptional", $key, PDO::PARAM_STR);
        $statementCount2->execute();

        $result2 = $statementCount2->fetchAll(PDO::FETCH_NUM);
        $result2Count = count($result2[0]);
        $totalCompleteCategoryCount = 0;

        for ($i = 0; $i < $result2Count; $i++) {
            $totalCompleteCategoryCount += (int) $result2[0][$i];
        }

        if ($use_contact_tasks) {
            $contact_tasks_count = count($contact_tasks_result[0]);
            for ($i = 0; $i < $contact_tasks_count; $i++) {
                $totalCompleteCategoryCount += (int) $contact_tasks_result[0][$i];
            }
        }

        $percentageComplete = ($totalCompleteCategoryCount / $allCategoriesCount) * 100;
        $finalPercent = (int) round($percentageComplete);

        return $finalPercent;
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function get_all_technicians($db) {
    try {
        $query = "SELECT technicianID AS 'id', technicianFullName AS 'title', avatar FROM technician WHERE technicianFullName != 'Shane Dollard' ORDER BY technicianFullName";
        $statement = $db->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}

function append_overnight_technicians($db, $loopArray, $mainEmpty, &$siteTypeSiteRegionSiteActivityResult, &$siteTypeSiteRegionSiteActivityResultOvernight) {
    foreach ($loopArray as $key => $value) {
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

        if (!$mainEmpty) {
            $siteTypeSiteRegionSiteActivityResult[$key]["overNightSupport"] = $overNightSupportAppend;
        } else {
            $siteTypeSiteRegionSiteActivityResultOvernight[$key]["overNightSupport"] = $overNightSupportAppend;
        }
    }

//    if (!$mainEmpty) {
//        print_r($siteTypeSiteRegionSiteActivityResult);
//    } else {
//        print_r($siteTypeSiteRegionSiteActivityResultOvernight);
//    } 
}

function create_technician_replacement_query($case, $changes_count, $changes, $original_technician_count, $original_technician) {
    //print_r($changes);
    //print_r($original_technician);

    $apend_fields_technician = "";
    $counter = 0;


    for ($j = 0; $j <= $changes_count; $j++) {
        if ($counter < $original_technician_count) {
            $apend_fields_technician .= $case . " = CASE WHEN " . $case . " = '" . $original_technician[$counter]["technician_name"] . "' THEN '" . $changes[$j]["technicianFullName"] . "' ELSE " . $case . " END";
            $counter++;
        }

        if ($changes_count > 1 && $j < $changes_count - 1) {
            $apend_fields_technician .= ", ";
        }

//        if ($j == 0) {
//            //echo $inside_changes_count;
//            //echo $changes[$j]["technicianFullName"];
//            echo $apend_fields_technician;
//            exit();
//        }
    }

    return $apend_fields_technician;
}

function create_category_replacement_query($changes, $original_categories, $case) {
    $changes_count = count($changes);
    $original_technician_count = count($original_categories);

    $apend_fields_technician = "";
    $counter = 0;


    for ($j = 0; $j <= $changes_count; $j++) {
        if ($counter < $original_technician_count) {
            $apend_fields_technician .= $case . " = CASE WHEN " . $case . " = '" . $original_categories[$counter][$case] . "' THEN '" . $changes[$j][$case] . "' ELSE " . $case . " END";
            $counter++;
        }

        if ($changes_count > 1 && $j < $changes_count - 1) {
            $apend_fields_technician .= ", ";
        }

//        if ($j == 0) {
//            //echo $inside_changes_count;
//            //echo $changes[$j]["technicianFullName"];
//            echo $apend_fields_technician;
//            exit();
//        }
    }

    return $apend_fields_technician;
}

function check_if_site_upcoming($db, $site_code, $event_country) {
    $query = "SELECT event FROM upcomingSiteEvents WHERE siteCode = :site_code AND event_country = :country";
    $statement = $db->prepare($query);
    $statement->bindParam(":site_code", $site_code, PDO::PARAM_STR);
    $statement->bindParam(":country", $event_country, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) >= 1) {
        $delete_query = "DELETE FROM upcomingSiteEvents WHERE siteCode = :site_code AND event_country = :country";
        $delete_statement = $db->prepare($delete_query);
        $delete_statement->bindParam(":site_code", $site_code, PDO::PARAM_STR);
        $delete_statement->bindParam(":country", $event_country, PDO::PARAM_STR);

        $delete_statement->execute();
    }
}

function delete_checklist_data($db, $where_in_site_code, $where_in_checklist_id) {
    $delete_checklist_query_1 = "DELETE FROM checklist_checklistCategories WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_1 = $db->prepare($delete_checklist_query_1);
    $delete_checklist_statement_1->execute();

    $delete_checklist_query_2 = "DELETE FROM checklist_checklistOptionalCategories WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_2 = $db->prepare($delete_checklist_query_2);
    $delete_checklist_statement_2->execute();

    $delete_checklist_query_3 = "DELETE FROM checklist_checklistTabs WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_3 = $db->prepare($delete_checklist_query_3);
    $delete_checklist_statement_3->execute();

    $delete_checklist_query_4 = "DELETE FROM checklist_checklistOptionalTabs WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_4 = $db->prepare($delete_checklist_query_4);
    $delete_checklist_statement_4->execute();

    $delete_checklist_query_5 = "DELETE FROM checklist_checklistTasks WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_5 = $db->prepare($delete_checklist_query_5);
    $delete_checklist_statement_5->execute();

    $delete_checklist_query_6 = "DELETE FROM checklist_checklistOptionalTasks WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_6 = $db->prepare($delete_checklist_query_6);
    $delete_checklist_statement_6->execute();

    $delete_checklist_query_7 = "DELETE FROM checklist_siteActivityType WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_7 = $db->prepare($delete_checklist_query_7);
    $delete_checklist_statement_7->execute();

    $delete_checklist_query_8 = "DELETE FROM checklist_technician WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_8 = $db->prepare($delete_checklist_query_8);
    $delete_checklist_statement_8->execute();

    $delete_checklist_query_9 = "DELETE FROM overnightSupport WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_9 = $db->prepare($delete_checklist_query_9);
    $delete_checklist_statement_9->execute();

    $delete_checklist_query_10 = "DELETE FROM siteContact WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_10 = $db->prepare($delete_checklist_query_10);
    $delete_checklist_statement_10->execute();

    $delete_checklist_query_12 = "DELETE FROM liveSiteEvents WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_12 = $db->prepare($delete_checklist_query_12);
    $delete_checklist_statement_12->execute();

    $delete_checklist_query_13 = "DELETE FROM siteNews WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_13 = $db->prepare($delete_checklist_query_13);
    $delete_checklist_statement_13->execute();

    $delete_checklist_query_14 = "DELETE FROM site_technician WHERE siteCode IN (" . $where_in_site_code . ")";
    $delete_checklist_statement_14 = $db->prepare($delete_checklist_query_14);
    $delete_checklist_statement_14->execute();

    $delete_checklist_query_15 = "DELETE FROM site_siteActivityType WHERE siteCode IN (" . $where_in_site_code . ")";
    $delete_checklist_statement_15 = $db->prepare($delete_checklist_query_15);
    $delete_checklist_statement_15->execute();

    $delete_checklist_query_16 = "DELETE FROM site_checklist WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_16 = $db->prepare($delete_checklist_query_16);
    $delete_checklist_statement_16->execute();

    $delete_checklist_query_17 = "DELETE FROM checklist WHERE checklistID IN (" . $where_in_checklist_id . ")";
    $delete_checklist_statement_17 = $db->prepare($delete_checklist_query_17);
    $delete_checklist_statement_17->execute();

    $delete_checklist_query_18 = "DELETE FROM site WHERE siteCode IN (" . $where_in_site_code . ")";
    $delete_checklist_statement_18 = $db->prepare($delete_checklist_query_18);
    $delete_checklist_statement_18->execute();
}
