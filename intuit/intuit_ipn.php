<?php
require_once("intuit.php");


$intuit_settings = get_option('event_espresso_intuit_settings');

// print_r($_POST);

$environment = "sandbox"; // Set to "live" when live.
$environment = "live";

$data = get_lead_data($_POST, $environment);
$message = create_transaction($data);


$url = ($environment == 'live') ? 'https://merchantaccount.quickbooks.com/j/AppGateway' : 'https://merchantaccount.ptc.quickbooks.com/j/AppGateway';

// create the message we will send

// post the query and get the response


$strResponse = post_query($url, $message);
$response = parse_response($strResponse);


switch ($response['statusCode'])
{
	case '0':
		/* Approved. Say thank you and update the database */

		$payment_status = 'Completed';	  
		$payment_date = date("m-d-Y");

		?>
		<h2><?php _e('Thank You!','event_espresso'); ?></h2>
		<p><?php _e('Your transaction has been processed.  You should receive an email notification with a confirmation.','event_espresso'); ?></p>		
		<?php
		break;
	default:
		// Declined
		?>
		<h2><?php _e('Sorry - an error has occurred','event_espresso'); ?></h2>
		<p><?php _e('There was an error processing your payment request.  Please try again.','event_espresso'); ?></p>				
		<?php
		$_SESSION['err'] = "[" . $response['statusCode'] . "] " . $response['statusSeverity'] . "  - " . $response['statusMessage'];
		$payment_status = 'Payment Declined';	  
		break;
}
echo "<!-- test -->";


//Add details to the DB
global $wpdb;

$sql = "UPDATE ". EVENTS_ATTENDEE_TABLE . " SET payment_status = '" . $payment_status . "', payment_date ='" . $payment_date . "', transaction_details = '" . serialize($response) . "'  WHERE registration_id ='" . espresso_registration_id($attendee_id) . "'";

$wpdb->query($sql);		

