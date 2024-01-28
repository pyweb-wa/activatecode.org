$(document).ready(function() {

    $('#nav_item_sms_supplier').addClass("active");
    $("#myBtn").click(function() {
        $("#myModal").modal();
    });
    var columnDefs = [{
            data: "Id_Api",
            title: "Id",
            type: "readonly"
        },
        {
            data: "Name",
            title: "Name"
        },
        {
            data: "Description",
            title: "Description"
        },
        {
            data: "account_balance",
            title: "Balance",
            type: "readonly"
        },
        {
            data: "Access_Token",
            title: "Access Token"
        },
        {
            data: "Refresh_Token",
            title: "Refresh_Token"

        },
        {
            data: "Valid",
            title: "token valid",
            type: "select",
            options: ["true", "false"]
        },
        // {
        //     data: "ExpiryDate",
        //     title: "ExpiryDate",
        // },

    ];

    var myTable;

    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "apisfxn/get_api_list.php",
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
                text: 'Recharge',
                name: 'recharge', // do not change name
                action: function(e, dt, node, config) {
                    showRecharge(e, dt, node, config)
                },

            }, {
                extend: 'selected', // Bind to Selected row
                text: 'Accounting',
                name: 'Accounting', // do not change name
                action: function(e, dt, node, config) {
                    gotoTransactions(e, dt, node, config)
                },
            },
            {
                text: 'Refresh',
                name: 'refresh' // do not change name
            }
        ],
        onAddRow: function(datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be / with type='PUT'
                url: "apisfxn/addapi.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onDeleteRow: function(datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='DELETE'
                url: "apisfxn/deleteapi.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onEditRow: function(datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='POST'
                url: "apisfxn/editapi.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        }
    });


});

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31 &&
        (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function showRecharge(e, dt, node, config) {
    var adata = dt.rows({
        selected: true
    });
    var fapi_id = adata.data()[0]['Id_Api']
    var fapi_name = adata.data()[0]['Name']
    var fapi_bal = adata.data()[0]['account_balance']
        // console.log(JSON.stringify(adata.data()[0]))
    $('#api_id_recharge_modal').val(fapi_id)
    $('#api_recharge_modal').val(fapi_name) //api_bal_recharge_modal
    $('#api_bal_recharge_modal').val(fapi_bal) //api_bal_recharge_modal 
    $("#myModal").modal();
}

function insert_recharge() {
    var fapi_id = $('#api_id_recharge_modal').val();
    var fname = $('#api_recharge_modal').val();
    var amount = $('#amount_recharge_modal').val();
    if (parseFloat(amount) != 0) {
        var co = confirm('the amount is ' + amount + ' $ paid for ' + fname);
        if (co == true) {
            $.ajax({
                url: "apisfxn/recharge.php",
                data: {
                    'fapi_id': fapi_id,
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

function gotoTransactions(e, dt, node, config) {
    var adata = dt.rows({
        selected: true
    });
    var apiID = adata.data()[0]['Id_Api']
    var win = window.open('transactions.php?apiID=' + apiID, '_blank');
    win.focus();
}