$(document).ready(function () {

    $.ajaxSetup({
        dataType: 'json'
    });

    $('#login-form').submit(function (e) {
        $form = $('#login-form');
        $.post($form.attr('action'), {email: $('#login-email').val()}, 
            function (response, status, xhr) {
                alert(response.data.url);
                window.location.href=response.data;
            });
        return false;
    });

    $('#nav ul').click(function(e) {
        var li = $(e.target);
        if (li.attr('pane')) {
            $('#nav ul li.selected, #content .pane.selected').removeClass("selected");
            li.addClass("selected");
            $("#" + li.attr('pane')).addClass('selected');
        }
    });

    $.post('action/getbalance.php', function(data) {
        var result = $.parseJSON(data);

        document.getElementById("owelist").innerHTML="";
        document.getElementById("owedlist").innerHTML="";

        if (result.owe && result.owe.length) {
            for (var i = 0; i < result.owe.length; i++) {
                var item = result.owe[i];
                document.getElementById("owelist").innerHTML += "<li>You owe " + item.name + " <span class='amount'>$" + item.amount + "</li>";
            }
        } else {
            $("#owe").addClass("hideMe");
        }
        if (result.owed && result.owed.length) {
            for (var i=0; i < result.owed.length; i++) {
                var item = result.owed[i];
                document.getElementById("owedlist").innerHTML += "<li>" + item.name + " owes you <span class='amount'>$" + item.amount + "</li>";
            }
        } else {
            $("#owed").addClass("hideMe");
        }
    });
});