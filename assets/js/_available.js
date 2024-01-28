$(document).ready(function () {
    get_user_details();
});

function get_user_details() {

    var columnDefs = [{
        data: "img",
        title: "",
        width: "20px"
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
        title: "country code"
    },
    {
        data: "app_code",
        title: "app code"

    }

    ];
    var myTable;
    myTable = $('#dataTable2').DataTable({
        "sPaginationType": "full_numbers",
        "order": [],
        ajax: {
            type: 'post',
            url: 'clientfxn/get_avail.php',
            dataSrc: '',

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
        toastr.error("No Application Available !!! ");
    };
};