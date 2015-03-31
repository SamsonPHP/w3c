<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 30.03.2015
 * Time: 20:56
 */
namespace samsonphp\w3c;

use samson\core\Service;
use samsonphp\w3c\violation\Collection;

/**
 * W3C validator SamsonPHP service.
 * This service is performing request to W3C HTML validation service
 * ans outputs its error for improving html markup quality.
 *
 * @package samsonphp\w3c
 * @author Vitalii Iehorov <egorov@samsonos.com>
 */
class Validator extends Service
{
    /** @var string Validating URL prefix, used for local domains */
    public $urlPrefix = '.samsonos.com';

    /** @var string URL for validating */
    public $w3cUrl = 'http://validator.w3.org/check';

    /** @var bool Validation result status */
    protected $w3cStatus;

    /** @var int Amount of validation errors found */
    protected $w3cErrorsCount = 0;

    /** @var int Amount of validation warnings found */
    protected $w3cWarningsCount = 0;

    /** @var \SimpleXMLElement W3C validator response */
    protected $w3cResponse;

    protected $w3cErrors = array();
    protected $w3cWarnings = array();

    /** @var bool Flag to perform w3c validation */
    protected $enabled = false;

    /**
     * Asynchronous validation controller action
     * @return array Asynchronous response array
     */
    public function __async_validate()
    {
        return array('status' => 1, 'html' => $this->validate());
    }


    /** Module initialization logic */
    public function init(array $params = array())
    {
        // Subscribe to resourcer event
        \samsonphp\event\Event::subscribe('resourcer.updated', array($this, 'enable'));

        $this->enabled = true;
        $this->__async_validate();
    }

    /** Trigger function to enable automatic validation if resource ahs been changed */
    public function enable()
    {
        // Switch flag to enable validation if resource has been changed
        $this->enabled = true;
    }


    /**
     * Perform HTTP request
     * @throws RequestException If W3C request has been failed
     * @returns string HTTP response results
     */
    protected function httpRequest()
    {
        // Build current HTML page url
        $url = $_SERVER['HTTP_HOST'] . $this->urlPrefix;

        // Add internal URL if present
        if ($_SERVER['REQUEST_URI'] !== '/') {
            $url .= '/' . $_SERVER['REQUEST_URI'];
        }

        // Build request URL with GET parameters
        $uri = $this->w3cUrl . '?' . http_build_query(array('output' => 'soap12', 'uri' => $url));

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
        if ($this->w3cResponse === false) {
            // Throw http request failed exception
            throw new RequestException('W3C API http request failed');
        }

        trace($response, true);

        return $response;
    }

    /**
     * W3C validator function
     * @throws RequestException
     */
    public function validate()
    {
        // If we need validation
        if ($this->enabled) {
            // Block errors reporting
            libxml_use_internal_errors(false);

            // Parse XML document
            $this->w3cResponse = simplexml_load_string($this->httpRequest());

            // Get document namespaces declaration
            $nameSpaces = $this->w3cResponse->getNamespaces(true);

            // Get validation data
            $validationResponse = $this->w3cResponse
                ->children($nameSpaces['env'])  // Get 'http://www.w3.org/2003/05/soap-envelope/'
                ->children($nameSpaces['m'])    // Get 'http://www.w3.org/2005/10/markup-validator'
                ->markupvalidationresponse;

            $this->w3cErrors = new Collection($validationResponse->errors->errorlist->error, __NAMESPACE__.'\violation\Error');
            $this->w3cWarnings = new Collection($validationResponse->warnings->warninglist->warning, __NAMESPACE__.'\violation\Warning');

            trace($this->w3cErrors, true);
            trace($this->w3cWarnings, true);

            // Get response headers validation data
            $this->w3cStatus = &$http_response_header['X-W3C-Validator-Status'];
            $this->w3cErrorsCount = &$http_response_header['X-W3C-Validator-Errors'];
            $this->w3cWarningsCount = &$http_response_header['X-W3C-Validator-Warnings'];
        }
    }
}
