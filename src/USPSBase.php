<?php declare(strict_types=1);

namespace USPS;

/**
 * USPS Base class
 * used to perform the actual api calls.
 *
 * @since  1.0
 *
 * @author Vincent Gabriel
 */
abstract class USPSBase
{
    const LIVE_API_URL = 'https://secure.shippingapis.com/ShippingAPI.dll';
    const TEST_API_URL = 'https://production.shippingapis.com/ShippingAPITest.dll';

    /**
     * @var string - the usps username provided by the usps website
     */
    protected string $username = '';
    /**
     *  the error code if one exists.
     *
     * @var int
     */
    protected int $errorCode = 0;
    /**
     * the error message if one exists.
     *
     * @var string
     */
    protected string $errorMessage = '';
    /**
     *  the response message.
     *
     * @var string
     */
    protected string $response = '';
    /**
     *  the headers returned from the call made.
     *
     * @var array
     */
    protected array $headers = [];
    /**
     * The response represented as an array.
     *
     * @var array
     */
    protected array $arrayResponse = [];
    /**
     * All the post fields we will add to the call.
     *
     * @var array
     */
    protected array $postFields = [];
    /**
     * The api type we are about to call.
     *
     * @var string
     */
    protected string $apiVersion = '';
    /**
     * @var bool - set whether we are in a test mode or not
     */
    public static bool $testMode = false;
    /**
     * @var array - different kind of supported api calls by this wrapper
     */
    protected array $apiCodes = [
        'RateV2'                          => 'RateV2Request',
        'RateV4'                          => 'RateV4Request',
        'IntlRateV2'                      => 'IntlRateV2Request',
        'Verify'                          => 'AddressValidateRequest',
        'ZipCodeLookup'                   => 'ZipCodeLookupRequest',
        'CityStateLookup'                 => 'CityStateLookupRequest',
        'TrackV2'                         => 'TrackFieldRequest',
        'FirstClassMail'                  => 'FirstClassMailRequest',
        'SDCGetLocations'                 => 'SDCGetLocationsRequest',
        'ExpressMailLabel'                => 'ExpressMailLabelRequest',
        'PriorityMail'                    => 'PriorityMailRequest',
        'OpenDistributePriorityV2'        => 'OpenDistributePriorityV2.0Request',
        'OpenDistributePriorityV2Certify' => 'OpenDistributePriorityV2.0CertifyRequest',
        'ExpressMailIntl'                 => 'ExpressMailIntlRequest',
        'PriorityMailIntl'                => 'PriorityMailIntlRequest',
        'FirstClassMailIntl'              => 'FirstClassMailIntlRequest',
    ];
    /**
     * Default options for curl.
     *
     * @var array<string, mixed>
     */
    public static array $CURL_OPTS = [
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_FRESH_CONNECT  => 1,
        CURLOPT_PORT           => 443,
        CURLOPT_USERAGENT      => 'usps-php',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
    ];

    /**
     * Constructor.
     *
     * @param string $username - the usps api username
     */
    public function __construct(string $username = '')
    {
        $this->username = $username;
    }

    /**
     * set the usps api username we are going to user.
     *
     * @param string $username - the usps api username
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Return the post data fields as an array.
     *
     * @return array<string, mixed>
     */
    public function getPostData(): array
    {
        return ['API' => $this->apiVersion, 'XML' => $this->getXMLString()];
    }

    /**
     * Set the api version we are going to use.
     *
     * @param string $version the new api version
     */
    public function setApiVersion($version): self
    {
        $this->apiVersion = $version;

        return $this;
    }

    /**
     * Set whether we are in a test mode or not.
     *
     * @return $this
     */
    public function setTestMode(bool $value): static
    {
        self::$testMode = $value;

        return $this;
    }

    /**
     * Response api name.
     */
    public function getResponseApiName(): string
    {
        return str_replace('Request', 'Response', $this->apiCodes[$this->apiVersion]);
    }

    /**
     * Makes an HTTP request. This method can be overriden by subclasses if
     * developers want to do fancier things or use something other than curl to
     * make the request.
     *
     * @param resource $ch Optional initialized cURL handle
     * @param int $timeout Time-out of request in seconds.
     * Optional cURL timeout value.
     *
     * @return string the response text
     */
    protected function doRequest($ch = null, int $timeout = 60): string
    {
        if (!$ch) {
            $ch = curl_init();
        }

        $opts = self::$CURL_OPTS;
        $opts[CURLOPT_POSTFIELDS] = http_build_query($this->getPostData(), "", '&');
        $opts[CURLOPT_URL] = $this->getEndpoint();

        // Replace 443 with 80 if it's not secured
        if (strpos($opts[CURLOPT_URL], 'https://') === false) {
            $opts[CURLOPT_PORT] = 80;
        }
        if (is_int($timeout)) {
            $opts[CURLOPT_TIMEOUT] = $timeout;
        }
        // set options
        curl_setopt_array($ch, $opts);

        // execute
        $this->setResponse(curl_exec($ch));
        $this->setHeaders(curl_getinfo($ch));

        // fetch errors
        $this->setErrorCode(curl_errno($ch));
        $this->setErrorMessage(curl_error($ch));

        // Convert response to array
        $this->convertResponseToArray();

        // If it failed then set error code and message
        if ($this->isError()) {
            $arrayResponse = $this->getArrayResponse();

            // Find the error number
            $errorInfo = $this->getValueByKey($arrayResponse, 'Error');

            if ($errorInfo) {
                $this->setErrorCode((int) $errorInfo['Number']);
                $this->setErrorMessage($errorInfo['Description']);
            }
        }

        // close
        curl_close($ch);

        return $this->getResponse();
    }

    public function getEndpoint(): string
    {
        return self::$testMode ? self::TEST_API_URL : self::LIVE_API_URL;
    }

    abstract public function getPostFields();

    /**
     * Return the xml string built that we are about to send over to the api.
     */
    protected function getXMLString(): string
    {
        // Add in the defaults
        $postFields = [
            '@attributes' => ['USERID' => $this->username],
        ];

        // Add in the sub class data
        $postFields = array_merge($postFields, $this->getPostFields());

        $xml = XMLParser::createXML($this->apiCodes[$this->apiVersion], $postFields);

        return $xml->saveXML();
    }

    /**
     * Did we encounter an error?
     */
    public function isError(): bool
    {
        $headers = $this->getHeaders();
        $response = $this->getArrayResponse();
        // First make sure we got a valid response
        if ($headers['http_code'] != 200) {
            return true;
        }

        // Make sure the response does not have error in it
        if (isset($response['Error'])) {
            return true;
        }

        // Check to see if we have the Error word in the response
        if (strpos($this->getResponse(), '<Error>') !== false) {
            return true;
        }

        // No error
        return false;
    }

    /**
     * Was the last call successful.
     */
    public function isSuccess(): bool
    {
        return !$this->isError() ? true : false;
    }

    /**
     * Return the response represented as string.
     */
    public function convertResponseToArray(): array
    {
        if ($this->getResponse()) {
            $this->setArrayResponse(XML2Array::createArray($this->getResponse()));
        }

        return $this->getArrayResponse();
    }

    /**
     * Set the array response value.
     */
    public function setArrayResponse(array $value): void
    {
        $this->arrayResponse = $value;
    }

    /**
     * Return the array representation of the last response.
     */
    public function getArrayResponse(): array
    {
        return $this->arrayResponse;
    }

    /**
     * Set the response.
     */
    public function setResponse(mixed $response = ''): self
    {
        if (is_string($response)) {
            $this->response = $response;
        } else {
            $this->response = '';
        }

        return $this;
    }

    /**
     * Get the response data.
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * Set the headers.
     *
     * @param array $headers the headers array
     */
    public function setHeaders(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get the headers.
     *
     * @return array the headers returned from the call
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set the error code number.
     *
     * @param int $code the error code number
     */
    public function setErrorCode(int $code = 0): self
    {
        $this->errorCode = $code;

        return $this;
    }

    /**
     * Get the error code number.
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Set the error message.
     *
     * @param string $message the error message
     */
    public function setErrorMessage($message = ''): self
    {
        $this->errorMessage = $message;

        return $this;
    }

    /**
     * Get the error code message.
     *
     * @return string error code message
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * Find a key inside a multi dim. array.
     * Returns null if nothing is found.
     *
     * @param array  $array
     * @param string $key
     *
     * @return mixed
     */
    protected function getValueByKey(array $array, string $key): mixed
    {
        foreach ($array as $k => $each) {
            if ($k === $key) {
                return $each;
            }

            if (is_array($each)) {
                if ($return = $this->getValueByKey($each, $key)) {
                    return $return;
                }
            }
        }

        return null;
    }
}
