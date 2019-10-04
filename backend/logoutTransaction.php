<?php
session_start();

// remove all session variables and destroy the session
session_unset();
session_destroy();

header("location: ../index.php");