"use strict";
$(document).ready(function(){
    /*Radar chart*/
    var radarElem = document.getElementById("radarChart");

    var data2 = {
        labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
        datasets: [{
            label: "My First dataset",
            backgroundColor: "rgba(100, 221, 187, 0.52)",
            borderColor: "rgba(72, 206, 168, 0.88)",
            pointBackgroundColor: "rgba(51, 175, 140, 0.88)",
            pointBorderColor: "rgba(44, 130, 105, 0.88)",
            pointHoverBackgroundColor: "rgba(44, 130, 105, 0.88)",
            pointHoverBorderColor: "rgba(107, 226, 193, 0.98)",
            data: [65, 59, 90, 81, 56, 55, 40]
        }, {
            label: "My Second dataset",
            backgroundColor: "rgba(255, 204, 189, 0.95)",
            borderColor: "rgba(255, 165, 138, 0.95)",
            pointBackgroundColor: "rgba(255, 116, 22, 0.94)",
            pointBorderColor: "rgba(251, 142, 109, 0.95)",
            pointHoverBackgroundColor: "rgba(251, 142, 109, 0.95)",
            pointHoverBorderColor: "rgba(255, 165, 138, 0.95)",
            data: [28, 48, 40, 19, 96, 27, 100]
        }]
    };
    

    /*Polar chart*/
    var polarElem = document.getElementById("polarChart");

    var data3 = {
        datasets: [{
            data: [
                11,
                16,
                7,
                3,
                14
            ],
            backgroundColor: [
                "#7E81CB",
                "#1ABC9C",
                "#B8EDF0",
                "#B4C1D7",
                "#01C0C8"
            ],
            hoverBackgroundColor: [
                "#a1a4ec",
                "#2adab7",
                "#a7e7ea",
                "#a5b0c3",
                "#10e6ef"
            ],
            label: 'My dataset' // for legend
        }],
        labels: [
            "Blue",
            "Green",
            "Light Blue",
            "grey",
            "Sea Green"
        ]
    };

   

    /*Pie chart*/
    var pieElem = document.getElementById("pieChart");
    var data4 = {
        labels: [
            "Blue",
            "Orange",
            "Sea Green"
        ],
        datasets: [{
            data: [30, 30, 40],
            backgroundColor: [
                "#25A6F7",
                "#FB9A7D",
                "#01C0C8"
            ],
            hoverBackgroundColor: [
                "#6cc4fb",
                "#ffb59f",
                "#0dedf7"
            ]
        }]
    };


   
    //Time Scale
   

});
