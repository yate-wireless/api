<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 *
 * Usage example, need guzzlehttp/guzzle to work
 */

require __DIR__ . '/../vendor/autoload.php';

use Yate\Api\Api;
use Yate\Api\Config;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Client;

// Create Config object and fill it with Yate core API location and nodes served
$config = (new Config)
        ->withNode(
        ['hss', 'smsc', 'ucn'],
        'http://10.20.30.40/api.php',
        'verySecretKey');

// Guzzle factory provides both request and stream
$factory = new HttpFactory();

// Guzzle HTTP client is PSR-18 copatible
$client = new Client();

// Create Api instance, mind all above coud be done with DI containers
$api = new Api($config, $factory, $factory, $client);

// Just ask how many SIMs registeered in our HSS in total, omitting params
$result = $api->call('hss', 'get_sims');

// Returned response allow to use fields as attributes or as associative array items
echo "\nStored $result->count SIM-cards, yea, there {$result['count']} SIMs for sure!\n";

// And let's load some data
$result = $api->call('hss', 'get_subscriber', ['limit' => 100, 'brief' => true]);

echo "\nList of total " . count($result['subscribers']) . "\n";
foreach ($result->subscribers as $subscriber) {
    echo "{$subscriber['msisdn']} : {$subscriber['sim']['imsi']} : {$subscriber['sim']['iccid']}\n";
}
