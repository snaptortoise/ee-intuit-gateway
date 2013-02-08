<?php
/***
	Event Espresso - Intuit Gateway Add-On
***/

function event_espresso_intuit_settings(){

	/* Updated the settings */

	if (isset($_POST['update_intuit_settings'])) {

			$intuit_settings = get_option('event_espresso_intuit_settings');
			print_r($intuit_settings);
			// $authnet_settings['authnet_login_id'] = $_POST['authnet_login_id'];
			// $authnet_settings['authnet_transaction_key'] = $_POST['authnet_transaction_key'];
			// $authnet_settings['image_url'] = $_POST['image_url'];
			// $authnet_settings['use_sandbox'] = $_POST['use_sandbox'];
			// $authnet_settings['surcharge'] = $_POST['surcharge'];
			// $authnet_settings['bypass_payment_page'] = $_POST['bypass_payment_page'];
			// $authnet_settings['button_url'] = $_POST['button_url'];

		update_option('event_espresso_intuit_settings', $intuit_settings);
		echo '<div id="message" class="updated fade"><p><strong>'.__('Intuit settings saved.','event_espresso').'</strong></p></div>';
	}	

	/* Display the table in the backend */
	?><div class="metabox-holder">
	    <div class="postbox">
	      <h3>
	        <?php _e('Intuit Settings','event_espresso'); ?>
	      </h3>

		 <?php
						if ($_REQUEST['activate_intuit'] == 'true'){
							add_option("events_intuit_active", 'true', '', 'yes');
							add_option("event_espresso_intuit_settings", '', '', 'yes');
						}
						if ($_REQUEST['reactivate_intuit'] == 'true'){
							update_option( 'events_intuit_active', 'true');
						}
						if ($_REQUEST['deactivate_intuit'] == 'true'){
							update_option( 'events_intuit_active', 'false');
						}
						echo '<ul>';
						switch (get_option('events_intuit_active')){
							case 'false':
							echo '<li>Intuit is installed.</li>';
								echo '<li style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&reactivate_intuit=true\';" class="green_alert pointer"><strong>' . __('Activate Intuit?','event_espresso') . '</strong></li>';
							break;
							case 'true':
							echo '<li style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&deactivate_intuit=true\';" class="red_alert pointer"><strong>' . __('Deactivate Intuit?','event_espresso') . '</strong></li>';
							event_espresso_display_intuit_settings();
							break;
							default:
								echo '<li style="width:50%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&activate_intuit=true\';" class="yellow_alert pointer"><strong>' . __('The Intuit gateway is installed. Would you like to activate it?','event_espresso') . '</strong></li>';
							break;
						}
						echo '</ul>';
		?>
		</div>
		</div>
		<?php	
		}
	
	/* Intuit Settings Form */
	function event_espresso_display_intuit_settings() {
		global $org_options;

		$intuit_settings = get_option('event_espresso_intuit_settings');
		?>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<table width="90%" border="0">
				<tr>
					<td>
						
					</td>
				</tr>
			</table>
			<input type="hidden" name="update_intuit_settings" value="update_intuit_settings">
			<p><input class="button-primary" type="submit" name="Submit" value="<?php  _e('Update Intuit Settings','event_espresso') ?>" id="save_intuit_settings" />
			</p>
			</form>
		<?php
	}
