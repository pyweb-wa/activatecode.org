$(document).ready(function () {
    $('#nav_item_customer').addClass("active");
    var columnDefs = [
        {
            data: "img",
            title: '',
            width: '20px'
        }, {
            data: "application",
            title: "application"
        },
        {
            data: "count",
            title: "count"

        },
        {
            data: "country_code",
            title: "country_code"
        },
        {
            data: "app_code",
            title: "app code"

        }

    ];

    var myTable;

    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "usersfxn/get_aval.php",
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            dataSrc: ''
        },
        columns: columnDefs,
        dom: 'Bfrtip', // Needs button container
        select: 'single',
        responsive: true,
    });
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
        toastr.error("No Application Available !!! ");
    };

});

function gotoTransactions(e, dt, node, config) {
    var adata = dt.rows({
        selected: true
    });
    var userID = adata.data()[0]['Id']
    var win = window.open('transactions.php?CustomerID=' + userID, '_blank');
    win.focus();
}