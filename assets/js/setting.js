$(document).ready(function () {

    get_token();
    set_url_callback();

    document.getElementById("customSwitches").addEventListener("change", function () {
        SwitchChaneg(this.checked);    
    });

});


function SwitchChaneg(stat) {
    console.log(stat);
    $('#call_back_url').prop('disabled', !stat);
    $.ajax({
        method: 'POST',
        url: 'clientfxn/setting.php',
        dataType: 'json',
        data: { set_callback_state: stat},
        success: function (data) {
            console.log(data)
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });    
}

function get_token() {
    $.ajax({
        method: 'POST',
        url: 'clientfxn/setting.php',
        dataType: 'json',
        data: { get_token: "true" },
        success: function (data) {
            var access_token = data['access_Token'];
            $('#access_token').val(access_token)
            console.log(data)
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}


function renew_token() {
    var dd = confirm("Are you sure to renew token?")
    if (dd) {
        $.ajax({
            method: 'POST',
            url: 'clientfxn/setting.php',
            dataType: 'json',
            data: { renew_token: "true" },
            success: function (data) {
                console.log(data)
                $('#access_token').val(data["token"])
                toastr.success("Token  Changed Successfully.");
                // location.reload()
            },
            error: function (e) {
                toastr.error(e);
                console.log(e.responseText);
            }
        });
    }
}

function set_url_callback() {
    $.ajax({
        method: 'POST',
        url: 'clientfxn/setting.php',
        dataType: 'json',
        data: { get_call_back_url: "true" },
        success: function (data) {
            if (data['error'] == "false") {                
                $('#call_back_url').val(decodeURIComponent(data['callback_url']));
                let isChecked = data['callback_status'] === "1";
                $('#customSwitches').prop('checked',isChecked );
                $('#call_back_url').prop('disabled', !isChecked);
            }
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function test_url() {
    var encodedcbUrl = encodeURIComponent($('#call_back_url').val());
    let isChecked = $('#customSwitches').prop('checked');
    if (isChecked){
        $.ajax({
            method: 'POST',
            url: 'clientfxn/setting.php',
            dataType: 'json',
            data: { call_back_url: encodedcbUrl },
            success: function (data) {
                if (data['error'] == "false") {
                    toastr.success(data['msg']);
                } else {
                    toastr.error(data['msg']);
                }
            },
            error: function (e) {
                toastr.error(e)
                console.log(e.responseText);
            }
        });
    }
}


function clear_url() {
    $('#call_back_url').val("")
    $.ajax({
        method: 'POST',
        url: 'clientfxn/setting.php',
        dataType: 'json',
        data: { clear_call_back_url: "true" },
        success: function (data) {
            toastr.success("cleared");
        },
        error: function (e) {
            toastr.error(e)
            console.log(e.responseText);
        }
    });
}


function change_password() {
    var old_pass = $('#old_pass').val()
    var new_pass = $('#new_pass').val()
    var confirm_pass = $('#confirm_pass').val()
    if (new_pass != confirm_pass) {
        $('#msg').val("password not equal")

        toastr.error("password not equal");
        throw new Error("password  Changed error.");
    }
    //#TODO password validation length and error
    $.ajax({
        method: 'POST',
        url: 'clientfxn/setting.php',
        dataType: 'json',
        data: { change_password: "true", old_pass: old_pass, new_pass: new_pass },
        success: function (data) {
            if (data['msg']) {
                if (data['msg'] == "wrong_pass") {
                    toastr.error("Wrong Password !!");
                    throw new Error("Wrong Password !!");

                } else if (data['msg'] == "success") {
                    toastr.success("password  Changed Successfully.<br>logout for renew session");
                    throw new Error("password  Changed Successfully.");

                }
            }
            console.log(data)

            toastr.error("Error Happened !!");


            // location.reload()

        },
        error: function (e) {
            toastr.error(e)

            console.log(e.responseText);
        }
    });
}