<?php

namespace emailplatform;

class ApiParser
{

	const REQUEST_FAILED = 'Unsuccessful request';

	var $settings = array ();
	

	/** Production **/
  	var $URL = 'https://api.mailmailmail.net/v1.1/';

    
	
	public function __construct ($settings = array())
	{
		$this->settings = $settings;
	}

	protected function GetHTTPHeader ()
	{
		switch($this->settings["format"])
		{
			case "xml":
				return array (
						"Accept: application/xml; charset=utf-8",
						"ApiUsername: " . $this->settings['username'],
						"ApiToken: " . $this->settings['token']
				);
			break;
			case "serialized":
				return array (
						"Accept: application/vnd.php.serialized; charset=utf-8",
						"ApiUsername: " . $this->settings['username'],
						"ApiToken: " . $this->settings['token']
				);
			break;
			case "php":
				return array (
						"Accept: application/vnd.php; charset=utf-8",
						"ApiUsername: " . $this->settings['username'],
						"ApiToken: " . $this->settings['token']
				);
			break;
			case "csv":
				return array (
						"Accept: application/csv; charset=utf-8",
						"ApiUsername: " . $this->settings['username'],
						"ApiToken: " . $this->settings['token']
				);
			break;
			default:
				return array (
						"Accept: application/json; charset=utf-8",
						"ApiUsername: " . $this->settings['username'],
						"ApiToken: " . $this->settings['token']
				);
			break;
		}
	}

	private function DecodeResult ($input = '')
	{
		switch($this->settings["format"])
		{
			case "xml":
				// @todo implement parser
				return $input;
			break;
			case "serialized":
				// @todo implement parser
				return $input;
			break;
			case "php":
				// @todo implement parser
				return $input;
			break;
			case "csv":
				// @todo implement parser
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
		if(!empty($fields))
		{
			$url .= "?" . http_build_query($fields, '', '&');
		}
		// set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// disable for security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		// execute post
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
// 		return $result;
		return $this->DecodeResult($result);
	}

	private function MakePostRequest ($url = "", $fields = array())
	{
		try
		{
			// open connection
			$ch = curl_init();
			
			// add the setting to the fields
// 			$data = array_merge($fields, $this->settings);
			$encodedData = http_build_query($fields, '', '&');
			
			// set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
			// disable for security
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
		$encodedData = http_build_query($fields, '', '&');
		
		// set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
		// disable for security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		// execute post
		$result = curl_exec($ch);
		
		// close connection
		curl_close($ch);
		return $this->DecodeResult($result);
	}

	//testing methods start
	
	
	
	public function HasRequirements ()
	{
		if(!is_callable('curl_init'))
		{
			return 'curl is not installed correctly!';
		}
		
		$params = array (
				"test" => "a"
		);
		$url = $this->URL . "/Test";
		
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
		return 'All requirements work correctly';
	}
	
	public function TestUserToken()
	{
		$url = $this->URL . "/Test/TestUserToken";
		return $this->MakePostRequest($url);
	}
	
	//testing methods end

	/**
	 * IsSubscriberOnList
	 * Checks whether a subscriber is on a particular list based on their email
	 * address/mobile or subscriberid and whether you are checking only for active
	 * subscribers.
	 *
	 * @param Array $listids
	 *        	Lists to check on. If this is not an array, it's turned in to
	 *        	one for easy checking.
	 * @param String $emailaddress
	 *        	Email address to check for.
	 * @param String $mobile
	 *        	Mobile phone to check for.
	 * @param String $mobilePrefix
	 * 			Country calling code.
	 * @param Int $subscriberid
	 *        	Subscriber id. This can be used instead of the email address.
	 * @param Boolean $activeonly
	 *        	Whether to only check for active subscribers or not. By
	 *        	default this is false - so it will not restrict searching.
	 * @param Boolean $not_bounced
	 *        	Whether to only check for non-bounced subscribers or not. By
	 *        	default this is false - so it will not restrict searching.
	 * @param Boolean $return_listid
	 *        	Whether to return the listid as well as the subscriber id. By
	 *        	default this is false, so it will only return the
	 *        	subscriberid. The bounce processing functions changes this to
	 *        	true, so it returns the list and the subscriber id's.
	 * @return Int|False Returns false if there is no such subscriber. Otherwise
	 *         returns the subscriber id.
	 */
	

	public function IsSubscriberOnList ($listids = array(), $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $activeonly = false, 
			$not_bounced = false, $return_listid = false)
	{
		$url = $this->URL . "/Subscribers/IsSubscriberOnList";
		$emailaddress = trim($emailaddress);
		$mobile = trim($mobile);
		if(!empty($listids) && ($emailaddress || ($mobile && $mobilePrefix) || ($subscriberid && intval($subscriberid) != 0)))
		{
		   
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
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	/**
	 * IsUnSubscriber
	 * Checks whether an email address is an 'unsubscriber' - they have
	 * unsubscribed from a list.
	 *
	 * @param Int $listid
	 *        	List to check for.
	 * @param String $emailaddress
	 *        	Email Address to check.
	 * @param String $mobile
	 * 			Mobile number to check.
	 * @param String $mobilePrefix
	 * 			Country calling code.
	 * @param Int $subscriberid
	 *        	Subscriber id to check.
	 * @param String $service
	 * 			Whether to check from email campaigns or sms campaigns.
	 * @return Int|False Returns the unsubscribed id if there is one. Returns
	 *         false if there isn't one.
	 */
	public function IsUnSubscriber ($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $service = false)
	{
		$url = $this->URL . '/Subscribers/IsUnSubscriber';
		if($listid && ($emailaddress || $mobile || $subscriberid))
		{
			$params = array (
					"listid" => $listid,
					"emailaddress" => $emailaddress,
					"mobile" => $mobile,
					"mobilePrefix" => $mobilePrefix,
					"subscriberid" => $subscriberid,
					"service" => $service
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	/**
	 * Load
	 * Loads up the customfield and sets the appropriate class variables.
	 * This handles loading of a subclass with different options and settings.
	 *
	 * @param Int $fieldid
	 *        	The fieldid to load up. If the field is not present then it
	 *        	will not load up.
	 * @param Boolean $return_options
	 *        	Whether to return the information loaded from the database or
	 *        	not. The default is not to return the options, so this sets up
	 *        	the class variables instead. Only subscriber importing should
	 *        	need to return the options.
	 *
	 * @return Boolean Will return false if the fieldid is not present, or the
	 *         field can't be found, otherwise it set the class vars,
	 *         associations and options and return true.
	 */
	public function LoadCustomField ($fieldid = 0, $return_options = false, $makeInstance = false)
	{
	    $url = $this->URL . "/CustomFields/LoadCustomField";
		if($fieldid)
		{
			$params = array (
					"fieldid" => $fieldid, 
					"return_options" => $return_options, 
					"makeInstance" => $makeInstance
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}


	/**
	 * UnsubscribeSubscriberEmail
	 * Unsubscribes an email address from a particular list.
	 *
	 * @param Int $listid
	 *        	List to remove subscriber from.
	 * @param String $emailaddress
	 *        	Subscriber's email address to unsubscribe.
	 * @param Int $subscriberid
	 *        	Subscriberid to remove.
	 * @param Boolean $skipcheck
	 *        	Whether to skip the check to make sure they are on the list.
	 * @param Int $statid
	 *        	The statistics id we're updating so we can see (through stats)
	 *        	the number of people who have unsubscribed directly from a
	 *        	send.
	 * @return Array Returns a status (success,failure) and a reason why.
	 */
	public function UnsubscribeSubscriberEmail ($listid = false, $emailaddress = false, $subscriberid = false, $skipcheck = false, 
			$statid = false)
	{
		$url = $this->URL . '/Subscribers/UnsubscribeSubscriberEmail';
		if($listid && ($emailaddress || $subscriberid))
		{
			$params = array ( 
					'listid' => $listid, 
					'emailaddress' => $emailaddress,
					'subscriberid' => $subscriberid, 
					'skipcheck' => $skipcheck,
					'statid' => $statid
			);
			return $this->MakePostRequest($url, $params);
		}
	}

	/**
	 * UnsubscribeSubscriberMobile
	 * Unsubscribes a mobile phone from a particular list.
	 *
	 * @param Int $listid
	 *        	List to remove them from.
	 * @param String $mobile
	 *        	Subscriber's mobile phone to unsubscribe.
	 * @param String $mobilePrefix
	 * 			Country calling code.
	 * @param Int $subscriberid
	 *        	Subscriberid to remove.
	 * @param Boolean $skipcheck
	 *        	Whether to skip the check to make sure they are on the list.
	 * @param Int $statid
	 *        	The statistics id we're updating so we can see (through stats)
	 *        	the number of people who have unsubscribed directly from a
	 *        	send.
	 * @return Array Returns a status (success,failure) and a reason why.
	 */
	public function UnsubscribeSubscriberMobile ($listid = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $skipcheck = false, 
			$statid = false)
	{
		$url = $this->URL . '/Subscribers/UnsubscribeSubscriberMobile';
		if($listid && (($mobile && $mobilePrefix) || $subscriberid))
		{
			$params = array (
					'listid' => $listid, 
					'mobile' => $mobile, 
					'mobilePrefix' => $mobilePrefix,
					'subscriberid' => $subscriberid, 
					'skipcheck' => $skipcheck,
					'statid' => $statid
			);
			return $this->MakePostRequest($url, $params);
		}
	}
	
	/**
	 * AddSubscriberToList
	 * Adds a subscriber to a list.
	 * Checks whether the list actually exists. If it doesn't, returns an error.
	 *
	 * @param Int $listid
	 *        	The list to add the subcriber to.
	 * @param String $emailaddress
	 *        	Subscriber address to add to the list.
	 * @param String $mobile
	 *        	Subscriber mobile phone to add to list.
	 * @param String $mobilePrefix
	 * 			Subscriber country calling code.
	 * @param Array $contactFields
	 * 			Subscribers' contact fields.
	 * @param Boolean $add_to_autoresponders
	 *        	Whether to add the subscriber to the lists' autoresponders or
	 *        	not.
	 * @param Boolean $skip_listcheck
	 *        	Whether to skip checking the list or not. This is useful if
	 *        	you've already processed the lists to make sure they are ok.
	 *
	 * @return Boolean Returns false if there is an invalid subscriber or list
	 *         id, or if the list doesn't really exist. If it works, then it
	 *         returns the new subscriber id from the database.
	 */
	public function AddSubscriberToList($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $contactFields = array(), $add_to_autoresponders = false, $skip_listcheck = false, $confirmed = true)
	{
		$url = $this->URL . '/Subscribers/AddSubscriberToList';
		if(($emailaddress || ($mobile && $mobilePrefix)) && $listid)
		{
			$params = array(
					'listid' => $listid,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobilePrefix' => $mobilePrefix,
					'contactFields' => $contactFields,
					'add_to_autoresponders' => $add_to_autoresponders,
					'skip_listcheck' => $skip_listcheck,
					'confirmed' => $confirmed
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ResubscribeContact($listid = false, $emailaddress = false, $mobileNumber = false, $mobilePrefix = false, $add_to_autoresponders = false, $contactFields = array())
	{
		$url = $this->URL . '/Subscribers/ResubscribeContact';
		if($listid && ($emailaddress || ($mobileNumber && $mobilePrefix)))
		{
			$params = array(
					'listid' => $listid,
					'emailaddress' => $emailaddress,
					'mobileNumber' => $mobileNumber,
					'mobilePrefix' => $mobilePrefix,
					'add_to_autoresponders' => $add_to_autoresponders,
					'contactFields' => $contactFields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetLists
	 * Gets a list of lists that this user owns / has access to.
	 *
	 *
	 * @return Array Returns an array - list of listid's this user has created
	 *         (or if the user is an admin/listadmin, returns everything).
	 */
	public function GetLists()
	{
		$url = $this->URL . '/Users/GetLists';
		$params = array ();
		return $this->MakeGetRequest($url, $params);
	}
	
	/**
	 * CreateList
	 * This function creates a list based on the current class vars.
	 *
	 * @param String $listName
	 * 			Name of the list.
	 * @param String $descriptiveName
	 * 			List description.
	 * @param String $mobile_prefix
	 * 			Default country calling code.
	 * @param Array $contact_fields
	 * 			Comma separated list of contact fields id related to this contact list.
	 * 
	 * @return Boolean Returns true if it worked, false if it fails.
	 */
	public function CreateList($listName = false, $descriptiveName = false, $mobile_prefix = false, $contact_fields = array())
	{
		$url = $this->URL . '/Lists/CreateList';
		if($listName && $descriptiveName)
		{
			$params = array(
					'listName' => $listName,
					'descriptiveName' => $descriptiveName,
					'mobile_prefix' => $mobile_prefix,
					'contact_fields' => $contact_fields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * UpdateList
	 * Updates a current list based on the current class vars.

	 * @param Int @listid
	 * 			ID of list which you want to edit.
	 * @param String $listName
	 * 			New name of the list.
	 * @param String $descriptiveName
	 * 			New list description.
	 * @param String $mobile_prefix
	 * 			New country calling code.
	 *
	 * @return Boolean Returns true if it worked, false if it fails.
	 */
	public function UpdateList($listid = false, $listName = false, $descriptiveName = false, $mobile_prefix = false)
	{
		$url = $this->URL . '/Lists/UpdateList';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
					'listName' => $listName,
					'descriptiveName' => $descriptiveName,
					'mobile_prefix' => $mobile_prefix
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * DeleteList
	 * Delete a list from the database.
	 *
	 * @param Int $listid
	 *        	Listid of the list to delete. If not passed in, it will delete
	 *        	'this' list.
	 *
	 * @return Boolean True if it deleted the list, false otherwise.
	 */
	public function DeleteList($listid = false)
	{
		$url = $this->URL . '/Lists/DeleteList';
		if($listid)
		{
			$params = array (
					'listid' => $listid
			);
			return $this->MakeDeleteRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * CreateCustomField
	 * Create new custom field
	 *
	 * @param string $name name of custom field.
	 * @param string $fieldtype type of custom field.
	 * @param Array $fieldsettings settings for custom field.
	 *
	 * @return int id of new custom field.
	 */
	public function CreateCustomField($name = '', $fieldtype = '', $fieldsettings = array(), $listids = false)
	{
	    $url = $this->URL . '/CustomFields/CreateCustomField';
	    if($name && $fieldtype)
	    {
	        $params = array (
	            'name' => $name,
	            'fieldtype' => $fieldtype,
	            'fieldsettings' => $fieldsettings,
	            'listids' => $listids
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	/**
	 * GetCustomFields
	 * Fetches custom fields for the list(s) specified.
	 *
	 * @param Array $listids
	 *        	An array of listids to get custom fields for. If not passed
	 *        	in, it will use 'this' list. If it's not an array, it will be
	 *        	converted to one.
	 *          
	 * @return Array Custom field information for the list provided.
	 */
	public function GetCustomFields($listids = false)
	{
		$url = $this->URL . '/Lists/GetCustomFields';
		
		$params = array (
				'listids' => $listids
		);
		return $this->MakeGetRequest($url, $params);
	}
	
	/**
	 * SendNewsletter
	 * 		Attempts to send a newsletter to specific subscriber or seeks for subscriber with given email address
	 * @param number $newsletterid the ID of the newsletter that will be sent
	 * @param number $subscriberid [optional] recipient subscriber's ID, $subscriberid or $email required
	 * @param string $email [optional] address used to found recipient from posible recipients of the newsletter, $subscriberid or $email required
	 * @param string $senderEmail [optional] sender email from which the email will appear to be sent
	 * @param string $senderName [optional] sender name from which the email will appear to be sent
	 * @param string $replyEmail [optional] reply to email, replying will be use this email
	 * @return boolean True if newsletter was sent, False otherwise
	 */
	public function SendNewsletter($newsletterid = 0, $subscriberid = 0, $email = '', $senderEmail = '', $senderName = '', $replyEmail = '', $callbackUrl = false, $reloadFeed = false)
	{
		$email = trim($email);
		$subscriberid = intval($subscriberid);
		$newsletterid = intval($newsletterid);
		$senderEmail = trim($senderEmail);
		$senderName = trim($senderName);
		$replyEmail = trim($replyEmail);
		
		$url = $this->URL . '/Messaging/SendNewsletter';
		
		if($newsletterid && ($subscriberid || $email))
		{
			$data = array(
					'newsletterid' => $newsletterid,
					'subscriberid' => $subscriberid,
					'email' => $email,
					'fromaddress' => $senderEmail,
					'fromname' => $senderName,
					'replyaddress' => $replyEmail,
					'callbackUrl' => $callbackUrl,
					'reloadFeed' => $reloadFeed
			);
			return $this->MakePostRequest($url, $data);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * SendSMS
	 * 		Attempts to send a sms to specific subscriber or seeks for subscriber with given mobile number
	 * @param string $subject for the sms
	 * @param string $text for the sms
	 * @param number $subscriberid [optional] recipient subscriber's ID, $subscriberid or $mobile and $listid required
	 * @param number $listid [optional] recipient list ID, $listid and $mobile or $subscriberid required
	 * @param string $mobile [optional] recipient mobile number $listid and $mobile or $subscriberid required
	 * @param string $mobilePrefix [optional] recipient mobile prefix $listid and $mobile or $subscriberid required
	 * @param string $country country of sending for which there are credits
	 * @return boolean True if newsletter was sent, False otherwise
	 */
	public function SendSMS($campaignid = 0, $subject = '', $text = '', $subscriberid = 0, $listid = 0, $mobile = '', $mobilePrefix = '')
	{
		$campaignid = intval($campaignid);
		$subscriberid = intval($subscriberid);
		$subject = trim($subject);
		$text = trim($text);
		$listid = intval($listid);
		$mobile = trim($mobile);
		$mobilePrefix = trim($mobilePrefix);
		
		$url = $this->URL . '/SMS/Send';
		
		if(($campaignid || ($subject && $text)) && ($subscriberid || ($listid && $mobile && $mobilePrefix)))
		{
			$data = array(
					'campaignid' => $campaignid,
					'subject' => $subject,
					'text' => $text,
					'subscriberid' => $subscriberid,
					'listid' => $listid,
					'mobile' => $mobile,
					'mobilePrefix' => $mobilePrefix
			);
			return $this->MakePostRequest($url, $data);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetSubscribers
	 * Returns a list of subscriber id's based on the information passed in.
	 * 
	 * @param Array $searchinfo
	 *        	An array of search information to restrict searching to. This
	 *        	is used to construct queries to cut down the subscribers
	 *        	found.
	 * @param Boolean $countonly
	 * 			Whether to only do a count or get the list of subscribers as well.
	 * 
	 * @return Mixed This will return the count only if that is set to true.
	 *         Otherwise this will return an array of data including the count
	 *         and the subscriber list.Or returns boolean if $atleastone is set
	 *         to true
	 */
	public function GetSubscribers ($searchinfo = array(), $countonly = false, $limit = 1000, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetSubscribers';
		
		$params = array (
				'searchinfo' => $searchinfo,
				'countonly' => $countonly,
				'limit' => $limit,
				'offset' => $offset
		);		
		return $this->MakeGetRequest($url, $params);	
	}
	
	
	public function GetSubscribersByCustomField ($listid = false, $data = array(), $activeonly = true, $countonly = false, $limit = 1000, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetSubscribersByCustomField';
		
		if(!empty($data) && $listid)
		{
			$params = array (
					'listid' => $listid,
					'data' => $data,
			        'activeonly' => $activeonly,
					'countonly' => $countonly,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetSubscriberDetails
	 * Gets subscriber data including all related events and bounces.
	 *
	 * @param Integer $listid
	 * 			Contact list you are searching on.
	 * @param Integer $subscriberid
	 * 			ID of the subscriber you want to get more details.
	 * @param String $emailaddress
	 *        	Email address of the subscriber you want to get more details.
	 * @param String $mobile
	 * 			Mobile number the subscriber you want to get more details.
	 * @param String $mobile_prefix
	 * 			Country calling code.
	 * 
	 * @return Array Return an array of subscribers details.
	 */
	public function GetSubscriberDetails($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobile_prefix = false)
	{
		$url = $this->URL . '/Subscribers/GetSubscriberDetails';
		if(($emailaddress || $mobile || $subscriberid) && $listid)
		{
			$params = array(
					'listid' => $listid,
					'subscriberid' => $subscriberid,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobile_prefix' => $mobile_prefix
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscriberEvents($listid = false, $subscriberid = false, $limit = 100, $offset = 0)
	{
	    $url = $this->URL . '/Subscribers/GetSubscriberEvents';
	    if($subscriberid && $listid)
	    {
	        $params = array(
	            'listid' => $listid,
	            'subscriberid' => $subscriberid,
	            'limit' => $limit,
	            'offset' => $offset
	        );
	        return $this->MakeGetRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	/**
	 * Save Subscriber CustomFields
	 * Saves custom field information for a particular subscriber, particular
	 * list and particular field.
	 * NOTE:
	 * - Any old custom field data will be deleted.
	 * - NULL data values will not be saved to the database.
	 *
	 * @param Integer $subscriberid
	 *        	ID of the subscriber whose data need to be updated.
	 * @param Integer $fieldid
	 *        	The ID of contact field you are saving for.
	 * @param Mixed $value
	 *        	The actual custom field value. If this is an array, it will be
	 *        	serialized up before saving.
	 * @param $skipEmptyData
	 * 			Method won't be executed if field value is empty.
	 * 
	 * @return Boolean Returns TRUE if successful, FALSE otherwise.
	 */
	public function SaveSubscriberCustomField($subscriberid = false, $fieldid = false, $value = false, $skipEmptyData = false)
	{
		$url = $this->URL . '/Subscribers/SaveSubscriberCustomField';
		if($subscriberid && $fieldid)
		{
			$params = array(
					'subscriberid' => $subscriberid,
					'fieldid' => $fieldid,
					'value' => $value,
					'skipEmptyData' => $skipEmptyData
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SaveSubscriberCustomFieldByList($listid = false, $fieldid = false, $data = false, $searchinfo = false)
	{
		$url = $this->URL . '/Subscribers/SaveSubscriberCustomFieldByList';
		if($listid && $fieldid)
		{
			$params = array(
					'listid' => $listid,
					'fieldid' => $fieldid,
					'data' => $data,
					'searchinfo' => $searchinfo
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
		
	/**
	 * LoadSubscriberListCustomFields
	 * Loads customfield data based on the list specified.
	 *
	 * @param Int $subscriberid
	 *        	Subscriber to load up.
	 * @param Int $listid
	 *        	The list the subscriber is on.
	 *        
	 * @return Array Returns the subscribers custom field data for that
	 *         particular list.
	 */
	public function LoadSubscriberCustomFields($subscriberid = false, $listid = false)
	{
		$url = $this->URL . '/Subscribers/LoadSubscriberCustomFields';
		if($subscriberid && $listid)
		{
			$params = array(
				'subscriberid' => $subscriberid,
				'listid' => $listid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * DeleteSubscriber
	 * Deletes a subscriber and their information from a particular list.
	 *
	 * @param Integer $listid
	 *        	List to delete them off.
	 * @param String $emailaddress
	 *        	Email Address to delete.
	 * @param String $mobile
	 * 			Mobile to delete.
	 * @param String $mobilePrefix
	 * 			Country calling code.
	 * @param Integer $subscriberid
	 *        	Subscriberid to delete.
	 *
	 * @return Array Returns a status (success,failure) and a reason why.
	 */
	public function DeleteSubscriber($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false)
	{
		$url = $this->URL . '/Subscribers/DeleteSubscriber';
		if($listid && ($emailaddress || $mobile || $subscriberid))
		{
			$params = array (
					"listid" => $listid,
					"emailaddress" => $emailaddress,
					"mobile" => $mobile,
					"mobilePrefix" => $mobilePrefix,
					"subscriberid" => $subscriberid
			);
			return $this->MakeDeleteRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * UpdateSubscriber
	 * Updates subscriber info.
	 *
	 * @param Integer $listid
	 * 			List from which the subscriber will be updated.
	 * @param Integer $subscriberid
	 * 			Subscriberid to update.
	 * @param String $emailaddress
	 * 			Email address of the subscriber you want update.
	 * @param String $mobile
	 * 			Mobile of the subscriber you want update.
	 * @param String $mobilePrefix
	 * 			Country calling code. 
	 * @param Array $customfields
	 *        	Contact fields to be updated.
	 *        
	 * @return Array Returns a status (success,failure) and a reason why.
	 */
	public function UpdateSubscriber($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $customfields = array())
	{
		$url = $this->URL . '/Subscribers/UpdateSubscriber';
		if($listid && ($subscriberid || ($emailaddress || $mobile)))
		{
			$params = array(
					'listid' => $listid,
					'subscriberid' => $subscriberid,
					'emailaddress' => $emailaddress,
			        'mobile' => $mobile,
					'mobilePrefix' => $mobilePrefix,
					'customfields' => $customfields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * ChangeMobile
	 * Change subscriber mobile.
	 *
	 * @param Integer $listid
	 * 			List from which the subscriber will be updated.
	 * @param Integer $subscriberid
	 * 			Subscriberid to update.
	 * @param String $mobile
	 * 			Mobile of the subscriber you want update.
	 * @param String $mobilePrefix
	 * 			Country calling code.
	 * @return Array Returns a status (success,failure) and a reason why.
	 */
	public function ChangeMobile($listid = false, $subscriberid = false, $mobile = false, $mobilePrefix = false)
	{
	    $url = $this->URL . '/Subscribers/ChangeMobile';
	    if($listid && $subscriberid && $mobile)
	    {
	        $params = array(
	            'listid' => $listid,
	            'subscriberid' => $subscriberid,
	            'mobile' => $mobile,
	            'mobilePrefix' => $mobilePrefix
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	
	/**
	 * RequestUpdateEmail
	 * Request to change current email address.
	 *
	 *
	 * @param Integer $subscriberid
	 * 			Subscriberid to update.
	 * @param Integer $listid
	 * 			List from which the subscriber will be updated.
	 * @param String $oldemail
	 * 			Current email address.
	 * @param String $newemail
	 * 			New email address.
	 * @param Array $contactFields
	 *        	Contact fields to be updated.
	 *
	 * @return Integer Returns a status (true/false).
	 */
	public function RequestUpdateEmail($subscriberid = false, $listid = false, $oldemail = false, $newemail = false, $contactFields = array())
	{
		$url = $this->URL . '/Subscribers/RequestUpdateEmail';
		if($listid && $subscriberid && !empty($oldemail) && !empty($newemail))
		{
			$params = array(
					'subscriberid' => $subscriberid,
					'listid' => $listid,
					'oldemail' => $oldemail,
					'newemail' => $newemail,
					'contactFields' => $contactFields
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * FetchStats
	 * Fetches the details of a newsletter or autoresponder statistics entry
	 *
	 * @param Integer $statid
	 *        	The statid of the entry you want to retrieve from the
	 *        	database.
	 * @param String $statstype
	 *        	The type of statistics the entry you are retrieving is
	 *        	(newsletter / autoresponder)
	 *
	 * @return Array Returns an array of details about the statistics entry
	 */
	public function FetchStats($statid = false, $statstype = false)
	{
		$url = $this->URL . '/Stats/FetchStats';
		if($statid && $statstype)
		{
			$params = array(
					'statid' => $statid,
					'statstype' => $statstype
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetBouncesByList
	 * Fetches a list of bounced emails.
	 *
	 * @param Integer $listid
	 *        	Id of a list from which the results are fetched.
	 * @param Boolean $count_only
	 * 			Whether to return the number of bounces instead of a list of bounces.
	 * @param String $bounce_type
	 * 			The type of bounce to get results for ("soft","hard","any").
	 * @param String $searchType
	 *			Which search rule should be used in date search. 
	 *			Possible values: before, after, between, not, exact/exactly.
	 * @param String $searchStartDate
	 * 			Date for filtering.
	 * @param String $searchEndDate
	 * 			Date for filtering.
	 * 
	 * @return Array Returns an array of details about the statistics entry
	 */
	public function GetBouncesByList($listid = false, $count_only = false, $bounce_type = false, $searchType = false, $searchStartDate = false, $searchEndDate = false)
	{
		$url = $this->URL . '/Stats/GetBouncesByList';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
					'count_only' => $count_only,
					'bounce_type' => $bounce_type,
					'searchType' => $searchType,
					'searchStartDate' => $searchStartDate,
					'searchEndDate' => $searchEndDate
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetUnsubscribesByList
	 * Fetches a list of unsubscribed emails.
	 *
	 * @param Integer $listid
	 *        	Id of a list from which the results are fetched.
	 * @param Boolean $count_only
	 * 			Whether to return the number of bounces instead of a list of bounces.
	 * @param String $searchType
	 *			Which search rule should be used in date search.
	 *			Possible values: before, after, between, not, exact/exactly.
	 * @param String $searchStartDate
	 * 			Date for filtering.
	 * @param String $searchEndDate
	 * 			Date for filtering.
	 * 
	 * @return Array Returns an array of details about the statistics entry
	 */
	public function GetUnsubscribesByList($listid = false, $count_only = false, $searchType = false, $searchStartDate = false, $searchEndDate = false)
	{
		$url = $this->URL . '/Stats/GetUnsubscribesByList';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
					'count_only' => $count_only,
					'searchType' => $searchType,
					'searchStartDate' => $searchStartDate,
					'searchEndDate' => $searchEndDate
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetOpens
	 * Fetches a list of subscribers who opened a campaign or autoresponder.
	 *
	 * @param Integer $statid
	 *        	The statids you want to fetch data for.
	 * @param Boolean $count_only
	 *        	Specify True to return the number of opens instead of a list
	 *        	of opens.
	 * @param Int $only_unique
	 *        	Specify true to count/retrieve unique opens only, specify
	 *        	false for all opens.
	 *        
	 * @return Array Returns an array of opens or if $count_only was set to true
	 *         returns the number of opens in total
	 */
	public function GetOpens($statid = false, $count_only = false, $only_unique = false)
	{
		$url = $this->URL . '/Stats/GetOpens';
		if($statid)
		{
			$params = array(
					'statid' => $statid,
					'count_only' => $count_only,
					'only_unique' => $only_unique
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetRecipients
	 * Fetches a list of recipients for an autoresponder.
	 *
	 * @param Integer $statid
	 *        	The statid you want to fetch data for.
	 * @param Boolean $count_only
	 *        	Specify True to return the number of recipients instead of a
	 *        	list of recipients.
	 *        
	 * @return Array Returns an array of recipients or if $count_only was set to
	 *         true returns the number of recipients in total
	 */
	public function GetRecipients($statid = false, $count_only = false)
	{
		$url = $this->URL . '/Stats/GetRecipients';
		if($statid)
		{
			$params = array (
					'statid' => $statid,
					'count_only' => $count_only
			);
			return $this->MakeGetRequest($url, $params);
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
		$url = $this->URL . '/Newsletters/CopyNewsletter';
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
	
	/**
	 * GetNewsletters
	 * Get a list of NewsletterDB based on the criteria passed in.
	 *
	 * @param Boolean $countonly
	 *        	Whether to only get a count of lists, rather than the
	 *        	information.
	 * @param Boolean $getLastSentDetails
	 * 			Get info about the last sent details.
	 * @param Boolean $content
	 * 			Whether to show campaign content or not.
	 * @param String $aftercreatedate
	 * 			Get newsletters created after this date.
	 * @param String $newsletterNameLike
	 * 			Get newsletters with name like this.
	 *
	 * @return Mixed Returns false if it couldn't retrieve newsletter
	 *         information. Otherwise returns the count (if specified), or an
	 *         array of NewsletterDB.
	 */
	public function GetNewsletters($countOnly= false, $getLastSentDetails = false, 
			$content = true, $aftercreatedate = false, $newsletterNameLike = false, $limit = false, $offset = false)
	{
		$url = $this->URL . '/Newsletters/GetNewsletters';
		
		$params = array (
				'countOnly' => $countOnly,
				'getLastSentDetails' => $getLastSentDetails,
				'content' => $content,
				'aftercreatedate' => $aftercreatedate,
				'newsletterNameLike' => $newsletterNameLike,
				'limit' => $limit,
				'offset' => $offset
		);
		return $this->MakeGetRequest ( $url, $params );
	}
	
	
	/**
	 * GetAllListsForEmailAddress
	 * Gets all subscriberid's, listid's for a particular email address and
	 * returns an array of them.
	 *
	 * @param String $emailaddress
	 *        	The email address to find on all of the lists.
	 * @param Array $listids
	 *        	The lists to check for the address on.
	 * @param Int $main_listid
	 *        	This is used for ordering the results of the query. When this
	 *        	is passed in, the main list should appear at the top.
	 * @param Boolean $activeonly
	 * 			Whether to only check for active subscribers or not.
	 * @param Boolean $include_deleted
	 *        	Whether to get the subscribers that are marked as deleted.
	 *        
	 * @return Array Returns either an empty array (if no email address is
	 *         passed in) or a multidimensional array containing both
	 *         subscriberid and listid.
	 */
	public function GetAllListsForEmailAddress ($emailaddress = false, $listids = array(), $main_listid = false, $activeonly = true,
			$include_deleted = false)
	{
		$url = $this->URL . '/Subscribers/GetAllListsForEmailAddress';
		if($emailaddress && !empty($listids))
		{
			$params = array(
					'emailaddress' => $emailaddress,
					'listids' => $listids,
					'main_listid' => $main_listid,
					'activeonly' => $activeonly,
					'include_deleted' => $include_deleted
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * CopyList
	 * Copy list details only along with custom field associations.
	 *
	 * @param Integer $listid
	 *        	Listid to copy.
	 *        
	 * @return Array Returns an array of status (whether the copy worked or not)
	 *         and a message to go with it. If the copy worked, then the message
	 *         is 'false'.
	 */
	public function CopyList($listid = false)
	{
		$url = $this->URL . '/Lists/CopyList';
		if($listid)
		{
			$params = array (
					'listid' => $listid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	
	/**
	 * ScheduleSendNewsletter
	 * Schedule newsletter campaign for sending.
	 *
	 * @param Integer $campaignid
	 *        	ID of the campain which need to be scheduled.
	 * @param Float $hours
	 * 			When should the campaign start
	 * 			(In how many hours from a starting point(real time : now)).
	 * 
	 * @return Array Returns an array of status (whether the copy worked or not)
	 *         and a message to go with it. If the copy worked, then the message
	 *         is 'false'.
	 */
	public function ScheduleSendNewsletter($campaignid = false, $hours = false, $saveSnapshots = true)
	{
		$url = $this->URL . '/Sends/ScheduleSend';
		if($campaignid)
		{
			$params = array(
					'campaignid' => $campaignid,
					'hours' => $hours,
			        'saveSnapshots' => $saveSnapshots
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ScheduleSendNewsletterToLists($newsletterid = false, $timeToSend = false, $listids = array())
	{
	    $url = $this->URL . '/Sends/ScheduleSendNewsletterToLists';
	    if($newsletterid && !empty($listids))
	    {
	        $params = array(
	            'newsletterid' => $newsletterid,
	            'timeToSend' => $timeToSend,
	            'listids' => $listids
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	public function ScheduleSendNewsletterToSegments($newsletterid = false, $timeToSend = false, $segmentids = array())
	{
	    $url = $this->URL . '/Sends/ScheduleSendNewsletterToSegments';
	    if($newsletterid && !empty($segmentids))
	    {
	        $params = array(
	            'newsletterid' => $newsletterid,
	            'timeToSend' => $timeToSend,
	            'segmentids' => $segmentids
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	/**
	 * ScheduleSendSMS
	 * Schedule SMS campaign for sending.
	 *
	 * @param Integer $campaignid
	 *        	ID of the campain which need to be scheduled.
	 * @param Float $hours
	 * 			When should the campaign start
	 * 			(In how many hours from a starting point(real time : now)).
	 * @param Array $lists
	 * 			Which lists to send.
	 * 
	 * @return Array Returns an array of status (whether the copy worked or not)
	 *         and a message to go with it. If the copy worked, then the message
	 *         is 'false'.
	 */
	public function ScheduleSendSMS($campaignid = false, $lists = false, $hours = false)
	{
		$url = $this->URL . '/SMSSends/ScheduleSend';
		if($campaignid && !empty($lists))
		{
			$params = array(
					'campaignid' => $campaignid,
					'lists' => $lists,
					'hours' => $hours
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	public function GetLatestStats($campaignID = false, $limit = 1)
	{
		$url = $this->URL . '/Stats/GetLatestStats';
		if($campaignID)
		{
			$params = array(
					'campaignid' => $campaignID,
					'limit' => $limit
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetOneToManySubscriberData($subscriberID = false, $fieldID = false)
	{
		$url = $this->URL . '/CustomFields/GetOneToManySubscriberData';
		if($subscriberID > 0 && $fieldID > 0)
		{
			$params = array(
					'subscriberID' => $subscriberID,
					'fieldID' => $fieldID
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetListSummary
	 * Calculates the total number of emails sent, bounces, unsubscribes, opens,
	 * forwards and link clicks for a list
	 *
	 * @param Int $listid
	 *        	The stat id of the entry you want to retrieve from the database.
	 *        
	 * @return Array Returns an array of the statistics
	 */
	public function GetListSummary ($listid = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Stats/GetListSummary';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
			        'limit' => $limit,
			        'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscribersUpdatedSince($date = false, $listid = false, $limit = 1000, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetSubscribersUpdatedSince';
		if($date)
		{
			$params = array(
					'date' => $date,
					'listid' => $listid,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
		
	
	public function GetSampleDataForOTM($fieldid)
	{
	    $url = $this->URL . '/Subscribers/GetSampleDataForOTM';
	    if($fieldid)
	    {
	        $params = array(
	            'fieldid' => $fieldid
	        );
	        return $this->MakeGetRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	public function GetTriggersForSegment($segmentid)
	{
		$url = $this->URL . '/Segments/GetTriggersForSegment';
		if($segmentid)
		{
			$params = array(
					'segmentid' => $segmentid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetTriggers($listid = false, $limit = 1000, $offset = 0)
	{
	    $url = $this->URL . '/Triggers/GetTriggers';
	    
	    $params = array(
	        'listid' => $listid,
	        'limit' => $limit,
	        'offset' => $offset
	    );
	    
	    return $this->MakeGetRequest($url, $params);
	}
	
	public function GetSegments($listid = false, $count_subscribers = false, $limit = 100, $offset = 0)
	{
	    $url = $this->URL . '/Segments/GetSegments';
	    
	    $params = array(
	        'listid' => $listid,
	        'count_subscribers' => $count_subscribers,
	        'limit' => $limit,
	        'offset' => $offset
	    );
	    
	    return $this->MakeGetRequest($url, $params);
	}
	
	public function ViewNewsletter($newsletterid)
	{
		$url = $this->URL . '/Newsletters/ViewNewsletter';
		if($newsletterid)
		{
			$params = array(
					'newsletterid' => $newsletterid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscribersFromSegment($segmentid = false, $countonly = false, $activeonly = true, $limit = 100, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetSubscribersFromSegment';
		if($segmentid)
		{
			$params = array(
					'segmentid' => $segmentid,
					'countonly' => $countonly,
					'activeonly' => $activeonly,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSegmentSummary($segmentid = false, $from = false, $to = false)
	{
		$url = $this->URL . '/Segments/GetSegmentSummary';
		if($segmentid)
		{
			$params = array(
					'segmentid' => $segmentid,
					'from' => $from,
					'to' => $to
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetNewsletterSummary($newsletterid = false, $statid = false, $from = false, $to = false)
	{
	    $url = $this->URL . '/Stats/GetNewsletterSummary';
	    if($newsletterid)
	    {
			$params = array (
					'newsletterid' => $newsletterid,
					'statid' => $statid,
					'from' => $from,
					'to' => $to
			);
			return $this->MakeGetRequest($url, $params);
		}
	    return self::REQUEST_FAILED;
	}
	
	public function GetRulesForSegment($segmentid = false)
	{
		$url = $this->URL . '/Segments/GetRulesForSegment';
		if($segmentid)
		{
			$params = array(
					'segmentid' => $segmentid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	public function EditNewsletter($newsletterid = false, $name = false, $subject = false)
	{
		$url = $this->URL . '/Newsletters/EditNewsletter';
		if($newsletterid && ($subject || $name))
		{
			$params = array(
					'newsletterid' => $newsletterid,
					'name' => $name,
					'subject' => $subject
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SetTriggerStatus($triggerid = false, $status = false)
	{
		$url = $this->URL . '/Triggers/SetTriggerStatus';
		if($triggerid)
		{
			$params = array(
					'triggerid' => $triggerid,
					'status' => $status
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SetAutoresponderStatus($autoresponderid = false, $status = false)
	{
		$url = $this->URL . '/Autoresponders/SetAutoresponderStatus';
		if($autoresponderid)
		{
			$params = array(
					'autoresponderid' => $autoresponderid,
					'status' => $status
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetTriggerSummary($triggerid = false, $from = false, $to = false)
	{
		$url = $this->URL . '/Triggers/GetTriggerSummary';
		if($triggerid)
		{
			$params = array(
					'triggerid' => $triggerid,
					'from' => $from,
					'to' => $to
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetAutoresponderSummary($autoresponderid = false, $from = false, $to = false)
	{
		$url = $this->URL . '/Autoresponders/GetAutoresponderSummary';
		if($autoresponderid)
		{
			$params = array(
					'autoresponderid' => $autoresponderid,
					'from' => $from,
					'to' => $to
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function AddToOTMDocument ($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $fieldid = false, 
	                                  $values = array(), $path = false)
	{
		$url = $this->URL . '/Subscribers/AddToOTMDocument';
		if($listid && ($subscriberid || $emailaddress || ($mobile && $mobilePrefix)) && $fieldid && !empty($values))
		{
			$params = array(
			        'listid' => $listid,
    			    'subscriberid' => $subscriberid,
    			    'emailaddress' => $emailaddress,
    			    'mobile' => $mobile,
    			    'mobilePrefix' => $mobilePrefix,
					'fieldid' => $fieldid,
					'path' => $path,
					'values' => $values
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSnapshots($subscriberid = false, $triggerid = false, $autoresponderid = false, $campaignid = false, $groupby = "date")
	{
	    $url = $this->URL . '/Stats/GetSnapshots';
	    if($subscriberid)
	    {
	        $params = array(
	            'subscriberid' => $subscriberid,
	            'triggerid' => $triggerid,
	            'autoresponderid' => $autoresponderid,
	            'campaignid' => $campaignid,
	            'groupby' => $groupby
	        );
	        return $this->MakeGetRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	
	public function GetStatids($listid = false, $segmentid = false, $newsletterid = false, $from = false, $to = false, $limit = 100, $offset = 0)
	{
	    $url = $this->URL . '/Stats/GetStatids';
	    if($listid || $segmentid || $newsletterid)
	    {
	        $params = array(
	            'listid' => $listid,
	            'segmentid' => $segmentid,
	            'newsletterid' => $newsletterid,
	            'from' => $from,
	            'to' => $to,
	            'limit' => $limit,
	            'offset' => $offset
	        );
	        return $this->MakeGetRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
	public function GetLeadScore($subscriberid = false)
	{
		$url = $this->URL . '/Subscribers/GetLeadScore';
		if($subscriberid)
		{
			$params = array (
					'subscriberid' => $subscriberid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SetLeadScore($subscriberid = false, $leadScore = false, $type = "add")
	{
		$url = $this->URL . '/Subscribers/SetLeadScore';
		if($subscriberid && $leadScore !== false)
		{
			$params = array (
					'subscriberid' => $subscriberid,
					'leadScore' => $leadScore,
					'type' => $type
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetTrackingEvents($listid = false, $subscriberid = false, $limit = 100, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetTrackingEvents';
		if($subscriberid && $listid)
		{
			$params = array(
					'listid' => $listid,
					'subscriberid' => $subscriberid,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetSentEmailCampaignEvents
	 * Fetch events for sent campaigns based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all sent campaigns between $form & $to.
	 */
	public function GetSentEmailCampaignEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetSentEmailCampaignEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetSentEmailCampaignWithTriggerEvents
	 * Fetch events for sent campaigns with trigger based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all sent campaigns between $form & $to.
	 */
	public function GetSentEmailCampaignWithTriggerEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetSentEmailCampaignWithTriggerEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	/**
	 * GetOpenCampaignEvents
	 * Fetch events for open campaigns based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all opened campaigns between $form & $to.
	 */
	public function GetOpenCampaignEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetOpenCampaignEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	/**
	 * GetOpenTriggersEvents
	 * Fetch events for open campaigns sent with trigger based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all opened campaigns sent with trigger between $form & $to.
	 */
	public function GetOpenTriggersEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetOpenTriggersEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}

	
	/**
	 * GetLinkClickCampaignEvents
	 * Fetch events for link click in campaigns based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all clicked links in campaigns between $form & $to.
	 */
	public function GetLinkClickCampaignEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetLinkClickCampaignEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	/**
	 * GetLinkClickTriggerEvents
	 * Fetch events for link click in campaigns sent with trigger based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all clicked links in campaigns sent with trigger between $form & $to.
	 */
	public function GetLinkClickTriggerEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetLinkClickTriggerEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	
	/**
	 * GetSentAutoresponderEvents
	 * Fetch events for sent autoresponders based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all sent autoresponders between $form & $to.
	 */
	public function GetSentAutoresponderEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetSentAutoresponderEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetOpenAutoresponderEvents
	 * Fetch events for opened autoresponders based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all opened autoresponders between $form & $to.
	 */
	public function GetOpenAutoresponderEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetOpenAutoresponderEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetLinkClickAutoresponderEvents
	 * Fetch events for link click in autoresponders based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all clicked links in campaigns between $form & $to.
	 */
	public function GetLinkClickAutoresponderEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetLinkClickAutoresponderEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	/**
	 * GetSentSMSCampaignEvents
	 * Fetch events for sent SMS campaigns based on user.
	 *
	 * @param String $from
	 *        	Event date.
	 * @param String $to
	 * 			Event date.
	 * @param Integer $limit
	 * 			How many events to fetch with single request.
	 *
	 * @return Array Returns an array of events for all sent SMS campaigns between $form & $to.
	 */
	public function GetSentSMSCampaignEvents ($from = false, $to = false, $limit = 10, $offset = 0)
	{
		$url = $this->URL . '/Events/GetSentSMSCampaignEvents';
		if(!empty($from))
		{
			$params = array(
					'from' => $from,
					'to' => $to,
					'limit' => $limit,
					'offset' => $offset
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function AddCustomFieldsToList($listid = false, $customFields = array())
	{
	    $url = $this->URL . '/Lists/AddCustomFieldsToList';
	    
	    if($listid && !empty($customFields))
	    {
	        $params = array (
	            'listid' => $listid,
	            'customFields' => $customFields
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    
	    return self::REQUEST_FAILED;
	}
	
	public function CreateSegment($name = "", $rules = array(), $connector = 'and')
	{
	    $url = $this->URL . '/Segments/CreateSegment';
	    if(!empty($name) && !empty($rules) && !empty($connector))
	    {
	        $params = array(
	            'name' => $name,
	            'rules' => $rules,
	            'connector' => $connector
	        );
	        return $this->MakePostRequest($url, $params);
	    }
	    return self::REQUEST_FAILED;
	}
	
}
