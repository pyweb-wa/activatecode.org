 $(document).ready(function() {
    $('#nav_item_customer_prop').addClass("active");

    // var id = $('#user_select').find(':selected')[0].value;
    // console.log(id)
    // if(id){
    //     getApisList(id);

    // }


//      $.ajax({
//          url: "usersfxn/get_users.php",
//          //dataSrc: '',
//          dataType: 'json',
//          type: 'get',
//          success: function(response) {
//              var len = response.length;
//              $("#user_select").empty();
//              for (var i = 0; i < len; i++) {
//                  var id = response[i]['Id'];
//                  var name = response[i]['name'];
//                  $("#user_select").append("<option value='" + id + "'>" + name + "</option>");

//              };
//              var id = $('#user_select').find(':selected')[0].value;
//              getApisList(id);
//          }
//      });


 });


function getApisList(user_id) {
    /////////////////////////////////////////////////////////////////////////////////////
    ///////////  fill Table from APIs                      //////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    var columnDefs = [{
            data: "id",
            title: "Id",
            type: "readonly"
        },
        {
            data: "Name",
            title: "Name"
        },
        {
            data: "checked",
            title: "Allow"
        }
    ];

    var myTable = $('#dataTable');
    // if ($.fn.dataTable.isDataTable('#dataTable')) {
    //     myTable.destroy();
    // }
    myTable = $('#dataTable').DataTable({
        destroy: true,
        "sPaginationType": "full_numbers",
        ajax: {
            type: 'post',
            url: 'usersfxn/get_allowedApis.php',
            data: {
                'user_id': user_id
            },
            dataSrc: '',

        },
        columns: columnDefs,
        //  dom: 'Bfrtip', // Needs button container

        responsive: true,

    });

    /////////////////////////////////////////////////////////////////////////////////////

};

$('#user_select').change(function() {
    var id = $(this).find(':selected')[0].value;
    getApisList(id);
});

function check(api_id) {

    var aid = 'uncheck(' + api_id + ');'
    var btn_id = event.target.id;
    $("#" + btn_id).removeClass('btn-danger').addClass('btn-success');
    $("#" + btn_id).removeAttr('onclick');
    $("#" + btn_id).attr('onClick', aid);
    var user_id = $('#user_select').find(':selected')[0].value;
    $.ajax({
        url: "usersfxn/setpermission.php",
        data: {
            'user_id': user_id,
            'api_id': api_id,
            'check': 1
        },
        type: 'post',
        complete: function(response) {
            console.log(response);
            //getApisList(user_id);
        }
    });
}

function uncheck(api_id) {

    var aid = 'check(' + api_id + ');'
    var btn_id = event.target.id;
    $("#" + btn_id).addClass('btn-danger').removeClass('btn-success');
    $("#" + btn_id).removeAttr('onclick');
    $("#" + btn_id).attr('onClick', aid);

    var user_id = $('#user_select').find(':selected')[0].value;
    $.ajax({
        url: "usersfxn/setpermission.php",
        data: {
            'user_id': user_id,
            'api_id': api_id,
            'check': 2
        },

        type: 'post',
        complete: function(response) {
            console.log(response);
            //getApisList(user_id);
        }
    });
}