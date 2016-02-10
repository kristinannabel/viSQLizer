/**
 * Function for setting text in empty-table to non-visible, 
 * and shorten the text of all spans who are higher than text-height
 */
function setViewToEmptyTable(thCount){
	for(var i = 0; i < thCount; i++){
		var spanName = ".span_" + i;
		$(".empty-table").find(spanName).css("visibility","hidden");
		$(".empty-table").find(".textOrigin").hide();
		var numOfSpans = $(".empty-table").find(spanName).length;
		for(var j = 0; j < numOfSpans; j++){
			while ($(".empty-table").find(spanName).eq(j).height()>16) {
				// While this element has height grater than 16
		    	$(".empty-table").find(spanName).eq(j).text(function (index, text) {
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
function checkIfDuplicatedData(textContent, duplData, thisGetTextOrigin, rowCount, columnCount){
	var isRightElem = false;
	
	// Column index of this element
	var columnIndexOriginal = thisGetTextOrigin.first().parent().index();
	
	// Column index of this element in empty table
	var columnIndexEmpty = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").index();
	var isNotFirstColumn = false;
	
	if((columnIndexOriginal > 0)&&(columnIndexEmpty > 0)){
		// If this is not the first column in both tables
		isNotFirstColumn = true;
	}
	
	// The previous column's name in empty-table
	var prevEmptyTableColumn = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").parent().parent().children().first().find("th").eq(columnIndexEmpty-1).html();
	// The previous column's name in original-table
	var prevOriginalTableColumn = thisGetTextOrigin.first().parent().parent().parent().children().first().find("th").eq(columnIndexOriginal-1).html();
	
	// If this element is duplicate in original table
	if(duplData > 1){
				while(isRightElem == false){
					if(thisGetTextOrigin.first().parent().is(':first-child')){
						// If this is the first element in this row
						isRightElem = true;
						return $(thisGetTextOrigin[0]).attr("id","animThis").next();
					}
					else if(thisGetTextOrigin.first().parent().parent().has(".usedInRow").length != 0){
						// If this has siblings with class usedInRow, siblings that have been used already on this row
						isRightElem = true;
						return $(thisGetTextOrigin[0]).attr("id","animThis").next();
					}
					else if((!thisGetTextOrigin.first().parent().siblings().find("span:not(#original-span)").hasClass("used")) && ((isNotFirstColumn) && (prevEmptyTableColumn != prevOriginalTableColumn))){
						// If this element is first to be used in row even if it is not first element
						// This test does not work if AS is set on column names in query
						isRightElem = true;
						return $(thisGetTextOrigin[0]).attr("id","animThis").next(); //TEST THIS; SEEMS WRONG IF isNotFirstColumn is false???
					}
					else{
						// This element is not the right one, find next occurence
						thisGetTextOrigin.first().addClass("notInUse");
						var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.notInUse)").filter(function(){
							return $(this).html() === textContent;
						});
					}
				}
			}
	else {
		// If this element is alone in this table, it is the only possible element to choose
		return $(thisGetTextOrigin[0]).attr("id","animThis").next();
	}
}

/*
 * Initial function for the animation and canvas visualizations
 * Is run onload of DOM body
 */
function init() {
	// Do not run if no table is present in DOM
	if($(".streammode-panel").length != 0){
		// Initial settings for the canvas
    	var stage = new createjs.Stage("demoCanvas");
		var canvas = document.getElementById("demoCanvas");
		var table = document.getElementById("main-panel streammode-panel");
		var formDOMElement = new createjs.DOMElement("main-panel streammode-panel");

		// Set canvas size to size of all tables in SQL view
  		stage.canvas.width = formDOMElement.htmlElement.clientWidth + 2 + 200;
		stage.canvas.height = formDOMElement.htmlElement.clientHeight + 2;
		
		// Move the DOMElement at the center of the form
		formDOMElement.regX = table.offsetWidth*0.5+100;
		formDOMElement.regY = table.offsetHeight*0.5;
		// Move the form to right possition
		formDOMElement.x = canvas.width * 0.5;
		formDOMElement.y = canvas.height * -0.50;
		
    	stage.addChild(formDOMElement);
    	stage.update();
		
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
		
		// Lopp through each row in empty-table (the last table in DOM-view with query result)
		for(var i = 0; i < tableRows; i++) (function(i){
			// Loop through each column on this (i) row in empty table
			for(var j = 0; j < tableColumns; j++) (function(j){
				// Set rowCount. +2 because it should start at 1 and because the first row does not contain data
				var rowCount = i + 2;
				// Set columnCount. +1 because it should start at 1
				var columnCount = j + 1;
				
				// Getting the text of this data element in empty-table
				var textContent = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span.textOrigin").html();
				
				// Getting how many elements in this row (in empty-table) is equal to the text of this data-element
				var numOfThisElem = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").length;
				// How many of these elements is not used jet
				var numOfThisElemNotUsed = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:not(.used):contains('"+textContent+"')").length;
				// The index for the element
				var elemNumInRow = numOfThisElem - numOfThisElemNotUsed;
				// Add class used to this element, so it is not used again
				$("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").eq(elemNumInRow).addClass("used");
						
						
				if(elemNumInRow > 0){
					// A similar element has been used before in this row, so do not get the ones marked duplicate or used
					var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.duplicate)").filter(function(){
							return $(this).html() === textContent;
					});
				}
				else {
					// This is the first or only similar element in this row, do not get a used element
					var thisGetTextOrigin = $(".original-table").find("span:not(.used)").filter(function(){
						return $(this).html() === textContent;
					});
				}
						
				var thisTable = thisGetTextOrigin.first().parent().parent().parent().parent();
				var duplData = $(thisTable).find("span:not(.used):contains('"+textContent+"')").length;
				
				// The element to be shown in original table when animation begins. The current element to be animated get id animThis in this function
				var getTextOrigin = checkIfDuplicatedData(textContent, duplData, thisGetTextOrigin, rowCount, columnCount);
						
				// Y position of the element in the original table
				var originalPosY = $(".original-table").find("span#animThis").first().position().top;
				var originalPosX = 	$(".streammode-panel").width();
				// Y position of the element in the empty-table
				var emptyPosY = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().top;
				// X position of the element in the empty-table
				var emptyPosX = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().left;
				
				// If there is more than one original-table
				if(numberOfTables > 1){
					// Get the right table for the element
					var thisTableTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
					var thisTable = "#" + thisTableTemp;
					var orPosX = $(thisTable).find("span#animThis").position().left;
				}
				else {
					var orPosX = $(".original-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span").position().left;
				}
				
				// Calculated position Y from the original position and result position
				var calcPositionY = emptyPosY - originalPosY;
				// Calculated position X from the original position and result position
				var calcPositionX = emptyPosX - orPosX;
				
				var thisDOMElem = $("#animThis:not(.used)").get(0);
				//Create an DOMElement for the canvas
				var textDOM = new createjs.DOMElement(thisDOMElem);
				stage.addChild(textDOM);
			   
				var line = new createjs.Shape();
				line.graphics.setStrokeStyle(3);
				line.graphics.beginStroke("#000000");
				line.graphics.moveTo(originalPosX - 11, originalPosY + (17/2) + 2); // X, Y
				line.graphics.lineTo(600, emptyPosY + (17/2) + 2);
  				stage.addChild(line);
				
				console.log("LINE: " + stage.getChildIndex(line) + "DOM: " + stage.getChildIndex(formDOMElement));
				// If there is more than one original-table
				if(numberOfTables > 1){
					
					// Animate the DOMElement in position set
			  		createjs.Tween.get(textDOM, {loop: false})
					.wait(timeCount).call(tweenStart)
					.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1))
					.to({alpha: 0}, 0, createjs.Ease.getPowIn(1))
					.to({alpha: 1, y: 0, x: 0}).call(tweenComplete);
					// call to dunction tweenComplete after animation is completed
					
					// Show the text in the original-table
					getTextOrigin.show();
				} else {
					// Animate the DOMElement in position set
			  		createjs.Tween.get(textDOM, {loop: false})
					.wait(timeCount).call(tweenStart)
					.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1));
					getTextOrigin.show();
				}
				
				// If there is more than one original-table
				if(numberOfTables > 1){
					var thisTableIdTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
					var thisTableId = "#" +thisTableIdTemp;
					var countOfDuplicates = $(thisTableId).find("span:not(#original-span):contains('"+textContent+"')").length;
					// If there is duplicates of this element in this table
					if(countOfDuplicates > 1){
						$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
					}
					else{
						var duplicatesInAllTables = $(".original-table").find("span:not(.used):contains('"+textContent+"')").length;
						// If there is duplicates of this element in both original-tables together
						if(duplicatesInAllTables > countOfDuplicates){
							//This is a duplicate, mark as one
							$(".original-table").find("span#animThis").first().addClass("duplicate").parent().addClass("usedInRow");
						}
						// remove id animThis from element
						$(".original-table").find("span#animThis").first().removeAttr("id");
					}
				}
				else {
					// Element is used and used in row
					$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
				}
				
				/**
				 * Function for when animation is completed
				 * Shows the hidden text in empty-table
				 */
				function tweenComplete(){
					var emptyTextPlace = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span");
					emptyTextPlace.css("visibility", "visible");
				}
				
				/**
				 * Function for when tween is starting
				 * Sets the background color for the selected row in the original table
				 */
				function tweenStart(){
					$(this.htmlElement.parentElement.parentElement).css("background-color", "#d9edf7");
				}
						
			})(j);
			
			// Counting up the time counter for the animations by 2000ms
			timeCount += 2000;
			
			// Remove the class usedInRow, before starting on a new row
			$(".original-table").find(".usedInRow").removeClass("usedInRow");
		})(i);
		
		// Framework settings for animations
        createjs.Ticker.setFPS(60);
        createjs.Ticker.addEventListener("tick", stage);
	}
}