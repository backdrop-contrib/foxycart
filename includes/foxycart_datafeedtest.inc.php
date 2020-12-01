<?php

include_once "includes/foxycart_rc4crypt.inc.php";

/**
 * FoxyCart Test XML Generator
 * 
 * @link http://wiki.foxycart.com/integration:misc:test_xml_post
 * @version 0.6a
 */
/*
	DESCRIPTION: =================================================================
	The purpose of this file is to help you set up and debug your FoxyCart XML DataFeed scripts.
	It's designed to mimic FoxyCart.com and send encrypted and encoded XML to a URL of your choice.
	It will print out the response that your script gives back, which should be "foxy" if successful.
	
	USAGE: =======================================================================
	- Place this file somewhere on your server.
	- Edit the $myURL to the URL where your XML processing script is located.
	- Edit the $myKey to match the key you put in your FoxyCart admin.
	- Edit the $XMLOutput if you have specific data you'd like to test.
	- Save.
	- Load this file in your browser. It will send XML to your script just like FoxyCart would
	  after an order on your store, and will output what your script returns.
	- Test until you get your script working properly.
	
	REQUIREMENTS: ================================================================
	- PHP
	- cURL support in PHP
*/

// ======================================================================================
// CHANGE THIS DATA:
// Set the URL you want to post the XML to.
// Set the key you entered in your FoxyCart.com admin.
// Modify the XML below as necessary.  DO NOT modify the structure, just the data
// ======================================================================================
function foxycart_datafeed_test()
{
	global $base_root;

	$myURL = $base_root . '/foxycart/datafeed';
	$myKey = foxycart_get_apikey();

	if ($myKey == '') return;

	// You can change the test data below if you'd like to test specific fields.
	// For example, you may want to set it up to mirror 
$XMLOutput = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<foxydata>
	<transactions>
		<transaction>
			<id><![CDATA[52768]]></id>
			<store_id><![CDATA[9]]></store_id>
			<store_version><![CDATA[0.7.0]]></store_version>
			<is_test><![CDATA[1]]></is_test>
			<transaction_date><![CDATA[2010-08-19 13:50:00]]></transaction_date>
			<processor_response><![CDATA[Authorize.net Transaction ID:2154082729]]></processor_response>
			<customer_id><![CDATA[193]]></customer_id>
			<is_anonymous><![CDATA[0]]></is_anonymous>
			<customer_first_name><![CDATA[Jörgé •™¡ªº]]></customer_first_name>
			<customer_last_name><![CDATA[Cantú]]></customer_last_name>
			<customer_company><![CDATA[-moz-bi ding:url(//businessi fo.co.uk/]]></customer_company>
			<customer_address1><![CDATA[First &amp; Second Street]]></customer_address1>
			<customer_address2><![CDATA[]]></customer_address2>
			<customer_city><![CDATA[seal beach]]></customer_city>
			<customer_state><![CDATA[CA]]></customer_state>
			<customer_postal_code><![CDATA[90740]]></customer_postal_code>
			<customer_country><![CDATA[US]]></customer_country>
			<customer_phone><![CDATA[]]></customer_phone>
			<customer_email><![CDATA[test@example.com]]></customer_email>
			<customer_ip><![CDATA[123.123.123.123]]></customer_ip>
			<shipping_first_name><![CDATA[ShipFN]]></shipping_first_name>
			<shipping_last_name><![CDATA[ShipLN]]></shipping_last_name>
			<shipping_company><![CDATA[]]></shipping_company>
			<shipping_address1><![CDATA[Ship Addr 1]]></shipping_address1>
			<shipping_address2><![CDATA[Ship Addr 2]]></shipping_address2>
			<shipping_city><![CDATA[ShipCity]]></shipping_city>
			<shipping_state><![CDATA[TN]]></shipping_state>
			<shipping_postal_code><![CDATA[37179]]></shipping_postal_code>
			<shipping_country><![CDATA[US]]></shipping_country>
			<shipping_phone><![CDATA[123-456-7890]]></shipping_phone>
			<shipto_shipping_service_description><![CDATA[USPS Priority Mail Flat Rate Envelope]]></shipto_shipping_service_description>
			<purchase_order><![CDATA[]]></purchase_order>
			<cc_number_masked><![CDATA[xxxxxxxxxxxx4242]]></cc_number_masked>
			<cc_type><![CDATA[Visa]]></cc_type>
			<cc_exp_month><![CDATA[08]]></cc_exp_month>
			<cc_exp_year><![CDATA[2011]]></cc_exp_year>
			<product_total><![CDATA[12.35]]></product_total>
			<tax_total><![CDATA[0]]></tax_total>
			<shipping_total><![CDATA[7.52]]></shipping_total>
			<order_total><![CDATA[19.87]]></order_total>
			<payment_gateway_type><![CDATA[authorize]]></payment_gateway_type>
			<receipt_url><![CDATA[http://themancan.foxycart.com/receipt?id=28a313c5217794e89a989ccd69eefa40]]></receipt_url>
			<taxes/>
			<discounts/>
			<customer_password><![CDATA[912ec803b2ce49e4a541068d495ab570]]></customer_password>
			<custom_fields>
				<custom_field>
					<custom_field_name><![CDATA[example_hidden]]></custom_field_name>
					<custom_field_value><![CDATA[value_1]]></custom_field_value>
					<custom_field_is_hidden><![CDATA[1]]></custom_field_is_hidden>
				</custom_field>
				<custom_field>
					<custom_field_name><![CDATA[Hidden_Value]]></custom_field_name>
					<custom_field_value><![CDATA[My Name Is_Jonas©;&amp;texture &amp;_ smoothness=rough||929274e2c2b22d8d51540d8bf657eef133121d7e67c05284687edcd8bfdcd946]]></custom_field_value>
					<custom_field_is_hidden><![CDATA[1]]></custom_field_is_hidden>
				</custom_field>
			</custom_fields>
			<transaction_details>
				<transaction_detail>
					<product_name><![CDATA[Example Product with Hex and Plus Spaces]]></product_name>
					<product_price><![CDATA[10.00]]></product_price>
					<product_quantity><![CDATA[2]]></product_quantity>
					<product_weight><![CDATA[4.000]]></product_weight>
					<product_code><![CDATA[abc123zzz]]></product_code>
					<downloadable_url><![CDATA[]]></downloadable_url>
					<sub_token_url><![CDATA[]]></sub_token_url>
					<subscription_frequency><![CDATA[]]></subscription_frequency>
					<subscription_startdate><![CDATA[0000-00-00]]></subscription_startdate>
					<subscription_nextdate><![CDATA[0000-00-00]]></subscription_nextdate>
					<subscription_enddate><![CDATA[0000-00-00]]></subscription_enddate>
					<is_future_line_item>0</is_future_line_item>
					<shipto><![CDATA[]]></shipto>
					<category_description><![CDATA[Discount: Price: Percentage]]></category_description>
					<category_code><![CDATA[discount_price_percentage]]></category_code>
					<product_delivery_type><![CDATA[shipped]]></product_delivery_type>
					<transaction_detail_options>
						<transaction_detail_option>
							<product_option_name><![CDATA[color]]></product_option_name>
							<product_option_value><![CDATA[red]]></product_option_value>
							<price_mod><![CDATA[-4.000]]></price_mod>
							<weight_mod><![CDATA[0.000]]></weight_mod>
						</transaction_detail_option>
						<transaction_detail_option>
							<product_option_name><![CDATA[Quantity Discount]]></product_option_name>
							<product_option_value><![CDATA[$0.50]]></product_option_value>
							<price_mod><![CDATA[0.500]]></price_mod>
							<weight_mod><![CDATA[0.000]]></weight_mod>
						</transaction_detail_option>
						<transaction_detail_option>
							<product_option_name><![CDATA[Price Discount Amount]]></product_option_name>
							<product_option_value><![CDATA[-5%]]></product_option_value>
							<price_mod><![CDATA[-0.325]]></price_mod>
							<weight_mod><![CDATA[0.000]]></weight_mod>
						</transaction_detail_option>
					</transaction_detail_options>
				</transaction_detail>
			</transaction_details>
			<shipto_addresses/>
		</transaction>
	</transactions>
</foxydata>
XML;
	
	// ======================================================================================
	// YOU'RE DONE.  DO NOT MODIFY BELOW THIS LINE.
	// The code below this line should not be modified unless you have a good reason to do so.
	// ======================================================================================
	
	// ======================================================================================
	// ENCRYPT YOUR XML
	// Modify the include path to go to the rc4crypt file.
	// ======================================================================================
	$XMLOutput_encrypted = rc4crypt::encrypt($myKey,$XMLOutput);
	$XMLOutput_encrypted = urlencode($XMLOutput_encrypted);
	
	
	// ======================================================================================
	// POST YOUR XML TO YOUR SITE
	// Do not modify.
	// ======================================================================================
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $myURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array("FoxyData" => $XMLOutput_encrypted));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	// Shared hosting users on GoDaddy or other hosts may need to uncomment the following lines:
	// curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	// curl_setopt($ch, CURLOPT_PROXY,"http://64.202.165.130:3128"); // Replace this IP with whatever your host specifies.
	// End shared hosting options
	$response = curl_exec($ch);
	curl_close($ch);
	
	
	// header("content-type:text/plain");
	return $response;
}

?>
