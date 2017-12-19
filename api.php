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
if ($debug) echo 'Processing ' . count($Campaigns). ' Campaigns' . PHP_EOL;