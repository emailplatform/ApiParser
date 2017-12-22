<?php

class ApiParser
{

	const REQUEST_FAILED = 'Unsuccessful request';

	var $settings = array ();

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
		// If two arrays merged, URL is invalid
		$data = array_merge($fields, $this->settings);
		if(!empty($data))
		{
			$url .= "?" . http_build_query($data, '', '&');
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
			$data = array_merge($fields, $this->settings);
			$encodedData = http_build_query($data, '', '&');
			
			// set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, count($data));
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
		
		// add the setting to the fields
		$data = array_merge($fields, $this->settings);
		$encodedData = http_build_query($data, '', '&');
		
		// set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, count($data));
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
		return 'All requirements work correctly';
	}
	
	public function TestUserToken()
	{
		$url = $this->settings["URL"] . "/Test/TestUserToken";
		return $this->MakePostRequest($url);
	}
	
	//testing methods end
	

	public function IsSubscriberOnList ($listids = array(), $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $activeonly = false, 
			$not_bounced = false, $return_listid = false)
	{
		$url = $this->settings["URL"] . "/Subscribers/IsSubscriberOnList";
		$emailaddress = trim($emailaddress);
		$mobile = trim($mobile);
		if(!empty($listids) && ($emailaddress || $mobile || $subscriberid))
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

	public function IsUnSubscriber ($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $service = false)
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
			return $this->MakeGetRequest($url, $params);
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
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}


	public function UnsubscribeSubscriberEmail ($emailaddress = false, $listid = false, $subscriberid = false, $skipcheck = false, 
			$statstype = false, $statid = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/UnsubscribeSubscriberEmail';
		if(($listid && $emailaddress) || $subscriberid)
		{
			$params = array (
					'emailaddress' => $emailaddress, 
					'listid' => $listid, 
					'subscriberid' => $subscriberid, 
					'skipcheck' => $skipcheck, 
					'statstype' => $statstype, 
					'statid' => $statid
			);
			return $this->MakePostRequest($url, $params);
		}
	}

	public function UnsubscribeSubscriberMobile ($mobile = false, $listid = false, $subscriberid = false, $skipcheck = false, 
			$statstype = false, $statid = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/UnsubscribeSubscriberMobile';
		if(($listid && $mobile) || $subscriberid)
		{
			$params = array (
					'mobile' => $mobile, 
					'listid' => $listid, 
					'subscriberid' => $subscriberid, 
					'skipcheck' => $skipcheck, 
					'statstype' => $statstype, 
					'statid' => $statid
			);
			return $this->MakePostRequest($url, $params);
		}
	}

	public function ActivateSubscriber ($lists = false, $emailaddress = false, $mobile = false, $mobile_prefix = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/ActivateSubscriber';
		if(($emailaddress || $mobile) && $lists)
		{
			$params = array (
					'lists' => $lists,
					'emailaddress' => $emailaddress,
					'mobile' => $mobile,
					'mobile_prefix' => $mobile_prefix
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function AddSubscriberToList($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $contactFields = array(), $add_to_autoresponders = false, $skip_listcheck = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/AddSubscriberToList';
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
	
	public function GetLists_Users ()
	{
		$url = $this->settings['URL'] . '/Users/GetLists';
		$params = array ();
		return $this->MakeGetRequest($url, $params);
	}
	
	public function Create_List($listName = false, $descriptiveName = false, $mobile_prefix = false, $contact_fields = array())
	{
		$url = $this->settings['URL'] . '/Lists/CreateList';
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
	
	public function Update_List($listid = false, $listName = false, $descriptiveName = false, $mobile_prefix = false)
	{
		$url = $this->settings['URL'] . '/Lists/UpdateList';
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
	
	public function Delete_List($listid = false)
	{
		$url = $this->settings['URL'] . '/Lists/DeleteList';
		if($listid)
		{
			$params = array (
					'listid' => $listid
			);
			return $this->MakeDeleteRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetCustomFields($listids = false)
	{
		$url = $this->settings['URL'] . '/Lists/GetCustomFields';
		if(!empty($listids))
		{
			$params = array (
					'listids' => $listids
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetSubscribers ($searchinfo = array(), $countonly = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetSubscribers';
		
		$params = array (
				'searchinfo' => $searchinfo,
				'countonly' => $countonly
		);		
		return $this->MakeGetRequest($url, $params);	
	}
	
	public function GetSubscriberDetails($emailaddress = false, $listid = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/GetSubscriberDetails';
		if($emailaddress && $listid)
		{
			$params = array(
					'emailaddress' => $emailaddress,
					'listid' => $listid
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SaveSubscriberCustomField($subscriberids = false, $fieldid = false, $value = false, $skipEmptyData = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/SaveSubscriberCustomField';
		if($subscriberids && $fieldid)
		{
			$params = array(
					'subscriberids' => $subscriberids,
					'fieldid' => $fieldid,
					'skipEmptyData' => $skipEmptyData,
					'value' => $value
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function SaveSubscriberCustomFieldByList($listid = false, $fieldid = false, $data = false, $searchinfo = false)
	{
		$url = $this->settings['URL'] . '/Subscribers/SaveSubscriberCustomFieldByList';
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
		
	public function LoadSubscriberCustomFields($subscriberid = false, $listid = false, $customfields = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/LoadSubscriberCustomFields';
		if($subscriberid && $listid)
		{
			$params = array(
				'subscriberid' => $subscriberid,
				'listid' => $listid,
				'customfields' =>$customfields
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function Delete_Subscriber($listid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $subscriberid = false)
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
			return $this->MakeDeleteRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function Update_Subscriber($subscriberid = false, $emailaddress = false, $mobile = false, $listid = false, $customfields = array())
	{
		$url = $this->settings['URL'] . '/Subscribers/UpdateSubscriber';
		if($subscriberid || (($emailaddress || $mobile) && $listid))
		{
			$params = array(
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
	
	public function FetchStats($statid = false, $statstype = false)
	{
		$url = $this->settings['URL'] . '/Stats/FetchStats';
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
	
	public function GetBouncesByList($listid = false, $start = false, $perpage = false, $bounce_type = false, $calendar_restrictions = false, 
			$count_only = false)
	{
		$url = $this->settings['URL'] . '/Stats/GetBouncesByList';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
					'start' => $start,
					'perpage' => $perpage,
					'bounce_type' => $bounce_type,
					'calendar_restrictions' => $calendar_restrictions,
					'count_only' => $count_only
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetUnsubscribesByList($listid = false, $start = false, $perpage = false, $calendar_restrictions = false, $count_only = false)
	{
		$url = $this->settings['URL'] . '/Stats/GetUnsubscribesByList';
		if($listid)
		{
			$params = array(
					'listid' => $listid,
					'start' => $start,
					'perpage' => $perpage,
					'calendar_restrictions' => $calendar_restrictions,
					'count_only' => $count_only
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetOpens($statid = false, $count_only = false, $only_unique = false)
	{
		$url = $this->settings['URL'] . '/Stats/GetOpens';
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
	
	public function GetRecipients($statid = false, $stats_type = false, $count_only = false)
	{
		$url = $this->settings['URL'] . '/Stats/GetRecipients';
		if($statid)
		{
			$params = array (
					'statid' => $statid, 
					'stats_type' => $stats_type,
					'count_only' => $count_only
			);
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function Copy_Newsletter($oldid = false, $name = false)
	{
		$url = $this->settings['URL'] . '/Newsletters/Copy_Newsletter';
		if($oldid)
		{
			$params = array(
					'oldid' => $oldid,
					'name' => $name
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function Update_Newsletter()
	{
		$url = $this->settings['URL'] . '/Newsletters/Update_Newsletter';
		$params = array();
		return $this->MakePostRequest($url, $params);
	}
	
	public function GetNewsletters($countOnly= false, $getLastSentDetails = false, 
			$content = true, $aftercreatedate = false, $newsletterNameLike = false)
	{
		$url = $this->settings ['URL'] . '/Newsletters/GetNewsletters';
		
		$params = array (
				'countOnly' => $countOnly,
				'getLastSentDetails' => $getLastSentDetails,
				'content' => $content,
				'aftercreatedate' => $aftercreatedate,
				'newsletterNameLike' => $newsletterNameLike 
		);
		return $this->MakeGetRequest ( $url, $params );
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
			return $this->MakeGetRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function CopyList($listid = false)
	{
		$url = $this->settings['URL'] . '/Lists/CopyList';
		if($listid)
		{
			$params = array (
					'listid' => $listid
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ScheduleSendNewsletter($campaignid = false, $hours = false, $sendingdetails = array())
	{
		$url = $this->settings['URL'] . '/Sends/ScheduleSend';
		if($campaignid)
		{
			$params = array(
					'campaignid' => $campaignid,
					'hours' => $hours,
					'sendingdetails' => $sendingdetails
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function ScheduleSendSMS($campaignid = false, $hours = false, $lists = false, $segments = false, $sendingdetails = array())
	{
		$url = $this->settings['URL'] . '/SMSSends/ScheduleSend';
		if($campaignid)
		{
			$params = array(
					'campaignid' => $campaignid,
					'hours' => $hours,
					'lists' => $lists,
					'segments' => $segments,
					'sendingdetails' => $sendingdetails
			);
			return $this->MakePostRequest($url, $params);
		}
		return self::REQUEST_FAILED;
	}
	
	public function GetLatestStats($campaignID = false, $limit = 1)
	{
		$url = $this->settings['URL'] . '/Stats/GetLatestStats';
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
		$url = $this->settings['URL'] . '/CustomFields/GetOneToManySubscriberData';
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
}