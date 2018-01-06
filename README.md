# CAKE Marketing Campaign Summary API
CAKE Marketing Campaign Summary API and SubID Summary API performance on a campaign-by-campaign basis.

#### Process
```
Pull the campaign summary API 
Example GET Request:
http://demo.cakemarketing.com/api/5/reports.asmx/CampaignSummary?
api_key=rYwtD48irQ0CiHRiuaB9abASO3e8O7GS&
start_date=01-01-2016%20&
end_date=01-31-2016%20&campaign_id=0%20&
source_affiliate_id=1111667%20&
subid_id=%20&
site_offer_id=0%20&source_affiliate_tag_id=0%20&
site_offer_tag_id=0%20&
source_affiliate_manager_id=0%20&
brand_advertiser_manager_id=0%20&
event_id=0%20&
event_type=macro_event_conversions

Pull the SubID Summary API 
Example GET Request:
http://demo.cakemarketing.com/api/1/reports.asmx/SubIDSummary?
api_key=dNJFmId9rI&
start_date=2015-08-01&
end_date=2015-09-01&
source_affiliate_id=1111667&
site_offer_id=0&
event_id=0&
revenue_filter=conversions_and_events
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

### API Usage
Call the **api.php** file with command line parameters:
Start date: YYYY-MM-DD  -  example: 2018-01-04
End date: YYYY-MM-DD  -  example: 2018-02-08

Command line example:
```sh
$ php api.php --start=2018-01-04 --end=2018-02-08
```
If you don't set a start and end date, it will default to run for yesterday.
Example:
```sh
$ php api.php
```

This application will generate a performance summary report from the Cake CampaignSummary API and the SubIDSummary API.

```php
    'Campaign ID',
    'Source Affiliate ID',
    'Source Affiliate Name',
    'Sub ID',
    'Sub ID Name',
    'Site Offer Name',
    'Site Offer ID',
    'Clicks',
    'Click Thru Percentage',
    'Macro Event Conversions',
    'Paid Macro Event Conversions',
    'Cost',
    'Average Cost',
    'Source Affiliate Manager ID'
    ...
```

#### Output
File output will be in the `data` folder
- **data/**
 	* **campaignData-2018-01-05.csv** : Example .csv file

Output example (truncated):


| Campaign ID | Source Affiliate ID | Source Affiliate Name | Sub ID | Sub ID Name | Site Offer ID | Clicks | Click Thru Percentage | Macro Event Conversions | Cost | Average Cost | Source Affiliate Manager ID |
|-------------|---------------------|-----------------------|--------|-------------|---------------|--------|-----------------------|-------------------------|------|--------------|-----------------------------|
| 4XQ-65      | 108455              | Avavee                | 6284WV | Konklab     | 3.8.7         | 191    | 72                    | 62                      | 6.15 | 1.81         | 054w5fs424                  |
| iZ6-65      | 716187              | Minyx                 | 5532WV | Fixflex     | 0.67          | 640    | 05                    | 87                      | 9.39 | 9.68         | 526y6ce180                  |
| yjC-64      | 349519              | Realcube              | 3850UA | Ventosanzap | 5.13          | 703    | 40                    | 00                      | 9.06 | 7.14         | 469j6tr906                  |
| Mh4-00      | 243817              | Demivee               | 0760JU | Temp        | 0.14          | 159    | 84                    | 25                      | 0.16 | 3.27         | 568b1qv626                  |
| W4u-88      | 769166              | Wikivu                | 5277ZG | Bigtax      | 0.6.0         | 104    | 87                    | 62                      | 9.01 | 5.33         | 675a1oe490                  |
| ...