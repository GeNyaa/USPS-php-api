<?php declare(strict_types=1);

namespace USPS;

/**
 * Class OpenDistributeLabel.
 */
class OpenDistributeLabel extends USPSBase
{
    /**
     * @var string the api version used for this type of call
     */
    protected string $apiVersion = 'OpenDistributePriorityV2';
    /**
     * @var array route added so far.
     */
    protected array $fields = [];

    /**
     * Perform the API call.
     */
    public function createLabel(): string
    {
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
            if (isset($response[$this->getResponseApiName()]['OpenDistributePriorityNumber'])) {
                return $response[$this->getResponseApiName()]['OpenDistributePriorityNumber'];
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
            if (isset($response[$this->getResponseApiName()]['OpenDistributePriorityLabel'])) {
                return $response[$this->getResponseApiName()]['OpenDistributePriorityLabel'];
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
        string $zip4 = null
    ): static
    {
        $this->setField(5, 'FromName', trim($firstName.' '.$lastName));
        $this->setField(7, 'FromFirm', $company);
        $this->setField(8, 'FromAddress1', $address2);
        $this->setField(9, 'FromAddress2', $address);
        $this->setField(10, 'FromCity', $city);
        $this->setField(11, 'FromState', $state);
        $this->setField(12, 'FromZip5', $zip);
        $this->setField(13, 'FromZip4', $zip4);

        return $this;
    }

    /**
     * Set the to address.
     *
     * @return $this
     */
    public function setToAddress(
        string $company,
        string $address,
        string $city,
        string $state,
        string $zip,
        string $address2 = null,
        string $zip4 = null
    ): static
    {
        $this->setField(15, 'ToFacilityName', $company);
        $this->setField(18, 'ToFacilityAddress1', $address2);
        $this->setField(19, 'ToFacilityAddress2', $address);
        $this->setField(20, 'ToFacilityCity', $city);
        $this->setField(21, 'ToFacilityState', $state);
        $this->setField(22, 'ToFacilityZip5', $zip);
        $this->setField(23, 'ToFacilityZip4', $zip4);

        return $this;
    }

    /**
     * Set package weight in ounces.
     *
     * @return $this
     */
    public function setWeightOunces(int|string $weight): static
    {
        $this->setField(38, 'WeightInOunces', $weight);

        return $this;
    }

    /**
     * Set package weight in ounces.
     *
     * @return $this
     */
    public function setWeightPounds(int|string $weight): static
    {
        $this->setField(37, 'WeightInPounds', $weight);

        return $this;
    }

    /**
     * Set any other requried string make sure you set the correct position as well
     * as the position of the items matters.
     *
     * @return $this
     */
    public function setField(int $position, string $key, string $value): static
    {
        $this->fields[$position.':'.$key] = $value;

        return $this;
    }

    /**
     * Add missing required elements.
     */
    protected function addMissingRequired(): void
    {
        $required = [
            '1:Revision'                      => '',
            '2:ImageParameters'               => '',
            '2:PermitNumber'                  => '',
            '4:PermitIssuingPOZip5'           => '',
            '14:POZipCode'                    => '',
            '34:FacilityType'                 => 'DDU',
            '35:MailClassEnclosed'            => 'Other',
            '36:MailClassOther'               => 'Free Samples',
            '37:WeightInPounds'               => '22',
            '38:WeightInOunces'               => '10',
            '39:ImageType'                    => 'PDF',
            '40:SeparateReceiptPage'          => 'false',
            '41:AllowNonCleansedFacilityAddr' => 'false',
            '42:HoldForManifest'              => 'N',
            '43:CommercialPrice'              => 'N',
        ];

        foreach ($required as $item => $value) {
            $explode = explode(':', $item);
            if (!isset($this->fields[$item])) {
                $this->setField($explode[0], $explode[1], $value);
            }
        }
    }
}
