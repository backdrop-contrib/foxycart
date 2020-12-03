// $Id$
(function ($) {
  Backdrop.behaviors.foxycart = {
    attach: function(context, settings) {
      // Check jQuery version for 1.8 or higher
      if ($().jquery.split(".")[0] == "1" && parseInt($().jquery.split(".")[1]) < 8) {
        alert("jQuery 1.8 or later required.");
      }

      $('.product-option').change(function() {
        Backdrop.foxycart.getProductOptions();
      });
      Backdrop.foxycart.getProductOptions();
    }
  };

  // Utility functions
  Backdrop.foxycart = {};
  Backdrop.foxycart.buildFullCart = function(params) {
    var products = FC.json.items;
    var cart = "";
    if (FC.json.item_count > 0) {
      for (i = 0; i < products.length; i++) {
        cart += Backdrop.foxycart.buildCartRow(products[i].name,
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
      $("#fc_minicart_empty, .fc_minicart_empty").hide();
    } else {
      $("#fc_cart_contents").hide();
      $(".fc_cart_link").hide();
      $("#fc_cart_message").html("Your shopping cart is empty");
      $("#fc_minicart_empty, .fc_minicart_empty").show();
    }
  }

  // This function is called by fc_BuildFoxyCart() for each product in your cart.
  Backdrop.foxycart.buildCartRow = function(fc_name, fc_code, fc_options, fc_quantity, fc_price_each, fc_price) {
    var cart = "<tr>";
    cart += "<td>" + fc_name + "</td>";
    cart += "<td class=\"right-align\">" + fc_quantity + "</td>";
    cart += "<td class=\"right-align\">" + fc_price.toFixed(2) + "</td>";
    cart += "</tr>";
    return cart;
  }

  Backdrop.foxycart.getProductOptions = function() {
    // Find the selected product options that have a 'code' aka 'sku' modifier
    $modifiers = '';
    $(".product-option option[value*='c+\!']:selected, .product-option[value*='c+\!']:checked").each( function() {
      this.value.match(/c\+(!.*)}/g);
      $modifiers += RegExp.$1;
    });
    var nid = $( "input[name^='nid||']" ).val();
    if ($modifiers !== "" && nid !== "") {
      Backdrop.foxycart.setInStock();
      $.getJSON( "/foxycart/stock-query", {
        nid: nid,
        modifiers: $modifiers
      }).done( Backdrop.foxycart.processStockQuery );
    }
  }

  Backdrop.foxycart.processStockQuery = function(data) {
    if (data['price'] != undefined && data['price'].length > 0) {
      $('.display-price .uc-price').html(data['price']);
    }
    Backdrop.foxycart.determineStockStatus( data );
  }

  Backdrop.foxycart.determineStockStatus = function( data ) {
    if (data['stock_level'] === "0") {
      Backdrop.foxycart.setOutOfStock();
    }
  }
  Backdrop.foxycart.setOutOfStock = function() {
    var button = $("input[type='submit'].node-add-to-cart");
    button.prop('disabled', true);
    button.val('Out of Stock');
  }

  Backdrop.foxycart.setInStock = function() {
    var button = $("input[type='submit'].node-add-to-cart");
    button.prop('disabled', false);
    button.val('Add to cart');
  }


})(jQuery);

var FC = FC || {};
FC.onLoad = function () {
  FC.client.on('ready.done', Backdrop.foxycart.buildFullCart);
  FC.client.on('cart-submit.done', Backdrop.foxycart.buildFullCart);
  FC.client.on('cart-item-quantity-update.done', Backdrop.foxycart.buildFullCart);
  FC.client.on('cart-item-remove.done', Backdrop.foxycart.buildFullCart);
  FC.client.on('cart-update', Backdrop.foxycart.buildFullCart);
};

