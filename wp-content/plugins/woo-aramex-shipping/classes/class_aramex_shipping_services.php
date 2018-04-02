<?php
/*
Class : Aramex Shipping
Author: Mohneesh Bhargava
Author URI: http://computerware.in
*/
class Aramex_Shipping_Method extends WC_Shipping_Method 
{
	/**
	 * Constructor for your shipping class
	 */
	public function __construct() {
		global $woocommerce;
	}
	
	function getWSDLService($service = ''){
		$testEnable = $this->settings['testmode'];
		// check test mode enabled
		if($testEnable=='yes'){
			$plg_path = ARAMEX_URL.'/wsdl/testmode';
		}else{
			$plg_path = ARAMEX_URL.'/wsdl';
		}
		
		if($service!=''){
			$path = $plg_path.'/'.$service.'.wsdl';
		}
		else{
			$path = $plg_path.'/shipping-services.wsdl';
		}
		return $path;
	}
	
	function getClientAuth()
    {
		$account 		= $this->settings['AccountNumber'];
		$username 		= $this->settings['UserName'];
		$password 		= $this->settings['Password'];
		$pin 			= $this->settings['AccountPin'];
		$entity 		= $this->settings['AccountEntity'];
		$country_code 	= $this->settings['AccountCountryCode'];
		return array(
			'AccountCountryCode'	=> $country_code,
			'AccountEntity'		 	=> $entity,
			'AccountNumber'		 	=> $account,
			'AccountPin'		 	=> $pin,
			'UserName'			 	=> $username,
			'Password'			 	=> $password,
			'Version'			 	=> 'v1.0'
		);
    }
	
	function getOrderItems(){
		global $woocommerce, $post;
		
		$order = new WC_Order( $post->ID );
		$items = $order->get_items();			
		return $items;
	}
	
	function getCustomerAddress(){
		global $woocommerce, $post;
		$order = new WC_Order( $post->ID );
		$customer_address = array(
				'first_name'    => $order->shipping_first_name,
                'last_name'     => $order->shipping_last_name,
                'company'       => $order->shipping_company,
                'address_1'     => $order->shipping_address_1,
                'address_2'     => $order->shipping_address_2,
                'city'          => $order->shipping_city,
                'state'         => $order->shipping_state,
                'postcode'      => $order->shipping_postcode,
                'country'       => $order->shipping_country,
				'email'     	=> $order->billing_email,
				'phone'      	=> $order->billing_phone
			);
		//echo '<pre>';
		//print_r($customer_address);
		//exit;	
		return $customer_address;
	}
	
	function getAramexItems(){
		global $woocommerce, $post;
		
		$order = new WC_Order( $post->ID );
		$items = $order->get_items();
			foreach($items as $item){
				//print_r($item);
				//get weight
				$_product = $order->get_product_from_item($item);
				$weight = $_product->get_weight();
				if($weight != 0){$weight = $weight*$item['qty'];}
				else{$weight = 0.5*$item['qty'];}				
					// collect items for aramex
					$aramex_items[]	= array(
						'PackageType'	=> 'Box',
						'Quantity'		=> $item['qty'],
						'Weight'		=> array(
							'Value'	=> $weight,
							'Unit'	=> 'Kg'
						),
						'Comments'		=> $item['name'], //'',
						'Reference'		=> ''
					);
			}	
		return $aramex_items;
	}
	
	function createShipment(){
		global $post, $woocommerce, $the_order;
		$the_order = new WC_Order( $post->ID );
		$totalWeight 	= 0;
		$totalItems 	= 0;
		$order_id 		= $post->ID;		
		$account 		= $this->settings['AccountNumber'];
		$username 		= $this->settings['UserName'];
		$password 		= $this->settings['Password'];
		$pin 			= $this->settings['AccountPin'];
		$entity 		= $this->settings['AccountEntity'];
		$country_code 	= $this->settings['AccountCountryCode'];
		$company 		= $this->settings['company'];
		$address1 		= $this->settings['address_1'];
		$address2 		= $this->settings['address_2'];
		$city 			= $this->settings['city'];
		$pincode 		= $this->settings['pincode'];
		$state 			= $this->settings['state'];
		$phone 			= $this->settings['phone'];
		$email 			= $this->settings['email'];
		
		$aramex_items = $this->getAramexItems();
		
		$items 	= $this->getOrderItems();
		$totalItems = count($items);
		
		foreach($items as $item){
			//print_r($the_order);
			$_product = $the_order->get_product_from_item($item);
			$totalWeight += $_product->get_weight();
		}
		
		$params = array();
		//shipper parameters
		$params['Shipper'] = array(
			'Reference1' 	=> '', //'ref11111',
			'Reference2' 	=> '',
			'AccountNumber' => $account, //'43871',
			
			//Party Address
			'PartyAddress'		=> array(
						'Line1'					=> $address1, //'13 Mecca St',
						'Line2'					=> $address2,
						'Line3'					=> '',
						'City'					=> '', //'Dubai',
						'StateOrProvinceCode'	=> '', //'',
						'PostCode'				=> $pincode,
						'CountryCode'			=> $country_code, //'AE'
			),

			//Contact Info
			'Contact' 			=> array(
						'Department'			=> '',
						'PersonName'			=> $company, //'Suheir',
						'Title'					=> '',
						'CompanyName'			=> $company, //'Aramex',
						'PhoneNumber1'			=> $phone, //'55555555',
						'PhoneNumber1Ext'		=> '',
						'PhoneNumber2'			=> '',
						'PhoneNumber2Ext'		=> '',
						'FaxNumber'				=> '',
						'CellPhone'				=> $phone,
						'EmailAddress'			=> $email, //'',
						'Type'					=> ''
			),
		);
		
		$consigneeAdd = $this->getCustomerAddress();	
		//consinee parameters
		$params['Consignee'] = array(
			'Reference1' 		=> '', //'',
			'Reference2'		=> '',
			'AccountNumber'		=> '',

			//Party Address
			'PartyAddress'		=> array(
						'Line1'					=> $consigneeAdd['address_1'], //'15 ABC St',
						'Line2'					=> $consigneeAdd['address_2'],
						'Line3'					=> '',
						'City'					=> $consigneeAdd['city'], //'Amman',
						'StateOrProvinceCode'	=> $consigneeAdd['state'],
						'PostCode'				=> $consigneeAdd['postcode'],
						'CountryCode'			=> $consigneeAdd['country'], //'JO'
			),

			//Contact Info
			'Contact' 			=> array(
						'Department'			=> '',
						'PersonName'			=> $consigneeAdd['first_name'].' '.$consigneeAdd['last_name'], //'Mazen',
						'Title'					=> '',
						'CompanyName'			=> $consigneeAdd['first_name'].' '.$consigneeAdd['last_name'], //'Aramex',
						'PhoneNumber1'			=> $consigneeAdd['phone'], //'6666666',
						'PhoneNumber1Ext'		=> '',
						'PhoneNumber2'			=> '',
						'PhoneNumber2Ext'		=> '',
						'FaxNumber'				=> '',
						'CellPhone'				=> $consigneeAdd['phone'],
						'EmailAddress'			=> $consigneeAdd['email'], //'mazen@aramex.com',
						'Type'					=> ''
			)
		);
		//new
		// Other Main Shipment Parameters
		$params['Reference1'] 				= $order_id; //'Shpt0001';
		$params['Reference2'] 				= '';
		$params['Reference3'] 				= '';
		$params['ForeignHAWB'] 				= '';

		$params['TransportType'] 			= 0;
		$params['ShippingDateTime'] 		= time(); //date('m/d/Y g:i:sA');
		$params['DueDate'] 					= time() + (7 * 24 * 60 * 60); //date('m/d/Y g:i:sA');
		$params['PickupLocation'] 			= 'Reception';
		$params['PickupGUID'] 				= '';				
		$params['Comments'] 				= '';
		$params['AccountingInstrcutions'] 	= '';
		$params['OperationsInstructions'] 	= '';
		$params['Details'] = array(
						'Dimensions' => array(
								'Length'	=> '',
								'Width'		=> '',
								'Height'	=> '',
								'Unit'		=> ''
						),
						
						'ActualWeight'			=> array(
							'Value'		=> $totalWeight,
							'Unit'		=> 'kg'
						),

						'ProductGroup'			=> 'EXP', //'EXP',
						'ProductType'			=> 'PDX', //,'PDX'
						'PaymentType'			=> 'P',
						'PaymentOptions'		=> '', //$post['aramex_shipment_info_payment_option']
						'Services'				=> '',
						'NumberOfPieces'		=> $totalItems,
						'DescriptionOfGoods'	=> 'Docs',
						'GoodsOriginCountry'	=> $country_code, //'JO',
						'Items'					=> $aramex_items,
		);
		
		
		if(count($aramex_atachments)){
		  $params['Attachments'] = $aramex_atachments;
		} 

		$params['Details']['CashOnDeliveryAmount'] = array(
				'Value' 		=> 0, 
				'CurrencyCode' 	=>  'INR'
		);

		$params['Details']['CustomsValueAmount'] = array(
				'Value' 		=> 0, 
				'CurrencyCode' 	=>  'INR'
		);
		
		$params['Details']['CollectAmount']	= array(
			'Value'					=> 0,
			'CurrencyCode'			=> 'INR'
		);
		
		$params['Details']['CashAdditionalAmount']	= array(
			'Value'					=> 0,
			'CurrencyCode'			=> 'INR'							
		);
		
		$params['Details']['CashAdditionalAmountDescription'] = '';
		
		
		$major_par['Shipments'][] = $params;
		$clientInfo = $this->getClientAuth();	
		$major_par['ClientInfo'] = $clientInfo;
		
		//$report_id = (int);
		//if(!$report_id){
			$report_id =9729;
		//}

		$major_par['LabelInfo'] = array(
			'ReportID'		=> $report_id, //'9201',
			'ReportType'		=> 'URL'
		);
		//echo '<pre>';
		//print_r($major_par);exit;
		$path 	= $this->getWSDLService();
		$soapClient = new SoapClient($path);
		try {
				return $auth_call = $soapClient->CreateShipments($major_par);
			} catch (SoapFault $fault) {
				die('Error : ' . $fault->faultstring);
			}	
	}
	
	function createPickup(){
		$params = array();
		//shipper parameters
		$params = array(
			'ClientInfo'  	=> $clientInfo,
									
			'Transaction' 	=> array(
									'Reference1'			=> $post['reference'] 
									),
									
			'Pickup'		=>array(
									'PickupContact'			=>array(
										'PersonName'		=>html_entity_decode($post['contact']),
										'CompanyName'		=>html_entity_decode($post['company']),
										'PhoneNumber1'		=>html_entity_decode($post['phone']),
										'PhoneNumber1Ext'	=>html_entity_decode($post['ext']),
										'CellPhone'			=>html_entity_decode($post['mobile']),
										'EmailAddress'		=>html_entity_decode($post['email'])
									),
									'PickupAddress'			=>array(
										'Line1'				=>html_entity_decode($post['address']),
										'City'				=>html_entity_decode($post['city']),
										'StateOrProvinceCode'=>html_entity_decode($post['state']),
										'PostCode'			=>html_entity_decode($post['zip']),
										'CountryCode'		=>$post['country']
									),
									
									'PickupLocation'		=>html_entity_decode($post['location']),
									'PickupDate'			=>$readyTime,
									'ReadyTime'				=>$readyTime,
									'LastPickupTime'		=>$closingTime,
									'ClosingTime'			=>$closingTime,
									'Comments'				=>html_entity_decode($post['comments']),
									'Reference1'			=>html_entity_decode($post['reference']),
									'Reference2'			=>'',
									'Vehicle'				=>$post['vehicle'],
									'Shipments'				=>array(
										'Shipment'					=>array()
									),
									'PickupItems'			=>array(
										'PickupItemDetail'=>array(
											'ProductGroup'	=>$post['product_group'],
											'ProductType'	=>$post['product_type'],
											'Payment'		=>$post['payment_type'],										
											'NumberOfShipments'=>$post['no_shipments'],
											'NumberOfPieces'=>$post['no_pieces'],										
											'ShipmentWeight'=>array('Value'=>$post['text_weight'],'Unit'=>$post['weight_unit']),
											
										),
									),
									'Status'				=>$post['status']
								)
		);
		
	}
	
	
	/**
	 * calculate_shipping function.
	 * @access public
	 * @param mixed $package
	 * @return void
	 */
	public function calculate_shipping( $package ) {
		//print_r($package);exit;
		$rate = array(
			'id' => $this->id,
			'label' => $this->title,
			'cost' => $this->fee,
			'calc_tax' => 'per_item'
		);

		// Register the rate
		$this->add_rate( $rate );
	}
	
	public function purchase_order($order_id)
	{ 
		try
		{
		   global $woocommerce;

		  $chosen_shipping_methods = $woocommerce->session->get( 'chosen_shipping_methods' );
		  error_log(var_export($chosen_shipping_methods,1));
		  $order        = &new WC_Order($order_id);
		  $shipping     = $order->get_shipping_address();
		
		  $method = $order->get_shipping_methods();
		  $method = array_values($method);
		  $shipping_method = $method[0]['method_id'];
		  $ship_arr = explode('|',$shipping_method);
		  //print_r($ship_arr);exit;
		  
		  $path 	= plugin_dir_path( __FILE__ ).'shipping-services-api-wsdl.wsdl';
		  $soapClient = new SoapClient($path);
		  
		  $params = array(
			'Shipments' => array(
				'Shipment' => array(
					'Shipper' => array(
								'Reference1' 	=> 'Ref 111111',
								'Reference2' 	=> 'Ref 222222',
								'AccountNumber' => '36670436',
								'PartyAddress'	=> array(
									'Line1'					=> 'Mecca St',
									'Line2' 				=> '',
									'Line3' 				=> '',
									'City'					=> '',
									'StateOrProvinceCode'	=> '',
									'PostCode'				=> '400093',
									'CountryCode'			=> 'IN'
								),
								'Contact'		=> array(
									'Department'			=> '',
									'PersonName'			=> 'Michael',
									'Title'					=> '',
									'CompanyName'			=> 'Kanabis',
									'PhoneNumber1'			=> '5555555',
									'PhoneNumber1Ext'		=> '125',
									'PhoneNumber2'			=> '',
									'PhoneNumber2Ext'		=> '',
									'FaxNumber'				=> '',
									'CellPhone'				=> '07777777',
									'EmailAddress'			=> 'michael@aramex.com',
									'Type'					=> ''
								),
					),
												
					'Consignee'	=> array(
								'Reference1'	=> 'Ref 333333',
								'Reference2'	=> 'Ref 444444',
								'AccountNumber' => '',
								'PartyAddress'	=> array(
									'Line1'					=> '15 ABC St',
									'Line2'					=> '',
									'Line3'					=> '',
									'City'					=> '',
									'StateOrProvinceCode'	=> '',
									'PostCode'				=> '110016',
									'CountryCode'			=> 'IN'
								),
								
								'Contact'		=> array(
									'Department'			=> '',
									'PersonName'			=> 'Mazen',
									'Title'					=> '',
									'CompanyName'			=> 'Aramex',
									'PhoneNumber1'			=> '6666666',
									'PhoneNumber1Ext'		=> '155',
									'PhoneNumber2'			=> '',
									'PhoneNumber2Ext'		=> '',
									'FaxNumber'				=> '',
									'CellPhone'				=> '9652356253',
									'EmailAddress'			=> 'mazen@aramex.com',
									'Type'					=> ''
								),
					),
						
					'ThirdParty' => array(
									'Reference1' 	=> '',
									'Reference2' 	=> '',
									'AccountNumber' => '',
									'PartyAddress'	=> array(
										'Line1'					=> '',
										'Line2'					=> '',
										'Line3'					=> '',
										'City'					=> '',
										'StateOrProvinceCode'	=> '',
										'PostCode'				=> '',
										'CountryCode'			=> ''
									),
									'Contact'		=> array(
										'Department'			=> '',
										'PersonName'			=> '',
										'Title'					=> '',
										'CompanyName'			=> '',
										'PhoneNumber1'			=> '',
										'PhoneNumber1Ext'		=> '',
										'PhoneNumber2'			=> '',
										'PhoneNumber2Ext'		=> '',
										'FaxNumber'				=> '',
										'CellPhone'				=> '',
										'EmailAddress'			=> '',
										'Type'					=> ''							
									),
					),
						
						'Reference1' 				=> 'Shpt 0001',
						'Reference2' 				=> '',
						'Reference3' 				=> '',
						'ForeignHAWB'				=> '',
						'TransportType'				=> 0,
						'ShippingDateTime' 			=> time(),
						'DueDate'					=> time(),
						'PickupLocation'			=> 'Reception',
						'PickupGUID'				=> '',
						'Comments'					=> 'Shpt 0001',
						'AccountingInstrcutions' 	=> '',
						'OperationsInstructions'	=> '',
						
						'Details' => array(
										'Dimensions' => array(
											'Length'				=> 10,
											'Width'					=> 10,
											'Height'				=> 10,
											'Unit'					=> 'cm',
											
										),
										
										'ActualWeight' => array(
											'Value'					=> 0.5,
											'Unit'					=> 'Kg'
										),
										
										'ProductGroup' 			=> 'EXP',
										'ProductType'			=> 'PDX',
										'PaymentType'			=> 'P',
										'PaymentOptions' 		=> '',
										'Services'				=> '',
										'NumberOfPieces'		=> 1,
										'DescriptionOfGoods' 	=> 'Docs',
										'GoodsOriginCountry' 	=> 'IN',
										
										'CashOnDeliveryAmount' 	=> array(
											'Value'					=> 0,
											'CurrencyCode'			=> 'INR'
										),
										
										/*'InsuranceAmount'		=> array(
											'Value'					=> 0,
											'CurrencyCode'			=> 'INR'
										),*/
										
										'CollectAmount'			=> array(
											'Value'					=> 0,
											'CurrencyCode'			=> 'INR'
										),
										
										'CashAdditionalAmount'	=> array(
											'Value'					=> 0,
											'CurrencyCode'			=> 'INR'							
										),
										
										'CashAdditionalAmountDescription' => '',
										
										'CustomsValueAmount' => array(
											'Value'					=> 0,
											'CurrencyCode'			=> 'INR'								
										),
							
										'Items' 				=> array(
											
										)
							),
					),
			),
			
				'ClientInfo'  			=> array(
											'AccountCountryCode'	=> 'IN',
											'AccountEntity'		 	=> 'BOM',
											'AccountNumber'		 	=> '36670436',
											'AccountPin'		 	=> '443543',
											'UserName'			 	=> 'testingapi@aramex.com',
											'Password'			 	=> 'R123456789$r',
											'Version'			 	=> 'v1.0
'
										),

				'Transaction' 			=> array(
											'Reference1'			=> '001',
											'Reference2'			=> '', 
											'Reference3'			=> '', 
											'Reference4'			=> '', 
											'Reference5'			=> '',									
										),
				'LabelInfo'				=> array(
											'ReportID' 				=> 9201,
											'ReportType'			=> 'URL',
				),
		);
		
		$params['Shipments']['Shipment']['Details']['Items'][] = array(
			'PackageType' 	=> 'Box',
			'Quantity'		=> 1,
			'Weight'		=> array(
					'Value'		=> 0.5,
					'Unit'		=> 'Kg',		
			),
			'Comments'		=> 'Docs',
			'Reference'		=> ''
		);
		
			//print_r($params['Shipments']['Shipment']['Details']['Items']);exit;
			try {
				$auth_call = $soapClient->CreateShipments($params);
				//echo '<pre>';
				//echo "sfasf";
				//print_r($auth_call->Shipments);
				//die();
			} catch (SoapFault $fault) {
				die('Error : ' . $fault->faultstring);
			}

		}
		catch(Exception $e)
		{
		  //mail('seanvoss@gmail.com', 'Error from WordPress', var_export($e,1));
		}
	}
	
}