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

    return{
        init: function () {
            processRegister();
            processForgotPw();
        }
    };
}();

