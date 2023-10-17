# ApiParser
PHP class for using our company's API as part of the subscription.
<hr><br/>

## Installation
Run following command in terminal from the root of your project:
```bash
composer require emailplatform/api_parser
```
You can load dependencies by adding these lines to your code: 
```php
require_once 'vendor/emailplatform/api_parser/src/settings.php';
require_once 'vendor/emailplatform/api_parser/src/ApiParser.class.php';
```
<hr><br />

## How to use
1. Set up your API credentials (apiusername & apitoken) into **settings.php**
2. Create instance from **ApiParser.class.php**
```php
$parser = new ApiParser($settings);
```
3. Call method from ApiParser
```php
$listid = 56;
$count_subscribers = true;
$limit = 10;

$result = $parser->GetSegments($listid, $count_subscribers, $limit);
```
<hr><br/>

## Changelog:
### _Differences between **v1.2.1** and **v1.2.2**_ 
public function ChangeMobile($listid = false, $subscriberid = false, $mobile = false, $mobilePrefix = false)
Parameter Mobile is not longer required.


<br/>

### _Differences between **v1.1.11** and **v1.2.1**_ 
#### New methods:

* **AddToOTMDocument**
>  *Definition:*
> ```php
> public function AddToOTMDocument ($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $fieldid = false, $values = array(), $path = false)
> 
>```
<br/>

* **GetSubscribersByCustomField**
>  *Definition:*
> ```php
> public function GetSubscribersByCustomField ($listid = false, $data = array(), $activeonly = true, $countonly = false, $limit = 1000, $offset = 0)
> 
>```
<br/>


### _Differences between **v1.1.10** and **v1.1.11**_ 
#### New methods:

* **GetTriggerSummary**
>  *Definition:*
> ```php
> public function GetTriggerSummary($triggerid = false, $from = false, $to = false)
> 
>```
<br/>

* **GetAutoresponderSummary**
>  *Definition:*
> ```php
> public function GetAutoresponderSummary($autoresponderid = false, $from = false, $to = false)
> 
>```
<br/>


### _Differences between **v1.1.9** and **v1.1.10**_ 
#### New methods:

* **GetSegmentSummary**
>  *Definition:*
> ```php
> public function GetSegmentSummary($segmentid = false, $from = false, $to = false)
> 
>```
<br/>

* **GetRulesForSegment**
>  *Definition:*
> ```php
> public function GetRulesForSegment($segmentid = false)
> 
>```
<br/>

* **EditNewsletter**
>  *Definition:*
> ```php
> public function EditNewsletter($newsletterid = false, $name = false, $subject = false)
> 
>```
<br/>

* **SetTriggerStatus**
>  *Definition:*
> ```php
> public function SetTriggerStatus($triggerid = false, $status = false)
> 
>```
<br/>

* **SetAutoresponderStatus**
>  *Definition:*
> ```php
> public function SetAutoresponderStatus($autoresponderid = false, $status = false)
> 
>```
<br/>

### _Differences between **v1.1** and **v1.1.9**_ 
#### New methods:

* **SendSMS**
>  *Definition:*
> ```php
> public function SendSMS($campaignid = 0, $subject = '', $text = '', $subscriberid = 0, $listid = 0, $mobile = '', $mobilePrefix = '')
> 
>```
<br/>

* **GetSubscribersFromSegment**
>  *Definition:*
> ```php
> public function GetSubscribersFromSegment($segmentid = false, $countonly = false, $activeonly = true, $limit = 100, $offset = 0)
> 
>```
<br/>

* **GetTriggersForSegment**
>  *Definition:*
> ```php
> public function GetTriggersForSegment($segmentid)
> 
>```
<br/>

* **ViewNewsletter**
>  *Definition:*
> ```php
> public function ViewNewsletter($newsletterid)
> 
>```
<br/>

#### Method definition changed:

* **GetNewsletters**
>  *Previous:*
> ```php
> public function GetNewsletters($countOnly= false, $getLastSentDetails = false, $content = true, $aftercreatedate = false, $newsletterNameLike = false)
>```
>  *Now:*
> ```php
> public function GetNewsletters($countOnly= false, $getLastSentDetails = false, $content = true, $aftercreatedate = false, $newsletterNameLike = false, $limit = false, $offset = false)
>```
> * **Added:** $limit & $offset.
<hr><br/>

### _Differences between **v1.0** and **v1.1**_ 

#### Renamed methods:
| Old name [v1.0] | New name [v1.1]|
| ------ | ------ |
| Create_List | CreateList |
| Update_List | UpdateList |
| Delete_List | DeleteList |
| Update_Subscriber | UpdateSubscriber |
| Copy_Newsletter | CopyNewsletter |

<br/>

#### Method definition changed:

* **UnsubscribeSubscriberEmail**
>  *Previous:*
> ```php
> public function UnsubscribeSubscriberEmail ($emailaddress = false, $listid = false, $subscriberid = false, $skipcheck = false, $statid = false)
>```
>
>  *Now:*
> ```php
> public function UnsubscribeSubscriberEmail ($listid = false, $emailaddress = false, $subscriberid = false, $skipcheck = false, $statid = false)
>```
<br />

* **UnsubscribeSubscriberMobile**
>  *Previous:*
> ```php
> public function UnsubscribeSubscriberMobile ($mobile = false, $mobilePrefix = false, $listid = false, $subscriberid = false, $skipcheck = false, $statid = false)
>```
>
>  *Now:*
> ```php
> public function UnsubscribeSubscriberMobile ($listid = false, $mobile = false, $mobilePrefix = false, $subscriberid = false, $skipcheck = false, $statid = false)
> 
>```
<br />

* **GetSubscriberDetails**
>  *Previous:*
> ```php
> public function GetSubscriberDetails($emailaddress = false, $listid = false)
> 
>```
>
>  *Now:*
> ```php
> public function GetSubscriberDetails($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobile_prefix = false)
> 
>```
> * **Added:** subscriberid, mobile and mobile_prefix.
<br />


* **GetRecipients**
>  *Previous:*
> ```php
> public function GetRecipients($statid = false, $stats_type = false, $count_only = false)
> 
>```
>
>  *Now:*
> ```php
> public function GetRecipients($statid = false, $count_only = false)
> 
>```
> * **Removed:** stats_type.
<br />

* **ActivateSubscriber**
>  *Previous:*
> ```php
> public function ActivateSubscriber ($service = false, $lists = false, $emailaddress = false, $mobile = false, $mobile_prefix = false)
> 
>```
>
>  *Now:*
> ```php
> public function ActivateSubscriber ($service = false, $listid = false, $emailaddress = false, $mobile = false, $mobile_prefix = false, $subscriberid = false)
> 
>```
> * **Added:** subscriberid.
<br />

* **UpdateSubscriber**
>  *Previous:*
> ```php
> public function UpdateSubscriber($subscriberid = false, $emailaddress = false, $mobile = false, $listid = false, $customfields = array())
> 
>```
>
>  *Now:*
> ```php
> public function UpdateSubscriber($listid = false, $subscriberid = false, $emailaddress = false, $mobile = false, $mobilePrefix = false, $customfields = array())
> 
>```
> * **Added:** mobile, mobilePrefix.
<br />

* **ScheduleSendSMS**
>  *Previous:*
> ```php
> public function ScheduleSendSMS($campaignid = false, $hours = false, $lists = false)
> 
>```
>
>  *Now:*
> ```php
> public function ScheduleSendSMS($campaignid = false, $lists = false, $hours = false)
> 
>```

