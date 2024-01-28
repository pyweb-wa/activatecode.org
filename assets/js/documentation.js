$(document).ready(function () {
    getcountriesapi();
    getappsapi();
});


function getcountriesapi() {

    var columnDefs = [
        {
            data: "country_name",
            title: "Country"
        },
        {
            data: "country",
            title: "code"

        }

    ];
    var myTable;
    myTable = $('#dataTablecountry').DataTable({
        "sPaginationType": "full_numbers",
        "order": [],
        ajax: {
            type: 'post',
            url: 'clientfxn/documentaion.php',
            dataSrc: '',
            data: { 'action': 'getcountriesapi' },

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
        toastr.error("No Application Available !!! ");
    };
};

function getappsapi() {

    var columnDefs = [
        {
            data: "Name",
            title: "App"
        },
        {
            data: "code",
            title: "Code"

        }

    ];
    var myTable;
    myTable = $('#dataTableapps').DataTable({
        "sPaginationType": "full_numbers",
        "order": [],
        ajax: {
            type: 'post',
            url: 'clientfxn/documentaion.php',
            dataSrc: '',
            data: { 'action': 'getappsapi' },

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
        toastr.error("No Application Available !!! ");
    };
};
