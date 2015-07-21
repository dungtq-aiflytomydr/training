var Transactions = function () {

    var currentUrl = window.location.pathname; // Returns full URL: domain/controller/method/param

    function setupTime() {
        $('#rp-date').kendoDatePicker({
            start: "year",
            depth: "year",
            format: 'MM-yyyy'
        });

        var month, year;

        var urlArr = currentUrl.split('/');

        if (typeof urlArr[3] === 'undefined') {
            year = new Date().getFullYear();
            month = new Date().getMonth() + 1;
        } else {
            month = urlArr[3].substring(0, 2);
            year = urlArr[3].substring(2, 6);
        }

        var day = new Date(year, month - 1);
        $('#rp-date').data("kendoDatePicker").value(day);
    }

    var processCreateTime = function () {
        //process show datepicker for create_time
        $("#TransactionCreateTime").kendoDatePicker({
            format: "dd-MM-yyyy"
        });
    };

    var processSortBy = function () {

        if (currentUrl.indexOf('listSortByCategory') >= 0
                || currentUrl.indexOf('listSortByDate') >= 0) {
            setupTime();
        }

        $('#sortBy').change(function () {
            if ($(this).val() === 'listSortByCategory') {
                window.location.href = currentUrl.replace("listSortByDate", "listSortByCategory");
            } else {
                window.location.href = currentUrl.replace("listSortByCategory", "listSortByDate");
            }
        });
    };

    var report = function () {
        var urlArr = currentUrl.split('/');

        if (currentUrl.indexOf('report') >= 0) {
            setupTime();
        }

        $('#rp-date').change(function () {
            var date = $(this).val().split('-');
            url = urlArr[0] + '/' + urlArr[1] + '/' + urlArr[2] + '/' + date[0] + date[1];

            window.location.href = url;
        });
    };

    return{
        init: function () {
            processCreateTime();
            processSortBy();
            report();
        }
    };
}();

