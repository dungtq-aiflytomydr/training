var Transactions = function () {

    var currentUrl = window.location.pathname; // Returns full URL: domain/controller/method/param
    var url = $('#myNavbar').attr('data-url');

    function setupTime() {
        $('#rp-date').kendoDatePicker({
            start: "year",
            depth: "year",
            format: 'MM-yyyy'
        });

        var arrUrl = currentUrl.split('/');
        $('#rp-date').change(function () {
            var date = $(this).val().split('-');
            url = url + '/' + arrUrl[1] + '/' + arrUrl[2] + '/' + date[0] + date[1];

            window.location.href = url;
        });
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
        if (currentUrl.indexOf('report') >= 0) {
            setupTime();
        }
    };

    return{
        init: function () {
            processCreateTime();
            processSortBy();
            report();
        }
    };
}();

