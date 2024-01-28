<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remaining Numbers Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Remaining Numbers Table</h1>
        <div class="form-group">
            <label for="dropdown">Select an option:</label>
            <select class="form-control" id="dropdown">
  
                <option value="Jikatel">Jikatel</option>
                <option value="lnovaro">lnovaro</option>
                <option value="saliba">saliba</option>
                <option value="digitalsim">digitalsim</option>
                <option value="All">All</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" id="sendBtn">Send</button>
        <div id="tableContainer" class="mt-5">
            <table class="table">
                <thead>
                    <tr>
                    <th>Application</th>
            <th>Country Code</th>
            <th>Count</th>
            <th>App Code</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle click event on send button
            $('#sendBtn').click(function() {
                var selectedOption = $('#dropdown').val();
                var url = 'api.php'; // Replace with your actual API URL

                // Make AJAX request
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: 'get',
                        option: selectedOption
                    },
                    success: function(response) {
                        // Clear existing table rows
                        $('#tableBody').empty();
                        var response = JSON.parse(response);
                        // Iterate over JSON data and create table rows
                        $.each(response, function(index, data) {
                            var row = '<tr>' +
                                '<td>' + data.application + '</td>' +
                                '<td>' + data.country_code + '</td>' +
                                '<td>' + data.count + '</td>' +
                                '<td>' + data.app_code + '</td>' +
                                '</tr>';
                            $('#tableBody').append(row);
                        });
                    },
                    error: function() {
                        alert('Error occurred while fetching data!');
                    }
                });
            });
        });
    </script>
</body>

</html>
