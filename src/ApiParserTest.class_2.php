<?php

class ApiParser
{

	const REQUEST_FAILED = 'Unsuccessful request';

	var $settings = array ();
	
	/** Localhost **/
//   	var $URL = 'http://localhost/public_api';

	/** Dev **/
  	//var $URL = 'https://api-dev.mailmailmail.net/development/';
	
	/** Bugs **/
	//var $URL = 'https://api-dev.mailmailmail.net/debuging';

	/** Staging **/
  	//var $URL = 'https://api-dev.mailmailmail.net/';

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
	
			return $this->MakeDeleteRequest($url, $params);
		}
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
	    
	    //dwqohdqodhqoiwdhoqwidoqwhdoqwohdqowidhoqhd
		
	
}
