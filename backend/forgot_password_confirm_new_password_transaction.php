<?php

require "../database/database.php";
require "functions.php";

$token = ltrim(rtrim(filter_input(INPUT_POST, "token", FILTER_SANITIZE_STRING)));
if (empty($token)) {
    echo "Ooops, the token seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$email = ltrim(rtrim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
if ((empty($email)) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
    echo "Ooops, the email seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$userPassword = ltrim(rtrim(filter_input(INPUT_POST, "newPassword", FILTER_SANITIZE_STRING)));
if (empty($userPassword)) {
    echo "Ooops, the new password seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}

$confirmPassword = ltrim(rtrim(filter_input(INPUT_POST, "confirmNewPassword", FILTER_SANITIZE_STRING)));
if (empty($confirmPassword)) {
    echo "Ooops, the new password confirmation seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
    exit();
}


/* Validate input data */
if ($userPassword != $confirmPassword) {
    echo 'The password and confirm password fields do not match. Please try again.';
    exit();
}

//Select email to check if exists
try {
    $userExistsQuery = "SELECT email FROM technician WHERE email = :email";
    $userExistsStatement = $db->prepare($userExistsQuery);
    $userExistsStatement->bindParam(":email", $email, PDO::PARAM_STR);
    $userExistsStatement->execute();
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}

//Check if email exists
if ($userExistsStatement->rowCount() == 0) {
    echo "No user with the email '" . $email . "' exists, please check to see if you might have made a typo and try again.";
    exit();
}

/* Check that the user is in the pending users database */
try {
    $pendingUserExistsQuery = "SELECT token, email, expiry_time_stamp FROM pending_users WHERE token = :token AND email = :email AND expiry_time_stamp > :expiry_time_stamp";
    $pendingUserExistsStatement = $db->prepare($pendingUserExistsQuery);
    $pendingUserExistsStatement->bindParam(":token", $token, PDO::PARAM_STR);
    $pendingUserExistsStatement->bindParam(":email", $email, PDO::PARAM_STR);
    $pendingUserExistsStatement->bindParam(":expiry_time_stamp", $_SERVER["REQUEST_TIME"], PDO::PARAM_INT);
    $pendingUserExistsStatement->execute();
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}

if ($pendingUserExistsStatement->rowCount() == 0) {
    /* remove this record from database */
    $deleteExpiredPendingUserQuery = "DELETE FROM pending_users WHERE email = :email";
    $deletePendingUserStatement = $db->prepare($deleteExpiredPendingUserQuery);
    $deletePendingUserStatement->bindParam(":email", $email, PDO::PARAM_STR);
    $deletePendingUserStatement->execute();

    echo "The token you have has timed out, please start from the beginning.";
    exit();
}

/* remove this record and all old pending users from database */
try {
    $deletePendingUserQuery = "DELETE FROM pending_users WHERE email = :email";
    $deletePendingUserStatement = $db->prepare($deletePendingUserQuery);
    $deletePendingUserStatement->bindParam(":email", $email, PDO::PARAM_STR);
    $deletePendingUserStatement->execute();

    /* remove all old pending users from database */
    $deleteExpiredPendingUserQuery2 = "DELETE FROM pending_users WHERE expiry_time_stamp < :expiry_time_stamp";
    $deletePendingUserStatement2 = $db->prepare($deleteExpiredPendingUserQuery2);
    $deletePendingUserStatement2->bindParam(":expiry_time_stamp", $_SERVER["REQUEST_TIME"], PDO::PARAM_INT);
    $deletePendingUserStatement2->execute();


    /* change the user's password */
    $query = "UPDATE technician SET technician.password = :password WHERE email = :email";
    $statement = $db->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->bindParam(":password", $userPassword, PDO::PARAM_STR);
    $statement->execute();
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
} 

if ($statement->rowCount() > 0) {
    echo "Password was reset successfully";
    exit();
} else {
    echo "There has been an error updating your password, please start from the beginning.";
}
?>        