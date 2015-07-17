var Transactions = function () {

    var processCreateTime = function () {
        //process show datepicker for create_time
        $("#TransactionCreateTime").kendoDatePicker({
            format: "dd/MM/yyyy"
        });

        var currentUrl = window.location.pathname;     // Returns full URL

        //process datetime for edit form
        if (currentUrl.indexOf("edit") >= 0) {
            var day = $(".k-input").val().split("-");
            day = new Date(day[2], day[1] - 1, day[0]);

            $('.k-input').data("kendoDatePicker").value(day);
        }

    };

    var processSortBy = function () {
        $('#sortBy').change(function () {
            var url = $('#myNavbar').attr('data-url');
            window.location.href = url + '/transactions/' + $(this).val();
        });
    };

    var reportByDate = function () {

        var month, year;

        $('#rp-date').kendoDatePicker({
            start: "year",
            depth: "year",
            format: 'MM-yyyy'
        });

        var currentUrl = window.location.pathname;     // Returns full URL
        var urlArr = currentUrl.split('/');
        if (typeof urlArr[3] === 'undefined') {
            year = new Date().getFullYear();
            month = new Date().getMonth() + 1;
        } else {
            month = urlArr[3].substring(0, 2);
            year = urlArr[3].substring(2, 6);
        }

        var day = new Date(year, month - 1);
        $('.k-input').data("kendoDatePicker").value(day);

        $('#rp-date').change(function () {
            var date = $(this).val().split('-');
            var url = $('#myNavbar').attr('data-url');
            url = '/transactions/report/' + date[0] + date[1];

            window.location.href = url;
        });
    };

    return{
        init: function () {
            processCreateTime();
            processSortBy();
            reportByDate();
        }
    };
}();

