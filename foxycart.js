// $Id$

(function ($) {
	Drupal.behaviors.foxycart = {
		attach: function(context, settings) {
			// Check jQuery version for 1.8 or higher
			if ($().jquery.split(".")[0] == "1" && parseInt($().jquery.split(".")[1]) < 8) {
				alert("jQuery 1.8 or later required.");
			}

			fcc.events.cart.postprocess = new FC.client.event();
			fcc.events.cart.postprocess.add(function(){
				fcc.cart_update();
				return "pause";
			});
			fcc.events.cart.postprocess.add(function(){
				Drupal.foxycart.buildFullCart(FC.json.products);
			});
			fcc.events.cart.ready.add(function() {
				if (FC.json.products) {
					Drupal.foxycart.buildFullCart(FC.json.products);
				}
			});

			FC.client.prototype.cart_update = function() {
				var self = this;
				$.getJSON('https://' + this.storedomain + '/cart.php?cart=get&output=json' + this.session_get() + '&callback=?', function(data) {
					FC.json = data;
					if ( ! self.session_initialized === true) {
						self.session_initialized = true;
						FC.session_id = data.session_id;
						self.session_set();
						self.session_get();
					}

					// "Minicart" Helpers
					if (FC.json.product_count > 0) {
						$("#fc_minicart, .fc_minicart").show();
						$("#fc_minicart_empty, .fc_minicart_empty").hide();
					} else {
						$("#fc_minicart, .fc_minicart").hide();
						$("#fc_minicart_empty, .fc_minicart_empty").show();
					}
					// update values
					$("#fc_quantity, .fc_quantity").html("" + FC.json.product_count);
					$("#fc_total_price, .fc_total_price").html("" + self._currency_format(FC.json.total_price));
					// Execute the ready event on intial pageload, if it's defined
					if (self.events.cart.ready.counter === 0) {
						self.events.cart.ready.execute();
					} else {
						self.events.cart.postprocess.resume();
					}
				});
			};

			$('.product-option').change(function() {
				Drupal.foxycart.getProductOptions();
			});
			Drupal.foxycart.getProductOptions();
		}
	};

	// Utility functions
	Drupal.foxycart = {};
	Drupal.foxycart.buildFullCart = function(products) {
		var cart = "";
		if (products.length > 0) {
			for (i = 0; i < products.length; i++) {
				cart += Drupal.foxycart.buildCartRow(products[i].name,
						products[i].code,
						products[i].options,
						products[i].quantity,
						products[i].price_each,
						products[i].price);
			}
			$("#fc_cart_contents").show();
			$("#fc_cart_items").html(cart);
			$("#fc_cart_message").html("");
			$(".fc_cart_link").show();
		} else {
			$("#fc_cart_contents").hide();
			$(".fc_cart_link").hide();
			$("#fc_cart_message").html("Your shopping cart is empty");
		}
	}

	// This function is called by fc_BuildFoxyCart() for each product in your cart.
	Drupal.foxycart.buildCartRow = function(fc_name, fc_code, fc_options, fc_quantity, fc_price_each, fc_price) {
		var cart = "<tr>";
		cart += "<td>" + fc_name + "</td>";
		cart += "<td class=\"right-align\">" + fc_quantity + "</td>";
		cart += "<td class=\"right-align\">" + fc_price.toFixed(2) + "</td>";
		cart += "</tr>";
		return cart;
	}

	Drupal.foxycart.getProductOptions = function() {
		// Find the selected product options that have a 'code' aka 'sku' modifier
		$modifiers = '';
		$(".product-option option[value*='c+\!']:selected, .product-option[value*='c+\!']:checked").each( function() {
			this.value.match(/c\+(!.*)}/g);
			$modifiers += RegExp.$1;
		});
		var nid = $( "input[name^='nid||']" ).val();
		if ($modifiers !== "" && nid !== "") {
			Drupal.foxycart.setInStock();
			$.getJSON( "/foxycart/stock-query", {
				nid: nid,
				modifiers: $modifiers
			}).done( Drupal.foxycart.processStockQuery );
		}
	}

	Drupal.foxycart.processStockQuery = function(data) {
		if (data['price'] != undefined && data['price'].length > 0) {
			$('.display-price .uc-price').html(data['price']);
		}
		Drupal.foxycart.determineStockStatus( data );
	}

	Drupal.foxycart.determineStockStatus = function( data ) {
		if (data['stock_level'] === "0") {
			Drupal.foxycart.setOutOfStock();
		}
	}
	Drupal.foxycart.setOutOfStock = function() {
		var button = $("input[type='submit'].node-add-to-cart");
		button.prop('disabled', true);
		button.val('Out of Stock');
	}

	Drupal.foxycart.setInStock = function() {
		var button = $("input[type='submit'].node-add-to-cart");
		button.prop('disabled', false);
		button.val('Add to cart');
	}

})(jQuery);



