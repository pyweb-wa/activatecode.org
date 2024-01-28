$(document).ready(function () {

    /////////////////////////////////////////////////////////////////////////////////////
    ///////////  fill select options from APIs             //////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    $('#nav_item_app').addClass("active");
    $.ajax({
        url: "apisfxn/get_api_list.php",
        dataType: 'json',
        type: 'get',
        success: function (response) {
            var len = response.length;
            $("#api_list").empty();
            for (var i = 0; i < len; i++) {
                var id = response[i]['Id_Api'];
                var name = response[i]['Name'];
                $("#api_list").append("<option value='" + id + "'>" + name + "</option>");

            };
            var id = $('#api_list').find(':selected')[0].value;
            getservicesAjax(id);
        }
    });


});

function getservicesAjax(Id_Api) {
    /////////////////////////////////////////////////////////////////////////////////////
    ///////////  fill Table from APIs                      //////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    var columnDefs = [{
        data: "Id_Service_Api",
        title: "Id",
        type: "readonly"
    },
    {
        data: "Name",
        title: "Name"
    },
    {
        data: "code",
        title: "code"
    },
    {
        data: "country",
        title: "country"
    },
    {
        data: "description",
        title: "description"

    },
    {
        data: "price_in",
        title: "price_in"
    },
    {
        data: "price_out",
        title: "price_out",
    },
    {
        data: "carrier",
        title: "carrier"
    },
    {
        data: "service_of_api",
        title: "Api code"
    }
    ];

    // var myTable = $('#dataTable');
    // if ($.fn.dataTable.isDataTable('#dataTable')) {
    //     myTable.destroy();
    // }
    var myTable = $('#dataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        "order": [
            [0, "desc"]
        ],
        pageLength: 50,
        // dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        // "sPaginationType": "full_numbers",
        ajax: {
            type: 'GET',
            url: 'servicesfxn/getservices.php',
            data: {
                'Id_Api': Id_Api
            },
            // dataSrc: '',

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
        }
        ],
        onAddRow: function (datatable, rowdata, success, error) {
            var api_id = $('#api_list').find(':selected')[0].value;
            rowdata['api_id'] = api_id;
            $.ajax({
                // a tipycal url would be / with type='PUT'
                url: "servicesfxn/addsrv.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onDeleteRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='DELETE'
                url: "servicesfxn/deletesrv.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onEditRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='POST'
                url: "servicesfxn/editsrv.php",
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        }
    });

    /////////////////////////////////////////////////////////////////////////////////////

};


$('#api_list').change(function () {
    var id = $(this).find(':selected')[0].value;
    getservicesAjax(id);
});