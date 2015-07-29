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
            var redirectUrl = url + '/transactions/listSortByDate/' + date[0] + date[1];

            if (arrUrl[2] !== undefined) {
                redirectUrl = url + '/' + arrUrl[1] + '/' + arrUrl[2] + '/' + date[0] + date[1];
            }

            window.location.href = redirectUrl;
        });

    }

    var processCreateTime = function () {
        //process show datepicker for create_time
        $("#TransactionCreateTime").kendoDatePicker({
            format: "dd-MM-yyyy"
        });
    };

    var processAddTransaction = function () {
        $('#btnTransactionAdd').click(function () {
            $(this).val('Please waitting...');
            $(this).attr('disabled', true);
            $('#TransactionAddForm').submit();
            return true;
        });
    };

    var processSortBy = function () {

        setupTime();

        $('#sortBy').change(function () {
            var redirectUrl = '';

            if ($(this).val() === 'listSortByCategory') {
                redirectUrl = currentUrl.replace("listSortByDate", "listSortByCategory");
            } else {
                redirectUrl = currentUrl.replace("listSortByCategory", "listSortByDate");
            }

            window.location.href = redirectUrl;
        });
    };

    var report = function () {
        if (currentUrl.indexOf('report') >= 0) {
            setupTime();
        }
    };

    return{
        init: function () {
            processAddTransaction();
            processCreateTime();
            processSortBy();
            report();
        }
    };
}();

