$(document).ready(function() {
    getData();
    // var intervalId = window.setInterval(function() {
    //     /// call your function here
    //     // getData();
    //     retreive_data();
    // }, 2000);



});
var data_length = 0;
var pendging_count = 0;

function retreive_data() {
    var rows = [];
    var response2 = $.get("clientfxn/get_channels.php?count=1", function(data) {
        //var json = JSON.parse(data);
        res = data.split(",");
        remote_length = parseInt(res[0])
        pend = parseInt(res[1])
            //alert(remote_length);
            // console.log('local =' + data_length);
            // console.log('remote =' + remote_length);
        if ((remote_length != data_length && data_length != 0) || pendging_count != pend) {
            toastr.success('New Record Received.');
            console.log('New Record Received.');
            getData();
        }
        data_length = remote_length;
        pendging_count = pend;
    });
}

function getData() {
    var columnDefs = [{
            data: "id",
            title: "Id",
            type: "readonly"
        },
        {
            data: "FileTime",
            title: "FileTime"
        },
        {
            data: "quantity",
            title: "cnt"
        },


        {
            data: "name",
            title: "Name"
        },
        {
            data: "status",
            title: "Status"
        },
        {
            data: "show",
            title: "show Online"
        },
        {
            data: "download",
            title: "Download"
        },
        {
            data: "convert",
            title: "Hashs"
        },
        {
            data: "emulator",
            title: "Emulator"
        }


    ];

    var myTable;

    myTable = $('#dataTable').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url: "clientfxn/get_channels.php?archive=1",
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            dataSrc: ''
        },
        destroy: true,
        columns: columnDefs,
        dom: 'Bfrtip', // Needs button container
        //select: 'single',
        responsive: true,
        // altEditor: true, // Enable altEditor
        // buttons: [{
        //     extend: 'selected', // Bind to Selected row
        //     text: 'Download',
        //     name: 'add' // do not change name
        // }],
        // onAddRow: function(datatable, rowdata, success, error) {
        //     $.ajax({
        //         // a tipycal url would be / with type='PUT'
        //         url: "clientfxn/channel-download.php",
        //         type: 'POST',
        //         data: rowdata,
        //         success: success,
        //         error: error
        //     });
        //},

    });
    myTable.buttons().remove();

}

function DownloadChannel(filename) {
    var req = new XMLHttpRequest();
    var url = "clientfxn/channel-download.php?fname=" + filename + "&archive=1";

    $.ajax({
        url: url,
        cache: false,
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 2) {
                    if (xhr.status == 200) {
                        xhr.responseType = "blob";
                    } else {
                        xhr.responseType = "text";
                    }
                }
            };
            return xhr;
        },
        success: function(data) {
            //Convert the Byte Data to BLOB object.
            var blob = new Blob([data], { type: "application/octetstream" });

            //Check the Browser type and download the File.
            var isIE = false || !!document.documentMode;
            if (isIE) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                var url = window.URL || window.webkitURL;
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename + ".txt";
                link.click();
                // $.ajax({
                //     url:"fdownload.php?mfname=" + filename,
                //     type:"GET",
                //     success: function(){
                //         lnk.parentNode.removeChild(lnk);

                //     }
                // })
            }
        }
    });
}

function Convert(filename) {
    var req = new XMLHttpRequest();
    var url = "clientfxn/channel-download.php?fname=" + filename + "&convert=1" + "&archive=1";

    $.ajax({
        url: url,
        cache: false,
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 2) {
                    if (xhr.status == 200) {
                        xhr.responseType = "blob";
                    } else {
                        xhr.responseType = "text";
                    }
                }
            };
            return xhr;
        },
        success: function(data) {
            //Convert the Byte Data to BLOB object.
            var blob = new Blob([data], { type: "application/octetstream" });

            //Check the Browser type and download the File.
            var isIE = false || !!document.documentMode;
            if (isIE) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                var url = window.URL || window.webkitURL;
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename + ".txt";
                link.click();

            }
        }
    });
}


function ShowOnline(filename) {
    // window.open("clientfxn/showchannels.php?show&fname=" + filename)
    // Create a form
    var mapForm = document.createElement("form");
    mapForm.target = "_blank";
    mapForm.method = "POST";
    mapForm.action = "clientfxn/showchannels.php?archive=1";

    // Create an input
    var mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "fname";
    mapInput.value = filename;

    var mapInput1 = document.createElement("input");
    mapInput1.type = "text";
    mapInput1.name = "show";
    mapInput1.value = "";
    // Add the input to the form
    mapForm.appendChild(mapInput);
    mapForm.appendChild(mapInput1);

    // Add the form to dom
    document.body.appendChild(mapForm);

    // Just submit
    mapForm.submit();

}




function Emulator(filename) {
    var req = new XMLHttpRequest();
    var url = "clientfxn/convert_emulator.php?fname=" + filename;
    $.ajax({
        url: url,
        cache: false,
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 2) {
                    if (xhr.status == 200) {
                        xhr.responseType = "blob";
                    } else {
                        xhr.responseType = "text";
                    }
                }
            };
            return xhr;
        },
        beforeSend: function(xhr) {
            $('#loading').show();
        },

        success: function(data) {
            $('#loading').hide();

            //Convert the Byte Data to BLOB object.
            var blob = new Blob([data], { type: "application/octetstream" });

            //Check the Browser type and download the File.
            var isIE = false || !!document.documentMode;
            if (isIE) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                var url = window.URL || window.webkitURL;
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename + ".zip";
                link.click();

            }
        }
    });
}