<?php
require "../database/database.php";
require "functions.php";

//$email = "Nichita.Postolachi@Prometric.com"; 

$email = ltrim(rtrim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)));
if (empty($email)) {
    echo "Ooops, the email seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!";
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

/* If the user is already in the pending_user table, then remove the old entry */
try {
    $deleteUserQuery = "DELETE FROM pending_users WHERE email = :email";
    $deleteUserStatement = $db->prepare($deleteUserQuery);
    $deleteUserStatement->bindParam(":email", $email, PDO::PARAM_STR);
    $deleteUserStatement->execute();
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}

/* Create new entry in the pending_user table for this email */
$expiry_time_stamp = 1200 + $_SERVER["REQUEST_TIME"]; // 1200 = 20 minutes from now
$token = sha1(uniqid($email, true));
$siteName = "localhost";

//Insert the pending transaction record
try {
    $insertPendingQuery = "INSERT INTO pending_users (token, email, expiry_time_stamp) VALUES (:token, :email, :expiry_time_stamp)";
    $insertPendingstatement = $db->prepare($insertPendingQuery);
    $insertPendingstatement->bindParam(":token", $token, PDO::PARAM_STR);
    $insertPendingstatement->bindParam(":email", $email, PDO::PARAM_STR);
    $insertPendingstatement->bindParam(":expiry_time_stamp", $expiry_time_stamp, PDO::PARAM_INT);
    $insertPendingstatement->execute();

    /* remove all old pending users from database */
    $deleteExpiredQuery = "DELETE FROM pending_users WHERE expiry_time_stamp < :expiry_time_stamp";
    $deleteExpiredStatement = $db->prepare($deleteExpiredQuery);
    $deleteExpiredStatement->bindParam(":expiry_time_stamp", $_SERVER["REQUEST_TIME"], PDO::PARAM_INT);
    $deleteExpiredStatement->execute();
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
} 

$subject = "<NO REPLY> Password Reset Link";
$body = 'You have recieved this email because you have requested a password change. Click the link below to proceed. If you did not request this email please inform your manager <b>AS SOON AS POSSIBLE<b> as this could be a security breach. <br><br> <a href="https://d00192082.alwaysdata.net/SiteTechWebsite/forgot_password_confirm_new_password.php?token=' . $token . '">Click here to continue</a> <br><br> <table border="0" cellpadding="0" cellspacing="0" width="500"> <tbody> <tr> <td border="0" cellpadding="0" cellspacing="0" height="38" width="222"> <img src="https://d00192082.alwaysdata.net/SiteTechWebsite/images/prometric_email_logo.png" alt="Prometric Icon" height="72" width="200"> </td> </tr> <tr> <td height="64" style="font-family:Helvetica, Arial, sans-serif; font-size:18px; font-style:bold;"> <strong>Shane Dollard</strong> <br> <em style="font-size:17px; font-weight:400;">Site Technology Services Manager</em> </td> </tr> <tr> <td height="58" style="font-family:Helvetica, Arial, sans-serif; font-size:16px; color:#4d4d4e;"> Building 3, Finnabair Cres | Dundalk, Co. Louth, Republic of Ireland </td> </tr> <tr> <td class="hover" height="60" style="font-family:Helvetica, Arial, sans-serif; font-size:16px; color:#d0292d;"> <a>Shane.Dollard@Prometric.com</a> <br> <a href="https://d00192082.alwaysdata.net/SiteTechWebsite/index.php/">Site Technology Services Management Website</a> </td> </tr><tr> <td height="70"> <small style="font-family:Helvetica, Arial, sans-serif; font-size:10px; color:#4d4d4e;">Confidentiality Notice: This e-mail message, including any attachments, is for the sole use of the intended recipient(s) and may contain confidential and privileged information. Any unauthorized review, use, disclosure or distribution of this information is prohibited, and may be punishable by law. If this was sent to you in error, please notify the sender by reply e-mail and destroy all copies of the original message. Please consider the environment before printing this e-mail.</small> </td> </tr> </tbody> </table>';
$successResponse = "Please check your email for a link to reset your password and follow the instructions.";

$response = sendEmail($email, $subject, $body, $successResponse);

if($response == $successResponse)
{
    echo $response;
}
else if($response == "The email has failed to send, please wait a bit and try again.")
{
    echo $response;
}
else
{
    echo "An unspecified error has occured.";
} 