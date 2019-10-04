$(document).ready(function () {
    document.getElementById('token').value = getURLValue('token');

    $("#newPassword").click(function () {
        let token = $("input[name=token]").val();
        let email = $("input[name=email]").val();
        let newPassword = $("input[name=forgotPasswordNewPassword]").val();
        let confirmNewPassword = $("input[name=forgotPasswordConfirmNewPassword]").val();

        $.ajax({
            type: 'POST',
            url: "backend/forgot_password_confirm_new_password_transaction.php",
            dataType: 'text',
            data: {token: token, email: email, newPassword: newPassword, confirmNewPassword: confirmNewPassword},
            beforeSend: function () {
                $("#newPassword").html('Loading... <div id="loading"></div>');
                $("#newPassword").prop('disabled', true);
            },
            success: function (data) {
                $("#newPassword").html('Create New Password');
                $("#newPassword").prop('disabled', false);

                if (data === "The token you have has timed out, please start from the beginning." || data === "There has been an error updating your password, please start from the beginning.")
                {
                    Swal.fire({
                        type: 'error',
                        title: 'New Password Creation Failed',
                        text: data,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Okay'
                    }).then(function (result) {
                        if (result.value) {
                            window.location.href = "index.php"
                        }
                    });
                }

                if (data === "Ooops, the token seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!" || data === "Ooops, the email seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!" || data === "Ooops, the new password seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!" || data === "Ooops, the new password confirmation seems to have either got lost in transaction, the client side validation broke or you forgot to put it in!" || data === "The password and confirm password fields do not match. Please try again.")
                {
                    Swal.fire({
                        type: 'error',
                        title: 'New Password Creation Failed',
                        text: data,
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Try Again'
                    });
                } else
                {
                    Swal.fire({
                        type: 'success',
                        title: 'New Password Created Successfully',
                        text: 'Password was reset successfully! Click "Okay" to go to the main page and login.',
                        showCancelButton: false,
                        allowOutsideClick: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Okay'
                    }).then(function (result) {
                        if (result.value) {
                            window.location.href = "index.php"
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // if error occured 
                $("#newPassword").html('Create New Password');
                $("#newPassword").prop('disabled', false);

                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });
});