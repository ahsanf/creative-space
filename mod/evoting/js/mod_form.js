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
M.mod_evoting = M.mod_evoting || {};

M.mod_evoting.form_init = function (Y, boolUpdate) {

    $(function () {

        // Confirm delete alert dialog
        var $_blockDelete = false;

        // Get idCourse
        var idCourse = $("input[name='idCourse']").val();

        // Max options choice
        var maxOptions = $("input[name='max_choice']").val();

        // Number of questions
        var question_repeats = parseInt($("input[name='question_repeats']").val());

        // Hide choice 7 to 16
        for (var i = 0; i < question_repeats; i++) {

            // Show only last delete button Question
            if (question_repeats == 1) {
                $("#fitem_id_deleteQuestion_" + (question_repeats - 1)).hide();
            } else if (i == (question_repeats - 1)) {
                $("#fitem_id_deleteQuestion_" + i).show();
            } else {
                $("#fitem_id_deleteQuestion_" + i).hide();
            }

            for (var j = 0; j < maxOptions; j++) {

                // If there is a value show it
                var valueInput = $('#id_option' + j + '_' + i).val();

                if (j < 4) {
                    $('#id_option' + j + '_' + i).parent().parent().parent().addClass("visible");
                    //$('#id_option' + j + '_' + i).closest("tr").addClass("visible");
                } else {
                    $('#id_option' + j + '_' + i).parent().parent().parent().addClass("noVisible");
                   // $('#id_option' + j + '_' + i).closest("tr").addClass("noVisible");

                    if (valueInput.length == 0) {
                        $('#id_option' + j + '_' + i).parent().parent().parent().addClass("noVisible");
                        $('#id_option' + j + '_' + i).parent().parent().parent().removeClass("visible");
                       // $('#id_option' + j + '_' + i).closest("tr").addClass("noVisible");
                      //  $('#id_option' + j + '_' + i).closest("tr").removeClass("visible");
                    } else {
                        $('#id_option' + j + '_' + i).parent().parent().parent().addClass("visible");
                        $('#id_option' + j + '_' + i).parent().parent().parent().removeClass("noVisible");
                       // $('#id_option' + j + '_' + i).closest("tr").addClass("visible");
                       // $('#id_option' + j + '_' + i).closest("tr").removeClass("noVisible");
                    }
                }

            }
        }

        // Click button add choice question
        $("a.addChoice").click(function () {

            var idButtonAddChoice = this.id;
            // Get the number of the question addChoice
            var numberQuestion = idButtonAddChoice.split('_')[2];
            var choiceToAdd = $('#id_option0' + '_' + numberQuestion).parent().parent().parent().nextAll(".noVisible").first();
            //var choiceToAdd = $('#id_option0' + '_' + numberQuestion).closest("tr").nextAll(".noVisible").first();
            if(parseInt(choiceToAdd.length) == 0){
                alert(M.util.get_string('noaddChoice','evoting'));
                return false;
            } else {
                choiceToAdd.addClass("visible");
                choiceToAdd.removeClass("noVisible");
            }
            return false;
        });

        // Click button delete question
        $("a.deleteButton").click(function (event) {

            event.preventDefault();

                var callbackDialog = confirm(M.util.get_string('confirmDelete', 'evoting'));

                if (callbackDialog == true) {

                    question_repeats = parseInt($("input[name='question_repeats']").val());

                    // Get the id of the button
                    var idButtonDelete = this.id;

                    // Get the number of the question to delete
                    var numberQuestion = parseInt(idButtonDelete.split('_')[2]);

                    var idQuestion = $("input[name='questionnameid[" + numberQuestion + "]']").val();

                    //Clear input question to delete
                    $("#id_questionname_" + numberQuestion).val("");

                    //Delete div Question
                    $("#id_questionname" + "_" + numberQuestion).parent().parent().parent().parent().parent().remove();

                    //Set cpt question repeat - 1
                    $("input[name='question_repeats']").val(question_repeats - 1);

                    // Show only last delete button Question
                    if (numberQuestion == 1) {
                        $("#id_deleteQuestion_" + (numberQuestion - 1)).parent().parent().hide();
                    } else {
                        $("#id_deleteQuestion_" + (numberQuestion - 1)).parent().parent().show();
                    }

                    // If we update the question, we must update the database dynamically
                    if (boolUpdate > 0) {

                        $.ajax({
                            url: '.././mod/evoting/ws/ajax_poll.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                idQuestion: idQuestion,
                                idCourse: idCourse,
                                action: 'mdl_deleteQuestion',
                                sesskey: M.cfg.sesskey
                            },
                            success: function (json) {
                                $_blockDelete = false;
                            },
                            error: function (resultat, statut, erreur) {
                            },
                            complete: function (resultat, statut) {
                            }
                        });
                    }
                    //$('html, body').animate({scrollTop: $('#id_questionname_' + (numberQuestion - 1)).offset().top}, 500);
                }
            
        });

    });

}
