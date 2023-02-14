<?php declare(strict_types=1);

namespace USPS;

/**
 * Class ServiceDeliveryCalculator.
 */
class ServiceDeliveryCalculator extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'SDCGetLocations';
    /**
     * @var array route added so far.
     */
    protected array $route = [];

    /**
     * Perform the API call.
     */
    public function getServiceDeliveryCalculation(): string
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
     * @param int $mailClass      integer from 0 to 6 indicating the class of mail.
     *                              “0” = All Mail Classes
     *                              “1” = Express Mail
     *                              “2” = Priority Mail
     *                              “3” = First Class Mail
     *                              “4” = Standard Mail
     *                              “5” = Periodicals
     *                              “6” = Package Services
     * @param string $originZip 5 digit zip code.
     * @param string $destinationZip 5 digit zip code.
     * @param string|null $acceptDate string in the format dd-mmm-yyyy.
     * @param string|null $acceptTime string in the format HHMM.
     *
     * @return $this
     */
    public function addRoute(int $mailClass, string $originZip, string $destinationZip, string $acceptDate = null, string $acceptTime = null): static
    {
        $route = [
            'MailClass'      => $mailClass,
            'OriginZIP'      => $originZip,
            'DestinationZIP' => $destinationZip,
        ];

        if ($acceptDate !== null) {
            $route['AcceptDate'] = $acceptDate;
        }

        if ($acceptTime !== null) {
            $route['AcceptTime'] = $acceptTime;
        }

        $this->route = $route;

        return $this;
    }
}
