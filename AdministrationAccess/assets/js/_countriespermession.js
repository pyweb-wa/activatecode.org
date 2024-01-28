let rechtable; 
$(document).ready(function () { 
    // getUsers(); 
    
    $('#progress').removeClass('hidden');
    getData();
    document.getElementById("customerSearch").addEventListener("keyup", filterTable);
     

});

function getData(){
        $.ajax({
        url: "servicesfxn/usersPermissions.php",
        dataType: 'json',
        type: 'POST',
        data: {
            'action': 'getUsersCountry'
        },
        success: function (response) { 
            var dt = JSON.parse(response);                         
            const userResults = dt.user;
            const countryResults = dt.country; 
            const perm = dt.perm; 
            const table = $('<table id="ttable">').addClass('table table-bordered'); 
            const headerRow = $('<tr>');
            headerRow.append($('<th>').text('###').css('width', '10%').css('padding', '5px').addClass('text-center '));   

            countryResults.forEach(country => {
                const cnt = country.source;
                const selectAllCheckbox = $('<input>')
                .attr('type', 'checkbox')
                .addClass(`text-center checkbox-all checkbox-${cnt}`)     
                .attr('data-country-source', cnt); 
                const specificCountryResults = perm.filter(item => item.country === cnt);
                const specificCountryCount = specificCountryResults.length;
               
                if (specificCountryCount == userResults.length){
                    selectAllCheckbox.prop('checked', true);
                }
                selectAllCheckbox.on('change', function() {
                    $('#progress').removeClass('hidden');
                    const isChecked = $(this).prop('checked'); 
                    const countrySource = $(this).data('country-source');
                    const usertoken = userResults.map(user => user.access_Token);
                    $.ajax({
                        url: 'servicesfxn/usersPermissions.php',
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            action: 'updateuser_country',
                            countrySource: countrySource,
                            isChecked: isChecked,
                            userarray:JSON.stringify(usertoken)
                        },
                        success: function(response) {
                            console.log('AJAX request for Select All successful:', response);
                            $('#progress').addClass('hidden');
                            location.reload();
                        },
                        error: function(error) {
                            console.error('AJAX request for Select All error:', error);
                            $('#progress').addClass('hidden');
                            DevExpress.ui.notify({
                                message: error,
                                height: 45,
                                width: 300,
                                minWidth: 150,
                                type: 'error',
                                position: {
                                    my: "center bottom",
                                    at: "center bottom",
                                    of: window
                                },
                                displayTime: 2000,
                                animation: {
                                  show: {
                                    type: 'fade', duration: 400, from: 0, to: 1,
                                  },
                                  hide: { type: 'fade', duration: 40, to: 0 },
                                },
                              });
                        }
                    }); 
                });

                const firstUnderscoreIndex = cnt.indexOf('_');
                const cnt1 = cnt.substring(0, firstUnderscoreIndex);
                const cnt2 =  cnt.substring(firstUnderscoreIndex + 1);
         
                headerRow.append($('<th>').css('padding', '5px').text(cnt1).append('<br>').append(cnt2).append('<br>').append(selectAllCheckbox).addClass('text-center '));
            });


            table.append(headerRow); 
            userResults.forEach(user => {
                const userRow = $('<tr>');
                userRow.append($('<td>').text(user.name)); 
                countryResults.forEach(country => {
                    const checkbox = $('<input>')
                        .attr('type', 'checkbox')
                        .attr('data-country-source', country.source)
                        .attr('data-user-token', user.access_Token)
                        .addClass('mx-auto w-100 user-checkbox');
                    
                    const exists = perm.some(item => item.usertoken  === user.access_Token && item.country === country.source);

                    if (exists) {
                        checkbox.prop('checked', true);
                    }
                    checkbox.on('change', function() {
                        $('#progress').removeClass('hidden'); 
                        const countrySource = $(this).data('country-source');
                        const accessTokens = $(this).data('user-token');
                        const isChecked = $(this).prop('checked'); 
                        $.ajax({
                            url: 'servicesfxn/usersPermissions.php',
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                userarray: JSON.stringify([accessTokens]),
                                countrySource: countrySource,
                                action: 'updateuser_country',
                                isChecked: isChecked
                            },
                            success: function(response) {
                                console.log('AJAX request successful:', response);
                                $('#progress').addClass('hidden');                                
                            },
                            error: function(error) {
                                console.error('AJAX request error:', error);
                                $('#progress').addClass('hidden');
                                DevExpress.ui.notify({
                                    message: error,
                                    height: 45,
                                    width: 300,
                                    minWidth: 150,
                                    type: 'error',
                                    position: {
                                        my: "center bottom",
                                        at: "center bottom",
                                        of: window
                                    },
                                    displayTime: 2000,
                                    animation: {
                                      show: {
                                        type: 'fade', duration: 400, from: 0, to: 1,
                                      },
                                      hide: { type: 'fade', duration: 40, to: 0 },
                                    },
                                  });
                            }
                        });
                    });
                 
                    userRow.append($('<td>').addClass('text-center').append(checkbox));
                });

                table.append(userRow);
            });
            $('#divtbl').append(table); 
            $('#progress').addClass('hidden');
        }
    });
}

function toggleCountryCheckboxes(countrySource, isChecked) {
    $('.user-checkbox[data-country-source="' + countrySource + '"]').not('.select-all-checkbox').prop('checked', isChecked);
}
function filterTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("customerSearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("ttable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0]; 
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
 
// function getUsers() {
//     $.ajax({
//         url: "servicesfxn/usersPermissions.php",
//         dataType: 'json',
//         type: 'POST',
//         data: {
//             'action': 'getUsersCountry'
//         },
//         success: function (response) { 
//             var len = response.length;
//             $('#tableBody').empty();
//             for (var i = 0; i < len; i++) {
//                 var countryInfo = response[i].country_char; 
//                 if (countryInfo !== null) {
//                     countryInfo = '<td>' + countryInfo + '</td>';
//                 } else {
//                     countryInfo = '<td></td>';
//                 }
    
//                 var row = '<tr>' +
//                     '<td>' + response[i].user_name + '</td>' +
//                     countryInfo +
//                     '<td><button class="btn btn-primary btn-sm" onclick="openModal(\'' + response[i].user_id + '\',\'' + response[i].country_id + '\')">Edit</button></td>' +
//                     '</tr>';
    
//                 $('#tableBody').append(row);
//             }
//         }
//     });



// }

// function openModal(userName,userCountryList) {
//     // Set the user name in the modal
//     var userCountryArray = userCountryList.split(',');
//     $('#modalUserName').text(userName); 

//     $('#modalCountryButtons').empty();
//     $.ajax({
//         url: "servicesfxn/usersPermissions.php",
//         dataType: 'json',
//         type: 'POST',
//         data: {
//             'action': 'getAllCountryCodes'
//         },
//         success: function (allCountryCodes) { 
//             console.log(userCountryArray);
//             var len = allCountryCodes.length;
//             var successButtons = [];
//             var regularButtons = [];
//             for (var i = 0; i < len; i++) {
//                 var countryid = String(allCountryCodes[i].id);
//                 var country_name = allCountryCodes[i].country_name;
//                 var country_char = allCountryCodes[i].country_char;
//                 var country_code = allCountryCodes[i].country_code;
//                 var buttonClass = userCountryArray.includes(countryid) ? 'btn btn-success' : 'btn btn-light';
//                 var buttonText = country_name +  '<br>' + country_char + '-' + country_code;
//                 var button = '<button id="'+countryid+'"  style="margin: 7px;"  class="' + buttonClass + '" onclick="selectCountry(\'' + countryid + '\')">' + buttonText + '</button>';
//                 if (buttonClass === 'btn btn-success') {
//                     successButtons.push(button);
//                 } else {
//                     regularButtons.push(button);
//                 }               
//             }
//             var allButtons = successButtons.concat(regularButtons); 
//             $('#modalCountryButtons').append(allButtons.join(''));

//         }
//     });
//     $('#myModal').modal('show');
    
// }

// function filterCountries() {
//     var searchValue = $('#countrySearch').val().toLowerCase();

//     $('.btn').each(function () {
//         var buttonText = $(this).text().toLowerCase(); 
//         if (buttonText.indexOf(searchValue) > -1) {
//             $(this).show();
//         } else {
//             $(this).hide();
//         }
//     });
// }

// function selectCountry(countryid) {
//     console.log('Selected Country Code:', countryid);
//     $('#'+countryid).toggleClass('btn-success btn-light');
// }

// function updateCountry(){
//         var successButtonIds = $('.btn-success').map(function() {
//             return this.id;
//         }).get();
//         console.log('IDs of buttons with btn-success:', successButtonIds);
//         var uid =   $('#modalUserName').text(); 
//         console.log('uid:', uid);
//         $.ajax({
//             url: "servicesfxn/usersPermissions.php",
//             dataType: 'json',
//             type: 'POST',
//             data: {
//                 'action': 'updateCountryperm',       
//                 'uid': uid,         
//                 'data': successButtonIds
//             },
//             success: function (res) { 
//                 console.log(res); 
//                 $('#customerSearch').val("");
//                 getUsers();
//                 $('#myModal').modal('hide');
//             }
//         });
// }

