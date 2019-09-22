var app_path = "http://localhost/foookat-seller-dashboard/public/";

$(document).ready(function () {

    <!--Confirm Delete Dialog show event handler -->
    $('#confirmDelete').on('show.bs.modal', function (e) {
        $message = $(e.relatedTarget).attr('data-message');
        $(this).find('.modal-body p').text($message);
        $title = $(e.relatedTarget).attr('data-title');
        $(this).find('.modal-title').text($title);
        $(this).find('.modal-title').text($title);
        // Pass form reference to modal for submission on yes/ok
        var form = $(e.relatedTarget).closest('form');
        $(this).find('.modal-footer #confirm').data('form', form);
    });

    <!-- Form confirm (yes/ok) handler, submits form -->
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function () {
        $(this).data('form').submit();
    });

    <!-- Displaying selected image in image view -->
    $("#profile_picture").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_profile_picture').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("#logo").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_logo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("#image1").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_image1').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("#image2").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_image2').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("#image3").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_image3').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    <!-- Web Editor text areas -->
    tinymce.init({
        selector: '#note, #overview',
        theme: 'modern',
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
        image_advtab: true,
        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ],
        content_css: [
            '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
            '//www.tinymce.com/css/codepen.min.css'
        ]
    });

    <!-- Date picker inputs -->
    $('#start_date').datepicker({format: "yyyy-mm-dd", startDate: "today"});
    $('#end_date').datepicker({format: "yyyy-mm-dd", startDate: "today"});

    <!-- Time picker inputs -->
    $('#mon_open').timepicker({'timeFormat': 'H:i:s'});
    $('#mon_close').timepicker({'timeFormat': 'H:i:s'});
    $('#tue_open').timepicker({'timeFormat': 'H:i:s'});
    $('#tue_close').timepicker({'timeFormat': 'H:i:s'});
    $('#wed_open').timepicker({'timeFormat': 'H:i:s'});
    $('#wed_close').timepicker({'timeFormat': 'H:i:s'});
    $('#thu_open').timepicker({'timeFormat': 'H:i:s'});
    $('#thu_close').timepicker({'timeFormat': 'H:i:s'});
    $('#fri_open').timepicker({'timeFormat': 'H:i:s'});
    $('#fri_close').timepicker({'timeFormat': 'H:i:s'});
    $('#sat_open').timepicker({'timeFormat': 'H:i:s'});
    $('#sat_close').timepicker({'timeFormat': 'H:i:s'});
    $('#sun_open').timepicker({'timeFormat': 'H:i:s'});
    $('#sun_close').timepicker({'timeFormat': 'H:i:s'});
    $('#start_time').timepicker({'timeFormat': 'H:i:s'});
    $('#end_time').timepicker({'timeFormat': 'H:i:s'});

    <!-- Discount type radio button actions -->
    $("input[name=discount_type]:radio").click(function () {
        if ($(this).val() == 'Percentage Off') {
            $('#new_price').val('');
            $('#amount_off').val('');
            $('#amount_off_div').hide();
            $('#percentage_off').val('');
            $('#percentage_off_div').show();
        }
        else {
            $('#new_price').val('');
            $('#percentage_off').val('');
            $('#percentage_off_div').hide('');
            $('#amount_off').val('');
            $('#amount_off_div').show();
        }
    });

    <!-- New price after applying percentage discount or amount discount -->
    $('#original_price').on('input', function () {
        var original_price = $('#original_price').val();
        var amount_off = $('#amount_off').val();
        var percentage_off = $('#percentage_off').val();
        if (amount_off) {
            var new_price = original_price - amount_off;
            $('#new_price').val(new_price);
        }
        if (percentage_off) {
            var new_price = original_price - original_price * percentage_off / 100;
            $('#new_price').val(new_price);
        }
    });
    $('#amount_off').on('input', function () {
        var original_price = $('#original_price').val();
        var amount_off = $('#amount_off').val();
        var new_price = original_price - amount_off;
        $('#new_price').val(new_price);
    });
    $('#percentage_off').on('input', function () {
        var original_price = $('#original_price').val();
        var percentage_off = $('#percentage_off').val();
        var new_price = original_price - original_price * percentage_off / 100;
        $('#new_price').val(new_price);
    })
});

function stateChanged() {
    var state = $('#state').val();
    var url = app_path + 'miscellaneous/cities';
    $.ajax({
        type: "POST",
        url: url,
        data: {'state': state, '_token': $('input[name=_token]').val()},
        success: function (result) {
            var city = $("#city");
            city.children().remove();
            if (result === null) {
                city.append($("<option>").val("").text("Pick a city"));
            }
            else {
                city.append($("<option>").val("").text("Pick a city"));
                for (var i = 0; i < result.length; i++) {
                    var val = result[i].name;
                    var text = result[i].name;
                    city.append($("<option>").val(val).text(text));
                }
            }
        }
    });
}

function deleteProfilePicture(id) {
    var url = app_path + 'miscellaneous/deleteProfilePicture';
    $.ajax({
        type: "POST",
        url: url,
        data: {'id': id, '_token': $('input[name=_token]').val()},
        success: function (result) {
            if (result) {
                $("#img_profile_picture").attr("src", app_path + "/images/uploads/avatars/default.png");
            }
        }
    });
}

function deleteBusinessLogo(id) {
    var url = app_path + 'miscellaneous/deleteBusinessLogo';
    $.ajax({
        type: "POST",
        url: url,
        data: {'id': id, '_token': $('input[name=_token]').val()},
        success: function (result) {
            if (result) {
                $("#img_logo").attr("src", app_path + "/images/uploads/avatars/logo.png");
            }
        }
    });
}

function deleteStoreImage(id, number) {
    var url = app_path + 'miscellaneous/deleteStoreImage';
    $.ajax({
        type: "POST",
        url: url,
        data: {'id': id, 'number': number, '_token': $('input[name=_token]').val()},
        success: function (result) {
            if (result) {
                if (number == 1) {
                    $("#img_image1").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image1Radio").attr("checked", false);
                }
                if (number == 2) {
                    $("#img_image2").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image2Radio").attr("checked", false);
                }
                if (number == 3) {
                    $("#img_image3").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image3Radio").attr("checked", false);
                }
            }
        }
    });
}

function deleteDealImage(id, number) {
    var url = app_path + 'miscellaneous/deleteDealImage';
    $.ajax({
        type: "POST",
        url: url,
        data: {'id': id, 'number': number, '_token': $('input[name=_token]').val()},
        success: function (result) {
            if (result) {
                if (number == 1) {
                    $("#img_image1").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image1Radio").attr("checked", false);
                }
                if (number == 2) {
                    $("#img_image2").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image2Radio").attr("checked", false);
                }
                if (number == 3) {
                    $("#img_image3").attr("src", app_path + "/images/uploads/avatars/image.png");
                    $("#image3Radio").attr("checked", false);
                }
            }
        }
    });
}