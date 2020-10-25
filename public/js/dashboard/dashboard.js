$(function() {
    initDonutChart('incomeCategoryChart', total_income_category)
    initDonutChart('expenseCategoryChart', total_expense_category)
    initDonutChart('cardsCategoryChart', total_card_expense_category)
    
    'use strict'

    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    }

    var mode      = 'index'
    var intersect = true

    var $entriesChart = $('#total-account-chart')
    var entriesChart  = new Chart($entriesChart, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    backgroundColor: '#007bff',
                    borderColor    : '#007bff',
                    data           : Object.values(total_income)
                },
                {
                    backgroundColor: '#ff1a1a',
                    borderColor    : '#ff1a1a',
                    data           : Object.values(total_expense),
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display      : true,
                        lineWidth    : '4px',
                        color        : 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: $.extend({
                        beginAtZero: true,
                        callback: function (value, index, values) {
                            if (value >= 1000) {
                                value /= 1000
                                value += 'k'
                            }
                            return 'R$ ' + value
                        }
                    }, ticksStyle)
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                },
                ticks: ticksStyle
                }]
            }
        }
    })

    let datasets = []
    const cards = Object.keys(total_invoices)
    const colors = [
        '#ffd800',
        '#007bff',
        '#ff0000',
        '#33cc33',
        '#003300',
        '#ced4da',
        '#6600cc',
        '#ffccff'
    ]

    for (let index = 0; index < Object.keys(total_invoices).length; index++) {
        let data = {
            type                : 'line',
            data                : total_invoices[cards[index]],
            backgroundColor     : 'tansparent',
            borderColor         : colors[index],
            pointBorderColor    : colors[index],
            pointBackgroundColor: colors[index],
            fill                : false,
            label               : cards[index]
        }
        datasets.push(data)
    }

    var $cardsChart = $('#credit-card-chart')
    var cardsChart  = new Chart($cardsChart, {
        data: {
            labels  : months,
            datasets: datasets
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: true,                            
            },
            scales: {
                yAxes: [{
                gridLines: {
                    display      : true,
                    lineWidth    : '4px',
                    color        : 'rgba(0, 0, 0, .2)',
                    zeroLineColor: 'transparent'
                },
                ticks: $.extend({
                    beginAtZero : true,
                    suggestedMax: 200
                }, ticksStyle)
                }],
                xAxes: [{
                    display  : true,
                    gridLines: {
                        display: false
                },
                ticks: ticksStyle
                }]
            }
        }
    })

    function initDonutChart(id, values) {
        let amount = 0;

        for (const object of values) {
            amount += object.value 
        }

        if(values.length === 0) {
            values = [
                {
                    value: 0,
                    label: "Nenhum Registro"
                }
            ]
        }

        Morris.Donut({
            element: id,
            data: values,
            colors: [
                'rgb(233, 30, 99)', 
                'rgb(0, 188, 212)', 
                'rgb(255, 152, 0)', 
                'rgb(0, 150, 136)', 
                'rgb(96, 125, 139)',
                'rgb(220,220,220)',
                'rgb(100,149,237)',
                'rgb(173,216,230)',
                'rgb(32,178,170)',
                'rgb(0,100,0)'
            ],
            formatter: function (y) {
                let total = 0
                
                if(amount !== 0) {
                    total = y / parseFloat(amount)
                }
                

                return (total * 100).toFixed(2) + '%'
            }
        });
    
                
    }

    $('#last_month').on('click', function(event) {
        event.preventDefault();
        let type = $(this).attr('data-month')
        $("#form_month").append("<input type='hidden' name='month' value=" + type + " />")
        $("#last_month").attr('disabled', true)
        $("#next_month").attr('disabled', true)
        $("#form_month").submit()
    })

    $('#next_month').on('click', function(event) {
        event.preventDefault();
        let type = $(this).attr('data-month')
        $("#form_month").append("<input type='hidden' name='month' value=" + type + " />")
        $("#last_month").attr('disabled', true)
        $("#next_month").attr('disabled', true)
        $("#form_month").submit()
    })
})