<?php

function foxycart_uc_view_datafeed ($order) {
	if ($order->data['fc_datafeed'])
		return '<pre>' . foxycart_xmlpp($order->data['fc_datafeed'], true) . '</pre>';
	else
		return '';
}

function foxycart_uc_df_add_product_to_order(&$order, $transaction_detail) {
	$product->order_id = $order->order_id;
	$product->title = (string)$transaction_detail->product_name;
	$product->model = (string)$transaction_detail->product_code;
	$product->qty = (int)$transaction_detail->product_quantity;
	$product->price = (float)$transaction_detail->product_price;
	$product->weight = (float)$transaction_detail->product_weight;
	
	$order->products[] = $product;	
	
	// In foxycart a line item on an order can have multiple product detail options.
	// Ubercart currently does not have a way to represent these, so we flatten them out
	// into additional products on the order
	foxycart_log("Found " . count($transaction_detail->transaction_detail_options[0]) . " transaction detail options.");
	foreach ($transaction_detail->transaction_detail_options[0] as $transaction_detail_option) {
		if ($transaction_detail_option->product_option_name == 'nid') {
			$product->nid = (int)$transaction_detail_option->product_option_value;
		} else {
			$option = array();
			$option["order_id"] = $order->order_id;
			$option["title"] = (string)$transaction_detail->product_name . ' - ' 
				. (string)$transaction_detail_option->product_option_name . ": "
				. (string)$transaction_detail_option->product_option_value;
			$option["qty"] = $product->qty;
			$option["price"] = (float)$transaction_detail_option->price_mod;
			$option["weight"] = (float)$transaction_detail_option->weight_mod;

			foxycart_log("Added detail option: " . $option["title"]);
			$order->products[] = $option;
		}
	}
}

function foxycart_uc_df_add_payment_to_order(&$order, $transaction) {
	uc_payment_enter($order->order_id,
		isset($transaction->payment_gateway_type) ? (string)$transaction->payment_gateway_type : 'Other',
		(float)$transaction->order_total, 
		$order->uid, $data = NULL, (string)$transaction->processor_response,
		strtotime((string)$transaction->transaction_date)
	);	
}

function foxycart_uc_df_add_shipping_to_order(&$order, $transaction) {
	if (isset($transaction->shipto_shipping_service_description) && (string)$transaction->shipto_shipping_service_description != '') {
		$shipping = "SHIPPING: ";
		$shipping .= (string)$transaction->shipto_shipping_service_description;
		uc_order_comment_save($order->order_id, $order->uid, $shipping, 'order');
	}
}


function foxycart_uc_df_create_order($transaction) {
	if (variable_get('foxycart_user_sync', true) == true
			&& (int)$transaction->is_anonymous == 0) {		
		$user = user_load_by_mail((string)$transaction->customer_email);
		if ($user == FALSE) {
			throw new Exception('Error matching FoxyCart user to Drupal user when creating order from datafeed.');
		}
		$order = uc_order_new($user->uid, 'post_checkout');
	} else {
		$order = uc_order_new(0, 'post_checkout');
	}

	$countries = uc_country_option_list();
	$zones = uc_zone_option_list();

	$order->data["fc_transaction_id"] = (int)$transaction->id;
	$order->data["fc_datafeed"] = $transaction->asXML(); 
	
	$order->primary_email = (string)$transaction->customer_email;
	$order->delivery_first_name = (string)$transaction->shipping_first_name;
	$order->delivery_last_name = (string)$transaction->shipping_last_name;
	$order->delivery_street1 = (string)$transaction->shipping_address1;
	$order->delivery_street2 = (string)$transaction->shipping_address2;
	$order->delivery_city = (string)$transaction->shipping_city;  

	$order->delivery_zone = foxycart_uc_get_zone_id($transaction->shipping_state);
	$order->delivery_postal_code = (string)$transaction->shipping_postal_code;

	$country_result = uc_get_country_data(Array('country_iso_code_2' => $transaction->customer_country));
	if (count($country_result) && isset($country_result[0]->country_id)) { 
		$order->delivery_country = $country_result[0]->country_id;
		$order->billing_country  = $country_result[0]->country_id;		
	}

	$order->billing_first_name = (string)$transaction->customer_first_name;
	$order->billing_last_name = (string)$transaction->customer_last_name;
	$order->billing_street1 = (string)$transaction->customer_address1;
	$order->billing_street2 = (string)$transaction->customer_address2;
	$order->billing_city = (string)$transaction->customer_city;  

	$order->billing_zone = foxycart_uc_get_zone_id((string)$transaction->customer_state); 
	$order->billing_postal_code = (string)$transaction->customer_postal_code;
	
	$country_result = uc_get_country_data(Array('country_iso_code_2' => (string)$transaction->billing_country));
	
	foreach ($transaction->transaction_details[0] as $transaction_detail) {
		foxycart_uc_df_add_product_to_order($order, $transaction_detail);
	}

	foxycart_log("Saving Order");
	uc_order_save($order);
	foxycart_log("Order Saved");

	foxycart_log("Adding line items");
	uc_order_line_item_add($order->order_id, 'product_total', 'Product Total', (float)$transaction->product_total);
	uc_order_line_item_add($order->order_id, 'generic', 'Tax Total', (float)$transaction->tax_total);
	uc_order_line_item_add($order->order_id, 'generic', 'Shipping Total', (float)$transaction->shipping_total);
	
	$order->order_total = $transaction->order_total;

	foxycart_log("Adding payment");
	foxycart_uc_df_add_payment_to_order($order, $transaction);
	foxycart_log("Adding shipping");
	foxycart_uc_df_add_shipping_to_order($order, $transaction);
	
	foxycart_log("Reloading order");
	$order = uc_order_load($order->order_id);

	return $order;
}
?>