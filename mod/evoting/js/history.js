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

M.mod_evoting.history_init = function (Y, graphData) {

	var idCourse;
	var jsonDataGraphic = graphData;
	var viewGraph;
	var optionsGraph;
	var dataGraph;
	var chart;
	var max = 20;
	var sumVote = 0;
	var countOptions = 0;
	var strTooltipStart = "<h2 style='padding-left:10px;padding-right:10px; font-size:1.1em; line-height:20px'>" + M.util.get_string('countvote', 'evoting') + "<p style='font-size:1.5em; line-height:30px; font-weight:600;color:#007cb7;padding-top:5px;' >";

// Load the Visualization API and the chart package.
	google.load("visualization", "1.0", {
		packages: ["corechart"]
	});

// Set a callback to run when the Google Visualization API is loaded.
	google.setOnLoadCallback(drawChart);

	$(function () {

		// Get idCourse
		idCourse = $("#inputIdCourse").val();

		// Resize graph if windows is resize
		$(window).resize(function () {
			updateOptionGraphicMax();
			chart.draw(viewGraph, optionsGraph);
		});

		// hide first select
		$("#menuselectTime option:first").css("display", "none");

		// Sum of vote
		$(".answerCount").each(function () {
			countOptions++;
			sumVote += parseInt($(this).text());
		});

		// Menu historique
		$('#menuselectHistory').on('change', function () {
			var select = $(this).val();
			var idModule = $('#inputIdModule').val();
			var idQuestion = $('#inputIdQuestion').val();
			var url = window.location.pathname + "?id=" + idModule + "&idQ=" + idQuestion + "&ts=" + select;
			window.location.href = url;
		});

		// Click on button delete history
		$("#btnDeleteHistory").click(function () {

			var callbackDialog = confirm(M.util.get_string('confirmDelete', 'evoting'));

			if (callbackDialog == true) {
				var select = $('#menuselectHistory').val();
				deleteHistory(select);
			}
		});
	});

	/**
	 * Delete history selected
	 * @param select
     */
	function deleteHistory(select) {

		$.ajax({
			url: './ws/ajax_poll.php',
			type: 'POST',
			dataType: 'json',
			data: {
				time: select,
				idCourse : idCourse,
                                idQuestion: $('#inputIdQuestion').val(),
				action: 'mdl_delete_history',
				sesskey: M.cfg.sesskey
			},
			success: function (json) {
				var select = 0
				var idModule = $('#inputIdModule').val();
				var idQuestion = $('#inputIdQuestion').val();
				var url = window.location.pathname + "?id=" + idModule + "&idQ=" + idQuestion + "&ts=" + select;
				window.location.href = url;
			},
			error: function (resultat, statut, erreur) {
			},
			complete: function (resultat, statut) {
			}
		});
	}

	/** Google Chart
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
					auraColor: 'rgba(255,255,255,0)',
					opacity: 1,
					x: 20
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
		$(".answerCount").each(function () {
			idOption = $(this).prop('id');
		});
		updateOptionGraphicMax();

		manageGoodAnswer();
	}

	/**
	 * Function to update the design of the graphic in mode maxi
	 */
	function updateOptionGraphicMax() {

		if (countOptions < 8) {
			optionsGraph.vAxis.textStyle.fontSize = 40;
			optionsGraph.annotations.textStyle.fontSize = 18;
		} else {
			optionsGraph.vAxis.textStyle.fontSize = 30;
			optionsGraph.annotations.textStyle.fontSize = 18;
		}
		
		optionsGraph.hAxis.title = M.util.get_string('totalvote', 'evoting') + " : " + sumVote;

	}

	/**
	 * Show the good answer in graphic in green
	 */
	function manageGoodAnswer() {

		var colorBlueAnswer = "#007cb7"
		var colorGoodAnswer = "#007cb7";
		var colorWrongAnswer = "#D8D8D8";
		var cptWrong = 0;

		for (var i = 0; i < dataGraph["Nf"].length; i++) {
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

			cptVoteToDelete = $.trim(dataGraph.getValue(i, 3).substr(dataGraph.getValue(i, 3).indexOf("-")));

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
		}


		// If there is no good answer
		if (cptWrong == dataGraph["Nf"].length) {
			for (var i = 0; i < dataGraph["Nf"].length; i++) {
				dataGraph.setValue(i, 2, colorBlueAnswer);
			}
		}
		chart.draw(viewGraph, optionsGraph);
	}

}