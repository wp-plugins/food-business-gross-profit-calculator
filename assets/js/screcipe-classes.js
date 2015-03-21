window.jQuery = window.$ = jQuery; /*SC Recipe type class*/

var SCRecipe = function() {
	this.id=-1;
	this.item='';
	this.measurement='';
	this.price='';

	this.toJson = function() {
		var scRecipeJson = {
			id: this.id,
			item: this.item,
			measurement: this.measurement,
			price: this.price,
		};
		return JSON.stringify(scRecipeJson);
	};

	this.arrSCRecipeToObject = function(arrSCRecipe) {
		this.id=arrSCRecipe['id'];
		this.item=arrSCRecipe['item'];
		this.measurement=arrSCRecipe['measurement'];
		this.price=arrSCRecipe['price'];
	}

};

var SCRecipeSettings=function(){
	this.total_serving=0;
	this.currency='$';
	this.gross_percent=20;
	this.operating_expenses=0;
	this.total_prices=0;

	this.toJson = function() {
		var scRecipeSettingJson = {
			total_serving: this.total_serving,
			currency: this.currency,
			gross_percent: this.gross_percent,
			operating_expenses: this.operating_expenses,
			total_prices: this.total_prices,
		};
		return JSON.stringify(scRecipeSettingJson);
	};

	this.JSONParse=function(screcipe_settings_json){
		var cleanJSON=screcipe_settings_json.replace('\\','');
		return JSON.parse(cleanJSON);
	}

	this.arrSCRecipeSettingsToObject=function(arrSCRecipeSetting){
		this.total_serving=arrSCRecipeSetting['total_serving'];
		this.currency=arrSCRecipeSetting['currency'];
		this.gross_percent=arrSCRecipeSetting['gross_percent'];
		this.operating_expenses=arrSCRecipeSetting['operating_expenses'];
		this.total_prices=arrSCRecipeSetting['total_prices'];
	}
};


var SCRecipeHTML = function() {
	var tbl_screcipe = $('#tbl_screcipe').DataTable();

	this.getTblDatatable = function() {
		return tbl_screcipe;
	}

	this.refreshDataTable = function(jsonSCRecipe, access) {
		$('#tbl_screcipe').DataTable().clear();
		for (index = 0; index < jsonSCRecipe.length; ++index) {
			curSCRecipe = new SCRecipe();
			curSCRecipe.arrSCRecipeToObject(jsonSCRecipe[index]);
			this.addToDataTable(curSCRecipe,access);
		}
		if(access==false){
			$('#addform_screcipe').hide();
		}
	}

	this.addToDataTable = function(screcipe,access) {
		var actionHtml = '<i class="dashicons dashicons-welcome-write-blog edit-recipe"></i><i class="dashicons dashicons-no remove-recipe"></i>';
		if(access==false){
			actionHtml='<i class="icon-small icon-warning-sign"></i>';
			$('#addform').hide();
		}
		var rowHtml = '<span class="screcipe_data">' + screcipe.item + '</span><br/>'+actionHtml;
		var rowTbl = this.getTblDatatable().row.add([rowHtml, '<span class="item_measurement">' + screcipe.measurement + '</span>', '<span class="currency">$</span> <span class="item_price">' + screcipe.price + '</span>']).draw();
		var rowIndex = rowTbl.index();
		rowTbl = rowTbl.node();
		$(rowTbl).attr('id', screcipe.id);
		$(rowTbl).attr('row', rowIndex);
		this.clearForm();
	};

	this.updateDataTable = function(screcipe) {
		var actionHtml = '<i class="dashicons dashicons-welcome-write-blog edit-recipe"></i><i class="dashicons dashicons-no remove-recipe"></i>';
		var rowHtml = '<span class="screcipe_data">' + screcipe.item + '</span><br/>'+actionHtml;
		rowTbl = '#' + screcipe.id;
		var rowIndex = $(rowTbl).attr('row');
		$('#tbl_screcipe').dataTable().fnUpdate([rowHtml, '<span class="item_measurement">' + screcipe.measurement + '</span>', '<span class="currency">$</span> <span class="item_price">' + screcipe.price + '</span>'], rowIndex);
	}

	this.refreshCalculator=function(screcipe_settings_json){
		if(screcipe_settings_json!=''){
			var screcipe_helper=new SCRecipeController();
			var screcipe_setting_instance=new SCRecipeSettings();
			var screcipe_settings=screcipe_setting_instance.JSONParse(screcipe_settings_json);
			$('.currency').html(screcipe_settings.currency);
			$('.total_price').html(screcipe_settings.total_prices);

			$('.price_ex').html(screcipe_helper.calculatePriceEx(screcipe_settings));
			$('.price_in').html(screcipe_helper.calculatePriceIn(screcipe_settings));

			$('#screcipe_tot_servings').val(screcipe_settings.total_serving);
			$('.total_serving').html(screcipe_settings.total_serving);
			$('#screcipe_currency').val(screcipe_settings.currency);
			$('#screcipe_gpp').val(screcipe_settings.gross_percent);
			$('.gpp').html(screcipe_settings.gross_percent);
			$('#screcipe_operating_expenses').val(screcipe_settings.operating_expenses);
			$('.operating_expenses').html(screcipe_settings.operating_expenses);
		}
	}

	this.clearForm = function() {
		$('#ingredient_item').val('');
		$('#ingredient_measurement').val('');
		$('#ingredient_price').val('');
	}


};


var SCRecipeAjax = function() {


	this.ajaxSubmit = function(request, ajax_data) {
		var screcipe_helper=new SCRecipeController();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: obj_screcipe.ajaxurl,
			data: {
				'action': 'screcipe_action',
				'request': request,
				'post_id': screcipe_helper.getPostID(),
				'data_screcipe': ajax_data.toJson(),
			},
			complete: function(object) {
				if (object.status == 200) {
					var jsonResponse = object.responseJSON;
					if (jsonResponse && typeof jsonResponse === "object" && jsonResponse !== null) {
						screcipeHtml = new SCRecipeHTML();
						if (request == 'add') {
							screcipeHtml.addToDataTable(ajax_data,true);
							screcipeHtml.refreshCalculator(jsonResponse.setting);
						} else if (request == 'get' || request=='remove') { //populate form
							screcipeHtml.refreshDataTable(jsonResponse.data,jsonResponse.access);
							screcipeHtml.refreshCalculator(jsonResponse.setting);
						} else if (request == 'edit' && jsonResponse.success) {
							screcipeHtml.updateDataTable(ajax_data);
							screcipeHtml.refreshCalculator(jsonResponse.setting);
						}else if(request=='update_setting' && jsonResponse.success){
							screcipeHtml.refreshCalculator(jsonResponse.setting);
						}
					} else {
						console.log("Ajax Failed");
					}

					screcipe_helper.updateTotalPrice();//update price
				} else {
					console.log("Ajax Failed");
				}
			}
		});
	}
};


var SCRecipeController = function() {

	this.addItem=function(){
		var screcipe_ajax=new SCRecipeAjax();
		if(this.validateValuesOfForm()){
           var screcipe=new SCRecipe();
           screcipe.id=Math.floor((Math.random() * 10000) + 1);
           screcipe.item=$('#ingredient_item').val();
           screcipe.measurement=$('#ingredient_measurement').val();
           screcipe.price=parseFloat($('#ingredient_price').val()).toFixed(2);
           screcipe_ajax.ajaxSubmit('add', screcipe);
		}else{
			alert('Please check your values and try again.');
		}
	}

	this.editSCRecipe=function(tr_obj){
		var newItem = prompt('Edit Ingredient Item', $(tr_obj).find('.screcipe_data').html());
		if (newItem != '' && newItem != null) {
			var screcipeEdited = new SCRecipe();
			screcipeEdited.item=newItem;
			var newMeasurement = prompt('Edit Ingredient\'s Measurement', $(tr_obj).find('.item_measurement').html());
			if (newMeasurement != '' && newMeasurement != null) {
				screcipeEdited.measurement=newMeasurement;
				var newPrice = prompt('Edit Ingredient\'s Price (numerical only)', $(tr_obj).find('.item_price').html());
				if (newPrice != '' && newPrice != null && $.isNumeric(newPrice)) {
					screcipeEdited.id=$(tr_obj).attr('id');
					screcipeEdited.price=parseFloat(newPrice).toFixed(2);
					var screcipeAjax = new SCRecipeAjax();
					screcipeAjax.ajaxSubmit('edit', screcipeEdited);
				}else{
					alert("Please try again. Error Price Input.");
				}
			}else{
				alert("Please try again. Error Measurement input.");
			}

		}else{
			alert("Please try again. Error Ingredient input.");
		}
	}

	this.removeSCRecipe=function(tr_obj){
		var screcipeToRemove=new SCRecipe();
		screcipeToRemove.id=$(tr_obj).attr('id');
		var screcipeAjax = new SCRecipeAjax();
		screcipeAjax.ajaxSubmit('remove', screcipeToRemove);
	}

	this.validateValuesOfForm=function(){
		if($('#ingredient_item').val()!='' && $('#ingredient_measurement').val()!='' && $.isNumeric($('#ingredient_price').val())){
			return true;
		}else{
			return false;
		}
	}

	this.populateForm = function() {
		var screcipeAjax = new SCRecipeAjax();
		screcipeAjax.ajaxSubmit('get', new SCRecipe());
	}

	this.updateTotalPrice=function(){
		var totalPrice=0.0;
		$('.item_price').each(function(index) {
			totalPrice = totalPrice+parseFloat($(this).html());
		});
		$('#total_price .total_price').html(totalPrice.toFixed(2));
	}

	this.calculatePriceEx=function(screcipe_settings){
		$('.currency').html(screcipe_settings.currency);
		//Gross profit percentage = {(Net sales – Cost of goods sold)/Net sales} x 100
		//GPC/100=1-(Cost/NS) //NS=TotalServing*Price
		//Cost/NS= 1-(GPC/100)
		//NS=Cost/(1-(GPC/100))
		//PriceEx=(Cost/ ((1-(GPC/100))*TotalServing)
		//PriceIncluded=((Cost+Expeses)/(1-(GPC/100)))/TotalServing
		var totalCost=parseFloat($('#total_price .total_price').html());
		var priceEx=0;
		if(parseFloat(screcipe_settings.total_serving)>0){
			priceEx=parseFloat((totalCost/ ((1-(screcipe_settings.gross_percent/100)) * screcipe_settings.total_serving)));
		}
		return priceEx.toFixed(2);
	}

	this.calculatePriceIn=function(screcipe_settings){
		$('.currency').html(screcipe_settings.currency);
		var totalCost=parseFloat($('#total_price .total_price').html());
		var priceIn=0;
		if(parseFloat(screcipe_settings.total_serving)>0){
			totalCost= parseFloat(totalCost) + parseFloat(screcipe_settings.operating_expenses);
			priceIn=parseFloat((totalCost/ ((1-(screcipe_settings.gross_percent/100)) * screcipe_settings.total_serving)));
		}
		return priceIn.toFixed(2);
	}

	this.updateSettings=function(){
		var totalServing=0;
		var currency='$';
		var grossPercent=20;
		var operatingExpenses=0;
		var totalCost=0;2665.91
		if($.isNumeric($('#total_price .total_price').html())){
	        totalCost=$('#total_price .total_price').html();
        }
        if($.isNumeric($('#screcipe_tot_servings').val())){
	        totalServing=$('#screcipe_tot_servings').val();
        }

	    currency=$('#screcipe_currency').val();

        if($.isNumeric($('#screcipe_gpp').val())){
	        grossPercent=$('#screcipe_gpp').val();
        }
        if($.isNumeric($('#screcipe_operating_expenses').val())){
	        operatingExpenses=$('#screcipe_operating_expenses').val();
        }
        var screcipe_ajax=new SCRecipeAjax();
        var screcipe_settings=new SCRecipeSettings();
		screcipe_settings.total_prices=totalCost;
		screcipe_settings.total_serving=totalServing;
		screcipe_settings.currency=currency;
		screcipe_settings.gross_percent=grossPercent;
		screcipe_settings.operating_expenses=operatingExpenses;
		screcipe_ajax.ajaxSubmit('update_setting', screcipe_settings);
	}

	this.getPostID = function() {
		return $('#tblcontainer').attr('post_id');
	}

};


var SCRecipeMain = function() {
	var screcipe_controller=new SCRecipeController();

    //populateform
    screcipe_controller.populateForm();

	$(document).on('click','#addform_screcipe .add_ingredient',function(e){
		screcipe_controller.addItem();
		e.preventDefault();
	});


	$(document).on('click', '.edit-recipe', function(e) {
		var closesttrid = $(this).closest('tr');
		screcipe_controller.editSCRecipe(closesttrid);
		e.preventDefault();
	});

	$(document).on('click', '.remove-recipe', function(e) {
		var closesttrid = $(this).closest('tr');
		screcipe_controller.removeSCRecipe(closesttrid);
		e.preventDefault();
	});

	$(document).on('click', '.update_settings', function(e) {
		screcipe_controller.updateSettings();
		e.preventDefault();
	});


	//add loader
	$(document.body).append('<div class="ajaxmodal"><!-- Place at bottom of page --></div>');
	$body = $("body");
	$(document).on({
		ajaxStart: function() {
			$body.addClass("loading");
		},
		ajaxStop: function() {
			$body.removeClass("loading");
		}
	});

};


var screcipeMain = new SCRecipeMain();