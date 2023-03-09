<?php declare(strict_types=1);

namespace USPS;

class Address
{
    /**
     * @var array list of all key=>value pairs we added so far for the current address
     */
    protected array $addressInfo = [];

    /**
     * Set the address2 property.
     *
     * @return $this
     */
    public function setAddress(string|int $value): static
    {
        return $this->setField('Address2', $value);
    }

    /**
     * Set the address2 property.
     *
     * @return $this
     */
    public function setAddress2(string|int $value): static
    {
        return $this->setField('Address2', $value);
    }

    /**
     * Set the address1 property usually the apt or suit number.
     *
     * @return $this
     */
    public function setApt(string|int $value): static
    {
        return $this->setAddress1($value);
    }

    public function setAddress1(string|int $value): static
    {
        return $this->setField('Address1', $value);
    }

    /**
     * Set the city property.
     *
     * @return $this
     */
    public function setCity(string|int $value): static
    {
        return $this->setField('City', $value);
    }

    /**
     * Set the state property.
     *
     * @return $this
     */
    public function setState(string|int $value): static
    {
        return $this->setField('State', $value);
    }

    /**
     * Set the zip4 property - zip code value represented by 4 integers.
     *
     * @return $this
     */
    public function setZip4(string|int $value): static
    {
        return $this->setField('Zip4', $value);
    }

    /**
     * Set the zip5 property - zip code value represented by 5 integers.
     *
     * @return $this
     */
    public function setZip5(string|int $value): static
    {
        return $this->setField('Zip5', $value);
    }

    /**
     * @return $this
     */
    public function setZipcode(string|int $value): static
    {
        return $this->setZip5($value);
    }

    /**
     * Set the firm name property.
     *
     * @return $this
     */
    public function setFirmName(string|int $value): static
    {
        return $this->setField('FirmName', $value);
    }

    /**
     * Add an element to the stack.
     *
     * @return $this
     */
    public function setField(string|int $key, string|int $value): static
    {
        $this->addressInfo[ucwords($key)] = $value;

        return $this;
    }

    /**
     * Returns a list of all the info we gathered so far in the current address object.
     */
    public function getAddressInfo(): array
    {
        return $this->addressInfo;
    }
}
