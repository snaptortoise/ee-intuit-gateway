<?php
 /**
 * Authorize.net Class
 *
 * Author 		Seth Shoultes
 * @package		Event Espresso Authorize.net SIM Gateway
 * @category	Library
 */
$intuit_gateway_version = '1.0';
define('CRYPT_KEY', '***YOU NEED TO GET YOUR CRYPT KEY FROM INTUIT***');

class Intuit extends PaymentGateway
{
    /**
     * Login ID of authorize.net account
     *
     * @var string
     */
    public $login;
    /**
     * Secret key from authorize.net account
     *
     * @var string
     */
    public $secret;
    /*
	 * Initialize the Authorize.net gateway
	 *
	 * @param none
	 * @return void
	 */
	public function __construct()
	{
        parent::__construct();
        // Some default values of the class
		$environment == "notlive";
		$this->gatewayUrl = 'https://merchantaccount.quickbooks.com/j/AppGateway';
		$this->ipnLogFile = 'intuit.ipn_results.log';
		// Populate $fields array with a few default
		// $this->addField('x_Version',        '3.0');
		//         $this->addField('x_Show_Form',      'PAYMENT_FORM');
		// $this->addField('x_Relay_Response', 'TRUE');
	}
    /**
     * Enables the test mode
     *
     * @param none
     * @return none
     */
    public function enableTestMode()
    {
        $this->testMode = TRUE;
        // $this->addField('x_Test_Request', 'TRUE');
        //$this->gatewayUrl = 'https://test.authorize.net/gateway/transact.dll';  //Used for dev testing

		$this->gatewayUrl = 'https://merchantaccount.ptc.quickbooks.com/j/AppGateway'; //Used for non-dev testing 
    }
    /**
     * Set login and secret key
     *
     * @param string user login
     * @param string secret key
     * @return void
     */
    // public function setUserInfo($login, $key)
    // {
    //     $this->login  = $login;
    //     $this->secret = $key;
    // }
    /**
     * Prepare a few payment information
     *
     * @param none
     * @return void
     */
    public function prepareSubmit()
    {
        $this->addField('x_Login', $this->login);
        $this->addField('x_fp_sequence', $this->fields['x_Invoice_num']);
        $this->addField('x_fp_timestamp', time());
        $data = $this->fields['x_Login'] . '^' .
                $this->fields['x_Invoice_num'] . '^' .
                $this->fields['x_fp_timestamp'] . '^' .
                $this->fields['x_Amount'] . '^';
        $this->addField('x_fp_hash', $this->hmac($this->secret, $data));

		$this->addField("ClientDateTime", substr(date('c', time()), 0 , 19));
		$this->addField("ApplicationLogin", "*** APPLICATION LOGIN GOES HERE ***");
		$this->addField("TransRequestID", '*** TRANSACTION REQUEST ID ***');
		$this->addField("ConnectionTicket", "*** CONNECTION TICKET ***");
		$this->addField("CreditCardNumber", "4111111111111111");
		$this->addField("ExpirationMonth", "12");
		$this->addField("ExpirationYear", "2011");
		$this->addField("Amount", "10.00");
		$this->addField("NameOnCard", "Alva Ryan");
		$this->addField("CreditCardNumber", "123 Main St.");
		$this->addField("CreditCardPostalCode", "73717");
		$this->addField("CardSecurityCode", "123");

		// $transaction = array (
		// 	"ClientDateTime"		=>	substr(date('c', time()), 0 , 19),
		// 	"ApplicationLogin"		=>	($environment == 'live') ? "** live appliation login **" : "** test appliation login **",
		// 	"TransRequestID"		=>	($environment == 'live') ? session_id() : '457353214088',
		// 	"ConnectionTicket"		=>	($environment == 'live') ? INTUIT_CONNECTION_TICKET : "** test connection ticket **",
		// 	"CreditCardNumber"		=>	($environment == 'live') ? $args['cc_num'] : "4111111111111111",
		// 	"ExpirationMonth"		=>	($environment == 'live') ? str_pad($args['cc_exp_month'], 2, '0', STR_PAD_LEFT) : "12",
		// 	"ExpirationYear"		=>	($environment == 'live') ? $args['cc_exp_year'] : "2013",
		// 	"Amount"				=>	($environment == 'live') ? number_format($args['chargetotal'], 2) : "10.00",
		// 	"NameOnCard"			=>	($environment == 'live') ? $args['billing_name'] : "Jane Doe",
		// 	"CreditCardAddress"		=>	($environment == 'live') ? $args['address1'] : "123 Main St.",
		// 	"CreditCardPostalCode"	=>	($environment == 'live') ? $args['zip'] : "73717",
		// 	"CardSecurityCode"		=>	($environment == 'live') ? $args['cvv'] : "123"
		// );

		// encode the data so that special characters will not cause problems
		// foreach ($lead as $name => $value) {
		// 	$lead[$name] = urlencode($value);
		// }
		// 
		// return $transaction;
		// 


    }
    /**
	 * Validate the IPN notification
	 *
	 * @param none
	 * @return boolean
	 */
	public function validateIpn()
	{
	    foreach ($_POST as $field=>$value)
		{
			$this->ipnData["$field"] = $value;
		}
        $invoice    = intval($this->ipnData['x_invoice_num']);
        $pnref      = $this->ipnData['x_trans_id'];
        $amount     = doubleval($this->ipnData['x_amount']);
        $result     = intval($this->ipnData['x_response_code']);
        $respmsg    = $this->ipnData['x_response_reason_text'];
        $md5source  = $this->secret . $this->login . $this->ipnData['x_trans_id'] . $this->ipnData['x_amount'];
        $md5        = md5($md5source);
		if ($result == '1')
		{
		 	// Valid IPN transaction.
		 	$this->logResults(true);
		 	return true;
		}
		else if ($result != '1')
		{
		 	$this->lastError = $respmsg;
			$this->logResults(false);
			return false;
		}
        else if (strtoupper($md5) != $this->ipnData['x_MD5_Hash'])
        {
            $this->lastError = 'MD5 mismatch';
            $this->logResults(false);
            return false;
        }
	}
    /**
     * RFC 2104 HMAC implementation for php.
     *
     * @author Lance Rushing
     * @param string key
     * @param string date
     * @return string encoded hash
     */
    private function hmac ($key, $data)
    {
       $b = 64; // byte length for md5
       if (strlen($key) > $b) {
           $key = pack("H*",md5($key));
       }
       $key  = str_pad($key, $b, chr(0x00));
       $ipad = str_pad('', $b, chr(0x36));
       $opad = str_pad('', $b, chr(0x5c));
       $k_ipad = $key ^ $ipad ;
       $k_opad = $key ^ $opad;
       return md5($k_opad  . pack("H*", md5($k_ipad . $data)));
    }
}


/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/


// This include contains the function that produces
// the XML for intuit
require_once('intuit_template.php');
//

//define('INTUIT_CONNECTION_TICKET', '132');

// Posts the complete lead  to DTX.
function post_query($url, $xmlMSG) {
	// Start a new curl session and get a curl handle
	$ch = curl_init();

	// Set all the options for the post
	$options = array(
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => True,
		CURLOPT_USERAGENT => 'PHP/Curl',
		# Uncomment the following two lines if you are sending to an SSL
		# connection and have trouble.
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $xmlMSG, 
		CURLOPT_HTTPHEADER => array('Content-type: application/x-qbmsxml')
		);
	curl_setopt_array($ch, $options);
	
	// Execute the post
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

// Retrieves the lead data and returns it as
// an array. Each element's name in the array
// corresponds to the Intuit Element
function get_lead_data($args, $environment) {

	// @todo: get encrypted connection ticket
	// $sql = "SELECT aes_decrypt(intuit_connection_ticket, '" . CRYPT_KEY . "') AS ICT FROM site_data";
	// db_connect();
	// $qry = mysql_query($sql);
	// if ($val = mysql_fetch_assoc($qry))
	// 	$conn_ticket_val = $val['ICT'];
	// else
	// 	$conn_ticket_val = "NOT FOUND";

	define('INTUIT_CONNECTION_TICKET', "** YOUR CONNECTION TICKET **");

	//process lead data
	$transaction = array (
		"ClientDateTime"		=>	substr(date('c', time()), 0 , 19),
		"ApplicationLogin"		=>	($environment == 'live') ? "** application login **" : "** test application login **",
		"TransRequestID"		=>	($environment == 'live') ? session_id() : '457353214088',
		"ConnectionTicket"		=>	($environment == 'live') ? INTUIT_CONNECTION_TICKET : "** TEST CONNECTION TICKET **",
		"CreditCardNumber"		=>	($environment == 'live') ? $args['cc_num'] : "4111111111111111",
		"ExpirationMonth"		=>	($environment == 'live') ? str_pad($args['cc_exp_month'], 2, '0', STR_PAD_LEFT) : "12",
		"ExpirationYear"		=>	($environment == 'live') ? $args['cc_exp_year'] : "2013",
		"Amount"				=>	($environment == 'live') ? number_format($args['amount'], 2) : "10.00",
		"NameOnCard"			=>	($environment == 'live') ? $args['first_name']." ".$args['last_name'] : "Jane Doe",
		"CreditCardAddress"		=>	($environment == 'live') ? $args['address'] : "123 Main St.",
		"CreditCardPostalCode"	=>	($environment == 'live') ? $args['zip'] : "73717",
		"CardSecurityCode"		=>	($environment == 'live') ? $args['cvv'] : "123"
	);
	// encode the data so that special characters will not cause problems
	foreach ($lead as $name => $value) {
		$lead[$name] = urlencode($value);
	}
	
	return $transaction;
	
}

function parse_response($xmlstr) {
	// remove the string element that surrounds the response
	$xmlstr = str_replace('<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>', '', $xmlstr);
	$xmlstr = str_replace('<!DOCTYPE QBMSXML PUBLIC "-//INTUIT//DTD QBMSXML QBMS 4.1//EN" "http://merchantaccount.ptc.quickbooks.com/dtds/qbmsxml41.dtd">', '', $xmlstr);
	$xmlstr = trim($xmlstr);
	
	// unescape the response
	$xmlstr = str_replace('&lt;', '<', $xmlstr);
	$xmlstr = str_replace('&gt;', '>', $xmlstr);
	$xmlstr = preg_replace("/>"."[[:space:]]+"."</","><",$xmlstr);

	//create the xml elements
	$xml = new SimpleXMLElement($xmlstr, LIBXML_NOWARNING);
	//die(print_r($xml));
	//parse the xml response
	$response_arr = array('statusCode' => 65000, 'statusMessage' => $xmlstr, 'statusSeverity' => 'PARSE RESPONSE ERROR');
	foreach($xml->children() as $node)
		if ($node->getName() == 'QBMSXMLMsgsRs')
			foreach($node->children() as $child_node)
				if ($child_node->getName() == 'CustomerCreditCardChargeRs')
					foreach ($child_node->attributes() as $attr)
						$response_arr[$attr->getName()] = (string)$attr[0];

	//return the response array
	return $response_arr;						
}

function submit_transaction($args, $environment)
{
	//debug
	//$environment = "sandbox";

	// Post URL
	$url = ($environment == 'live') ? 'https://merchantaccount.quickbooks.com/j/AppGateway' : 'https://merchantaccount.ptc.quickbooks.com/j/AppGateway';

	// create the message we will send
	$tempmessage = get_lead_data($args, $environment);
	$message = create_transaction($tempmessage);

	// post the query and get the response
	$strResponse = post_query($url, $message);
	$response = parse_response($strResponse);

	//debug
	//die(print_r($response));
	//$response['statusCode'] = 1021;
	
	//return response
	switch ($response['statusCode'])
	{
		case '0':
			return TRANS_APPROVED;
			break;
		default:
			$_SESSION['err'] = "[" . $response['statusCode'] . "] " . $response['statusSeverity'] . "  - " . $response['statusMessage'];
			return TRANS_DECLINED;
			break;
	}
}

?>