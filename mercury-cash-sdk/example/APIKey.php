<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MercuryCash\SDK\Adapter;
use MercuryCash\SDK\Auth\APIKey;
use MercuryCash\SDK\Endpoints\Transaction;

$public_key = '78d3427aa60f2b220a578d057e7d08b978dd8840b754311665422af0184f4399';
$secret_key = '7A469Gk2s1oaOQtwAJJM/TjwQ87pOjUcZILEIm45yag=';

$api_key = new APIKey($public_key, $secret_key);
$adapter = new Adapter($api_key, 'https://api-way.mercurydev.tk');
$endpoint = new Transaction($adapter);

$transaction = $endpoint->create([
    'email' => 'marco@mercury.cash',
    'crypto' => 'ETH',
    'fiat' => 'USD',
    'amount' => 3,
    'tip' => 1,
]);

$checkout = $endpoint->process($transaction->getUuid());

$status = $endpoint->status($checkout->getUuid());
