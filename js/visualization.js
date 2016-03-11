/**
 * Function for setting text in empty-table to non-visible,
 * and shorten the text of all spans who are higher than text-height
 */

function setViewToEmptyTable(thCount) {
	for (var i = 0; i < thCount; i++) {
		var spanName = ".span_" + i;
		$(".empty-table").find(spanName).css("visibility", "hidden");
		$(".empty-table").find(".textOrigin").hide();
		var numOfSpans = $(".empty-table").find(spanName).length;
		for (var j = 0; j < numOfSpans; j++) {
			while ($(".empty-table").find(spanName).eq(j).height() > 16) {
				// While this element has height grater than 16
				$(".empty-table").find(spanName).eq(j).text(function(index, text) {
					return text.replace(/\W*\s(\S)*$/, '...');
					// Cut last word out, replace with '...'
				});
			}
		}
	}
}

/*
 * Function for checking if data is duplicated in the specific original-table
 * Checks if the right element is sent, and not just first occurence of similar element
 */

function checkIfDuplicatedData(textContent, duplData, thisGetTextOrigin, rowCount, columnCount) {
	var isRightElem = false;
	if ((($(".alert-info-decomposer").find("b:contains(ORDER )").length > 0) || ($(".alert-info-decomposer").find("b:contains(GROUP )").length > 0)) && (thisGetTextOrigin.length > 1)) {
		var numberOfColumns = $("#empty-table").find("tr.data:first").find("td").length;
		var countNumOfEquals = 0;
		while(countNumOfEquals != numberOfColumns){
			countNumOfEquals = 0;
			for(var h=0; h < numberOfColumns; h++){
				var thisEmptyText = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td").eq(h).find("span.textOrigin").html();
				var thisOrgText = thisGetTextOrigin.first().parent().parent().find("td").eq(h).find("span.extraSpan").html();
				if(thisEmptyText === thisOrgText){
					countNumOfEquals++;
				}
			}
			if(countNumOfEquals != numberOfColumns){
				thisGetTextOrigin.first().addClass("notInUse");
				var thisGetTextOrigin = $(".original-table").find("span.extraSpan:not(.notInUse)").filter(function() {
					return $(this).html() === textContent;
				});
			}
		}
	}
	else if($(".alert-info-decomposer:contains(*)").length == 0){
		var numberOfColumns = $("#empty-table").find("tr.data:first").find("td").length;
		var countNumOfEquals = 0;
		while(countNumOfEquals != numberOfColumns){
			countNumOfEquals = 0;
			for(var h=0; h < numberOfColumns; h++){
				var thisEmptyText = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td").eq(h).find("span.textOrigin").html();
				var thisEmptyColumnName = $("#empty-table").find("tr").first().find("th").eq(h).text();
				var thisEmptyIdName = $("#empty-table").find("tr").first().find("th").eq(h).attr("id");
				var thisOrgText = thisGetTextOrigin.first().parent().parent().find("td").find("span.extraSpan:not(.notInUse)").filter(function() {
					return $(this).html() === thisEmptyText;
				});
				var jumpOut = false;
				while(!jumpOut){
					if(thisOrgText.length == 0){
						jumpOut = true;
						thisOrgText.first().addClass("notInUse");
						var thisOrgText = thisGetTextOrigin.first().parent().parent().find("td").find("span.extraSpan:not(.notInUse)").filter(function() {
							return $(this).html() === thisEmptyText;
						});
					}
					else {
						var thisOrgIndex = thisOrgText.parent().index();
						var thisOrgColumnHead = thisOrgText.parent().parent().parent().find("tr").first().find("th").eq(thisOrgIndex);
						if(thisOrgColumnHead.hasClass(thisEmptyColumnName) && thisOrgColumnHead.attr("id") == thisEmptyIdName){
							//right element
							countNumOfEquals++;
							jumpOut = true;
						}
						else{
							//get next element
							thisOrgText.first().addClass("notInUse");
							var thisOrgText = thisGetTextOrigin.first().parent().parent().find("td").find("span.extraSpan:not(.notInUse)").filter(function() {
								return $(this).html() === thisEmptyText;
							});
						}
					}
				}
			}
			if(countNumOfEquals != numberOfColumns){
				thisGetTextOrigin.first().addClass("notInUse");
				var thisGetTextOrigin = $(".original-table").find("span.extraSpan:not(.used):not(.notInUse)").filter(function() {
					return $(this).html() === textContent;
				});
			}
		}
	}

	// Column index of this element
	var columnIndexOriginal = thisGetTextOrigin.first().parent().index();

	// Column index of this element in empty table
	var columnIndexEmpty = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").index();
	var isNotFirstColumn = false;

	if ((columnIndexOriginal > 0) && (columnIndexEmpty > 0)) {
		// If this is not the first column in both tables
		isNotFirstColumn = true;
	}

	// If this element is duplicate in original table
	//if(duplData > 1){
	while (isRightElem == false) {
		// The previous column's name in empty-table
		var prevEmptyTableColumn = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").parent().parent().children().first().find("th").eq(columnIndexEmpty - 1).text();
		// The previous column's name in original-table
		var prevOriginalTableColumn = thisGetTextOrigin.first().parent().parent().parent().children().first().find("th").eq(columnIndexOriginal - 1).text();
		var thisOriginalIndex = thisGetTextOrigin.first().parent().index();
		var thisEmptyIndex = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").index();
		
		var thisEmptyId = $("#empty-table").find("tr").first().find("th").eq(thisEmptyIndex).attr("id");
		var thisOriginalId = thisGetTextOrigin.first().parent().parent().parent().find("tr").first().find("th").eq(thisOriginalIndex).attr("id");
		if (((thisGetTextOrigin.first().parent().is(':first-child')) && (thisGetTextOrigin.first().parent().parent().parent().has(".usedInRow").length == 0) && (thisEmptyId == thisOriginalId)) || ((thisEmptyId == thisOriginalId) && (thisGetTextOrigin.first().parent().parent().parent().find("tr").has(".usedInRow").length == 0) && (thisGetTextOrigin.first().parent().parent().parent().find("tr").first().find("th").eq(thisOriginalIndex).text() === $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").parent().parent().find("th").eq(thisEmptyIndex).text()))) {
			// If this is the first element in this row
			isRightElem = true;
			return $(thisGetTextOrigin[0]).attr("id", "animThis").next();
		} else if ((thisGetTextOrigin.first().parent().parent().has(".usedInRow").length != 0) && (!thisGetTextOrigin.first().parent().hasClass("usedInRow")) && (thisEmptyId == thisOriginalId)) {
			// If this has siblings with class usedInRow, siblings that have been used already on this row
			isRightElem = true;
			return $(thisGetTextOrigin[0]).attr("id", "animThis").next();
		} else if ((!thisGetTextOrigin.first().parent().siblings().find("span:not(#original-span)").hasClass("used")) && ((isNotFirstColumn) && (thisEmptyId == thisOriginalId) && (prevEmptyTableColumn != prevOriginalTableColumn) && (!thisGetTextOrigin.first().parent().parent().parent().find("tr").has(".usedInRow").find("td").eq(thisOriginalIndex).hasClass("usedInRow")) && (thisGetTextOrigin.first().parent().parent().parent().find("tr").first().find("th").eq(thisOriginalIndex).text() === $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").parent().parent().find("th").eq(thisEmptyIndex).text()))) {
			// If this element is first to be used in row even if it is not first element
			// This test does not work if AS is set on column names in query
			isRightElem = true;
			return $(thisGetTextOrigin[0]).attr("id", "animThis").next();
		} else {
			// This element is not the right one, find next occurence
			thisGetTextOrigin.first().addClass("notInUse");
			var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.notInUse)").filter(function() {
				return $(this).html() === textContent;
			});
		}
	}
	//}
	/*else {
		// If this element is alone in this table, it is the only possible element to choose
		return $(thisGetTextOrigin[0]).attr("id","animThis").next();
	}*/
}

function setSpanWidth() {
	for (var a = 0; a < $(".original-table").length; a++) {
		var columnCount = $(".original-table").eq(a).find("tr").first().find("th").length;
		for (var b = 0; b < columnCount; b++) {
			var columnWidth = $(".original-table").eq(a).find("tr").first().find("th").eq(b).outerWidth();
			numOfRows = $(".original-table").eq(a).find("tr").length;
			for (var c = 1; c < numOfRows; c++) {
				if ($(".original-table").eq(a).find("tr").eq(c).find("td").eq(b).find(".extraSpan").outerWidth() > columnWidth) {
					$(".original-table").eq(a).find("tr").eq(c).find("td").eq(b).find(".extraSpan").css("width", columnWidth - 10);
				}
			}
		}
	}
}

/*
 * Initial function for the animation and canvas visualizations
 * Is run onload of DOM body
 */

function init() {
	// Do not run if no table is present in DOM
	if ($(".streammode-panel").length != 0) {
		$lastElemIsDone = false;
		$arrowThisDone = false;
		$isLast = false;
		// Initial settings for the canvas
		var stage = new createjs.Stage("demoCanvas");
		var canvas = document.getElementById("demoCanvas");
		ctx = canvas.getContext('2d');
		var table = document.getElementById("main-panel streammode-panel");
		var formDOMElement = new createjs.DOMElement("main-panel streammode-panel");

		// Set canvas size to size of all tables in SQL view
		stage.canvas.width = formDOMElement.htmlElement.clientWidth + 2 + 200;
		stage.canvas.height = formDOMElement.htmlElement.clientHeight + 2;

		// Move the DOMElement at the center of the form
		formDOMElement.regX = table.offsetWidth * 0.5 + 100;
		formDOMElement.regY = table.offsetHeight * 0.5;
		// Move the form to right possition
		formDOMElement.x = canvas.width * 0.5;
		formDOMElement.y = canvas.height * -0.50;

		stage.addChild(formDOMElement);
		//stage.update();

		$("#main-panel#streammode-panel").css("z-index", "1");

		var thCount = $(".empty-table").children('tbody').find('tr').first().find("th").length;
		setViewToEmptyTable(thCount);

		// Number of tables in SQL view
		var numberOfTables = $(".original-table").length;
		// Number of rows in empty-table
		var tableRows = $("#empty-table").find("tr.data").length;
		// Number of columns in empty table
		var tableColumns = $("#empty-table").find("tr.data:first").find("td").length;
		//Inital timecount - ms wait before the animation starts on page-load
		var timeCount = 1500;
		$zIndexNum = tableRows * tableColumns;
		// Lopp through each row in empty-table (the last table in DOM-view with query result)
		for (var i = 0; i < tableRows; i++)(function(i) {
			// Loop through each column on this (i) row in empty table
			for (var j = 0; j < tableColumns; j++)(function(j) {

				// Set rowCount. +2 because it should start at 1 and because the first row does not contain data
				var rowCount = i + 2;
				// Set columnCount. +1 because it should start at 1
				var columnCount = j + 1;

				// Getting the text of this data element in empty-table
				var textContent = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span.textOrigin").html();
				
				// Getting how many elements in this row (in empty-table) is equal to the text of this data-element
				var numOfThisElem = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("span.textOrigin").filter(function() {
    				return $(this).text() === textContent;
				}).length;
				// How many of these elements is not used jet
				var numOfThisElemNotUsed = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("span.textOrigin:not(.used)").filter(function() {
    				return $(this).text() === textContent;
				}).length;
				// The index for the element
				var elemNumInRow = numOfThisElem - numOfThisElemNotUsed;
				// Add class used to this element, so it is not used again
				$("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span.textOrigin:contains('" + textContent + "')").addClass("used");

				var thisGetTextOrigin = $(".original-table").find("span:not(.used)").filter(function() {
					return $(this).html() === textContent;
				});

				var thisTable = thisGetTextOrigin.first().parent().parent().parent().parent();
				var duplData = $(thisTable).find("span:not(.used):contains('" + textContent + "')").length;
				// The element to be shown in original table when animation begins. The current element to be animated get id animThis in this function
				var getTextOrigin = checkIfDuplicatedData(textContent, duplData, thisGetTextOrigin, rowCount, columnCount);
				// Y position of the element in the original table
				var originalPosY = $(".original-table").find("span#animThis").first().position().top;
				// X position of the lement in the original table
				var orgPosXElem = $(".original-table").find("span#animThis").first().position().left;
				var originalPosX = $(".streammode-panel").width();
				// Y position of the element in the empty-table
				var emptyPosY = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span:not(.textOrigin)").position().top;
				// X position of the element in the empty-table
				var emptyPosX = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span:not(.textOrigin)").position().left;

				// If there is more than one original-table
				if (numberOfTables > 1) {
					// Get the right table for the element
					var thisTableTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
					var thisTable = "#" + thisTableTemp;
					var orPosX = $(thisTable).find("span#animThis").position().left;
				} else {
					var orPosX = $(".original-table").find("span#animThis").position().left;
				}

				// Calculated position Y from the original position and result position
				var calcPositionY = emptyPosY - originalPosY;
				// Calculated position X from the original position and result position
				var calcPositionX = emptyPosX - orPosX;

				var thisDOMElem = $("#animThis").get(0);
				if (localStorage['bigtext'] == "true") {
					var animThisIndex = $("#animThis:not(.used)").parent().index();
					/*if (animThisIndex > 0) {
						if ($("#animThis:not(.used)").parent().parent().find(".usedInRow").length > 0) {
							var prevElementXPos = $("#animThis:not(.used)").parent().parent().find(".usedInRow").last().find("span").first().position().left;
							var $clone = $("#animThis:not(.used)").parent().parent().find(".usedInRow").last().find("span").first().clone().css({
								width: "auto",
								display: "none"
							}).appendTo($("#animThis:not(.used)").parent().parent().find(".usedInRow").last().find("span").first().parent());
							var prevElementWidth = $clone.outerWidth();
							$clone.remove();
						}
					}*/
					if ((animThisIndex > 0) && ((($prevCalcPositionX + $prevOrgPosXElem) + ($prevOrgWidth * 2)) >= (orgPosXElem + calcPositionX))) {
						var changeXPosition = $prevChangeXPosition + ($prevOrgWidth * 1.5) - $prevOrgWidth;
					} else {
						var changeXPosition = 0;
					}
					$prevCalcPositionX = calcPositionX;
					$prevOrgPosXElem = orgPosXElem;
					$prevChangeXPosition = changeXPosition;
					var $clone2 = $("#animThis").first().clone().css({
						width: "auto",
						display: "none"
					}).appendTo($("#animThis").parent());
					$prevOrgWidth = $clone2.outerWidth();
					$clone2.remove();
					
				}

				setSpanWidth();
				//Create an DOMElement for the canvas
				var textDOM = new createjs.DOMElement(thisDOMElem);
				stage.addChild(textDOM);

				animateTextFromDOM(localStorage['bigtext'], localStorage['dragout'], changeXPosition);

				function animateTextFromDOM(bigText, dragOut, changeXPosition) {
					var scaleX = 1;
					var scaleY = 1;
					var scaleTime = 0;
					var newTimeCount = 0;
					var calcScalePositionX = calcPositionX;
					if (bigText == "true") {
						scaleX = 1.5;
						scaleY = 1.5;
						scaleTime = 300;
						newTimeCount = 600;
						if (changeXPosition > 0) {
							if ((($prevOrgPosXElem + $prevOrgWidth) > orPosX) && ($prevOrgPosXElem < orPosX)) {
								changeXPosition = $prevOrgWidth - $prevOrgPosXElem + 30;
							}
							calcPositionX = calcPositionX + changeXPosition;
							//calcScalePositionX = calcPositionX;
						}
					}
					if (dragOut == "true") {
						createjs.Tween.get(textDOM, {
							loop: false
						})
							.wait(timeCount - newTimeCount).call(tweenStart)
							.to({
								scaleX: scaleX,
								scaleY: scaleY,
								x: changeXPosition
							}, scaleTime)
							.to({
								x: calcPositionX + 300
							}, 300, createjs.Ease.getPowIn(1))
							.to({
								y: calcPositionY
							}, 900, createjs.Ease.getPowIn(1))
							.to({
								x: calcPositionX
							}, 300, createjs.Ease.getPowIn(1))
						//.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1))
						.to({
							scaleX: 1,
							scaleY: 1,
							x: calcScalePositionX
						}, scaleTime)
							.to({
								alpha: 0
							}, 0, createjs.Ease.getPowIn(1))
							.to({
								alpha: 1,
								y: 0,
								x: 0
							}).call(tweenComplete);
						getTextOrigin.show();
						// call to function tweenComplete after animation is completed
					} else {
						createjs.Tween.get(textDOM, {
							loop: false
						})
							.wait(timeCount - newTimeCount).call(tweenStart)
							.to({
								scaleX: scaleX,
								scaleY: scaleY,
								x: changeXPosition
							}, scaleTime)
							.to({
								y: calcPositionY,
								x: calcPositionX
							}, 1500, createjs.Ease.getPowIn(1))
							.to({
								scaleX: 1,
								scaleY: 1,
								x: calcScalePositionX
							}, scaleTime)
							.to({
								alpha: 0
							}, 0, createjs.Ease.getPowIn(1))
							.to({
								alpha: 1,
								y: 0,
								x: 0
							}).call(tweenComplete);
						getTextOrigin.show();
					}
				}
				// If there is more than one original-table
				if (numberOfTables > 1) {
					var thisTableIdTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
					var thisTableId = "#" + thisTableIdTemp;
					var countOfDuplicates = $(thisTableId).find("span:not(#original-span)").filter(function() {
    					return $(this).text() === textContent;
					}).length;

					// If there is duplicates of this element in this table
					if ((countOfDuplicates > 1) && ($(".empty-table").find("tr").length <= $(thisTableId).find("tr").length)) {
						/*if($(".original-table").find("span#animThis").parent().parent().find(".usedInRow").length > 0){
							$(".original-table").find("span#animThis").removeAttr("id").parent().addClass("usedInRow").addClass("usedInRow_" + i);
						}
						else {*/
						$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow").addClass("usedInRow_" + i);
						//}
					} else {
						var duplicatesInAllTables = $(".original-table").find("span:not(.used)").filter(function() {
    						return $(this).text() === textContent;
						}).length;
						// If there is duplicates of this element in both original-tables together
						if (duplicatesInAllTables > countOfDuplicates) {
							//This is a duplicate, mark as one
							var thisTable = $(".original-table").find("span#animThis").first().parent().parent().parent().parent();
							var thisRowCount = $(thisTable).find("tr#data").length;
							var numOfReps = tableRows/thisRowCount;
							if (($(".alert-info-decomposer").find("b:contains(ON)").length == 0) && ($(".alert-info-decomposer").find("b:contains(JOIN)").length > 0)) {
								//If Cartesian product
								if(typeof $(".original-table").find("span#animThis").first().attr("value") === 'undefined'){
									$(".original-table").find("span#animThis").first().attr("value", "0")
								}
								var numUsed = parseInt($(".original-table").find("span#animThis").first().attr("value"));
								if(numUsed < numOfReps){
									if(duplicatesInAllTables - countOfDuplicates > countOfDuplicates){
										$(".original-table").find("span#animThis").first().addClass("duplicate");
									}
									numUsed = numUsed+1;
									$(".original-table").find("span#animThis").first().attr("value", numUsed);
									if(numUsed == numOfReps){
										$(".original-table").find("span#animThis").first().addClass("used");
									}
								}
								else {
									$(".original-table").find("span#animThis").first().addClass("used");
								}
							}
							else {
								$(".original-table").find("span#animThis").first().addClass("duplicate");
							}
						}
						else if (($(".alert-info-decomposer").find("b:contains(ON)").length == 0) && ($(".alert-info-decomposer").find("b:contains(JOIN)").length > 0)) {
							//If Cartesian product
							var thisTable = $(".original-table").find("span#animThis").first().parent().parent().parent().parent();
							var thisRowCount = $(thisTable).find("tr#data").length;
							var numOfReps = tableRows/thisRowCount;
							if(typeof $(".original-table").find("span#animThis").first().attr("value") === 'undefined'){
								$(".original-table").find("span#animThis").first().attr("value", "0")
							}
							var numUsed = parseInt($(".original-table").find("span#animThis").first().attr("value"));
							if(numUsed < numOfReps){
								numUsed = numUsed+1;
								$(".original-table").find("span#animThis").first().attr("value", numUsed);
								if(numUsed == numOfReps){
									$(".original-table").find("span#animThis").first().addClass("used");
								}
							}
							else {
								$(".original-table").find("span#animThis").first().addClass("used");
							}
						}
						// remove id animThis from element
						$(".original-table").find("span#animThis").first().removeAttr("id").parent().addClass("usedInRow").addClass("usedInRow_" + i);
					}
				} else {
					// Element is used and used in row
					$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow").addClass("usedInRow_" + i);
				}

				/**
				 * Function for when animation is completed
				 * Shows the hidden text in empty-table
				 */

				function tweenComplete() {
					setSpanWidth();
					$(this.htmlElement).html($(this.htmlElement).next().html());

					var emptyTextPlace = $("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span");
					emptyTextPlace.css("visibility", "visible"); //Check quick away and back
					$(this.htmlElement).removeClass("whiteBrick");
					$(this.htmlElement).css("z-index", 1);
					$(this.htmlElement.parentElement).removeClass("usedBlue");
				}

				/**
				 * Function for when tween is starting
				 * Sets the background color for the selected row in the original table
				 */

				function tweenStart() {
					$(this.htmlElement).css("width", "auto");
					if ($("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span:not(.textOrigin):contains('...')").length > 0) {
						$(this.htmlElement).html($("#empty-table").find("tr:nth-child(" + rowCount + ")").find("td:nth-child(" + columnCount + ")").find("span:not(.textOrigin):contains('...')").html());
					}
					$(this.htmlElement).addClass("whiteBrick").css("z-index", $zIndexNum);

					if ($(".alert-info-decomposer").find("b:contains(WHERE)").length > 0) {
						var numOfWheres = $(".original-table").find(".where").length;
						for (var e = 0; e < numOfWheres; e++) {
							var whereIndex = $(".original-table").find(".where").eq(e).index();
							if ($(this.htmlElement.parentElement).index() == whereIndex) {
								$(this.htmlElement.parentElement).addClass("usedWhere");
							}
						}
					} else {
						$(this.htmlElement.parentElement).addClass("usedBlue");
					}

					if ($(".alert-info-decomposer").find("b:contains(ON)").length > 0) {
						var numOfTables = $(".original-table").length;
						for (var t = 0; t < numOfTables; t++) {
							var numOfOns = $(".original-table").eq(t).find(".onColumn").length;
							for (var e = 0; e < numOfOns; e++) {
								var onIndex = $(".original-table").eq(t).find(".onColumn").eq(e).index();
								if (($(this.htmlElement.parentElement).index() == onIndex) && ($(this.htmlElement.parentElement.parentElement.parentElement.parentElement).is(".original-table:eq(" + t + ")"))) {
									$(this.htmlElement.parentElement).addClass("usedOn");
								}
							}
						}

					} else {
						$(this.htmlElement.parentElement).addClass("usedBlue");
					}
					if (($(".alert-info-decomposer").find("b:contains(ORDER )").length > 0) || ($(".alert-info-decomposer").find("b:contains(GROUP )").length > 0)) {
						var numOfOrderBy = $(".original-table").find(".orderByColumn").length;
						for (var e = 0; e < numOfOrderBy; e++) {
							var orderIndex = $(".original-table").find(".orderByColumn").eq(e).index();
							if ($(this.htmlElement.parentElement).index() == orderIndex) { //check on name of column as well for error prevention
								$(this.htmlElement.parentElement).addClass("usedOrderBy");
							}
						}
					} else {
						$(this.htmlElement.parentElement).addClass("usedBlue");
					}


					if (!$(this.htmlElement.parentElement).hasClass("usedOn")) {
						$(this.htmlElement.parentElement).addClass("usedBlue");
					}
					$zIndexNum = $zIndexNum - 1;
				}

			})(j);

			// Counting up the time counter for the animations by 2000ms

			if (localStorage['dragout'] == 'false') {
				//Plugin for creating a guide for animating arrows in an arc
				createjs.MotionGuidePlugin.install();
				var originalPosY = $(".original-table").find(".usedInRow").find("span").not("#original-span").first().position().top;
				var originalPosX = $(".streammode-panel").width();
				var emptyPosY = $("#empty-table").find("tr:nth-child(" + (i + 2) + ")").find("span:not(.textOrigin)").position().top;

				var arrowStartPosY = originalPosY + (17 / 2) + 2;
				var arrowStartPosX = originalPosX - 11;
				var arrowEndPosY = emptyPosY + (17 / 2) + 2;
				var middlePosY = ((arrowEndPosY - arrowStartPosY) / 2) + arrowStartPosY;

				var shape = new createjs.Shape();
				var bar = {
					x: arrowStartPosX,
					y: arrowStartPosY,
					oldx: arrowStartPosX,
					oldy: arrowStartPosY
				};
				stage.addChild(shape);

				var arrowz = new createjs.Shape();
				stage.addChild(arrowz);

				createjs.Ticker.addEventListener("tick", tick);

				run();

				function getMotionPathFromPoints(points) {
					var i, motionPath;
					for (i = 0, motionPath = []; i < points.length - 1; ++i) {
						if (i === 0) {
							motionPath.push(points[i].x, points[i].y);
						} else if (i === 1) {
							motionPath.push(points[i].x, points[i].y, points[i + 1].x, points[i + 1].y);
						} else {
							i++;
							motionPath.push(points[i].x, points[i].y, points[i + 1].x, points[i + 1].y);
						}
					}

					return motionPath;
				}

				function run() {
					var posXStart = arrowStartPosX + 200;
					var points = [
						new createjs.Point(arrowStartPosX, arrowStartPosY),
						new createjs.Point(posXStart, middlePosY),
						new createjs.Point(arrowStartPosX, arrowEndPosY)
					];

					if (localStorage['bigtext'] == "false") {
						createjs.Tween.get(bar).wait(timeCount).to({
							guide: {
								path: getMotionPathFromPoints(points)
							}
						}, 1500).call(lastArrowAnimateComplete);
					} else {
						createjs.Tween.get(bar).wait(timeCount - 600).to({
							guide: {
								path: getMotionPathFromPoints(points)
							}
						}, 2100).call(lastArrowAnimateComplete);
					}
				}



				function drawFilledPolygon(shape, arrowzShape) {
					arrowzShape.graphics
						.clear()
						.beginFill("#000000")
						.moveTo(shape[0][0], shape[0][1]);
					for (p in shape) {
						if (p > 0) {
							arrowzShape.graphics.lineTo(shape[p][0], shape[p][1]);
						}
					}
					arrowzShape.graphics.lineTo(shape[0][0], shape[0][1])
						.endStroke();

				};

				function translateShape(shape, x, y) {
					var rv = [];
					for (p in shape)
						rv.push([shape[p][0] + x, shape[p][1] + y]);
					return rv;
				};

				function rotateShape(shape, ang) {
					var rv = [];
					for (p in shape)
						rv.push(rotatePoint(ang, shape[p][0], shape[p][1]));
					return rv;
				};

				function rotatePoint(ang, x, y) {
					return [
						(x * Math.cos(ang)) - (y * Math.sin(ang)), (x * Math.sin(ang)) + (y * Math.cos(ang))
					];
				};

				var arrow = [
					[2, 0],
					[-10, -4],
					[-10, 4]
				];

				function tick() {
					if (!$lastElemIsDone) {


						shape.graphics
							.setStrokeStyle(2, 'round', 'round')
							.beginStroke("#000000")
							.curveTo(bar.oldx, bar.oldy, bar.x, bar.y)
							.endStroke();
						var ang = Math.atan2(bar.y - bar.oldy, bar.x - bar.oldx);
						if (bar.y.toFixed(1) != arrowEndPosY.toFixed(1) && bar.x.toFixed(1) != arrowStartPosX.toFixed(1) && bar.oldx.toFixed(1) != arrowStartPosX.toFixed(1) && bar.oldy.toFixed(1) != arrowEndPosY.toFixed(1)) {
							drawFilledPolygon(translateShape(rotateShape(arrow, ang), bar.x, bar.y), arrowz);

						}

						if (typeof shape2 !== 'undefined') {
							shape2.graphics
								.setStrokeStyle(2, 'round', 'round')
								.beginStroke("#000000")
								.curveTo(bahar.oldx, bahar.oldy, bahar.x, bahar.y)
								.endStroke();
							var ang = Math.atan2(bahar.y - bahar.oldy, bahar.x - bahar.oldx);
							if (bahar.y.toFixed(1) != (originalPosY + (17 / 2) + 2).toFixed(1) && bahar.x.toFixed(1) != arrowStartPosX.toFixed(1) && bahar.oldx.toFixed(1) != arrowStartPosX.toFixed(1) && bahar.oldy.toFixed(1) != (originalPosY + (17 / 2) + 2).toFixed(1)) {
								drawFilledPolygon(translateShape(rotateShape(arrow, ang), bahar.x, bahar.y), arrowz2);
							}

							bahar.oldx = bahar.x;
							bahar.oldy = bahar.y;
						}


						//stage.update();

						bar.oldx = bar.x;
						bar.oldy = bar.y;
						if ($arrowThisDone) {
							$arrowThisDone = false;
						}
					}
				}

				function removeShapeFromStage() {
					stage.removeChild(shape);
					stage.removeChild(arrowz);
				}

				function removeShape2FromStage() {
					stage.removeChild(shape2);
					stage.removeChild(arrowz2);
				}

				function lastArrowAnimateComplete() {
					$arrowThisDone = true;
					if (localStorage['bigtext'] == "false") {
						createjs.Tween.get(shape).to({
							alpha: 0
						}, 3000).call(removeShapeFromStage);
						createjs.Tween.get(arrowz).to({
							alpha: 0
						}, 3000);
						if (numberOfTables === 2) {
							createjs.Tween.get(shape2).to({
								alpha: 0
							}, 3000).call(removeShape2FromStage);
							createjs.Tween.get(arrowz2).to({
								alpha: 0
							}, 3000);
						}
						if (i == (tableRows - 1)) {
							$lastElemIsDone = true;

						}
					} else {
						createjs.Tween.get(shape).to({
							alpha: 0
						}, 3500).call(removeShapeFromStage);
						createjs.Tween.get(arrowz).to({
							alpha: 0
						}, 3000);
						if (numberOfTables === 2) {
							createjs.Tween.get(shape2).to({
								alpha: 0
							}, 3500).call(removeShape2FromStage);
							createjs.Tween.get(arrowz2).to({
								alpha: 0
							}, 3000);
						}
						if (i == (tableRows - 1)) {
							$lastElemIsDone = true;

						}
					}

				}

				if (numberOfTables === 2) {
					originalPosY = $(".original-table").find(".usedInRow").find("span").not("#original-span").last().position().top;

					arrowStartPosY = originalPosY + (17 / 2) + 2;
					var middlePosY = ((arrowEndPosY - arrowStartPosY) / 2) + arrowStartPosY;

					var shape2 = new createjs.Shape();
					var bahar = {
						x: arrowStartPosX,
						y: arrowStartPosY,
						oldx: arrowStartPosX,
						oldy: arrowStartPosY
					};
					stage.addChild(shape2);

					var arrowz2 = new createjs.Shape();
					stage.addChild(arrowz2);

					//createjs.Ticker.addEventListener("tick", tick);

					function getMotionPathFromPoints2(points) {
						var i, motionPath;
						for (i = 0, motionPath = []; i < points.length - 1; ++i) {
							if (i === 0) {
								motionPath.push(points[i].x, points[i].y);
							} else if (i === 1) {
								motionPath.push(points[i].x, points[i].y, points[i + 1].x, points[i + 1].y);
							} else {
								i++;
								motionPath.push(points[i].x, points[i].y, points[i + 1].x, points[i + 1].y);
							}
						}

						return motionPath;
					}

					function runner() {
						var posXStart = arrowStartPosX + 200;
						var points = [
							new createjs.Point(arrowStartPosX, arrowStartPosY),
							new createjs.Point(posXStart, middlePosY),
							new createjs.Point(arrowStartPosX, arrowEndPosY)
						];

						if (localStorage['bigtext'] == "false") {
							createjs.Tween.get(bahar).wait(timeCount).to({
								guide: {
									path: getMotionPathFromPoints2(points)
								}
							}, 1500).call(lastArrowAnimateComplete);
						} else {
							createjs.Tween.get(bahar).wait(timeCount - 400).to({
								guide: {
									path: getMotionPathFromPoints2(points)
								}
							}, 1900).call(lastArrowAnimateComplete);
						}
					}

					runner();

				}
			}
			// Remove this for animating all at once!
			if (localStorage['animation'] == "normal") {
				timeCount += 2200;
			}
			// Remove the class usedInRow, before starting on a new row
			$(".original-table").find(".usedInRow").removeClass("usedInRow");
			$(".original-table").find(".notInUse").removeClass("notInUse");
		})(i);

		// Framework settings for animations
		createjs.Ticker.setFPS(30);
		createjs.Ticker.addEventListener("tick", stage);
		//createjs.Ticker.reset();
	}
}

function stepClicked() {
	createjs.Tween.removeAllTweens();
}
