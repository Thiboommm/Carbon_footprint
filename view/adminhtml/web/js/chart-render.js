define(['jquery', 'Chart'], function ($, Chart) {
    'use strict';

    return {
        renderChart: function (url, canvasElement) {
            $.get(url)
                .done(function (response) {
                    const ctx = canvasElement.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'Monthly Carbon Footprint (kg CO₂)',
                                data: response.data,
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'kg CO₂'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        }
                    });
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Error loading chart data:', errorThrown, jqXHR.responseText);
                });
        }
    };
});
