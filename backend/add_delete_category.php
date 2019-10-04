<?php

require_once ("../database/database.php");
require_once ("functions.php");

$access_code = ltrim(rtrim(filter_input(INPUT_POST, "access_code", FILTER_SANITIZE_STRING))); 
$directory = ltrim(rtrim(filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)));

$connection = ftp_connect($ftp_host);

/* Login to FTP */
$login_result = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
if ($login_result) {
    ftp_pasv($connection, true);

    if ($access_code === "add_category") {
        
        
        if (@ftp_mkdir($connection, "SiteTechWebsiteServer/documents/" . $directory)) {
            ftp_close($connection);
            try {
                $query = "INSERT INTO documentCategories (category) VALUES(:category)";
                $statement = $db->prepare($query);
                $statement->bindParam(":category", $directory, PDO::PARAM_STR);
                $statement->execute();

                if ($statement) {
                    echo "1";
                }
            } catch (Exception $exception) {
                echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
                exit();
            }
        } else {
            echo "Could not create the directory '$directory'";
            exit();
        }
    } else if ($access_code === "delete_category") {
        $category_id = ltrim(rtrim(filter_input(INPUT_POST, "category_id", FILTER_SANITIZE_NUMBER_INT)));
        
        if (@ftp_rmdir($connection, "SiteTechWebsiteServer/documents/" . $directory)) {
            ftp_close($connection);
            try {
                $query = "DELETE FROM documentCategories WHERE id = :category_id";
                $statement = $db->prepare($query);
                $statement->bindParam(":category_id", $category_id, PDO::PARAM_INT);
                $statement->execute();

                if ($statement) {
                    echo "1";
                }
            } catch (Exception $exception) {
                echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
                exit();
            }
        } else {
            echo "Could not delete the directory '$directory'! Please make sure all files are deleted first and try again.";
        }
    }
} 