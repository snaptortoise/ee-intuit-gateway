<?php

global $org_options;

/*
	We should see these vars:
		- $fname
		- $lname
		- $attendee_id
		- $attendee_email
		- $address
		- $city
		- $state
		- $zip
		- $event_cost
		- $event_name
		- $quantity
*/

include("intuit.php");
$myIntuit = new Intuit;

$myIntuit->enableTestMode();


// $myIntuit->setUserInfo($authnet_login_id, $authnet_transaction_key);


// $myIntuit->addField('x_Relay_URL', get_option('siteurl').'/?page_id='.$org_options['notify_url']);
// $myIntuit->addField('x_Description', stripslashes_deep($event_name) . ' | '.__('Reg. ID:','event_espresso').' '.$attendee_id. ' | '.__('Name:','event_espresso').' '. stripslashes_deep($fname . ' ' . $lname) .' | '.__('Total Registrants:','event_espresso').' '.$quantity);
// $myIntuit->addField('x_Amount', number_format($event_cost,2));
// $myIntuit->addField('x_Logo_URL', $image_url);
// $myIntuit->addField('x_Invoice_num', 'au-'.event_espresso_session_id());
// //Post variables
// $myIntuit->addField('x_Cust_ID', $attendee_id);
// $myIntuit->addField('x_first_name', $fname);
// $myIntuit->addField('x_last_name', $lname);
// 
// $myIntuit->addField('x_Email', $attendee_email);
// $myIntuit->addField('x_Address', $address);
// $myIntuit->addField('x_City', "pp");
// $myIntuit->addField('x_State', $state);
// $myIntuit->addField('x_Zip', $zip);


// $button_url = EVENT_ESPRESSO_PLUGINFULLURL . "gateways/authnet/btn_cc_vmad.gif";
// $myIntuit->submitButton($button_url, 'intuit');//Display payment button
?>

<form id="intuit_payment_form" name="intuit_payment_form" method="post" action="<?php echo get_option('siteurl').'/?page_id='.$org_options['notify_url']; ?>" style="width:100%;float:left">

	<div style="float:left;width:320px">
	<h4><?php _e('Billing Information', 'event_espresso') ?></h4>
  <p>
    <label for="first_name"><?php _e('First Name', 'event_espresso'); ?></label>
    <input name="first_name" type="text" id="first_name" value="<?php echo $fname ?>" />
  </p>
  <p>
    <label for="last_name"><?php _e('Last Name', 'event_espresso'); ?></label>
    <input name="last_name" type="text" id="last_name" value="<?php echo $lname ?>" />
  </p>
  <p>
    <label for="email"><?php _e('Email Address', 'event_espresso'); ?></label>
    <input name="email" type="text" id="email" value="<?php echo $attendee_email ?>" />
  </p>
  <p>
    <label for="address"><?php _e('Address', 'event_espresso'); ?></label>
    <input name="address" type="text" id="address" value="<?php echo $address ?>" />
  </p>
  <p>
    <label for="city"><?php _e('City', 'event_espresso'); ?></label>
    <input name="city" type="text" id="city" value="<?php echo $city ?>" />
  </p>
  <p>
    <label for="state"><?php _e('State', 'event_espresso'); ?></label>
    <input name="state" type="text" id="state" value="<?php echo $state ?>" />
  </p>
  <p>
    <label for="zip"><?php _e('Zip', 'event_espresso'); ?></label>
    <input name="zip" type="text" id="zip" value="<?php echo $zip ?>" />
  </p>
	</div>

  <div style="float:left;width:300px">
  <h4><?php _e('Credit Card Information', 'event_espresso'); ?></h4>
  <p>
    <label for="card_num"><?php _e('Card Number', 'event_espresso'); ?></label>
    <input type="text" name="cc_num" id="cc_num" />
  </p>
	<p>
	    <label for="exp_date"><?php _e('Expiration Date', 'event_espresso'); ?></label>
		<select name="cc_exp_month" id="cc_exp_month">
			<option value="01">Jan</option>
						<option value="02">Feb</option>
						<option value="03">Mar</option>
						<option value="04">Apr</option>
						<option value="05">May</option>
						<option value="06">Jun</option>
						<option value="07">Jul</option>
						<option value="08">Aug</option>
						<option value="09">Sept</option>
						<option value="10">Oct</option>
						<option value="11">Nov</option>
						<option value="12">Dec</option>
		</select>
		<select name="cc_exp_year" id="cc_exp_year">
			<?php
			for ($i=0; $i <= 19; $i++) {
				?>
				<option value="<?= (date("Y")+$i) ?>"><?= (date("Y")+$i) ?></option>
			<?php
			}
			?>
		</select>
	</p>
  <p>
	<b>CCV:</b>
			<input type="password" name="cvv" value="" />
  </p>
  <input name="amount" type="hidden" value="<?php echo number_format($event_cost,2) ?>" />
  <input name="invoice_num" type="hidden" value="<?php echo 'au-'.event_espresso_session_id() ?>" />
  <input name="intuit" type="hidden" value="true" />
  <input name="x_cust_id" type="hidden" value="<?php echo $attendee_id ?>" />
  
  <input name="intuit_submit" id="intuit_submit" type="submit" value="<?php _e('Complete Purchase &raquo;', 'event_espresso'); ?>" />
	<div id="cards"></div>

	</div>
</form>

<div style=" clear:both; margin-bottom:10px;"></div>

