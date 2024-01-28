$(document).ready(function () {
    $('#nav_item_customer').addClass("active");
    var columnDefs = [{
        data: "Id_User",
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
        data: "Passwd",
        title: "Password",
        required: "true",
        visible: false

    },
    {
        data: "token",
        title: "Token",
        required: "false",
        readonly: true,
        defaultContent: ""

    },
    {
        data: "is_super",
        title: "IS Super",
        type: "select",
        options: ["false", "true"]
    },
    {
        data: "Is_Activated",
        title: "Active ?",
        type: "select",
        options: ["true", "false"]
    },  {
        data: "balance",
        title: "balance",
        required: "true",
        unique: "true"

    },
    ];

    var myTable;

    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "adminsfxn/get_admins.php",
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
            text: 'Refresh',
            name: 'refresh' // do not change name
        },
         {
            extend: 'selected', // Bind to Selected row
            text: 'Recharge',
            name: 'recharge', // do not change name
            action: function(e, dt, node, config) {
                showRecharge(e, dt, node, config)
            }
        }
        ],
        onAddRow: function (datatable, rowdata, success, error) {
            var pass = $('#Passwd').val();
            if (pass.length >= 8) {
                $.ajax({
                    // a tipycal url would be / with type='PUT'
                    url: "adminsfxn/addadmin.php",
                    type: 'POST',
                    data: rowdata,
                    success: success,
                    error: error
                });
            } else {
                $('#Passwd').css('border', '1px solid red')
            }

        },
        onDeleteRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='DELETE'
                url: "adminsfxn/deleteadmin.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onEditRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='POST'
                url: "adminsfxn/editadmin.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        }
    });

    // console.log(myTable.column( 0 ).visible(false))

});

function showRecharge(e, dt, node, config) {
    var adata = dt.rows({
        selected: true
    });
    var fapi_id = adata.data()[0]['Id_User']
    var fapi_name = adata.data()[0]['name']
    var fapi_bal = adata.data()[0]['balance']
        // console.log(JSON.stringify(adata.data()[0]))
    $('#admin_id_recharge_modal').val(fapi_id)
    $('#admin_recharge_modal').val(fapi_name) //api_bal_recharge_modal
    $('#admin_bal_recharge_modal').val(fapi_bal) //api_bal_recharge_modal 
    $("#myModal").modal();
}

function insert_recharge() {
    var admin_id = $('#admin_id_recharge_modal').val();
    var fname = $('#admin_recharge_modal').val();
    var amount = $('#amount_recharge_modal').val();
    if (parseFloat(amount) != 0) {
        var co = confirm('the amount is ' + amount + ' $ paid for ' + fname);
        if (co == true) {
            $.ajax({
                url: "apisfxn/recharge.php",
                data: {
                    'admin_id': admin_id,
                    'amount': amount,
                },
                type: 'post',
                complete: function(response) {
                    toastr.success("Amount added to Api balance.");
                    $('#dataTable').DataTable().ajax.reload();
                }
            });
        }

    } else {
        toastr.error('Amount cannot be zero !!');
    }
}


// function gotoTransactions(e, dt, node, config) {
//     var adata = dt.rows({
//         selected: true
//     });
//     var userID = adata.data()[0]['Id']
//     var win = window.open('transactions.php?CustomerID=' + userID, '_blank');
//     win.focus();
// }