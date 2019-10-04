<?php

require_once ("../../database/database.php");
require_once ("../functions.php"); 

$pdf = $_FILES['pdf']['tmp_name']; 
$pdf_name_short = ltrim(rtrim(filter_input(INPUT_POST, "pdf_name_short", FILTER_SANITIZE_STRING)));
$category_id = ltrim(rtrim(filter_input(INPUT_POST, "categoryID", FILTER_SANITIZE_STRING)));
$directory = ltrim(rtrim(filter_input(INPUT_POST, "directory", FILTER_SANITIZE_STRING))); 
$technician = ltrim(rtrim(filter_input(INPUT_POST, "technician", FILTER_SANITIZE_STRING))); 

if (!empty($pdf)) {
    $filepath = "../../images/temp/" . $_FILES["pdf"]["name"];
    $remote_file = "SiteTechWebsiteServer/documents/" . $directory . "/" . $_FILES["pdf"]["name"]; 
    
    move_uploaded_file($pdf, $filepath);
    //^^^^^^^^^^^^^^^^^^^^SANITIZE THIS^^^^^^^^^^^^^^^^^^^^    

    $connection = ftp_connect($ftp_host);

    /* Login to FTP */
    $login_result = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
    if ($login_result) {
        ftp_pasv($connection, true);
        /* Send $local_file to FTP */ 
        ftp_put($connection, $remote_file, $filepath, FTP_BINARY);
        unlink($filepath);
    }
    /* Close the connection */
    ftp_close($connection);

    try {
        $update_technician_query = "UPDATE technician SET documentsCreated = documentsCreated + 1 WHERE technicianFullName = :technician";
        $update_technician_statement = $db->prepare($update_technician_query);
        $update_technician_statement->bindParam(":technician", $technician, PDO::PARAM_STR);
        $update_technician_statement->execute();
        
        $url = $siteName . $remote_file;
        
        $insert_document_query = "INSERT INTO documents (createdBy, documentName, documentURL, category) VALUES((SELECT technicianID AS 'createdBy' FROM technician WHERE technicianFullName = :technicianFullName), :pdf_name, :url, :category)";
        $insert_document_statement = $db->prepare($insert_document_query); 
        $insert_document_statement->bindParam(":technicianFullName", $technician, PDO::PARAM_STR);
        $insert_document_statement->bindParam(":pdf_name", $pdf_name_short, PDO::PARAM_STR);
        $insert_document_statement->bindParam(":url", $url, PDO::PARAM_STR);
        $insert_document_statement->bindParam(":category", $category_id, PDO::PARAM_INT);

        $insert_document_statement->execute();

        if ($insert_document_statement == true) {  
            $event = $technician . " has uploaded a new document, " . $pdf_name_short . ".";
            
            $date = date("Y-m-d");
            $time = date("H:i:s");

            $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/DocumentIcons/DocumentAdded.png', 'https://$url', 0, 'documents')";
            $statement = $db->prepare($query);
            $statement->execute(); 

            $query_created_count = "SELECT documentsCreated FROM technician WHERE technicianFullName = :technician";
            $statement_created_count = $db->prepare($query_created_count);
            $statement_created_count->bindParam(":technician", $technician, PDO::PARAM_STR);
            $statement_created_count->execute();

            $result_created_count = $statement_created_count->fetchAll(PDO::FETCH_ASSOC);

            if ($result_created_count[0]["documentsCreated"] % 10 == 0) {
                $event = $technician . " has created " . $result_created_count[0]["documentsCreated"] . " documents! Congratulations " . explode(' ', $technician)[0] . "!";
                $date = date("Y-m-d");
                $time = date("H:i:s");

                $query = "INSERT INTO documentEvents (event, technician, date, time, image, link, allDayEvent, event_type) VALUES ('" . $event . "', '" . $technician . "', '" . $date . "', '" . $time . "', 'images/SiteIcons/ModalIcons/ActivityType.png', '#', 0, 'other')";
                $statement = $db->prepare($query);
                $statement->execute();
            }
            
            echo "1";
        }
    } catch (Exception $exception) {
        echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
        exit();
    }
}