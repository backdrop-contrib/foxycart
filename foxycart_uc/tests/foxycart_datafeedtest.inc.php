<?php

require_once(dirname(__FILE__).'/../../includes/foxycart_rc4crypt.inc.php');

function foxycart_uc_test_get_order_XML($product = null, $user = null)
{

    if ($product == null) {
        $product = new stdClass();
        $product->model = "Sample Wrench";
        $product->sell_price = 9.95;
    }
    if ($user == null) {
        $user = new stdClass();
        $user->mail = 'test@example.com';
    }
    $tax = 2.06;
    $shipping = 12.35;
    $product_adjustments = 0;

    $product_attributes = uc_attribute_load_multiple(array(), 'product', $product->nid);
    $XMLattributes = "";
    foreach($product_attributes as $attribute) {
$XMLattributes .= "
    <transaction_detail_option>
        <product_option_name>$attribute->name</product_option_name>
        <product_option_value>" . $attribute->options[1]->name . "</product_option_value>
        <price_mod>" . $attribute->options[1]->price . "</price_mod>
        <weight_mod>" . $attribute->options[1]->weight . "</weight_mod>
    </transaction_detail_option>";
    $product_adjustments += $attribute->options[1]->price;
    }
    $product_total_adjusted = $product->sell_price + $product_adjustments;
    $order_total = $product_total_adjusted + $tax + $shipping;

$XMLOutput = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<foxydata>
  <transactions>
        <transaction>
            <id>123123123</id>
            <store_id>123</store_id>
            <store_version>1.0</store_version>
            <is_test>1</is_test>
            <is_hidden>0</is_hidden>
            <data_is_fed>0</data_is_fed>
            <transaction_date>2013-02-02 12:21:08</transaction_date>
            <payment_type>plastic</payment_type>
            <payment_gateway_type>authorize</payment_gateway_type>
            <processor_response>Authorize.net Transaction ID:2123123123</processor_response>
            <processor_response_details/>
            <purchase_order/>
            <cc_number_masked>xxxxxxxxxxxx1111</cc_number_masked>
            <cc_type>Visa</cc_type>
            <cc_exp_month>03</cc_exp_month>
            <cc_exp_year>2013</cc_exp_year>
            <cc_start_date_month/>
            <cc_start_date_year/>
            <cc_issue_number/>
            <minfraud_score>0</minfraud_score>
            <paypal_payer_id/>
            <customer_id>4387746</customer_id>
            <is_anonymous>0</is_anonymous>
            <customer_first_name>Test</customer_first_name>
            <customer_last_name>User</customer_last_name>
            <customer_company/>
            <customer_address1>123 Main St</customer_address1>
            <customer_address2/>
            <customer_city>CustomerPlace</customer_city>
            <customer_state>MI</customer_state>
            <customer_postal_code>12345</customer_postal_code>
            <customer_country>US</customer_country>
            <customer_phone/>
            <customer_email>$user->mail</customer_email>
            <customer_ip>1.2.3.4</customer_ip>
            <shipping_first_name>Test</shipping_first_name>
            <shipping_last_name>User</shipping_last_name>
            <shipping_company/>
            <shipping_address1>123 Main St</shipping_address1>
            <shipping_address2/>
            <shipping_city>ShippingPlace</shipping_city>
            <shipping_state>AK</shipping_state>
            <shipping_postal_code>54321</shipping_postal_code>
            <shipping_country>US</shipping_country>
            <shipping_phone/>
            <shipto_shipping_service_description>USPS Priority Mail Medium Flat Rate Box</shipto_shipping_service_description>
            <product_total>$product_total_adjusted</product_total>
            <tax_total>$tax</tax_total>
            <shipping_total>$shipping</shipping_total>
            <order_total>$order_total</order_total>
            <taxes>
                <tax>
                    <tax_rate>9.2500</tax_rate>
                    <tax_name>Tennessee</tax_name>
                    <tax_amount>2.0628</tax_amount>
                </tax>
            </taxes>
            <discounts/>
            <customer_password><![CDATA[\$S\$DvYkoe3RIPa11cQ9wpzWyBUqvlOAIPokfk2Eefb5y/Zh2pb9LzZ1]]></customer_password>
            <customer_password_salt/>
            <customer_password_hash_type>backdrop</customer_password_hash_type>
            <customer_password_hash_config>15</customer_password_hash_config>
            <custom_fields/>
            <transaction_details>
                <transaction_detail>
                    <product_name>$product->title</product_name>
                    <product_price>$product->sell_price</product_price>
                    <product_quantity>1</product_quantity>
                    <product_weight>$product->weight</product_weight>
                    <product_code>$product->model</product_code>
                    <image>http://demo.foxypal.com/sites/demo.foxypal.com/files/styles/thumbnail/public/wrench_0.jpg</image>
                    <url>http://demo.foxypal.com/node/$product->nid</url>
                    <length>0</length>
                    <width>0</width>
                    <height>0</height>
                    <downloadable_url/>
                    <sub_token_url/>
                    <subscription_frequency/>
                    <subscription_startdate>0000-00-00</subscription_startdate>
                    <subscription_nextdate>0000-00-00</subscription_nextdate>
                    <subscription_enddate>0000-00-00</subscription_enddate>
                    <is_future_line_item>0</is_future_line_item>
                    <shipto/>
                    <category_description>Default for all products</category_description>
                    <category_code>DEFAULT</category_code>
                    <product_delivery_type>shipped</product_delivery_type>
                    <transaction_detail_options>
                        $XMLattributes
                        <transaction_detail_option>
                            <product_option_name>nid</product_option_name>
                            <product_option_value>$product->nid</product_option_value>
                            <price_mod>0</price_mod>
                            <weight_mod>0.000</weight_mod>
                        </transaction_detail_option>
                    </transaction_detail_options>
                </transaction_detail>
            </transaction_details>
            <shipto_addresses/>
            <attributes/>
        </transaction>
  </transactions>
</foxydata>
XML;

return $XMLOutput;
}

function foxycart_uc_encrypt_feed($feed) {
    $key = foxycart_get_apikey();
    if ($key == '') {
        throw new Exception('No api key found');
    }
    return rc4crypt::encrypt($key,$feed);
}

?>
