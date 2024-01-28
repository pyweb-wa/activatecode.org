$(document).ready(function() {
    getUsers();
    getcountries();
   $('#users').on('change', function(e) {
        getusercountries()
        $('#users').selectpicker("refresh");
    });
     $('#myTable').DataTable({
       processing: true,
                 serverSide: true,
                 searching: true,
                 "language": {
                   "info": ""
                 },
       "ajax": {
         "url": "servicesfxn/countriesAccess.php",
         "type": "POST",
         dataSrc:"",
         "data": {"action":"showtable"} // you can add additional data to the request
         
       },
       "columns": [
                 { "data": 'name' },
                 { "data": 'countries' }
               ]
     });
     
     $('#update').click(function() {
       // get the selected option and remove it from the DOM
       $('#countries option:selected').remove();
   });
   
  
 


});

function getusercountries(){
  // $('#countries').empty();
   //return
   var user = $('#users').val()
   console.log(user)
  

   $.ajax({
       url: "servicesfxn/countriesAccess.php",
      // dataType: 'json',
       type: 'POST',
       data: {
           'action': 'getusercountries',
           'user_id':user
       },
       success: function(response) {
           response = JSON.parse(response);  

           $("#countries").val(response)
           $("#countries").val(response).change();
       //    $("#countries").val(response).change(function() {
       //     var selectedOption = $(this).find('option:selected');
       //     $(this).prepend(selectedOption);
       //   });
          console.log(response)
       }
   });
   $('input[type="search"]').val('');
   // $('input[type="search"]').on('keypress', function(event) {
   //     if (event.keyCode === 13) {
   //         console.log("clicke")
   //       $(this).val('');
   //       $('input[type="search"]').val('');
   //     }
   //   });

   // var selectedOption = $('#countries option:selected');
   // selectedOption.insertBefore($('#countries option:first'));

}

function getUsers() {
   $.ajax({
       url: "servicesfxn/countriesAccess.php",
       dataType: 'json',
       type: 'POST',
       data: {
           'action': 'getUsers'
       },
       success: function(response) {
           var len = response.length;
           $("#users").empty();
           for (var i = 0; i < len; i++) {
               var username = response[i]['name'];
               var id = response[i]['id'];
               $("#users").append("<option value='" + id + "'  >" + username + "</option>");
           };
           $('#users').selectpicker("refresh");
           //getusercountries();
       }
   });



}
function getcountries(){
   $.ajax({
       url: "servicesfxn/countriesAccess.php",
       dataType: 'json',
       type: 'POST',
       data: {
           'action': 'getcountries'
       },
       success: function(response) {
           var len = response.length;
           $("#countries").empty();
           for (var i = 0; i < len; i++) {
               var country = response[i]['country'];
               var country_code = response[i]['country_code'];
               var id = response[i]['id'];
               $("#countries").append("<option value='" + id + "'  >" + country +"-"+country_code+ "</option>");
           };
           $('#countries').selectpicker("refresh");
           getusercountries();
       }
   });
}

function update() {
    var user = $('#users').val()
    var countries = $('#countries').val() || [];
    
    console.log(user)
    console.log(countries)
    //throw new Error();
    var formData = new FormData();
    formData.append('action', 'update');
    formData.append('user_id', user);
    formData.append('countries', countries);
    $.ajax({
        url: "servicesfxn/countriesAccess.php",
        type: "POST",
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            try {
               //console.log(response);

                response = jQuery.parseJSON(response)
                if (response["status"]) {
                    if (response["status"] == "OK") {
                        toastr.success(response["msg"]);
                    }
                }
                $('#myTable').DataTable().ajax.reload();
               //console.log(response)
                //getusercountries()
                location.reload();
               // $('#countries').find('option:selected').remove();
            } catch (err) {
                alert("error happened " + err);
            }
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });

}


function clearcountries() {
   console.log("clicked clear")
   var user = $('#users').val()|| []
   //var countries = $('#countries').val() || [];
   console.log(user)
   console.log(countries)
   //throw new Error();
   var formData = new FormData();
   formData.append('action', 'clear');
   formData.append('user_id', user);
  
   $.ajax({
       url: "servicesfxn/countriesAccess.php",
       type: "POST",
       cache: false,
       data: formData,
       processData: false,
       contentType: false,
       dataType: 'json',
       success: function(response) {
           try {
              //console.log(response);

               response = jQuery.parseJSON(response)
               if (response["status"]) {
                   if (response["status"] == "OK") {
                       toastr.success(response["msg"]);
                   }
               }
               $('#myTable').DataTable().ajax.reload();
              //console.log(response)
               //getusercountries()
               location.reload();
           } catch (err) {
               alert("error happened " + err);
           }
       },
       error: function(request, status, error) {
           alert(request.responseText);
       }
   });

}