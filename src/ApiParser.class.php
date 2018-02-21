<?php

class ApiParser
{

	const VIP_MAX_RATING = 12;
	// VIP subcriber
	const VIP_MIN_RATING = 7;

	const GOOD_MAX_RATING = 6;
	// Good
	const GOOD_MIN_RATING = 4;

	const AVERAGE_MAX_RATING = 3;
	// Average
	const AVERAGE_MIN_RATING = 1;

	const POOR_MAX_RATING = 0;

	const POOR_MIN_RATING = -4;
	// very poor subscriber
	
	const RATING_TYPE_VIP = "VIP";

	const RATING_TYPE_GOOD = "Good";

	const RATING_TYPE_AVERAGE = "Average";

	const RATING_TYPE_POOR = "Poor";

	/*
	 * How events affect subscriber rating
	 */
	const EVENT_OPEN = 2;
	// done
	const EVENT_CLICK = 4;
	// done
	const EVENT_MODIFY_FORM = 6;
	// done
	const EVENT_SOFTBOUNCE = -5;
	// done
	const EVENT_HARDBOUNCE = -10;
	// done
	const EVENT_FBL_SPAM_COMPLAINER = -15;

	const EVENT_NOT_OPENED_CAMPAIGN = -1;

	const EVENT_SUBSCRIBER_IMPORTED = 1;
	// done
	const EVENT_SINGLE_OPT_IN = 5;
	// done
	const EVENT_DOUBLE_OPT_IN = 10;
	// done
	const EVENT_FACEBOOK_SIGN_UP = 10;
	// done
	
	const SOURCE_WEB_FORM = 1;
	// web form signup
	const SOURCE_IMPORT_SUBSCRIBER = 2;
	// import subscribers
	const SOURCE_MANUAL_ADD_SUBSCRIBER = 3;
	// import subscribers
	const SOURCE_XML_API = 4;
	// subscribers from xml
	const SOURCE_FACEBOOK_WEB_FORM = 5;
	// facebook web form signup
	const SOURCE_UNKOWN = 6;
	// unknown signup
	const SOURCE_CONNECT_CONTACT_FIELD = 7;
	// connector signup
	const SOURCE_DATASYNCH = 8;
	//imported using our datasynch process
	
	const UNSUB_SOURCE_UNKNOWN = 0;
 // unknown unsubscribe
	const UNSUB_SOURCE_WEB_FORM = 1;
 // web form unsub
	const UNSUB_SOURCE_WEB_FORM_CONFIRM = 2;
 // web form unsub WITH confirmation
	const UNSUB_SOURCE_WEB_FORM_FLOW = 3;
 // web form_flow unsubscribe
	const UNSUB_SOURCE_WEB_FORM_FLOW_CONFIRM = 4;

	const UNSUB_SOURCE_WEB_FORM_MODIFY = 5;
 // unsubscribed with modify form on multiple lists
	const UNSUB_SOURCE_MANUAL_UNSUBSCRIBE = 6;
 // manual unsubscribe subscribers
	const UNSUB_SOURCE_XML_API = 7;
 // unsubsubscribers from xml - API
	const UNSUB_SOURCE_WEB_FORM_SUBSCRIBE = 8;
 // unsubscribed through subscription form on multiple lists with added
	                                           // update option
	                                           
	const UNSUB_SOURCE_SYSTEM_UNSUBSCRIBE = 9;
	//the system unsubscribe without any unsubscribe form assigned to the list
	
	
	const REQUEST_FAILED = 'Unsuccessful request';
	var $settings = array ();

	public function __construct($settings = array())
	{
		$this->settings = $settings;
	}

	private function GetHTTPHeader ()
	{
		switch($this->settings["format"])
		{
			case "xml":
				return array (
						"Accept: application/xml; charset=utf-8"
				);
			break;
			case "serialized":
				return array (
						"Accept: application/vnd.php.serialized; charset=utf-8"
				);
			break;
			case "php":
				return array (
						"Accept: application/vnd.php; charset=utf-8"
				);
			break;
			case "csv":
				return array (
						"Accept: application/csv; charset=utf-8"
				);
			break;
			default:
				return array (
						"Accept: application/json; charset=utf-8"
				);
			break;
		}
	}

	private function DecodeResult ($input = '')
	{
		switch($this->settings["format"])
		{
			case "xml":
				//@todo implement parser
				return $input;
			break;
			case "serialized":
				//@todo implement parser
				return $input;
			break;
			case "php":
				//@todo implement parser
				return $input;
			break;
			case "csv":
				//@todo implement parser
				return $input;
			break;
			default:
				return json_decode($input, TRUE);
			break;
		}
	}

	private function MakeGetRequest ($url = "", $fields = array())
	{
		// open connection
		$ch = curl_init();
		// If two arrays merged, URL is invalid
		$data = $fields; // array_merge($fields, $this->settings);
		if(!empty($data))
		{
			$url .= "?" . http_build_query($data, '', '&');
		}
		// set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($ch, CURLOPT_USERPWD, 'myusername:mypassword');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		// execute post
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
		return $this->DecodeResult($result);
	}

	private function MakePostRequest ($url = "", $fields = array())
	{
		try
		{
			// open connection
			$ch = curl_init();
			// add the setting to the fields
			$data = array_merge($fields, $this->settings);
			
			$encodedData = http_build_query($data, '', '&');
			
			// set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, count($data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
			// curl_setopt($ch, CURLOPT_USERPWD, 'myusername:mypassword');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			
			// execute post
			$result = curl_exec($ch);
			
			// close connection
			curl_close($ch);
			return $this->DecodeResult($result);
		}
		catch(Exception $error)
		{
			return $error->GetMessage();
		}
	}

	private function MakeDeleteRequest ($url = "", $fields = array())
	{
		// open connection
		$ch = curl_init();
		
		// add the setting to the fields
		$data = array_merge($fields, $this->settings);
		
		// set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, count($data));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		// curl_setopt($ch, CURLOPT_USERPWD, 'myusername:mypassword');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		// execute post
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
		return $this->DecodeResult($result);
	}

	public function HasRequirements ()
	{
		if(!is_callable('curl_init'))
		{
			return 'curl is not installed correctly!';
		}
		
		$params = array (
				"test" => "a"
		);
		$url = $this->settings["URL"] . "/Test";
		
		$result = $this->MakePostRequest($url, $params);
		
		if(!array_key_exists('postResponse', $result))
		{
			return 'Post request not work properly';
		}
		
		$result = $this->MakeDeleteRequest($url, $params);
		if(!array_key_exists('deleteResponse', $result))
		{
			return 'Delete request not work properly';
		}
// 		switch($requestType)
// 		{
// 			case "post":
// 				$result = $this->MakePostRequest($url, $params);
// 			break;
// 			case "delete":
// 				$result = $this->MakeDeleteRequest($url, $params);
// 			break;
// 			default:
// 				$result = $this->MakeGetRequest($url, $params);
// 			break;
// 		}
		return 'All requirements work correctly';
	}
	
	// funkcija za test na login
	public function TestUserToken ()
	{
		$result = FALSE;
		$url = $this->settings["URL"] . "/Test/TestUserToken";
		$result = $this->MakePostRequest($url);
		return $result;
	}

	public function LoadLists ($userid = 0)
	{
		$result = FALSE;
		$url = $this->settings["URL"] . "/Test/LoadLists";
		$result = $this->MakePostRequest($url, array (
				"userid" => $userid
		));
		return $result;
	}

	public function GetGroup ($userid = 0)
	{
		$result = FALSE;
		$url = $this->settings["URL"] . "/Test/GetGroup";
		$result = $this->MakePostRequest($url, array (
				"userid" => $userid
		));
		return $result;
	}

	public function IsBannedSubscriber ($emailaddress = '', $mobile = '', $listids = array(), $return_ids = false)
	{
		$url = $this->settings["URL"] . "/Subscribers/IsBannedSubscriber";
		$emailaddress = trim($emailaddress);
		if(($emailaddress || $mobile) && !empty($listids))
		{
// 			$lists = implode('#', $listids);
			$params = array (
					"emailaddress" => $emailaddress,
					"mobile" => $mobile,
					"listids" => $listids, 
					"return_ids" => $return_ids
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function IsSubscriberOnList ($listids = array(), $emailaddress = '', $mobile = '', $mobilePrefix = '', $subscriberid = 0, $activeonly = false, 
			$not_bounced = false, $return_listid = false)
	{
		$url = $this->settings["URL"] . "/Subscribers/IsSubscriberOnList";
		$emailaddress = trim($emailaddress);
		$mobile = trim($mobile);
		if(!empty($listids) && ($emailaddress || $mobile || $subscriberid))
		{
// 			$lists = implode('#', $listids);
			$params = array (
					"listids" => $listids, 
					"emailaddress" => $emailaddress, 
					"mobile" => $mobile,
					"mobilePrefix" => $mobilePrefix,
					"subscriberid" => $subscriberid, 
					"activeonly" => $activeonly, 
					"not_bounced" => $not_bounced, 
					"return_listid" => $return_listid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function IsUnSubscriber ($listid = 0, $emailaddress = '', $mobile = '', $mobilePrefix = '', $subscriberid = 0, $service = '')
	{
		$url = $this->settings['URL'] . '/Subscribers/IsUnSubscriber';
		if(($listid && ($emailaddress || $mobile)) || $subscriberid)
		{
			$params = array (
					"listid" => $listid,
					"emailaddress" => $emailaddress, 
					"mobile" => $mobile,
					"mobilePrefix" => $mobilePrefix,
					"subscriberid" => $subscriberid,
					"service" => $service
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function DeleteSubscriber ($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/DeleteSubscriber';
		if($listid && ($emailaddress || $mobile || $subscriberid))
		{
			$params = array (
					"listid" => $listid,
					"emailaddress" => $emailaddress,
					"mobile" => $mobile,
					"mobilePrefix" => $mobilePrefix,
					"subscriberid" => $subscriberid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function IsBounceSubscriber ($emailaddress = '', $listid = 0, $subscriberid = 0)
	{
		$url = $this->settings['URL'] . '/Subscribers/IsBounceSubscriber';
		if($emailaddress)
		{
			$params = array (
					"emailaddress" => $emailaddress, 
					"listid" => $listid, 
					"subscriberid" => $subscriberid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function BounceSubscriber ($emailaddress = false, $listid = 0, $subscriberid = 0, $bouncetime = 0, 
			$alreadySoftBounce = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/BounceSubscriber';
		if($emailaddress && $listid)
		{
			$params = array (
					"emailaddress" => $emailaddress, 
					"listid" => $listid, 
					"subscriberid" => $subscriberid, 
					"bouncetime" => $bouncetime, 
					"alreadySoftBounce" => $alreadySoftBounce
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function LoadSubscriberList ($subscriberid = 0, $listid = 0, $returnonly = false, $activeonly = false, 
			$include_customfields = true, $include_deleted = false)
	{
		$url = $this->settings["URL"] . "/Subscribers/LoadSubscriberList";
		if($subscriberid && $listid)
		{
			$params = array (
					"subscriberid" => $subscriberid, 
					"listid" => $listid, 
					"returnonly" => $returnonly, 
					"activeonly" => $activeonly, 
					"include_customfields" => $include_customfields, 
					"include_deleted" => $include_deleted
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetSubscriberInfo ($data = array(), $customFieldsOnly = false)
	{
		
		$url = $this->settings['URL'] . '/Subscribers/GetSubscriberInfo';
		if($data)
		{
			$params = array (
					'$data' => $data, 
					'customFieldsOnly' => $customFieldsOnly
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscriberDetails ($email = false, $listid = false)
	{
		
		$url = $this->settings['URL'] . '/Subscribers/GetSubscriberDetails';
		if($email)
		{
			$params = array (
					'email' => $email,
					'listid' => $listid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetUnconfirmedSubscriptionsForEmail ($email = '', $listids = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/GetUnconfirmedSubscriptionsForEmail';
		if($email && $listids)
		{
			$params = array (
					'email' => $email, 
					'listids' => $listids
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function LoadList ($listid = 0, $includeDeleted = false)
	{
		$url = $this->settings["URL"] . "/Lists/LoadList";
		if($listid)
		{
			$params = array (
					"listid" => $listid, 
					"includeDeleted" => $includeDeleted
			);
			return $this->MakePostRequest($url, $params);
		}
		
		return self::REQUEST_FAILED;
	
	}

	public function LoadListName ($listid = 0)
	{
		$url = $this->settings["URL"] . "/Lists/LoadName";
		if($listid)
		{
			$params = array (
					"listid" => $listid
			);
			return $this->MakePostRequest($url, $params);
		}
		
		return self::REQUEST_FAILED;
	}

	public function LoadCustomField ($fieldid = 0, $return_options = false, $makeInstance = false)
	{
		$url = $this->settings["URL"] . "/CustomFields/LoadCustomField";
		if($fieldid)
		{
			$params = array (
					"fieldid" => $fieldid, 
					"return_options" => $return_options,
					"makeInstance" => $makeInstance
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function LoadSubField ($fieldid = 0)
	{
	$url = $this->settings["URL"] . "/CustomFields/LoadCustomField";
	if($fieldid)
	{
	$params = array (
	"fieldid" => $fieldid
	);
	return $this->MakePostRequest($url, $params);
	}
	return self::REQUEST_FAILED;
	}
	
	public function GetAssociations ($fieldid = 0)
	{
		$url = $this->settings["URL"] . "/CustomFields/GetAssociations";
		if($fieldid)
		{
			$params = array (
					"fieldid" => $fieldid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function TransformValue ($value = '')
	{
		$url = $this->settings["URL"] . "/CustomFields/TransformValue";
		if($value)
		{
			$params = array (
					"value" => $value
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetRealValue ($value = '')
	{
		$url = $this->settings["URL"] . "/CustomFields/GetRealValue";
		if($value)
		{
			$params = array (
					"value" => $value
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetFieldName ($fieldid)
	{
		$url = $this->settings["URL"] . "/CustomFields/GetFieldName";
		if($fieldid)
		{
			$params = array (
					"fieldid" => $fieldid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetDefaultValue ($fieldid)
	{
		$url = $this->settings["URL"] . "/CustomFields/GetDefaultValue";
		if($fieldid)
		{
			$params = array (
					"fieldid" => $fieldid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function LoadForm ($formid = 0, $formType = '')
	{
		$url = $this->settings["URL"] . "/NewForms/LoadForm";
		if($formid && $formType)
		{
			$params = array (
					"formid" => $formid, 
					"formType" => $formType
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ValidData ($data = '', $fid = null)
	{
		$url = $this->settings['URL'] . '/CustomFields/ValidData';
		if($data)
		{
			$params = array (
					'data' => $data,
					'fid' => $fid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function ValidateCustomFields ($customfields = array())
	{
		$url = $this->settings['URL'] . '/CustomFields/ValidateCustomFields';
		if(!empty($customfields))
		{
			$params = array (
					'customfields' => $customfields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function ProcessSubscriptionRequest ($email = "", $mobile = "", $disableCheck = false, $data = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/ProcessSubscriptionRequest';
		if($email)
		{
			$params = array (
					'email' => $email,
					'mobile' => $mobile,
					'disableCheck' => $disableCheck, 
					'data' => $data
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ProcessUpdateProfileRequest($updateData)
	{
		$url = $this->settings['URL'] . '/Subscribers/ProcessUpdateProfileRequest';
		if($updateData)
		{
			$params = array (
					'updateData' => $updateData,
			);
			return $this->MakePostRequest($url, $params);
		}
	}

	public function GetListInfo ($lists = array(), $includeDeleted = false)
	{
		$url = $this->settings['URL'] . '/Lists/GetListInfo';
		if($lists)
		{
			$params = array (
					"lists" => $lists, 
					"includeDeleted" => $includeDeleted
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetOwnerInfo($lists = array(), $userID = 0)
	{
		$url = $this->settings['URL'] . '/Users/GetOwnerInfo';
		if ($lists && $userID)
		{
			$params = array(
					'lists' => $lists,
					'userID' => $userID
			);
			return $this->MakePostRequest($url,$params);
		}
		return self::REQUEST_FAILED;		
	}
	public function SendNotificationCheck($lists = array(), $purpose = '')
	{
		$url = $this->settings['URL'] . '/Lists/SendNotificationCheck';
	
		if($lists && $purpose)
		{
			$params = array (
					'lists' => $lists,
					'purpose' => $purpose
			);
				
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function SendEmail ($subject = false, $email = false, $body = false, $listid = false)
	{
		$url = $this->settings['URL'] . '/Messaging/SendEmail';
		if($listid && $email && $subject && $body)
		{
			$params = array (
					'subject' => $subject,
					'email' => $email,
					'body' => $body,
					'listid' => $listid					
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SendEmailMultiple($param = array())
	{
		$url = $this->settings['URL'] . '/Messaging/SendEmailMultiple';
		if($param)
		{
			$params = array (
					'param' => $param
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetMessagingInfo($lists = array())
	{
		$url = $this->settings['URL'] . '/Lists/GetMessagingInfo';
		if($lists)
		{
			$params = array (
					'lists' => $lists
			);
			return $this->MakePostRequest($url, $params);
		}
	}
	
	public function ConfirmUnconfirmedSubscriber($validData = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/ConfirmUnconfirmedSubscriber';
		if($validData)
		{
			$params = array (
					'validData' => $validData
			);
			return $this->MakePostRequest($url, $params);
		}
	}
	
	public function UnsubscribeSubscriberEmail ($emailaddress = false, $listid = false, $subscriberid = false, $skipcheck = false, 
			$statstype = false, $statid = false, $data = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/UnsubscribeSubscriberEmail';
		if((($listid && $emailaddress) || $subscriberid) && !empty($data))
		{
			$params = array(
					'emailaddress' => $emailaddress,
					'listid' => $listid,
					'subscriberid' => $subscriberid,
					'skipcheck' => $skipcheck,
					'statstype' => $statstype,
					'statid' => $statid,
					'data' => $data
			);
			return $this->MakePostRequest($url, $params);
		}
	}
	
	public function UnsubscribeSubscriberMobile ($mobile = false, $listid = false, $subscriberid = false, $skipcheck = false,
			$statstype = false, $statid = false, $data = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/UnsubscribeSubscriberMobile';
		if((($listid && $mobile) || $subscriberid) && !empty($data))
		{
			$params = array(
					'mobile' => $mobile,
					'listid' => $listid,
					'subscriberid' => $subscriberid,
					'skipcheck' => $skipcheck,
					'statstype' => $statstype,
					'statid' => $statid,
					'data' => $data
			);
			return $this->MakePostRequest($url, $params);
		}
	}
	
	public function UnsubscribeSubscriberMultiple($param = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/UnsubscribeSubscriberMultiple';
		if($param)
		{
			$params = array (
					'param' => $param
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
		//
	}
	
	public function ActivateSubscriber ($emailaddress = false, $mobile = false, $mobile_prefix = false, $lists = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/ActivateSubscriber';
		if(($emailaddress || $mobile) && $lists)
		{
			$params = array(
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobile_prefix' => $mobile_prefix,
					'lists' => $lists
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ProcessUnsubscribeRequest($lists = false, $data = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/ProcessUnsubscribeRequest';
		if($lists && $data)
		{
			$params = array(
					'lists' => $lists,
					'data' => $data
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetNewsletterInfo($id = false, $api = '')
	{
		switch ($api)
		{
			case 'newsletter':
				$url = $this->settings['URL'] . '/Newsletters/GetNewsletterInfo';
		break;
			case 'autoresponder':
				$url = $this->settings['URL'] . '/Autoresponders/GetAutoresponderInfo';
		}
		
		if($id)
		{
			$params = array(
					'id' => $id
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function CheckID($newsletterid = false)
	{
		$url = $this->settings['URL'] . '/Newsletters/CheckID';
		if($newsletterid)
		{
			$params = array(
					'newsletterid' => $newsletterid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetDeviceID($param = array())
	{
		$url = $this->settings['URL'] . '/Devices/GetDeviceID';
		if(!empty($param))
		{
			$params = array(
					'param' => $param
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetAllListsForEmailAddress ($emailaddress = false, $listids = array(), $main_listid = false, $activeonly = true, 
			$include_deleted = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetAllListsForEmailAddress';
		if($emailaddress && !empty($listids))
		{
			$params = array(
					'emailaddress' => $emailaddress,
					'listids' => $listids,
					'main_listid' => $main_listid,
					'activeonly' => $activeonly,
					'include_deleted' => $include_deleted
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ProcessUpdateProfile ($updateData = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetAllListsForEmailAddress';
		if(!empty($updateData))
		{
			$params = array(
					'updateData' => $updateData
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SendEmailToFriend($emailComponents = array())
	{
		$url = $this->settings['URL'] . '/Messaging/SendEmailToFriend';
		if(!empty($emailComponents))
		{
			$params = array(
					'emailComponents' => $emailComponents
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function NotifySystemAdmin($emailComponents = array())
	{
		$url = $this->settings['URL'] . '/Messaging/NotifySystemAdmin';
		if(!empty($emailComponents))
		{
			$params = array(
					'emailComponents' => $emailComponents
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	public function GetSubscriberSource ($subscriberid = 0)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetSubscriberSource';
		
		if(!empty($subscriberid))
		{
			
			$params = array (
					'subscriberid' => $subscriberid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetServerTime()
	{
		$url = $this->settings['URL'] . '/ServerInfo/GetServerTime';
		$params = array();
		return $this->MakePostRequest($url, $params);
	}
	
	public function RecordForward($forward_details = array(), $statstype = false)
	{
		$url = $this->settings['URL'] . '/Stats/RecordForward';
		if(!empty($forward_details))
		{
			$params = array(
					'forward_details' => $forward_details,
					'statstype' =>  $statstype
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetEmailForSubscriber($subscriberid = false, $activeonly = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetEmailForSubscriber';
		if($subscriberid)
		{
			$params = array(
					'subscriberid' => $subscriberid,
					'activeonly' => $activeonly
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function CheckDKIM($rawMessage = false)
	{
		$url = $this->settings['URL'] . '/DkimCheck/Check';
		if($rawMessage)
		{
			$params = array(
					'rawMessage' => $rawMessage
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetNewslettersLists($statid = false, $service = false)
	{
		$url = $this->settings['URL'] . '/Stats/GetNewslettersLists';
		if($statid)
		{
			$params = array(
					'statid' => $statid,
					'service' => $service
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function Unsubscribe ($statid = false, $statstype = false, $unsubscribe_details = false)
	{
		$url = $this->settings['URL'] . '/Stats/Unsubscribe';
		if($statid && $statstype && $unsubscribe_details)
		{
			$params = array(
					'statid' => $statid,
					'statstype' => $statstype,
					'unsubscribe_details' =>$unsubscribe_details
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function PrepareCustomfields($postData = array(), $customFields = array())
	{
		$url = $this->settings['URL'] . '/CustomFields/PrepareCustomfields';
		if(!empty($postData) && !empty($customFields))
		{
			$params = array(
					'postData' => $postData,
					'customFields' => $customFields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function LogActivity ($ipaddress = false, $formid = false, $timestamp = false, $reason = false, $getrequest = false, $postrequest = false, $serverrequest = false)
	{
		$url = $this->settings['URL'] . '/FormsActivityLogs/LogActivity';
		if($ipaddress && $timestamp)
		{
			$params = array(
					'ipaddress' => $ipaddress,
					'formid' => $formid,
					'timestamp' => $timestamp,
					'reason' => $reason,
					'getrequest' => $getrequest,
					'postrequest' => $postrequest,
					'serverrequest' => $serverrequest
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function BanIPaddress ($ipaddress = false, $formid = false, $timestamp = false, $reason = false, $getrequest = false, $postrequest = false, $serverrequest = false)
	{
		$url = $this->settings['URL'] . '/FormsActivityLogs/BanIPaddress';
		if($ipaddress && $timestamp)
		{
			$params = array(
					'ipaddress' => $ipaddress,
					'formid' => $formid,
					'timestamp' => $timestamp,
					'reason' => $reason,
					'getrequest' => $getrequest,
					'postrequest' => $postrequest,
					'serverrequest' => $serverrequest
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	//
	public function CanAccessFromIP ($ipaddress = false)
	{
		$url = $this->settings['URL'] . '/FormsActivityLogs/CanAccessFromIP';
		if($ipaddress)
		{
			$params = array(
					'ipaddress' => $ipaddress
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function CreateUser ($userDetails = array())
	{
		$url = $this->settings['URL'] . '/Users/CreateUser';
		if(!empty($userDetails))
		{
			$params = array(
					'userDetails' => $userDetails
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ChangePassword($userID = false, $newPassword = false)
	{
		$url = $this->settings['URL'] . '/Users/ChangePassword';
		if($userID && $newPassword)
		{
			$params = array(
					'userID' => $userID,
					'newPassword' => $newPassword
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function TerminateAccount ($userID = false)
	{
		$url = $this->settings['URL'] . '/Users/TerminateAccount';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function DisableAccount($userID = false)
	{
		$url = $this->settings['URL'] . '/Users/DisableAccount';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function EnableAccount($userID = false)
	{
		$url = $this->settings['URL'] . '/Users/EnableAccount';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ChangePackage($userID = false, $newpackage = false)
	{
		$url = $this->settings['URL'] . '/Users/ChangePackage';
		if($userID && $newpackage)
		{
			$params = array(
					'userID' => $userID,
					'newPackage' => $newpackage
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetPackages()
	{
		$url = $this->settings ['URL'] . '/UserGroups/GetPackages';
		return $this->MakePostRequest ($url);
	}
	
	public function GetAvailableEmailsThisMonth($userID = false)
	{
		$url = $this->settings['URL'] . '/UsersStats/GetAvailableEmailsThisMonth';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscribersNumber($userID = false)
	{
		$url = $this->settings['URL'] . '/UsersStats/GetSubscribersNumber';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetListsNumber($userID = false)
	{
		$url = $this->settings['URL'] . '/UsersStats/GetListsNumber';
		if($userID)
		{
			$params = array(
					'userID' => $userID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSmsStatsByCountry($userID = false, $month = false, $year = false)
	{
		$url = $this->settings['URL'] . '/UsersStats/GetSmsStatsByCountry';
		if($userID)
		{
			$params = array(
					'userID' => $userID,
					'month' => $month,
					'year' => $year
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function AddSmsCredits($userID = false, $country = false, $creditsNumber = false)
	{
		$url = $this->settings['URL'] . '/Users/AddSmsCredits';
		if($userID && $country && $creditsNumber)
		{
			$params = array(
					'userID' => $userID,
					'country' => $country,
					'creditsNumber' => $creditsNumber
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function RecordLinkClicks($linkClicks = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordLinkClicks';
		if(!empty($linkClicks))
		{
			$params = array(
					'linkClicks' => $linkClicks
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function RecordLinkClicksClicks($linkClicks = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordLinkClicksClicks';
		if(!empty($linkClicks))
		{
			$params = array(
					'linkClicks' => $linkClicks
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function RecordOpen($opens = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordOpen';
		if(!empty($opens))
		{
			$params = array(
					'opens' => $opens
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function RecordOpenOpen($opens = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordOpenOpen';
		if(!empty($opens))
		{
			$params = array(
					'opens' => $opens
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
// 	public function RecordOpens($opens = array())
// 	{
// 		$url = $this->settings['URL'] . '/Stats/RecordOpens';
// 		if(!empty($opens))
// 		{
// 			$params = array(
// 					'opens' => $opens
// 			);
// 			return $this->MakePostRequest($url, $params);
// 		}
// 		return self::REQUEST_FAILED;
// 	}
	
	public function RecordAppLinkClicks($appLinks = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordAppLinkClicks';
		if(!empty($appLinks))
		{
			$params = array(
					'appLinks' => $appLinks
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function RecordStatsLink($links = array())
	{
		$url = $this->settings['URL'] . '/Stats/RecordStatsLink';
		if(!empty($links))
		{
			$params = array(
					'links' => $links
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function UpdateMobileNumber($subscriberids = array(), $mobile = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/UpdateMobileNumber';
		if(!empty($subscriberids) && $mobile)
		{
			$params = array(
					'subscriberids' => $subscriberids,
					'mobile' => $mobile
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
// 	public function AddSubscriberToList($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $add_to_autoresponders = false, $skip_listcheck = false)
// 	{
// 		$url = $this->settings['URL'] . '/Subscribers/AddSubscriberToList';
// 		if(($emailaddress || ($mobile && $mobilePrefix)) && $listid)
// 		{
// 			$params = array(
// 					'listid' => $listid,
// 					'emailaddress' => $emailaddress,
// 					'mobile' => $mobile,
// 					'mobilePrefix' => $mobilePrefix,
// 					'add_to_autoresponders' => $add_to_autoresponders,
// 					'skip_listcheck' => $skip_listcheck
// 			);
// 			return $this->MakePostRequest($url, $params);
// 		}
// 		return self::REQUEST_FAILED;
// 	}
	
	public function AddSubscriberToList($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $contactFields = array(), $add_to_autoresponders = false, $skip_listcheck = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/AddSubscriberToList';
		if($listid && ($emailaddress || ($mobile && $mobilePrefix)))
		{
			$params = array(
					'listid' => $listid,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobilePrefix' => $mobilePrefix,
					'contactFields' => $contactFields,
					'add_to_autoresponders' => $add_to_autoresponders,
					'skip_listcheck' => $skip_listcheck
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function CreateList($ownderid = false, $listName = false, $descriptiveName = false, $mobile_prefix = false)
	{
		$url = $this->settings['URL'] . '/Lists/CreateList';
		if($listName && $descriptiveName)
		{
			$params = array(
					'ownerid' => $ownderid,
					'listName' => $listName,
					'descriptiveName' => $descriptiveName,
					'mobile_prefix' => $mobile_prefix
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function UpdateList($ownderid = false, $listid = false, $listName = false, $descriptiveName = false, $mobile_prefix = false)
	{
		$url = $this->settings['URL'] . '/Lists/UpdateList';
		if($listid)
		{
			$params = array(
					'ownderid' => $ownderid,
					'listid' => $listid,
					'listName' => $listName,
					'descriptiveName' => $descriptiveName,
					'mobile_prefix' => $mobile_prefix
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function DeleteList($listid = false, $userid = false)
	{
		$url = $this->settings['URL'] . '/Lists/DeleteList';
		if($listid)
		{
			$params = array (
					'listid' => $listid,
					'userid' => $userid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetLists_Lists($lists = null, $countonly = false, $includeDeleted = false, $searchinfo = array(), $sortinfo = array())
	{
		$url = $this->settings['URL'] . '/Lists/GetLists';
		$params = array (
				'lists' => $lists,
				'countonly' => $countonly,
				'includeDeleted' => $includeDeleted,
				'searchinfo' => $searchinfo,
				'sortinfo' => $sortinfo
		);
		return $this->MakePostRequest($url, $params);
	}
	
	public function GetCustomFields($listids = false)
	{
	    
		$url = $this->settings['URL'] . '/Lists/GetCustomFields';
		
		if(!empty($listids))
		{
			$params = array (
					'listids' => $listids
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetCustomFields_Zapier($listid = false)
	{
		$url = $this->settings['URL'] . '/Zapier/GetCustomFields';
		if(!empty($listid))
		{
			$params = array (
					'contact_list' => $listid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetLists_Users ($userid = false)
	{
		$url = $this->settings['URL'] . '/Users/GetLists';
		if($userid)
		{
			$params = array (
					'userid' => $userid
			);
		}
		return $this->MakePostRequest($url, $params);
	}

	public function GetLinksFromStatId($statids = array())
	{
		$url = $this->settings['URL'] . '/Stats/GetLinksFromStatId';
		if(!empty($statids))
		{
			$params = array (
					'statid' => $statids,
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function UpdateSubscriber($subscriberid = 0, $emailaddress = "", $mobile = "", $listid = 0, $customfields = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/UpdateSubscriber';
		if($subscriberid || $emailaddress)
		{
			$params = array (
					'subscriberid' => $subscriberid,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'listid' => $listid,
					'customfields' => $customfields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function AddClicksToQueue($clicksNumber = 10)
	{
		$url = $this->settings['URL'] . '/TriggerEmails/AddClicksToQueue';
		$params = array(
				'clicksNumber' => $clicksNumber
		);
		return $this->MakePostRequest($url, $params);
	}
	
	public function AddOpensToQueue($opensNumber = 10)
	{
		$url = $this->settings['URL'] . '/TriggerEmails/AddOpensToQueue';
		$params = array(
				'opensNumber' => $opensNumber
		);
		return $this->MakePostRequest($url, $params);
	}
	
	/**
	 *  Method to set counter for Add[Clicks/Opens]ToQueue
	 *  @param String $choice
	 *             Choose whether you want set counter for Clicks or Opens
	 *  @param String $counter
	 *             New counter value
	 *  */
	public function SetCounter($choice = false, $counter = false)
	{
	    $url = $this->settings['URL'] . '/TriggerEmails/SetCounter';
	    if($choice && $counter)
	    {
            $params = array(
                'choice' => $choice,
                'counter' => $counter
            );
            return $this->MakePostRequest($url, $params);
        }
        return self::REQUEST_FAILED;
	}
	
	public function SetValueOTM($data = array(), $subscriberID = false, $fieldID = FALSE)
	{
		$url = $this->settings['URL'] . '/ElasticSearch/SetValue';
		if(!empty($data) && $subscriberID && $fieldID)
		{
			$params = array(
					'data' => $data,
					'subscriberID' => $subscriberID,
					'fieldID' => $fieldID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetOneToManySubscriberData($subscriberID = false, $fieldID = false)
	{
		$url = $this->settings['URL'] . '/CustomFields/GetOneToManySubscriberData';
		if($subscriberID > 0 && $fieldID > 0)
		{
			$params = array(
					'subscriberID' => $subscriberID,
					'fieldID' => $fieldID
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ZapierAddSubscriberToList($username = false, $password = false, $listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $contactFields = array(), $add_to_autoresponders = false, $skip_listcheck = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/AddSubscriberToList';
		if($listid && ($emailaddress || ($mobile && $mobilePrefix)))
		{
			$params = array(
					'username' => $username,
					'password' => $password,
					'listid' => $listid,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobilePrefix' => $mobilePrefix,
					'contactFields' => $contactFields,
					'add_to_autoresponders' => $add_to_autoresponders,
					'skip_listcheck' => $skip_listcheck
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetCustomFieldsForLists ($listids = false, $fieldids = array(), $fieldTypes = array())
	{
		$url = $this->settings['URL'] . '/CustomFields/GetCustomFieldsForLists';
		if($listids)
		{
			$params = array(
					'listids' => $listids,
					'fieldids' => $fieldids,
					'fieldTypes' => $fieldTypes
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function LoadSubscribersBasicInformation ($subscriberids = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/LoadSubscribersBasicInformation';
		if($subscriberids)
		{
			$params = array(
					'subscriberids' => $subscriberids
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscribers ($searchinfo = array(), $countonly = false, $sortdetails = array(), $queuedetails = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/GetSubscribers';
		if(!empty($searchinfo))
		{
			$params = array(
					'searchinfo' => $searchinfo,
					'countonly' => $countonly,
					'sortdetails' => $sortdetails,
					'queuedetails' => $queuedetails
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function FetchSubscribers ($searchinfo = array(), $pageid = 1, $perpage = 20, $sortdetails = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/FetchSubscribers';
		if(!empty($searchinfo))
		{
			$params = array(
					'searchinfo' => $searchinfo,
					'pageid' => $pageid,
					'perpage' => $perpage,
					'sortdetails' => $sortdetails
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetAllSubscriberCustomFields ($listids = array(), $limit_fields = array(), $subscriberids = array(), $custom_fieldids = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/GetAllSubscriberCustomFields';
		
		if(!empty($listids))
		{
			$params = array(
					'listids' => $listids,
					'limit_fields' => $limit_fields,
					'subscriberids' => $subscriberids,
					'custom_fieldids' => $custom_fieldids
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	public function ImportSubscriberLine ($line, $linked_fields, $importresults, $list,	$update_unsubscribed = true, $sourcesignup, $batch_import, $importinfo)
	{
		$url = $this->settings['URL'] . '/DataSynch/ImportSubscriberLine';
		if($line && ($list || ($importinfo && $linked_fields)))
		{
			$params = array(
					'line' => $line,
					'linked_fields' => $linked_fields,
					'importresults' => $importresults,
					'list' => $list,
					'update_unsubscribed' => $update_unsubscribed,
					'sourcesignup' => $sourcesignup,
					'batch_import' => $batch_import,
					'importinfo' => $importinfo,
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function LinkFields($customfields,$topline)
	{
		$url = $this->settings['URL'] . '/DataSynch/LinkFields';
		
		if($customfields && $topline)
		{
			$params = array(
					'customfields' => $customfields,
					'topline' => $topline
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	public function SendNotificationDatasynch ($email = '', $body = '', $subject = '')
	{
		$url = $this->settings['URL'] . '/Messaging/SendNotificationDatasynch';
		
		if($email != '')
		{
			$params = array(
					'email' => $email,
					'body' => $body,
					'subject' => $subject
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	/**
	 * CopyNewsletter
	 * Copy a newsletter along with attachments, images etc.
	 *
	 * @param Int $oldid
	 *        	Newsletterid of the newsletter to copy.
	 * @param String $name
	 * 			Name of the copied newsletter.
	 * @param String $subject
	 * 			Subject of the copied newsletter.
	 *
	 * @return Array Returns an array of statuses. The first one is whether the
	 *         newsletter could be found/loaded/copied, the second is whether
	 *         the images/attachments could be copied. Both are true for
	 *         success, false for failure.
	 */
	public function CopyNewsletter($oldid = false, $name = false, $subject = false)
	{
	   
	    $url = $this->settings['URL'] . '/Newsletters/CopyNewsletter';
	    
	    if($oldid)
	    {
	        $params = array(
	            'oldid' => $oldid,
	            'name' => $name,
	            'subject' => $subject
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	
	
	

	
	
}