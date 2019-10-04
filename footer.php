</main>

<aside class="col-lg-4">
    <div class="widget latest-events">
        <header>
            <h3 class="h6">Events: News</h3>
            <hr>
        </header>
        <div id="siteNews">   
            <h4 class="display-4" style="text-align: center;">There is currently no news!</h4>
        </div>
    </div>
    <div class="widget latest-events">
        <header>
            <h3 class="h6">Events: Documents/Other </h3>
            <hr>
        </header>
        <div id="otherEvents"> 
            <h4 class="display-4" style="text-align: center;" id="no_document_events">There are currently no events for this category!</h4>
        </div>
    </div>  
</aside>

</div>
</div>

<!-- Login Modal -->
<div id="modalLogin" class="modal fade">
    <div class="modal-dialog modal-login">
        <div class="modal-content">
            <div class="modal-header">				
                <h4 class="modal-title">Login</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-group">
                        <i class="glyphicon glyphicon-user"></i>
                        <input type="email" class="form-control" placeholder="Email" name='email' required="required">
                    </div>
                    <div class="form-group">
                        <i class="glyphicon glyphicon-lock"></i>
                        <input type="password" class="form-control" placeholder="Password" name='password' required="required">					
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-lg green-button" id='login'>Login</button>
                    </div> 	
                </form>
            </div>
            <div class="modal-footer">
                <a href="#modalForgotPassword" id="forgotPasswordLink">Forgot Password?</a>
            </div>
        </div>
    </div>
</div> 

<!-- Forgot Password Modal -->
<div id="modalForgotPassword" class="modal fade">
    <div class="modal-dialog modal-login">
        <div class="modal-content">
            <div class="modal-header">				
                <h4 class="modal-title">Forgot Password</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-group">
                        <i class="glyphicon glyphicon-user"></i>
                        <input type="email" class="form-control" placeholder="Email" name='forgotPasswordEmail' required="required">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-lg green-button" id='forgotPassword'>Reset Password</button>
                    </div> 	
                </form>
            </div>
            <div class="modal-footer">
                <a href="#modalLogin" id="backToLogin">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<div id="modal_add_event" class="modal fade">
    <div class="modal-dialog modal-login">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 0px;">				
                <h4 class="modal-title">Create New Event</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bs-callout bs-callout-success"> 
                    <h4>Add Event</h4> 
                    <p id="event_description">Start creating your own event by choosing the event type.</p>
                </div>

                <form style="text-align: center;" id="create_event_form" method="post" onsubmit="event.preventDefault();">
                    <label for="event_type">Event Type</label> 
                    <div class="form-group"> 
                        <i class="glyphicon glyphicon-user"></i>
                        <select name="event_type" class="form-control selectpicker" id="event_type">
                            <option value="default" disabled selected>Event Type</option> 
                            <option value="site">Site Related</option>
                            <option value="upcoming_site">Upcoming Site Build</option>
                            <option value="other">Other</option>
                        </select>
                    </div> 
                    
                    <div id="admin_select">
                        <label for="siteType">Technician</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-user"></i>
                            <select name="admin_select_tech" class="form-control selectpicker" id="admin_select_tech">
                                <option value="default" id="0" disabled selected>Technician</option>
                            </select>				
                        </div>
                    </div>
                    
                    <div class="event_site_code">
                        <label for="siteType">Site Code</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventSiteCode" class="form-control" id="eventSiteCode" placeholder="Site Code" autocomplete="off">					
                        </div>
                    </div>

                    <div class="event_activity_type">
                        <label for="event_type">Activity Type</label> 
                        <div class="form-group"> 
                            <i class="glyphicon glyphicon-user"></i>
                            <select name="eventActivityType" class="form-control selectpicker" id="eventActivityType">
                                <option value="default" disabled selected>Activity Type</option>
                            </select>
                        </div> 

                        <label for="siteType">Site Country</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventSiteCountry" class="form-control" id="eventSiteCountry" placeholder="Site Country" autocomplete="off">					
                        </div>

                        <label for="siteType">Site County</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventSiteCounty" class="form-control" id="eventSiteCounty" placeholder="Site County" autocomplete="off">					
                        </div>

                        <label for="siteType">Site Town</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventSiteTown" class="form-control" id="eventSiteTown" placeholder="Site Town" autocomplete="off">					
                        </div>
                    </div>

                    <div class="event_start_date">
                        <label for="siteType">Event Start Date</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventStartDate" class="form-control" id="eventStartDate" placeholder="Event Start Date" autocomplete="off">					
                        </div>
                    </div>

                    <div class="event_end_date">
                        <label for="siteType">Event End Date</label> 
                        <div class="form-group">
                            <i class="glyphicon glyphicon-lock"></i>
                            <input type="text" name="eventEndDate" class="form-control" id="eventEndDate" placeholder="Event End Date" autocomplete="off">					
                        </div>
                    </div>

                    <div class="event_event">
                        <label for="siteType">Event Description</label> 
                        <div class="form-group"> 
                            <textarea rows="4" cols="34" name="event_event_description" id="event_event_description" style="resize: none;"></textarea>			
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-lg green-button" id="create_event">Create Event</button>
                    </div> 	
                </form>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div> 


<footer id="fh5co-footer" class="fh5co-bg" role="contentinfo">
    <div class="overlay"></div>
    <div class="container">
        <div class="row row-pb-md">
            <div class="col-md-4 fh5co-widget">
                <h3>A Little About This Website.</h3>
                <p>This website has been created to make it easier for YOU, the technician, to manage, build and deploy Prometric test centers all over the world.</p>
                <p id='loginToBegin'><a class="btn btn-primary" href="#"><i class="glyphicon glyphicon-off"></i> Login To Begin</a></p>
            </div>
            <div class="col-md-8"> 
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <h3>Quick Links</h3>
                    <ul class="fh5co-footer-links">
                        <li><a href="index.php"><i class="glyphicon glyphicon-home"></i> Home</a></li>
                        <li><a href="#"><i class="glyphicon glyphicon-calendar"></i> Team Calender</a></li>
                        <li><a href="#"><i class="glyphicon glyphicon-file"></i> Documents</a></li>
                        <!--<li><a href="#"><i class="glyphicon glyphicon-map-marker"></i> Site Map</a></li>-->  
                    </ul>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-6">
                    <h3>Site Management</h3>
                    <ul class="fh5co-footer-links"> 
                        <li class='allSitesLogin'><a href="allSites.php"><i class="glyphicon glyphicon-list-alt"></i> All Sites</a></li>
                    </ul>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-6">
                    <h3>Profile</h3>
                    <ul class="fh5co-footer-links" id='footerLinks'>
                        <li class='login'><a href="#modalLogin" data-toggle="modal"><i class="glyphicon glyphicon-off"></i> Login</a></li>  
                    </ul>
                </div>
            </div>
        </div>

        <div class="row copyright">
            <div class="col-md-12 text-center">
                <p>
                    <small class="block">&copy; 2019 Prometric | All Rights Reserved.</small> 
                </p>
            </div>
        </div>

    </div>
</footer> 
</body>
</html>