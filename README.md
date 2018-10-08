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
$newsletterid = 1;
$parser->ViewNewsletter($newsletterid);
```
<hr><br />

## Changelog
### _Differences between **v1.1** and **v1.1.9**_ 
<br/>


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
<br />
