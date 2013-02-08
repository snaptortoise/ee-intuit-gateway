<?php
// When passed in an associative array containing the lead contents it
// will return the SellGX XML message.
function create_transaction($transaction)
{
	$request_template = <<<INTUIT_XML
<?xml version="1.0"?>
<?qbmsxml version="4.1"?>
<QBMSXML>
	<SignonMsgsRq>
		<SignonDesktopRq>
			<ClientDateTime>{$transaction['ClientDateTime']}</ClientDateTime>
			<ApplicationLogin>{$transaction['ApplicationLogin']}</ApplicationLogin>
			<ConnectionTicket>{$transaction['ConnectionTicket']}</ConnectionTicket>
		</SignonDesktopRq>
	</SignonMsgsRq>
	<QBMSXMLMsgsRq>
		<CustomerCreditCardChargeRq>
			<TransRequestID>{$transaction['TransRequestID']}</TransRequestID>
			<CreditCardNumber>{$transaction['CreditCardNumber']}</CreditCardNumber>
			<ExpirationMonth>{$transaction['ExpirationMonth']}</ExpirationMonth>
			<ExpirationYear>{$transaction['ExpirationYear']}</ExpirationYear>
			<IsCardPresent>false</IsCardPresent>
			<Amount>{$transaction['Amount']}</Amount>
			<NameOnCard>{$transaction['NameOnCard']}</NameOnCard>
			<CreditCardAddress>{$transaction['CreditCardAddress']}</CreditCardAddress>
			<CreditCardPostalCode>{$transaction['CreditCardPostalCode']}</CreditCardPostalCode>
			<CardSecurityCode>{$transaction['CardSecurityCode']}</CardSecurityCode>
		</CustomerCreditCardChargeRq>
	</QBMSXMLMsgsRq>
</QBMSXML>
INTUIT_XML;
	
	return $request_template;
}

//<CardSecurityCode>{$transaction['CardSecurityCode']}</CardSecurityCode>

/*  EXAMPLE REQUEST

<?xml version="1.0"?>
<?qbmsxml version="4.1"?>
<QBMSXML>
	<SignonMsgsRq>
		<SignonDesktopRq>
			<ClientDateTime>2010-08-14T10:39:05</ClientDateTime>
			<ApplicationLogin>** APPLICATION LOGIN **</ApplicationLogin>
			<ConnectionTicket>** CONNECTION TICKET **</ConnectionTicket>
		</SignonDesktopRq>
	</SignonMsgsRq>
	<QBMSXMLMsgsRq>
		<CustomerCreditCardChargeRq>
			<TransRequestID>457353214088</TransRequestID>
			<CreditCardNumber>4111111111111111</CreditCardNumber>
			<ExpirationMonth>12</ExpirationMonth>
			<ExpirationYear>2011</ExpirationYear>
			<IsCardPresent>false</IsCardPresent>
			<Amount>10.00</Amount>
			<NameOnCard>Jane Doe</NameOnCard>
			<CreditCardAddress>123 Main St.</CreditCardAddress>
			<CreditCardPostalCode>73717</CreditCardPostalCode>
			<CardSecurityCode>456</CardSecurityCode>
		</CustomerCreditCardChargeRq>
	</QBMSXMLMsgsRq>
</QBMSXML>
*/

?>