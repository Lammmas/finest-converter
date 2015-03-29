$(document).ready(function () {
    NProgress.start();

    $("#historytable").dataTable();

    $.ajax("/api/currencies/", {
        dataType: "json",
        method: "POST",
        success: function(data) {
            $.each(data, function (index, value) {
                var elem = '<li data-curr="' + index + '"><a href="">' + value + ' (' + index + ')' + '</a></li>';
                $("#fromselect").append(elem);
                $("#targetselect").append(elem);
            });
        }
    });

    $("#time").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        maxDate: new Date(2014, 11, 31),
        minDate: new Date(1992, 5, 21),
        onSelect: validate
    });

    $('.dropdown-menu').on("click", "li", function (e) {
        e.preventDefault();
        var selected = $(this).text();
        var $div = $(this).closest("div");
        $div.find(".currency-name").text(selected);
        $div.find(".dropdown-target").val($(this).data("curr"));
        validate();
    });

    $("#fromamount").on("keyup", function() {
        var value = parseFloat($(this).val());
        if (isNaN(value)) {
            $(this).parent().removeClass("has-success").addClass("has-error");
        } else $(this).parent().removeClass("has-error").addClass("has-success");
        validate();
    });

    $("#confirmbtn").click(function() {
        NProgress.start();

        var from = $("#fromamount").val();
        var currency = $("#fromcurrency").val();
        var target = $("#targetcurrency").val();
        var time = $("#time").val();

        var info = {
            "amount": from,
            "from": currency,
            "to": target,
            "time": time
        };

        $.ajax("/api/convert/", {
            dataType: "json",
            data: JSON.stringify(info),
            method: "POST",
            complete: function(jqXHR, textStatus) {
                NProgress.done();
                // JSON.parse($.cookie("History")) <- contains all entries
            },
            success: function (data) {
                addResult(info, data)
            },
            error: function(jqXHR, textStatus) {
                showError(textStatus);
            }
        });
    });

    function validate() {
        var from = parseFloat($("#fromamount").val());
        var currency = $("#fromcurrency").val();
        var target = $("#targetcurrency").val();
        var time = new Date($("#time").val()).toISOString();
        var $btn = $("#confirmbtn");
        var valid = true;

        if (isNaN(from) || from <= 0) valid = false;
        if (currency.length != 3 || target.length != 3) valid = false;
        if (typeof time == 'undefined') valid = false;

        if (valid == true) $btn.removeClass("disabled").removeAttr("disabled");
        else $btn.addClass("disabled").attr("disabled", "disabled");
    }

    function addResult(request, result) {
        if (result['status'] == "error") showError(result['result']);
        else {
            $(".alert").fadeOut("slow");

            var row = $("<tr></tr>");
            var cols = [
                $("<td></td>"),
                $("<td></td>"),
                $("<td></td>"),
                $("<td></td>"),
                $("<td></td>")
            ];

            cols[0].text(request['amount']);
            cols[1].text(request['from'] + " -> " + request['to']);
            cols[2].text(request['time']);
            cols[3].text(result['result']['est']);
            cols[4].text(result['result']['lit']);

            row.append(cols[0], cols[1], cols[2], cols[3], cols[4]);
            row.prependTo("#historytable");
        }
    }

    function showError(error) {
        $("#alert").hide().removeClass("hidden");
        $("#alertcontent").text(error);
        $("#alert").fadeIn("slow");
    }
});

$(window).load(function(){
    NProgress.done();
});
