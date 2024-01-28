let rechtable;
$(document).ready(function () {
    getUsers();
    getcountries();
    getApiList();

    $('#user_select').on('change', function (e) {
        var selected_value = $(this).val();
        get_user_details(selected_value)
        //getusercountries()
        //$('#user_select').selectpicker("refresh");
    });

    $('#user_select_his').on('change', function (e) {
        var selected_value = $(this).val();
        get_user_recharge_his(selected_value) 
    });
    // $('#Select_user_view').on('change', function (e) {
    //     var user = $('#Select_user_view').val() || [];
    //     if(user.length>0){
    //         get_user_country_api(user);
    //     }
    // });

    rechtable=  $('#myTable').DataTable({
        "pageLength": 100,
        "lengthMenu": [ 10, 50, 100, 200, 500],
        "ajax": {
            "url": "servicesfxn/usersPermissions.php",
            "data": {
                "action": "get_user_country_api"
              },
            "type": "POST"
        },
        "searching": true,
        "columns": [
          { "data": "user" , "title": "Name"},
          { "data": "country", "title": "Countries","render": function (data, type, row) { 
            var values = data.split(',');
            var output = '';
            for (var i = 0; i < values.length; i++) {
              output += '<div>' + values[i].trim() + '</div>';
            }
            return output;
          } },
          { "data": "api" , "title": "Api","render": function (data, type, row) { 
            var values = data.split(',');
            var output = '';
            for (var i = 0; i < values.length; i++) {
              output += '<div>' + values[i].trim() + '</div>';
            }
            return output;
          }}
        ]
    });

});

function get_user_country_api(user){
    var formData = new FormData();
    formData.append('action', 'get_user_country_api');
    formData.append('user_id',user);
    $.ajax({
        url: "servicesfxn/usersPermissions.php",
        type: 'POST',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            console.log(response)
            // for (var i = 0; i < response.length; i++) {
            // }
        },
        error: function (request, status, error) {
            console.log(error);
        }
    });
}

function getusercountries() {
    // $('#countries').empty();
    //return
    var user = $('#users').val()
    console.log(user)


    $.ajax({
        url: "servicesfxn/usersPermissions.php",
        // dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getusercountries',
            'user_id': user
        },
        success: function (response) {
            response = JSON.parse(response);

            $("#countries").val(response)
            $("#countries").val(response).change();
            //    $("#countries").val(response).change(function() {
            //     var selectedOption = $(this).find('option:selected');
            //     $(this).prepend(selectedOption);
            //   });
            console.log(response)
        }
    });
    $('input[type="search"]').val('');
    // $('input[type="search"]').on('keypress', function(event) {
    //     if (event.keyCode === 13) {
    //         console.log("clicke")
    //       $(this).val('');
    //       $('input[type="search"]').val('');
    //     }
    //   });

    // var selectedOption = $('#countries option:selected');
    // selectedOption.insertBefore($('#countries option:first'));

}

function clear_List(select_id){    
    console.log("clearrrrr",select_id);
    var select = $('#' + select_id); 
    var selectize = select[0].selectize; 
    selectize.clear();
}

function getUsers() {
    $.ajax({
        url: "servicesfxn/usersPermissions.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getUsers'
        },
        success: function (response) {
            var len = response.length;
            $("#users").empty();
            $("#user_select").empty();
            $("#user_select_his").empty();
            // $("#Select_user_view").empty();
            for (var i = 0; i < len; i++) {
                var username = response[i]['name'];
                var id = response[i]['id'];
                $("#users").append("<option value='" + id + "'  >" + username + "</option>");
                $("#user_select").append("<option value='" + id + "'>" + username + "</option>");
                $("#user_select_his").append("<option value='" + id + "'>" + username + "</option>");
                // $("#Select_user_view").append("<option value='" + id + "'>" + username + "</option>");
            };
            $('#user_select').selectpicker("val", ""); 
            $('#user_select').selectpicker("refresh");

            $('#user_select_his').selectpicker("val", ""); 
            $('#user_select_his').selectpicker("refresh");

            // $('#Select_user_view').selectize({
            //     plugins: ['remove_button'],
            //     create: false, 
            //     dropdownParent: 'body', // Set the parent element of the dropdown menu to the body
            //     maxOptions: 10, // Set the maximum number of visible options
            //     dropdownHeight: 200,
            //     onDropdownOpen: function($dropdown) {
            //         // Handle click and slide behavior
            //         $dropdown.on('mousedown', function(e) {
            //             e.preventDefault();
            //             var $target = $(e.target);
            //             if ($target.hasClass('option')) {
            //                 var selectize = this.selectize;
            //                 selectize.setValue($target.attr('data-value'), true);
            //                 $(document).on('mousemove.selectizeClickSlide', function(e) {
            //                     if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
            //                         selectize.setValue($(e.target).attr('data-value'), true);
            //                     }
            //                 });
            //             }
            //         });
            //     },
            //     onDropdownClose: function() {
            //         // Remove click and slide behavior
            //         $(document).off('mousemove.selectizeClickSlide');
            //     },
            //     onInitialize: function() {
            //       this.$control.css({
            //         'height': '40px', /* Set the height of the input element to 40 pixels */
            //         'max-height': '100px'
            //       });
            //     },
            // });
            
            $('#users').selectize({
                plugins: ['remove_button'],
                create: false,
                onDropdownOpen: function($dropdown) {
                    // Handle click and slide behavior
                    $dropdown.on('mousedown', function(e) {
                        e.preventDefault();
                        var $target = $(e.target);
                        if ($target.hasClass('option')) {
                            var selectize = this.selectize;
                            selectize.setValue($target.attr('data-value'), true);
                            $(document).on('mousemove.selectizeClickSlide', function(e) {
                                if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
                                    selectize.setValue($(e.target).attr('data-value'), true);
                                }
                            });
                        }
                    });
                },
                onDropdownClose: function() {
                    // Remove click and slide behavior
                    $(document).off('mousemove.selectizeClickSlide');
                }
            });
            
        }
    });



}

function getApiList() {
    $.ajax({
        url: "servicesfxn/usersPermissions.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'get_api_lis'
        },
        success: function (response) {
            var len = response.length;
            $("#api_list").empty();
            for (var i = 0; i < len; i++) {
                var api_id= response[i]['Id_Api'];
                var api_name = response[i]['Name']; 
                $("#api_list").append("<option value='" + api_id + "'  >" +api_name + "</option>");
            };
            $('#api_list').selectize({
                plugins: ['remove_button'],
                create: false,
                onDropdownOpen: function($dropdown) {
                    // Handle click and slide behavior
                    $dropdown.on('mousedown', function(e) {
                        e.preventDefault();
                        var $target = $(e.target);
                        if ($target.hasClass('option')) {
                            var selectize = this.selectize;
                            selectize.setValue($target.attr('data-value'), true);
                            $(document).on('mousemove.selectizeClickSlide', function(e) {
                                if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
                                    selectize.setValue($(e.target).attr('data-value'), true);
                                }
                            });
                        }
                    });
                },
                onDropdownClose: function() {
                    // Remove click and slide behavior
                    $(document).off('mousemove.selectizeClickSlide');
                }
            });
            clear_List('api_list');
        }
    });
}

function getcountries() {
    $.ajax({
        url: "servicesfxn/usersPermissions.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getcountries'
        },
        success: function (response) {
            var len = response.length;
            $("#countries_list").empty();
            for (var i = 0; i < len; i++) {
                var country = response[i]['country'];
                var country_code = response[i]['country_code'];
                var id = response[i]['id'];
                $("#countries_list").append("<option value='" + id + "'  >" + country + "-" + country_code + "</option>");
            };
            $('#countries_list').selectize({
                plugins: ['remove_button'],
                create: false,
                onDropdownOpen: function($dropdown) {
                    // Handle click and slide behavior
                    $dropdown.on('mousedown', function(e) {
                        e.preventDefault();
                        var $target = $(e.target);
                        if ($target.hasClass('option')) {
                            var selectize = this.selectize;
                            selectize.setValue($target.attr('data-value'), true);
                            $(document).on('mousemove.selectizeClickSlide', function(e) {
                                if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
                                    selectize.setValue($(e.target).attr('data-value'), true);
                                }
                            });
                        }
                    });
                },
                onDropdownClose: function() {
                    // Remove click and slide behavior
                    $(document).off('mousemove.selectizeClickSlide');
                }
            });
        }
    });
}

function update() {
    var user = $('#users').val()|| [];
    if(user.length>0){ 
        var countries = $('#countries_list').val() || [];
        var api_list = $('#api_list').val() || [];
        var formData = new FormData();
        formData.append('action', 'update');
        formData.append('user_id', user);
        formData.append('countries', countries);
        formData.append('api_list', api_list); 
        var formDatadelete = new FormData();
        formDatadelete.append('action', 'clear');
        formDatadelete.append('user_id', user);

                $.ajax({
                    url: "servicesfxn/usersPermissions.php",
                    type: "POST",
                    cache: false,
                    data: formDatadelete,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        try {
                            $.ajax({
                                url: "servicesfxn/usersPermissions.php",
                                type: "POST",
                                cache: false,
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function (response) {
                                    try {
                                        rechtable.ajax.reload();
                                        response = jQuery.parseJSON(response)
                                        if (response["status"]) {
                                            if (response["status"] == "OK") {
                                                DevExpress.ui.notify({
                                                    message: response["msg"],
                                                    height: 45,
                                                    width: 300,
                                                    minWidth: 150,
                                                    type: 'success',
                                                    position: {
                                                        my: "center bottom",
                                                        at: "center bottom",
                                                        of: window
                                                    },
                                                    displayTime: 2000,
                                                    animation: {
                                                    show: {
                                                        type: 'fade', duration: 400, from: 0, to: 1,
                                                    },
                                                    hide: { type: 'fade', duration: 40, to: 0 },
                                                    },
                                                });

                                                clear_List('api_list');
                                                clear_List('users');
                                                clear_List('countries_list');
                                            
                                            }
                                        }
                                    } catch (err) {
                                        alert("error happened " + err);
                                    }
                                },
                                error: function (request, status, error) {
                                    alert(request.responseText);
                                }
                            });
                        } catch (err) {
                            alert("error happened " + err);
                        }
                    },
                    error: function (request, status, error) {
                        alert(error);
                    }
                });
    



       
    }
}

function clearUserPermissions() {
    console.log("clicked clear");
    var user = $('#users').val() || [];
    //var countries = $('#countries').val() || [];
    console.log('clearUserPermissions',user);
    //throw new Error();
    var formData = new FormData();
    formData.append('action', 'clear');
    formData.append('user_id', user);
    if(user.length>0){
        var result = DevExpress.ui.dialog.confirm("<i>Are you sure you want to remove all api and countries related to this users?</i>", "Confirm Reset");
        result.done(function(dialogResult) {
            if (dialogResult){
                $.ajax({
                    url: "servicesfxn/usersPermissions.php",
                    type: "POST",
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        try {
                            console.log('response',response);
                            response = jQuery.parseJSON(response)
                            if (response["status"]) {
                                if (response["status"] == "OK") { 
                                    DevExpress.ui.notify({
                                        message: response["msg"],
                                        height: 45,
                                        width: 300,
                                        minWidth: 150,
                                        type: 'success',
                                        position: {
                                            my: "center bottom",
                                            at: "center bottom",
                                            of: window
                                        },
                                        displayTime: 2000,
                                        animation: {
                                          show: {
                                            type: 'fade', duration: 400, from: 0, to: 1,
                                          },
                                          hide: { type: 'fade', duration: 40, to: 0 },
                                        },
                                      });
                                      
                                      clear_List('api_list');
                                      clear_List('users');
                                      clear_List('countries_list');
                                      rechtable.ajax.reload();
                                }
                            }
                        } catch (err) {
                            alert("error happened " + err);
                        }
                    },
                    error: function (request, status, error) {
                        alert(error);
                    }
                });
            }else { 
            }
        });
    }
        

}

function SaveCustomerSettings(){
    var user_idobj = $('#user_select').find(':selected')[0];
    if (user_idobj){ 
        var user_id = user_idobj.value;
        var username = $('#user_select').find(':selected')[0].text;
        var amount = $('#amount').val().trim();
        var gift = $('#gift').val().trim();
        var new_pass = $('#new_pass').val().trim();

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
                    complete: function(response) {
                        toastr.success("Amount added to user balance.");
                        $('#amount').val('0');
                        $('#gift').val('0');
                        get_user_details(user_id);
                    }
                });
            }
        } 
        
        if(new_pass === null || new_pass === '') {

        }
        else{
            if (new_pass.length > 7) {
                $.ajax({
                    url: "usersfxn/password_reset.php",
                    data: {
                        'user_id': user_id,
                        'new_pass': new_pass
                    },
                    type: 'post',
                    complete: function(response) {
                        toastr.success("Password Changed Successfully.");
                        $('#new_pass').val("");
                    }
                });
            } else {
                toastr.error('Password is Too Short <small>(7 charachters at least)</small>!! <br>Try another one.');
            }
        }
    }
}

function get_user_recharge_his(user){
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

    var myTable = $('#table_cus_recharge');
    myTable = $('#table_cus_recharge').DataTable({
        destroy: true,
        "sPaginationType": "full_numbers",
        "order": [],
        ajax: {
            type: 'post',
            url: 'usersfxn/billing_actions.php',
            data: {
                'user_id': user,
                'get': 'history'
            },
            dataSrc: '',

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

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
        complete: function(response) {
            //toastr.success(response);
            var returnedData = response['responseJSON'];
            $('#current_balance').val(returnedData['Balance']);

        }
    });
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31 &&
        (charCode < 48 || charCode > 57))
        return false;
    return true;
}

