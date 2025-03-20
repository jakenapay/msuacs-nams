(function($) {
    'use strict';
    $(function() {
        $("[nams-authenticate]").submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize(); // Changed to serialize instead of FormData
            $.ajax({
                url: site_url + "admin/verify",
                type: "POST",
                data: data,
                beforeSend: function() {
                    if ($("input[name='username']").val().length < 1) {
                        warning("Username cannot be empty!", "fa fa-exclamation-triangle");
                        return false;
                    }
                    if ($("input[name='password']").val().length < 1) {
                        warning("Passwords cannot be empty!", "fa fa-exclamation-triangle");
                        return false;
                    }
                    $("button").attr("disabled", "disabled");
                },
                success: function(response) {
                    // Assuming response is already an object
                    if (response.status == 200) {
                        success(response.message, "fa fa-check-circle", refresh(3000));
                    } else {
                        $("button").removeAttr("disabled");
                        console.log(response.message);
                        danger(response.message, "fa fa-exclamation-triangle");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // Wave effects (unchanged)
        $("#feel-the-wave").wavify({
            height: 100,
            bones: 3,
            amplitude: 90,
            color: 'rgb(245,219,45, 0.1)',
            speed: .25
        });

        $("#feel-the-wave-two").wavify({
            height: 70,
            bones: 5,
            amplitude: 60,
            color: 'rgb(245,219,45, 0.3)',
            speed: .35
        });

        $("#feel-the-wave-three").wavify({
            height: 50,
            bones: 4,
            amplitude: 50,
            color: 'rgb(245,219,45, 0.2)',
            speed: .45
        });
    });
})(jQuery);
