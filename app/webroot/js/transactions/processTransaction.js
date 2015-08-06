var Transactions = function () {

    var currentUrl = window.location.pathname; // Returns full URL: domain/controller/method/param
    var url = $('#myNavbar').attr('data-url');

    function setupTime() {
        $('#rp-date').kendoDatePicker({
            start: "year",
            depth: "year",
            format: 'yyyy-MM'
        });

        var arrUrl = currentUrl.split('/');

        $('#rp-date').change(function () {
            var date = $(this).val().split('-');
            var redirectUrl = url + '/transactions/view/sortDate' + date[0] + '-' + date[1];

            if (arrUrl[3] !== undefined) {
                redirectUrl = url + '/' + arrUrl[1] + '/' + arrUrl[2] + '/' + arrUrl[3] + '/' + date[0] + '-' + date[1];
            }

            window.location.href = redirectUrl;
        });

    }

    var processCreateTime = function () {
        //process show datepicker for create_time
        $("#TransactionCreateTime").kendoDatePicker({
            format: "yyyy-MM-dd"
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
            var redirectUrl = '',
                oldStr = 'sortCategory';

            if (currentUrl.indexOf('sortDate') < 0
                    && currentUrl.indexOf('sortCategory') < 0) {
                redirectUrl = currentUrl + '/view/' + $(this).val();
            } else {

                if ($(this).val() === 'sortCategory') {
                    oldStr = 'sortDate';
                }
                redirectUrl = currentUrl.replace(oldStr, $(this).val());
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

