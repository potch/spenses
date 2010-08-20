function get_userid() {
    var cookie_fields = document.cookie.split('; ');
    for (var i = 0; i < cookie_fields.length; i++) {
    var cookie = cookie_fields[i].split('=');
    if (cookie[0] == 'user[userid]')
        return cookie[1];
    }
}

function showModal(msg, callback, options) {

    function hideModal() {
        $("#modal").addClass("hidden");
    }

    options = options || {};
    var cancelFunc = options.cancelFunc || $.noop;
    callback = callback || $.noop;

    $("#modal-ok").click(function () {
        hideModal();
        callback.call();
    })

    $("#modal-msg").text(msg);
    $("#modal").removeClass("hidden");

}

$(document).ready(function () {

    $.ajaxSetup({
        dataType: 'json'
    });

    ////////////////////////////////////////
    // Callback functions for asynchronous munging of UI

    function get_cohort_list_callback(response) {

        if (response && response.status == 'success' && response.data.length) {

            var myid = get_userid();

            var contents = "";
            var most_recent_cohort = null;

            for (var i = 0; i < response.data.length; i++)
            if (most_recent_cohort == null || response.data[i].date_updated < response.data[most_recent_cohort].date_updated)
                most_recent_cohort = i

            for (var i = 0; i < response.data.length; i++)
            {
            var item = response.data[i];
            contents += "<option name='" + item.name + "' value='" + item.cohortid + "'" + (i == most_recent_cohort ? " selected" : "") + ">" + item.name + "</option>";
            }

            document.getElementById('cohorts').innerHTML = contents;

            $.post('action/get_user_list.php', {'cohortid': $('#cohorts').val()}, get_user_list_callback);
        }
    }

    function get_balance_callback(response) {

        if (response && response.status == 'success') {

            var owelist = "", owedlist = "", myid = get_userid();

            for (var i = 0; i < response.data.length; i++) {
                var item = response.data[i];
                if (item.userid_from == myid && item.amount > 0) {
                    owelist += '<li>You owe ' + item.to_nick + ' <span class="amount">$' + (1 * item.amount) + '</span></li>';
                } else if (item.userid_from == myid && item.amount < 0) {
                    owedlist += '<li>' + item.to_nick + ' owes you <span class="amount">$' + (-1 * item.amount) + '</span></li>';
                } else if (item.userid_to == myid && item.amount > 0) {
                    owedlist += '<li>' + item.from_nick + ' owes you <span class="amount">$' + (1 * item.amount) + '</span></li>';
                } else if (item.userid_to == myid && item.amount < 0) {
                    owelist += '<li>You owe ' + item.from_nick + ' <span class="amount">$' + (-1 * item.amount) + '</span></li>';
                }
            }

            $('#owelist')[0].innerHTML  = owelist;
            $('#owedlist')[0].innerHTML = owedlist;

            if (owelist  == "") $("#owe").addClass("hidden");
            if (owedlist == "") $("#owed").addClass("hidden");

            if (owelist == "" && owedlist == "") $("#debtfree").removeClass("hidden");
        }
    }

    function get_user_list_callback(response) {
        if (response && response.status == 'success')
        {
            var myid = get_userid();

            var contents_user_select = "";
            var contents_iou_table   = "";
            for (var i = 0; i < response.data.length; i++) {
                var item = response.data[i];
                if (myid == item.userid) {
                    contents_user_select += "<option name='" + item.nick + "' value='" + item.userid + "' selected>" + item.nick + "</option>";
                    contents_iou_table   += "<div class='row hidden' data-userid='" + item.userid + "'><label for=''>To " + item.nick + "</label><input type='tel' name='iou["+i+"][amount]' /><input type='hidden' name='iou["+i+"][userid]' value='" + item.userid + "' /></div>";
                }
                else {
                    contents_user_select += "<option name='" + item.nick + "' value='" + item.userid + "'>" + item.nick + "</option>";
                    contents_iou_table   += "<div class='row' data-userid='" + item.userid + "'><label for=''>To " + item.nick + "</label><input type='tel' name='iou["+i+"][amount]' /><input type='hidden' name='iou["+i+"][userid]' value='" + item.userid + "' /></div>";
                }
            }

            $('#whopaid')[0].innerHTML           = contents_user_select;
            $('#purchaseamounts')[0].innerHTML = contents_iou_table;
        }
    }

    function add_purchase_callback(response) {
        if (response && response.status == 'success') {
            $('#location, #desc, #amount, #purchaseamounts input').val('');
        }
    }

    ////////////////////////////////////////
    // Login javascript

    $('#login-form').submit(function (e) {
        $form = $('#login-form');
        $.post($form.attr('action'), {email: $('#login-email').val()},
            function (response, status, xhr) {
                window.location.href=response.data;
            });
        return false;
    });

    ////////////////////////////////////////
    // Navigation bar events

    $('#nav ul').click(function(e) {
        var li = $(e.target);

        if (li.attr('pane')) {
            $('#nav ul li.selected, #content .pane.selected').removeClass("selected");
            li.addClass("selected");
            $("#" + li.attr('pane')).addClass('selected');
        }

        if (li.attr('pane') == 'purchases') {
            $.post('action/get_cohort_list.php', {'userid': get_userid()}, get_cohort_list_callback);
        } else if (li.attr('pane') == 'balances') {
            $.post('action/get_balance.php',     {'userid': get_userid()}, get_balance_callback);
        }
    });

    ////////////////////////////////////////
    // Purchases pane events

    $('#cohorts').change(function(e) {
        $.post('action/get_user_list.php', {'cohortid': $('#cohorts').val()}, get_user_list_callback);
    });

    $('#whopaid').change(function(e) {
        var selected_id = $(this).val();

        $('#purchaseamounts .row').removeClass('hidden');
        $('#purchaseamounts .row[data-userid=' + selected_id +']').addClass('hidden');
    });

    $('#purchases-form').submit(function(e) {
        e.preventDefault();

        $form = $('#purchases-form');
        $.post($form.attr('action'), $form.serializeArray(), add_purchase_callback);

        return false;
    });

    ////////////////////////////////////////
    // Default view requires starting with get-balance post

    $.post('action/get_balance.php', {'userid': get_userid()}, get_balance_callback);
});