<?php

date_default_timezone_set('America/New_York');

define('API_KEY', '<APIKEY>');
define('CONCURRENT_REQUESTS', 40);

include __DIR__ . '/config.php';

$options = getopt('', ['start:', 'end:']);

$startDate = empty($options['start'])
    ? date('Y-m-d', strtotime('-1 day'))    // default to yesterday
    : date('Y-m-d', strtotime($options['start']));
$endDate = empty($options['end'])
    ? date('Y-m-d')                              // default to today
    : date('Y-m-d', strtotime($options['end']));


require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

$timeStart = microtime(true);


$Client = new Client(['base_uri' => '<YOUR BASE URI>']);

if ($debug) echo "Fetching Campaigns" . PHP_EOL;
$Campaigns = getCampaigns($Client, $startDate, $endDate);
// $Campaigns = array_slice((array)$Campaigns, 0, 100);
if ($debug) echo 'Processing ' . count($Campaigns) . ' Campaigns' . PHP_EOL;


// https://support.getcake.com/support/solutions/articles/13000003854-reports-campaignsummary-api-version-5
$fname = "campaignData-{$startDate}.csv";
$filename = "{$dataDirectory}/{$fname}";
$csvHandle = fopen($filename, 'w');
$headerFields = [
    'Campaign ID',
    'Source Affiliate ID',
    'Source Affiliate Name',
    'Sub ID',
    'Site Offer Name',
    'Site Offer ID',
    'Clicks',
    'Click Thru Percentage',
    'Macro Event Conversions',
    'Paid Macro Event Conversions',
    'Cost',
    'Average Cost',
    'Source Affiliate Manager ID',
    'Brand Advertiser Manager ID',
];

fputcsv($csvHandle, $headerFields);

if ($debug) echo "Adorning SubIdSummary data to Campaigns" . PHP_EOL;
$iStart = microtime(true);


// Sub Id Summary Request Generator
$requestGenerator = function ($Campaigns, $startDate, $endDate) use ($debug) {

    foreach ($Campaigns as $index => $Campaign) {
        $uri = '<YOUR SubIDSummary URI>' . http_build_query([
                'api_key' => API_KEY,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'source_affiliate_id' => $Campaign->SourceAffiliateID,
                'site_offer_id' => $Campaign->SiteOfferID,
                'event_id' => 0,
            ]);

        if ($debug) echo "API call for {$index}::{$Campaign->SourceAffiliateID}::{$Campaign->SiteOfferID}" . PHP_EOL;
        yield new Request('GET', $uri);
    }
};
