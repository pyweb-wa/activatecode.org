$(document).ready(function () {
    $('#nav_item_price').addClass("active");

    var columnDefs = [{
        data: "Name",
        title: "App"
    },
    {
        data: "price_out",
        title: "price"
    }
        ,
    {
        data: "country_name",
        title: "Country"
    }
    ];
    var myTable;
    var appselectize;
    var countryselectize;
    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: "servicesfxn/pricing_api.php",
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            data: { 'action': 'getallpricing' },
            type: "POST",
            dataSrc: ''
        },
        columns: columnDefs,
        // dom: 'Bfrtip', // Needs button container
        select: 'single',
        responsive: true,
        altEditor: true // Enable altEditor
    });






    getAppList();
    getcountryList();
    //  $.ajax({
    //      url: "servicesfxn/pricing_api.php",
    //      dataType: 'json',
    //      type: 'POST',
    //      data: {
    //          'action': 'getapplist'
    //      },
    //      success: function(response) {
    //          var len = response.length;
    //          $("#app_list").empty();
    //          for (var i = 0; i < len; i++) {
    //              var name = response[i]['Name'];
    //              var price = response[i]['price_out'];
    //              $("#app_list").append("<option value='" + price + "'  >" + name + "</option>");
    //          };
    //          $('#app_list').selectpicker("refresh");
    //          load_price()
    //      }
    //  });

    //  $.ajax({
    //     url: "servicesfxn/pricing_api.php",
    //     dataType: 'json',
    //     type: 'POST',
    //     data: {
    //         'action': 'getcountrylist'
    //     },
    //     success: function(response) {
    //         var len = response.length;
    //         $("#country_list").empty();
    //         for (var i = 0; i < len; i++) {
    //             var name = response[i]['country'];
    //             var price = response[i]['country_char'];
    //             $("#country_list").append("<option value='" + price + "'  >" + name + "</option>");
    //         };
    //         $('#country_list').selectpicker("refresh");
    //         load_price()
    //     }
    // });

    //  $('#app_list').on('change', function(e) {
    //      load_price()
    //  });
    //  $('#country_list').on('change', function(e) {
    //     load_price()
    // });
});


function getAppList() {
    $.ajax({
        url: "servicesfxn/pricing_api.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getapplist'
        },
        success: function (response) {
            var len = response.length;
            $("#app_list").empty();
            for (var i = 0; i < len; i++) {
                var name = response[i]['Name'];
                $("#app_list").append("<option value='" + name + "'  >" + name + "</option>");
            };
            appselectize = $('#app_list').selectize({
                plugins: ['remove_button'],
                create: false,
                onDropdownOpen: function ($dropdown) {
                    // Handle click and slide behavior
                    $dropdown.on('mousedown', function (e) {
                        e.preventDefault();
                        var $target = $(e.target);
                        if ($target.hasClass('option')) {
                            var selectize = this.selectize;
                            selectize.setValue($target.attr('data-value'), true);
                            $(document).on('mousemove.selectizeClickSlide', function (e) {
                                if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
                                    selectize.setValue($(e.target).attr('data-value'), true);
                                }
                            });
                        }
                    });
                },
                onDropdownClose: function () {
                    // Remove click and slide behavior
                    $(document).off('mousemove.selectizeClickSlide');
                }
            });
            clear_List('app_list');
        }
    });
}

function getcountryList() {
    $.ajax({
        url: "servicesfxn/pricing_api.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getcountrylist'
        },
        success: function (response) {
            var len = response.length;
            var arr = [];
            $("#country_list").empty();
            for (var i = 0; i < len; i++) {
                var name = response[i]['country'];
                var code = response[i]['country_char'];
                // $("#country_list").append("<option value='" + code + "'  >" + name + "</option>");
                arr.push({
                    code: code,
                    name: name
                });
            };
            // $('#select-id')[0].selectize
            countryselectize = $('#country_list').selectize({
                plugins: ['remove_button'],
                create: false,
                valueField: "code",
                labelField: "name",
                searchField: ["name"],
                options: arr,
                onDropdownOpen: function ($dropdown) {
                    // Handle click and slide behavior
                    $dropdown.on('mousedown', function (e) {
                        e.preventDefault();
                        var $target = $(e.target);
                        if ($target.hasClass('option')) {
                            var selectize = this.selectize;
                            selectize.setValue($target.attr('data-value'), true);
                            $(document).on('mousemove.selectizeClickSlide', function (e) {
                                if ($(e.target).hasClass('option') && selectize.$activeOption[0] != e.target) {
                                    selectize.setValue($(e.target).attr('data-value'), true);
                                }
                            });
                        }
                    });
                },
                onDropdownClose: function () {
                    // Remove click and slide behavior
                    $(document).off('mousemove.selectizeClickSlide');
                }
            });
            clear_List('country_list');
        }
    });
}




function load_price() {
    var price = $('#app_list').find(':selected')[0].value;
    $('#price').val(price)
}

function update_price() {

    var country_list = $('#country_list').val() || [];
    var app_list = $('#app_list').val() || [];
    var price = $('#price').val();
    var formData = new FormData();
    formData.append('action', 'update_price');
    formData.append('app_list', app_list);
    formData.append('country_list', country_list);
    formData.append('price', price);
    $.ajax({
        url: "servicesfxn/pricing_api.php",
        type: "POST",
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            try {
                response = jQuery.parseJSON(response)
                if (response["msg"]) {
                    if (response["msg"] == "OK") {
                        toastr.success("update succeeded ");
                        location.reload();
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

}

function clear_List(select_id) {
    var select = $('#' + select_id);
    var selectize = select[0].selectize;
    selectize.clear();
}
function selectall(select_id) {
    var select = $('#' + select_id);
    var selectize = select[0].selectize;
    selectize.setValue(Object.keys(selectize.options));
}