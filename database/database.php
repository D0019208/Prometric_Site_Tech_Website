<?php

$dsn = "mysql:host=mysql-d00192082.alwaysdata.net;dbname=d00192082_sitetechwebsite";
$username = "d00192082";
$password = "3820065Np2";
//$siteName = "http://localhost/SiteTechWebsite";
$siteName = "d00192082.alwaysdata.net/";
$ftp_host = 'ftp-d00192082.alwaysdata.net'; /* host */
$ftp_user_name = 'd00192082_connect'; /* username */
$ftp_user_pass = '3820065Np2'; /* password */

try {
    $db = new PDO($dsn, $username, $password);
    //set up error reporting on server
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_reporting(E_ALL);
} catch (PDOException $exception) {
    //Error message
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
    exit();
}