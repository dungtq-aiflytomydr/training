var Users = function () {

    var previewAvatar = function () {

        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var fileType = input.files[0]['type'];

                if (fileType !== 'image/jpeg'
                        && fileType !== 'image/jpg'
                        && fileType !== 'image/gif'
                        && fileType !== 'image/png') {
                    $('.u-ava-preview').attr('src', '/img/ava_default.jpeg');
                    alert('Image format incorrect.');
                    return false;
                }

                reader.onload = function (e) {
                    $('.u-ava-preview').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#UserAvatar").change(function () {
            readURL(this);
        });
    }

    return{
        init: function () {
            previewAvatar();
        }
    };
}();

