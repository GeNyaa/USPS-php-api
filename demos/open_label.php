<?php

require_once('autoload.php');

// Initiate and set the username provided from usps
$label = (new \USPS\OpenDistributeLabel('xxxx'))
    ->setFromAddress(
        'John',
        'Doe',
        '',
        '5161 Lankershim Blvd',
        'North Hollywood',
        'CA',
        '91601',
        '# 204'
    )->setToAddress(
        'Vincent Gabriel',
        '5440 Tujunga Ave',
        'North Hollywood',
        'CA',
        '91601',
        '707'
    )->setWeightOunces(1);

$label->createLabel();

print_r($label->getPostData());

// See if it was successful
if ($label->isSuccess()) {
    echo 'Done';
    echo "\n Confirmation:".$label->getConfirmationNumber();

    $label = $label->getLabelContents();
    if ($label) {
        $contents = base64_decode($label);
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="label.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.strlen($contents));
        echo $contents;
        exit;
    }
} else {
    echo sprintf('Error: %s', $label->getErrorMessage());
}
