function init() {
	if($(".streammode-panel").length != 0){
		
    	var stage = new createjs.Stage("demoCanvas");
		var canvas = document.getElementById("demoCanvas");
		var table = document.getElementById("main-panel streammode-panel");
		var formDOMElement = new createjs.DOMElement("main-panel streammode-panel");

  		stage.canvas.width = formDOMElement.htmlElement.clientWidth + 2;
		stage.canvas.height = formDOMElement.htmlElement.clientHeight + 2;
		//move it's rotation center at the center of the form
		formDOMElement.regX = table.offsetWidth*0.5;
		formDOMElement.regY = table.offsetHeight*0.5;
		//move the form above the screen
		formDOMElement.x = canvas.width * 0.5;
		formDOMElement.y = canvas.height * -0.50;

    	stage.addChild(formDOMElement);
    	stage.update();
  		var thCount = $(".empty-table").children('tbody').find('tr').first().find("th").length;
		for(var i = 0; i < thCount; i++){
			var spanName = ".span_" + i;
			$(".empty-table").find(spanName).css("visibility","hidden");
			$(".empty-table").find(".textOrigin").hide();
			var numOfSpans = $(".empty-table").find(spanName).length;
			for(var j = 0; j < numOfSpans; j++){
				while ($(".empty-table").find(spanName).eq(j).height()>16) {
			    	$(".empty-table").find(spanName).eq(j).text(function (index, text) {
			        	return text.replace(/\W*\s(\S)*$/, '...');
			    	});
				}
			}			
		}
		var numberOfTables = $(".original-table").length;
		var tableRows = $("#empty-table").find("tr.data").length;
		var tableColumns = $("#empty-table").find("tr.data:first").find("td").length;
		var timeCount = 1500;
		for(var i = 0; i < tableRows; i++) (function(i){ //for each row
			for(var j = 0; j < tableColumns; j++) (function(j){ //for each column in one row
				var rowCount = i + 2;
				var columnCount = j + 1;
				var textContent = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span.textOrigin").html();
				var emptyContains = $("#empty-table").find("span:contains('"+textContent+"')").length;
						
				var numOfThisElem = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").length;
				var numOfThisElemNotUsed = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:not(.used):contains('"+textContent+"')").length;
				var elemNumInRow = numOfThisElem - numOfThisElemNotUsed;
				$("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").eq(elemNumInRow).addClass("used");
						
				if(elemNumInRow > 0){
							var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.duplicate)").filter(function(){
									return $(this).html() === textContent;
							});
						}
				else {
							var thisGetTextOrigin = $(".original-table").find("span:not(.used)").filter(function(){
									return $(this).html() === textContent;
							});
						}
						
				var isRightElem = false;
				var thisTable = thisGetTextOrigin.first().parent().parent().parent().parent();
				var duplData = $(thisTable).find("span:not(.used):contains('"+textContent+"')").length;
				var columnIndexOriginal = thisGetTextOrigin.first().parent().index();
				var columnIndexEmpty = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").index();
				var isNotFirstColumn = false;
				if((columnIndexOriginal > 0)&&(columnIndexEmpty > 0)){
							isNotFirstColumn = true;
						}
						
				var nextEmptyTableColumn = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").parent().parent().children().first().find("th").eq(columnIndexEmpty-1).html();
				var nextOriginalTableColumn = thisGetTextOrigin.first().parent().parent().parent().children().first().find("th").eq(columnIndexOriginal-1).html();
						
				if(duplData > 1){
							while(isRightElem == false){
								if(thisGetTextOrigin.first().parent().is(':first-child')){
									var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
									isRightElem = true;
								}
								else if(thisGetTextOrigin.first().parent().parent().has(".usedInRow").length != 0){
									var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
									isRightElem = true;
								}
								else if((!thisGetTextOrigin.first().parent().siblings().find("span:not(#original-span)").hasClass("used")) && ((isNotFirstColumn) && (nextEmptyTableColumn != nextOriginalTableColumn))){
									//This element is first to be used in row even if it is not first element
									var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
									isRightElem = true;
								}
								else{
									thisGetTextOrigin.first().addClass("notInUse");
									var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.notInUse)").filter(function(){
										return $(this).html() === textContent;
									});
								}
							}
						}
				else {
					var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
				}
						
				var originalPosY = $(".original-table").find("span#animThis").first().position().top;
						
				var emptyPosY = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().top;
				var emptyPosX = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().left;
				if(numberOfTables > 1){
							var thisTableTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
							var thisTable = "#" + thisTableTemp;
							var orPosX = $(thisTable).find("span#animThis").position().left;
						}
				else {
							var orPosX = 									$(".original-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span").position().left;
						}
				var calcPositionY = emptyPosY - originalPosY;
				var calcPositionX = emptyPosX - orPosX;
				var thisDOMElem = $("#animThis:not(.used)").get(0);
				var textDOM = new createjs.DOMElement(thisDOMElem);
				stage.addChild(textDOM);
				if(numberOfTables > 1){
							getTextOrigin.show();
			  		  		createjs.Tween.get(textDOM, {loop: false})
							.wait(timeCount).call(tweenStart)
							.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1))
							.to({alpha: 0}, 0, createjs.Ease.getPowIn(1))
							.to({alpha: 1, y: 0, x: 0}).call(tweenComplete);
						} else {
			  		  		createjs.Tween.get(textDOM, {loop: false})
							.wait(timeCount).call(tweenStart)
							.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1));
							//console.log($("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span").position());
							getTextOrigin.show();
						}
						
				if(numberOfTables > 1){
							var thisTableIdTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
							var thisTableId = "#" +thisTableIdTemp;
							var countOfDuplicates = $(thisTableId).find("span:not(#original-span):contains('"+textContent+"')").length;
							if(countOfDuplicates > 1){
								$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
							}else{
								var duplicatesInAllTables = $(".original-table").find("span:not(.used):contains('"+textContent+"')").length;
								if(duplicatesInAllTables > countOfDuplicates){
									$(".original-table").find("span#animThis").first().addClass("duplicate").parent().addClass("usedInRow");
								}
								$(".original-table").find("span#animThis").first().removeAttr("id").parent().addClass("usedInRow");
							}
						}
				else {
					$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
				}
						
				function tweenComplete(){
					var emptyTextPlace = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span");
					emptyTextPlace.css("visibility", "visible");
				}
						
				function tweenStart(){
					$(this.htmlElement.parentElement.parentElement).css("background-color", "#d9edf7");
				}
						
			})(j);
			timeCount += 2000;
			$(".original-table").find(".usedInRow").removeClass("usedInRow");
		})(i);
			
        createjs.Ticker.setFPS(60);
        createjs.Ticker.addEventListener("tick", stage);
	}
}