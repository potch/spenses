function get_userid() {
    var cookie_fields = document.cookie.split('; ');
    for (var i = 0; i < cookie_fields.length; i++) {
    var cookie = cookie_fields[i].split('=');
    if (cookie[0] == 'user[userid]')
        return cookie[1];
    }
}


// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){
  var cache = {};

  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :

      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +

        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +

        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");

    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();


$(document).ready(function () {

    $.ajaxSetup({
        dataType: 'json'
    });

    /**
     * Shows the modal dialog.
     * Takes a message, and an optional callback.
    **/
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

            $('#cohorts').html(contents);

            $.post('action/get_user_list.php', {'cohortid': $('#cohorts').val()}, get_user_list_callback);
        }
    }

    function get_balance_callback(response) {

        var balance_to = tmpl('<li><%=to_nick%> owes you <span class="amount"><%=amount%></span></li>');
        var balance_from = tmpl('<li>You owe <%=from_nick%> <span class="amount"><%=amount%></span></li>');

        if (response && response.status == 'success') {

            var owelist = "", owedlist = "", myid = get_userid();

            for (var i = 0; i < response.data.length; i++) {
                var item = response.data[i];
                if (item.amount < 0) item.amount *= -1;
                item.amount *= 1;
                if (item.userid_from == myid) {
                    owedlist += balance_to(item);
                } else {
                    owelist += balance_from(item);
                }
            }

            $('#owelist').html(owelist);
            $('#owedlist').html(owedlist);

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

            $('#whopaid').html(contents_user_select);
            $('#purchaseamounts').html(contents_iou_table);
        }
    }

    function add_purchase_callback(response) {
        if (response && response.status == 'success') {
            showModal('Purchase added');
            $('#location, #desc, #amount, #purchaseamounts input').val('');
        } else if (response && response.status == 'error') {
            showModal('Failed purchase add: ' + response.message);
        }
    }

    function get_purchases_callback(response) {
        if (response && response.status == 'success') {

            var pl = "";

            for (var i = 0; i < response.data.length; i++) {
                var item = response.data[i];
                pl += "<li>" +
                      item.date_of.match(/\d{4}-\d{2}-\d{2}/)[0] +
                      "<span class='amount'>" + Math.abs(item.myamount) + " of  " + +item.amount + "</span>" +
                      "<div style='font-size:.8em;color:#888;'>" + (item.location_name || item.description) +
                      "<span style='float:right;margin-right:5%'>paid by " + item.payer_nick + "</span></div>" +
                      "</li>";
            }

            $("#purchaselist").html(pl);
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
            show_pane(li.attr('pane'));
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

    function show_pane(pane_id) {
        $pane = $('#' + pane_id);
        $('#nav ul li.selected, #content .pane.selected').removeClass("selected");
        $pane.addClass("selected");
        $("li[pane='" + pane_id + "']").addClass('selected');

        if (pane_init[pane_id]) pane_init[pane_id]();
    }

    var pane_init = {
        'purchases' : function() {
            $.post('action/get_cohort_list.php', {'userid': get_userid()}, get_cohort_list_callback);
        },
        'balances' : function () {
            $.post ('action/get_balance.php', {
                'userid': get_userid(),
                'cohortid': 1
            }, get_balance_callback);
            $.post('action/get_purchases.php', {'userid': get_userid(), 'cohortid': '1'}, get_purchases_callback);
        }
    };

    show_pane('balances');
});