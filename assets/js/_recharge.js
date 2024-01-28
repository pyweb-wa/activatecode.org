$(document).ready(function() {
    get_user_details();
});

function get_user_details() {

    var columnDefs = [{
            data: "id",
            title: "invoice ID",
            type: "readonly"
        },
        {
            data: "amount",
            title: "amount"
        },
        {
            data: "type",
            title: "Type"
        },
        {
            data: "recharge_date",
            title: "date"
        },
        {
            data: "gateway",
            title: "payment gateway"
        }
    ];

    var myTable = $('#dataTable2');
    myTable = $('#dataTable2').DataTable({
        destroy: true,
        "sPaginationType": "full_numbers",
        "order": [],
        ajax: {
            type: 'post',
            url: 'clientfxn/get_recharge_his.php',
            dataSrc: '',

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });
};