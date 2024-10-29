<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div id="woo-aacvp-modal">
	<div id="woo-aacvp-wrapper">
		<button id="woo-aacvp-wrapper-close" class="woo-aacvp-close">x</button>
		<div id="woo-aacvp-content" class="products">
		    <p id="woo-aacvp-content-title">Select an option</p>
			<p id="woo-aacvp-product-name"></p>
			<p class="woo-aacvp-error"></p>
			<div id="woo-aacvp-variation-wrapper"></div>
			<div id="woo-aacvp-buttons" class="product">
				<a href="" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_#" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart woo-aacvp-btn" data-product_id="" data-product_sku="" aria-label="#product_name" rel="nofollow">Add to cart</a>
				<button id="woo-aacvp-wrapper-cancel" class="woo-aacvp-close" type="button">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="woo-aacvp-form">
	<div id="woo-aacvp-from-wrapper">
		<button id="woo-aacvp-form-wrapper-close" class="woo-aacvp-close">x</button>
		<div id="woo-aacvp-form-content">
			<p id="woo-aacvp-form-content-title">Select an option</p>
			<p id="woo-aacvp-form-product-name"></p>
			<p class="woo-aacvp-error"></p>
			<input type="hidden" name="dirty_keys" value="">
			<input type="hidden" name="clean_keys" value="">
			<div id="woo-aacvp-form-variation-wrapper"></div>
			<div id="woo-aacvp-form-buttons">
				<a href="#" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_#" data-quantity="1" class="button add_to_cart_button woo-aacvp-btn" data-product_id="" data-product_sku="" data-variation_id="" aria-label="#product_name" data-attributes="{}" rel="nofollow">Add to cart</a>
				<button id="woo-aacvp-form-wrapper-cancel" class="woo-aacvp-close" type="button">Cancel</button>
			</div>
		</div>
	</div>
</div>