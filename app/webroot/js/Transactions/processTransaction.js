var Transactions = function () {

    var processCreateTime = function () {
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
            window.location.href = url + '/transactions/listTransaction/' + $(this).val();
        });
    };

    return{
        init: function () {
            processCreateTime();
            processSortBy();
        }
    };
}();

