<?php

require_once('autoload.php');

use USPS\RatePackage;

$rate = new \USPS\Rate('xxxx');
$rate->setInternationalCall(true);
$rate->addExtraOption('Revision', 2);

$package = (new RatePackage)
    ->setPounds(15.12345678)
    ->setOunces(0)
    ->setField('Machinable', 'True')
    ->setField('MailType', 'Package')
    ->setField('GXG', [
        'POBoxFlag' => 'Y',
        'GiftFlag' => 'Y'
    ])
    ->setField('ValueOfContents', 200)
    ->setField('Country', 'Australia')
    ->setField('Container', 'RECTANGULAR')
    ->setField('Size', 'LARGE')
    ->setField('Width', 10)
    ->setField('Length', 15)
    ->setField('Height', 10)
    ->setField('Girth', 0)
    ->setField('OriginZip', 18701)
    ->setField('CommercialFlag', 'N')
    ->setField('AcceptanceDateTime', '2016-07-05T13:15:00-06:00')
    ->setField('DestinationPostalCode', '2046');

// add the package to the rate stack
$rate->addPackage($package);
// Perform the request and print out the result
print_r($rate->getRate());
print_r($rate->getArrayResponse());
// Was the call successful
if ($rate->isSuccess()) {
    echo 'Done';
} else {
    echo sprintf('Error: %s', $rate->getErrorMessage());
}