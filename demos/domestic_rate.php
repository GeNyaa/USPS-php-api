<?php

require_once('autoload.php');

use USPS\Enum\MailType;
use USPS\Enum\ServiceType;
use USPS\RatePackage;

// Initiate and set the username provided from usps
$rate = new \USPS\Rate('xxxx');

// During test mode this seems not to always work as expected
//$rate->setTestMode(true);

// Create new package object and assign the properties
// apparently the order you assign them is important so make sure
// to set them as the example below
// set the RatePackage for more info about the constants
$package = (new RatePackage())
    ->setService(ServiceType::FIRST_CLASS)
    ->setFirstClassMailType(MailType::LETTER)
    ->setZipOrigination(91601)
    ->setZipDestination(91730)
    ->setPounds(0)
    ->setOunces(3.5)
    ->setContainer('')
    ->setSize(RatePackage::SIZE_REGULAR)
    ->setField('Machinable', true);

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
