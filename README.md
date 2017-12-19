# cake-marketing-campaign-api
CAKE Marketing Campaign Summary API and SubID Summary API performance on a campaign-by-campaign basis.

```php
TODO: Pull the campaign summary API 
TODO: Pull the SubID Summary API 
```
Pull the campaign summary API which will provide the affiliate ID and offer ID for each campaign.
Documentation: https://support.getcake.com/solution/articles/13000003854-reports-campaignsummary-api-version-5

Next, pull the SubID Summary API to get the specific SubID performance on a campaign-by-campaign basis.
Documentation: https://support.getcake.com/solution/articles/5000688590-reports-subidsummary-api-version-1



#### Configuration
The **config.php** file has the following configurable parameters.
Email feature configurable parameters: *mail_from*, *mail_to*, *email_subject*
The *data_directory* is where the output files will be written to.
```php
$mailTo = 'recipient@example.com';
$mailFrom = 'data@example.com';
...
$dataDirectory = 'data';
$download_loc = 'http://download-loc-example.com/';
...
```

#### Verbose option
The verbose option echoes the current status as the API is running with start and end time attributes. Verbose option on or off is as simple as changing the debug variable to *true* or *false*.

```php
$debug = true;
```
