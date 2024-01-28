$(document).ready(function() {


    $.ajax({
        url: "../backend/view/requestLog.php",
        type: 'POST',
        data: "id",
        success: function(response) {
            console.log(response);
            let json = JSON.parse(response);
            var my_columns = [];

            $.each(json[0], function(key, value) {
                var my_item = {};
                my_item.data = key;
                my_item.title = key;
                my_columns.push(my_item);
            });
            console.log(my_columns);


            var table = $('#dataTable').DataTable({
                responsive: false,
                search: false,
                data: json,
                "order": [
                    [7, "desc"]
                ],
                //dom: 'lfBrtip',
                //dom: '<"row"<"col-md-4"l><"col-md-4"f><"col-md-2"><"col-md-4"B>>rtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'csv',

                ],
                "columns": my_columns
            });
            table.buttons().container()
                .appendTo('#dataTable_wrapper .col-md-6:eq(0)');


        },
        beforeSend: function() {

        },
        error: function(res) {
            console.log(res);
        }
    });


    // $('#dataTable1').DataTable({


    //     responsive: true,
    //     data: dataSet,
    //     dom: 'Bfrtip',
    //     // dom: '<"row"<"col-md-2"l><"col-md-4"B><"col-md-2"f>>rtip',
    //     buttons: [
    //         'copy', 'excel', 'pdf', 'csv',
    //         //         // {
    //         //         //     text: 'Delete Report',
    //         //         //     className:"btn-success",
    //         //         //     action: function ( e, dt, node, config ) {
    //         //         //         location.href='myapi.php?action=delete_report';
    //         //         //     }
    //         //         // }
    //     ],
    //     "columns": my_columns
    // });



    // $('#dataTable1').DataTable({
    //     paging: false,
    //     searching: false,
    //     dom: "Bfrtip",
    //     responsive: true,
    //     data: dataSet,
    //     dom: '<"row"<"col-md-2"l><"col-md-4"B><"col-md-2"f>>rtip',
    //     // dom: '<"row"<"col-md-3"l><"col-md-5"B><"col-md-3"f>>rtip',
    //     buttons: [
    //         'copy', 'excel', 'pdf', 'csv',
    //         // {
    //         //     text: 'Delete Report',
    //         //     className:"btn-success",
    //         //     action: function ( e, dt, node, config ) {
    //         //         location.href='myapi.php?action=delete_report';
    //         //     }
    //         // }
    //     ],
    //     columns: [
    //         { title: "Name" },
    //         { title: "Position" },
    //         { title: "Office" },
    //         { title: "Extn." },
    //         { title: "Start date" },
    //         { title: "Salary" }
    //     ]

    // });

    //     },
    //     beforeSend: function() {

    //     },
    //     error: function(res) {
    //         console.log(res);
    //     }
    // });


});