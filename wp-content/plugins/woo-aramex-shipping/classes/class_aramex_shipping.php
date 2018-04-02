<?php
/*
Class : Aramex Shipping
Author: Mohneesh Bhargava
Author URI: http://computerware.in
*/
include_once('class_aramex_shipping_services.php');
class WC_Aramex_Shipping extends Aramex_Shipping_Method 
{
	/**
	 * Constructor for your shipping class
	 */
	public function __construct() {
		global $woocommerce;
			
		$this->id                 = 'armex_shipping_method'; // Id for your shipping method. Should be uunique.
		$this->method_title       = __( 'Armex Shipping Method' );  // Title shown in admin
		$this->method_description = __( 'Description of your shipping method' ); // Description shown in admin
		$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
		$this->title              = "Aramex Shipping Method"; // This can be added as an setting but for this example its forced.
		$this->init();
	}
	
	/**
	 * Init your settings
	 */
	function init() {
		// Load the settings API
		$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
		$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
		$this->testmode		= $this->settings['testmode'];
		$this->AccountNumber= $this->settings['AccountNumber'];
		$this->AccountPin	= $this->settings['AccountPin'];
		$this->AccountEntity= $this->settings['AccountEntity'];
		$this->UserName     = $this->settings['UserName'];
		$this->Password		= $this->settings['Password'];
		$this->AccountCountryCode = $this->settings['AccountCountryCode'];
		$this->fee			= $this->settings['fee'];
		$this->company		= $this->settings['company'];
		$this->address_1	= $this->settings['address_1'];
		$this->address_2    = $this->settings['address_2'];
		$this->city			= $this->settings['city'];
		$this->state 		= $this->settings['state'];
		$this->pincode		= $this->settings['pincode'];
		$this->phone		= $this->settings['phone'];
		$this->email		= $this->settings['email'];
		// Save settings in admin if you have any defined	
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action('woocommerce_checkout_order_processed', array(&$this, 'purchase_order' ));	
		//$this->getWSDLService('shipments-tracking');
		//$this->getOrderItems();
		//$this->getCustomerAddress();
		//$this->createShipment();
		
	}
	
	function init_form_fields(){
		global $woocommerce;
		$this->form_fields = array(
		'enabled' => array(
			'title'		=> __('Enable/Disable', 'aramex'),
			'type'		=> 'checkbox',
			'label'		=> __('Enable Aramex', 'aramex'),
			'default'	=> 'no'
		),
		'testmode' => array(
			'title'		=> __('Test Mode', 'aramex'),
			'type'		=> 'checkbox',
			'label'		=> __('Enable test mode', 'aramex'),
			'default'	=> 'no'
		),
		'title' => array(
			'title'			=> __('Method title', 'aramex'),
			'type'			=> 'text',
			'description'	=> __('Enter the title of the shipping method.', 'aramex'),
			'default'		=> __('Aramex', 'aramex')
		),
		'api'           => array(
			'title'           => __( 'API Settings', 'aramex' ),
			'type'            => 'title',
			'description'     => __( 'Your API access details', 'aramex' )
		),
		'AccountCountryCode' => array(
			'title'			=> __('Account Country Code', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex Account Country Code', 'aramex'),
			'default'		=> ''
		),
		'AccountNumber' => array(
			'title'			=> __('Account Number', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex Account Number', 'aramex'),
			'default'		=> ''
		),
		'AccountEntity' => array(
			'title'			=> __('Account Entity', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex Account Entity', 'aramex'),
			'default'		=> ''
		),
		'AccountPin' => array(
			'title'			=> __('Account Pin', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex Account Pin', 'aramex'),
			'default'		=> ''
		),
		'UserName' => array(
			'title'			=> __('Username', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex username', 'aramex'),
			'default'		=> ''
		),
		'Password' => array(
			'title'			=> __('Password', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Your Aramex password', 'aramex'),
			'default'		=> ''
		),
		'fee' => array(
			'title'			=> __('Handling Fee', 'aramex'),
			'type'			=> 'text',
			'description'	=> __('Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank for no fee.', 'aramex'),
			'default'		=> '0'
		),
		'store'           => array(
			'title'           => __( 'Store Settings', 'aramex' ),
			'type'            => 'title',
			'description'     => __( 'Configure your company details.', 'aramex' )
		),
		'company' => array(
			'title'			=> __('Company', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter company name.', 'aramex'),
			'default'		=> ''
		),
		'address_1' => array(
			'title'			=> __('Address1', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter address line 1.', 'aramex'),
			'default'		=> ''
		),
		'address_2' => array(
			'title'			=> __('Address2', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter address line 2.', 'aramex'),
			'default'		=> ''
		),
		'city' => array(
			'title'			=> __('City', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter company city.', 'aramex'),
			'default'		=> ''
		),
		'pincode' => array(
			'title'			=> __('Pincode', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter company pincode.', 'aramex'),
			'default'		=> ''
		),
		'state' => array(
			'title'			=> __('State', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter company state.', 'aramex'),
			'default'		=> ''
		),
		'phone' => array(
			'title'			=> __('Phone', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter contact number', 'aramex'),
			'default'		=> ''
		),
		'email' => array(
			'title'			=> __('Email', 'aramex'),
			'type'			=> 'text',
			'css'			=> 'width: 250px;',
			'description'	=> __('Enter sales/support email.', 'aramex'),
			'default'		=> ''
		)
	);
	}
	
	function requestShipment(){
		$auth_call = $this->createShipment();
		
		if($auth_call->HasErrors){
			if(empty($auth_call->Shipments)){
				if(count($auth_call->Notifications->Notification) > 1){
					foreach($auth_call->Notifications->Notification as $notify_error){
						$message = sprintf( __( 'Aramex error, some fields are missing: %s' , 'aramex' ), implode( ", ", $missings ) );
						echo '<div class="error fade"><p>' . $notify_error->Code .' - '. $notify_error->Message . '</p></div>' . "\n";
					}
				} else {
					echo '<div class="error fade"><p>' . $auth_call->Notifications->Notification->Code . ' - '. $auth_call->Notifications->Notification->Message . '</p></div>' . "";
				}
			} else {
				if(count($auth_call->Shipments->ProcessedShipment->Notifications->Notification) > 1){
					$notification_string = '';
					foreach($auth_call->Shipments->ProcessedShipment->Notifications->Notification as $notification_error){
						$notification_string .= $notification_error->Code .' - '. $notification_error->Message . ' <br />';
					}
					echo '<div class="error fade"><p>' . $notification_string . '</p></div>' . "";
				} 
				else 
				{
					echo '<div class="error fade"><p>' . $auth_call->Shipments->ProcessedShipment->Notifications->Notification->Code .' - '. $auth_call->Shipments->ProcessedShipment->Notifications->Notification->Message . '</p></div>' . "";
				}
			}
		}
		else{
			$awbno = $auth_call->Shipments->ProcessedShipment->ID;
			$orderid = $auth_call->Shipments->ProcessedShipment->Reference1;
			$filepath = $auth_call->Shipments->ProcessedShipment->ShipmentLabel->LabelURL;
			/*try{
				$auth_printLabel = $this->printLabel($orderid,$awbno);
				echo $label_url = $auth_printLabel->ShipmentLabel->LabelURL;
				exit;
			}
			catch (SoapFault $fault) {					
				echo 'Error : ' . $fault->faultstring;
			}*/
			$name="{$orderid}-shipment-label.pdf";	
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="'.$name.'"');
			readfile($filepath);
			exit;					
		}
		//echo '<pre>';
		//print_r($auth_call);
		//die();
	} // end 
	
	function printLabel($orderID,$awbno){
		$clientInfo = $this->getClientAuth();
		
		$params = array(		
			'ClientInfo'  			=> $clientInfo,
			'Transaction' 			=> array(
										'Reference1'			=> $orderID,
										'Reference2'			=> '', 
										'Reference3'			=> '', 
										'Reference4'			=> '', 
										'Reference5'			=> '',									
									),
			'LabelInfo'				=> array(
										'ReportID' 				=> $report_id,
										'ReportType'			=> 'URL',
			),
		);
		$params['ShipmentNumber'] = $awbno;
		echo $path 	= $this->getWSDLService();
		$soapClient = new SoapClient($path);
		$auth_call = $soapClient->PrintLabel($params);
		echo '<pre>';print_r($auth_call);exit;
		return $auth_call;
	}
	
	
}