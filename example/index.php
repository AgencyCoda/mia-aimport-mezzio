<?php

use Mia\Aimport\Service\Aimport;

require '../vendor/autoload.php';

$service = new Aimport('api_key', 'api_secret');
var_dump($service->getPaymentInfo('tx_id'));
exit();