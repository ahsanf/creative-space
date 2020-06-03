// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package    mod_evoting
 * @copyright  2016 Cyberlearn
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M = M || {};
var strTooltipEnd = "</p></h2>";
M.mod_evoting = M.mod_evoting || {};

M.mod_evoting.poll_init = function (Y, graphData) {
    jsonDataGraphic = graphData;
    var intervalVoteDirect;
    var myCounter;
    var viewGraph;
    var optionsGraph;
    var dataGraph;
    var chart;
    var refreshBoolean = false;
    var timeToVote;
    var max = 20;
    var visibleQRCode = false;
    var pollStart = false;
    var reset = false;
    var sumVote = 0;
    var countOptions = 0;
    var idQuestion;
    var idPoll;
    var idCourse;
    var goodAnswerBoolean = false;
    var jsonDataGraphic;
    var strTooltipStart = "<h2 style='padding-left:10px;padding-right:10px; font-size:1.1em; line-height:20px'>" + M.util.get_string('countvote', 'evoting') + "<p style='font-size:1.5em; line-height:30px; font-weight:600;color:#007cb7;padding-top:5px;' >";

// Load the Visualization API and the chart package.
    google.load("visualization", "1.0", {
        packages: ["corechart"]
    });

// Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

    $(function ($) {



        var history = false;

        // Resize graph if windows is resize
        $(window).resize(function () {
            updateOptionGraphicMax();
            chart.draw(viewGraph, optionsGraph);
        });

        // Get the ID of the Poll, Course and Question
        idPoll = $("#inputIdPoll").val();
        idQuestion = $("#inputIdQuestion").val();
        idCourse = $("#inputIdCourse").val();

        // hide first select
        $("#menuselectTime option:first").css("display", "none");

        //set Poll to false at the initialisation of the page -> Poll is not started
        setStatutPoll(idPoll, false);

        // Sum of vote
        sumVote = 0;
        $(".answerCount").each(function () {
            sumVote += parseInt($(this).text());
            countOptions++;

        });

        // Click on navigation page.
        $('.pagenum').on('click', function() {
            changeQuestion(idPoll, $(this).data('value'));
        });

        // Click on button statut poll to set (Start / Stop) the poll
        $("#buttonStatutPoll").click(function () {
            clearInterval(myCounter);
            clearInterval(intervalVoteDirect);

            reset = false;

            // Time to vote
            timeToVote = $('select[name=selectTime]').val();

            if (pollStart == true && timeToVote != 1) {
                endTimeToVote(idPoll, history);
                return;
            }

            if (timeToVote > 1) {
                // Start the poll to vote
                setStatutPoll(idPoll, true);

                $("#buttonStatutPoll").val(M.util.get_string('end', 'evoting'));
                $("#buttonStatutPoll").addClass("stopMpoll");

                // Disable buttons
                $("#buttonResetQuestion").prop("disabled", true);
                $("#buttonResetQuestion").css("opacity", 0.5);
                $("#buttonBack").prop("disabled", true);
                $("#buttonNext").prop("disabled", true);
                $("#menuselectTime").prop("disabled", true);
                $("#selectZone").css("opacity", 0.5);

                // Show the countdown div
                $("#divCountDown").show();
                $("#divEndText").show();
                $("#divCountText").show();
                $("#divOptions").show();

                // Hide chart
                $("#chartContainer").hide();

                // Write time to initiate countdown
                $('#divEndText').html(M.util.get_string('endvote', 'evoting'));
                $('#divCountText').html("<h1>" + timeToVote + "</h1><span class='sec'>" + M.util.get_string('seconds', 'evoting') + "</span>");

                // Countdown loading display
                countdownCircle(timeToVote, idPoll, history);

            } else if (timeToVote == 1) {

                if (pollStart == true) {

                    // Sum of vote
                    sumVote = 0;
                    $(".answerCount").each(function () {
                        sumVote += parseInt($(this).text());
                    });

                    manageGoodAnswer(true);
                    updateOptionGraphicMax();
                    chart.draw(viewGraph, optionsGraph);
                    pollStart = false;
                    setStatutPoll(idPoll, pollStart);
                    clearInterval(intervalVoteDirect);
                    clearInterval(myCounter);

                    // History
                    saveHistory(idPoll);

                    // Manage the visibility of the historic link
                    manageHistoryLink(idQuestion);

                    $("#buttonStatutPoll").val(M.util.get_string('start', 'evoting'));
                    $("#buttonStatutPoll").removeClass("stopMpoll");
                    $("#buttonResetQuestion").prop("disabled", false);
                    $("#buttonResetQuestion").css("opacity", 1);
                    $("#menuselectTime").prop("disabled", false);
                    $("#selectZone").css("opacity", 1);

                    // Show the countdown div
                    $("#divOptions").hide();
                    $("#divOptions").css("padding-top", "280px");

                    //Hide button back at the first question
                    if ($("#numberQuestion").text() == 1) {
                        $("#buttonBack").prop('disabled', true);
                    } else {
                        $("#buttonBack").prop('disabled', false);
                    }

                    // Hide button next at the last question
                    if ($("#inputIdQuestionNext").val() == 0) {
                        $("#buttonNext").prop('disabled', true);
                    } else {
                        $("#buttonNext").prop('disabled', false);
                    }
                } else {
                    manageGoodAnswer(false);

                    pollStart = true;

                    updateOptionGraphicMax();

                    chart.draw(viewGraph, optionsGraph);

                    // Show the countdown div
                    $("#divOptions").show();
                    $("#divOptions").css("padding-top", "15px");

                    setStatutPoll(idPoll, pollStart);
                    $("#buttonStatutPoll").val(M.util.get_string('end', 'evoting'));
                    $("#buttonStatutPoll").addClass("stopMpoll");
                    intervalVoteDirect = setInterval(function () {
                        refresh(history, idPoll);
                    }, 3000);
                    $("#buttonResetQuestion").prop("disabled", true);
                    $("#buttonResetQuestion").css("opacity", 0.5);
                    $("#menuselectTime").prop("disabled", true);
                    $("#selectZone").css("opacity", 0.5);
                }
            } else {
                alert(M.util.get_string('timechoice', 'evoting'));
            }
        });

        // Click on button statut poll to set (Start / Stop) the poll
        $("#buttonResetQuestion").click(function () {
            reset = true;
            manageGoodAnswer(false);
            resetQuestion(idPoll, history, idQuestion);
        });

        // Click on button to show / hide QR Code
        $("#divButtonQrCode").click(function () {
            if (visibleQRCode == true) {
                $("#divQRCode").hide();
                $("#buttonQrCode").val(M.util.get_string('msgShowQrCode', 'evoting'));
                visibleQRCode = false;
            } else {
                $("#divQRCode").show();
                $("#buttonQrCode").val(M.util.get_string('msgHideQrCode', 'evoting'));
                visibleQRCode = true;
            }
        });

    });

    /**
     * Manage the history link button
     * @param idQuestion
     */
    function manageHistoryLink(idQuestion) {

        $.ajax({
            url: './ws/ajax_poll.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idCourse: idCourse,
                idQuestion: idQuestion,
                action: 'mdl_get_history',
                sesskey: M.cfg.sesskey
            },
            success: function (json) {

                var countHistory = json.length;

                if (countHistory > 0) {
                    $("#divHistorique").css("visibility", "visible");
                } else {
                    $("#divHistorique").css("visibility", "hidden");
                }
            },
            error: function (resultat, statut, erreur) {
            },
            complete: function (resultat, statut) {
            }
        });

    }

    /* Google Chart
     * Callback that creates and populates a data table,
     * instantiates the  chart, passes in the data and draws it.
     */
    function drawChart() {

        // Get the dataGraphic array form PHP
        var dataOptionsGraphic = JSON.parse(jsonDataGraphic);

        for (col in dataOptionsGraphic) {
            dataOptionsGraphic[col][5] = strTooltipStart + dataOptionsGraphic[col][1] + strTooltipEnd;
        }

        // Header of the data Graphic
        var headDataGraphic = [M.util.get_string('choice', 'evoting'), M.util.get_string('countvote', 'evoting'), {
            type: 'string',
            role: 'style'
        }, {
            type: 'string',
            role: 'annotation'
        }, 'id', {
            'type': 'string',
            'role': 'tooltip',
            'p': {
                'html': true
            }
        }, {
            type: 'string'
        }];

        // Create the complete data of the Graphic by integrated  header data
        dataOptionsGraphic.splice(0, 0, headDataGraphic);

        // Sum of vote
        sumVote = 0;
        $(".answerCount").each(function () {
            sumVote += parseInt($(this).text());
            countOptions++;
        });

        optionsGraph = {
            title: "",
            allowHtml: true,
            height: 450,
            legend: {
                position: 'none'
            },
            animation: {
                duration: 500,
                easing: 'out'
            },

            vAxis: {
                gridlines: {
                    color: '#000000'
                },
                textPosition: 'out',
                textStyle: {
                    color: '#007cb7',
                    fontName: 'Oswald, Times-Roman',
                    fontSize:40,
                    bold: true,
                    italic: false
                }

            },
            hAxis: {
                title: M.util.get_string('totalvote', 'evoting') + " : " + sumVote,
                minValue: "0",
                maxValue: max,
                gridlines: {
                    color: '#e6e6e6'
                },
                textStyle: {
                    color: '#e6e6e6'
                },
                titleTextStyle: {
                    color: '#007cb7',
                    fontName: 'Oswald, Times-Roman',
                    bold: false,
                    italic: false
                }
            },

            baselineColor: '#007cb7',
            tooltip: {
                isHtml: true
            },
            annotations: {
                textStyle: {
                    fontName: 'Oswald,Helvetica,Arial,sans-serif',
                    fontSize:16,
                    bold: false,
                    italic: false,
                    color: '#007cb7',
                    auraColor: 'rgba(255,255,255,0)'
                },
                alwaysOutside: false,
                highContrast: true,
                stemColor: '#000000'
            },
            backgroundColor: '#f6f6f6',
            chartArea: {
                left: '5%',
                top: '5%',
                height: '75%',
                width: '100%'
            }
        };

        dataGraph = google.visualization.arrayToDataTable(dataOptionsGraphic);
        dataGraph.setColumnProperty(0, {
            allowHtml: true
        });

        viewGraph = new google.visualization.DataView(dataGraph);
        viewGraph.setColumns([0, 1, 2, 3, 5]);

        // Create and draw the visualization.
        chart = new google.visualization.BarChart(document.getElementById('chartContainer'));

        updateOptionGraphicMax();
        chart.draw(viewGraph, optionsGraph);
    }

    /**
     * MouseOver function
     * @param e
     */
    function barMouseOver(e) {
        chart.setSelection([e]);
    }

    /**
     * MouseOut function
     * @param e
     */
    function barMouseOut(e) {
        chart.setSelection([{
            'row': null,
            'column': null
        }]);
    }

    /**
     * Countdown vote function display
     * @param timeToVote
     * @param idPoll
     * @param history
     */
    function countdownCircle(timeToVote, idPoll, history) {

        var totaltime = timeToVote;

        var count = totaltime;
        myCounter = setInterval(function () {

            count -= 1;

            if (count > 1) {
                $('#divCountText').html("<h1>" + count + "</h1><span class='sec'>" + M.util.get_string('seconds', 'evoting') + "</span>");
                $('#divEndText').html(M.util.get_string('endvote', 'evoting'));
            } else {
                $('#divCountText').html("<h1>" + count + "</h1><span class='sec'>" + M.util.get_string('second', 'evoting') + "</span>");
                $('#divEndText').html(M.util.get_string('endvote', 'evoting'));
            }

            updateCountdown(count, totaltime);

            if (count == 0) {
                endTimeToVote(idPoll, history);
            }

        }, 1000);
    }

    /**
     * Function when time to vote is end
     * @param idPoll
     * @param history
     */
    function endTimeToVote(idPoll, history) {
        clearInterval(myCounter);

        //Reset css Countdown
        $('.pie').css('background-image', 'linear-gradient(90deg, #007cb7 50%, #007cb7 50%),linear-gradient(90deg, #007cb7 50%, #007cb7 50%)');

        $("#buttonStatutPoll").val(M.util.get_string('start', 'evoting'));
        $("#buttonStatutPoll").removeClass("stopMpoll");

        // activ button start vote
        $("#buttonStatutPoll").prop("disabled", false);
        $("#buttonResetQuestion").prop("disabled", false);
        $("#buttonResetQuestion").css("opacity", 1);
        $("#menuselectTime").prop("disabled", false);
        $("#selectZone").css("opacity", 1);

        //Hide button back at the first question
        if ($("#numberQuestion").text() == 1) {
            $("#buttonBack").prop('disabled', true);
        } else {
            $("#buttonBack").prop('disabled', false);
        }

        // Hide button next at the last question
        if ($("#inputIdQuestionNext").val() == 0) {
            $("#buttonNext").prop('disabled', true);
        } else {
            $("#buttonNext").prop('disabled', false);
        }

        // hide the countdown div
        $("#divCountDown").hide();
        $("#divCountText").hide();
        $("#divEndText").hide();
        $("#divOptions").hide();

        // When timout finish
        goodAnswerBoolean = true;
        history = true;
        setStatutPoll(idPoll, false);
        refresh(history, idPoll);
    }

    /**
     * Save history
     * @param idPoll
     */
    function saveHistory(idPoll) {

        var idOptionCurrent = 0;
        var nbrVoteOptionCurrent = 0;
        var time = Math.round(new Date().getTime() / 1000);
        var sumVoteOption = 0

        // Sum of vote
        $(".answerCount").each(function () {
            sumVoteOption = sumVoteOption + parseInt($(this).text());
        });

        if (sumVoteOption > 0) {
            $(".answerCount").each(function () {

                idOptionCurrent = $(this).prop('id');
                nbrVoteOptionCurrent = parseInt($(this).html());

                $.ajax({
                    url: './ws/ajax_poll.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        idPoll: idPoll,
                        idCourse: idCourse,
                        idOptionCurrent: idOptionCurrent,
                        nbrVoteOptionCurrent: nbrVoteOptionCurrent,
                        time: time,
                        action: 'mdl_save_history',
                        sesskey: M.cfg.sesskey
                    },
                    success: function (json) {

                    },
                    error: function (resultat, statut, erreur) {
                    },
                    complete: function (resultat, statut) {
                    }
                });
            });
        }
    }

    /**
     * Countdown vote function update tick
     * @param percent
     * @param totaltime
     */
    function updateCountdown(percent, totaltime) {
        var deg;
        if (percent < (totaltime / 2)) {
            deg = 90 + (360 * percent / totaltime);
            $('.pie').css('background-image', 'linear-gradient(' + deg + 'deg, transparent 50%, white 50%),linear-gradient(90deg, white 50%, transparent 50%)');
        } else if (percent >= (totaltime / 2)) {
            deg = -90 + (360 * percent / totaltime);
            $('.pie').css('background-image', 'linear-gradient(' + deg + 'deg, transparent 50%, #007cb7 50%),linear-gradient(90deg, white 50%, transparent 50%)');
        }
    }

    /**
     * Function that refresh the display of the count answers options in graphic
     * @param history
     * @param idPoll
     */
    function refresh(history, idPoll) {

        var deferreds = [];

        $(".answerCount").each(function () {
            idOption = $(this).prop('id');
            deferreds.push(getAnswer(idOption));
        });

        // When all choice are check if there is an update in the graph -> Update the graph if is necessary
        $.when.apply(this, deferreds).done(function () {

            if (refreshBoolean) {
                $(".answerCount").each(function () {
                    idOption = $(this).prop('id');
                });

                // Refresh graph
                updateOptionGraphicMax();
                chart.draw(viewGraph, optionsGraph);

                // History
                if (history == true) {
                    saveHistory(idPoll);
                    // Manage the visibility of the historic link
                    manageHistoryLink(idQuestion);
                }

                if (reset == false) {
                    if (timeToVote > 1) {
                        manageGoodAnswer(true);
                    }
                } else {
                    manageGoodAnswer(false);
                }
            } else {
                if (goodAnswerBoolean == true) {
                    manageGoodAnswer(true);
                } else {
                }
            }

            // var for refresh graph, set to false
            history = false;
            refreshBoolean = false;
            reset = false;
            goodAnswerBoolean = false;
        });
        // show chart
        $("#chartContainer").show();
    }


    /**
     * Function that reset answer of the current question
     * @param idPoll
     * @param history
     */
    function resetQuestion(idPoll, history, idQuestion) {
        $.ajax({
            url: './ws/ajax_poll.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idPoll: idPoll,
                idCourse: idCourse,
                idQuestion: idQuestion,
                action: 'mdl_resetQuestion',
                sesskey: M.cfg.sesskey
            },
            success: function (json) {
                refresh(history, idPoll)
            },
            error: function (resultat, statut, erreur) {
            },
            complete: function (resultat, statut) {

            }
        });
    }

    /**
     * Function that start / stop a poll
     * @param idPoll
     * @param statut
     */
    function setStatutPoll(idPoll, statut) {
        $.ajax({
            url: './ws/ajax_poll.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idPoll: idPoll,
                idCourse: idCourse,
                statut: statut,
                action: 'mdl_setStatutPoll',
                sesskey: M.cfg.sesskey
            },
            success: function (json) {

                // If the poll was started or stopped
                if (json.publish == true) {
                    pollStart = true;
                } else {
                    pollStart = false;
                    //Hide button back at the first question
                    if ($("#numberQuestion").text() == 1) {
                        $("#buttonBack").prop('disabled', true);
                    } else {
                        $("#buttonBack").prop('disabled', false);
                    }

                }
            },
            error: function (resultat, statut, erreur) {
                console.log(erreur);
            },
            complete: function (resultat, statut) {
            }
        });
    }

    /**
     * Function that get the count of answers from end user and display into Moodle
     * @param idOption
     * @returns {*}
     */
    function getAnswer(idOption) {
        i = 1;
        return $.ajax({
            url: './ws/ajax_poll.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idOption: idOption,
                idCourse: idCourse,
                action: 'mdl_refreshOption',
                sesskey: M.cfg.sesskey
            },
            success: function (json) {

                // Check if the graph must be refresh
                if ($("#" + idOption).text() != json.count) {

                    // Data change refresh the graphic
                    $("#" + idOption).text(json.count);

                    refreshBoolean = true;

                    // Update the graphic (annotation and row Count)
                    var rowFilter = dataGraph.getFilteredRows([{
                        column: 4,
                        value: idOption
                    }]);
                    dataGraph.setValue(parseInt(rowFilter), 1, json.count);
                    dataGraph.setValue(parseInt(rowFilter), 3, json.text);
                    dataGraph.setValue(parseInt(rowFilter), 5, strTooltipStart + json.count + strTooltipEnd);
                }

            },
            error: function (resultat, statut, erreur) {
            },
            complete: function (resultat, statut) {
            }
        });
    }

    /**
     * Function that get th previous / next question activ from current Poll
     * @param idPoll
     * @param number
     */
    function changeQuestion(idPoll, number) {
        $.ajax({
            url: './ws/ajax_poll.php',
            type: 'POST',
            dataType: 'json',
            data: {
                idPoll: idPoll,
                idCourse: idCourse,
                number: number,
                action: 'mdl_changeQuestion',
                sesskey: M.cfg.sesskey
            },
            success: function (json) {
                // if succes reload the page
                location.reload();
            },

            error: function (resultat, statut, erreur) {
            },

            complete: function (resultat, statut) {
            }
        });
    }

    /**
     * Udapte layout graphic to big screen
     */
    function updateOptionGraphicMax() {
        if (countOptions < 8) {
            optionsGraph.vAxis.textStyle.fontSize = 40;
            optionsGraph.annotations.textStyle.fontSize = 18;
        } else {
            optionsGraph.vAxis.textStyle.fontSize = 30;
            optionsGraph.annotations.textStyle.fontSize = 18;
        }

        // Sum of vote
        sumVote = 0;
        $(".answerCount").each(function () {
            sumVote += parseInt($(this).text());
        });
        optionsGraph.hAxis.title = M.util.get_string('totalvote', 'evoting') + " : " + sumVote;

    }

    /**
     * Show the good answer in graphic
     * @param pollStart
     */
    function manageGoodAnswer(pollStart) {

        var colorBlueAnswer = "#007cb7"
        var colorGoodAnswer = "#007cb7";
        var colorWrongAnswer = "#D8D8D8 ";
        var cptWrong = 0;

        // Sum of vote
        sumVote = 0;
        $(".answerCount").each(function () {
            sumVote += parseInt($(this).text());
        });

        for (var i = 0; i < dataGraph["Nf"].length; i++) {
            if (pollStart) {
                var value = dataGraph.getValue(i, 6);
                var cptVote = dataGraph.getValue(i, 1);
                var cptVoteToDelete = "";

                if (value.toString() == "true") {
                    dataGraph.setValue(i, 2, colorGoodAnswer);
                    dataGraph.setValue(i, 3, dataGraph.getValue(i, 3).replace(" " + M.util.get_string('goodanswer', 'evoting'), ""));
                    dataGraph.setValue(i, 3, dataGraph.getValue(i, 3) + " " + M.util.get_string('goodanswer', 'evoting'));
                } else {
                    dataGraph.setValue(i, 2, colorWrongAnswer);
                    cptWrong++;
                }
                cptVoteToDelete = $.trim(dataGraph.getValue(i, 3).substr(dataGraph.getValue(i, 3).indexOf(" - ")));

                if (cptVoteToDelete != "" && cptVoteToDelete.length > 1) {
                    if (value.toString() == "true") {
                        dataGraph.setValue(i, 3, dataGraph.getValue(i, 3).replace(cptVoteToDelete, " " + M.util.get_string('goodanswer', 'evoting')));
                    } else {
                        dataGraph.setValue(i, 3, dataGraph.getValue(i, 3).replace(cptVoteToDelete, ""));
                    }
                }

                if (sumVote < 29) {
                    dataGraph.setValue(i, 3, dataGraph.getValue(i, 3) + " - " + cptVote + "/" + sumVote);
                } else {
                    var perCent = (isNaN(perCent = Math.round((dataGraph.getValue(i, 1) / sumVote) * 100)) ? 0 : perCent);
                    dataGraph.setValue(i, 3, dataGraph.getValue(i, 3) + " - " + perCent + " %");
                }

            } else {

                dataGraph.setValue(i, 2, colorBlueAnswer);
                dataGraph.setValue(i, 3, dataGraph.getValue(i, 3).replace(" " + M.util.get_string('goodanswer', 'evoting'), ""));

                cptVoteToDelete = $.trim(dataGraph.getValue(i, 3).substr(dataGraph.getValue(i, 3).indexOf(" - ")));

                if (cptVoteToDelete != "" && cptVoteToDelete.length > 1) {
                    dataGraph.setValue(i, 3, dataGraph.getValue(i, 3).replace(cptVoteToDelete, ""));
                }

            }
        }
        // If there is no good answer
        if (cptWrong == dataGraph["Nf"].length) {
            for (var i = 0; i < dataGraph["Nf"].length; i++) {
                dataGraph.setValue(i, 2, colorBlueAnswer);
            }
        }
        chart.draw(viewGraph, optionsGraph);

    }

    /*
     * Show the QR Code
     */
    M.mod_evoting.poll_init.showQrCode = function () {
        var objB = document.getElementById("QrCodeBig");
        var objS = document.getElementById("divQrCode");
        objB.style.display = "block";
        objS.style.display = "none";

    }

    /*
     * Hide the QR Code
     */
    M.mod_evoting.poll_init.hideQrCode = function () {
        var objB = document.getElementById("QrCodeBig");
        var objS = document.getElementById("divQrCode");
        objB.style.display = "none";
        objS.style.display = "block";

    }

    /**
     * Get the extension file
     * @param name
     * @returns {string}
     */
    function getFileExtension(name) {
        var found = name.lastIndexOf('.') + 1;
        return (found > 0 ? name.substr(found) : "");
    }
}
