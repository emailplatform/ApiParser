<?php

class ApiParser
{

	const REQUEST_FAILED = 'Unsuccessful request';

	var $settings = array ();

	
	/** Production **/
  	var $URL = 'https://api.mailmailmail.net/v1.1';
	
	public function __construct ($settings = array())
	{
		$this->settings = $settings;
	}

	private function GetHTTPHeader ()
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
		$url = $this->settings["URL"] . "/CustomFields/LoadCustomField";
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
	public function AddSubscriberToList($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $contactFields = array(), $add_to_autoresponders = false, $skip_listcheck = false)
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
					'skip_listcheck' => $skip_listcheck
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
		if(!empty($listids))
		{
			$params = array (
					'listids' => $listids
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
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
	public function SendNewsletter($newsletterid = 0, $subscriberid = 0, $email = '', $senderEmail = '', $senderName = '', $replyEmail = '')
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
					'replyaddress' => $replyEmail
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
	
	public function RequestUpdateEmail($subscriberid = false, $listid = false, $oldemail = false, $newemail = false, $contactFields = array(), $source = false)
	{
		$url = $this->URL . '/Subscribers/RequestUpdateEmail';
		if($listid && $subscriberid && !empty($oldemail) && !empty($newemail))
		{
			$params = array(
					'subscriberid' => $subscriberid,
					'listid' => $listid,
					'oldemail' => $oldemail,
					'newemail' => $newemail,
					'contactFields' => $contactFields,
					'source' => $source
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
			$content = true, $aftercreatedate = false, $newsletterNameLike = false)
	{
		$url = $this->URL . '/Newsletters/GetNewsletters';
		
		$params = array (
				'countOnly' => $countOnly,
				'getLastSentDetails' => $getLastSentDetails,
				'content' => $content,
				'aftercreatedate' => $aftercreatedate,
				'newsletterNameLike' => $newsletterNameLike 
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
	public function ScheduleSendNewsletter($campaignid = false, $hours = false)
	{
		$url = $this->URL . '/Sends/ScheduleSend';
		if($campaignid)
		{
			$params = array(
					'campaignid' => $campaignid,
					'hours' => $hours
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
	public function GetListSummary ($listid = false)
	{
		$url = $this->URL . '/Stats/GetListSummary';
		if($listid)
		{
			$params = array(
					'listid' => $listid
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
	
	function GetSubscribers_V2($searchinfo = array(), $countonly = false, $limit = 1000, $offset = 0)
	{
		$url = $this->URL . '/Subscribers/GetSubscribers_V2';
		
		$params = array (
				'searchinfo' => $searchinfo,
				'countonly' => $countonly,
				'limit' => $limit,
				'offset' => $offset
		);
		return $this->MakeGetRequest($url, $params);
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
	
	
}
