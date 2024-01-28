$(document).ready(function() {
    
    chart_total();
    dailyProfit();
    $('#dataTableA').DataTable();

});
//getdailyProfits

function chart_total() {
    $.ajax({
        method: 'POST',
        url: 'servicesfxn/dashboard.php',
        dataType: 'json',
        data: { requestCountperDate: "true" },
        success: function(response) {
            drawGraph1(response);
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

function dailyProfit() {
    $.ajax({
        method: 'POST',
        url: 'servicesfxn/dashboard.php',
        dataType: 'json',
        data: { getdailyProfits: "true" },
        success: function(response) {
            drawGraph2(response);
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}




function drawGraph1(data) {
    var chartData = {
        labels: data['date'],
        datasets: [{
            data: data['count'],
        }]
    };

    var chLine = document.getElementById("chLine");

    if (chLine) {
        new Chart(chLine, {
            type: 'line',
            responsive: 'true',
            data: chartData,
            borderColor: 'rgba(78, 115, 223, 1)',
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        },
                        gridLines: { color: 'rgba(0, 0, 0, 0.1)' },
                    }, ]
                },
                legend: {
                    display: false
                },
                maintainAspectRatio: false,
                drawOnChartArea: false
            }
        });
    }

}


function drawGraph2(data) {
    var chartData = {
        labels: data['date'],
        datasets: [{
            data: data['count'],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1

        }]
    };

    var chLine = document.getElementById("chLine2");

    if (chLine) {
        new Chart(chLine, {
            type: 'bar',
            responsive: 'true',
            data: chartData,
            borderColor: 'rgba(78, 115, 223, 1)',

            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        },
                        gridLines: { color: 'rgba(0, 0, 0, 0.1)' },
                    }, ]
                },
                legend: {
                    display: false
                },
                maintainAspectRatio: false,
                drawOnChartArea: false
            }
        });
    }

}