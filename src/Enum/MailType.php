<?php declare(strict_types=1);

namespace USPS\Enum;

enum MailType: string
{
    case LETTER = 'LETTER';
    case FLAT = 'FLAT';
    case PARCEL = 'PARCEL';
    case POSTCARD = 'POSTCARD';
    case PACKAGE = 'PACKAGE';
    case PACKAGE_SERVICE = 'PACKAGE SERVICE';
}