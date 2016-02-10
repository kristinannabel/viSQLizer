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