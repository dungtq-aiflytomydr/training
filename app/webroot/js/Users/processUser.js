var Users = function () {

    //params
    var regExEmail = new RegExp(/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i);

    var processRegister = function () {
        $('#btn-register').click(function () {
            var user_email = $.trim($('#u-email').val());
            var user_pw = $.trim($('#u-pw').val());
            var user_name = $.trim($('#u-name').val());

            if (user_email.length < 1) {
                alert('Please fill out your email!');
                return false;
            }
            if (!regExEmail.test(user_email)) {
                alert('Email not validate!');
                return false;
            }

            if (user_pw.length < 6 || user_pw.length > 32) {
                alert('Your password must be 6 - 32 characters!');
                return false;
            }

            if (user_name.length < 4) {
                alert('Your password must be greater or equal than 4 characters!');
                return false;
            }

            $('#frmRegister').submit();
        });
    };

    var processForgotPw = function () {
        $('#btn-forgotPw').click(function () {
            var user_email = $.trim($('#u-email').val());

            if (user_email.length < 1) {
                alert('Please fill out your email!');
                return false;
            }
            if (!regExEmail.test(user_email)) {
                alert('Email not validate!');
                return false;
            }

            $('#frmForgotPw').submit();
        });
    };

    var processChangePw = function () {
        
        var url = $('#myNavbar').attr('data-url');
        var user_key = $('#myNavbar').attr('data-user');
        var new_pw = $('#u-newpw').val();
        var confirm_pw = $('#u-confirmpw').val();

        /**
         * show alert with messenger
         * @param {object} element: this object content msg
         * @param {boolean} is_error: choose effect for msg
         * @param {text} msg:  msg content
         * @returns {undefined}
         */
        function showMessenger(element, is_error, msg) {
            if (is_error === null) {
                element.removeClass('text-success text-error').text('');
                return false;
            }

            if (!is_error) {
                element.addClass('text-success').removeClass('text-error ')
                        .text(msg);
            } else {
                element.addClass('text-error').removeClass('text-success ')
                        .text(msg);
            }
        }

        /**
         * check condition old password
         */
        $('#u-oldpw').keyup(function () {
            var $this = $(this);
            var old_pw = $.trim($this.val());

            if (old_pw.length < 1) {
                $this.removeClass('border-error border-success');
                showMessenger($this.nextAll('.msg-error'), null, null);
                $('#btn-changepw').prop('disabled', true);
                return false;
            }

            if ($('#u-newpw').nextAll('.msg-error').hasClass('text-success')
                    && $('#u-confirmpw').nextAll('.msg-error').hasClass('text-success')) {
                $('#btn-changepw').prop('disabled', false);
            }


            $.ajax({
                url: url + '/users/checkOldPw',
                type: 'POST',
                async: false,
                data: {key: user_key, value: old_pw},
                success: function (data) {
                    if (data == 1) {
                        $this.addClass('border-success').removeClass('border-error');
                        showMessenger($this.nextAll('.msg-error'), false, 'Password correct.');
                        return false;
                    } else {
                        $this.addClass('border-error').removeClass('border-success');
                        showMessenger($this.nextAll('.msg-error'), true, 'Password incorrect.');
                        $('#btn-changepw').prop('disabled', true);
                        return false;
                    }
                }
            });
        });

        /**
         * check condition new password
         */
        $('#u-newpw').keyup(function () {
            var $this = $(this);
            var new_pw = $.trim($this.val());
            var confirm_pw = $.trim($('#u-confirmpw').val());

            if (new_pw.length == 0) {
                $this.removeClass('border-success').addClass('border-error');
                showMessenger($this.nextAll('.msg-error'), true, 'Password not empty.');
                $('#btn-changepw').prop('disabled', true);
                return false;
            }

            if (new_pw.length < 6 || new_pw.length > 32) {
                $this.removeClass('border-error border-success');
                showMessenger($this.nextAll('.msg-error'), true, 'Password has length 6 - 32 characters.');
                $('#btn-changepw').prop('disabled', true);
                return false;
            }

            if (confirm_pw.length > 0 && (confirm_pw !== new_pw)) {
                showMessenger($('#u-confirmpw').nextAll('.msg-error'), true, 'Confirm password incorrect.');
                $('#btn-changepw').prop('disabled', true);
                return false;
            }

            $this.removeClass('border-error').addClass('border-success');
            showMessenger($this.nextAll('.msg-error'), false, 'Password has use.');
            if (confirm_pw.length > 0) {
                showMessenger($('#u-confirmpw').nextAll('.msg-error'), false, 'Confirm password correct.');
            }

            if ($('#u-oldpw').nextAll('.msg-error').hasClass('text-success')
                    && $('#u-confirmpw').nextAll('.msg-error').hasClass('text-success')) {
                $('#btn-changepw').prop('disabled', false);
            }
        });

        /**
         * check condition confirm password
         */
        $('#u-confirmpw').keyup(function () {
            var $this = $(this);
            var confirm_pw = $.trim($this.val());
            var new_pw = $.trim($('#u-newpw').val());

            if (confirm_pw !== new_pw) {
                $this.removeClass('border-success').addClass('border-error');
                showMessenger($this.nextAll('.msg-error'), true, 'Confirm password incorrect.');
                $('#btn-changepw').prop('disabled', true);
                return false;
            }

            $this.removeClass('border-error').addClass('border-success');
            showMessenger($this.nextAll('.msg-error'), false, 'Confirm password correct.');

            if ($('#u-oldpw').nextAll('.msg-error').hasClass('text-success')
                    && $('#u-newpw').nextAll('.msg-error').hasClass('text-success')) {
                $('#btn-changepw').prop('disabled', false);
            }
        });

        $('#btn-changepw').click(function () {
            $('#frmChangePw').submit();
        });
    }

    return{
        init: function () {
            processRegister();
            processForgotPw();
            processChangePw();
        }
    };
}();

