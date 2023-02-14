<?php declare(strict_types=1);

namespace USPS;

/**
 * Class TrackConfirm.
 */
class TrackConfirm extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'TrackV2';
    /**
     * @var string revision version for including additional response fields
     */
    protected string $revision = '';
    /**
     * @var string User IP address. Required when TrackFieldRequest[Revision=’1’].
     */
    protected string $clientIp = '';
    /**
     * @var string Internal User Identification. Required when TrackFieldRequest[Revision=’1’].
     */
    protected string $sourceId = '';
    /**
     * @var array list of all packages added so far
     */
    protected array $packages = [];

    public function getEndpoint(): string
    {
        return self::$testMode ? 'https://production.shippingapis.com/ShippingAPITest.dll' : 'https://production.shippingapis.com/ShippingAPI.dll';
    }

    /**
     * Perform the API call.
     */
    public function getTracking(): string
    {
        return $this->doRequest();
    }

    /**
     * returns array of all packages added so far.
     */
    public function getPostFields(): array
    {
        $postFields = array();
        if ( !empty($this->revision) ) { $postFields['Revision'] = $this->revision; }
        if ( !empty($this->revision) ) { $postFields['ClientIp'] = $this->clientIp; }
        if ( !empty($this->revision) ) { $postFields['SourceId'] = $this->sourceId; }

        return array_merge($postFields, $this->packages);
    }

    /**
     * Add Package to the stack.
     *
     * @param string $id the address unique id
     *
     * @return $this
     */
    public function addPackage(string $id): static
    {
        $this->packages['TrackID'][] = ['@attributes' => ['ID' => $id]];

        return $this;
    }

    /**
     * Set the revision value
     *
     * @param string|int $value
     *
     * @return $this
     */
    public function setRevision(string|int $value): static
    {
        $this->revision = (string)$value;

        return $this;
    }

    /**
     * Set the ClientIp value
     *
     * @return $this
     */
    public function setClientIp(string $value): static
    {
        $this->clientIp = $value;

        return $this;
    }

    /**
     * Set the SourceId value
     *
     * @return $this
     */
    public function setSourceId(string $value): static
    {
        $this->sourceId = $value;

        return $this;
    }
}
