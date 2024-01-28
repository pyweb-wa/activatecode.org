$(document).ready(function () {
    $('#nav_item_customer').addClass("active");
    var columnDefs = [{
        data: "Id",
        title: "Id",
        type: "readonly"
    },
    {
        data: "name",
        title: "Name"
    },
    {
        data: "email",
        title: "email",
        required: "true",
        unique: "true"

    },
    {
        data: "Balance",
        title: "Balance",
        type: "readonly"
    },
    {
        data: "registration_date",
        title: "registration date",
        type: "readonly"
    },

    {
        data: "Valid",
        title: "token valid",
        type: "select",
        options: ["true", "false"]
    },
    {
        data: "Is_Activated",
        title: "Active ?",
        type: "select",
        options: ["true", "false"]
    },
    {
        data: "access_Token",
        title: "token",
        type: "select",
        options: ["generate new", "empty"],
        defaultContent: 'generate new'
    }
    ];

    var myTable;

    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "usersfxn/get_users.php",
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            dataSrc: ''
        },
        columns: columnDefs,
        dom: 'Bfrtip', // Needs button container
        select: 'single',
        responsive: true,
        altEditor: true, // Enable altEditor
        buttons: [{
            text: 'Add',
            name: 'add' // do not change name

        },
        {
            extend: 'selected', // Bind to Selected row
            text: 'Edit',
            name: 'edit' // do not change name
        },
        {
            extend: 'selected', // Bind to Selected row
            text: 'Delete',
            name: 'delete' // do not change name
        },
        {
            extend: 'selected', // Bind to Selected row
            text: 'Accounting',
            name: 'Accounting', // do not change name
            action: function (e, dt, node, config) {
                gotoTransactions(e, dt, node, config)
            },
        },
        {
            text: 'Refresh',
            name: 'refresh' // do not change name
        }
        ],
        onAddRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be / with type='PUT'
                url: "usersfxn/adduser.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onDeleteRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='DELETE'
                url: "usersfxn/deleteuser.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onEditRow: function (datatable, rowdata, success, error) {

            $.ajax({
                // a tipycal url would be /{id} with type='POST'
                url: "usersfxn/edituser.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        }
    });


});

function gotoTransactions(e, dt, node, config) {
    var adata = dt.rows({
        selected: true
    });
    var userID = adata.data()[0]['Id']
    var win = window.open('transactions.php?CustomerID=' + userID, '_blank');
    win.focus();
}