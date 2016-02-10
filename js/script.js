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

$( document ).ready(function() {
	
	/** Operations for stream mode **/
	
	function setActiveStep(anum){
		var thisStep = ".step" + anum;
		$(".wizard-footer").find(thisStep).addClass("active");
		
		//check if step is last step
		var stepnum = anum + 1;
		var nextstep = ".step" + stepnum;
		if($(".wizard-footer").find(nextstep).length){
			$(".wizard-footer").find(".next").removeClass("disabled");
		}
		else { // if this is last step
			$(".wizard-footer").find(".next").addClass("disabled").attr('disabled', true).off(); // disable next-button
		}
		
		//check if step is first step
		var prevnum = stepnum - 2;
		prevstep = ".step" + prevnum;
		if($(".wizard-footer").find(prevstep).length){
			$(".wizard-footer").find(".previous").removeClass("disabled");
		}
		else { // if this is first step
			$(".wizard-footer").find(".previous").addClass("disabled"); // disable previous-button
		}
	}
	
	/*$('.streammode-panel').bind("DOMSubtreeModified",function(){
		init();
	});*/
	
	$(document).on("click", ".wizard-footer .step a", function(e) {
		e.preventDefault();
		var stepnum = $(this).html();//get clicked step
		var stepnum = parseInt(stepnum);
		var anum = stepnum;
		
		var query = $('#sql-query-input').val();
		$.post("", {"stepnumber": stepnum, "sql-input": query}, function response(data){
			$(".streammode-panel").html($(".streammode-panel", data).html());
			init();
			//setTableCanvas();
			setActiveStep(anum);
		});
		
	});
	
	$(document).on("click", ".wizard-footer .next a", function(e) {
		e.preventDefault();
		var stepnum = $(".wizard-footer").find(".active").find("a").html(); //get active step

		var num = parseInt(stepnum);
		num = num + 1; // get number of next step
		var anum = num;
		var thisstep = ".step" + anum;
		if($(".wizard-footer").find(thisstep).length){
			var query = $('#sql-query-input').val();
			$.post("", {"stepnumber": num, "sql-input": query}, function response(data){
				$(".streammode-panel").html($(".streammode-panel", data).html());
				init();
				setActiveStep(anum);
			});
		}
		else { // if this is last step
		}
		
		
	});
	
	$(document).on("click", ".wizard-footer .previous a", function(e) {
		e.preventDefault();
		var stepnum = $(".wizard-footer").find(".active").find("a").html(); //get active step
		var num = parseInt(stepnum);
		num = num - 1; // get number of previous step
		var anum = num;
		
		var thisstep = ".step" + anum;
		if($(".wizard-footer").find(thisstep).length){
			var query = $('#sql-query-input').val();
			$.post("", {"stepnumber": num, "sql-input": query}, function response(data){
				$(".streammode-panel").html($(".streammode-panel", data).html());
				init();
				setActiveStep(anum);
			});
		}
		else { // if this is last step
		}
		
		
	});	

	$(".wizard-footer").find(".step1").addClass("active");
	$(".wizard-footer").find(".previous").addClass("disabled");

	/**Operations for saving/deleting/running queries**/
	// when save-button is clicked
	$('#save_button').click(function(e){
		e.preventDefault(); // prevent page from loading
		var query = $('#sql-query-input').val();
		var ajaxurl = 'queryoperations.php?f=save_query',
		data =  {'action': query};
		if(query.length > 0){
			$.post(ajaxurl, data, function (response) {
				switch(response) {
					case 'success': // if the query was successfully saved
						$('.query-was-saved').removeClass('hidden');
						$('#save_button').prop("disabled", true);
						$( ".savedQueries tbody" ).append("<tr><td>"+query+"</td><td style='width: 70px;'><button style='margin-right: 5px;' type='button' class='btn btn-default btn-xs edit_button'><span class='glyphicon glyphicon-play'></span></button><button type='button' class='btn btn-default btn-xs delete_button'><span class='glyphicon glyphicon-remove'></span></button></td></tr>");
						break;
					case 'excist':	// if the query already was saved to this user
						$('.query-excists').removeClass('hidden');
						break;
				}
			});
		}
	});

	//Shows columns for each table in right-sided menu
	$('.databaseTables').on( 'click', 'tr',function(e){
		var thistr = $(this);
		//if columns already are collected from the db
		if(thistr.find('td').find('.tableColumns').length > 0){
			if(thistr.find('td').find('.tableColumns').hasClass('hidden')){
				thistr.find('td').find('.tableColumns').removeClass('hidden');
				thistr.find('td').find('span').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			}
			else {
				thistr.find('td').find('.tableColumns').addClass('hidden');
				thistr.find('td').find('span').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
			}
		}
		else {
			var table = $(this).find('td').text();
			var ajaxurl = 'tableOperations.php?f=getTableColumns',
			data = {'action': table};
			$.post(ajaxurl, data, function (response) {
				thistr.find('td').first().append(response);
				thistr.find('td').find('span').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			});
		}
	});

	// When the user clicks the run-button on a saved query, the query is placed into the input field and the decomposer is run
	$('.savedQueries').on( 'click', '.edit_button',function(e){
		var query = $(this).parent().parent().children(':first-child').text();
		$('#sql-query-input').val(query);
		//setTablesCanvas();
		$('.decompose').click();
	});
	
	// When the user clicks the delete-button on a saved query, an ajax-request is sent to queryoperations.php, where the query is deleted from the db if it excists.
	$('.savedQueries').on( 'click', '.delete_button',function(e){
		if (confirm('Are you sure you want to delete this query?')) {
			e.preventDefault(); // prevent page from loading
			var query = $(this).parent().parent().children(':first-child').text();
			var button = this;
			var ajaxurl = 'queryoperations.php?f=delete_query',
			data =  {'action': query};
			if(query.length > 0){
				$.post(ajaxurl, data, function (response) {
					switch(response) {
						case 'success': // if the query was successfully deleted
							$(button).parent().parent().remove();
							alert("success!");
							break;
						case 'excist':	// error
							break;
					}
				});
			}
		}
		else {}
	});
	
	// Disable or enable the save-button
	if($('#sql-query-input').val().length === 0){ // Disable if input-field is empty
		$('#save_button').prop("disabled", true);
	}
	else { // Enable if input-field contains something
		$('#save_button').prop("disabled", false);
	}
	
	// Disable or enable the save-button when input field changes
	$('#sql-query-input').on('input', function() {
	  if($('#sql-query-input').val().length === 0){
		$('#save_button').prop("disabled", true);
	  }
	  else {
		$('#save_button').prop("disabled", false);
	  }
	});
	
});