<?php declare(strict_types=1);

namespace USPS;

/**
 * USPS Address Verify Class
 * used to verify an address is valid.
 *
 * @since  1.0
 */
class AddressVerify extends USPSAPIClient
{
    /**
     * @var string - the api version used for this type of call
     */
    protected string $apiVersion = 'Verify';
    /**
     * @var string - revision version for including additional response fields
     */
    protected string $revision = '';
    /**
     * @var array - list of all addresses added so far
     */
    protected array $addresses = [];

    /**
     * Perform the API call to verify the address.
     */
    public function verify(): string
    {
        return $this->doRequest();
    }

    /**
     * returns array of all addresses added so far.
     */
    public function getPostFields(): array
    {
        $postFields = !empty($this->revision) ? ['Revision' => $this->revision] : [];
        return array_merge($postFields, $this->addresses);
    }

    /**
     * Add Address to the stack.
     *
     * @return $this
     */
    public function addAddress(Address $data, string $id = null): static
    {
        $packageId = $id !== null ? $id : ((count($this->addresses) + 1));

        $this->addresses['Address'][] = array_merge(['_attributes' => ['ID' => $packageId]], $data->getAddressInfo());

        return $this;
    }

    /**
     * Set the revision value
     *
     * @return $this
     */
    public function setRevision(string|int $value): static
    {
        $this->revision = (string)$value;

        return $this;
    }
}
