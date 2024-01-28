function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31 &&
        (charCode < 48 || charCode > 57))
        return false;
    return true;
}
$(document).ready(function () {

    get_user_balance();
    $.ajax({
        url: "usersfxn/get_users.php",
        //dataSrc: '',
        dataType: 'json',
        type: 'get',
        success: function (response) {
            var len = response.length;
            if (len == 0) {
                $('#new_pass_submit').disabled = false;
                $('#customerRecieveBtn').disabled = false;
            } else {
                $("#user_select").empty();
                for (var i = 0; i < len; i++) {
                    var id = response[i]['Id'];
                    var name = response[i]['name'];
                    $("#user_select").append("<option value='" + id + "'>" + name + "</option>");

                };

                $('#user_select').selectpicker("refresh");
                var id = $('#user_select').find(':selected').value;

                if (id !== undefined && id !== '') {
                    var id = $('#user_select').find(':selected')[0].value;

                    get_user_details(id);
                    getApisList(id); //from permissions js file 
                } else {
                    // Code to be executed if the length is zero or the variable is empty
                    console.log("not data");
                }
                var id = $('#user_select').find(':selected')[0].value;
                console.log(id)
                
                if(id){
                    get_user_details(id);
                    getApisList(id); //from permissions js file 

                }
            }

        }
    });


});

function get_user_balance(){
    $.ajax({
        url: "usersfxn/get_balance.php",  
        dataType: 'json',
        data: {
            'action': 'get_balance'
        },
        type: 'post',
        success: function (response) {
            console.log('balance:',response);
            $("#user_blnc_view").text(response);
        }
    });
}
function get_user_details(user_id) {
    $.ajax({
        url: "usersfxn/billing_actions.php",
        dataType: 'json',
        data: {
            'user_id': user_id,
            'get': 'balance'
        },
        type: 'post',
        complete: function (response) {
            //toastr.success(response);
            var returnedData = response['responseJSON'];
            $('#current_balance').val(returnedData['Balance']);

        }
    });
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
            url: 'usersfxn/billing_actions.php',
            data: {
                'user_id': user_id,
                'get': 'history'
            },
            dataSrc: '',

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });
};

$('#user_select').change(function () {
    var id = $(this).find(':selected')[0].value;
    get_user_details(id);
});



function submit() {

    var user_id = $('#user_select').find(':selected')[0].value;
    var username = $('#user_select').find(':selected')[0].text;
    var amount = $('#amount').val();
    var gift = $('#gift').val();
    if (parseFloat(amount) != 0) {
        var co = confirm('the amount is ' + amount + ' paid by ' + username);
        if (co == true) {
            $.ajax({
                url: "usersfxn/billing_actions.php",
                data: {
                    'user_id': user_id,
                    'amount': amount,
                    'gift': gift
                },
                type: 'post',
                complete: function (response) {
                    console.log(response);
                    if(response.responseText == "NoBalance"){
                        toastr.error("You Don't Have enough Balance.");
                        console.log("You Don't Have enough Balance.");
                    }
                    else if(response.responseText == "Success"){
                        toastr.success("Amount added to user balance.");
                        console.log("Amount added to user balance.");
                    }
                    else{
                        toastr.error("Unkown Error when adding balance");
                        console.log("Unkown Error when adding balance");
                    }
                   
                    $('#amount').val('0');
                    $('#gift').val('0');
                    get_user_details(user_id);
                    // location.reload();
                }
            });
        }
    } else {
        toastr.error('Amount cannot be zero !!');

    }

}


function pass_submit() {

    var user_id = $('#user_select').find(':selected')[0].value;
    var new_pass = $('#new_pass').val();
    if (new_pass.length > 7) {
        $.ajax({
            url: "usersfxn/password_reset.php",
            data: {
                'user_id': user_id,
                'new_pass': new_pass
            },
            type: 'post',
            complete: function (response) {
                toastr.success("Password Changed Successfully.");
            }
        });
    } else {
        toastr.error('Password is Too Short !! <br>Try another one.');
    }

}