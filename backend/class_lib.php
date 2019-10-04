<?php

abstract class Activity {

    protected $siteCode = "";
    protected $siteName = "";
    protected $siteCountry = "";
    protected $siteCounty = "";
    protected $siteTown = "";
    protected $siteType = "";
    protected $siteRegion = "";
    protected $activityType = "";
    protected $technician = "";
    protected $overNightSupport = "";
    protected $expectedGoLiveDate = "";

    function __construct($siteCode, $siteName, $siteCountry, $siteCounty, $siteTown, $siteType, $siteRegion, $activityType, $technician, $overNightSupport, $expectedGoLiveDate) {
        $this->siteCode = $siteCode;
        $this->siteName = $siteName;
        $this->siteCountry = $siteCountry;
        $this->siteCounty = $siteCounty;
        $this->siteTown = $siteTown;
        $this->siteType = $siteType;
        $this->siteRegion = $siteRegion;
        $this->activityType = $activityType;
        $this->technician = $technician;
        $this->overNightSupport = $overNightSupport;
        $this->expectedGoLiveDate = $expectedGoLiveDate;
    }

//========================================================================\\
//==============================GETTERS===================================\\
//========================================================================\\
    public function getSiteCode() {
        return $this->siteCode;
    }

    public function getSiteName() {
        return $this->siteName;
    }

    public function getSiteCountry() {
        return $this->siteCountry;
    }

    public function getSiteCounty() {
        return $this->siteCounty;
    }

    public function getSiteTown() {
        return $this->siteTown;
    }

    public function getSiteType() {
        return $this->siteType;
    }

    public function getSiteRegion() {
        return $this->siteRegion;
    }

    public function getActivityType() {
        return $this->activityType;
    }

    public function getTechnician() {
        return $this->technician;
    }

    public function getOverNightSupport() {
        return $this->overNightSupport;
    }

    public function getExpectedGoLiveDate() {
        return $this->expectedGoLiveDate;
    }

//========================================================================\\
//==============================SETTERS===================================\\
//========================================================================\\
    public function setSiteCode($siteCode) {
        $this->siteCode = $siteCode;
    }

    public function setSiteName($siteName) {
        $this->siteName = $siteName;
    }

    public function setSiteCountry($siteCountry) {
        $this->siteCountry = $siteCountry;
    }

    public function setSiteCounty($siteCounty) {
        $this->siteCounty = $siteCounty;
    }

    public function setSiteTown($siteTown) {
        $this->siteTown = $siteTown;
    }

    public function setSiteType($siteType) {
        $this->siteType = $siteType;
    }

    public function setSiteRegion($siteRegion) {
        $this->siteRegion = $siteRegion;
    }

    public function setActivityType($activityType) {
        $this->activityType = $activityType;
    }

    public function setTechnician($technician) {
        $this->technician = $technician;
    }

    public function setOverNightSupport($overNightSupport) {
        $this->overNightSupport = $overNightSupport;
    }

    public function setExpectedGoLiveDate($expectedGoLiveDate) {
        $this->expectedGoLiveDate = $expectedGoLiveDate;
    }

}

class Site extends Activity {

//========================================================================\\
//==============================METHODS===================================\\
//========================================================================\\
    public function createSite($db, $genericInsertValuesArray, $insertCategories, $ActivitySite) {
//Class variables
        $siteCode = $ActivitySite->getSiteCode();
        $technician = $ActivitySite->getTechnician();
        $activityType = $ActivitySite->getActivityType();

        $numberOfColumns = count($insertCategories);
        $genericCountInsert = count($genericInsertValuesArray[0]);
        $insertColumns = implode(', ', $insertCategories);

        $preparedStatement = Site::buildInsertStatement($genericInsertValuesArray, $genericCountInsert, $numberOfColumns, $insertCategories);

        //try {
//Insert
            $query = "INSERT INTO site (" . $insertColumns . ") VALUES "; //Prequery
            $qPart = array_fill(0, 1, $preparedStatement);
            $query .= implode(",", $qPart);

            $stmt = $db->prepare($query);
            $i = 1;

            foreach ($genericInsertValuesArray as $item) { //bind the values one by one  
                for ($j = 0; $j < $genericCountInsert; $j++) {
                    $stmt->bindValue($i++, $item[array_keys($item)[$j]]);
                }
            }

            $stmt->execute(); //execute

            $query = "INSERT INTO site_technician (siteCode, technician) VALUES (:siteCode, :technician)";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $statement->bindParam(":technician", $technician, PDO::PARAM_STR);
            $statement->execute();

            $query = "INSERT INTO site_siteActivityType (siteCode, activityType) VALUES (:siteCode, :activityType)";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $statement->bindParam(":activityType", $activityType, PDO::PARAM_STR);
            $statement->execute();

            return "Success";
        //} catch (PDOException $exception) {
        //    return $exception->getMessage();
        //}
    }

    protected function buildInsertStatement($genericInsertValuesArray, $genericCountInsert, &$insertCategoriesCount, &$insertCategories) {
//Our goal is to make a prepared statement placeholder, start here (?, ?, ?)
        $preparedStatement = "(";

//Continue creating prepared statement string
        for ($i = 0; $i < $insertCategoriesCount; $i++) {
            $preparedStatement .= "?";

            if ($insertCategoriesCount > 1 && $i !== $insertCategoriesCount - 1) {
                $preparedStatement .= ",";
            }
        }

        return $preparedStatement .= ")";
    }

}

class Checklist extends Activity {

    protected $checklistHeader = "";
    protected $optionalFields = "";
    protected $checklistID = "";
    protected $status = 1;
    protected $allDayEvent = true;

    function __construct($siteCode, $siteName, $siteCountry, $siteCounty, $siteTown, $siteType, $siteRegion, $activityType, $technician, $overNightSupport, $expectedGoLiveDate, $checklistHeader, $optionalFields) {
        Activity::__construct($siteCode, $siteName, $siteCountry, $siteCounty, $siteTown, $siteType, $siteRegion, $activityType, $technician, $overNightSupport, $expectedGoLiveDate);
        $this->optionalFields = $optionalFields;
        $this->checklistHeader = $checklistHeader; 
    }

//========================================================================\\
//==============================GETTERS===================================\\
//========================================================================\\
    public function getChecklistHeader() {
        return $this->checklistHeader;
    }

    public function getOptionalFields() {
        return $this->optionalFields;
    }

    public function getChecklistID() {
        return $this->checklistID;
    }

//========================================================================\\
//==============================SETTERS===================================\\
//========================================================================\\
    public function SetChecklistHeader($checklistHeader) {
        return $this->checklistHeader = $checklistHeader;
    }

    public function setOptionalFields($optionalFields) {
        $this->optionalFields = $optionalFields;
    }

    public function setChecklistID($checklistID) {
        return $this->checklistID = $checklistID;
    }

//========================================================================\\
//==============================METHODS===================================\\
//========================================================================\\
    public function createChecklist($db, &$ActivityChecklist, $genericInsertValuesArray, $insertCategories, $optionalFieldSelected, $overNightSupport) {
        //Class variables
        //THIS IS NOT OVERNIGHT SUPPORT, ADD OVERNIGHT SUPPORT TECH 
        $technician = $ActivityChecklist->getTechnician();
        $activityType = $ActivityChecklist->getActivityType();
        $siteCode = $ActivityChecklist->getSiteCode();
        $checklistID;
        
        $overnightSupportCount = count($overNightSupport);

        $numberOfColumns = count($insertCategories);
        $genericCountInsert = count($genericInsertValuesArray[0]);
        $insertColumns = implode(', ', $insertCategories);

        $preparedStatement = Checklist::buildInsertStatement($genericInsertValuesArray, $genericCountInsert, $numberOfColumns, $insertCategories);

        try {
            //Insert
            $query = "INSERT INTO checklist (" . $insertColumns . ") VALUES "; //Prequery
            $qPart = array_fill(0, 1, $preparedStatement);
            $query .= implode(",", $qPart);

            $stmt = $db->prepare($query);
            $i = 1;

            foreach ($genericInsertValuesArray as $item) { //bind the values one by one  
                for ($j = 0; $j < $genericCountInsert; $j++) {
                    $stmt->bindValue($i++, $item[array_keys($item)[$j]]);
                }
            }

            $stmt->execute(); //execute
            //AND expectedGoLiveDate = :date
            //$date = date("Y/M/d h:i:s");
            $query = "SELECT checklistID FROM checklist WHERE siteCode = :siteCode AND activityType = :activityType";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $statement->bindParam(":activityType", $activityType, PDO::PARAM_STR);
            //$statement->bindParam(":date", $date, PDO::PARAM_STR);
            $statement->execute();

            $checklistIDResult = $statement->fetchAll(PDO::FETCH_ASSOC);
            $checklistID = $checklistIDResult[0]["checklistID"];

            $ActivityChecklist->setChecklistID($checklistID); 
            
//            $query = "INSERT INTO checklist_technician (checklistID, siteCode) VALUES (:checklistID, :siteCode)";
//            $statement = $db->prepare($query);
//            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
//            $statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
//            $statement->execute();
            
            $query = "INSERT INTO checklist_technician (checklistID, technician) VALUES (:checklistID, :technician)";
            $statement = $db->prepare($query);
            $statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR);
            $statement->bindParam(":technician", $technician, PDO::PARAM_STR);
            $statement->execute();
            
            $query = "INSERT INTO site_checklist (siteCode, checklistID) VALUES (:siteCode, :checklistID)";
            $statement = $db->prepare($query);
            $statement->bindParam(":siteCode", $siteCode, PDO::PARAM_STR);
            $statement->bindParam(":checklistID", $checklistID, PDO::PARAM_STR); 
            $statement->execute();            
            
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }

        //Insert MANDATORY checklistCategories into checklist_checklistCategories table
        $selectID1 = ["categoryName", "categories_identifier"];
        $insertValues1 = ["checklistID" => $checklistID, "categoryStatus" => "1"];
        $response1 = insertRecords($db, $selectID1, "checklistCategories", $insertValues1, "checklist_checklistCategories");

        if ($response1 !== "Success") {
            echo "An error has occured: " . $response1;
            exit();
        }

        //Insert MANDATORY checklistTasks into siteContact table 
        $typeArrays = array("TCA", "Main Tech", "Overnight Support");
        $insertValues = "";
        try {

            for ($i = 0; $i < count($typeArrays); $i++) {
                if ($typeArrays[$i] === "TCA") {
                    $insertValues .= "('" . $checklistID . "', '" . $typeArrays[$i] . "', '', '', 'false')";
                } else if ($typeArrays[$i] === "Main Tech") {
                    $insertValues .= "('" . $checklistID . "', '" . $typeArrays[$i] . "', '" . $technician . "', '', 'false')";
                } else {
                    for ($j = 0; $j < $overnightSupportCount; $j++) {
                        $insertValues .= "('" . $checklistID . "', '" . $typeArrays[$i] . "', '" . $overNightSupport[$j] . "', '', 'false')";
                        if ($j !== $overnightSupportCount - 1) {
                            $insertValues .= ", ";
                        }
                    }
                }

                if ($i !== count($typeArrays) - 1) {
                    $insertValues .= ", ";
                }
            }

            $siteContactquery = "INSERT INTO siteContact (checklistID, type, name, phoneNumber, taskCompleted) VALUES " . $insertValues;
            $siteContactStatement = $db->prepare($siteContactquery);
            $siteContactStatement->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }

        //Insert MANDATORY checklistTasks into checklist_checklistTasks table
        $selectID2 = ["taskName", "tabName", "categoryName"];
        $insertValues2 = ["checklistID" => $checklistID, "taskCompleted" => "false"];
        $response2 = insertRecords($db, $selectID2, "checklistTasks", $insertValues2, "checklist_checklistTasks");

        if ($response2 !== "Success") {
            echo "An error has occured: " . $response2;
            exit();
        }

        //Insert MANDATORY checklistTabs into checklist_checklistTabs
        $selectID3 = ["tabName", "tabs_identifier"];
        $insertValues3 = ["checklistID" => $checklistID, "tabStatus" => "1"];
        $response3 = insertRecords($db, $selectID3, "checklistTabs", $insertValues3, "checklist_checklistTabs");

        if ($response3 !== "Success") {
            echo "An error has occured: " . $response3;
            exit();
        }

        //Insert the MANDATORY checklistActivityType into checklist_checklistActivityType
        $selectID4 = ["activityType"];
        $insertValues4 = ["checklistID" => $checklistID];
        $response4 = insertRecords($db, $selectID4, "siteActivityType", $insertValues4, "checklist_siteActivityType");

        if ($response4 !== "Success") {
            echo "An error has occured: " . $response4;
            exit();
        }

        //Insert the MANDATORY Overnight Support Technician INTO overnightSupport
        try {
            $OVNSQuery = "INSERT INTO overnightSupport (checklistID, siteCode, technician) VALUES ";

            for ($i = 0; $i < $overnightSupportCount; $i++) {
                $OVNSQuery .= "('" . $checklistID . "', '" . $siteCode . "', '" . $overNightSupport[$i] . "')";
                //CHANGED HERE ON 22:02 14/06/2019
                if ($i !== $overnightSupportCount - 1 && $overnightSupportCount > 1) {
                    $OVNSQuery .= ",";
                }
            }

            $OVNSStatement = $db->prepare($OVNSQuery);
            $OVNSStatement->execute();
            
            $overNightSupportIn = "";
            for($i = 0; $i < $overnightSupportCount; $i++)
            {
                $overNightSupportIn .= "'" . $overNightSupport[$i] . "', ";
            }
            
            //Update the number of sites in progress the technician has 
            $activitiesInProgressQuery = "UPDATE technician SET activitiesInProgress = activitiesInProgress + 1 WHERE technicianFullName IN(" . $overNightSupportIn . "'" . $technician . "'" . ")";
            $activitiesInProgressStatement = $db->prepare($activitiesInProgressQuery);
            $activitiesInProgressStatement->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }

        //If the user has chosen to add optional fields, add them here.
        if ($optionalFieldSelected == true) {
            //Insert OPTIONAL categories into checklist_checklistOptionalCategories table
            $selectID5 = ["optionalCategoryName", "optional_categories_identifier"];
            $insertValues5 = ["checklistID" => $checklistID, "optionalCategoryStatus" => "1"];
            $response5 = insertRecords($db, $selectID5, "checklistOptionalCategories", $insertValues5, "checklist_checklistOptionalCategories", $ActivityChecklist);

            if ($response5 !== "Success") {
                echo "An error has occured: " . $response5;
                exit();
            }

            //Insert OPTIONAL tasks into checklist_checklistOptionalTasks table
            $selectID6 = ["optionalTaskName", "optionalTabName", "optionalCategoryName"];
            $insertValues6 = ["checklistID" => $checklistID, "taskCompleted" => "false"];
            $response6 = insertRecords($db, $selectID6, "checklistOptionalTasks", $insertValues6, "checklist_checklistOptionalTasks", $ActivityChecklist);

            if ($response6 !== "Success") {
                echo "An error has occured: " . $response6;
                exit();
            }

            //Insert OPTIONAL tabs into checklist_checklistOptionalTabs
            $selectID7 = ["optionalTabName", "optional_tabs_identifier"];
            $insertValues7 = ["checklistID" => $checklistID, "optionalTabStatus" => "1"];
            $response7 = insertRecords($db, $selectID7, "checklistOptionalTabs", $insertValues7, "checklist_checklistOptionalTabs", $ActivityChecklist);

            if ($response7 !== "Success") {
                echo "An error has occured: " . $response7;
                exit();
            }

            return "Success";
        } else {
            return "Success";
        }
    }

    protected function buildInsertStatement($genericInsertValuesArray, $genericCountInsert, &$insertCategoriesCount, &$insertCategories) {
        //Our goal is to make a prepared statement placeholder, start here (?, ?, ?)
        $preparedStatement = "(";

        //Continue creating prepared statement string
        for ($i = 0; $i < $insertCategoriesCount; $i++) {
            $preparedStatement .= "?";

            if ($insertCategoriesCount > 1 && $i !== $insertCategoriesCount - 1) {
                $preparedStatement .= ",";
            }
        }

        return $preparedStatement .= ")";
    }

}

class Technician {

    protected $email = "";
    protected $name = "";
    protected $avatar = "";
    protected $experience = "";
    protected $workingSince = "";
    protected $activitiesComplete = "";
    protected $activitiesInProgress = "";
    protected $documentsUpdated = "";
    protected $documentsCreated = "";
    protected $accessLevel = "";

    function __construct($email, $name, $avatar, $experience, $workingSince, $activitiesComplete, $activitiesInProgress, $documentsUpdated, $documentsCreated, $accessLevel) {
        $this->email = $email;
        $this->name = $name;
        $this->avatar = $avatar;
        $this->experience = $experience;
        $this->workingSince = $workingSince;
        $this->activitiesComplete = $activitiesComplete;
        $this->activitiesInProgress = $activitiesInProgress;
        $this->documentsUpdated = $documentsUpdated;
        $this->documentsCreated = $documentsCreated;
        $this->accessLevel = $accessLevel;
    }

    //========================================================================\\
    //==============================GETTERS===================================\\
    //========================================================================\\
    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function getExperience() {
        return $this->experience;
    }

    public function getWorkingSince() {
        return $this->workingSince;
    }

    public function getActivitiesComplete() {
        return $this->activitiesComplete;
    }

    public function getActivitiesInProgress() {
        return $this->activitiesInProgress;
    }

    public function getDocumentsUpdated() {
        return $this->documentsUpdated;
    }

    public function getDocumentsCreated() {
        return $this->documentsCreated;
    }

    public function getAccessLevel() {
        return $this->accessLevel;
    }

    //========================================================================\\
    //==============================SETTERS===================================\\
    //========================================================================\\
    public function setEmail($email) {
        return $this->email = $email;
    }

    public function setName($name) {
        $this->siteCode = $name;
    }

    public function setAvatar($avatar) {
        return $this->avatar = $avatar;
    }

    public function setExperience($experience) {
        $this->experience = $experience;
    }

    public function setWorkingSince($workingSince) {
        $this->workingSince = $workingSince;
    }

    public function setActivitiesComplete($activitiesComplete) {
        $this->activitiesComplete = $activitiesComplete;
    }

    public function setActivitiesInProgress($activitiesInProgress) {
        $this->activitiesInProgress = $activitiesInProgress;
    }

    public function setDocumentsUpdated($documentsUpdated) {
        $this->documentsUpdated = $documentsUpdated;
    }

    public function setDocumentsCreated($documentsCreated) {
        $this->documentsCreated = $documentsCreated;
    }

    public function setAccessLevel($accessLevel) {
        $this->accessLevel = $accessLevel;
    }

}
