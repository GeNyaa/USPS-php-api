<?php declare(strict_types=1);

namespace USPS;

/**
 * Class FirstClassServiceStandards.
 */
class FirstClassServiceStandards extends USPSAPIClient
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'FirstClassMail';
    /**
     * @var array route added so far.
     */
    protected array $route = [];

    /**
     * Perform the API call.
     */
    public function getServiceStandard(): string
    {
        return $this->doRequest();
    }

    /**
     * returns array of all routes added so far.
     */
    public function getPostFields(): array
    {
        return $this->route;
    }

    /**
     * Add route to the stack.
     */
    public function addRoute(string|int $origin_zip, string|int $destination_zip): static
    {
        $this->route = [
            'OriginZip'      => $origin_zip,
            'DestinationZip' => $destination_zip,
        ];

        return $this;
    }
}
