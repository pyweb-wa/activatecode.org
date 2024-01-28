$(document).ready(function () {
    var id = $('#country_list').find(':selected')[0].value;
    getservicesAjax(id);
    getlogAjax();
    get_pending();

    // var id = $(country_list).find(':selected').value;
    // if (id !== undefined && id !== '') {
    // var id = $(country_list).find(':selected')[0].value;
    // getservicesAjax(id);
    //   } else {
    //     // Code to be executed if the length is zero or the variable is empty
    //     console.log("not numbers found");
    //   }

    //getservicesAjax(id);


});

function imgError(image) {
    image.onerror = "";
    image.src = "assets/img/placeholder.png";
    return true;
}

$('#country_list').change(function () {
    var id = $(this).find(':selected')[0].value;
    // get_carriers(id);
    getservicesAjax(id);


});

// $('#carrier_list').change(function() {
//     var country_id = $(country_list).find(':selected')[0].value;
//     var carrier_id = $(this).find(':selected')[0].value;
//     getservicesAjax(country_id, carrier_id);


// });

function get_carriers(country) {
    $.ajax({
        method: 'POST',
        url: 'clientfxn/get_carriers.php',
        dataType: 'json',
        data: { country: country },
        success: function (data) {
            var options = [];
            var option = "<option value='any' data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> Any Carrier\" ></option>"
            //data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> Any \">
            options.push(option);
            data.forEach(function (item) {
                var option = "<option value='" + item.carrier + "' data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> " + item.carrier + "\" ></option>"
                //data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> Any \">
                options.push(option);
            });
            $('#carrier_list').html(options);
            $('#carrier_list').selectpicker('refresh');

        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function get_pending() {
    setInterval(function () {
        $(".progress-bar").each(function () {
            //style_pending_rows($(this).closest('tr'));
            var value = $(this).attr('aria-valuenow');
            if (value < 1) {
                getlogAjax();
            } else {

                $(this).css('width', ((value / 6) - 1) + '%').attr('aria-valuenow', value - 6);
                $(this).find('.pending_progress').text(((value - 5)) + 'seconds left');
                var req_id = $(this).find('._request_id').attr('value');
                get_sms_code(req_id);
            }

        });
    }, 6000);
}

function getlogAjax() {
    $.ajax({
        method: 'POST',
        url: 'clientfxn/get_req_log.php',
        dataType: 'json',
        success: function (data) {
            //   alert(data);
            console.log("fromgetlog")
            $('#requests_history').bootstrapTable({
                data: data
            });
            $('#requests_history').bootstrapTable('hideLoading');
            $('#requests_history').bootstrapTable('load', data);

        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function detailFormatter(index, row) {
    var html = []
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>')
    })
    return html.join('')
}

function getservicesAjax(country, carrier = 'any') {
    /////////////////////////////////////////////////////////////////////////////////////
    ///////////  fill Table from APIs                      //////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////// 


    $.ajax({
        method: 'POST',
        url: 'clientfxn/get_app_list.php',
        dataType: 'json',
        data: { country: country },
        success: function (data) {
            if (data == null) {
                toastr.error("No Application Available !!! ");
            } else {
                $('#applist').bootstrapTable({
                    data: data,
                    search: true,
                    pagination: true,
                    pageSize: 5, // Number of rows to show per page
                    pageList: [10, 25, 50, 100], // Options for the number of rows per page
                    paginationPreText: 'Previous', // Text for the "previous" button
                    paginationNextText: 'Next',
                });
                $('#applist').bootstrapTable('hideLoading');
                $('#applist').bootstrapTable('load', data);
            }

        },
        error: function (e) {
            console.log(e.responseText);
        }
    });

    /////////////////////////////////////////////////////////////////////////////////////

};


function purchase(app, country, carrier,token) {
    console.log(app)
    toastr.info('Ordering New Number.');
    // http://127.0.0.1:81/sms/Api/out_interface.php?
    //  
    // appcode=fb  
    // &action=getnumber
    // &country=LB
    // &carrier=alfa
    $.ajax({
        method: 'GET',
        url: 'backend/out_interface.php',
        dataType: 'json',
        data: {
            action: 'getnumber',
            country: country,
            carrier: carrier,
            appcode: app,
            api_key: token
        },
        success: function (data) {
            if (data['Result'] !== null) {
                if (data['Result']['Balance']) {
                    blc = data['Result']['Balance'];
                    $("#balance_span").text('Balance ' + blc + "$");
                }
                if (data['Msg'].toLowerCase().includes("error")) {
                    toastr.error(data['Msg']);
                } else {
                    toastr.success(data['Msg']);
                }
                getlogAjax();
                var id = $('#country_list').find(':selected')[0].value;
                getservicesAjax(id);
            } else {
                toastr.error(data['Msg']);
            }
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function copy_to_clip() {
    /* Get the text field */
    var copyText = event.target.id
    setClipboard(copyText);
    toastr.info(copyText + ' Copied to Clipboard.');
}

function setClipboard(value) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}

function get_sms_code(request_id) {
    //out_interface.php?
    //api_key=25f9e794323b453885f5181f1b624d0b&
    //id=3&
    //action=getcode
    $.ajax({
        method: 'POST',
        url: 'backend/out_interface.php',
        dataType: 'json',
        data: {
            action: 'getcode',
            id: request_id
        },
        success: function (data) {
            console.log(data)
            //{"ResponseCode":0,"Msg":"OK","Result":{"SMS":"Your Zalo code is 65474","Code":"65474"}}
            if (data['Result']) {
                var Code = data['Result']['Code'];
                console.log(Code)
                if (Code)
                    //$("#balance_span").text('Balance ' + blc + "$");
                    getlogAjax();
            }
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function cancel_code_request(request_id) {
    //out_interface.php?
    //action=expired&id=3
    toastr.info('Canceling Request.');
    $.ajax({
        method: 'POST',
        url: 'backend/out_interface.php',
        dataType: 'json',
        data: {
            action: 'expired',
            id: request_id
        },
        success: function (data) {
            //{"ResponseCode":0,"Msg":"OK","Result":{"flag":"OK","refund":0,"Msg":"Already expired before"}}

            var flag = data['Result']['flag'];
            var refund = data['Result']['refund'];
            var msg = data['Result']['Msg'];
            var balance = data['Result']['balance'];
            if (flag == "OK") {
                toastr.success('Order Cancelled successfully. <br/>Refund ammount is ' + refund + '$');
                getlogAjax();
                $("#balance_span").text('Balance ' + balance + " $");

            } else {
                toastr.error('Order Cancelling ERROR. <br/>' + msg + '');

            }
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

function style_pending_rows(myRow) {

    myRow.addClass('pending_animation');
    setTimeout(function () {
        myRow.removeClass('pending_animation');

    }, 1000);



}