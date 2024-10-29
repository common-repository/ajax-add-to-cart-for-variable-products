jQuery(document).ready(function($){
	$("#woo-aacvp-modal .woo-aacvp-btn").click(function(e){
		e.preventDefault(); 
		
		var product_id = $(this).attr("data-product_id");
		var product_sku = $(this).attr("data-product_sku");
		var product_href = $(this).attr("href");
		
		if ( product_id !== "" && product_sku !== "" && product_href !== "" ) {
			$("#woo-aacvp-modal .woo-aacvp-error").html("").hide();
		} else {
			var error = "Please select an option";
			$("#woo-aacvp-modal .woo-aacvp-error").html(error).show();
		}
		
	});
	
	$("#woo-aacvp-form-buttons .woo-aacvp-btn").click(function(e){
		e.preventDefault();

		var $button = $(this);	
		var product_id = $(this).attr("data-product_id");
		var quantity = 1;
		var variation_id = $(this).attr("data-variation_id");
		var attributesStr = $(this).attr("data-attributes");
		if (attributesStr !== '') {
			var attributes = JSON.parse($(this).attr("data-attributes"));
		}
		if ( product_id !== "" && variation_id !== "" && attributes !== ""  ) {
			$(this).addClass("loading");
			var sendData = {
				product_id: product_id,
				variation_id: variation_id,
				quantity: quantity, 
				attributes: attributes,
				action: 'aacvp_add_to_cart_variable',
				nonce: aacvp_ajax_obj.aacvp_nonce
			}
			
			$.ajax({
				url: aacvp_ajax_obj.ajax_url,
				type: 'POST',
				data: sendData,
				success: function(response){
					if (response.error) {
						var error = 'Sorry, an error has occurred. Please try again.';
						$("#woo-aacvp-form .woo-aacvp-error").html( error ).show();
					} else {
						$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
						$("#woo-aacvp-form-buttons .woo-aacvp-btn").attr({
							'data-product_id' : '', 
							'data-variation_id' : '', 
							'data-product_sku' : '',
							'data-attributes' : '' 
						});
						$("#woo-aacvp-form-variation-wrapper").find("input.woo-aacvp-radio").removeClass("active-radio").prop("checked", false);
						$("#woo-aacvp-form-variation-wrapper").find("select").removeClass("pointer-abled pointer-disabled").val([]).prop("selectedIndex", 0);
						$("#woo-aacvp-form-variation-wrapper").find("select").parent().attr("data-attribute_value", "");
					}
				}
			});
		} else {	
			if ( $("#woo-aacvp-from-wrapper").find("input.active-radio").length > 0 ) {
				var productID = $("#woo-aacvp-from-wrapper").find("input.active-radio").data("product_id");
				var productQty = 1;
				var variationID = $("#woo-aacvp-from-wrapper").find("input.active-radio").data("variation_id");
				var variationAttrs = {};
				var attrDivs = $("#woo-aacvp-from-wrapper").find("input.active-radio").parent().find(".woo-aacvp-variation-attr-label");
				var attr_counter = 0; 
				$.each(attrDivs, function(index, attrDiv){
					var attrDivValue = $(attrDiv).attr("data-attribute_value");
					if ( attrDivValue == '' ) {
						attr_counter ++; 
					}
				});
				if (attr_counter > 0) {
					var error = "Please select all attributes";
					$("#woo-aacvp-form .woo-aacvp-error").html(error).show();
				} else {
					$.each(attrDivs, function(index, attrDiv){
						var attrDivValue = $(attrDiv).attr("data-attribute_value");
						var attrDivName = $(attrDiv).attr("data-attribute_key");
						var attr_clean_keys = $("#woo-aacvp-form-content input[name='clean_keys']").val(); 
						var attr_dirty_keys = $("#woo-aacvp-form-content input[name='dirty_keys']").val(); 
						var attr_clean_keys_arr = attr_clean_keys.split(",");
						var attr_dirty_keys_arr = attr_dirty_keys.split(",");
						var the_attr_key_index = attr_clean_keys_arr.indexOf(attrDivName);
						var the_attr_key = 'attribute_' + attr_dirty_keys_arr[the_attr_key_index];
						var attr = {}; 
						attr[the_attr_key] = attrDivValue;
						$.extend(variationAttrs, attr);
					});
					$(this).addClass("loading");
					var sendData2 = {
						product_id: productID,
						variation_id: variationID,
						quantity: productQty, 
						attributes: variationAttrs,
						action: 'aacvp_add_to_cart_variable',
						nonce: aacvp_ajax_obj.aacvp_nonce
					}
					$.ajax({
						url: aacvp_ajax_obj.ajax_url,
						type: 'POST',
						data: sendData2,
						success: function(response){
							if (response.error) {
								var error = 'Sorry, an error has occurred. Please try again.';
								$("#woo-aacvp-form .woo-aacvp-error").html( error ).show();
							} else {
								$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
								$("#woo-aacvp-form-buttons .woo-aacvp-btn").removeClass("added");
								$("#woo-aacvp-form-buttons .woo-aacvp-btn").attr({
									'data-product_id' : '', 
									'data-variation_id' : '', 
									'data-product_sku' : '',
									'data-attributes' : '' 
								});
								$("#woo-aacvp-form-variation-wrapper").find("input.woo-aacvp-radio").removeClass("active-radio").prop("checked", false);
								$("#woo-aacvp-form-variation-wrapper").find("select").removeClass("pointer-abled pointer-disabled").val([]).prop("selectedIndex", 0);
								$("#woo-aacvp-form-variation-wrapper").find("select").parent().attr("data-attribute_value", "");
							}
						}
					});
				}
				
			} else {
				var error = "Please select an option";
				$("#woo-aacvp-form .woo-aacvp-error").html(error).show();
			}
		}

	});
});