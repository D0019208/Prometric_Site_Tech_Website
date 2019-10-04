<?php
include_once 'header.php';
?> 
<!--Main Javascript-->
<script src="js/forgot_password.js" type="text/javascript"></script>

<div class="container-fluid">
    <div class="bs-callout bs-callout-success"> 
        <h4>New Password</h4> 
        <p>Enter your <b>email</b>, <b>new password</b> and <b>confirm the new password</b> to create your new password. We recommend that your password is <b>8 characters long</b>, <b>contains one capital letter</b>, <b>one symbol</b> and a <b>number</b>. This is done to ensure that nobody can guess your password or brute force it and get unauthorized access.</p>
    </div>

    <div class="modal-dialog modal-login">
        <div class="modal-content">
            <div class="modal-header">				
                <h4 class="modal-title">New Password</h4> 
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="token" id="token">
                    <div class="form-group">
                        <i class="glyphicon glyphicon-user"></i>
                        <input type="email" class="form-control" placeholder="Email" name='email' required="required">
                    </div>
                    <div class="form-group">
                        <i class="glyphicon glyphicon-lock"></i>
                        <input type="password" class="form-control" placeholder="New Password" name='forgotPasswordNewPassword' required="required">
                    </div>
                    <div class="form-group">
                        <i class="glyphicon glyphicon-lock"></i>
                        <input type="password" class="form-control" placeholder="Confirm New Password" name='forgotPasswordConfirmNewPassword' required="required">
                    </div>
                    <div class="form-group">
                        <button type="newPassword" class="btn btn-primary btn-block btn-lg" id='newPassword'>Create New Password</button>
                    </div> 	
                </form>
            </div> 
        </div>
    </div>

</div> 
<?php
include_once 'footer.php';
?> 