<?php declare(strict_types=1);

namespace USPS;

/**
 * Class TrackConfirm.
 */
class TrackConfirm extends USPSBase
{
    /**
     * @var string - the api version used for this type of call
     */
    protected string $apiVersion = 'TrackV2';
    /**
     * @var string - revision version for including additional response fields
     */
    protected string $revision = '';
    /**
     * @var string - User IP address. Required when TrackFieldRequest[Revision=’1’].
     */
    protected string $clientIp = '';
    /**
     * @var string - Internal User Identification. Required when TrackFieldRequest[Revision=’1’].
     */
    protected string $sourceId = '';
    /**
     * @var array - list of all packages added so far
     */
    protected array $packages = [];

    public function getEndpoint(): string
    {
        return self::$testMode ? 'https://production.shippingapis.com/ShippingAPITest.dll' : 'https://production.shippingapis.com/ShippingAPI.dll';
    }

    /**
     * Perform the API call.
     *
     * @return string
     */
    public function getTracking(): string
    {
        return $this->doRequest();
    }

    /**
     * returns array of all packages added so far.
     *
     * @return array
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
     * @return void
     */
    public function addPackage(string $id): void
    {
        $this->packages['TrackID'][] = ['@attributes' => ['ID' => $id]];
    }

    /**
     * Set the revision value
     *
     * @param string|int $value
     *
     * @return TrackConfirm
     */
    public function setRevision(string|int $value): self
    {
        $this->revision = (string)$value;

        return $this;
    }

    /**
     * Set the ClientIp value
     *
     * @param string $value
     *
     * @return TrackConfirm
     */
    public function setClientIp($value): self
    {
        $this->clientIp = (string)$value;

        return $this;
    }

    /**
     * Set the SourceId value
     *
     * @param string $value
     *
     * @return TrackConfirm
     */
    public function setSourceId($value): self
    {
        $this->sourceId = (string)$value;

        return $this;
    }
}
