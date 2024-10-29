jQuery(document).ready(function($){
	// Open Correspondent Popups
	$(".woo-aacvp-add_to_cart_button").click(function(e){
		e.preventDefault();
		$(this).addClass("loading");
		var product_name = $(this).parent().find("input[name='product_name']").val();
		var options_wrappers = $(this).parent().find("input[name='variation_data']");
		var options = []; 
		$.each(options_wrappers, function(index, element){
			var variation_id = $(element).data("variation_id");
			var variation_sku = $(element).data("variation_sku");
			var variation_price_html = $(element).data("variation_price_html");
			var variation_image = $(element).data("variation_image");
			var variation_price = $(element).data("variation_price");
			var variation_attr = $(element).data("variation_attr");
			var option = {
				'variation_id' : variation_id, 
				'variation_sku' : variation_sku, 
				'variation_price_html' : variation_price_html,
				'variation_thumbnail_url' : variation_image, 
				'variation_price_raw' : variation_price, 
				'variation_attr' : variation_attr
			};
			options.push(option);
		});
		var data = {
			'product_name' : product_name,
			'options' : options
		}; 
		insertDataToModal( data );
		$("#woo-aacvp-modal").css("display", "flex");
		$(this).removeClass("loading");
	});
	
	$(".woo-aacvp-add_to_cart_form").click(function(e){
		e.preventDefault();
		$(this).addClass("loading");
		var product_name = $(this).parent().find("input[name='product_name']").val();
		var clean_keys = $(this).parent().find("input[name='attribute_clean_keys']").data("attribute_keys");
		var dirty_keys = $(this).parent().find("input[name='attribute_keys']").data("attribute_keys");
		var options_wrappers = $(this).parent().find("input[name='variation_data']");
		var options = []; 
		$.each(options_wrappers, function(index, element){
			var product_id = $(element).data("product_id");
			var variation_id = $(element).data("variation_id");
			var variation_sku = $(element).data("variation_sku");
			var variation_price_html = $(element).data("variation_price_html");
			var variation_image = $(element).data("variation_image");
			var variation_price = $(element).data("variation_price");
			var variation_attributes = []; 
			var filled_attrs = $(element).data("variation_attr");
			$.each(filled_attrs, function(index, filled_attr){
				variation_attributes.push(filled_attr);
			});
			var empty_attrs = $(element).data("empty_attr");
			$.each(empty_attrs, function(index, empty_attr){
				variation_attributes.push(empty_attr);
			});
			var option = {
				'product_id' : product_id, 
				'variation_id' : variation_id, 
				'variation_sku' : variation_sku,
				'variation_price_html' : variation_price_html,
				'variation_thumbnail_url' : variation_image, 
				'variation_price_raw' : variation_price, 
				'variation_attributes' : variation_attributes,
			};
			options.push(option);
		});
		var data = {
			'product_name' : product_name,
			'options' : options,
			'dirty_keys' : dirty_keys,
			'clean_keys' : clean_keys,
		}; 
		insertDataToForm( data );
		$("#woo-aacvp-form").css("display", "flex");
		$(this).removeClass("loading");
	});
	
	// Close Correspondent Popups
	$(".woo-aacvp-close").click(function(){
		closePopUp(); 
		clearPopUp(); 
	});
	
	// Click events on the modal popup 
	$("#woo-aacvp-variation-wrapper").on("click", "input.woo-aacvp-radio", function(){
		$("#woo-aacvp-variation-wrapper input.woo-aacvp-radio").removeClass("active-radio");
		$(this).addClass("active-radio");
		$(".woo-aacvp-error").html("").hide();
		var variation_id = $(this).data("variation_id");
		var variation_sku = $(this).data("variation_sku");
		var destination_url = "?add-to-cart="+variation_id;
		$("#woo-aacvp-buttons .woo-aacvp-btn").attr({
			'data-product_id' : variation_id, 
			'data-product_sku' : variation_sku,
			'href' : destination_url
		});
	});
	
	// Click events on the form popup - radio buttons
	$("#woo-aacvp-form-variation-wrapper").on("click", "input.woo-aacvp-radio", function(){
		$("#woo-aacvp-form-variation-wrapper input.woo-aacvp-radio").removeClass("active-radio error-border");
		$("#woo-aacvp-form-variation-wrapper select").removeClass("error-border pointer-disabled");
		$(this).addClass("active-radio");
		$(".woo-aacvp-error").html("").hide();
		var attr_containers = $(this).parent().find(".woo-aacvp-variation-attr .woo-aacvp-variation-attr-label"); 
		var attributes = {};
		var miss = 0; 
		$.each(attr_containers, function(index, attr){
			var attr_value = $(attr).attr("data-attribute_value");
			if ( attr_value == '' ) {
				miss ++; 
			} else {
				var attr_name = $(attr).attr("data-attribute_key");
				var clean_keys = $("#woo-aacvp-form-content input[name='clean_keys']").val(); 
				var dirty_keys = $("#woo-aacvp-form-content input[name='dirty_keys']").val(); 
				var clean_keys_arr = clean_keys.split(",");
				var dirty_keys_arr = dirty_keys.split(",");
				var key_index = clean_keys_arr.indexOf(attr_name);
				var attr_key = 'attribute_' + dirty_keys_arr[key_index];
				var attribute = {};
				attribute[attr_key] = attr_value;
				$.extend(attributes, attribute);
			}
		});
		if ( miss > 0 ) {
			var error = 'Select required attributes';
			$("#woo-aacvp-form .woo-aacvp-error").html( error ).show();
			$(this).parent().find("select").addClass("error-border");
			// Disable all other select elements' pointer event besides the current element's siblings
			// Unsettle their option to the default value 
			$("select").not(".error-border").addClass("pointer-disabled").prop("selectedIndex", 0);
		} else {
			var product_id = $(this).data("product_id");
			var variation_id = $(this).data("variation_id");
			var variation_sku = $(this).data("variation_sku");
			var variation_attributes_str = JSON.stringify(attributes);
			$("#woo-aacvp-form-buttons .woo-aacvp-btn").attr({
				'data-product_id' : product_id, 
				'data-variation_id' : variation_id, 
				'data-product_sku' : variation_sku,
				'data-attributes' : variation_attributes_str 
			});
		}
	});
	
	// Click events on the form popup - select options 
	$("#woo-aacvp-form-variation-wrapper").on("change", "select", function(){
		$(".error-border").removeClass("error-border");
		$(".woo-aacvp-error").html("").hide();
		//$("#woo-aacvp-form-variation-wrapper input.woo-aacvp-radio").prop("checked", false);
		// Disable pointer event on all other select elements that are not current element's siblings
		// To prevent user clicking multiple options across differnt variations 
		// Without clicking on the radio button  
		$(this).parent().parent().find("select").addClass("pointer-abled");
		$(this).parent().parent().find("input.woo-aacvp-radio").addClass("active-radio").prop("checked", true);
		$("select").not(".pointer-abled").addClass("pointer-disabled");
		var selected_value = $(this).val(); 
		$(this).parent().attr('data-attribute_value', selected_value); 
	}); 
	
	function closePopUp() {
		$('#woo-aacvp-modal *, #woo-aacvp-form *').scrollTop(0);
		$("#woo-aacvp-modal, #woo-aacvp-form").css("display", "none");
	}
	
	function clearPopUp() {
		$("#woo-aacvp-product-name, #woo-aacvp-form-product-name, .woo-aacvp-error").html("");
		$(".woo-aacvp-error").css("display", "none");
		$("#woo-aacvp-variation-wrapper, #woo-aacvp-form-variation-wrapper").empty(); 
		$('.loading').removeClass("loading");
		$('#woo-aacvp-modal .added_to_cart, #woo-aacvp-form .added_to_cart').remove();
		$("#woo-aacvp-modal #woo-aacvp-buttons .woo-aacvp-btn").attr({
			'data-product_id' : '',
			'data-product_sku' : ''
		});
		$("#woo-aacvp-form-buttons .woo-aacvp-btn").attr({
			'data-product_id' : '', 
			'data-variation_id' : '', 
			'data-product_sku' : '',
			'data-attributes' : '' 
		});
	}
	
	function insertDataToModal(data) {
		$("#woo-aacvp-product-name").html(data.product_name);
		var data_options = data.options; 
		$.each(data_options, function(index, data_option){
			var optionDiv = $('<div>', {
				class: 'woo-aacvp-variation',
			});
			var radioButton = $('<input>', {
            	type: 'radio',
				class: 'woo-aacvp-radio',
                name: 'option',
				'data-variation_id' : data_option.variation_id,
				'data-variation_sku' : data_option.variation_sku, 
            });
			$(optionDiv).append(radioButton);
			var imageDiv = $('<img>', {
				src: data_option.variation_thumbnail_url,
				class: 'woo-aacvp-variation-image',
				alt: data.product_name, 
			});
			$(optionDiv).append(imageDiv);
			var attributesDiv = $('<div>', {
				class: 'woo-aacvp-variation-attr'
			});
			var attributes = data_option.variation_attr; 
			$.each(attributes, function(key, value){
                var attribute = $('<p>', {
					class: 'woo-aacvp-variation-attr-label',
					text: key + ': ' + value,
				});
                $(attributesDiv).append(attribute);
            });
			$(optionDiv).append(attributesDiv);
			var priceDiv = $('<div>', {
				class: 'woo-aacvp-variation-price',
				html: data_option.variation_price_html
			}); 
			$(optionDiv).append(priceDiv);
			$("#woo-aacvp-variation-wrapper").append(optionDiv);
		});
	}
	
	function insertDataToForm(data) {
		$("#woo-aacvp-form-product-name").html(data.product_name);
		$("#woo-aacvp-form-content input[name='clean_keys']").val(data.clean_keys);
		$("#woo-aacvp-form-content input[name='dirty_keys']").val(data.dirty_keys);
		var data_options = data.options; 
		$.each(data_options, function(index, data_option){
			var optionDiv = $('<div>', {
				class: 'woo-aacvp-variation',
			});
			var radioButton = $('<input>', {
				type: 'radio',
				class: 'woo-aacvp-radio',
				name: 'form-option',
				'data-product_id' : data_option.product_id, 
				'data-variation_id' : data_option.variation_id,
				'data-variation_sku' : data_option.variation_sku,
				'data-variation_attributes' : '',
			});
			$(optionDiv).append(radioButton);
			var imageDiv = $('<img>', {
				src: data_option.variation_thumbnail_url,
				class: 'woo-aacvp-variation-image',
				alt: data.product_name, 
			});
			$(optionDiv).append(imageDiv);
			var attributesDiv = $('<div>', {
				class: 'woo-aacvp-variation-attr'
			});
			var attributes = data_option.variation_attributes;
			$.each(attributes, function(key, value){
				var keys = Object.keys(value);
				var key = keys[0];
				var content = value[key];
				if ( typeof(content) == "string" ) {
					var attribute = $('<p>', {
						class: 'woo-aacvp-variation-attr-label',
						text: key + ': ' +content,
						'data-attribute_key' : key, 
						'data-attribute_value' : content
					});
					$(attributesDiv).append(attribute);
				} else {
					var attribute = $('<p>', {
						class: 'woo-aacvp-variation-attr-label',
						'data-attribute_key' : key,
						'data-attribute_value' : '',
						text: key + ': ',
					});
					var select_options = content; 
					var select = $('<select>', {
						label: key, 
					});
					$(attribute).append(select);
					var first_option = $('<option>', {
						value: '',
						text: 'Select',
					})
					$(select).append(first_option);
					$.each(select_options, function(index, select_option){
						var select_option = $('<option>', {
							value: select_option,
							text: select_option
						});
						$(select).append(select_option);
					});
					$(attributesDiv).append(attribute);
				}
			});
			$(optionDiv).append(attributesDiv);
			var priceDiv = $('<div>', {
				class: 'woo-aacvp-variation-price',
				html: data_option.variation_price_html
			}); 
			$(optionDiv).append(priceDiv);
			$("#woo-aacvp-form-variation-wrapper").append(optionDiv);
		});
		
	}
});