<?php

require_once ("../database/database.php");
require_once ("functions.php");

try {
    $query_headers = "SELECT id AS categoryID, category AS id, category AS text, false AS expanded, 'icon-page' AS img, NULL AS nodes FROM documentCategories";
    $statement_headers = $db->prepare($query_headers);
    $statement_headers->execute();
    
    $result_headers = $statement_headers->fetchAll(PDO::FETCH_ASSOC);
    
    $query_documents = "SELECT documentID AS id, documentName AS text, documentURL AS url, category, 'icon-page' AS img FROM documents";
    $statement_documents = $db->prepare($query_documents);
    $statement_documents->execute();
    
    $result_documents = $statement_documents->fetchAll(PDO::FETCH_ASSOC);
    
    //print_r($result_headers);
    //print_r($result_documents);
    
    $document_headers_count = count($result_headers);
    $document_count = count($result_documents);
    
    for($i = 0; $i < $document_headers_count; $i++)
    {
        $result_headers[$i]["id"] = str_replace(" ", "_", $result_headers[$i]["id"]);
        
        for($j = 0; $j < $document_count; $j++)
        {
            if($result_headers[$i]["categoryID"] === $result_documents[$j]["category"])
            {
                if($result_headers[$i]["nodes"] === null)
                {
                    $result_headers[$i]["nodes"] = array($result_documents[$j]);
                }
                else
                {
                    array_push($result_headers[$i]["nodes"], $result_documents[$j]);
                } 
            }
        }
    } 
    
    echo json_encode($result_headers);
} catch (Exception $exception) {
    echo $exception->getMessage() . ". Error has occured on line '" . $exception->getLine() . "' in the file '" . $exception->getFile() . "'";
}