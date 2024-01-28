function handleFiles(files) {
    uploadFile(files[0]);
}



function uploadFile(file) {
    let url = 'clientfxn/order_upload.php'
    let formData = new FormData()
    formData.append('file', file);
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        datatype: 'json',
        success: function(data) {
            console.log(data)
            // response = jQuery.parseJSON(data)
            // if (response['status'] == "success") {
            //     progressDone();
            //     previewFile(file, response);
            //     console.log('eeeeee')
            //     toastr.success(response['message']);
            // } else {
            //     toastr.error(response['message']);
            // }
        },
        error: function(res) {
            console.log(res);
        }
    });
}