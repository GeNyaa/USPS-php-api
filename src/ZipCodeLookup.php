<?php declare(strict_types=1);

namespace USPS;

class ZipCodeLookup extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'ZipCodeLookup';
    /**
     * @var array list of all addresses added so far
     */
    protected array $addresses = [];

    /**
     * Perform the API call.
     */
    public function lookup(): string
    {
        return $this->doRequest();
    }

    /**
     * returns array of all addresses added so far.
     */
    public function getPostFields(): array
    {
        return $this->addresses;
    }

    /**
     * Add Address to the stack.
     *
     * @param Address $data
     * @param string  $id   the address unique id
     *
     * @return $this
     */
    public function addAddress(Address $data, string $id = null): static
    {
        $packageId = $id !== null ? $id : ((count($this->addresses) + 1));

        $this->addresses['Address'][] = array_merge(['@attributes' => ['ID' => $packageId]], $data->getAddressInfo());

        return $this;
    }
}
