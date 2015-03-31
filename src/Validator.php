<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 30.03.2015
 * Time: 20:56
 */
namespace samsonphp\w3c;

use samsonphp\w3c\violation\Collection;

/**
 * W3C validator
 *
 * @package samsonphp\w3c
 * @author Vitalii Iehorov <egorov@samsonos.com>
 */
class Validator
{
    /** @var string URL for validating */
    protected $w3cUrl = 'http://validator.w3.org/check';

    /** @var bool Validation result status */
    protected $w3cStatus;

    /** @var int Amount of validation errors found */
    protected $w3cErrorsCount = 0;

    /** @var int Amount of validation warnings found */
    protected $w3cWarningsCount = 0;

    /** @var \samsonphp\w3c\violation\Collection W3C Errors collection */
    protected $w3cErrors = array();

    /** @var \samsonphp\w3c\violation\Collection W3C Warnings collection */
    protected $w3cWarnings = array();

    /** @var string Source URL for validating  */
    protected $sourceUrl;

    /**
     * @param $sourceUrl Source URL for validating
     */
    public function __construct($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * Perform HTTP request
     * @param string $sourceUrl Source URL for validating
     * @returns string HTTP response results
     * @throws RequestException If W3C request has been failed
     */
    protected function httpRequest($sourceUrl)
    {
        // Build request URL with GET parameters
        $uri = $this->w3cUrl . '?' . http_build_query(array('output' => 'soap12', 'uri' => $sourceUrl));

        // Create a stream
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: W3C Validation bot\r\n"
            )
        );

        // Perform HTTP request
        $response = trim(file_get_contents($uri, false, stream_context_create($opts)));

        // If we have completed HTTP request
        if ($response === false) {
            // Throw http request failed exception
            throw new RequestException('W3C API http request failed');
        }

        return $response;
    }

    /**
     * W3C validator function
     * @throws RequestException
     * @returns array
     */
    public function validate()
    {
        // Block errors reporting
        libxml_use_internal_errors(false);

        // W3C validator response
        $w3cResponse = simplexml_load_string($this->httpRequest($this->sourceUrl));

        // Get document namespaces declaration
        $nameSpaces = $w3cResponse->getNamespaces(true);

        // Get validation data
        $validationResponse = $w3cResponse
            ->children($nameSpaces['env'])  // Get 'http://www.w3.org/2003/05/soap-envelope/'
            ->children($nameSpaces['m'])    // Get 'http://www.w3.org/2005/10/markup-validator'
            ->markupvalidationresponse;

        // Create errors collection
        $this->w3cErrors = new Collection(
            $validationResponse->errors->errorlist->error,
            __NAMESPACE__.'\violation\Error'
        );

        // Create warnings collection
        $this->w3cWarnings = new Collection(
            $validationResponse->warnings->warninglist->warning,
            __NAMESPACE__.'\violation\Warning'
        );

        // Set validation summary results
        $this->w3cStatus = (bool)$validationResponse->validity;
        $this->w3cErrorsCount = (int)$validationResponse->errors->errorcount;
        $this->w3cWarningsCount = (int)$validationResponse->warnings->warningcount;

        // Form response array
        return array(
            'validity' => $this->w3cStatus,
            'errorsCount' => $this->w3cErrorsCount,
            'errors' => $this->w3cErrors->toArray(),
            'warningsCount' => $this->w3cWarningsCount,
            'warnings' => $this->w3cWarnings->toArray(),
        );
    }
}
