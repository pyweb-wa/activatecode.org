$(document).ready(function() {


});



function change_password() {
    console.log("start");

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
        url: 'servicesfxn/setting.php',
        dataType: 'json',
        data: { change_password: "true", old_pass: old_pass, new_pass: new_pass },
        success: function(data) {
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
        error: function(e) {
            toastr.error(e)

            console.log(e.responseText);
        }
    });
}