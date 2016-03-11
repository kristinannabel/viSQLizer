$( document ).ready(function() {
	
	localStorage['animation'] = "normal";
	localStorage['dragout'] = true;
	localStorage['bigtext'] = true;
		
	/* For when user hovers one of the cells in the empty-table */
	$("#empty-table td").live('mouseover', function(e){
		e.stopPropagation();
		$(this).css("background-color", "#fcf8c3");
		var columnIndexEmpty = $(this).index();
		var thisColumnId = $(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id");
	    var thisColumn = $( this ).parent().parent().children().first().find("th").eq(columnIndexEmpty).find("p").html();
		var originalColumnIndex = $(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).index();
		var numberOfColumnsInThisTable = $(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").length;
		for(var i = 0; i < numberOfColumnsInThisTable; i++){
			if(i == 0){
				$(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").eq(i).find("th").eq(originalColumnIndex).css("background-color", "#fcf8e3");
			} else {
				$(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").eq(i).find("td").eq(originalColumnIndex).css("background-color", "#fcf8e3");
			}
		}
		var thisText = $(this).find("span").first().html();
		var columnIndex = $(".original-table").find("tr").find("."+thisColumn+"").index();
		columnIndex = columnIndex + 1;
		
		if($(".original-table").find("tr").find("."+thisColumn+"").length > 1){
			if($(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id") != undefined){
				var thisElementId = $(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id");
				var thisOrgElemIndex = $(".original-table").find("tr").find("."+thisColumn+"#"+thisElementId).index();
				var thisColIndex = thisOrgElemIndex + 1;
				var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"#"+thisElementId).parent().parent().find("td:nth-of-type(" + thisColIndex + ")").find("span#original-span").filter(function(){
					return $(this).html() === thisText;
				});
				
			}
			else {
				var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"").parent().parent().find("td:nth-of-type(" + columnIndex + ")").find("span#original-span").filter(function(){
					return $(this).html() === thisText;
				});
			}
		}
		else {
			var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"").parent().parent().find("td:nth-of-type(" + columnIndex + ")").find("span#original-span").filter(function(){
				return $(this).html() === thisText;
			});
		}
		
		if(thisTextOriginal.length > 1){
			var foundCorrectCell = false;
			while(foundCorrectCell == false){
				var thisRow = $(this).parent().index();
				thisRow = thisRow - 1;
				if(thisTextOriginal.first().parent().parent().find("td[class*='usedInRow_" + thisRow + "']").length != 0){
					thisTextOriginal.first().parent().parent().children("td").css("background-color", "#fcf8e3");
					thisTextOriginal.first().parent().css("background-color", "#fcf8c3");
					foundCorrectCell = true;
				}
				else {
					thisTextOriginal.first().addClass("notInUse");
					if($(".original-table").find("tr").find("."+thisColumn+"").length > 1){
						if($(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id") != undefined){
							var thisElementId = $(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id");
							var thisOrgElemIndex = $(".original-table").find("tr").find("."+thisColumn+"#"+thisElementId).index();
							var thisColIndex = thisOrgElemIndex + 1;
							var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"#"+thisElementId).parent().parent().find("td:nth-of-type(" + thisColIndex + ")").find("span#original-span:not(.notInUse)").filter(function(){
								return $(this).html() === thisText;
							});
				
						}
						else {
							var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"").parent().parent().find("td:nth-of-type(" + columnIndex + ")").find("span#original-span:not(.notInUse)").filter(function(){
								return $(this).html() === thisText;
							});
						}
					}
					else {
						var thisTextOriginal = $(".original-table").find("tr").find("."+thisColumn+"").parent().parent().find("td:nth-of-type(" + columnIndex + ")").find("span#original-span:not(.notInUse)").filter(function(){
							return $(this).html() === thisText;
						});
					}
				}
			}
		}
		else{
			thisTextOriginal.parent().parent().children("td").css("background-color", "#fcf8e3")
			thisTextOriginal.parent().css("background-color", "#fcf8c3");
		}
		
		if($(".alert-info-decomposer").find("b:contains(ON)").length > 0){
			var numOfOns = $(".original-table").find(".onColumn").length;
			var thisIndex = $(this).index();
			var isOnColumn = false;
			for(var e = 0; e < numOfOns; e++){
				if($(".original-table").find(".onColumn").eq(e).text() === $(this).parent().parent().find("tr").first().find("th").eq(thisIndex).text()){
					isOnColumn = true;
				}
			}
			
			for(var e = 0; e < numOfOns; e++){
				var onIndex = $(".original-table").find(".onColumn").eq(e).index();
				var onName = $(".original-table").find(".onColumn").eq(e).text();
				var thisIndex = $(this).index();
				if($(this).parent().parent().find("tr").first().find("th").eq(thisIndex).text() == onName){ //Would not work with AS
					$(this).css("background-color", "#fcf8c3"); //mørk farge ved rad: #fcd1a1
				}
				else if(isOnColumn){
					var otherOnIndex = $(this).parent().parent().find("tr").first().find("th."+ onName).index();
					if(otherOnIndex >= 0){
						$(this).parent().find("td").eq(otherOnIndex).css("background-color", "#fcf8c3");
					}
				}
			}
		}
		if($(this).index() == 0){
			$(this).find(".glyphicon").show();
		}
		
		
	}).live("mouseleave", function() {
		$(this).removeAttr('style');
		$(this).parent().find("td").removeAttr('style');
   		var columnIndexEmpty = $(this).index();
		var thisColumnId = $(this).parent().parent().find("tr").first().find("th").eq(columnIndexEmpty).attr("id");
   	    var thisColumn = $( this ).parent().parent().children().first().find("th").eq(columnIndexEmpty).find("p").html();
   		var originalColumnIndex = $(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).index();
   		var numberOfColumnsInThisTable = $(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").length;
		$(".original-table").find("span").removeClass("notInUse");
		$(".original-table").find("tr").removeAttr('style');
		$(".original-table").find("td").removeAttr('style');
		
   		for(var i = 0; i < numberOfColumnsInThisTable; i++){
   			if(i == 0){
   				$(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").eq(i).find("th").eq(originalColumnIndex).removeAttr('style');
   			} else {
   				$(".original-table").find("tr").find("."+thisColumn+"#"+thisColumnId).parent().parent().find("tr").eq(i).find("td").eq(originalColumnIndex).removeAttr('style');
   			}
   		}
		if(!$markRowisShown){
			$(this).find(".glyphicon").hide();
		}
	});
	
	$markRowisShown = false;
	
	$("#empty-table tr").live("mouseleave", function() {
		$(".empty-table").find("tr").removeAttr('style');
		$markRowisShown = false;
		$(this).find(".glyphicon").hide();
	});
	
	$(document).on("click", "#empty-table td:has(.glyphicon) ", function() {
		if($markRowisShown){
			$(this).parent().removeAttr('style');
			$markRowisShown = false;
		}else{
			$(this).parent().css("background-color", "#fcf8e3");
			$markRowisShown = true;
		}
	});
	
	function setColumnColor(){
		if($(".alert-info-decomposer").find("b:contains(ORDER )").length > 0){
			var numOfOrderBy = $(".original-table").find(".orderByColumn").length;	
			for(var e = 0; e < numOfOrderBy; e++){
		 		$(".original-table").find(".orderByColumn").addClass("usedOrderBy");
		 	}
		}
		if($(".alert-info-decomposer").find("b:contains(WHERE)").length > 0){
			var numOfWheres = $(".original-table").find(".where").length;
			for(var e = 0; e < numOfWheres; e++){
				$(".original-table").find(".where").addClass("usedWhere");
			}
		}
		if($(".alert-info-decomposer").find("b:contains(ON)").length > 0){
			var numOfOns = $(".original-table").find(".onColumn").length;
			for(var e = 0; e < numOfOns; e++){
				$(".original-table").find(".onColumn").addClass("usedOn");
			}
		}
	}
	
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
		
		var input = $('#sql-query-input').val();
		var query = input.replace(/"/g, "'");
		$.post("", {"stepnumber": stepnum, "sql-input": query}, function response(data){
			$(".streammode-panel").html($(".streammode-panel", data).html());
			setActiveStep(anum);
			setColumnColor();
			init();
			
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
			var input = $('#sql-query-input').val();
			var query = input.replace(/"/g, "'");
			$.post("", {"stepnumber": num, "sql-input": query}, function response(data){
				$(".streammode-panel").html($(".streammode-panel", data).html());
				setActiveStep(anum);
				setColumnColor();
				init();
				
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
			var input = $('#sql-query-input').val();
			var query = input.replace(/"/g, "'");
			$.post("", {"stepnumber": num, "sql-input": query}, function response(data){
				$(".streammode-panel").html($(".streammode-panel", data).html());
				setActiveStep(anum);
				setColumnColor();
				init();
				
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