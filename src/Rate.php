<?php declare(strict_types=1);

namespace USPS;

/**
 * USPS Rate calculator class
 * used to get a rate for shipping methods.
 *
 * @since  1.0
 */
class Rate extends USPSBase
{
    /**
     * @var string - the api version used for this type of call
     */
    protected string $apiVersion = 'RateV4';
    /**
     * @var array - list of all addresses added so far
     */
    protected array $packages = [];

    /**
     * Perform the API call.
     */
    public function getRate(int $timeout = 60): string
    {
        return $this->doRequest(null, $timeout);
    }

    /**
     * returns array of all packages added so far.
     */
    public function getPostFields(): array
    {
        return $this->packages;
    }

    /**
     * sets the type of call to perform domestic or international.
     */
    public function setInternationalCall(bool $status): array
    {
        $this->apiVersion = $status === true ? 'IntlRateV2' : 'RateV4';
    }

    /**
     * Add other option for International & Insurance.
     */
    public function addExtraOption(string|int $key, mixed $value): static
    {
        $this->packages[$key][] = $value;

        return $this;
    }

    /**
     * Add Package to the stack.
     *
     * @param string|null $id the address unique id
     */
    public function addPackage(RatePackage $data, string $id = null): static
    {
        $packageId = $id !== null ? $id : ((count($this->packages) + 1));

        $this->packages['Package'][] = array_merge(['@attributes' => ['ID' => $packageId]], $data->getPackageInfo());

        return $this;
    }
}
