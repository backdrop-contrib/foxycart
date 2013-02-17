<?php
function foxycart_api_getuser($email) {
	$output = "";
	$foxyData = array();
	$foxyData["api_token"] = foxycart_get_apikey();
	$foxyData["api_action"] = "customer_get";
	$foxyData["customer_email"] = $email;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://" . foxycart_get_domain() . "/api");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $foxyData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	// If you get SSL errors, you can uncomment the following, or ask your host to add the appropriate CA bundle
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = trim(curl_exec($ch));

	// The following if block will print any CURL errors you might have
	if ($response == false) {
		$output .=  "CURL Error: " . curl_error($ch);
	}
	curl_close($ch);

	$foxyResponse = new SimpleXMLElement($response);
	return $foxyResponse;
}


/* Used to synchronize user accounts with FoxyCart.com when a user account is updated */
function foxycart_user_presave(&$edit, $account, $category) {

	foxycart_log("foxycart_user_presave: begin");
	if ( variable_get('foxycart_user_sync', true) == true && isset($edit['pass']) ) {
		foxycart_log("foxycart_api_update_user: begin");
		$foxyresponse = foxycart_api_update_user($edit['mail'], $edit['pass']);
		foxycart_log("foxycart_api_update_user: end, pw: " . $edit['pass'] . " response: " . (string)$foxyresponse->result);
		if ((string)$foxyresponse->result == 'SUCCESS') {
			$edit['data']['fc_customer_id'] = (integer)$foxyresponse->customer_id;
		}
	}
}

function foxycart_update_drupal_user($fc_customer_id, $fc_email, $fc_password, $user_id = NULL) {

	$user = FALSE;
	if ($user_id) {
		$user = user_load($user_id);
	}
	if (!$user) {
		foxycart_log("foxycart_update_drupal_user: Did not find user by user_id");
		// If we didn't find it try to load by email address
		$user = user_load_by_mail($fc_email);
		if (!$user) {
			foxycart_log("foxycart_update_drupal_user: Did not find user by email address");
			
			// create a new user
			$user = array("is_new" => true);
			
			$user = array(
			  'name' => $fc_email,
			  'pass' => 'password', // temporary because user_save will hash the password, but it's already hashed from FoxyCart
			  'mail' => $fc_email,
			  'status' => 1,
			  'init' => $fc_email,
			  'data' => array('fc_customer_id' => $fc_customer_id)
			);
			$user = user_save(null, $user);				
			foxycart_log("foxycart_update_drupal_user: Created user: " . $user->uid);
		} else {
			foxycart_log("foxycart_update_drupal_user: Found user by email");
		}
	} else {
		foxycart_log("foxycart_update_drupal_user: Found user by user_id");
	}
	
	if (isset($user->uid)) {
		$sql = "UPDATE {users} set pass = :pass where uid = :uid";
		$result = db_query($sql, array(':pass' => $fc_password, ':uid' => $user->uid));
	}
	
	return $user;
}

function foxycart_api_update_user($email, $pass) {
	$output = "";
	$foxyResponse = new stdClass();
	$foxyResponse->result = "FAIL";
	$foxyData = array();
	$foxyData["api_token"] = foxycart_get_apikey();
	$foxyData["api_action"] = "customer_save";
	$foxyData["customer_email"] = $email;
	$foxyData["customer_password_hash"] = $pass;
//	$foxyData["customer_password"] = $pass;
	if (foxycart_get_domain() != '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://" . foxycart_get_domain() . "/api");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $foxyData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		// If you get SSL errors, you can uncomment the following, or ask your host to add the appropriate CA bundle
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = trim(curl_exec($ch));

		// The following if block will print any CURL errors you might have
		if ($response == false) {
			$output .=  "CURL Error: " . curl_error($ch);
			return $output;
		}
		curl_close($ch);

		$foxyResponse = new SimpleXMLElement($response);
	}
	return $foxyResponse;
}

/* This function is called by foxycart.com to verify that the user is authenticated */
function foxycart_process_sso() {
	global $base_root;
	global $user;

	$user_is_authenticated = true;
	$foxycart_api_key = foxycart_get_apikey();
	$foxycart_domain = foxycart_get_domain();

	$return_hash = '';
	$redirect_url = 'https://' . $foxycart_domain . '/checkout';
	$customer_id = 0;
	$timestamp = 0;
	$fcsid = '';

	foxycart_log("sso: Starting");
	
	// URL for non_auth checkout
	$full_redirect = $base_root . '/user';

	if (isset($_REQUEST['timestamp']) && isset($_REQUEST['fcsid'])) {
		$fcsid = $_REQUEST['fcsid'];
		$timestamp = $_REQUEST['timestamp'] + (60 * 30); // valid for 30 minutes;

		if ($user->uid) {
			if (!isset($user->fc_customer_id)) {
				foxycart_log("sso: foxycart_api_getuser1");
				
				$foxyresponse = foxycart_api_getuser($user->mail);
				if ((string)$foxyresponse->result == 'SUCCESS') {
					$customer_id = (string)$foxyresponse->customer_id;
					foxycart_log("sso: found customer_id: $customer_id");
					user_save($user, array('data' => array('fc_customer_id' => $customer_id )));
				}
				else {
					foxycart_log("foxycart_api_getuser");
					$foxyresponse = foxycart_api_update_user($user->mail, $user->pass);
					if ((string)$foxyresponse->result == 'SUCCESS') {
						$customer_id = (integer)$foxyresponse->customer_id;
						foxycart_log("sso: Created new customer_id: $customer_id");
						foxycart_log("sso: user_save");
						user_save($user, array('data' => array('fc_customer_id' => $customer_id )));
						foxycart_log("sso: user_save done");
					}
				}
			}
			else {
				$customer_id = $user->fc_customer_id;
				foxycart_log("sso: already had customer_id: $customer_id");
			}
			$return_hash = sha1($customer_id . '|' . $timestamp . '|' . $foxycart_api_key);
			$full_redirect = $redirect_url . '?fc_auth_token=' . $return_hash . '&fc_customer_id=' . $customer_id . '&timestamp=' . $timestamp . '&fcsid=' . $fcsid;
		}
		else {
			$return_hash = sha1($customer_id . '|' . $timestamp . '|' . $foxycart_api_key);
			$full_redirect = $redirect_url . '?fc_auth_token=' . $return_hash . '&fc_customer_id=' . $customer_id . '&timestamp=' . $timestamp . '&fcsid=' . $fcsid;
		}
	}
	foxycart_log("sso: returning - " . $full_redirect);
	
	drupal_add_http_header('Location', $full_redirect);
}
?>