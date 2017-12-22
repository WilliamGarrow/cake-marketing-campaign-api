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


$Client = new Client(['base_uri' => 'http://demo.cakemarketing.com/api/']);  // <YOUR BASE URI>

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
    'Sub ID Name',
    'Site Offer Name',
    'Site Offer ID',
    'Clicks',
    'Click Thru Percentage',
    'Macro Event Conversions',
    'Paid Macro Event Conversions',
    'Cost',
    'Average Cost',
    'Source Affiliate Manager ID',
//    'Brand Advertiser Manager ID',
];

fputcsv($csvHandle, $headerFields);

if ($debug) echo "Adorning SubIdSummary data to Campaigns" . PHP_EOL;
$iStart = microtime(true);

/*
 * Sub Id Summary Request Generator
 * https://support.getcake.com/support/solutions/articles/5000688590-reports-subidsummary-api-version-1
 *
 * Example Request: GET
 * http://demo.cakemarketing.com/api/1/reports.asmx/SubIDSummary?
 * api_key=dNJFmId9rI&start_date=2015-08-01&end_date=2015-09-01&
 * source_affiliate_id=1111667&site_offer_id=0&event_id=0&
 * revenue_filter=conversions_and_events
 */


$requestGenerator = function ($Campaigns, $startDate, $endDate) use ($debug) {

    foreach ($Campaigns as $index => $Campaign) {
        $uri = '1/reports.asmx/SubIDSummary?' . http_build_query([
                'api_key'               => API_KEY,
                'start_date'            => $startDate,
                'end_date'              => $endDate,
                'source_affiliate_id'   => $Campaign->SourceAffiliateID,
                'site_offer_id'         => $Campaign->SiteOfferID,
                'event_id'              => 0,
                'revenue_filter'        => 'conversions_and_events'
            ]);

        if ($debug) echo "API call for {$index}::{$Campaign->SourceAffiliateID}::{$Campaign->SiteOfferID}" . PHP_EOL;
        yield new Request('GET', $uri);
    }
};

$Pool = new Pool($Client, $requestGenerator($Campaigns, $startDate, $endDate), [
    'concurrency' => CONCURRENT_REQUESTS,
    'fulfilled' => function ($Response, $index) use ($Campaigns, $csvHandle) {
        $XML = new SimpleXMLElement((string)$Response->getBody());

        $Sub = $XML->sub_ids->sub_id_summary;
        $Campaign = $Campaigns[$index];

        if ($XML->row_count > 1) {
            for ($i = 0; $i < $XML->row_count; $i++) {
                $Sub = $XML->sub_ids->sub_id_summary[$i];
                if (trim($Sub->sub_id_name) == '') {
                    fputcsv($csvHandle, [
                        $Campaign->CampaignID,
                        $Campaign->SourceAffiliateID,
                        $Campaign->SourceAffiliateName,
                        (string)$Sub->sub_id,
                        'N/A',
                        $Campaign->site_offer_name,
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        $Campaign->account_manager_name,
                    ]);
                } else {
                    fputcsv($csvHandle, [
                        $Campaign->CampaignID,
                        $Campaign->SourceAffiliateID,
                        $Campaign->SourceAffiliateName,
                        (string)$Sub->sub_id,
                        (string)$Sub->sub_id_name,
                        $Campaign->site_offer_name,
                        (string)$Sub->site_offer_id,
                        (string)$Sub->clicks,
                        (string)$Sub->click_thru_percentage,
                        (string)$Sub->macro_event_conversions,
                        (string)$Sub->paid,
                        (string)$Sub->cost,
                        (string)$Sub->average_cost,
                        $Campaign->account_manager_name,
                    ]);
                }
            }
        } else {
            if (trim($Sub->sub_id_name) == '') {
                fputcsv($csvHandle, [
                    $Campaign->CampaignID,
                    $Campaign->SourceAffiliateID,
                    $Campaign->SourceAffiliateName,
                    (string)$Sub->sub_id,
                    'N/A',
                    $Campaign->site_offer_name,
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    $Campaign->account_manager_name,
                ]);
            } else {
                fputcsv($csvHandle, [
                    $Campaign->CampaignID,
                    $Campaign->SourceAffiliateID,
                    $Campaign->SourceAffiliateName,
                    (string)$Sub->sub_id,
                    (string)$Sub->sub_id_name,
                    $Campaign->site_offer_name,
                    (string)$Sub->site_offer_id,
                    (string)$Sub->clicks,
                    (string)$Sub->click_thru_percentage,
                    (string)$Sub->macro_event_conversions,
                    (string)$Sub->paid,
                    (string)$Sub->cost,
                    (string)$Sub->average_cost,
                    $Campaign->account_manager_name,
                ]);
            }
        }
    },
    'rejected' => function ($reason, $index) use ($debug) {
        if ($debug) echo "There was an error with index {$index}, reason: {$reason}";
    },
]);

$promise = $Pool->promise();
$promise->wait();

$iEnd = microtime(true);
if ($debug) echo "Completed Adorning in " . ($iEnd - $iStart) . ' seconds' . PHP_EOL;

fclose($csvHandle);

// Email csv - settings in config file
mail(
    $mailTo,
    $subject,
    "Your Data is ready for downloading at: {$download_loc}/{$fname}",
    "From: {$mailFrom}"
);

$timeEnd = microtime(true);
if ($debug) echo 'Completed in ' . ($timeEnd - $timeStart) . ' seconds' . PHP_EOL;


/*
 * TODO: Get all the Campaigns
 */
