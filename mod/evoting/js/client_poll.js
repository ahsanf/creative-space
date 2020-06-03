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
var idClient;
var idPoll;
var lang;
var nbrOptionTotal = 5;

$(function () {

    // Lang of the user
    lang = $("#lang").val();

    // Get the id of the current poll
    idPoll = $("#spanIdPoll").text();
    idClient = $('#clientid').val();
    // Get options for the poll
    get_options_poll(idPoll, idClient);
    var intervalGetOption = setInterval(function () {
        get_options_poll(idPoll, idClient);
    }, 3000);

    //Function onClick buttons
    $("#cancelbutton").click(function () {
        cancel_vote(idClient, idPoll, lang);
    });
});


/*
 * Function for voting (INSERT / UPDATE)
 * @param : idClient
 * @param : myChoice
 * @param : idQuestion
 */
function cancel_vote(idClient, idPoll, lang) {

    $.ajax({
        url: './ws/ajax_poll_vote.php',
        type: 'POST',
        data: {
            idClient: idClient,
            idPoll: idPoll,
            lang: lang,
            action: 'client_cancel_vote',
            sesskey: $("#sesskey").val()
        },
        beforeSend: function () {
            // Show loading message
            $('#loader').show();
            $('.toastBg').fadeIn(400)
            $('#myToast').fadeIn(400);
            $('#myToast').removeClass('toast');
            $('#myToast').addClass('toastLoading');
            $("#myToast span").hide();
        },
        success: function (data, statut) {
            // Show sucess message
            $('#myToast span').html("");
            $('#myToast span').text((data));
            $('#myToast').addClass('toast');
            $('#myToast').removeClass('toastLoading');
            $("#myToast span").show();
            $('.toastBg').delay(1000).fadeOut(400);
            $('#myToast').delay(1000).fadeOut(400);

            changeBackgroundOption(data, button = null);
        },

        error: function (resultat, statut, erreur) {
            // Show error message
            $('#myToast span').html("");
            $('#myToast span').text("Error");
            $('#myToast').addClass('toast');
            $('#myToast').removeClass('toastLoading');
            $("#myToast span").show();
            $('.toastBg').delay(1000).fadeOut(400);
            $('#myToast').delay(1000).fadeOut(400);
        },

        complete: function (resultat, statut) {
        }
    });
}

/*
 * Function for voting (INSERT / UPDATE)
 * @param : idClient
 * @param : myChoice
 * @param : idQuestion
 */
function vote(idClient, myChoice, idPoll, lang, button) {

    $.ajax({
        url: './ws/ajax_poll_vote.php',
        type: 'POST',
        data: {
            // MyChoice-1 because position start to 0 and button to 1
            choice: myChoice,
            idClient: idClient,
            idPoll: idPoll,
            lang: lang,
            action: 'client_vote',
            sesskey: $("#sesskey").val()
        },
        beforeSend: function () {
            // Show loading message
            $('#loader').show();
            $('.toastBg').fadeIn(400)
            $('#myToast').fadeIn(400);
            $('#myToast').removeClass('toast');
            $('#myToast').addClass('toastLoading');
            $("#myToast span").hide();
        },
        success: function (data, statut) {

            data = $.parseJSON(data);

            // Show sucess message
            $('#myToast span').html("");
            $('#myToast span').text((data[1]));
            $('#myToast').addClass('toast');
            $('#myToast').removeClass('toastLoading');
            $("#myToast span").show();
            $('.toastBg').delay(1000).fadeOut(400);
            $('#myToast').delay(1000).fadeOut(400);

            changeBackgroundOption(data,button);
        },
        error: function (resultat, statut, erreur) {
            // Show error message
            $('#myToast span').html("");
            $('#myToast span').text("Error");
            $('#myToast').addClass('toast');
            $('#myToast').removeClass('toastLoading');
            $("#myToast span").show();
            $('.toastBg').delay(1000).fadeOut(400);
            $('#myToast').delay(1000).fadeOut(400);
        },

        complete: function (resultat, statut) {
        }
    });
}

/**
 * Function that change the background option when voted/unvoted
 */
function changeBackgroundOption(data, button){

    if(data[0] == 0){
        $("button").addClass("evotingOptionBtn");
        $("button").removeClass("evotingOptionBtnSelect");
    }

    if(data[1] == $("#voteok").val() ){
        button.addClass("evotingOptionBtnSelect");
        button.removeClass("evotingOptionBtn");
    } else if(data[1] == $("#votesingledeleted").val() ){
        button.addClass("evotingOptionBtn");
        button.removeClass("evotingOptionBtnSelect");
    }

    /* else if (data == $("#votedeleted").val()){
        $(".evotingOptionBtn").css("background-color", "#007cb7");
    }*/
}

/*
 * Function for get options
 * @param : idPoll
 */
function get_options_poll(idPoll, idClient) {

    $.ajax({
        url: './ws/ajax_poll_vote.php',
        type: 'POST',
        data: {
            idPoll: idPoll,
            idClient : idClient,
            action: 'client_get_options_poll',
            sesskey: $("#sesskey").val()
        },
        beforeSend: function () {
        },
        success: function (data, statut) {

            data = $.parseJSON(data);

            console.log(data);

            nbrOptionTotal = data[0].length;
            nbrOptionSelected = data[1].length;

            // Sum Rows
            var nbrRow = Math.ceil(nbrOptionTotal / 2);
            var rowHeight = 100 / nbrRow;
            $("#clientOptions").empty();

            for (var i = 1; i <= nbrRow * 2; i++) {
                var caseVote = '<div id="evotingOption" style="height: ' + rowHeight + '%"><div class="evotingOptionEmpty"></div></div>';
                if (i <= nbrOptionTotal) {
                    if(nbrOptionSelected > 0){
                        for(var j = 0 ; j < nbrOptionSelected ; j++) {
                            if (data[0][i - 1].id == data[1][j].id) {
                                caseVote = '<div id="evotingOption" style="height: ' + rowHeight + '% "><button class="evotingOptionBtnSelect" id="' + i + '"><span id="optionNo" style="font-size : 30px ; font-size: 10vm ">' + i + '</span><input type="hidden" value="' + data[0][i - 1].id + '"/></button></div>';
                                break;
                            } else {
                                caseVote = '<div id="evotingOption" style="height: ' + rowHeight + '% "><button class="evotingOptionBtn" id="' + i + '"><span id="optionNo" style="font-size : 30px ; font-size: 10vm ">' + i + '</span><input type="hidden" value="' + data[0][i - 1].id + '"/></button></div>';
                            }
                        }
                    } else {
                        caseVote = '<div id="evotingOption" style="height: ' + rowHeight + '% "><button class="evotingOptionBtn" id="' + i + '"><span id="optionNo" style="font-size : 30px ; font-size: 10vm ">' + i + '</span><input type="hidden" value="' + data[0][i - 1].id + '"/></button></div>';
                    }
                }
                $("#clientOptions").append(caseVote);
            }

            //Function onClick buttons
            $("button").click(function () {
                var button = $(this);
                vote(idClient, this.id, idPoll, lang, button);
            });

        },

        error: function (resultat, statut, erreur) {
        },

        complete: function (resultat, statut) {
        }
    });
}