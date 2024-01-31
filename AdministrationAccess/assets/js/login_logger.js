var table;
$(document).ready(function() {

    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText, inst) {
            var selectedDate = $(this).val();
            console.log(selectedDate);
            getdata(selectedDate);
        }
    });
});

function getdata(targetDate){
    $.ajax({
        url: "../backend/view/loginLogger.php",
        type: 'POST',
        data: {date:targetDate},
        success: function(response) {
            let json = JSON.parse(response);
            var my_columns = [];

            $.each(json[0], function(key, value) {
                var my_item = {};
                my_item.data = key;
                my_item.title = key;
                my_columns.push(my_item);
            });
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                table.clear();
                table.destroy();
            }
            if (my_columns.length >0){
                table = $('#dataTable').DataTable({
                    responsive: false,
                    search: false,
                    data: json,
                    buttons: [
                        {
                            extend: 'copy',
                            filename: 'custom_filename_copy'
                        },
                        {
                            extend: 'excel',
                            filename: 'loginLog_'+targetDate
                        },
                        {
                            extend: 'pdf',
                            filename: 'loginLog_'+targetDate
                        },
                        {
                            extend: 'csv',
                            filename: 'loginLog_'+targetDate
                        },
                    ],
                    "columns": my_columns
                });
                table.buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
            }

        },
        beforeSend: function() {

        },
        error: function(res) {
            console.log(res);
        }
    });
}