<?php declare(strict_types=1);

namespace USPS;

use USPS\Enum\MailType;
use USPS\Enum\ServiceType;

/**
 * USPS Rate Package
 * used by the ups rate class to create packages represented as objects.
 *
 * @since  1.0
 */
class RatePackage extends Rate
{
    /**
     * @var array - list of all packages added so far
     */
    protected array $packageInfo = [];
    /**
     * First class mail type
     * required when you use one of the first class services.
     */

    /**
     * Container constants.
     */
    public const CONTAINER_VARIABLE = 'VARIABLE';
    public const CONTAINER_FLAT_RATE_ENVELOPE = 'FLAT RATE ENVELOPE';
    const CONTAINER_PADDED_FLAT_RATE_ENVELOPE = 'PADDED FLAT RATE ENVELOPE';
    const CONTAINER_LEGAL_FLAT_RATE_ENVELOPE = 'LEGAL FLAT RATE ENVELOPE';
    const CONTAINER_SM_FLAT_RATE_ENVELOPE = 'SM FLAT RATE ENVELOPE';
    const CONTAINER_WINDOW_FLAT_RATE_ENVELOPE = 'WINDOW FLAT RATE ENVELOPE';
    const CONTAINER_GIFT_CARD_FLAT_RATE_ENVELOPE = 'GIFT CARD FLAT RATE ENVELOPE';
    const CONTAINER_FLAT_RATE_BOX = 'FLAT RATE BOX';
    const CONTAINER_SM_FLAT_RATE_BOX = 'SM FLAT RATE BOX';
    const CONTAINER_MD_FLAT_RATE_BOX = 'MD FLAT RATE BOX';
    const CONTAINER_LG_FLAT_RATE_BOX = 'LG FLAT RATE BOX';
    const CONTAINER_REGIONALRATEBOXA = 'REGIONALRATEBOXA';
    const CONTAINER_REGIONALRATEBOXB = 'REGIONALRATEBOXB';
    const CONTAINER_RECTANGULAR = 'RECTANGULAR';
    const CONTAINER_NONRECTANGULAR = 'NONRECTANGULAR';
    /**
     * Size constants.
     */
    const SIZE_LARGE = 'LARGE';
    const SIZE_REGULAR = 'REGULAR';

    /**
     * Set the service property.
     *
     * @return $this
     */
    public function setService(ServiceType $value): static
    {
        return $this->setField('Service', $value->value);
    }

    /**
     * Set the first class mail type property.
     *
     * @return $this
     */
    public function setFirstClassMailType(MailType $value): static
    {
        return $this->setField('FirstClassMailType', $value->value);
    }

    /**
     * Set the zip origin property.
     *
     * @return $this
     */
    public function setZipOrigination(string|int $value): static
    {
        return $this->setField('ZipOrigination', $value);
    }

    /**
     * Set the zip destination property.
     *
     * @return $this
     */
    public function setZipDestination(string|int $value): static
    {
        return $this->setField('ZipDestination', $value);
    }

    /**
     * Set the pounds property.
     *
     * @return $this
     */
    public function setPounds(string|int $value): static
    {
        return $this->setField('Pounds', $value);
    }

    /**
     * Set the ounces property.
     *
     * @return $this
     */
    public function setOunces(string|int $value): static
    {
        return $this->setField('Ounces', $value);
    }

    /**
     * Set the container property.
     *
     * @return $this
     */
    public function setContainer(string|int $value): static
    {
        return $this->setField('Container', $value);
    }

    /**
     * Set the size property.
     *
     * @return $this
     */
    public function setSize(string|int $value): static
    {
        return $this->setField('Size', $value);
    }

    /**
     * Add an element to the stack.
     *
     * @return $this
     */
    public function setField(string|int $key, mixed $value): static
    {
        $this->packageInfo[ucwords($key)] = $value;

        return $this;
    }

    /**
     * Returns a list of all the info we gathered so far in the current package object.
     */
    public function getPackageInfo(): array
    {
        return $this->packageInfo;
    }
}
