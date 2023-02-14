<?php declare(strict_types=1);

namespace USPS;

class InternationalLabel extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'ExpressMailIntl';
    /**
     * @var array route added so far.
     */
    protected array $fields = [];

    protected array $contents = [];

    /**
     * Perform the API call.
     */
    public function createLabel(): string
    {
        // Add contents
        if ($this->contents && count($this->contents)) {
            $this->setField(31, 'ShippingContents', $this->contents);
        }

        // Add missing required
        $this->addMissingRequired();

        // Sort them
        // Hack by the only way this will work properly
        // since usps wants the tags to be in
        // a certain order
        ksort($this->fields, SORT_NUMERIC);

        // remove the \d. from the key
        foreach ($this->fields as $key => $value) {
            $newKey = str_replace('.', '', $key);
            $newKey = preg_replace('/\d+\:/', '', $newKey);
            unset($this->fields[$key]);
            $this->fields[$newKey] = $value;
        }

        return $this->doRequest();
    }

    /**
     * Return the USPS confirmation/tracking number if we have one.
     */
    public function getConfirmationNumber(): string|bool
    {
        $response = $this->getArrayResponse();
        // Check to make sure we have it
        if (isset($response[$this->getResponseApiName()])) {
            if (isset($response[$this->getResponseApiName()]['BarcodeNumber'])) {
                return $response[$this->getResponseApiName()]['BarcodeNumber'];
            }
        }

        return false;
    }

    /**
     * Return the USPS label as a base64 encoded string.
     */
    public function getLabelContents(): string|bool
    {
        $response = $this->getArrayResponse();
        // Check to make sure we have it
        if (isset($response[$this->getResponseApiName()])) {
            if (isset($response[$this->getResponseApiName()]['LabelImage'])) {
                return $response[$this->getResponseApiName()]['LabelImage'];
            }
        }

        return false;
    }

    /**
     * returns array of all fields added.
     */
    public function getPostFields(): array
    {
        return $this->fields;
    }

    /**
     * Add shipping contents.
     *
     * @return $this
     */
    public function addContent(
        string $description,
        string $value,
        string $pounds,
        string $ounces,
        int $quantity = 1,
        string $tariffNumber = null,
        string $countryOfOrigin = null
    ): static
    {
        $this->contents['ItemDetail'][] = [
            'Description'     => $description,
            'Quantity'        => $quantity,
            'Value'           => $value,
            'NetPounds'       => $pounds,
            'NetOunces'       => $ounces,
            'HSTariffNumber'  => $tariffNumber,
            'CountryOfOrigin' => $countryOfOrigin,
        ];

        return $this;
    }

    /**
     * Set the from address.
     *
     * @return $this
     */
    public function setFromAddress(
        string $firstName,
        string $lastName,
        string $company,
        string $address,
        string $city,
        string $state,
        string $zip,
        string $address2 = null,
        string $zip4 = null,
        string $phone = null,
        string $middleName = null
    ): static
    {
        $this->setField(5, 'FromFirstName', $firstName);
        $this->setField(6, 'FromMiddleInitial', $middleName);
        $this->setField(7, 'FromLastName', $lastName);
        $this->setField(8, 'FromFirm', $company);
        $this->setField(9, 'FromAddress1', $address2);
        $this->setField(10, 'FromAddress2', $address);
        $this->setField(11, 'FromCity', $city);
        $this->setField(12, 'FromState', $state);
        $this->setField(13, 'FromZip5', $zip);
        $this->setField(14, 'FromZip4', $zip4);
        $this->setField(15, 'FromPhone', $phone);

        return $this;
    }

    /**
     * Set the to address.
     *
     * @return $this
     */
    public function setToAddress(
        string $firstName,
        string $lastName,
        string $company,
        string $address,
        string $city,
        string $province,
        string $country,
        string $zip,
        string $address2 = null,
        string $poBoxFlag = 'N',
        string $phone = null,
        string $fax = null,
        string $email = null
    ): static
    {
        $this->setField(16, 'ToFirstName', $firstName);
        $this->setField(17, 'ToLastName', $lastName);
        $this->setField(18, 'ToFirm', $company);
        $this->setField(19, 'ToAddress1', $address2);
        $this->setField(20, 'ToAddress2', $address);
        $this->setField(21, 'ToCity', $city);
        $this->setField(22, 'ToProvince', $province);
        $this->setField(23, 'ToCountry', $country);
        $this->setField(24, 'ToPostalCode', $zip);
        $this->setField(25, 'ToPOBoxFlag', $poBoxFlag);
        $this->setField(26, 'ToPhone', $phone);
        $this->setField(27, 'ToFax', $fax);
        $this->setField(28, 'ToEmail', $email);

        return $this;
    }

    /**
     * Set any other required string make sure you set the correct position as well
     * as the position of the items matters.
     *
     * @return $this
     */
    public function setField(int $position, string $key, mixed $value): static
    {
        $this->fields[$position.':'.$key] = $value;

        return $this;
    }

    /**
     * Set package weight in ounces.
     *
     * @param float|int $weight
     *
     * @return $this
     */
    public function setWeightOunces(float|int $weight): static
    {
        $this->setField(33, 'GrossOunces', $weight);

        return $this;
    }

    /**
     * Set package weight in ounces.
     *
     * @param float|int $weight
     *
     * @return $this
     */
    public function setWeightPounds(float|int $weight): static
    {
        $this->setField(32, 'GrossPounds', $weight);

        return $this;
    }

    /**
     * Add missing required elements.
     *
     * @return void
     */
    protected function addMissingRequired(): void
    {
        $required = [
            '1:Option'           => '',
            '1.1:Revision'       => '2',
            '4:ImageParameters'  => '',
            '30:Container'       => 'NONRECTANGULAR',
            '32:GrossPounds'     => '',
            '33:GrossOunces'     => '',
            '34:ContentType'     => 'Documents',
            '35:Agreement'       => 'Y',
            '36:ImageType'       => 'PDF',
            '37:ImageLayout'     => 'ALLINONEFILE',
            '38:POZipCode'       => '',
            '39:LabelDate'       => '',
            '40:HoldForManifest' => 'N',
            '41:Size'            => 'LARGE',
            '42:Length'          => '',
            '43:Width'           => '',
            '44:Height'          => 'false',
            '45:Girth'           => '',
        ];

        // We need to add additional fields based on api we are using
        switch ($this->apiVersion) {
            case 'ExpressMailIntl':
                $required = array_merge($required, [
                    '29:NonDeliveryOption' => 'Return',
                ]);
                break;
            case 'PriorityMailIntl':
                $required = array_merge($required, [
                    '29:NonDeliveryOption' => 'Return',
                    '31.1:Insured'         => 'N',
                    '40.1:EELPFC'          => '',
                ]);
                break;
            case 'FirstClassMailIntl':
                $required = array_merge($required, [
                    '30.1:FirstClassMailType' => 'PARCEL',
                    '31.1:Insured'            => 'N',
                    '33.1:Machinable'         => 'false',
                    '40.1:EELPFC'             => '',
                ]);
                break;
        }

        foreach ($required as $item => $value) {
            $explode = explode(':', $item);
            if (!isset($this->fields[$item])) {
                $this->setField($explode[0], $explode[1], $value);
            }
        }
    }
}
