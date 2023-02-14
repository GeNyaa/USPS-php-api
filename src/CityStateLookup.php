<?php declare(strict_types=1);

namespace USPS;

/*
 * USPS City/State lookup
 * used to find a city/state by a zipcode lookup
 * @since 1.0
 */
class CityStateLookup extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'CityStateLookup';
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
     * Add zip zip code to the stack.
     *
     * @return $this
     */
    public function addZipCode(string $zip5, string $zip4 = '', string $id = null): static
    {
        $packageId = $id !== null ? $id : ((count($this->addresses) + 1));
        $zipCodes = ['Zip5' => $zip5];
        if ($zip4) {
            $zipCodes['Zip4'] = $zip4;
        }
        $this->addresses['ZipCode'][] = array_merge(['@attributes' => ['ID' => $packageId]], $zipCodes);

        return $this;
    }
}
