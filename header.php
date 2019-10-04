<?php

function redirectTohttps() {

    if ($_SERVER['HTTPS'] != 'on') {

        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        header('Location:' . $redirect);
    }
}

redirectTohttps();
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <script>
            var path = window.location.pathname;
            var page = path.split("/").pop();

            if (page === "newActivity.php" || page === "mySites.php" || page === "administratorConsole.php")
            {
                if (page === "administratorConsole.php" && sessionStorage.getItem("accessLevel") !== '2')
                {
                    window.location.href = "index.php";
                }

                if (sessionStorage.getItem("userName") === null)
                {
                    window.location.href = "index.php";
                }
            }
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, intial-scale=1">
        <title>Global Site Technology Services Management Website</title>
        <link rel="icon" href="images/prometric_logo.png"> 

        <!--CSS Libraries-->
        <link href="css/libraries/bootstrap.min.css?random=<?php echo uniqid(); ?>" rel="stylesheet" type="text/css"/> 
        <link href="css/libraries/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>

        <!--Main CSS-->
        <link href="css/main.css?random=<?php echo uniqid(); ?>" rel="stylesheet" type="text/css"/>

        <!--JavaScript Libraries-->
        <script src="js/libraries/jquery-3.4.0.min.js" type="text/javascript"></script> 
        <script src="js/libraries/bootstrap.min.js" type="text/javascript"></script>  
        <script src="js/libraries/sweetalert.min.js"></script>
        <script src="js/libraries/moment.min.js" type="text/javascript"></script>
        <script src="js/libraries/promise.min.js" type="text/javascript"></script>

        <!--Main JavaScript-->
        <script src="js/main.js?random=<?php echo uniqid(); ?>" type="text/javascript"></script>
    <body>  
        <header>
            <nav class="navbar navbar-default menu"> 
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar3">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img class="logo" src="images/prometric_logo.png" alt="Prometric Logo">
                    </a>
                </div>
                <div id="navbar3" class="navbar-collapse collapse navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="index.php"><i class="glyphicon glyphicon-home"></i> Home</a></li>
                        <li><a href="teamCalender.php"><i class="glyphicon glyphicon-calendar"></i> Team Calender</a></li>
                        <li><a href="documents.php"><i class="glyphicon glyphicon-file"></i> Documents</a></li>
                        <!--<li><a href="#"><i class="glyphicon glyphicon-map-marker"></i> Site Map</a></li>-->

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="glyphicon glyphicon-print"></i> Site Management <span class="caret"></span></a>
                            <ul class="dropdown-menu" id="site_management" role="menu"> 
                                <li class='allSitesLogin'><a href="allSites.php"><i class="glyphicon glyphicon-list-alt"></i> All Sites</a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="nav-link dropdown-toggle profile-image" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="https://d00192082.alwaysdata.net/SiteTechWebsiteServer/avatar/base.png" width="40" height="40" class="rounded-circle" id='avatar'> Profile <span class="caret"></span></a>  
                            <ul class="dropdown-menu" id="profile" role="menu">
                                <li class='login'><a href="#modalLogin" data-toggle="modal"><i class="glyphicon glyphicon-off"></i> Login</a></li>               
                            </ul> 
                        </li> 

                        <!--                        <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="#">Action</a></li>
                                                        <li><a href="#">Another action</a></li>
                                                        <li><a href="#">Something else here</a></li>
                                                        <li class="divider"></li>
                                                        <li class="dropdown-header">Nav header</li>
                                                        <li><a href="#">Separated link</a></li>
                                                        <li><a href="#">One more separated link</a></li>
                                                    </ul>
                                                </li>-->
                    </ul>
                </div> 
            </nav>
        </header>
        <div class="container-fluid">
            <div class="row">
                <aside class="col-lg-4">
                    <div class="widget latest-events">
                        <header>
                            <h3 class="h6">Activities: Ongoing</h3>
                            <hr>
                        </header>
                        <div id="liveEvents"> 
                            <h4 class="display-4" style="text-align: center;">There are currently no live activities!</h4>
                        </div>
                    </div>
                    <div class="widget latest-events">
                        <header>
                            <h3 class="h6">Activities: Upcoming </h3>
                            <hr>
                        </header>
                        <div id="upcomingEvents"> 
                            <h4 class="display-4" style="text-align: center;">There are currently no upcoming activities!</h4></h4>
                        </div>  
                </aside>
                <main class="col-lg-6">