$(document).ready(function () {
    var columnDefs = [
        {
            data: "ServerID",
            title: 'Server '
        }, {
            data: "ServerType",
            title: "Server Type"
        },
        {
            data: "Number",
            title: "Number"
        },
        {
            data: "Country",
            title: "country code"
        },
        {
            data: "Port",
            title: "Port"
        }
        ,
        {
            data: "count",
            title: "count"
        }

    ];
    var myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "usersfxn/get_stats.php",
            data: { "action": "STATS_NUMBER" },
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            dataSrc: ''
        },
        columns: columnDefs,
        select: 'single',
        responsive: true,
    });
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
        toastr.error("No Numbers Available !!! ");
    };

});